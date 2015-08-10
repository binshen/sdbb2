<?php
if (! defined('BASEPATH'))
    exit('No direct script access allowed');
/**
 * 扩展业务控制器
 *
 * @package		app
 * @subpackage	Libraries
 * @category	controller
 * @author      yaobin<645894453@qq.com>
 *        
 */
class MY_Controller extends CI_Controller
{

    public function __construct ()
    {
        parent::__construct();
        //初始数据
        $this->cismarty->assign('base_url',base_url());//url路径
		ini_set('date.timezone','Asia/Shanghai');
		if($this->session->userdata('username')){
			$pic = $this->sysconfig_model->get_pic();
			if($pic){
				$this->cismarty->assign('pic',$pic);
			}else{
				$this->cismarty->assign('pic','auto.jpg');
			}
			
		}else{
			$this->cismarty->assign('pic','');
		}
		//$this->session->sess_destroy();die();
		//var_dump($this->session->all_userdata());die;
	    if ( strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') !== false ) {
	    	if(!$this->session->userdata('openid')){
	    		$appid="wx84455ea5b029beb2";
				$secret="c9df7b05ce5aec516f9893079d246dd4";
				if(empty($_GET['code'])){
					$url = 'http://'.$_SERVER['SERVER_NAME'].$_SERVER["REQUEST_URI"]; 
					$url = urlencode($url);
					redirect("https://open.weixin.qq.com/connect/oauth2/authorize?appid=wx84455ea5b029beb2&redirect_uri={$url}&response_type=code&scope=snsapi_base&state=STATE#wechat_redirect");
				}else{
					$j_access_token=file_get_contents("https://api.weixin.qq.com/sns/oauth2/access_token?appid={$appid}&secret={$secret}&code={$_GET['code']}&grant_type=authorization_code");
					$a_access_token=json_decode($j_access_token,true);
					$access_token=$a_access_token["access_token"];
					$openid=$a_access_token["openid"];

					$rs = $this->sysconfig_model->check_openid($openid);
					if($rs == 1){
						$this->session->set_userdata('openid', $openid);
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
						$this->session->set_userdata('openid', $openid);
					}
				}	
	    	}
			
			
			
	    }
    }
    
    
	//重载smarty方法assign
	public function assign($key,$val) {  
        $this->cismarty->assign($key,$val);  
    }  
    
	//重载smarty方法display
    public function display($html) {
        $this->cismarty->display($html);  
    }
    
    /**
     * 获取产品菜单的树状结构
     **/
    public function subtree($arr,$id=0,$lev=1)
    {
    	static $subs = array();
    	foreach($arr as $v){
    		if((int)$v['parent_id']==$id){
    		    $v['lev'] = $lev;
    		    $subs[]=$v;
    		    $this->subtree($arr,$v['id'],$lev+1);
    		}
    	}
    	return $subs;
    }
    
	/**
     * 获取页码列表
     * 例如<上一页>...56789<下一页>
     * @param int $total 总页数
     * @param int $current 当前页
     * @param int $page_list_size 显示页码个数
     * @return array 显示页码的数组
     **/
    public function get_page_list($total,$current,$page_list_size = '7')
    {
	    $page= array();
		if($total<$page_list_size){
			for($i=1;$i<=$total;$i++){
				$page[]=$i;
			}
		}else{
			if($current <= ceil($page_list_size/2)){
			//当前页小于居中页码，则正常打印
				for($i=1;$i<=$page_list_size;$i++){
					$page[]=$i;
				}
				
			}else if($current > ($total - ceil($page_list_size/2))){
			//最后几页正常打印
				for($i=0;$i<$page_list_size;$i++){
					$page[]=$total-$i;
				}
				$page = array_reverse($page);
			}else{
				for($i=$current-floor($page_list_size/2);$i<=$current+floor($page_list_size/2);$i++){
					$page[]=$i;
				}
			}
		}
		return array_reverse($page);
    }
    
	/**
	 * 提示信息
	 * @param varchar $message 提示信息
	 * @param varchar $url 跳转页面，如果为空则后退
	 * @param int $type 提示类型，1是自动关闭的提示框，2是错误提示框
	 * @return array 显示页码的数组
	 **/
	public function show_message($message,$url=null,$type){
		if($url){
			$js = "location.href='".$url."';";
		}else{
			$js = "history.back();";
		}

		if($type=='1'){
			echo "<!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.0 Transitional//EN' 'http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd'>
				<html xmlns='http://www.w3.org/1999/xhtml'>
				<head>
				<meta http-equiv='Content-Type' content='text/html; charset=utf-8' />
				<meta name='viewport' content='width=device-width,initial-scale=1.0,maximum-scale=1.0,user-scalable=no' />
				<title>".$message."</title>
				<script src='".base_url()."js/jquery-v1.10.2.min.js'></script>
				<link rel='stylesheet' href='".base_url()."css/easydialog.css'>
				</head>
				<body>
				<script src='".base_url()."js/easydialog.min.js'></script>
				<script>
				var callFn = function(){
				  ".$js."
				};
				easyDialog.open({
					container : {
						content : '".$message."'
					},
					autoClose : 2000,
					callback : callFn
					
				});
				
				</script>
				</body>
				</html>";
		}
		exit;
	}
	
	/**************************************************************
	*  生成指定长度的随机码。
	*  @param int $length 随机码的长度。
	*  @access public
	**************************************************************/
	function createRandomCode($length)
	{
		$randomCode = "";
		$randomChars = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
		for ($i = 0; $i < $length; $i++)
		{
			$randomCode .= $randomChars { mt_rand(0, 35) };
		}
		return $randomCode;
	}
	
	/**************************************************************
	*  生成指定长度的随机码。
	*  @param int $length 随机码的长度。
	*  @access public
	**************************************************************/
	function toVirtualPath($physicalPpath)
	{
		$virtualPath = str_replace($_SERVER['DOCUMENT_ROOT'], "", $physicalPpath);
		$virtualPath = str_replace("\\", "/", $virtualPath);
		return $virtualPath;
	}
}

/* End of file MY_Controller.php */
/* Location: ./application/core/MY_Controller.php */