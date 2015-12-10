<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * 接口控制器
 * 
 * @package		app
 * @subpackage	core
 * @category	controller
 * @author		yaobin<645894453@qq.com>
 *
 */
class Api extends CI_Controller {
    
    public function __construct() {
        parent::__construct();
        
        $this->load->model('funmall_model');
    }
    
    public function get_pro_byid($id){
		$project = $this->sysconfig_model->chenck_pro($id);
		echo $project;
	}
	
// 	public function test() {
// 		$this->funmall_model->bindBroker('11211', 23);
// 	}
	
	public function authorize() {
		$open_id = $this->session->userdata('openid');
		if(empty($open_id)) {
			if (strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') !== false) {
				$code = $_GET['code'];
				if(empty($code)){
					$url = 'http://'.$_SERVER['SERVER_NAME'].$_SERVER["REQUEST_URI"];
					redirect("https://open.weixin.qq.com/connect/oauth2/authorize?appid=".APP_ID."&redirect_uri=".urlencode($url)."&response_type=code&scope=snsapi_base&state=STATE#wechat_redirect");
				} else {
					$url = 'https://api.weixin.qq.com/sns/oauth2/access_token?appid='.APP_ID.'&secret='.APP_SECRET.'&code='.$code.'&grant_type=authorization_code';
					$result = file_get_contents($url);
					$jsonInfo = json_decode($result, true);
					$open_id = $jsonInfo['openid'];
					if(!empty($open_id)) {
						$this->session->set_userdata('openid', $open_id);
					}
				}
			}
		}
		$uri = "http://www.funmall.com.cn/b_house/index/";
		if(!empty($open_id)) {
			//file_get_contents('http://www.funmall.com.cn/api/update_weixin_user/' . $open_id);
			$uri .= $open_id . '/';
			$funmallDB = $this->load->database("funmall", True);
			$funmallDB->from('wx_user');
			$funmallDB->where('open_id', $open_id);
			$funmallDB->order_by('updated DESC');
			$wxUser = $funmallDB->get()->row_array();
			if(!empty($wxUser)) {
				$uri .= $wxUser['broker_id'] . '/';
			}
		}
		redirect($uri);
	}
	
	public function view_art($broker_id) {
		if (strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') !== false) {
			$code = $_GET['code'];
			if(empty($code)){
				$url = 'http://'.$_SERVER['SERVER_NAME'].$_SERVER["REQUEST_URI"];
				redirect("https://open.weixin.qq.com/connect/oauth2/authorize?appid=".APP_ID."&redirect_uri=".urlencode($url)."&response_type=code&scope=snsapi_base&state=STATE#wechat_redirect");
			} else {
				$url = 'https://api.weixin.qq.com/sns/oauth2/access_token?appid='.APP_ID.'&secret='.APP_SECRET.'&code='.$code.'&grant_type=authorization_code';
				$result = file_get_contents($url);
				$jsonInfo = json_decode($result, true);
				$open_id = $jsonInfo['openid'];
				
				$this->funmall_model->bindBroker($open_id, $broker_id);
				file_get_contents('http://www.funmall.com.cn/api/update_weixin_user/' . $open_id);
				
				$uri = "http://www.funmall.com.cn/api/view_art/" . $open_id . "/" . $broker_id;
				redirect($uri);
			}
		}
	}
	
	public function index() {
		
		$echoStr = $_GET["echostr"];
		if(isset($echoStr)) {
			if($this->checkSignature()){
				echo $echoStr;
				exit;
			}
		} else {
			$postStr = $GLOBALS["HTTP_RAW_POST_DATA"];
			if (!empty($postStr)){
				$postObj = simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);
				$RX_TYPE = trim($postObj->MsgType);
				$result = "";
				switch ($RX_TYPE) {
					case "text":
						$result = $this->receiveText($postObj);
						break;
					case "event":
						$result = $this->receiveEvent($postObj);
						break;
					case "image":
						//$result = $this->receiveImage($postObj);
						break;
					default:
						$result = "Unknow msg type: ".$RX_TYPE;
						break;
				}
				echo $result;
				exit;
			} else {
				echo "";
				exit;
			}
		}
	}
	
	private function receiveText($object) {
		$keyword = trim($object->Content);
		$result = $this->post('http://www.funmall.com.cn/api/search_house', $keyword);
		return $this->transmitText($object,  $result);
	}
	
	private function receiveEvent($object) {
		$content = "";
		switch ($object->Event) {
			case "subscribe":
				$content = "欢迎关注房猫微店公众账号。";
				if (!empty($object->EventKey)){
					$broker_id = str_replace("qrscene_", "", $object->EventKey);
					$broker_name = $this->funmall_model->getBrokerNameById($broker_id);
					if(!empty($broker_name)) {
						$content .= "您已成功绑定经纪人: " . $broker_name;
					}
					$this->funmall_model->bindBroker($object->FromUserName, $broker_id);
				}
				file_get_contents('http://www.funmall.com.cn/api/update_weixin_user/' . $object->FromUserName);
				break;
			case "unsubscribe":
				$content = "取消关注";
				file_get_contents('http://www.funmall.com.cn/api/unsubscribe_weixin_user/' . $object->FromUserName);
				break;
			case "SCAN":
				$content = "您扫描二维码成功绑定经纪人";
				$broker_id = $object->EventKey;
				$broker_name = $this->funmall_model->getBrokerNameById($broker_id);
				if(!empty($broker_name)) {
					$content = "您已成功绑定经纪人: " . $broker_name;
				}
				$this->funmall_model->bindBroker($object->FromUserName, $broker_id);
				file_get_contents('http://www.funmall.com.cn/api/update_weixin_user/' . $object->FromUserName);
 				break;
			case "CLICK":
				$content = "点击菜单拉取消息： " . $object->EventKey;
				break;
			case "VIEW":
				$content = "点击菜单跳转链接： " . $object->EventKey;
				break;
			case "LOCATION":
				$content = "上传位置：纬度 " . $object->Latitude . ";经度 " . $object->Longitude;
				break;
		}
		return $this->transmitText($object, $content);
	}
	
	private function transmitText($object, $content) {
		$textTpl = "
			<xml>
				<ToUserName><![CDATA[%s]]></ToUserName>
				<FromUserName><![CDATA[%s]]></FromUserName>
				<CreateTime>%s</CreateTime>
				<MsgType><![CDATA[text]]></MsgType>
				<Content><![CDATA[%s]]></Content>
				<FuncFlag>0</FuncFlag>
			</xml>
		";
		return sprintf($textTpl, $object->FromUserName, $object->ToUserName, time(), $content);
	}
	
	private function checkSignature() {
		$signature = $_GET["signature"];
		$timestamp = $_GET["timestamp"];
		$nonce = $_GET["nonce"];
		$token = TOKEN;
		$tmpArr = array($token, $timestamp, $nonce);
		sort($tmpArr);
		$tmpStr = implode( $tmpArr );
		$tmpStr = sha1( $tmpStr );
		if($tmpStr == $signature){
			return true;
		} else {
			return false;
		}
	}
	
	private function post($url, $post_data, $timeout = 300){
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_POSTFIELDS, 'input=' . $post_data);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch,CURLOPT_TIMEOUT, $timeout);
		curl_setopt($ch, CURLOPT_POST, true);
		
		$data = curl_exec($ch);
		curl_close($ch);
		return $data;
	}
}
