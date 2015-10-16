<?php
if (! defined('BASEPATH'))
    exit('No direct script access allowed');

/**
 * 普通用户模型
 *
 * @package		app
 * @subpackage	core
 * @category	model
 * @author		yaobin<645894453@qq.com>
 *        
 */
class User_model extends MY_Model
{
	protected $tables = array(
			'main_list',	
			'project',
			'admin',
			'm_p'

    );
	
    public function __construct ()
    {
        parent::__construct();
    }

    public function __destruct ()
    {
        parent::__destruct();
    }
    
	
	/**
     * 保存报备信息
     */
	public function save_bb(){
		if($this->input->post('k_id')){//修改
			$this->db->where('id',$this->input->post('k_id'));
			$res = $this->db->update($this->tables[0],array('remark'=>$this->input->post('remark',true)));
			if($res)
				return 1;
			else 
				return -99;
		}else{//新增
			if(!$this->input->post('k_name')){
				return -99;
			}
			$rs = $this->db->select('count(1) num,yqm')->from($this->tables[0])->where('phone',$this->input->post('k_phone'))
			->where('project_id',$this->input->post('k_project'))->get()->row();
			$data = array(
				'name' => $this->input->post('k_name'),
				'phone' =>$this->input->post('k_phone'),
				'yqm' => $this->session->userdata('yqm'),
				'project_id' => $this->input->post('k_project'),
				'idno' => $this->input->post('k_idno'),
				'remark' => $this->input->post('k_remark'),
				'target' => $this->input->post('k_target'),
				'sex' => $this->input->post('k_sex'),
				'status' => '1',
				'cdate' => date('Y-m-d H:i:s',time())
			);
			
			$this->db->trans_start();//--------开始事务
			$this->db->insert($this->tables[0],$data);
			
    		$this->db->where('yqm',$this->session->userdata('yqm'));
			$this->db->set('jifen', 'jifen+1', FALSE);
			$this->db->set('bb_count', 'bb_count+1', FALSE);
			$this->db->update($this->tables[2]);
			
            $this->db->trans_complete();//------结束事务
	        if ($this->db->trans_status() === FALSE) {
	            return -99;
	        } else {
	            return 1;
	        }
			
		}
	}
	
    public function list_wdbb(){
    	
        $per_page=10;//每页显示多少调数据
        $this->db->select('count(1) num');
    	$this->db->from($this->tables[0]);
    	$this->db->where('yqm',$this->session->userdata('yqm'));
    	$this->db->where_in('status',array(1,2,3,4,5,-2));
    	if($this->input->post('status')){
    		$this->db->where('status',$this->input->post('status'));
    	}

    	if($this->input->post('project_id')){
    		$this->db->where('project_id',$this->input->post('project_id'));
    	}

        $rs_total = $this->db->get()->row();
        //总记录数
        $total_rows = $rs_total->num;
		$total_page = ceil($total_rows/$per_page); //总页数
		$pageNum=$this->uri->segment(3)?$this->uri->segment(3):1;//当前页
		
		if($pageNum > $total_page & $total_rows > 0 || $pageNum <1){
			echo "<meta http-equiv='Content-Type' content='text/html; charset=utf-8'><script>alert('页码错误');history.back();</script>";
			exit();
		}
		$data['total_rows'] = $total_rows;
		$data['total_page'] = $total_page;
		$data['pageNum'] = $pageNum;
		
        $data['status']='';
        $data['project_id']='';
        
        //list
        $this->db->select("a.*,project project");
    	$this->db->from("{$this->tables[0]} a");
    	$this->db->join("{$this->tables[1]} b","a.project_id =b.id","left");
    	$this->db->where('yqm',$this->session->userdata('yqm'));
    	$this->db->where_in('status',array(1,2,3,4,5,-2));
    	if($this->input->post('status')){
    		$this->db->where('status',$this->input->post('status'));
    		$data['status'] = $this->input->post('status');
    	}
        	
    	if($this->input->post('project_id')){
    		$this->db->where('project_id',$this->input->post('project_id'));
    		$data['project_id'] = $this->input->post('project_id');
    	}

        $this->db->limit($per_page, ($pageNum - 1) * $per_page );
    	$this->db->order_by('cdate','desc');
        $data['res_list'] = $this->db->get()->result_array();
        
        return $data;
    }
	
	public function del_bb($id){
		$this->db->trans_start();//--------开始事务
		$this->db->where('yqm',$this->session->userdata('yqm'));
		$this->db->where('id',$id);
		$this->db->update($this->tables[0],array('status'=>-1,'deldate'=>date('Y-m-d H:i:s',time())));
		
		$this->db->where('yqm',$this->session->userdata('yqm'));
		$this->db->set('jifen', 'jifen+1', FALSE);
		$this->db->set('bb_count', 'bb_count+1', FALSE);
		$this->db->update($this->tables[2]);
			
		$this->db->trans_complete();//------结束事务
		if ($this->db->trans_status() === FALSE) {
			return -1;
		} else {
			return 1;
		}

	}
	
