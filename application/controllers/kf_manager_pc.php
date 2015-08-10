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
class Kf_manager_pc extends MY_Controller {
    
    public function __construct()
    {
        parent::__construct();
        if(!$this->session->userdata('username')){
        	redirect('login');
        }
        $this->load->model('user_model');
        $this->cismarty->assign('admin_group',$this->session->userdata('admin_group'));
		if($this->session->userdata('admin_group') === '1')
        	redirect('admin_pc');//管理员
        if($this->session->userdata('admin_group') === '3')
        	redirect('user_pc');//业务员
        if($this->session->userdata('admin_group') === '4')
        	redirect('wl_manager_pc');//外联经理
        if($this->session->userdata('admin_group') === '5')
        	redirect('md_manager_pc');//门店经理
        	
        	
		$this->cismarty->assign('rel_name',$this->session->userdata('rel_name'));
		$this->cismarty->assign('flag','d');
		
    }
    
    public function index()
    {
        if($this->session->userdata('admin_group') != '2'){
    		$this->show_message('权限不足','','1');
    	}
    	$all_count = $this->user_model->get_kf_data();
    	$this->cismarty->assign('all_count',$all_count);
    	
        $projects = $this->sysconfig_model->get_projects();
        $this->cismarty->assign('projects',$projects);
        $data = $this->user_model->m_list_bb();
        
        $data['page_list'] = $this->get_page_list($data['total_page'],$data['pageNum']);
		if($data['page_list']){
			$data['min_p'] = min($data['page_list']);
			$data['max_p'] = max($data['page_list']);
		}
        
		$this->cismarty->assign('data',$data);
    	
    	$this->cismarty->display('pc/kf_manager.html');
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

    
}
