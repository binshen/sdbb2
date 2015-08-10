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
class User extends MY_Controller {
    
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
        if($this->session->userdata('admin_group') === '2')
        	redirect('kf_manager');//客服经理
        if($this->session->userdata('admin_group') === '4')
        	redirect('wl_manager');//外联经理
        if($this->session->userdata('admin_group') === '5')
        	redirect('md_manager');//门店经理
        	
        	
		$this->cismarty->assign('rel_name',$this->session->userdata('rel_name'));
		
		$count = $this->user_model->get_bb_count();
		$this->cismarty->assign('count',$count);
		
    }
    
    public function index()
    {
    	$this->wybb();
    	//$this->show_message('操作成功！！');
    	$this->cismarty->display('user_index.html');
    }
    
    public function wybb($project_id=''){
    	if($this->session->userdata('admin_group') != '3'){
    		$this->show_message('权限不足','','1');
    	}
        $projects = $this->sysconfig_model->get_projects();
        $this->cismarty->assign('projects',$projects);
        $this->cismarty->assign('project_id',$project_id);
    	$this->cismarty->display('wybb.html');
    }
    
    public function save_bb(){
        if($this->session->userdata('admin_group') != '3'){
    		$this->show_message('权限不足','','1');
    	}
    	$rs = $this->user_model->save_bb();
    	if($rs == '1'){
    		$this->show_message('保存成功',site_url('user/wdbb'),'1');
    	}else{
    		$this->show_message('保存失败','','1');
    	}
    }
    
    
    public function wdbb(){
        if($this->session->userdata('admin_group') != '3'){
    		$this->show_message('权限不足','','1');
    	}
    	$projects = $this->sysconfig_model->get_projects();
        $this->cismarty->assign('projects',$projects);
        $data = $this->user_model->list_wdbb();
		$this->cismarty->assign('data',$data);
    	$this->cismarty->display('wdbb.html');
    }
	
    public function del_bb($id){
    	if($this->session->userdata('admin_group') != '3'){
    		$this->show_message('权限不足','','1');
    	}
    	$rs = $this->user_model->del_bb($id);
    	echo $rs;
    }
    
    //重新报备
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