	public function re_bb($id){
		$this->db->trans_start();//--------开始事务
		$data = $this->db->select()->from($this->tables[0])->where('id',$id)->where('yqm',$this->session->userdata('yqm'))->get()->row_array();
		$data['cdate'] = date('Y-m-d H:i:s',time());
		$data['status']= 1;
		unset($data['id']);
		unset($data['ddate']);
		unset($data['deldate']);
		$this->db->insert($this->tables[0],$data);
		
		$this->db->where('yqm',$this->session->userdata('yqm'));
		$this->db->set('jifen', 'jifen+1', FALSE);
		$this->db->set('bb_count', 'bb_count+1', FALSE);
		$this->db->update($this->tables[2]);
/*		$this->db->where('id',$id);
		$this->db->update($this->tables[0],array('status'=>'-2'));*/
		$this->db->trans_complete();//------结束事务
		if ($this->db->trans_status() === FALSE) {
			return -1;
		} else {
			return 1;
		}
	}
	
/*	public function get_bz($id){
		$rs = $this->db->select('remark')->from($this->tables[0])->where('id',$id)->get()->row();
		return $rs->remark;
	}*/
	
	public function save_bz(){
		$this->db->where('id',$this->input->post('id'));
		$res = $this->db->update($this->tables[0],array('remark'=>$this->input->post('remark',true)));
		if($res)
			return 1;
		else 
			return -99;
		
	}
	
	public function get_bb_count(){
		$yqm = $this->session->userdata('yqm');
		if( $this->session->userdata('admin_group') == 4){//外联经理
			$where = "t.yqm in (select yqm from admin where manager_id in (select id from admin where manager_id = {$this->session->userdata('userid')}))";
		}else if($this->session->userdata('admin_group') == 3){//业务员
			$where = "t.yqm = '{$yqm}'";
		}else if($this->session->userdata('admin_group') == 5){//门店经理
			$where = "t.yqm in (select yqm from admin where manager_id  = {$this->session->userdata('userid')} or id = {$this->session->userdata('userid')})";
		}
		if($this->session->userdata('admin_group') == 1){
			$sql = "SELECT
			*
			FROM
			(
			SELECT
			count(t.id) AS zbb,
			count(a.id) AS wbb,
			count(b.id) AS mbb
			FROM
			main_list t
			LEFT JOIN main_list a ON t.id = a.id
			AND a.cdate >= date_add(NOW(), INTERVAL - 7 DAY)
			LEFT JOIN main_list b ON t.id = b.id
			AND b.cdate >= date_add(NOW(), INTERVAL - 30 DAY)
			WHERE
			t. STATUS != - 1
			) a,
			(
			SELECT
			count(t.id) AS zdk,
			count(a.id) AS wdk,
			count(b.id) AS mdk
			FROM
			main_list t
			LEFT JOIN main_list a ON t.id = a.id
			AND a.ddate >= date_add(NOW(), INTERVAL - 7 DAY)
			LEFT JOIN main_list b ON t.id = b.id
			AND b.ddate >= date_add(NOW(), INTERVAL - 30 DAY)
			where t. STATUS = 3
			) b,
			(
			SELECT
			count(t.id) AS zcj,
			count(a.id) AS wcj,
			count(b.id) AS mcj
			FROM
			main_list t
			LEFT JOIN main_list a ON t.id = a.id
			AND a.cjdate >= date_add(NOW(), INTERVAL - 7 DAY)
			LEFT JOIN main_list b ON t.id = b.id
			AND b.cjdate >= date_add(NOW(), INTERVAL - 30 DAY)
			WHERE t. STATUS = 5
			) c,
			(
			SELECT
			count(t.id) AS zdq,
			count(a.id) AS ddq,
			count(b.id) AS qdq
			FROM
			main_list t
			LEFT JOIN main_list a ON t.id = a.id
			AND a.status = 2
			LEFT JOIN main_list b ON t.id = b.id
			AND b.status = 4
			WHERE (t. STATUS = 2 OR t. STATUS = 4)
			) d";
		}else{
			$sql = "SELECT
			*
			FROM
			(
			SELECT
			count(t.id) AS zbb,
			count(a.id) AS wbb,
			count(b.id) AS mbb
			FROM
			main_list t
			LEFT JOIN main_list a ON t.id = a.id
			AND a.cdate >= date_add(NOW(), INTERVAL - 7 DAY)
			LEFT JOIN main_list b ON t.id = b.id
			AND b.cdate >= date_add(NOW(), INTERVAL - 30 DAY)
			WHERE
			{$where}
			AND t. STATUS != - 1
			) a,
			(
			SELECT
			count(t.id) AS zdk,
			count(a.id) AS wdk,
			count(b.id) AS mdk
			FROM
			main_list t
			LEFT JOIN main_list a ON t.id = a.id
			AND a.ddate >= date_add(NOW(), INTERVAL - 7 DAY)
			LEFT JOIN main_list b ON t.id = b.id
			AND b.ddate >= date_add(NOW(), INTERVAL - 30 DAY)
			WHERE
			{$where}
			AND t. STATUS = 3
			) b,
			(
			SELECT
			count(t.id) AS zcj,
			count(a.id) AS wcj,
			count(b.id) AS mcj
			FROM
			main_list t
			LEFT JOIN main_list a ON t.id = a.id
			AND a.cjdate >= date_add(NOW(), INTERVAL - 7 DAY)
			LEFT JOIN main_list b ON t.id = b.id
			AND b.cjdate >= date_add(NOW(), INTERVAL - 30 DAY)
			WHERE
			{$where}
			AND t. STATUS = 5
			) c,
			(
			SELECT
			count(t.id) AS zdq,
			count(a.id) AS ddq,
			count(b.id) AS qdq
			FROM
			main_list t
			LEFT JOIN main_list a ON t.id = a.id
			AND a.status = 2
			LEFT JOIN main_list b ON t.id = b.id
			AND b.status = 4
			WHERE
			t.yqm = {$where}
			AND (t. STATUS = 2 OR t. STATUS = 4)
			) d";
		}
		
		$query = $this->db->query($sql);
		$data = $query->row_array();
		return $data;
	}
	
