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
		$funmallDB = $this->load->database("funmall", True);
		$funmallDB->from('wx_user_broker');
		$funmallDB->where('open_id', $open_id);
		$wxUser = $funmallDB->get()->row_array();
		if(empty($wxUser)) {
			$wxUser = array(
				'open_id' => $open_id,
				'broker_id' => $broker_id
			);
			$this->db->insert('wx_user_broker', $wxUser);
		} else {
			$wxUser['broker_id'] = $broker_id;
			$this->db->where('id', $wxUser['id']);
			$this->db->update('wx_user_broker', $wxUser);
		}
	}
}
