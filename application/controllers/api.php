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
    
    public function __construct()
    {
        parent::__construct();
    }
    
    public function get_pro_byid($id){
		$project = $this->sysconfig_model->chenck_pro($id);
		echo $project;
	}
    
}
