<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>sdbb</title>

<link href="<?php echo base_url();?>dwz/themes/default/style.css" rel="stylesheet" type="text/css" media="screen"/>
<link href="<?php echo base_url();?>dwz/themes/css/core.css" rel="stylesheet" type="text/css" media="screen"/>
<link href="<?php echo base_url();?>dwz/themes/css/print.css" rel="stylesheet" type="text/css" media="print"/>
<link href="<?php echo base_url();?>dwz/uploadify/css/uploadify.css" rel="stylesheet" type="text/css" media="screen"/>
<!--[if IE]>
<link href="themes/css/ieHack.css" rel="stylesheet" type="text/css" media="screen"/>
<![endif]-->

<!--[if lte IE 9]>
<script src="js/speedup.js" type="text/javascript"></script>
<![endif]-->


<script src="<?php echo base_url();?>dwz/js/speedup.js" type="text/javascript"></script>
<script src="<?php echo base_url();?>dwz/js/jquery-1.7.2.min.js" type="text/javascript"></script>
<script src="<?php echo base_url();?>dwz/js/jquery.cookie.js" type="text/javascript"></script>
<script src="<?php echo base_url();?>dwz/js/jquery.validate.js" type="text/javascript"></script>
<script src="<?php echo base_url();?>dwz/js/jquery.bgiframe.js" type="text/javascript"></script>

<script src="<?php echo base_url();?>dwz/xheditor/xheditor-1.2.1.min.js" type="text/javascript"></script>
<script src="<?php echo base_url();?>dwz/bin/dwz.min.js" type="text/javascript"></script>
<script src="<?php echo base_url();?>dwz/js/dwz.regional.zh.js" type="text/javascript"></script>


<script type="text/javascript">
$(function(){
	DWZ.init("<?php echo base_url();?>dwz/dwz.frag.xml", {
		loginUrl:"<?php echo site_url('manage_login/login');?>", loginTitle:"登录",	// 弹出登录对话框
//		loginUrl:"login.html",	// 跳到登录页面
		statusCode:{ok:200, error:300, timeout:301}, //【可选】
		pageInfo:{pageNum:"pageNum", numPerPage:"numPerPage", orderField:"orderField", orderDirection:"orderDirection"}, //【可选】
		debug:false,	// 调试模式 【true|false】
		callback:function(){
			initEnv();
			$("#themeList").theme({themeBase:"<?php echo base_url();?>dwz/themes"}); // themeBase 相对于index页面的主题base路径
		}
	});
});

</script>
</head>

<body scroll="no">
	<div id="layout">
		<div id="header">
			<div class="headerNav">
				<!--<a class="logo" href="#">标志</a>-->
				<!--<img height="55"src="<?php echo base_url().'images/logo_b.png';?>" />
				--><ul class="nav">
				<li><a href="<?php echo base_url();?>" target="_black">前台首页</a></li>
				<li><a href="<?php echo site_url('manage_login/change_pwd');?>" target="dialog" rel="chagepwd">密码修改</a></li>
				<li>欢迎您：<?php echo $this->session->userdata('username');?></li>
				<li><a href="<?php echo site_url('manage_login/logout');?>">退出</a></li>
				</ul>
				<ul class="themeList" id="themeList">
					<li theme="default"><div class="selected">蓝色</div></li>
					<li theme="green"><div>绿色</div></li>
					<!--<li theme="red"><div>红色</div></li>-->
					<li theme="purple"><div>紫色</div></li>
					<li theme="silver"><div>银色</div></li>
					<li theme="azure"><div>天蓝</div></li>
				</ul>
			</div>

			<!-- navMenu -->
			
		</div>

		<div id="leftside">
			<div id="sidebar_s">
				<div class="collapse">
					<div class="toggleCollapse"><div></div></div>
				</div>
			</div>
			<div id="sidebar">
				<div class="toggleCollapse"><h2>主菜单</h2><div>收缩</div></div>

				<div class="accordion" fillSpace="sidebar" >
					<div class="accordionHeader">
						<h2><span>Folder</span>项目管理</h2>
					</div>
					<div class="accordionContent">
						<ul class="tree">
							<li><a href="<?php echo site_url('manage/list_project');?>" target="navTab" rel="list_project">项目管理</a></li>
						</ul>
					</div>
					
					
					<div class="accordionHeader">
						<h2><span>Folder</span>重要信息</h2>
					</div>
					<div class="accordionContent">
						<ul class="tree">
							<li><a href="<?php echo site_url('manage/message');?>" target="navTab" rel="message">重要信息</a></li>
						</ul>
					</div>
					
					<div class="accordionHeader">
						<h2><span>Folder</span>动态</h2>
					</div>
					<div class="accordionContent">
						<ul class="tree">
							<li><a href="<?php echo site_url('manage/list_news');?>" target="navTab" rel="list_news">动态列表</a></li>
						</ul>
					</div>

				

				</div>
			</div>
		</div>
		<div id="container">
			<div id="navTab" class="tabsPage">
				<div class="tabsPageHeader">
					<div class="tabsPageHeaderContent"><!-- 显示左右控制时添加 class="tabsPageHeaderMargin" -->
						<ul class="navTab-tab">
							<li tabid="main" class="main"><a href="javascript:;"><span><span class="home_icon">我的主页</span></span></a></li>
						</ul>
					</div>
					<div class="tabsLeft">left</div><!-- 禁用只需要添加一个样式 class="tabsLeft tabsLeftDisabled" -->
					<div class="tabsRight">right</div><!-- 禁用只需要添加一个样式 class="tabsRight tabsRightDisabled" -->
					<div class="tabsMore">more</div>
				</div>
				<ul class="tabsMoreList">
					<li><a href="javascript:;">我的主页</a></li>
				</ul>
				<div class="navTab-panel tabsPageContent layoutBox">
				<div class="page unitBox">
	<div class="accountInfo">
		<div class="alertInfo">
			<h2>使用手册</h2>
			<a>演示视频</a>
		</div>
		<p><span>个人主页内容</span></p>
		<p>个人主页内容</p>
	</div>
	<div class="pageFormContent" layoutH="80">
	</div>
</div>					
				</div>
			</div>
		</div>

	</div>

	<div id="footer">Copyright &copy; 2010 DWZ团队 京ICP备05019125号-10</div>


</body>
</html>