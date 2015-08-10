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
class Md_manager_pc extends MY_Controller {
    
    public function __construct()
    {
        parent::__construct();
        $this->load->model('user_model');
        if(!$this->session->userdata('username')){
        	redirect('login');
        }else{
        	if($this->session->userdata('admin_group') === '1')
	        	redirect('admin_pc');//管理员
            if($this->session->userdata('admin_group') === '2')
	        	redirect('kf_manager_pc');//客服经理
	        if($this->session->userdata('admin_group') === '3')
	        	redirect('user_pc');//业务员
	        if($this->session->userdata('admin_group') === '4')
	        	redirect('wl_manager_pc');//外联经理
        }
		$projects = $this->sysconfig_model->get_projects();
        $this->cismarty->assign('projects',$projects);
        $this->cismarty->assign('flag','d');
        $this->cismarty->assign('rel_name',$this->session->userdata('rel_name'));
        $this->cismarty->assign('admin_group',$this->session->userdata('admin_group'));
        $all_count = $this->user_model->get_md_data();
    	$this->cismarty->assign('all_count',$all_count);
    }
    
    //默认首页
    public function index()
    {

        $data = $this->user_model->list_ywy();
        $data['page_list'] = $this->get_page_list($data['total_page'],$data['pageNum']);
    	if($data['page_list']){
			$data['min_p'] = min($data['page_list']);
			$data['max_p'] = max($data['page_list']);
		}
    	$this->cismarty->assign('data',$data);
    	$this->cismarty->display('pc/md_manager.html');
    }
    
    public function save_ywy(){
    	$rs = $this->user_model->save_ywy();
    	if($rs == '1'){
    		$this->show_message('保存成功',site_url('md_manager_pc/index'),'1');
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
    
    public function add_ywy(){
    	$this->cismarty->display('pc/add_ywy.html');
    }
    
    public function huoyue($type = 1){
    	$data = $this->user_model->md_huoyue($type);
        $data['page_list'] = $this->get_page_list($data['total_page'],$data['pageNum']);
    	if($data['page_list']){
			$data['min_p'] = min($data['page_list']);
			$data['max_p'] = max($data['page_list']);
		}
    	$this->cismarty->assign('data',$data);
    	$this->cismarty->display('pc/md_huoyue.html');
    }
    
    public function main_list(){
    	$data = $this->sysconfig_model->main_list();
        $data['page_list'] = $this->get_page_list($data['total_page'],$data['pageNum']);
    	if($data['page_list']){
			$data['min_p'] = min($data['page_list']);
			$data['max_p'] = max($data['page_list']);
		}
    	$this->cismarty->assign('data',$data);
    	$this->cismarty->display('pc/main_list.html');
    }
    
	
}

/* End of file index.php */
/* Location: ./application/controllers/default.php */