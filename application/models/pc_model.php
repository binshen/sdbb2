<?php
if (! defined('BASEPATH'))
    exit('No direct script access allowed');

/**
 * 登陆控制器
 *
 * @package		app
 * @subpackage	core
 * @category	model
 * @author		yaobin<645894453@qq.com>
 *        
 */
class Pc_model extends MY_Model
{
	protected $tables = array(
			'main_list',
			'project' ,
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
    
	
	/**
     * 获取登陆首页信息
     */
	public function get_index_info(){
		$data['projects'] =  $this->db->select('*')->from($this->tables[1])->get()->result_array();
		$data['main_list'] =  $this->db->select('a.*,b.project')->from("{$this->tables[0]} a")->
			join("{$this->tables[1]} b","a.project_id=b.id")->where('yqm',$this->session->userdata('yqm'))->limit(14,0)->
			order_by('a.cdate','desc')->get()->result_array();
		$rs = $this->db->select('count(1) num')->from($this->tables[0])->where('yqm',$this->session->userdata('yqm'))->get()->row();
		$rs2 = $this->db->select('count(1) num')->from($this->tables[0])->where('yqm',$this->session->userdata('yqm'))->
				where('status','2')->get()->row();
		$data['num1'] = $rs->num;
		$data['num2'] = $rs2->num;
		return $data;		
	}
	
	/**
     * 获取登陆用户信息
     */
	public function get_user_info($name){
		$this->db->select('*');
		$this->db->from($this->tables[0]);
		$this->db->where('username',$name);
		$rs = $this->db->get()->row_array();
		return $rs;
	}
	
	public function get_count(){
		$data = $this->sysconfig_model->get_projects_m();
		foreach($data as $v){
			$project[] = $v['id'];
		}
		
		$this->db->select('count(1) num')->from($this->tables[0]);
		if($this->session->userdata('admin_group') == '2'){
			if(!empty($project)){
				$this->db->where_in('project_id',$project);
			}else{
				$this->db->where('1','2');
			}
			
		}elseif ($this->session->userdata('admin_group') == '3'){
			$this->db->where('yqm',$this->session->userdata('yqm'));
		}else{
			$this->db->where('1','2');
		}
		
		if($this->input->post('project_id'))
			$this->db->where('project_id',$this->input->post('project_id'));
		if($this->input->post('status'))
			$this->db->where('status',$this->input->post('status'));
		if($this->input->post('s_date'))
			$this->db->where('cdate >=',$this->input->post('s_date'));
		if($this->input->post('e_date'))
			$this->db->where('cdate <=',$this->input->post('e_date'));
		$rs = $this->db->get()->row();
		return $rs->num;
	}
	
	public function main_list($per_page,$offset){
		$data = $this->sysconfig_model->get_projects_m();
		foreach($data as $v){
			$project[] = $v['id'];
		}
		
		$this->db->select('a.*,b.project,c.rel_name')->from("{$this->tables[0]} a");
		$this->db->join("{$this->tables[1]} b","a.project_id=b.id","left");
		$this->db->join("{$this->tables[2]} c","a.yqm=c.yqm","left");
		
		if($this->session->userdata('admin_group') == '2'){
			if(!empty($project)){
				$this->db->where_in('project_id',$project);
			}else{
				$this->db->where('1','2');
			}
			
		}elseif ($this->session->userdata('admin_group') == '3'){
			$this->db->where('a.yqm',$this->session->userdata('yqm'));
		}else{
			$this->db->where('1','2');
		}
		
		//$this->db->where('yqm',$this->session->userdata('yqm'));
		if($this->input->post('project_id'))
			$this->db->where('a.project_id',$this->input->post('project_id'));
		if($this->input->post('status'))
			$this->db->where('a.status',$this->input->post('status'));
		if($this->input->post('s_date'))
			$this->db->where('a.cdate >=',$this->input->post('s_date'));
		if($this->input->post('e_date'))
			$this->db->where('a.cdate <=',$this->input->post('e_date'));

		if($this->input->post('name'))
			$this->db->like('a.name',$this->input->post('name'));
		if($this->input->post('phone'))
			$this->db->like('a.phone',$this->input->post('phone'));
		if($this->input->post('sex'))
			$this->db->where('a.sex',$this->input->post('sex'));
		$this->db->limit($per_page,$offset);
		$this->db->order_by("cdate", "desc");
		$rs = $this->db->get()->result_array();
		return $rs;
	}
	
	public function get_all_pro(){
		return $this->db->select('*')->from($this->tables[1])->get()->result_array();
	}
	
	/**
	 * 修改密码
	 */
	public function change_pwd(){
		$this->db->where('username',$this->session->userdata('username'));
		$rs = $this->db->update($this->tables[2],array('passwd'=>sha1($this->input->post('passwd'))));
		if($rs)
			return true;
		else
			return false;
	}
	
	
	public function get_main_info($id){
		return $this->db->select('*')->from($this->tables[0])->where('id',$id)->get()->row_array();
	}
	
	public function get_count_user(){
		$rs = $this->db->select('count(1) num')->from($this->tables[2])->
		where('admin_group','3')->get()->row();
		return $rs->num;
	}
	
	public function user_list($per_page,$offset){
		$this->db->select('*');
		$this->db->from($this->tables[2])->where('admin_group','3');
		$this->db->limit($per_page,$offset);
		$this->db->order_by("cdate", "desc");
		$rs = $this->db->get()->result_array();
		return $rs;
	}
	
	//改变状态
	public function change_status(){
		$id = $this->input->post('id');
		$status = $this->input->post('status');
		$this->db->where('id',$id);
		if($status == '2'){
			if($this->session->userdata('admin_group') == '2')
				$status = '3';
			$rs = $this->db->update($this->tables[0],array('status'=>$status,'ddate'=>date('Y-m-d H:i:s',time())));
		}else{
			$rs = $this->db->update($this->tables[0],array('status'=>$status,'deldate'=>date('Y-m-d H:i:s',time())));
		}
		return $rs;
	}
	
}

/* End of file sysconfig_model.php */
/* Location: ./application/models/sysconfig_model.php */