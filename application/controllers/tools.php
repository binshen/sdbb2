<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Tools extends MY_Controller {
    
    public function __construct()
    {
        parent::__construct();
    	if($this->session->userdata('username')){
            if($this->session->userdata('admin_group') === '1')
        		$admin_group = "管理员";
        	if($this->session->userdata('admin_group') === '2')
        		$admin_group = "经理";
        	if($this->session->userdata('admin_group') === '3')
        		$admin_group = "业务员";
        	$this->cismarty->assign('rel_name',$this->session->userdata('rel_name'));
        }else{
        	$this->cismarty->assign('rel_name','');
        }
        $this->cismarty->assign('flag','c');
    }
    
    //默认首页
    public function index()
    {
    	$this->cismarty->display('tools.html');
    }
    
    public function fangdai()
    {
    	$this->cismarty->display('fangdai.html');
    }
    
}

/* End of file index.php */
/* Location: ./application/controllers/default.php */