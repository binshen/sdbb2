<?php
if (! defined('BASEPATH'))
    exit('No direct script access allowed');

/**
 * 系统设置模型
 * 可用于抓取系统初始数据以及定义系统变量和获取一些首页需要的信息
 *
 * @package		app
 * @subpackage	core
 * @category	model
 * @author		yaobin<645894453@qq.com>
 *        
 */
class Sysconfig_model extends MY_Model
{
	protected $tables = array(
            'project',
			'admin',
			'm_p',
			'main_list',
			'message'
    );
	
    public function __construct ()
    {
        parent::__construct();
    }

    public function __destruct ()
    {
        parent::__destruct();
    }
    
    public function get_projects(){
    	$data = $this->db->select('a.*')->from("{$this->tables[0]} a")->get()->result_array();
    	return $data;
    }
    
    public function get_m_p(){
    	$data = $this->db->select('a.*,b.project')->from("{$this->tables[2]} a")->join("{$this->tables[0]} b","a.project_id=b.id")
    	->get()->result_array();
    	$new = array();
    	foreach($data as $k=>$v){
			$new[$v['manager_id']][] = array('project_id'=>$v['project_id'],'project'=>$v['project']);
    	}
    	return $new;
    }
    
    public function chenck_pro($id){
    	$rs = $this->db->select('project')->from($this->tables[0])->where('id',$id)->get()->row();
    	if($rs){
    		return $rs->project;
    	}else{
    		exit('Path is not exist!');
    	}
    }
    
    
	/**
     * 保存报备信息
     */
	public function save_bb(){
		$user = $this->message['user'];
		$pwd = md5($this->message['pwd']);
		$rs = $this->db->select('count(1) num,yqm,name,phone,sex')->from($this->tables[3])->where('phone',$this->input->post('k_phone'))->get()->row();
		$phone = $this->db->select('phone')->from($this->tables[1])->where('yqm',$this->input->post('k_yqm'))->get()->row();
		if($rs->num > 0){//数据库中已经存在
			if(!$rs->yqm && $phone){//公司资源并且邀请码正确
				$this->db->where('phone',$this->input->post('k_phone'));
				$res = $this->db->update($this->tables[3],array('yqm'=>$this->input->post('k_yqm')?$this->input->post('k_yqm'):''));
				
				$str = '您的客户'.$rs->name.''.$rs->sex.''.$rs->phone.'已自助预约成功，请及时跟进及报备带看';
				//$con2 = mb_convert_encoding($str,'gbk');
				//$url2="http://api.52ao.com/?user={$user}&pass={$pwd}&mobile={$phone->phone}&content={$con2}"; 
				//$content=urlencode(iconv("UTF-8","GB2312//IGNORE",$content.$smsarr["signature"]));
				$con2 = urlencode(iconv("UTF-8","GB2312//IGNORE",$str));//liufei 140929
				$url2="http://210.5.158.31/hy/?uid=801351&auth=47597e66de21404fd742c85bc6e4a05c&mobile={$phone->phone}&msg={$con2}&expid=0";//liufei 140929

				$fcontent2=@file_get_contents($url2);
				
				if($res)
					return 1;
				else 
					return -99;
			}else if(!$phone){
				return -2;
			}else{
				return -1;
			}
		}else{
			if($phone){
				$yqm = $this->input->post('k_yqm');
			}else{
				$yqm = '';
			}
			$data = array(
				'name' => $this->input->post('k_name'),
				'sex' => $this->input->post('k_sex'),
				'phone' =>$this->input->post('k_phone'),
				'yqm' => $yqm,
				'project_id' => $this->input->post('k_project'),
				'status' => '1',
				'target'=>'派单',
				'cdate' => date('Y-m-d H:i:s',time())
			);
			
			$res = $this->db->insert($this->tables[3],$data);
			
			$p = $this->input->post('k_phone');//客户手机
			$p2 = $phone?$phone->phone:'';//业务员手机
			
			if($this->input->post('k_project') == '10'){//报备了原鼎国际
				//$con = mb_convert_encoding('恭喜您已成功预约原鼎国际大厦电商优惠活动码上约惠活动，凭此短信前往原鼎国际售楼处办理团购手续即可享受2.5万抵5万优惠活动，详询原鼎国际售楼处热线：0512-50336666。','gbk');
				//$url="http://api.52ao.com/?user={$user}&pass={$pwd}&mobile={$p}&content={$con}"; 
				$str_new='恭喜您已成功预约原鼎国际大厦电商优惠活动码上约惠活动，凭此短信前往原鼎国际售楼处办理团购手续即可享受2.5万抵5万优惠活动，详询原鼎国际售楼处热线：0512-50336666。';
				$con = urlencode(iconv("UTF-8","GB2312//IGNORE",$str_new));//liufei 140929
				$url="http://210.5.158.31/hy/?uid=801351&auth=47597e66de21404fd742c85bc6e4a05c&mobile={$p}&msg={$con}&expid=0";//liufei 140929

				$fcontent=@file_get_contents($url);
			}
			else{
				$str_new3='恭喜您已成功预约本次活动，谢谢您的关注';
				$con3 = urlencode(iconv("UTF-8","GB2312//IGNORE",$str_new3));//liufei 140929
				$url3="http://210.5.158.31/hy/?uid=801351&auth=47597e66de21404fd742c85bc6e4a05c&mobile={$p}&msg={$con3}&expid=0";//liufei 140929
				$fcontent3=@file_get_contents($url3);
			}
			
			if($p2){
				//$con2 = mb_convert_encoding('您的客户'.$this->input->post('k_name').''.$this->input->post('k_sex').''.$this->input->post('k_phone').'已自助预约成功，请及时跟进及报备带看','gbk');
				//$url2="http://api.52ao.com/?user={$user}&pass={$pwd}&mobile={$p2}&content={$con2}";
				$str_new2='您的客户'.$this->input->post('k_name').''.$this->input->post('k_sex').''.$this->input->post('k_phone').'已自助预约成功，请及时跟进及报备带看';
				$con2 = urlencode(iconv("UTF-8","GB2312//IGNORE",$str_new2));//liufei 140929
				$url2="http://210.5.158.31/hy/?uid=801351&auth=47597e66de21404fd742c85bc6e4a05c&mobile={$p2}&msg={$con2}&expid=0";//liufei 140929
				$fcontent2=@file_get_contents($url2);
			}
			
			if($res){
				if(!$this->input->post('k_yqm') || !$p2){
					return -2;
				}else{
					return 1;
				}
			}else{
				return -99;
			}
		}
	}