	public function get_m_bb_count(){
		$rs = $this->db->select('project_id')->from('m_p')->where('manager_id',$this->session->userdata('userid'))->get()->result_array();
		foreach($rs as $k=>$v){
			$project_arr[]=$v['project_id'];
		}
		
		$rs_a = $this->db->select('count(1) num')->from($this->tables[0])->where('status','1')->where_in('project_id',$project_arr)->get()->row();
		$rs_b = $this->db->select('count(1) num')->from($this->tables[0])->where('status','3')->where_in('project_id',$project_arr)->get()->row();
		$rs_c = $this->db->select('count(1) num')->from($this->tables[0])->where('status','5')->where_in('project_id',$project_arr)->get()->row();
		$rs_d = $this->db->select('count(1) num')->from($this->tables[0])->where('status','2')->where_in('project_id',$project_arr)->get()->row();
		
		$data = array(
			'bb'=>$rs_a->num,
			'dk'=>$rs_b->num,
			'qy'=>$rs_c->num,
			'jjdq'=>$rs_d->num,
		);
		return $data;
	}
	
	public function save_re_bb(){
		$data = $this->db->select()->from($this->tables[0])->where('id',$this->input->post('id'))->where('yqm',$this->session->userdata('yqm'))->get()->row_array();
		$data['cdate'] = date('Y-m-d H:i:s',time());
		$data['status']= 1;
		unset($data['id']);
		unset($data['ddate']);
		unset($data['deldate']);
		$this->db->trans_start();//--------开始事务
		foreach($this->input->post('project_id') as $k=>$v){
			$data['project_id'] = $v;
			$this->db->insert($this->tables[0],$data);
		}
		$count = count($this->input->post('project_id'));
		$this->db->where('yqm',$this->session->userdata('yqm'));
		$this->db->set('jifen', 'jifen+'.$count, FALSE);
		$this->db->set('bb_count', 'bb_count+'.$count, FALSE);
		$this->db->update($this->tables[2]);
		
        $this->db->trans_complete();//------结束事务
        if ($this->db->trans_status() === FALSE) {
            return -1;
        } else {
            return 1;
        }
	}
	
