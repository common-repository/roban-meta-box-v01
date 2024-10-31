<?php
/*
Plugin Name: 	Roban Meta Box
Plugin Script: 	meta_box_main.php
Plugin URI:		http://www.robanlee.com
Description: 	Roban Custom Field Plugin
Version: 		2.0.2
Author: 		Roban Lee
Author URI: 	http://www.robanlee.com
*/

//uninstall plugin,drop database
//$action = isset($_GET['action']) ? $_GET['action'] : '';
//
//if(isset($action) && $action=='deactivate'){
//	
//	global $wpdb;
//
//	$wpdb->query('DROP TABLE RL_BOX');
//	
//	$wpdb->query('DROP TABLE RL_BOXMETA');
//	
//	return;
//
//}


if( ! class_exists('Roban_meta_box') ){
	
	class Roban_meta_box{
		
		protected  $Meta_Filds = array();
		protected $_common;
		
		function __construct(&$clsCommon){
			
			$this->_common = $clsCommon;
			add_action('admin_menu',array(&$this,'admin_menu'));
			add_action('admin_init', array(&$this,'init_box'));
			add_action('save_post', array(&$this,'Roban_MetaBox_save_data'),1,2);
			$this->Meta_Filds = $this->_common->get_Box();
		}
		
		function init_box() {
			
			$this->_common->createtable();
			
			if( empty( $this->Meta_Filds ) || count( $this->Meta_Filds ) <=0  || !$this->Meta_Filds ) return;
			
			foreach ( $this->Meta_Filds as $box_name => $box ){
				$box_type = isset($box['box_type']) ? strtolower(trim($box['box_type'])) : 'post';
				add_meta_box( $box_name,$box_name,array(&$this,'__addBox'),$box_type);
			}

		}
		function __addBox($object='', $box=''){
			global $post;
			$post_id = $post->ID;
			foreach ( ( array ) $this->Meta_Filds[$box['id']]['box_fields'] as $field_id => $field )
			{
				$field_name 	= isset($field['field_name']) 	?  strtolower($field['field_name']) : 'field_'.$field_id;
				$field_label	= isset($field['field_label']) 	?  strtolower($field['field_label']) : 'field_'.$field_id;
				$field_type		= isset($field['field_type']) 	?  strtolower($field['field_type']) : 'input';
				$field_class	= isset($field['field_class']) 	?  strtolower($field['field_class']) : 'default';
				$value 			= $this->get($post->ID,$field_name);
				
				wp_nonce_field( 'RL_BOX_'.$field_name.'_nonce', 'RL_BOX_'.$field_name.'_nonce' );

				$label_format ='<div class="%s">';
				switch ($field_type){
					case 'text':
						$label_format .= '<p><label for="%s"><strong>%s</strong></label></p>';
						$label_format .= '<input style="width: 100%%;" type="text" id="'.$field_name.'" name="'.$field_name.'" value="'.$value.'" />';
					break;
					
					case 'select':
						$label_format .= '<p><label for="%s"><strong>%s</strong></label></p>';
						$label_format .= '<select  name="'.$field_name.'" id="'.$field_name.'">';
						foreach ($field['field_meta'] as $meta)
						{
							if(strtolower($value) == strtolower(trim($meta['meta_value'])))
								$select = 'selected="selected"';
							else 
								$select='';
								
							$label_format.= '<option '.$select.' value="'.$meta['meta_value'].'">'.$meta['meta_key'].'</option>';
						}
						$label_format .= '</select>';
					break;
					//textarea
					case 'textarea':
						$label_format .= '<p><label for="%s"><strong>%s</strong></label></p>';
						$label_format .= '<textarea id="'.$field_name.'" name="'.$field_name.'" >'.$value.'</textarea>';
					break;
					case 'radio':
						$label_format .= '<p><label for="%s"><strong>%s</strong></label></p>';
						foreach ($field['field_meta'] as $meta)
						{
							if(strtolower($value) == strtolower(trim($meta['meta_value'])))
								$checked = 'checked="checked"';
							else 
								$checked='';
								
							$label_format.= '<input '.$checked.' type="radio" name="'.$field_name.'" id="'.$field_name.'" value="'.$meta['meta_value'].'" />'.$meta['meta_key'];
						}
						$label_format .= '</select>';
					break;
				}
				$label_format .= '</div>';
				printf( $label_format, $field_class, $field_name, $field_label);
			
			}
		}
		function get($post_id='',$field)
		{
			if ( empty( $post_id ) ) {
				global $post;
				$post_id = $post->ID;
			} 
			
			$value = get_post_meta( $post_id, $field, true );
			
			if ( is_wp_error( $value ) ) {
				return false;
			}
			
			return $value;
		}
		
		function Roban_MetaBox_save_data($post_id ,$args) {
			

			$post_type = isset($_POST['post_type']) ? $_POST['post_type'] : '';
			
			if(! $post_type ) return $post_id;

			  
		  if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE ) 
		    return $post_id;
		
		  
		  // Check permissions
		   if ( !current_user_can( 'manage_options' ) )
		      return $post_id;
		
	
		  foreach ($this->Meta_Filds as $id => $fields)
		  {
		  	if(strtolower($fields['box_type']) != $post_type) continue;
		  	
		  	

		  	
		  	foreach ($fields['box_fields'] as $f)
		  	{
		  		if(empty($f) || count($f) <=0 ) continue;
		  		$field_name = strtolower(trim($f['field_name']));
		  		
				if(empty($_POST) || ! wp_verify_nonce($_POST['RL_BOX_'.$field_name.'_nonce'],'RL_BOX_'.$field_name.'_nonce')){
					return $post_id;
				}
				
				$field_value = $_POST[$field_name];
				
				if(empty($field_value) || !isset($field_value))
					delete_post_meta($post_id,$field_name);
				else 
		  			update_post_meta($post_id,$field_name,$field_value);
 		  	}
		  	
		  }
		   return $post_id;
		}

		function admin_menu()
		{
			$pluginPath = plugin_dir_path(__FILE__);
			add_menu_page('Roban MetaBox',__('Roban MetaBox','roban-meta'),'manage_options',$pluginPath."/RL_metaBox_ui.php");
		}
			
	}
}
require_once("common.cls.php");
$clsCommon = new common();
$Roban_meta_box = new Roban_meta_box(&$clsCommon);

//载入语言包
load_plugin_textdomain('roban-meta', false, dirname( plugin_basename(__FILE__) ) . '/languages');