	/**
     * 保存报备信息pc
     */
	public function save_bb_pc($pro,$name,$phone){
		$rs = $this->db->select('count(1) num')->from($this->tables[3])->where('phone',$phone)->where('project_id',$pro)->get()->row();
		if($rs->num > 0){//数据库中已经存在
			return -1;
		}else{
			$data = array(
				'name' => $name,
				'phone' =>$phone,
				'yqm' => '',
				'project_id' => $pro,
				'status' => '1',
				'cdate' => date('Y-m-d H:i:s',time())
			);
			$res = $this->db->insert($this->tables[3],$data);
			if($res)
				return 1;
			else 	
				return -99;
		}
	}
	
	public function get_count(){
		$this->db->select('count(1) num')->from("{$this->tables[3]} a");
		$this->db->join("{$this->tables[1]} b","a.yqm=b.yqm","left");
		if($this->input->post('yqm'))
			$this->db->where('a.yqm',$this->input->post('yqm'));
		if($this->input->post('status'))
			$this->db->where('a.status',$this->input->post('status'));
		if($this->input->post('s_date'))
			$this->db->where('a.cdate >=',$this->input->post('s_date'));
		if($this->input->post('e_date'))
			$this->db->where('a.cdate <=',$this->input->post('e_date'));
		if($this->input->post('phone'))
			$this->db->like('a.phone',$this->input->post('phone'));
		if($this->input->post('yw_phone'))
			$this->db->like('b.phone',$this->input->post('yw_phone'));
		if($this->input->post('project'))
			$this->db->where('a.project_id',$this->input->post('project'));
		$rs = $this->db->get()->row();
		return $rs->num;
	}
	
//	public function main_list($per_page,$offset){
//		$this->db->select('a.*,b.rel_name f_rel_name,c.project,b.username username');
//		$this->db->from("{$this->tables[3]} a");
//		$this->db->join("{$this->tables[1]} b","a.yqm=b.yqm","left");
//		$this->db->join("{$this->tables[0]} c","a.project_id=c.id");
//		//$this->db->join("{$this->tables[1]} d","a.next_yqm=d.yqm","left");
//		//$this->db->join("{$this->tables[1]} e","b.manager_id=e.id","left");
//		if($this->input->post('yqm'))
//			$this->db->where('a.yqm',$this->input->post('yqm'));
//		if($this->input->post('status'))
//			$this->db->where('a.status',$this->input->post('status'));
//		if($this->input->post('s_date'))
//			$this->db->where('a.cdate >=',$this->input->post('s_date'));
//		if($this->input->post('e_date'))
//			$this->db->where('a.cdate <=',$this->input->post('e_date'));
//		if($this->input->post('phone'))
//			$this->db->like('a.phone',$this->input->post('phone'));
//		if($this->input->post('yw_phone'))
//			$this->db->like('b.phone',$this->input->post('yw_phone'));
//		if($this->input->post('project'))
//			$this->db->where('a.project_id',$this->input->post('project'));
//			
//		$this->db->limit($per_page,$offset);
//		$this->db->order_by("cdate", "desc");
//		$rs = $this->db->get()->result_array();
//		return $rs;
//	}
	
