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
class Admin_model extends MY_Model
{
	protected $tables = array(
			'main_list',	
			'project',
			'admin',
			'm_p'

    );
	
    public function __construct ()
    {
        parent::__construct();
    }

    public function __destruct ()
    {
        parent::__destruct();
    }
    
	
	
	//列出经理
	public function list_managers(){
		$data = $this->db->select('a.id,a.username,a.rel_name,project_id,project')->from("{$this->tables[2]} a")->join("{$this->tables[3]} b","a.id=b.manager_id","left")
				->join("{$this->tables[1]} c","b.project_id=c.id")
				->where('admin_group','2')->get()->result_array();
		$data_n = array();
		foreach($data as $k=>$v){
			if(isset($data_n[$v['id']])){
				$data_n[$v['id']]['project'][] = array('project_id'=>$v['project_id'],'project_name'=>$v['project']);
			}else{
				$data_n[$v['id']]['id'] = $v['id'];
				$data_n[$v['id']]['username'] = $v['username'];
				$data_n[$v['id']]['rel_name'] = $v['rel_name'];
				$data_n[$v['id']]['project'][] = array('project_id'=>$v['project_id'],'project_name'=>$v['project']);
			}

		}
		return $data_n;
	}
	

    
    //删除经理
    public function del_m(){
 		$this->db->trans_start();
    	$this->db->where('id',$this->input->post('m_id'));
    	$this->db->delete($this->tables[2]);
    	$this->db->where('manager_id',$this->input->post('m_id'));
    	$this->db->delete($this->tables[3]);
    	$this->db->trans_complete();
		if ($this->db->trans_status() === FALSE) {
            return -1;
        } else {
            return 1;
        }
    }
    
    //保存经理
    public function save_kfjl(){
		$this->db->trans_start();
		$project_id = $this->input->post('project_id');
		$is_exe = $this->input->post('is_exe')?$this->input->post('is_exe'):1;
		if($this->input->post('id')){
			$this->db->where('manager_id',$this->input->post('id'));
			$this->db->delete($this->tables[3]);
			foreach($project_id as $v){
				$this->db->insert($this->tables[3],array('manager_id'=>$this->input->post('id'),'project_id'=>$v));
			}
			$this->db->where('id',$this->input->post('id'));
			$this->db->update($this->tables[2],array('rel_name'=>$this->input->post('rel_name'),'is_exe'=>$this->input->post('is_exe')));
			
		}else{//新增
	    	$rsb = $this->db->select('count(username) username')->from($this->tables[2])->where('username',$this->input->post('username'))->get()->row();
			if($rsb->username)
				return -1;
			$data = array(
				'username'=>$this->input->post('username'),
				'passwd'=>sha1('888888'),
				'rel_name'=>$this->input->post('rel_name'),
				'admin_group'=>'2',
				'is_exe'=>$is_exe,
				'phone'=>$this->input->post('username'),
				'manager_id'=>'0',
				'cdate'=>date('Y-m-d H:i:s',time())
			);
			$rs = $this->db->insert($this->tables[2],$data);
			$id = $this->db->insert_id();
			foreach($project_id as $v){
				$this->db->insert($this->tables[3],array('manager_id'=>$id,'project_id'=>$v));
			}
		}

        $this->db->trans_complete();
		if ($this->db->trans_status() === FALSE) {
            return -99;
        } else {
            return 1;
        }
    }
    
    function get_kfjl($id){
    	$data = $this->db->select('id,username,rel_name')->from($this->tables[2])->where('id',$id)->get()->row_array();
    	$list = $this->db->select('project_id')->from($this->tables[3])->where('manager_id',$id)->get()->result_array();
    	foreach($list as $k=>$v){
    		$data['list'][] = $v['project_id'];
    	}
    	return $data;
    }
    
	
	//列出渠道经理
	public function list_qdjl(){
		$data = $this->db->select('id,rel_name,username')->from($this->tables[2])->where('admin_group','4')->get()->result_array();
		return $data;
	}
	
	//保存渠道经理
    public function save_qdjl(){
		$this->db->trans_start();
		if($this->input->post('id')){
			$this->db->where('id',$this->input->post('id'));
			$this->db->update($this->tables[2],array('username'=>$this->input->post('username'),'phone'=>$this->input->post('username'),'rel_name'=>$this->input->post('rel_name')));
		}else{//新增
	    	$rsb = $this->db->select('count(username) username')->from($this->tables[2])->where('username',$this->input->post('username'))->get()->row();
			if($rsb->username)
				return -1;
			$data = array(
				'username'=>$this->input->post('username'),
				'passwd'=>sha1('888888'),
				'rel_name'=>$this->input->post('rel_name'),
				'admin_group'=>'4',
				'phone'=>$this->input->post('username'),
				'manager_id'=>'0',
				'cdate'=>date('Y-m-d H:i:s',time())
			);
			$rs = $this->db->insert($this->tables[2],$data);
		}

        $this->db->trans_complete();
		if ($this->db->trans_status() === FALSE) {
            return -99;
        } else {
            return 1;
        }
    }
    
    function get_qdjl($id){
    	$data = $this->db->select('id,username,rel_name')->from($this->tables[2])->where('id',$id)->get()->row_array();
    	return $data;
    }
    
    //获取管理员报备量
    public function get_admin_data(){
    	$data = $this->db->select('sum(bb_count) bb_count,sum(dk_count) dk_count,sum(qy_count) qy_count')->from($this->tables[2])
    			->get()->row_array();
    	return $data;
    }
	
}

/* End of file sysconfig_model.php */
/* Location: ./application/models/sysconfig_model.php */