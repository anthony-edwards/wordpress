<?php
/*
Plugin Name: GE KSES
Description: Adds whitelist of HTML tags allowed to be used for content.
Version: 1.0
Author: Anthony Edwards
*/
//Block direct access to plugin
defined( 'ABSPATH' ) or die();
require_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'kses-plus-settings.php');
function kses_plus_allowed_tags() {
	$user = wp_get_current_user();
	$kses_roles = get_option( 'kses_plus' );
	$kses_roles = $kses_roles['kses_plus_roles'];
	$allowed_roles = $kses_roles;
	if( array_intersect($allowed_roles, $user->roles ) ) {
		global $allowedtags;
	  $tags = get_option('kses_plus');
	  $tags = $tags['allowed_tags'];
	  $ge_tags = explode(',',$tags);
	  foreach ($ge_tags as $key => $tag) {
	    $allowedtags[$tag] = array();
	  }
  }
}
/**
 * Filter Title field for any post created in the application
 * @param  [type] $data    [description]
 * @param  [type] $postarr [description]
 * @return [type]          [description]
 */
function filter_post( $data , $postarr )
{
	global $allowedtags;
	$id = $postarr["ID"];
	$data['post_title'] = wp_kses($data['post_title'],$allowedtags);
	$data['post_content'] = wp_kses($data['post_content'],$allowedtags);
  return $data;
}
add_action('init', 'kses_plus_allowed_tags');
add_filter( 'wp_insert_post_data' , 'filter_post' , '99', 2 );
