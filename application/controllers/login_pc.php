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
class Login_pc extends MY_Controller {
    
    public function __construct()
    {

        parent::__construct();
        $this->load->model('login_model');
        $this->cismarty->assign('flag','d');
		
    }
    
    public function index()
    {
    	$this->cismarty->display('pc/login.html');
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
	        	$this->show_message('登陆成功',site_url("admin_pc"),'1');//管理员
	        if($data['admin_group'] === '2')
        		$this->show_message('登陆成功',site_url("kf_manager_pc"),'1');//客服经理
	        if($data['admin_group'] === '3')
	        	$this->show_message('登陆成功',site_url("user_pc"),'1');//业务员
	        if($data['admin_group'] === '4')
	        	$this->show_message('登陆成功',site_url("wl_manager_pc"),'1');//外联经理
	        if($data['admin_group'] === '5')
	        	$this->show_message('登陆成功',site_url("md_manager_pc"),'1');//门店经理
		}else{
			$this->show_message('登陆失败!请检查用户名密码','','1');
		}
	}
	
    
	/**
     * 修改密码
     */
	public function change_pwd(){
		if(sha1($this->input->post('passwd')) != $this->session->userdata('passwd')){
			$this->show_message('原密码输入错误','','1');
		}else{
			$rs = $this->login_model->change_pwd();
			if($rs){
				$this->session->sess_destroy();
				$this->show_message('密码修改成功，请重新登陆',site_url('login_pc'),'1');
			}else{
				$this->show_message('密码修改失败','','1');
			}
		}
	}
	
	public function aqzx(){
        if(!$this->session->userdata('username')){
        	redirect('login_pc');
        }
        $this->cismarty->assign('admin_group',$this->session->userdata('admin_group'));
		$this->cismarty->assign('username',$this->session->userdata('username'));
		$this->cismarty->assign('rel_name',$this->session->userdata('rel_name'));
		$this->cismarty->assign('flag','d');
		$this->cismarty->display('pc/aqzx.html');
	}
	
    /**
     * 退出登录
     */
    public function logout(){
        $this->session->sess_destroy();
        $this->show_message('安全退出成功',site_url('login_pc'),'1');
    }
    
    public function change_pic(){
        if(!$this->session->userdata('username')){
        	redirect('login_pc');
        }
        $this->cismarty->assign('admin_group',$this->session->userdata('admin_group'));
        $this->cismarty->assign('rel_name',$this->session->userdata('rel_name'));
        $this->cismarty->display('pc/change_pic.html');
    }
    
	public function upload_pic(){
		header('Content-Type: text/html; charset=utf-8');
		$result = array();
		$result['success'] = false;
		$success_num = 0;
		$msg = '';
		//上传目录
		$dir = $_SERVER['DOCUMENT_ROOT']."/uploadfiles/head";

		// 取服务器时间+8位随机码作为部分文件名，确保文件名无重复。
		$filename = date("YmdHis").'_'.floor(microtime() * 1000).'_'.$this->createRandomCode(8);
		// 处理头像图片开始------------------------------------------------------------------------>
		//头像图片(file 域的名称：__avatar1,2,3...)。
		$avatars = array("__avatar1", "__avatar2", "__avatar3");
		$avatars_length = count($avatars);
		
		for ( $i = 0; $i < $avatars_length; $i++ )
		{
			$avatar = $_FILES[$avatars[$i]];
			$avatar_number = $i + 1;
			if ( $avatar['error'] > 0 )
			{
				$msg .= $avatar['error'];
			}
			else
			{
				$savePath = "$dir/s" . $avatar_number . "_$filename.jpg";
				//$result['avatarUrls'][$i] = $this->toVirtualPath($savePath);
				move_uploaded_file($avatar["tmp_name"], $savePath);
				$success_num++;
			}
		}
		//<------------------------------------------------------------------------处理头像图片结束
		//upload_url中传递的额外的参数，如果定义的method为get请换为$_GET
		//$result["userid"]	= $_POST["userid"];
		//$result["username"]	= $_POST["username"];
		$result['pic'] = $filename.".jpg";
		
		$this->login_model->change_pic($result['pic']);
		
		$result['msg'] = $msg;
		if ($success_num > 0)
		{
			$result['success'] = true;
		}
		//返回图片的保存结果（返回内容为json字符串）
		print json_encode($result);
	}
        
}
