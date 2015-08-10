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
class Pc extends MY_Controller {
    
    public function __construct()
    {
        parent::__construct();
    	if(!$this->session->userdata('username')){
    		$this->cismarty->assign('err','');
        	$this->cismarty->display('pc_login.html');
        	exit();
        }
        $this->load->model('user_model');
        $this->load->model('pc_model');
        $this->load->model('login_model');
        $this->load->model('admin_model');
		$this->cismarty->assign('rel_name',$this->session->userdata('rel_name'));
		$this->cismarty->assign('admin_group',$this->session->userdata('admin_group'));
    }
    
   
    public function index()
    {
    	$data=$this->pc_model->get_index_info();
    	$this->cismarty->assign('projects',$data['projects']);
    	$this->cismarty->assign('main_list',$data['main_list']);
    	
    	$this->cismarty->assign('num1',$data['num1']);
    	$this->cismarty->assign('num2',$data['num2']);
    	$this->cismarty->assign('phone',$this->session->userdata('phone'));
    	$this->cismarty->display('pc_index.html');
    }
    
	public function cust(){
		$per_page=10;//每页显示多少调数据
    	$total_rows=$this->pc_model->get_count();//总记录数
    	$total_page = ceil($total_rows/$per_page); //总页数
    	$pageNum=$this->input->post('page')?$this->input->post('page'):1;//当前页
    	if($pageNum > $total_page & $total_rows > 0 || $pageNum <1){
    		echo "<meta http-equiv='Content-Type' content='text/html; charset=utf-8'><script>alert('页码错误');history.back();</script>";
    		exit();
    	}
    	
    	if($pageNum==1){
    		$offset=0; //从第几条记录开始查询
    	}else{
    		$offset=$per_page*($pageNum-1);//从第几条记录开始查询
    	}
    	$data=$this->pc_model->main_list($per_page,$offset);//根据分页查询出来的数据
    	$projects = $this->pc_model->get_all_pro();
    	
    	
    	//查询数据
    	$status = $this->input->post('status')?$this->input->post('status'):'';
    	$s_date = $this->input->post('s_date')?$this->input->post('s_date'):'';
    	$e_date = $this->input->post('e_date')?$this->input->post('e_date'):'';
		$name = $this->input->post('name')?$this->input->post('name'):'';
		$phone = $this->input->post('phone')?$this->input->post('phone'):'';
		$sex = $this->input->post('sex')?$this->input->post('sex'):'';
    	$project_id = $this->input->post('project_id')?$this->input->post('project_id'):'';

    	$this->cismarty->assign('project_id',$project_id);
		$this->cismarty->assign('name',$name);
		$this->cismarty->assign('phone',$phone);
		$this->cismarty->assign('sex',$sex);
    	$this->cismarty->assign('status',$status);
    	$this->cismarty->assign('projects',$projects);
    	$this->cismarty->assign('s_date',$s_date);
    	$this->cismarty->assign('e_date',$e_date);
    	$this->cismarty->assign('pageNum',$pageNum);
    	$this->cismarty->assign('total_rows',$total_page);
    	$this->cismarty->assign('page_list',array_reverse($this->get_page_list($total_page,$pageNum)));
    	$this->cismarty->assign('list',$data);
    	//var_dump(array_reverse($this->get_page_list($total_page,$pageNum)));die;
    	$this->cismarty->display('kehu.html');
	}
	
	public function list_projects(){
		$projects = $this->pc_model->get_all_pro();
		$this->cismarty->assign('projects',$projects);
		$this->cismarty->display('loupan.html');
	}
	
	public function member_info(){
		$this->cismarty->display('zhanghao.html');
	}
	
	public function change_pwd(){
		if(sha1($this->input->post('old_pwd')) != $this->session->userdata('passwd')){
			echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" /><script>alert("旧密码不正确");history.back();</script>';
        	exit();
		}else{
			$rs = $this->pc_model->change_pwd();
			if($rs){
				$this->session->sess_destroy();
				echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" /><script>alert("密码修改成功，请重新登陆");location.href="'.site_url('pc').'";</script>';
			}else{
				echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" /><script>alert("系统错误");history.back();</script>';
        		exit();
			}
		}
	}
	
	public function add_cust(){
		$data = array(
			'id'=>'',
			'project_id'=>'',
			'target'=>'',
			'sex'=>'',
			'name'=>'',
			'phone'=>'',
			'idno'=>'',
			'remark'=>''
		);
		$projects = $this->pc_model->get_all_pro();
    	$this->cismarty->assign('projects',$projects);
    	$this->cismarty->assign('info',$data);
		$this->cismarty->display('guanli.html');
	}
	
	public function edit_cust($id){
		$projects = $this->pc_model->get_all_pro();
    	$this->cismarty->assign('projects',$projects);
    	$this->cismarty->assign('info',$this->pc_model->get_main_info($id));
		$this->cismarty->display('guanli.html');
	}
	
	public function list_managers(){
		$m_p = $this->sysconfig_model->get_m_p();
		$managers = $this->admin_model->list_managers();
    	$projects = $this->admin_model->get_projects();
    	$this->cismarty->assign('projects',$projects);
    	$this->cismarty->assign('managers',$managers);
        $this->cismarty->assign('m_p',$m_p);
		$this->cismarty->display('manager_gl.html');
	}
	
	public function list_users(){
		$per_page=12;//每页显示多少调数据
    	$total_rows=$this->pc_model->get_count_user();//总记录数
    	$total_page = ceil($total_rows/$per_page); //总页数
    	$pageNum=$this->uri->segment(3)?$this->uri->segment(3):1;//当前页
    	if($pageNum > $total_page & $total_rows > 0 || $pageNum <1){
    		echo "<meta http-equiv='Content-Type' content='text/html; charset=utf-8'><script>alert('页码错误');history.back();</script>";
    		exit();
    	}
    	
    	if($pageNum==1){
    		$offset=0; //从第几条记录开始查询
    	}else{
    		$offset=$per_page*($pageNum-1);//从第几条记录开始查询
    	}
    	$data=$this->pc_model->user_list($per_page,$offset);//根据分页查询出来的数据
    	
    	$managers = $this->admin_model->list_managers();
    	$this->cismarty->assign('managers',$managers);
    	$this->cismarty->assign('pageNum',$pageNum);
    	$this->cismarty->assign('total_rows',$total_page);
    	$this->cismarty->assign('page_list',array_reverse($this->get_page_list($total_page,$pageNum)));
    	$this->cismarty->assign('list',$data);
    	$this->cismarty->display('manager_yw.html');
	}
	
	public function list_pros(){
		$projects = $this->pc_model->get_all_pro();
		$managers = $this->admin_model->list_managers();
		$this->cismarty->assign('managers',$managers);
		$this->cismarty->assign('projects',$projects);
		$this->cismarty->display('manager_fcxm.html');
	}
	
	public function change_status(){
		$rs = $this->pc_model->change_status();
		if($rs){
			echo '1';
		}else{
			echo '-1';
		}
	}
	
    public function save_bb(){
    	$rs = $this->user_model->save_bb();
    	echo $rs;
    }
	
}
