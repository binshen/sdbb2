<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * 业务员控制器
 * 
 * @package		app
 * @subpackage	core
 * @category	controller
 * @author		yaobin<645894453@qq.com>
 *
 */
class User_pc extends MY_Controller {
    
    public function __construct()
    {
        parent::__construct();
        if(!$this->session->userdata('username')){
        	redirect('login_pc');
        }
        $this->load->model('user_model');
        $this->cismarty->assign('admin_group',$this->session->userdata('admin_group'));
		if($this->session->userdata('admin_group') === '1')
        	redirect('admin');//管理员
        if($this->session->userdata('admin_group') === '2')
        	redirect('kf_manager');//客服经理
        if($this->session->userdata('admin_group') === '4')
        	redirect('wl_manager');//外联经理
        if($this->session->userdata('admin_group') === '5')
        	redirect('md_manager');//门店经理
        	
        	
		$this->cismarty->assign('rel_name',$this->session->userdata('rel_name'));
		$this->cismarty->assign('flag','d');
		
		$count = $this->user_model->get_bb_count();
		$this->cismarty->assign('count',$count);
		
    }
    
    public function index()
    {
        $projects = $this->sysconfig_model->get_projects();
        $this->cismarty->assign('projects',$projects);
        $data = $this->user_model->list_wdbb();
        
        
    	$data['page_list'] = $this->get_page_list($data['total_page'],$data['pageNum']);
		if($data['page_list']){
			$data['min_p'] = min($data['page_list']);
			$data['max_p'] = max($data['page_list']);
		}
        
		$this->cismarty->assign('data',$data);
    	$this->cismarty->display('pc/user_center_pc.html');
    }
    
    public function wybb($project_id=''){
    	if($this->session->userdata('admin_group') != '3'){
    		$this->show_message('权限不足','','1');
    	}
        $projects = $this->sysconfig_model->get_projects();
        $this->cismarty->assign('projects',$projects);
        $this->cismarty->assign('project_id',$project_id);
    	$this->cismarty->display('pc/wybb.html');
    }
    
    public function save_bb(){
        if($this->session->userdata('admin_group') != '3'){
    		$this->show_message('权限不足','','1');
    	}
    	$rs = $this->user_model->save_bb();
    	if($rs == '1'){
    		$this->show_message('保存成功',site_url('user_pc/index'),'1');
    	}else{
    		$this->show_message('保存失败','','1');
    	}
    }
    
    

	
    public function del_bb($id){
    	if($this->session->userdata('admin_group') != '3'){
    		$this->show_message('权限不足','','1');
    	}
    	$rs = $this->user_model->del_bb($id);
    	echo $rs;
    }
    
    public function re_bb($id){
        if($this->session->userdata('admin_group') != '3'){
    		$this->show_message('权限不足','','1');
    	}
        $rs = $this->user_model->re_bb($id);
    	echo $rs;
    }
    
    public function get_bz($id){
    	if($this->session->userdata('admin_group') != '3'){
    		$this->show_message('权限不足','','1');
    	}
    	$rs = $this->user_model->get_bz($id);
    	echo $rs;
    }
    
    public function save_bz(){
    	if($this->session->userdata('admin_group') != '3'){
    		$this->show_message('权限不足','','1');
    	}
        $rs = $this->user_model->save_bz();
    	echo $rs;
    }
    
    public function save_re_bb(){
    	if($this->session->userdata('admin_group') != '3'){
    		$this->show_message('权限不足','','1');
    	}
        $rs = $this->user_model->save_re_bb();
    	echo $rs;
    }
    
}
