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
class Set_time extends CI_Controller {
    
    public function __construct()
    {
        parent::__construct();
		
    }
    
    public function index()
    {
		$this->sysconfig_model->exe_main();
    }
    
    public function send_total_mail(){
    	$data = $this->sysconfig_model->get_total_data();
    	$content = '<!doctype html>
			<html>
			<head>
			<meta charset="utf-8">
			<title>无标题文档</title>
			<style type="text/css">
			html,body,div,tr,th,td{margin:0;padding:0;}
			
			div table{}
			div table th{border-top:2px solid #749dd5; }
			div table td,div table th{padding:5px 10px;background:#c0e0ff;color:#696969; font-size:14px;}
			div table .trfff td{background:#fff;}
			</style>
			</head>
			
			<body>
			<div style="padding:0;margin:2em 0; width:100%;"> 
			    <table cellpadding="0" cellspacing="0" border="0" align="center" width="100%">
			        <tr>
			            <th align="center" width="40%"></th><th align="center" width="20%">报备</th><th align="center" width="20%">带看</th><th align="center"  width="20%">成交</th>
			        </tr>';
    	foreach($data as $k=>$v){
    		$content = $content."<tr class='trfff'>
			            <td align='center'>{$k}</td><td align='center'>{$v[1]}</td><td align='center'>{$v[3]}</td><td align='center'>{$v[5]}</td>
			        </tr>";
    	}
			
			
			$content = $content.'</table>
			</div>
			</body>
			</html>';
    	$this->send_mail_model('645894453@qq.com','报备系统',$content);
    }
    
    /**
     * 发送邮件
     * @param varchar $to_mail 收件人
     * @param varchar $title 标题
     * @param varchar $content 内容
     * @return int 1成功，-1失败
     **/
    public function send_mail_model($to_mail,$title,$content)
    {
    	$mail =  $to_mail;//需要发送的邮箱
    	$name = "报备系统";//发件人姓名
    
    	$this->load->library('email');            //加载CI的email类
    
    	//以下设置Email参数
    	$config['protocol'] = 'smtp';
    	$config['smtp_host'] = 'smtp.163.com';
    	$config['smtp_user'] = 'bigmanager@163.com';
    	$config['smtp_pass'] = 'izhxwtaarumrxhgj';
    	$config['charset'] = 'utf-8';
    	$config['wordwrap'] = TRUE;
    	$config['mailtype'] = 'html';
    	$this->email->initialize($config);
    
    	//以下设置Email内容
    	$this->email->from('bigmanager@163.com', $name);
    	$this->email->to($mail);
    	$this->email->subject($title);
    	$this->email->message($content);
    	//$this->email->attach('application\controllers\1.jpeg');			//相对于index.php的路径
    	$this->email->send();

    	//return $this->email->print_debugger();		//返回包含邮件内容的字符串，包括EMAIL头和EMAIL正文。用于调试。
    }

    
}
