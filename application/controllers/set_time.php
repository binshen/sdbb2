<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * 设置时间自动处理
 * 
 * @package		app
 * @subpackage	core
 * @category	controller
 * @author		yaobin<645894453@qq.com>
 *
 */
class Set_time extends MY_Controller {
    
    public function __construct()
    {
        parent::__construct();
		
    }
    
    public function index()
    {
		$this->sysconfig_model->exe_main();
    }
    
//     public function test(){
//     	$rs = $this->sysconfig_model->send_message();
//     	foreach($rs as $k=>$v){
//     		$user = $this->message['user'];
// 			$pwd = md5($this->message['pwd']);
//     		$phone = $v['phone'];
// 			$p_next = $v['next_phone'];
			
// 			$con = mb_convert_encoding('温馨提示：您的客户'.$v['name'].'已经到期！','gbk');
// 			$url="http://api.52ao.com/?user={$user}&pass={$pwd}&mobile={$phone}&content={$con}"; 
// 			$fcontent=@file_get_contents($url);
			
// 			if($p_next){
// 				$con_next = mb_convert_encoding('温馨提示：客户'.$v['name'].'已经由您负责，请及时查看','gbk');
// 				$url2="http://api.52ao.com/?user={$user}&pass={$pwd}&mobile={$p_next}&content={$con_next}"; 
// 				$fcontent2=@file_get_contents($url2);
// 			}
			
			
//     	}
//     }
    
}
