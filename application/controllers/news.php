<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class News extends MY_Controller {
    
    public function __construct()
    {
        parent::__construct();
        $this->load->model('news_model');
        $this->cismarty->assign('flag','b');
    }
    
    //默认首页
    public function index()
    {
    	$total_page = $this->news_model->get_total_page();
    	$this->cismarty->assign('total_page',$total_page);
    	$this->cismarty->display('news.html');
    }
    
    public function list_news($page){
    	$news = $this->news_model->list_news($page);
    	echo json_encode($news);
    }
    
    public function save_comment(){
    	if(!$this->session->userdata('username')){
        	echo '-1';
        	exit();
        }
        $rs = $this->news_model->save_comment();
        echo $rs;
    }
    
}

/* End of file index.php */
/* Location: ./application/controllers/default.php */