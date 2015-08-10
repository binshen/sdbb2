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
        $this->load->model('admin_model');
		$projects = $this->sysconfig_model->get_projects();
        $this->cismarty->assign('projects',$projects);
        $this->cismarty->assign('flag','d');
    }
    
    //默认首页
    public function index()
    {
        $data = $this->admin_model->get_admin_data();
    	$this->cismarty->assign('all_count',$data);
    	$this->cismarty->display('admin.html');
    }
    
    public function kfjlgl(){
    	$data = $this->admin_model->list_managers();
    	$this->cismarty->assign('data',$data);
    	$this->cismarty->display('kfjlgl.html');
    }
    
    public function add_kfjl(){
    	$this->cismarty->display('kfjlgl.html');
    }
    
    public function save_kfjl(){
    	$rs = $this->admin_model->save_kfjl();
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
    
    function get_kfjl($id){
    	$data = $this->admin_model->get_kfjl($id);
    	echo json_encode($data);
    }
    
    
    public function qdjlgl(){
    	$data = $this->admin_model->list_qdjl();
    	$this->cismarty->assign('data',$data);
    	$this->cismarty->display('qdjlgl.html');
    }
    
    public function add_qdjl(){
    	$this->cismarty->display('kfjlgl.html');
    }
    
    public function save_qdjl(){
    	$rs = $this->admin_model->save_qdjl();
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
    
    
    function get_qdjl($id){
    	$data = $this->admin_model->get_qdjl($id);
    	echo json_encode($data);
    }
    
    public function projects(){
    	$this->cismarty->display('projects.html');
    }
    
    
    public function main_list(){
    	$data = $this->sysconfig_model->main_list();
    	$this->cismarty->assign('data',$data);
    	$this->cismarty->display('main_list.html');
    }
    
    
    
}

/* End of file index.php */
/* Location: ./application/controllers/default.php */