<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Manage extends MY_Controller {



	public function __construct()
	{
		parent::__construct();
		$this->load->model('manage_model');
		$this->load->library('image_lib');
		$this->load->helper('directory');

	}

	function _remap($method,$params = array())
	{
		if(! $this->session->userdata('username'))
		{
			if($this->input->is_ajax_request()){
				header('Content-type: text/json');
				echo '{
                        "statusCode":"301",
                        "message":"\u4f1a\u8bdd\u8d85\u65f6\uff0c\u8bf7\u91cd\u65b0\u767b\u5f55\u3002"
                    }';
			}else{
				redirect(site_url('manage_login/login'));
			}

		}else{
			return call_user_func_array(array($this, $method), $params);
		}
	}

	/**
	 *
	 *
	 */
	public function index()
	{
		$this->load->view('manage/index.php');
	}


	

	/**
	 *
	 * ***************************************以下为重要信息控制器*******************************************************************
	 */
	public function message(){
		$data = $this->manage_model->get_message();
		$this->load->view('manage/message.php',$data);
	}

	public function save_message(){
		$rs = $this->manage_model->save_message();
		if ($rs === 1) {
			form_submit_json("200", "操作成功", "message", "", "");
		} else {
			form_submit_json("300", $rs);
		}
	}
	
	/**
	 *
	 * ***************************************以下为楼盘项目*******************************************************************
	 */
	public function list_project(){
		$data = $this->manage_model->list_project();
		$this->load->view('manage/list_project.php',$data);
	}

	public function add_project(){
		$this->load->view('manage/add_project.php');
	}
	
	public function edit_project($id){
		$data = $this->manage_model->get_project($id);
		$this->load->view('manage/add_project.php',$data);
	}
	
	public function delete_project($id){
		$rs = $this->manage_model->delete_project($id);
		if ($rs === 1) {
			form_submit_json("200", "操作成功", "list_project", "", "");
		} else {
			form_submit_json("300", $rs);
		}
	}
	
	public function save_project(){
		$rs = $this->manage_model->save_project();
		if ($rs === 1) {
			form_submit_json("200", "操作成功", "list_project");
		} else {
			form_submit_json("300", $rs);
		}
	}
	
	
	public function list_news(){
		$data = $this->manage_model->list_news();
		$this->load->view('manage/list_news.php',$data);
	}

	public function add_news(){
		$this->load->view('manage/add_news.php');
	}

	public function delete_news($id){
		$rs = $this->manage_model->delete_news($id);
		if ($rs === 1) {
			form_submit_json("200", "操作成功", "list_news", "", "");
		} else {
			form_submit_json("300", $rs);
		}
	}

	public function edit_news($id){
		$data = $this->manage_model->get_news($id);
		$this->load->view('manage/add_news.php',$data);
	}

	public function save_news(){
		$rs = $this->manage_model->save_news();
		if ($rs === 1) {
			form_submit_json("200", "操作成功", "list_news");
		} else {
			form_submit_json("300", $rs);
		}
	}
	
	
	
	

	
	
	//webedit的图片上传
	public function upload_pic(){
		$path = './././uploadfiles/others/';
		$path_out = '/uploadfiles/others/';
		$msg = '';

		//设置原图限制
		$config['upload_path'] = $path;
		$config['allowed_types'] = 'gif|jpg|png|jpeg';
		$config['max_size'] = '1000';
		$config['encrypt_name'] = true;
		$this->load->library('upload', $config);

		if($this->upload->do_upload('filedata')){
			$data = $this->upload->data();
			$targetPath = $path_out.$data['file_name'];
			$msg="{'url':'".$targetPath."','localname':'','id':'1'}";
			$err = '';
		}else{
			$err = $this->upload->display_errors();
		}
		echo "{'err':'".$err."','msg':".$msg."}";
	}
	

}

/* End of file manage.php */
/* Location: ./application/controllers/manage.php */