<?php
if (! defined('BASEPATH'))
    exit('No direct script access allowed');

/**
 * 普通用户模型
 *
 * @package		app
 * @subpackage	core
 * @category	model
 * @author		yaobin<645894453@qq.com>
 *        
 */
class Manager_model extends MY_Model
{
	protected $tables = array(
			'main_list',	
			'project',
			'admin'

    );
	
    public function __construct ()
    {
        parent::__construct();
    }

    public function __destruct ()
    {
        parent::__destruct();
    }
    
	
	
	//列出报备信息
	public function list_bb($s_page){
		$data = $this->sysconfig_model->get_projects_m();
		foreach($data as $v){
			$project[] = $v['id'];
		}
		//var_dump($project);die;
		$this->db->select('a.*,b.project')->from("{$this->tables[0]} a");
		$this->db->join("{$this->tables[1]} b","a.project_id = b.id","left");
		$this->db->where('yqm','');
		if(!empty($project))
			$this->db->where_in('project_id',$project);
		else 
			$this->db->where_in('1','2');
		if($this->input->post('status'))
			$this->db->where('status',$this->input->post('status'));
		if($this->input->post('project'))
			$this->db->where('project_id',$this->input->post('project'));
			
		if($s_page){
			$this->db->limit($s_page+5,$s_page);
		}else{
			$this->db->limit(5,0);
		}

		$this->db->order_by('a.cdate','desc');
		$data = $this->db->get()->result_array();
		return $data;
	}
	
	//列出报备信息
	public function list_audit($s_page){
		//$data = $this->db->select('yqm')->from($this->tables[2])->where('manager_id',$this->session->userdata('userid'))->get()->result();
		//foreach($data as $v){
		//	$user[]=$v->yqm;
		//}
		$data = $this->sysconfig_model->get_projects_m();
		foreach($data as $v){
			$project[] = $v['id'];
		}
		$this->db->select('a.*,b.project,c.rel_name')->from("{$this->tables[0]} a");
		$this->db->join("{$this->tables[1]} b","a.project_id = b.id","left");
		$this->db->join("{$this->tables[2]} c","a.yqm = c.yqm","left");
		$this->db->where_in('a.project_id',$project);
		//$this->db->where_in('status',array('2','3','4','5','6','-1'));
		
		if($this->input->post('status'))
			$this->db->where('status',$this->input->post('status'));
		if($this->input->post('project'))
			$this->db->where('project_id',$this->input->post('project'));
			
		if($s_page){
			$this->db->limit($s_page+5,$s_page);
		}else{
			$this->db->limit(5,0);
		}

		$this->db->order_by('a.cdate','desc');
		$data = $this->db->get()->result_array();
		return $data;
	}
	
	//改变状态
	public function change_status(){
		$id = $this->input->post('id');
		$status = $this->input->post('status');
		
		$this->db->where('id',$id);
		if($status == '-1'){
			$rs = $this->db->delete($this->tables[0]);
		}else{
			$rs = $this->db->update($this->tables[0],array('status'=>$status,'ddate'=>date('Y-m-d H:i:s',time())));
			if($status == '3'){
				$yqm = $this->db->select('yqm,name,phone,sex')->from($this->tables[0])->where('id',$id)->get()->row();
				if(!empty($yqm->yqm)){
					$phone = $this->db->select('phone')->from($this->tables[2])->where('yqm',$yqm->yqm)->get()->row();
					$p = $phone->phone;
					$user = $this->message['user'];
					$pwd = md5($this->message['pwd']);
					$con = mb_convert_encoding('您的客户'.$yqm->name.''.$yqm->sex.''.$yqm->phone.'已成功带看，确认为有效客户，请保持跟进并配合案场成交','gbk');
					$url="http://api.52ao.com/?user={$user}&pass={$pwd}&mobile={$p}&content={$con}"; 
					$fcontent=@file_get_contents($url);
				}
			}
			if($status == '4'){
				$yqm = $this->db->select('yqm,name,phone,sex')->from($this->tables[0])->where('id',$id)->get()->row();
				if(!empty($yqm->yqm)){
					$phone = $this->db->select('phone')->from($this->tables[2])->where('yqm',$yqm->yqm)->get()->row();
					$p = $phone->phone;
					$user = $this->message['user'];
					$pwd = md5($this->message['pwd']);
					$con = mb_convert_encoding('恭喜！！您的客户'.$yqm->name.''.$yqm->sex.''.$yqm->phone.'已成功签约，请等待电商佣金核准，近期将通知开票結佣。','gbk');
					$url="http://api.52ao.com/?user={$user}&pass={$pwd}&mobile={$p}&content={$con}"; 
					$fcontent=@file_get_contents($url);
				}
			}
		}
		return $rs;
	}
	
	//列出经理下面的所有业务员
	public function list_users(){
		$data = $this->db->select('*')->from($this->tables[2])->where('admin_group','3')->get()->result_array();
		return $data;
	}
	
	//经理删除业务员
	public function del_user(){
		$this->db->where('id',$this->input->post('id'));
		$rs = $this->db->delete($this->tables[2]);
		return $rs;
	}
	
	//保存业务员
	public function save_user(){
		$rsa = $this->db->select('count(yqm) yqm')->from($this->tables[2])->where('yqm',$this->input->post('yqm'))->get()->row();
		if($rsa->yqm >0)
			return -1;
		$rsb = $this->db->select('count(phone) phone')->from($this->tables[2])->where('phone',$this->input->post('phone'))->get()->row();
		if($rsb->phone)
			return -2;
		$data = array(
			'username'=>$this->input->post('phone'),
			'passwd'=>sha1('888888'),
			'rel_name'=>$this->input->post('rel_name'),
			'admin_group'=>'3',
			'phone'=>$this->input->post('phone'),
			'cdate'=>date('Y-m-d H:i:s',time()),
			'yqm'=>$this->input->post('yqm')
		);
		$rs = $this->db->insert($this->tables[2],$data);
		if($rs){
			return 1;
		}else{
			return -99;
		}
	}
	
}

/* End of file sysconfig_model.php */
/* Location: ./application/models/sysconfig_model.php */