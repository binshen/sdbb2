<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * 外联经理控制器
 * 
 * @package		app
 * @subpackage	core
 * @category	controller
 * @author		yaobin<645894453@qq.com>
 *
 */
class Md_manager extends MY_Controller {
    
    public function __construct()
    {
        parent::__construct();
        $this->load->model('user_model');
        if(!$this->session->userdata('username')){
        	redirect('login');
        }else{
        	if($this->session->userdata('admin_group') === '1')
	        	redirect('admin');//管理员
            if($this->session->userdata('admin_group') === '2')
	        	redirect('kf_manager');//客服经理
	        if($this->session->userdata('admin_group') === '3')
	        	redirect('user');//业务员
	        if($this->session->userdata('admin_group') === '4')
	        	redirect('wl_manager');//外联经理
        }
		$projects = $this->sysconfig_model->get_projects();
        $this->cismarty->assign('projects',$projects);
        $this->cismarty->assign('flag','d');
        $this->cismarty->assign('rel_name',$this->session->userdata('rel_name'));
    }
    
    //默认首页
    public function index()
    {
    	$data = $this->user_model->get_md_data();
    	$this->cismarty->assign('all_count',$data);
    	$this->cismarty->display('md_manager.html');
    }
    
//    public function qy_company(){
//    	//$list = $this->user_model->list_company_wl();
//    	//$this->cismarty->assign('list',$list);
//    	$this->cismarty->display('md_company.html');
//    }
//    
    public function save_ywy(){
    	$rs = $this->user_model->save_ywy();
    	if($rs == '1'){
    		$this->show_message('保存成功',site_url('md_manager/list_ywy'),'1');
    	}else if($rs == '-1'){
    		$this->show_message('改账号已经存在！','','1');
    	}else{
    		$this->show_message('保存失败','','1');
    	}
    }
  
    public function get_ywy($id){
    	$data = $this->user_model->get_ywy($id);
    	echo json_encode($data);
    }
    
    public function list_ywy(){
    	$data = $this->user_model->list_ywy();
    	$this->cismarty->assign('data',$data);
    	$this->cismarty->display('list_ywy.html');
    }
    
    public function huoyue($type=1){
    	$data = $this->user_model->md_huoyue($type);
    	$this->cismarty->assign('data',$data);
    	$this->cismarty->display('md_huoyue.html');
    }
    
    public function main_list(){
    	$data = $this->sysconfig_model->main_list();
    	$this->cismarty->assign('data',$data);
    	$this->cismarty->display('main_list.html');
    }
    
    public function wybb($project_id=''){
    	$projects = $this->sysconfig_model->get_projects();
    	$this->cismarty->assign('projects',$projects);
    	$this->cismarty->assign('project_id',$project_id);
    	$this->cismarty->display('wybb.html');
    }
    
    public function save_bb(){
    	$rs = $this->user_model->save_bb();
    	if($rs == '1'){
    		$this->show_message('保存成功',site_url('md_manager/wdbb'),'1');
    	}else{
    		$this->show_message('保存失败','','1');
    	}
    }
    
    public function wdbb(){
    	$projects = $this->sysconfig_model->get_projects();
    	$this->cismarty->assign('projects',$projects);
    	$data = $this->user_model->list_wdbb();
    	$this->cismarty->assign('data',$data);
    	$this->cismarty->display('wdbb.html');
    }
    
    public function del_bb($id){
    	$rs = $this->user_model->del_bb($id);
    	echo $rs;
    }
    
    public function re_bb($id){
    	$rs = $this->user_model->re_bb($id);
    	echo $rs;
    }
    
    public function get_bz($id){
    	$rs = $this->user_model->get_bz($id);
    	echo $rs;
    }
    
    public function save_bz(){
    	$rs = $this->user_model->save_bz();
    	echo $rs;
    }
    
    public function save_re_bb(){
    	$rs = $this->user_model->save_re_bb();
    	echo $rs;
    }
    
	
}

/* End of file index.php */
/* Location: ./application/controllers/default.php */