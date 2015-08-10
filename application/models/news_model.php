<?php
if (! defined('BASEPATH'))
    exit('No direct script access allowed');

class News_model extends MY_Model
{
	protected $tables = array(
			'news',
			'comment'

    );
	
    public function __construct ()
    {
        parent::__construct();
    }

    public function __destruct ()
    {
        parent::__destruct();
    }
    
    public function list_news($pageNum){
    	$per_page=6;//每页显示多少调数据
    	
    	$news_ids = array();
    	$data = $this->db->select()->from($this->tables[0])->limit($per_page, ($pageNum - 1) * $per_page )->order_by('cdate','desc')->get()->result_array();
    	if(!$data){
    		if($pageNum=='1'){
    			return '';
    		}else{
    			return -1;
    		}
    	}
    		
    	foreach($data as $k=>$v){
    		$news_ids[] = $v['id'];
    	}
    	
    	$rs = $this->db->select()->from($this->tables[1])->where_in('head_id',$news_ids)->order_by('cdate','acs')->get()->result_array();
    	
    	foreach($data as $k=>$v){
    		foreach($rs as $kk=>$vv){
    			if($v['id'] == $vv['head_id']){
    				$data[$k]['comment'][]=$vv;
    			}
    		}
    	}
    	
    	return $data;

    }
    
    public function get_total_page(){
    	$per_page=6;//每页显示多少调数据
    	$this->db->select('count(1) num')->from($this->tables[0]);
    	$rs_total = $this->db->get()->row();
        //总记录数
        $total_rows = $rs_total->num;
		return ceil($total_rows/$per_page); //总页数
    
    }
    
    public function save_comment(){
    	$data = $this->input->post(null,true);
    	$data['username']  = $this->session->userdata('username');
    	$data['rel_name']  = $this->session->userdata('rel_name');
    	$data['cdate']  = date('Y-m-d H:i:s',time());
    	$rs = $this->db->insert($this->tables[1],$data);
    	if($rs)
    		return 1;
    	else 
    		return -2;
    }
    
	
	
}

/* End of file sysconfig_model.php */
/* Location: ./application/models/sysconfig_model.php */