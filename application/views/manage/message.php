<div class="pageContent">
    <form method="post" action="<?php echo site_url('manage/save_message');?>" class="pageForm required-validate" onsubmit="return validateCallback(this, navTabAjaxDone);">
        <div class="pageFormContent" layoutH="55">
    	<fieldset>
    	    <legend>重要信息</legend>
    	    <dl>
    	    <dt>标题</dt>
    			<dd><input type="text" name="title" value="<?php if(!empty($title)) echo $title;?>" class="required"></dd>
    		</dl>
    	    <dl class="nowrap">
    			<dd><textarea class="editor" name="content" rows="22" cols="100" upImgUrl="<?php echo site_url('manage/upload_pic')?>" upImgExt="jpg,jpeg,gif,png" ><?php if(!empty($content)) echo $content;?></textarea></dd>
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


