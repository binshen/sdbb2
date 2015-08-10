<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * 登录后的默认控制器
 *
 * @package		app
 * @subpackage	core
 * @category	controller
 * @author		yaobin<645894453@qq.com>
 *
 */
class Index extends MY_Controller {

	public function __construct()
	{
		parent::__construct();
		if($this->session->userdata('username')){
			$this->cismarty->assign('rel_name',$this->session->userdata('rel_name'));
		}else{
			redirect('login');
		}
	}
	
	public function index(){
		if($this->session->userdata('admin_group') === '1')
			redirect('admin');//管理员
		if($this->session->userdata('admin_group') === '2')
			redirect('kf_manager');//客服经理
		if($this->session->userdata('admin_group') === '3')
			redirect('user');//业务员
		if($this->session->userdata('admin_group') === '4')
			redirect('wl_manager');//外联经理
		if($this->session->userdata('admin_group') === '5')
			redirect('md_manager');//门店经理
	}

}

/* End of file index.php */
/* Location: ./application/controllers/default.php */