<?php

if ( !defined('ABSPATH') )
	die('-1');
	
require_once(ABSPATH . 'wp-load.php');

if(! current_user_can('manage_options'))
{
	die("You cannot visit this page!");
}

require_once("common.cls.php");

$cls = new common();
$RobanBox = $cls->get_Box();
?>
<style>
	.error{ border:1px solid #ff0000 !important; padding: 3px 4px;}
	.hide { display: none;}
    #listPost { padding-top: 15px;}
    #listPost .title { background: #E9E9E9; padding: 3px 10px;}
	#listPost .meta-box { border-top: 1px solid #DCDCDC; padding: 8px 0 5px;}
    #listPost .meta-item span { padding: 0 5px 0 10px; }
    #listPost .meta-field-box {  border-top: 1px solid #DCDCDC; }
	#listPost .meta-field-item {  padding: 10px 0 10px 40px; }
    #listPost .meta-field-item span, #divAddGroup .add-box span {padding: 0 5px 0 10px;  }
	.listPost-box { border: 1px solid #DCDCDC; margin-bottom: 15px; background: #FCFCFC;}
	#divAddGroup { border: 1px solid #DCDCDC; padding: 8px 0; margin-top: 15px; background: #FCFCFC;}
</style>
<link href="<?php echo plugin_dir_url(__FILE__)?>/RL_Style.css" rel="stylesheet" type="text/css" />
<script language="javascript" src="<?php echo plugin_dir_url(__FILE__).'/js/jquery.JS'?>"></script>
<script>
	postUrl 	= '<?php echo plugin_dir_url(__FILE__)?>/RL_metaBox_disp.php';
	loadPath 	= '<?php echo plugin_dir_url(__FILE__)?>/RL_metaBox_ctr.php';
	
	/*Update Log 6,01*/
	$(function(){
		$('[htype]').val('');
	});
	
	function _doDelete(action,frmName,opener)
	{
		if(!confirm('您确定要删除条数据?')) return false;
		
		_postForm(action,frmName,opener);
	}

	
	function _doCheck(obj){
		var obj = $('#'+obj);
		var returnValue = false;
		obj.find('input:text').each(function(){
			if($(this).val()==''){
				$(this).addClass('error');
				returnValue = false;}
			else
			{
				$(this).removeClass('error');
				returnValue =true;
			}
		});
		
		return returnValue;
	}
	
	function add(action,objFrm,opener)
	{
		var flag = _doCheck(objFrm);
		
		if(!flag) return false;
		
		_postForm(action,objFrm,opener);
	}
	
	function _postForm(action,frmName,opener)
	{
		if(action =='' || typeof action =='undefined')	return false;
		var frmValue = $('#'+frmName).serialize();
		var strValue = 'action='+encodeURIComponent(action)+'&cookie='+encodeURIComponent(document.cookie)+'&'+frmValue;
		
		$.post(postUrl,strValue,function(data){
			if( parseInt(data) >0 ) location.reload();
			else
			opener.value='无更新,请重试.';
		});
	}
	
	function _display(objName,show)
	{
		if(show)
			$('#'+objName).show();
		else
			$('#'+objName).hide();
	}
</script>
<div class="wrap">
<h2><?php _e('Roban Meta Box', 'roban-meta'); ?> <input type="button" value="<?php _e('AddGroup', 'roban-meta'); ?>" onclick="_display('divAddGroup',true);"/></h2>

<div id="divAddGroup" class="hide">
	<form id="frmAddGroup">
	<div class="add-box">
		<span><?php _e('Group name', 'roban-meta'); ?>: <input type="text" name="field_name" id="field_name" htype="add"/></span>
		<span><?php _e('ffield type', 'roban-meta'); ?>
			 <select id="field_type" name="field_type">
			<option value ="post">POST</option>
			<option value ="page">PAGE</option>
			</select>
		</span>
		<span>
			<input type="button" value="<?php _e('Add', 'roban-meta'); ?>" name="btnAdd" onclick="add('addgroup','frmAddGroup',this);"/>
			<input type="button" value="<?php _e('Cancel', 'roban-meta'); ?>" onclick="_display('divAddGroup',false);"/>
		</span>
	</div>
	</form>
</div>

<div id="listPost">
	<?php
	if(count($RobanBox) <=0 || empty($RobanBox)):
		echo '<p>' ;
		echo __('No custom groups found,Please add first' , 'roban-meta');
		echo '</p>' ;
	else:
	foreach ($RobanBox as $box_name => $box):
	?>
<div class="listPost-box">

	<form id="frmGroup_<?=$box['box_id']?>">
	<div class="title">
		<span><input type="text" name="field_name" id="field_name" value="<?=$box_name?>"/></span>
		<span>
			<select name="field_type" id="field_type">
			<option value ="post" <?php if(strtolower(trim($box['box_type']))=='post') echo 'selected="selected"';?> >POST</option>
			<option value ="page" <?php if(strtolower(trim($box['box_type']))=='page') echo 'selected="selected"';?>>PAGE</option>
			</select>
		</span>
		<span>
			<input type="hidden" id="id" name="id" value="<?=$box['box_id']?>" />
			<input type="button" value="<?php _e('Add field', 'roban-meta'); ?>" name="btnAdd" onclick="_display('divAddField_<?=$box['box_id']?>',true);"/>
			<input type="button" value="<?php _e('DelGroup', 'roban-meta'); ?>" name="btnDelGroup" onclick="_doDelete('delgroup','frmGroup_<?=$box['box_id']?>',this)"/>
			<input type="button" value="<?php _e('UpdateGroup', 'roban-meta'); ?>" name="btnUpdateGroup" onclick="_postForm('upgroup','frmGroup_<?=$box['box_id']?>',this);" />
		</span>
	</div>
	</form>

	<div class="meta-box hide" id="divAddField_<?=$box['box_id']?>">
		<form id="frmAddField_<?=$box['box_id']?>">
		<div class="meta-item">
			<span><?php _e('Lebel', 'roban-meta'); ?>:
				<input type="hidden" id="box_id" name="box_id" value="<?=$box['box_id']?>" />
				<input type=text name ="field_label" id="field_label" htype="add" />
			</span>
			<span><?php _e('field name', 'roban-meta'); ?>: <input type=text name ="field_name" id="field_name" htype="add" /></span>
			<span><?php _e('field type', 'roban-meta'); ?>:
				<select name ="field_type" id="field_type">
				<option  value="text">Text Field</option>
				<option value="textarea">TextArea</option>
				<option value="select">Select</option>
				<option value="radio">RadioBox</option>
				</select>
			</span>
			<span><?php _e('style', 'roban-meta'); ?>: <input type=text name ="field_class" id="field_class" htype="add" />
			</span>
			<span>
				<input type="button" value="<?php _e('Add', 'roban-meta'); ?>" onclick="add('addfield','frmAddField_<?=$box['box_id']?>',this)" />
				<input type="button" value="<?php _e('Cancel', 'roban-meta'); ?>" onclick="_display('divAddField_<?=$box['box_id']?>',false);"/>
			</span>
		</div>
		</form>
	</div>
	<?php if( empty( $box['box_fields'] )  || count( $box['box_fields'] ) <=0) {
		echo '</div>';continue;
	}
		foreach ($box['box_fields'] as $loop => $field ):
		$field_type = strtolower(trim($field['field_type']));
	?>

	<div class="meta-box">
		<form id="frmField_<?=$field['id']?>">
			<div class="meta-item">
				<span><?php _e('Lebel', 'roban-meta'); ?>: <input type=text name ="field_label" id="field_label" value="<?=$field['field_label']?>" /></span>
				<span><?php _e('field name', 'roban-meta'); ?>: <input type=text name ="field_name" id="field_name" value="<?=$field['field_name']?>" /></span>
				<span><?php _e('field type', 'roban-meta'); ?>:
					<select name ="field_type" id="field_type">
					<option  value="text" <?php if($field_type =='text') echo 'selected = "selected"' ?>>Text Field</option>
					<option value="textarea"  <?php if($field_type =='textarea') echo 'selected = "selected"' ?>>TextArea</option>
					<option value="select"  <?php if($field_type =='select') echo 'selected = "selected"' ?>>Select</option>
					<option value="radio"  <?php if($field_type =='radio') echo 'selected = "selected"' ?>>RadioBox</option>
					</select>
				</span>
				<span><?php _e('style', 'roban-meta'); ?>: <input type=text name ="field_class" id="field_class" value="<?=$field['field_class']?>" /></span>
				<span>
					<input type="hidden" id="id" name="id" value="<?=$field['id']?>"/>
					<input type="button" value="<?php _e('AddField', 'roban-meta'); ?>" onclick="_display('meta_p_<?=$field['id']?>',true);"/>
					<input type="button" value="<?php _e('DeleteField', 'roban-meta'); ?>" name="btnDelField" onclick="_doDelete('delfield','frmField_<?=$field['id']?>',this)"/>
					<input type="button" value="<?php _e('UpField', 'roban-meta'); ?>" name="btnUpField" onclick="_postForm('upfield','frmField_<?=$field['id']?>',this);"  />
				</span>
			</div>
		</form>
	</div>

	<div class="meta-field-box hide" id="meta_p_<?=$field['id']?>">
		<form id="metaFrm_<?=$field['id']?>">
			<div class="meta-field-item">
				<span>Meta Key: <input id="meta_key" name="meta_key" type="text" value="" htype="add"/></span>
				<span>Meta Value: <input id="meta_value" name="meta_value"  type="text" value="" htype="add"/></span>
				<span>
					<input type="hidden" name="box_id" value="<?=$field['id']?>" id="box_id"/>
					<input type="button" value="<?php _e('Add', 'roban-meta'); ?>" onclick="add('addmeta','metaFrm_<?=$field['id']?>',this)"/>
					<input type="button" value="<?php _e('Cancel', 'roban-meta'); ?>" onclick="_display('meta_p_<?=$field['id']?>',false);"/>
				</span>
			</div>
		</form>
	</div>
					

	<?php if($field_type=='select' || $field_type =='radio'):?>
	<?php
		if(!isset($field['field_meta']) || count( $field['field_meta'] ) <=0 || empty($field['field_meta']))
		continue;
		foreach ($field['field_meta'] as $meta):
	?>
	<div class="meta-field-box">
		<form id="frmMetaInfo_<?=$meta['id']?>">
		<div class="meta-field-item">
			<span>Meta Key: <input id="meta_key" name="meta_key" type="text" value="<?=$meta['meta_key']?>" /></span>
			<span>Meta Value: <input id="meta_value" name="meta_value" type="text" value="<?=$meta['meta_value']?>" /></span>
			<span>
			<input id="id" name="id" type="hidden" value="<?=$meta['id']?>" />
			<input type="button" value="<?php _e('delete', 'roban-meta'); ?>"  name="btnDelMeta" onclick="_doDelete('delmeta','frmMetaInfo_<?=$meta['id']?>',this)"/>
			<input type="button" value="<?php _e('update', 'roban-meta'); ?>"  name="btnUpMeta" onclick="_postForm('upmeta','frmMetaInfo_<?=$meta['id']?>',this);"/>
			</span>
		</div>
		</form>
	</div>
	<?php endforeach; endif; ?>		
										
	<?php endforeach; ?> <!--172-->
	

</div>			
<?php 
endforeach; 
?> <!--118-->

<?php endif;	?>
</div>
</div>