	public function m_list_bb(){
		$rs = $this->db->select('project_id')->from('m_p')->where('manager_id',$this->session->userdata('userid'))->get()->result_array();
		foreach($rs as $k=>$v){
			$project_arr[]=$v['project_id'];
		}
        $per_page=10;//每页显示多少调数据
        $this->db->select('count(1) num');
    	$this->db->from($this->tables[0]);
    	$this->db->where_in('project_id',$project_arr);
    	$this->db->where_in('status',array(1,2,3,4,5));
    	if($this->input->post('status')){
    		$this->db->where('status',$this->input->post('status'));
    	}

    	if($this->input->post('project_id')){
    		$this->db->where('project_id',$this->input->post('project_id'));
    	}
    	
    	if($this->input->post('start_date')){
    		$this->db->where('cdate >=',$this->input->post('start_date').' 00:00:00');
    	}
    	if($this->input->post('end_date')){
    		$this->db->where('cdate <=',$this->input->post('end_date').' 23:59:59');
    	}
    	if($this->input->post('main_search')){
    		$where = "(name LIKE '%" . $this->input->post('main_search') . "%' OR phone LIKE '%" . $this->input->post('main_search') . "%')";
    		$this->db->where($where);
    	}

        $rs_total = $this->db->get()->row();
        //总记录数
        $total_rows = $rs_total->num;
		$total_page = ceil($total_rows/$per_page); //总页数
		$pageNum=$this->uri->segment(3)?$this->uri->segment(3):1;//当前页
		
		if($pageNum > $total_page & $total_rows > 0 || $pageNum <1){
			echo "<meta http-equiv='Content-Type' content='text/html; charset=utf-8'><script>alert('页码错误');history.back();</script>";
			exit();
		}
		$data['total_rows'] = $total_rows;
		$data['total_page'] = $total_page;
		$data['pageNum'] = $pageNum;
		
        $data['status']='';
        $data['project_id']='';
        
        //list
        $this->db->select("a.*,b.project,c.rel_name rel_name,c.company_name,d.company_name cname");
    	$this->db->from("{$this->tables[0]} a");
    	$this->db->join("{$this->tables[1]} b","a.project_id = b.id","left");
    	$this->db->join("{$this->tables[2]} c","a.yqm = c.yqm","left");
    	$this->db->join("{$this->tables[2]} d","c.manager_id = d.id","left");
    	$this->db->where_in('project_id',$project_arr);
    	$this->db->where_in('status',array(1,2,3,4,5));
    	if($this->input->post('status')){
    		$this->db->where('status',$this->input->post('status'));
    		$data['status'] = $this->input->post('status');
    	}
        	
    	if($this->input->post('project_id')){
    		$this->db->where('project_id',$this->input->post('project_id'));
    		$data['project_id'] = $this->input->post('project_id');
    	}

    	if($this->input->post('start_date')){
    		$this->db->where('a.cdate >=',$this->input->post('start_date').' 00:00:00');
    	}
    	if($this->input->post('end_date')){
    		$this->db->where('a.cdate <=',$this->input->post('end_date').' 23:59:59');
    	}
    	if($this->input->post('main_search')){
    		$where = "(name LIKE '%" . $this->input->post('main_search') . "%' OR a.phone LIKE '%" . $this->input->post('main_search') . "%')";
    		$this->db->where($where);
    	}
    	
    	$data['start_date'] = $this->input->post('start_date');
    	$data['end_date'] = $this->input->post('end_date');
    	$data['main_search'] = $this->input->post('main_search');
    	
        $this->db->limit($per_page, ($pageNum - 1) * $per_page );
    	$this->db->order_by('a.cdate','desc');
        $data['res_list'] = $this->db->get()->result_array();
        return $data;
	}
	
	public function confirm_dk($id){
		$this->db->trans_start();//--------开始事务
		
		$rs = $this->db->select('yqm')->from($this->tables[0])->where('id',$id)->get()->row();
		
		$this->db->where('id',$id);
		$this->db->where_in('status',array('1','2'));
		$this->db->update($this->tables[0],array('status'=>'3','ddate'=>date('Y-m-d H:i:s',time())));
		
		if($rs){
	    	$this->db->where('yqm',$rs->yqm);
			$this->db->set('jifen', 'jifen+50', FALSE);
			$this->db->set('dk_count', 'dk_count+1', FALSE);
			$this->db->update($this->tables[2]);
		}
		
        $this->db->trans_complete();//------结束事务
        if ($this->db->trans_status() === FALSE) {
            return -1;
        } else {
            return 1;
        }
	}
	
	public function confirm_qy($id){
		$this->db->trans_start();//--------开始事务
		$rs = $this->db->select('yqm')->from($this->tables[0])->where('id',$id)->get()->row();
		
		$this->db->where('id',$id);
		$this->db->where_in('status',array('3','4'));
		$this->db->update($this->tables[0],array('status'=>'5','cjdate'=>date('Y-m-d H:i:s',time())));
		
		if($rs){
	    	$this->db->where('yqm',$rs->yqm);
			$this->db->set('jifen', 'jifen+500', FALSE);
			$this->db->set('qy_count', 'qy_count+1', FALSE);
			$this->db->update($this->tables[2]);
		}
		
        $this->db->trans_complete();//------结束事务
        if ($this->db->trans_status() === FALSE) {
            return -1;
        } else {
            return 1;
        }
	}
	
	public function list_company_wl(){
		return $this->db->select()->from($this->tables[2])->where('admin_group','5')->where('manager_id',$this->session->userdata('userid'))->get()->result_array();
	}
	
