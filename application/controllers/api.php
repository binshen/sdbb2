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
class Api extends MY_Controller {
    
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
		$uri = "http://www.funmall.com.cn/b_house/view_list/1/";
		if(!empty($open_id)) {
			$uri .= $open_id . '/';
			$funmallDB = $this->load->database("funmall", True);
			$funmallDB->from('wx_user');
			$funmallDB->where('open_id', $open_id);
			$wxUser = $funmallDB->get()->row_array();
			if(!empty($wxUser)) {
				$uri .= $wxUser['broker_id'] . '/';
			}
		}
		redirect($uri);
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
		return $this->transmitText($object, $object->FromUserName);
	}
	
	private function receiveEvent($object) {
		$content = "";
		switch ($object->Event) {
			case "subscribe":
				$content = "关注";
				if (!empty($object->EventKey)){
					$broker_id = str_replace("qrscene_", "", $object->EventKey);
					$this->funmall_model->bindBroker($object->FromUserName, $broker_id);
				}
				break;
			case "unsubscribe":
				$content = "取消关注";
				break;
			case "SCAN":
				$content = "扫描";
				$broker_id = $object->EventKey;
				$this->funmall_model->bindBroker($object->FromUserName, $broker_id);
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
}
