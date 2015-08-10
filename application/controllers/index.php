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
class Index extends MY_Controller {

	public function __construct()
	{
		parent::__construct();
		if($this->session->userdata('username')){
			$this->cismarty->assign('rel_name',$this->session->userdata('rel_name'));
			if($this->session->userdata('admin_group') === '1')
			$admin_group = "管理员";
			if($this->session->userdata('admin_group') === '2')
			$admin_group = "经理";
			if($this->session->userdata('admin_group') === '3')
			$admin_group = "业务员";
			
		}else{
			redirect('login');
		}
		$this->load->model('admin_model');
		$this->load->model('user_model');
		$this->cismarty->assign('flag','a');
	}

	//默认首页
	public function index()
	{
		$rs = $this->_is_mobilephone();
		if($rs){//手机端登陆
//			$data = $this->sysconfig_model->get_index_info();
//			$this->cismarty->assign('index_info',$data);
//			$this->cismarty->display('index.html');
			
			if($this->session->userdata('admin_group') === '1')
				redirect('admin');//管理员
	        if($this->session->userdata('admin_group') === '2')
        		redirect('kf_manager');//客服经理
	        if($this->session->userdata('admin_group') === '3')
	        	redirect('user');//业务员
	        if($this->session->userdata('admin_group') === '4')
	        	redirect('wl_manager');//外联经理
	        if($this->session->userdata('admin_group') === '5')
	        	redirect('md_manager');//门店经理

		}else{
			if($this->session->userdata('username')){
				$user_info = $this->sysconfig_model->get_user_info();
				$this->cismarty->assign('user_info',$user_info);

		        if($user_info['admin_group'] === '1'){
	                $data = $this->admin_model->get_admin_data();
    				$this->cismarty->assign('all_count',$data);
		        }
					
		        if($user_info['admin_group'] === '2'){//客服经理
	            	$data = $this->user_model->get_kf_data();
    				$this->cismarty->assign('all_count',$data);
		        }
				if($user_info['admin_group'] === '3'){//业务员
					$data = array();
		        	$data['bb_count'] = $user_info['bb_count'];
		        	$data['dk_count'] = $user_info['dk_count'];
		        	$data['qy_count'] = $user_info['qy_count'];
		        	$this->cismarty->assign('all_count',$data);
		        }
				if($user_info['admin_group'] === '4'){//外联经理
		        	$data = $this->user_model->get_wl_data();
    				$this->cismarty->assign('all_count',$data);
		        }
				if($user_info['admin_group'] === '5'){//门店经理
            		$data = $this->user_model->get_md_data();
    				$this->cismarty->assign('all_count',$data);
		        }
				
			}else{
				$this->cismarty->assign('user_info','');
			}
			$this->cismarty->display('pc/index.html');
		}

	}


	public function contains($str = '', $search_str)
	{
		return strpos($str, $search_str) === FALSE ? FALSE : TRUE;
	}


	/**
	 * 判断手机登陆
	 */

	function _is_mobilephone()
	{
		$agent = $_SERVER['HTTP_USER_AGENT'];
		//$keywords = array("Android", "iPhone", "iPod", "iPad", "Windows Phone", "MQQBrowser");
		$mobile_os_list=array('Google Wireless Transcoder','Windows CE','WindowsCE','Symbian','Android','armv6l','armv5','Mobile','CentOS','mowser','AvantGo','Opera Mobi','J2ME/MIDP','Smartphone','Go.Web','Palm','iPAQ');
		$mobile_token_list=array('Profile/MIDP','Configuration/CLDC-','160×160','176×220','240×240','240×320','320×240','UP.Browser','UP.Link','SymbianOS','PalmOS','PocketPC','SonyEricsson','Nokia','BlackBerry','Vodafone','BenQ','Novarra-Vision','Iris','NetFront','HTC_','Xda_','SAMSUNG-SGH','Wapaka','DoCoMo','iPhone','iPod');
		$mobile_list = array_merge($mobile_token_list, $mobile_os_list);
		//排除Windows
		if (!$this->contains($agent, "Windows NT") || ($this->contains($agent, "Windows NT") && $this->contains($agent, "compatible; MSIE"))) {
			//排除Mac
			if (!$this->contains($agent, "Windows NT") && !$this->contains($agent, "Macintosh")) {
				foreach ($mobile_list as $k => $item) {
					if ($this->contains($agent, $item)) {
						return true;
					}
				}
			}
		}
		return false;
	}
}

/* End of file index.php */
/* Location: ./application/controllers/default.php */