    public function save_company(){
    	$data = $this->input->post();
    	if($data['address'] == '公司地址'){
    		$data['address'] = '';
    	}
        if($data['tel'] == '联系固话'){
    		$data['tel'] = '';
    	}
        if($data['zhanghu'] == '公司账户'){
    		$data['zhanghu'] = '';
    	}
        if($data['guimo'] == '公司规模'){
    		$data['guimo'] = '';
    	}

    	$this->db->trans_start();
		if($this->input->post('id')){
			$this->db->where('id',$this->input->post('id'));
			$this->db->update($this->tables[2],$data);
		}else{//新增
	    	$rsb = $this->db->select('count(username) username')->from($this->tables[2])->where('username',$this->input->post('username'))->get()->row();
			if($rsb->username)
				return -1;
				
	    	$rs = $this->db->select('max(yqm) yqm')->from($this->tables[2])->where('admin_group','5')->get()->row();
	    	if($rs->yqm){
	    		$data['yqm'] = $rs->yqm + 1;
	    	}else{
	    		$data['yqm'] = 100;
	    	}
			$data['passwd'] = sha1('888888');
			$data['admin_group'] = '5';
			$data['phone'] = $data['username'];
			$data['cdate'] = date('Y-m-d H:i:s',time());
			$data['manager_id'] = $this->session->userdata('userid');
			
			$this->db->insert($this->tables[2],$data);
		}

        $this->db->trans_complete();
		if ($this->db->trans_status() === FALSE) {
            return -99;
        } else {
            return 1;
        }
    }
    
    public function get_company($id){
    	return $this->db->select()->from($this->tables[2])->where('id',$id)->get()->row_array();
    }
	
    public function list_ywy(){
    	$per_page=10;//每页显示多少调数据
        $this->db->select('count(1) num');
    	$this->db->from($this->tables[2]);
    	$this->db->where('manager_id',$this->session->userdata('userid'));
        $rs_total = $this->db->get()->row();
        //总记录数
        $total_rows = $rs_total->num;
		$total_page = ceil($total_rows/$per_page); //总页数
		$pageNum=$this->uri->segment(3)?$this->uri->segment(3):1;//当前页
		
		if($pageNum > $total_page & $total_rows > 0 || $pageNum <1){
			echo "<meta http-equiv='Content-Type' content='text/html; charset=utf-8'><script>alert('页码错误');history.back();</script>";
			exit();
		}
		$data['total_rows'] = $total_rows;
		$data['total_page'] = $total_page;
		$data['pageNum'] = $pageNum;
		
        //list
        $this->db->select();
    	$this->db->from($this->tables[2]);
    	$this->db->where('manager_id',$this->session->userdata('userid'));
        $this->db->limit($per_page, ($pageNum - 1) * $per_page );
    	$this->db->order_by('cdate','desc');
        $data['res_list'] = $this->db->get()->result_array();
        
        return $data;
    }
    
    public function save_ywy(){
    	$this->db->trans_start();
		if($this->input->post('id')){
			$this->db->where('id',$this->input->post('id'));
			$this->db->update($this->tables[2],array('username'=>$this->input->post('username'),'phone'=>$this->input->post('username'),'rel_name'=>$this->input->post('rel_name')));
		}else{//新增
	    	$rsb = $this->db->select('count(username) username')->from($this->tables[2])->where('username',$this->input->post('username'))->get()->row();
			if($rsb->username)
				return -1;
			
	    	$data = array(
				'username'=>$this->input->post('username'),
				'passwd'=>sha1('888888'),
				'rel_name'=>$this->input->post('rel_name'),
				'admin_group'=>'3',
				'phone'=>$this->input->post('username'),
				'manager_id'=>$this->session->userdata('userid'),
				'cdate'=>date('Y-m-d H:i:s',time())
			);
			
	    	$rs = $this->db->select('max(yqm) yqm')->from($this->tables[2])->where('manager_id',$this->session->userdata('userid'))->get()->row();
	    	if($rs->yqm){
	    		$data['yqm'] = $rs->yqm + 1;
	    	}else{
	    		$data['yqm'] = $this->session->userdata('yqm').'001';
	    	}
	    	
			
			$this->db->insert($this->tables[2],$data);
		}

        $this->db->trans_complete();
		if ($this->db->trans_status() === FALSE) {
            return -99;
        } else {
            return 1;
        }
    }
    
    public function get_ywy($id){
    	return $this->db->select()->from($this->tables[2])->where('id',$id)->get()->row_array();
    }
    
