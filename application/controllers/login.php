<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * 公司简介控制器
 * 
 * @package		app
 * @subpackage	core
 * @category	controller
 * @author		yaobin<645894453@qq.com>
 *
 */
class Login extends MY_Controller {
    
    public function __construct()
    {
        parent::__construct();
        $this->load->model('login_model');
        $this->cismarty->assign('flag','d');
		
    }
    
    public function index($openid=null)
    {
    	$this->cismarty->assign('openid',$openid);
    	$this->cismarty->display('login.html');
    }
    
	/**
     * 用户登陆
     * @param $id
     */
	public function check_login(){
		$rs = $this->login_model->check_login();
		if($rs > 0){
			//获取用户信息
			$data = $this->login_model->get_user_info($this->input->post('name'));
			$member_info['userid'] = $data['id'];
			$member_info['username'] = $data['username'];
			$member_info['rel_name'] = $data['rel_name'];
			$member_info['admin_group'] = $data['admin_group'];
			$member_info['phone'] = $data['phone'];
			$member_info['passwd'] = $data['passwd'];
			$member_info['admin_group'] = $data['admin_group'];
			$member_info['manager_id'] = $data['manager_id'];
			$member_info['yqm'] = $data['yqm'];
			$this->session->set_userdata($member_info); //记录session
			
			if($data['admin_group'] === '1')
	        	$this->show_message('登陆成功',site_url("admin"),'1');//管理员
	        if($data['admin_group'] === '2')
        		$this->show_message('登陆成功',site_url("kf_manager"),'1');//客服经理
	        if($data['admin_group'] === '3')
	        	$this->show_message('登陆成功',site_url("user"),'1');//业务员
	        if($data['admin_group'] === '4')
	        	$this->show_message('登陆成功',site_url("wl_manager"),'1');//外联经理
	        if($data['admin_group'] === '5')
	        	$this->show_message('登陆成功',site_url("md_manager"),'1');//门店经理
		}else{
			$this->show_message('登陆失败!请检查用户名密码','','1');
		}
	}
	
	/**
     * 修改密码
     */
	public function save_change_pwd(){
		if(sha1($this->input->post('passwd')) != $this->session->userdata('passwd')){
			$this->show_message('原密码输入错误','','1');
		}else{
			$rs = $this->login_model->change_pwd();
			if($rs){
				$this->session->sess_destroy();
				$this->show_message('密码修改成功，请重新登陆',site_url('login'),'1');
			}else{
				$this->show_message('密码修改失败','','1');
			}
		}
	}
	
	public function change_pwd(){
		$this->cismarty->display('change_pwd.html');
	}
	
    /**
     * 退出登录
     */
    public function logout(){
        $this->session->sess_destroy();
        $this->show_message('安全退出成功',site_url('login'),'1');
    }
    
}
