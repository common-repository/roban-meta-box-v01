<?php

if ( !defined('ABSPATH') )
	die('-1');
	
require_once(ABSPATH . 'wp-load.php');

/**
 * @name common
 * @author  Roban Lee
 * @todo Main class of RobanMetaBox
 */
class common {
	
	protected $_wpdb;
	
	function __construct(){
		global $wpdb;
		$this->_wpdb = &$wpdb;
	}
	
	function get_Box(){
		$arrTemp = $this->get_field(0);
		$arrReturn = array();
		if(empty($arrTemp) || count($arrTemp) <=0) return false;
		foreach ($arrTemp as $loop => $box){
			$arrReturn[$box['field_name']]['box_id']	=	$box['id'];
			$arrReturn[$box['field_name']]['box_class'] =	$box['field_class'];
			$arrReturn[$box['field_name']]['box_type'] 	=	$box['field_type'];
			$arrReturn[$box['field_name']]['box_label'] =	$box['field_label'];
			
			$fields = $this->get_field($box['id']);
			
			
			if(empty($fields) || count($fields) <= 0 ){
				$arrReturn[$box['field_name']]['box_fields'] ='';
				continue;
			}
		
			foreach ($fields as $field_loop => $field){
				
				if(empty( $field ) || count( $field ) <= 0 ) continue;
				$field_meta = $this->get_field_meta($field['id']);
				
				if(empty( $field_meta ) || count( $field_meta ) <= 0 ) continue;
				$fields[$field_loop]['field_meta'] = $field_meta;
			}
			
			$arrReturn[$box['field_name']]['box_fields'] =$fields;
			
			
		}
		return $arrReturn;
	}
	
	function createtable()
	{
			$strTable ='
			CREATE TABLE IF NOT EXISTS `rl_box` (
			  `id` int(11) NOT NULL AUTO_INCREMENT,
			  `box_id` int(11) NOT NULL,
			  `field_name` varchar(100) DEFAULT NULL,
			  `field_type` varchar(100) DEFAULT NULL,
			  `field_class` varchar(100) DEFAULT NULL,
			  `field_label` varchar(100) DEFAULT NULL,
			  PRIMARY KEY (`id`)
			)  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;';
			$this->_wpdb->query($strTable);
			$strTable ='
			CREATE TABLE IF NOT EXISTS `rl_boxmeta` (
			  `id` int(11) NOT NULL AUTO_INCREMENT,
			  `box_id` int(11) NOT NULL,
			  `meta_key` varchar(100) NOT NULL,
			  `meta_value` longtext NOT NULL,
			  PRIMARY KEY (`id`)
			)DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;';
			$this->_wpdb->query($strTable);
	}
	function get_field($BoxID =0)
	{
		$pareparedString =  $this->_wpdb->prepare('select * from rl_box where box_id = %d ',$BoxID);
		$arrResult = $this->_wpdb->get_results( $pareparedString,ARRAY_A);
		
		if( empty($arrResult) || count( $arrResult ) <=0 ) return ;
		
		return $arrResult;
	}
	function get_field_meta($field_id=0,$meta_key='')
	{
		$pareparedString =  $this->_wpdb->prepare('select * from rl_boxmeta where box_id = %d ',$field_id);
		
		if(isset($meta_key) && !empty( $meta_key ))
		{
			$condition = ' and meta_key = %s';
			$pareparedString .=  $this->_wpdb->prepare($condition,$meta_key);
		}
		
		
		$arrResult = $this->_wpdb->get_results( $pareparedString,ARRAY_A);
		
		if( empty($arrResult) || count( $arrResult ) <=0 ) return ;
		
		return $arrResult;
	}
	
	// delete group 
	function deleteGroup($GroupID)
	{
		//global  $wpdb;
		//Check child element
		$numOf = $this->_wpdb->get_var('select count(*) from rl_box where box_id ='.intval($GroupID));
		if( intval($numOf) >0 )	return -1;
		
		$flag = $this->_wpdb->query('DELETE FROM RL_BOX WHERE ID = '.intval($GroupID));
		return $flag;
	}
	
	//Update 
	function update($data,$where,$table='RL_BOX')
	{
		$result = $this->_wpdb->update($table,$data,$where);
		return $result;
	}
	
	//Add group 
	function _doAdd($data,$table='RL_BOX')
	{
		$this->_wpdb->insert($table,$data);
		
		return $this->_wpdb->insert_id;
	}
	
	//Delete Field
	function deleteField($ItemID=0)
	{
		$this->deleteMetaByParentID($ItemID);
		
		$result = $this->_wpdb->query("DELETE FROM rl_box where id = ".intval($ItemID));
		return $result;
	}
	
	function deleteMetaByParentID($parentID = 0)
	{
		$result = $this->_wpdb->query("DELETE FROM rl_boxmeta where box_id = ".intval($parentID));
		
		return $result;
	}
	
	//Delete Meta
	function deleteMeta($metaID=0)
	{
		$str = "DELETE FROM rl_boxmeta where id = ".intval($metaID);
		$result = $this->_wpdb->query($str);
		return $result;
	}
}