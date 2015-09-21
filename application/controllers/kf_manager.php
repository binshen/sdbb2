<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * 客服经理控制器
 * 
 * @package		app
 * @subpackage	core
 * @category	controller
 * @author		yaobin<645894453@qq.com>
 *
 */
class Kf_manager extends MY_Controller {
    
    public function __construct()
    {
        parent::__construct();
        if(!$this->session->userdata('username')){
        	redirect('login');
        }
        $this->load->model('user_model');
        $this->cismarty->assign('admin_group',$this->session->userdata('admin_group'));
		if($this->session->userdata('admin_group') === '1')
        	redirect('admin');//管理员
        if($this->session->userdata('admin_group') === '3')
        	redirect('user');//业务员
        if($this->session->userdata('admin_group') === '4')
        	redirect('wl_manager');//外联经理
        if($this->session->userdata('admin_group') === '5')
        	redirect('md_manager');//门店经理
        	
        	
		$this->cismarty->assign('rel_name',$this->session->userdata('rel_name'));
		$this->cismarty->assign('flag','d');
		
    }
    
    public function index()
    {
    	$this->m_list_bb();
    	//$data = $this->user_model->get_kf_data();
    	//$this->cismarty->assign('all_count',$data);
    	//$this->cismarty->display('kf_manager.html');
    }
    
    public function m_list_bb(){
        if($this->session->userdata('admin_group') != '2'){
    		$this->show_message('权限不足','','1');
    	}
        $projects = $this->sysconfig_model->get_projects();
        $this->cismarty->assign('projects',$projects);
        $data = $this->user_model->m_list_bb();
		$this->cismarty->assign('data',$data);
    	$this->cismarty->display('m_list_bb.html');
    }
    
    public function confirm_dk($id){
        if($this->session->userdata('admin_group') != '2'){
    		$this->show_message('权限不足','','1');
    	}
        $rs = $this->user_model->confirm_dk($id);
    	echo $rs;
    }
    
    public function confirm_qy($id){
        if($this->session->userdata('admin_group') != '2'){
    		$this->show_message('权限不足','','1');
    	}
        $rs = $this->user_model->confirm_qy($id);
    	echo $rs;
    }
    
    public function reset_status($id){
    	if($this->session->userdata('admin_group') != '2'){
    		$this->show_message('权限不足','','1');
    	}
    	$rs = $this->user_model->reset_status($id);
    	echo $rs;
    }

    
}
