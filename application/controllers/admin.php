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
class Admin extends MY_Controller {
    
    public function __construct()
    {
        parent::__construct();
        if(!$this->session->userdata('username')){
        	redirect('login');
        }else{
	        if($this->session->userdata('admin_group') === '2')
	        	redirect('kf_manager');//客服经理
	        if($this->session->userdata('admin_group') === '3')
	        	redirect('user');//业务员
	        if($this->session->userdata('admin_group') === '4')
	        	redirect('wl_manager');//外联经理
	        if($this->session->userdata('admin_group') === '5')
	        	redirect('md_manager');//门店经理
        }
        $this->load->model('user_model');
		$projects = $this->sysconfig_model->get_projects();
        $this->cismarty->assign('projects',$projects);
    }
    
    //默认首页
    public function index()
    {
    	$data = $this->user_model->get_bb_count();
    	$this->cismarty->assign('data',$data);
    	$this->cismarty->display('user_index.html');
    }
    
    public function kfjlgl(){
    	$data = $this->user_model->list_managers();
    	$this->cismarty->assign('data',$data);
    	$this->cismarty->display('kfjlgl.html');
    }
    
    public function save_kfjl(){
    	$rs = $this->user_model->save_kfjl();
    	if($rs == '1'){
    		$this->show_message('保存成功！',site_url('admin/kfjlgl'),'1');
    	}
    	if($rs == '-1'){
    		$this->show_message('该用户已经存在，请核实！','','1');
    	}
    	if($rs == '-99'){
    		$this->show_message('系统错误！','','1');
    	}
    }
    
    
    public function qdjlgl(){
    	$data = $this->user_model->list_qdjl();
    	$this->cismarty->assign('data',$data);
    	$this->cismarty->display('qdjlgl.html');
    }
    
    public function save_qdjl(){
    	$rs = $this->user_model->save_qdjl();
    	if($rs == '1'){
    		$this->show_message('保存成功！',site_url('admin/qdjlgl'),'1');
    	}
    	if($rs == '-1'){
    		$this->show_message('该用户已经存在，请核实！','','1');
    	}
    	if($rs == '-99'){
    		$this->show_message('系统错误！','','1');
    	}
    }
    
    public function projects(){
    	$this->cismarty->display('projects.html');
    }
    
    public function admin_bb_list(){
    	$projects = $this->sysconfig_model->get_projects();
    	$this->cismarty->assign('projects',$projects);
    	$data = $this->user_model->admin_bb_list();
    	$this->cismarty->assign('data',$data);
    	$this->cismarty->display('admin_bb_list.html');
    }
    
    
    
}

/* End of file index.php */
/* Location: ./application/controllers/default.php */