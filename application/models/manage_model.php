<?php
if (! defined('BASEPATH'))
    exit('No direct script access allowed');

/**
 * 网站后台模型
 *
 * @package		app
 * @subpackage	core
 * @category	model
 * @author		yaobin<645894453@qq.com>
 *        
 */
class Manage_model extends MY_Model
{
	protected $tables = array(
			'message',//0
			'news_type',//1
			'news',//2
			'admin',//3
			'project',//4
			'huxing',//5
			'news',//6
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
     * 用户登录检查
     * 
     * @return boolean
     */
	public function check_login ()
	{
		$login_id = $this->input->post('username');
		$passwd = $this->input->post('password');
		$this->db->select("permissions");
		$this->db->from($this->tables[3]);
		$this->db->where('username', $login_id);
		$this->db->where('passwd', sha1($passwd));
		$this->db->where('admin_group', '1');
		$rs = $this->db->get()->row();
		if ($rs && $rs->permissions) {
			$user_info['username'] = $this->input->post('username');
			$user_info['permissions'] = explode("|",$rs->permissions);
			$this->session->set_userdata($user_info);
			return true;
		} else {
			return false;
		}
	}
    
    /**
     * 修改密码
     * 
     */
    public function change_pwd ()
    {
        $login_id = $this->input->post('username');
        $newpassword = $this->input->post('newpassword');
        
		$rs=$this->db->where('username', $login_id)->update($this->tables[3], array('passwd'=>sha1($newpassword))); 
        if ($rs) {
            return 1;
        } else {
            return $rs;
        }
    }
    
    
	public function get_message(){
		return $this->db->select()->from($this->tables[0])->get()->row_array();
	}

	public function save_message(){
		$data=$this->input->post();
		$data['cdate']= date('Y-m-d H:i:s',time());
		$this->db->trans_start();
		$this->db->empty_table($this->tables[0]);
		$this->db->insert($this->tables[0],$data);
		$this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            return $this->db_error;
        } else {
            return 1;
        }
	}
    
	
	/**
     * 分页列出旅游行程
     */
	public function list_project(){
		// 每页显示的记录条数，默认20条
        $numPerPage = $this->input->post('numPerPage') ? $this->input->post('numPerPage') : 20;
        $pageNum = $this->input->post('pageNum') ? $this->input->post('pageNum') : 1;
        
        //获得总记录数
        $this->db->select('count(1) as num');
        $this->db->from($this->tables[4]);
        
        $rs_total = $this->db->get()->row();
        //总记录数
        $data['countPage'] = $rs_total->num;
        
        //list
        $this->db->select('*');
        $this->db->from($this->tables[4]);
            
        $this->db->limit($numPerPage, ($pageNum - 1) * $numPerPage );
        $this->db->order_by($this->input->post('orderField') ? $this->input->post('orderField') : 'cdate', $this->input->post('orderDirection') ? $this->input->post('orderDirection') : 'desc');
        $data['res_list'] = $this->db->get()->result();
        $data['pageNum'] = $pageNum;
        $data['numPerPage'] = $numPerPage;
        return $data;
	}
	
