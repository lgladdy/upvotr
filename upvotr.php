<?php
/**
 * Plugin Name: Upvotr
 * Plugin URI: https://gladdy.uk/projects/upvotr
 * Description: A plugin to enable user upvoting of post objects.
 * Version: 1.0
 * Author: Liam Gladdy
 * Text Domain: upvotr
 * Author URI: https://github.com/lgladdy
 * GitHub Plugin URI: lgladdy/upvotr
 * GitHub Branch: master
 * License: Apache 2 License
 */
 
require_once('upvotr/upvotr.class.php');
$upvotr = new \upvotr\upvotr();

function get_post_upvotes($id = false) {
	global $upvotr;
	if ($id == false) {
		$id = get_the_ID();
		if (empty($id)) return false;
	}
	return $upvotr->get_post_upvotes($id);
} 

function get_post_upvote_count($id = false) {
	global $upvotr;
	if ($id == false) {
		$id = get_the_ID();
		if (empty($id)) return false;
	}
	return $upvotr->get_post_upvote_count($id);
}

function has_upvoted_post($id = false, $user = false) {
	global $upvotr;
	if ($id == false) {
		$id = get_the_ID();
		if (empty($id)) return false;
	}
	if ($user == false) {
		$user = get_current_user_id();
		if (empty($user)) return false;
	}
	try {
		return $upvotr->has_upvoted_post($id, $user);
	} catch(Exception $e) {
		return false;
	}
}

function get_users_upvotes($user = false) {
	global $upvotr;
	if ($user == false) {
		$user = get_current_user_id();
		if (empty($user)) return false;
	}
	try {
		return $upvotr->get_users_upvotes($user);
	} catch(Exception $e) {
		return array();
	}
}