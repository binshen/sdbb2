<?php
if (! defined('BASEPATH'))
    exit('No direct script access allowed');

class Funmall_model extends MY_Model {
	
	public function __construct () {
		parent::__construct();
	}
	
	public function __destruct () {
		parent::__destruct();
	}
	
	public function bindBroker($open_id, $broker_id) {
// 		$funmallDB = $this->load->database("funmall", True);
// 		$funmallDB->from('wx_user');
// 		$funmallDB->where('open_id', $open_id);
// 		$wxUser = $funmallDB->get()->row_array();
// 		if(empty($wxUser)) {
// 			$wxUser = array(
// 				'open_id' => $open_id,
// 				'broker_id' => $broker_id,
// 				'created' => date('Y-m-d H:i:s')
// 			);
// 			$this->db->insert('wx_user', $wxUser);
// 		} else {
// 			$wxUser['broker_id'] = $broker_id;
// 			$this->db->where('id', $wxUser['id']);
// 			$this->db->update('wx_user', $wxUser);
// 		}
		$conn = mysql_connect('121.40.97.183', 'root', 'soukecsk');
		$sql = "SELECT count(1) FROM `wx_user` where open_id = '".$open_id."' AND broker_id = ".$broker_id;
		$result = mysql_db_query('funmall', $sql, $conn); 
		$row = mysql_fetch_row($result);
		$date = date('Y-m-d H:i:s');
		if($row[0] > 0) {
			$sql = "UPDATE `wx_user` SET updated = '".$date."' WHERE open_id = '".$open_id."' AND broker_id = ".$broker_id;
		} else {
			$sql = "INSERT INTO `wx_user` (open_id,broker_id,created, updated) VALUES ('".$open_id."','".$broker_id."',1,'".$date."','".$date."')";
		}
		mysql_query($sql, $conn);
		mysql_close($conn);
	}
}