    public function md_huoyue($type){
    	$userid = $this->session->userdata('userid');
    	
        $per_page=6;//每页显示多少调数据
        $this->db->select('count(1) num');
    	$this->db->from($this->tables[2]);
    	$this->db->where('manager_id',$userid);
        $rs_total = $this->db->get()->row();
        //总记录数
        $total_rows = $rs_total->num;
		$total_page = ceil($total_rows/$per_page); //总页数
		$pageNum=$this->uri->segment(4)?$this->uri->segment(4):1;//当前页
		
		if($pageNum > $total_page & $total_rows > 0 || $pageNum <1){
			echo "<meta http-equiv='Content-Type' content='text/html; charset=utf-8'><script>alert('页码错误');history.back();</script>";
			exit();
		}
		$data['total_rows'] = $total_rows;
		$data['total_page'] = $total_page;
		$data['pageNum'] = $pageNum;
		$data['type'] = $type;
    	
    	$this->db->select('*');
    	$this->db->from($this->tables[2]);
    	$this->db->where('manager_id',$userid);
    	$this->db->limit($per_page, ($pageNum - 1) * $per_page );
    	if($type == '1'){//报备排名
    		$this->db->order_by('bb_count','desc');
    	}
    	if($type == '2'){//带看排名
    		$this->db->order_by('dk_count','desc');
    	}
    	if($type == '3'){//成交排名
    		$this->db->order_by('qy_count','desc');
    	}
    	
    	$data['res_list'] = $this->db->get()->result_array();
    	return $data;
    }
    
    public function wl_huoyue($type){
    	$userid = $this->session->userdata('userid');
    	
    	$rs = $this->db->select('id')->from($this->tables[2])->where('manager_id',$userid)->get()->result_array();
    	
    	foreach($rs as $v){
    		$md_uid[]=$v['id'];
    	}
    	
        $per_page=6;//每页显示多少调数据
        $data['per_page'] = $per_page;
        $this->db->select('count(1) num');
    	$this->db->from($this->tables[2]);
    	if(!empty($md_uid)) {
    		$this->db->where_in('manager_id',$md_uid);
    	}
    	$this->db->group_by('manager_id');
        $rs_total = $this->db->get()->row();
        //总记录数
        $total_rows = $rs_total->num;
		$total_page = ceil($total_rows/$per_page); //总页数
		$pageNum=$this->uri->segment(4)?$this->uri->segment(4):1;//当前页
		
		if($pageNum > $total_page & $total_rows > 0 || $pageNum <1){
			echo "<meta http-equiv='Content-Type' content='text/html; charset=utf-8'><script>alert('页码错误');history.back();</script>";
			exit();
		}
		$data['total_rows'] = $total_rows;
		$data['total_page'] = $total_page;
		$data['pageNum'] = $pageNum;
		$data['type'] = $type;
    	
    	$this->db->select('b.*,sum(a.bb_count) s_bb_count,sum(a.dk_count) s_dk_count,sum(a.qy_count) s_qy_count');
    	$this->db->from("{$this->tables[2]} a");
    	$this->db->join("{$this->tables[2]} b","a.manager_id=b.id","left");
    	if(!empty($md_uid)) {
    		$this->db->where_in('a.manager_id',$md_uid);
    	}
    	$this->db->group_by('a.manager_id');
    	$this->db->limit($per_page, ($pageNum - 1) * $per_page );
    	if($type == '1'){//报备排名
    		$this->db->order_by('sum(a.bb_count)','desc');
    	}
    	if($type == '2'){//带看排名
    		$this->db->order_by('sum(a.dk_count)*50','desc');
    	}
    	if($type == '5'){//成交排名
    		$this->db->order_by('sum(a.qy_count)*500','desc');
    	}
    	
    	$data['res_list'] = $this->db->get()->result_array();
    	return $data;
    }
    
    //获取客服经理报备量
    public function get_kf_data(){
    	$rs = $this->db->select('project_id')->from($this->tables[3])->where('manager_id',$this->session->userdata('userid'))->get()->result_array();
    	
    	foreach($rs as $v){
    		$pro[]=$v['project_id'];
    	}
    	
    	$rs = $this->db->select('count(1) num')->from($this->tables[0])
    			->where_in('project_id',$pro)->where_in('status',array('1','2','3','4','5','-2'))->get()->row();
    	$data['bb_count'] = $rs->num;
    	
    	$rs = $this->db->select('count(1) num')->from($this->tables[0])
    			->where_in('project_id',$pro)->where('status','3')->get()->row();
    	$data['dk_count'] = $rs->num;
    	$rs = $this->db->select('count(1) num')->from($this->tables[0])
    			->where_in('project_id',$pro)->where('status','5')->get()->row();
    	$data['qy_count'] = $rs->num;
    	return $data;
    }
    
    //获取门店经理报备量
    public function get_md_data(){
    	$data = $this->db->select('sum(bb_count) bb_count,sum(dk_count) dk_count,sum(qy_count) qy_count')->from($this->tables[2])
    			->where('manager_id',$this->session->userdata('userid'))->group_by('manager_id')->get()->row_array();
    	return $data;
    }
    