	public function save_project(){
		//主档图片处理
		if(!$_FILES["imgfile"]['name'] and !$this->input->post('old_img')){//未上传图片
			form_submit_json("300", "请添加图片");exit;
		}
		
		//旧图片
		$old_img = $this->input->post('old_img');
		$old_img_a = $this->input->post('old_img_a');
		
		
		//详情表信息a
		$huxing = $this->input->post('huxing');
		
		$config['upload_path'] = './././uploadfiles/huxing/';
		$config['allowed_types'] = 'gif|jpg|png|jpeg';
		$config['encrypt_name'] = true;
		$this->load->library('upload', $config);
		
		$this->db->trans_start();
		
		//主档图片上传
		if($this->upload->do_upload('imgfile')){
			$imgfile = $this->upload->data();
		}else{
			if(!$_FILES["imgfile"]['name'] and $this->input->post('old_img')){
				$imgfile = array();
				$imgfile['file_name'] = $this->input->post('old_img');
			}else{
				form_submit_json("300", $this->upload->display_errors('<b>','</b>'));
				exit;
			}
		}
		
		$data_head = array(
			'project'=>$this->input->post('project'),
			'remark'=>$this->input->post('remark'),
			'bb_valid'=>$this->input->post('bb_valid'),
			'qy_valid'=>$this->input->post('qy_valid'),
			'price'=>$this->input->post('price'),
			'pic'=>$imgfile['file_name'],
			'kaipan'=>$this->input->post('kaipan'),
			'kaifa'=>$this->input->post('kaifa'),
			'wuye_gongsi'=>$this->input->post('wuye_gongsi'),
			'wuye_leixing'=>$this->input->post('wuye_leixing'),
			'jianzhu_leixing'=>$this->input->post('jianzhu_leixing'),
			'jianzhu_mianji'=>$this->input->post('jianzhu_mianji'),
			'zhuangxiu'=>$this->input->post('zhuangxiu'),
			'hushu'=>$this->input->post('hushu'),
			'chewei'=>$this->input->post('chewei'),
			'rongji'=>$this->input->post('rongji'),
			'ludi'=>$this->input->post('ludi'),
			'wuyefei'=>$this->input->post('wuyefei'),
			'point'=>$this->input->post('point'),
			'address'=>$this->input->post('address'),
			'cdate'=>date('Y-m-d H:i:s',time())
		);
		
		if($this->input->post('id')){//修改主档
			$this->db->where('id',$this->input->post('id'));
			$this->db->update($this->tables[4], $data_head); 
			$id = $this->input->post('id');
			
			//删除旧数据
			$this->db->delete($this->tables[5], array('head_id' => $id)); 
			
		}else{//新增主档
			//保存主档信息，取得主档保存的id
			$this->db->insert($this->tables[4], $data_head);        
			$id = $this->db->insert_id(); 
		}
		
		//详情表图片以及数据保存a
		foreach($huxing as $i=>$v){
			$filename = 'userfile'.$i;
			if($this->upload->do_upload($filename)){
				$userfile = $this->upload->data();
				$data_detail = array(
					'huxing' => $v,
					'pic' => $userfile['file_name'],
					'head_id' => $id
				);
				$this->db->insert($this->tables[5], $data_detail);  
			}else{
				if(!$_FILES[$filename]['name'] and $old_img_a[$i]){//修改保存，但不修改图片
					$data_detail = array(
						'huxing' => $v,
						'pic' => $old_img_a[$i],
						'head_id' => $id
					);
					$this->db->insert($this->tables[5], $data_detail);  
				}else{
					form_submit_json("300", $this->upload->display_errors('<b>','</b>'));
					exit;
				}
			}	
		}
		
		$this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            return $this->db_error;
        } else {
            return 1;
        }
		
	}
	
	public function get_project($id){
		$data = $this->db->select('*')->from($this->tables[4])->where('id',$id)->get()->row_array();
		$data['list'] = $this->db->select('*')->from($this->tables[5])->where('head_id',$id)->get()->result();
		return $data;
	}
	
	public function delete_project($id){
		$this->db->trans_start();
		$this->db->delete($this->tables[4],array('id'=>$id));
		$this->db->delete($this->tables[5],array('head_id'=>$id));
		
		$this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            return $this->db_error;
        } else {
            return 1;
        }
		
	}
	
	public function list_news(){
        // 每页显示的记录条数，默认20条
        $numPerPage = $this->input->post('numPerPage') ? $this->input->post('numPerPage') : 20;
        $pageNum = $this->input->post('pageNum') ? $this->input->post('pageNum') : 1;
        
        //获得总记录数
        $this->db->select('count(1) as num');
        $this->db->from($this->tables[6]);
    	if($this->input->post('title'))
            $this->db->like('title',$this->input->post('title'));
        
        $rs_total = $this->db->get()->row();
        //总记录数
        $data['countPage'] = $rs_total->num;
        
		$data['title'] = null;
        //list
        $this->db->select();
        $this->db->from($this->tables[6]);
        if($this->input->post('title')){
        	$this->db->like('title',$this->input->post('title'));
        	$data['title'] = $this->input->post('title');
        }
            
        $this->db->limit($numPerPage, ($pageNum - 1) * $numPerPage );
        $this->db->order_by($this->input->post('orderField') ? $this->input->post('orderField') : 'cdate', $this->input->post('orderDirection') ? $this->input->post('orderDirection') : 'desc');
        $data['res_list'] = $this->db->get()->result();
        $data['pageNum'] = $pageNum;
        $data['numPerPage'] = $numPerPage;
        return $data;
	}
	
	public function save_news(){
		$this->db->trans_start();
		$data = $this->input->post();
		unset($data['ajax']);
		if($this->input->post('id')){//修改
			$this->db->where('id', $this->input->post('id'));
			$this->db->update($this->tables[6], $data); 
		}else{//新增
			$data['cdate'] = date('Y-m-d H:i:s',time());
			$this->db->insert($this->tables[6], $data); 
		}
		$this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            return $this->db_error;
        } else {
            return 1;
        }
	}
	
	public function delete_news($id){
		$rs = $this->db->delete($this->tables[6], array('id' => $id)); 
		if($rs){
			return 1;
		}else{
			return -1;
		}
	}
	
	public function get_news($id){
		$this->db->select('*')->from($this->tables[6])->where('id', $id);
		$data = $this->db->get()->row_array();
		return $data;
	}
	
	
	
	

}

/* End of file manage_model.php */
/* Location: ./application/models/manage_model.php */