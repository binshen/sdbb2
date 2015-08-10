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
class Login_model extends MY_Model
{
	protected $tables = array(
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
     * 用户登陆
     */
	public function check_login(){
		$rs = $this->db->select('count(1) num')->from($this->tables[0])
			->where('username',$this->input->post('name'))
			->where('passwd',sha1($this->input->post('passwd')))->get()->row();

		if($rs->num > 0 && $this->session->userdata('openid')){
			$this->db->where('username',$this->input->post('name'));
			$this->db->where('passwd',sha1($this->input->post('passwd')));
			$this->db->update($this->tables[0],array('openid'=>$this->session->userdata('openid')));
		}
		
		return $rs->num;
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
	/**
	 * 修改密码
	 */
	public function change_pwd(){
		$this->db->where('username',$this->session->userdata('username'));
		$rs = $this->db->update($this->tables[0],array('passwd'=>sha1($this->input->post('new_passwd'))));
		if($rs)
			return true;
		else
			return false;
	}
	
	public function change_pic($pic){
		$this->db->where('username',$this->session->userdata('username'));
		$this->db->update($this->tables[0],array('pic'=>$pic));
	}
	
	
}

/* End of file sysconfig_model.php */
/* Location: ./application/models/sysconfig_model.php */