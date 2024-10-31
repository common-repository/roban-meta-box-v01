<?php

define('ABSPATH', dirname(dirname(dirname(dirname(__FILE__)))).'/');

require_once(ABSPATH . 'wp-load.php');

if( ! current_user_can( 'manage_options' ) )
	die('You cannot access this file!');
	
$action = $_POST['action'];

if(empty($action))	die('Action Error');

require_once('common.cls.php');
$clsCommon = new common();

switch (strtolower($action))
{
	/*Field begin*/
	
	//Add Field
	case 'addfield':
		$updateArray = array(
			'box_id'		=>	$_POST['box_id'],
			'field_name'	=>	$_POST['field_name'],
			'field_type'	=>	$_POST['field_type'],
			'field_class'	=>	$_POST['field_class'],
			'field_label'	=>	$_POST['field_label'],
		);
		_add($updateArray);
	break;
	
	//Update field
	case 'upfield':
		$updateArray = array(
			'field_name'	=>	$_POST['field_name'],
			'field_type'	=>	$_POST['field_type'],
			'field_class'	=>	$_POST['field_class'],
			'field_label'	=>	$_POST['field_label'],
		);
		update($updateArray,array('id'=>$_POST['id']));
	break;
	
	//Delete Field
	
	case 'delfield':
		deleteField($_POST['id']);
	break;
	
	/*Field End*/
	
	/*Meta begin*/
	//Delete Meta
	case 'delmeta':
		_delMeta($_POST['id']);
	break;
	
	//Add Meta
	case 'addmeta':
		$arrTemp = array(
			'box_id'		=>	$_POST['box_id'],
			'meta_key'		=>	$_POST['meta_key'],
			'meta_value'	=>	$_POST['meta_value'],
		);
		_add($arrTemp,'RL_BOXMETA');
	break;
	
	//Update meta
	case 'upmeta':
		$arrTemp = array(
			'meta_key'		=>	$_POST['meta_key'],
			'meta_value'	=>	$_POST['meta_value'],
		);
		update($arrTemp,array('id'=>$_POST['id']),'RL_BOXMETA');
	break;
	/*Meta end*/
	
	/* Group begin */
	
	//Delete group
	case 'delgroup':
		_delgroup($_POST['id']);
	break;

	//Add group
	case 'addgroup':
		$data = array(
			'box_id'=>0,
			'field_name'=>$_POST['field_name'],
			'field_type'=>$_POST['field_type']
		);
		_add($data);
	break;
	
	//Update group
	case 'upgroup':
		$where = array('id'=>$_POST['id']);
		$data = array('field_name'=>$_POST['field_name'],
			'field_type'=>$_POST['field_type']);
		update($data,$where);
	break;
	/* Group end */

}
function _add($data,$table='RL_BOX')
{
	global $clsCommon;
	echo $clsCommon->_doAdd($data,$table);
}
function update($data,$where,$table='RL_BOX')
{
	global $clsCommon;
	echo $clsCommon->update($data,$where,$table);
}
function _delgroup($id)
{
	global $clsCommon;
	echo $clsCommon->deleteGroup($id);
}
function deleteField($id)
{
	global $clsCommon;
	echo $clsCommon->deleteField($id);
}
function _delMeta($id)
{
	global $clsCommon;
	echo $clsCommon->deleteMeta($id);
}