    //获取外联经理报备量
    public function get_wl_data(){
    	$rs = $this->db->select('id')->from($this->tables[2])->where('manager_id',$this->session->userdata('userid'))->get()->result_array();
    	$md_m = array();
    	if($rs){
            foreach($rs as $v){
	    		$md_m[]=$v['id'];
	    	} 	
    	}

    	
    	$this->db->select('sum(bb_count) bb_count,sum(dk_count) dk_count,sum(qy_count) qy_count')->from($this->tables[2]);
    	if($md_m)
    		$this->db->where_in('manager_id',$md_m);
    	else 
    		$this->db->where('manager_id','a');
    	$data = $this->db->get()->row_array();
    	return $data;
    }
    
    public function admin_bb_list(){
    	$per_page=10;//每页显示多少调数据
    	$this->db->select('count(1) num');
    	$this->db->from("{$this->tables[0]} a");
    	if($this->session->userdata('admin_group') == 1){
    		$where = '(1=1)';
    	}else{
    		$where="(a.yqm in (select yqm from admin where manager_id = {$this->session->userdata('userid')} or id = {$this->session->userdata('userid')}))";
    	}
    	$this->db->where($where);
    	
    	
    	$this->db->where_in('status',array(1,2,3,4,5,-2));
    	if($this->input->post('status')){
    		$this->db->where('status',$this->input->post('status'));
    	}
    	
    	if($this->input->post('project_id')){
    		$this->db->where('project_id',$this->input->post('project_id'));
    	}
    	
    	$rs_total = $this->db->get()->row();
    	//总记录数
    	$total_rows = $rs_total->num;
    	$total_page = ceil($total_rows/$per_page); //总页数
    	$pageNum=$this->uri->segment(3)?$this->uri->segment(3):1;//当前页
    	
    	if($pageNum > $total_page & $total_rows > 0 || $pageNum <1){
    		echo "<meta http-equiv='Content-Type' content='text/html; charset=utf-8'><script>alert('页码错误');history.back();</script>";
    		exit();
    	}
    	$data['total_rows'] = $total_rows;
    	$data['total_page'] = $total_page;
    	$data['pageNum'] = $pageNum;
    	
    	$data['status']='';
    	$data['project_id']='';
    	
    	//list
    	$this->db->select("a.*,project project,c.rel_name");
    	$this->db->from("{$this->tables[0]} a");
    	$this->db->join("{$this->tables[1]} b","a.project_id =b.id","left");
    	$this->db->join("{$this->tables[2]} c","a.yqm =c.yqm","left");
    	$this->db->where($where);
    	$this->db->where_in('status',array(1,2,3,4,5,-2));
    	if($this->input->post('status')){
    		$this->db->where('status',$this->input->post('status'));
    		$data['status'] = $this->input->post('status');
    	}
    	 
    	if($this->input->post('project_id')){
    		$this->db->where('project_id',$this->input->post('project_id'));
    		$data['project_id'] = $this->input->post('project_id');
    	}
    	
    	$this->db->limit($per_page, ($pageNum - 1) * $per_page );
    	$this->db->order_by('cdate','desc');
    	$data['res_list'] = $this->db->get()->result_array();
    	
    	return $data;
    }
    
    //管理员功能相关////////////////////////////////////////////////////////////////////////////////////////////////////////
    //列出经理
    public function list_managers(){
    	$data = $this->db->select('a.id,a.username,a.rel_name,project_id,project,is_exe')->from("{$this->tables[2]} a")->join("{$this->tables[3]} b","a.id=b.manager_id","left")
    	->join("{$this->tables[1]} c","b.project_id=c.id")
    	->where('admin_group','2')->get()->result_array();
    	$data_n = array();
    	foreach($data as $k=>$v){
    		if(isset($data_n[$v['id']])){
    			$data_n[$v['id']]['project'][] = array('project_id'=>$v['project_id'],'project_name'=>$v['project']);
    		}else{
    			$data_n[$v['id']]['id'] = $v['id'];
    			$data_n[$v['id']]['username'] = $v['username'];
    			$data_n[$v['id']]['rel_name'] = $v['rel_name'];
    			$data_n[$v['id']]['is_exe'] = $v['is_exe'];
    			$data_n[$v['id']]['project'][] = array('project_id'=>$v['project_id'],'project_name'=>$v['project']);
    		}
    
    	}
    	return $data_n;
    }
    
    
    
    //删除经理
    public function del_m(){
    	$this->db->trans_start();
    	$this->db->where('id',$this->input->post('m_id'));
    	$this->db->delete($this->tables[2]);
    	$this->db->where('manager_id',$this->input->post('m_id'));
    	$this->db->delete($this->tables[3]);
    	$this->db->trans_complete();
    	if ($this->db->trans_status() === FALSE) {
    		return -1;
    	} else {
    		return 1;
    	}
    }
    