	public function get_mangers(){
		return $this->db->select('*')->from($this->tables[1])->where('admin_group','2')->get()->result_array();
	}
	
	public function get_user($id){
		if(!$id)
			return '';
		return $this->db->select('*')->from($this->tables[1])->where('manager_id',$id)->get()->result_array();
	}
	
	public function exe_main(){
		$sql_a = 'update '.$this->tables[3].' set status="-2" where id in (
		select id from (select a.id id,a.cdate cdate,b.bb_valid bb_valid,b.qy_valid qy_valid from main_list a left join project b on a.project_id = b.id where a.status ="1" or a.status="2") as c where TIMESTAMPDIFF(DAY,c.cdate,now()) >= c.bb_valid)';
		
		$sql_b = 'update '.$this->tables[3].' set status="-2" where id in (
		select id from (select a.id id,a.cdate cdate,a.ddate,b.bb_valid bb_valid,b.qy_valid qy_valid from '.$this->tables[3].' a left join '.$this->tables[0].' b on a.project_id = b.id where a.status ="3" or a.status="4") as c where TIMESTAMPDIFF(DAY,c.ddate,now()) >= c.qy_valid)';
		
		$sql_c = 'update '.$this->tables[3].' set status="2" where id in (
		select id from (select a.id id,a.cdate cdate,b.bb_valid bb_valid,b.qy_valid qy_valid from main_list a left join project b on a.project_id = b.id where a.status ="1" or a.status="2") as c where c.bb_valid - TIMESTAMPDIFF(DAY,c.cdate,now()) = 1)';
		
		$sql_d = 'update '.$this->tables[3].' set status="4" where id in (
		select id from (select a.id id,a.cdate cdate,a.ddate ddate,b.bb_valid bb_valid,b.qy_valid qy_valid from main_list a left join project b on a.project_id = b.id where a.status ="3") as c where c.qy_valid - TIMESTAMPDIFF(DAY,c.ddate,now()) = 1)';
		
		
		$this->db->query($sql_a);
		$this->db->query($sql_b);
		$this->db->query($sql_c);
		$this->db->query($sql_d);
	}
	
	public function send_message(){
		$rs = $this->db->select('a.yqm ymq,a.next_yqm next_yqm,a.name name,b.phone phone,c.phone next_phone')
		->from("{$this->tables[3]} a")
		->join("{$this->tables[1]} b","a.yqm=b.yqm")
		->join("{$this->tables[1]} c","a.next_yqm=c.yqm","left")
		->where('substr(timediff(now(),a.cdate),1,2) >=','48')
		->where('status','1')->where('a.yqm is not null',null)->get()->result_array();
		return $rs;
	}
	
	public function stop_flag(){
		$rs = $this->db->select('flag')->from('stop')->get()->row();
		return $rs->flag;
		
	}
	
	public function get_projects_m(){
		$data = $this->db->select('project_id id')->from($this->tables[2])
		->where('manager_id',$this->session->userdata('userid'))->get()->result_array();
    	return $data;
	}
	
	public function revoke_status($id,$status){
		$status = $status - 1;
		$data = array('status'=>$status);
		$this->db->where('id',$id);
		$rs = $this->db->update($this->tables[3],$data);
		if($rs){
			return 1;
		}else{
			return -2;
		}
	}
	
	public function main_list(){
		
    	if($this->session->userdata('admin_group') === '4'){//外联经理
    		$md_m = array();
    		$rs = $this->db->select('id')->from($this->tables[1])->where('manager_id',$this->session->userdata('userid'))->get()->result_array();
        	foreach($rs as $v){
    			$md_m[]=$v['id'];
    		}
    		$this->db->select('yqm')->from($this->tables[1]);
    		
    		if($md_m){
    			$this->db->where_in('manager_id',$md_m);
    		}else {
    			$this->db->where('manager_id','a');
    		}
    			
    		$rs = $this->db->get()->result_array();
    		foreach($rs as $v){
    			$yqm[]=$v['yqm'];
    		}
    		
    	}
    	
    	if($this->session->userdata('admin_group') === '5'){//门店经理
    		$rs = $this->db->select('yqm')->from($this->tables[1])->where('manager_id',$this->session->userdata('userid'))->get()->result_array();
    		foreach($rs as $v){
    			$yqm[]=$v['yqm'];
    		}
    	}
		
		
        $per_page=10;//每页显示多少调数据
        $this->db->select("count(1) num");
    	$this->db->from("{$this->tables[3]} a");
    	$this->db->join("{$this->tables[0]} b","a.project_id =b.id","left");
    	$this->db->join("{$this->tables[1]} c","a.yqm =c.yqm","left");
    	$this->db->join("{$this->tables[1]} d","c.manager_id = d.id","left");
    	$this->db->join("{$this->tables[1]} e","d.manager_id = e.id","left");
    	if($this->input->post('status')){
    		$this->db->where('status',$this->input->post('status'));
    	}

    	if($this->input->post('project_id')){
    		$this->db->where('project_id',$this->input->post('project_id'));
    	}
    	
		if($this->input->post('main_search')){
			
    		$where="(`c`.`rel_name` like '%".$this->input->post('main_search')."%' or `name` like '%".$this->input->post('main_search')."%' or `a`.`phone` like '%".$this->input->post('main_search')."%' or `c`.`username` like '%".$this->input->post('main_search')."%')";
        	$this->db->where($where);
    	}
    	
    	if($this->session->userdata('admin_group') === '4'){//外联经理
    		$this->db->where_in('a.yqm',$yqm);
    	}
    	
    	if($this->session->userdata('admin_group') === '5'){//门店经理
    		$this->db->where_in('a.yqm',$yqm);
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
        $data['main_search'] = '';
        $data['admin_group'] = $this->session->userdata('admin_group');
        
        //list
        $this->db->select("a.*,project,c.rel_name ywy_name,c.username ywy_phone,d.rel_name md_name,d.username md_phone,d.company_name company_name,e.rel_name qd_name,e.username qd_phone");
    	$this->db->from("{$this->tables[3]} a");
    	$this->db->join("{$this->tables[0]} b","a.project_id =b.id","left");
    	$this->db->join("{$this->tables[1]} c","a.yqm =c.yqm","left");
    	$this->db->join("{$this->tables[1]} d","c.manager_id = d.id","left");
    	$this->db->join("{$this->tables[1]} e","d.manager_id = e.id","left");
    	
    	if($this->input->post('status')){
    		$this->db->where('status',$this->input->post('status'));
    		$data['status'] = $this->input->post('status');
    	}
        	
    	if($this->input->post('project_id')){
    		$this->db->where('project_id',$this->input->post('project_id'));
    		$data['project_id'] = $this->input->post('project_id');
    	}
    	
		if($this->input->post('main_search')){
    		$where="(`c`.`rel_name` like '%".$this->input->post('main_search')."%' or `name` like '%".$this->input->post('main_search')."%' or `a`.`phone` like '%".$this->input->post('main_search')."%' or `c`.`username` like '%".$this->input->post('main_search')."%')";
        	$this->db->where($where);
        	$data['main_search'] = $this->input->post('main_search');
    	}
    	
		if($this->session->userdata('admin_group') === '4'){//外联经理
    		$this->db->where_in('a.yqm',$yqm);
    	}
    	
    	if($this->session->userdata('admin_group') === '5'){//门店经理
    		$this->db->where_in('a.yqm',$yqm);
    	}

        $this->db->limit($per_page, ($pageNum - 1) * $per_page );
    	$this->db->order_by('a.cdate','desc');
        $data['res_list'] = $this->db->get()->result_array();
        return $data;
	}
	
	public function get_index_info(){
		$data['project'] = $this->db->select()->from($this->tables[0])->get()->result_array();
		$rs = $this->db->select()->from($this->tables[4])->get()->row();
		$data['message'] = $rs->content;
		return $data;
	}
	
	public function get_pic(){
		$rs = $this->db->select('pic')->from($this->tables[1])->where('username',$this->session->userdata('username'))->get()->row();
		return $rs->pic;
	}
	
	public function get_user_info(){
		$rs = $this->db->select()->from($this->tables[1])->where('username',$this->session->userdata('username'))->get()->row_array();
		return $rs;
	}

	public function check_openid($openid){
		 $member_info = $this->db->select()->from($this->tables[1])->where('openid',$openid)->get()->row_array();
		 if($member_info){
		 	$this->session->set_userdata($member_info);
		 	$this->session->set_userdata('userid',$member_info['id']);
		 	return 1;
		 }else{
		 	return -1;
		 }
		 
	}
	
}

/* End of file sysconfig_model.php */
/* Location: ./application/models/sysconfig_model.php */