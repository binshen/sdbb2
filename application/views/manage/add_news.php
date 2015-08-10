<style type="text/css">
.file-box{ position:relative;width:340px}
.btn{ background-color:#FFF; border:1px solid #CDCDCD;height:21px; width:70px;}
.file{ position:absolute; top:0; right:80px; height:24px; filter:alpha(opacity:0);opacity: 0;width:300px }
</style>
<div class="pageContent">
    <form method="post"  action="<?php echo site_url('manage/save_news');?>" class="pageForm required-validate" onsubmit="return iframeCallback(this, navTabAjaxDone);">
        <div class="pageFormContent" layoutH="55">
        	<fieldset>
        	<legend>动态</legend>
        	    <dl>
        			<dt>动态标题：</dt>
        			<dd>
        			<input type="hidden" name="id" value="<?php if(!empty($id)) echo $id;?>">
        			<input name="title" type="text" class="required" value="<?php if(!empty($title)) echo $title;?>" />
        			</dd>
        		</dl>
        		
        		<dl class="nowrap">
        		<dt>动态内容：</dt>
    			<dd><textarea  name="content" rows="22" cols="100" upImgExt="jpg,jpeg,gif,png"  ><?php if(!empty($content)) echo $content;?></textarea></dd>
    			</dl>
        	</fieldset>
        </div>
        <div class="formBar">
    		<ul>
    			<li><div class="buttonActive"><div class="buttonContent"><button type="submit" class="icon-save">保存</button></div></div></li>
    			<li><div class="button"><div class="buttonContent"><button type="button" class="close icon-close">取消</button></div></div></li>
    		</ul>
        </div>
	</form>
</div>