    //保存经理
    public function save_kfjl(){
    	$this->db->trans_start();
    	$project_id = $this->input->post('project_id');
    	$is_exe = $this->input->post('is_exe')?$this->input->post('is_exe'):1;
    	if($this->input->post('id')){
    		$this->db->where('manager_id',$this->input->post('id'));
    		$this->db->delete($this->tables[3]);
    		foreach($project_id as $v){
    			$this->db->insert($this->tables[3],array('manager_id'=>$this->input->post('id'),'project_id'=>$v));
    		}
    		$this->db->where('id',$this->input->post('id'));
    		$this->db->update($this->tables[2],array('rel_name'=>$this->input->post('rel_name'),'is_exe'=>$this->input->post('is_exe')));
    	}else{//新增
    		$rsb = $this->db->select('count(username) username')->from($this->tables[2])->where('username',$this->input->post('username'))->get()->row();
    		if($rsb->username)
    			return -1;
    		$data = array(
    				'username'=>$this->input->post('username'),
    				'passwd'=>sha1('888888'),
    				'rel_name'=>$this->input->post('rel_name'),
    				'admin_group'=>'2',
    				'is_exe'=>$is_exe,
    				'phone'=>$this->input->post('username'),
    				'manager_id'=>'0',
    				'cdate'=>date('Y-m-d H:i:s',time())
    		);
    		$rs = $this->db->insert($this->tables[2],$data);
    		$id = $this->db->insert_id();
    		foreach($project_id as $v){
    			$this->db->insert($this->tables[3],array('manager_id'=>$id,'project_id'=>$v));
    		}
    	}
    
    	$this->db->trans_complete();
    	if ($this->db->trans_status() === FALSE) {
    		return -99;
    	} else {
    		return 1;
    	}
    }
    
    
    //列出渠道经理
    public function list_qdjl(){
    	$data = $this->db->select('id,rel_name,username')->from($this->tables[2])->where('admin_group','4')->get()->result_array();
    	return $data;
    }
    
    //保存渠道经理
    public function save_qdjl(){
    	$this->db->trans_start();
    	if($this->input->post('id')){
    		$this->db->where('id',$this->input->post('id'));
    		$this->db->update($this->tables[2],array('username'=>$this->input->post('username'),'phone'=>$this->input->post('username'),'rel_name'=>$this->input->post('rel_name')));
    	}else{//新增
    		$rsb = $this->db->select('count(username) username')->from($this->tables[2])->where('username',$this->input->post('username'))->get()->row();
    		if($rsb->username)
    			return -1;
    		$data = array(
    				'username'=>$this->input->post('username'),
    				'passwd'=>sha1('888888'),
    				'rel_name'=>$this->input->post('rel_name'),
    				'admin_group'=>'4',
    				'phone'=>$this->input->post('username'),
    				'manager_id'=>'0',
    				'cdate'=>date('Y-m-d H:i:s',time())
    		);
    		$rs = $this->db->insert($this->tables[2],$data);
    	}
    
    	$this->db->trans_complete();
    	if ($this->db->trans_status() === FALSE) {
    		return -99;
    	} else {
    		return 1;
    	}
    }
    
    public function reset_status($id){
    	$this->db->trans_start();//--------开始事务
    	
    	$rs = $this->db->select('yqm,status')->from($this->tables[0])->where('id',$id)->get()->row();
    	
    	if($rs->status == '5'){
    		$status = 3;
    		$data = array(
    				'status'=>$status,
    				'cjdate'=>null
    		);
    	}else{
    		$status = 1;
    		$data = array(
    				'status'=>$status,
    				'ddate'=>null
    		);
    	}
    	
    	$this->db->where('id',$id);
    	$this->db->update($this->tables[0],$data);
    	
    	if($status == 3){
    		$this->db->where('yqm',$rs->yqm);
    		$this->db->set('jifen', 'jifen-500', FALSE);
    		$this->db->set('qy_count', 'qy_count-1', FALSE);
    		$this->db->update($this->tables[2]);
    	}else{
    		$this->db->where('yqm',$rs->yqm);
    		$this->db->set('jifen', 'jifen-50', FALSE);
    		$this->db->set('dk_count', 'dk_count-1', FALSE);
    		$this->db->update($this->tables[2]);
    	}
    	
    	$this->db->trans_complete();//------结束事务
    	if ($this->db->trans_status() === FALSE) {
    		return -1;
    	} else {
    		return 1;
    	}
    }
	

	
}

/* End of file sysconfig_model.php */
/* Location: ./application/models/sysconfig_model.php */