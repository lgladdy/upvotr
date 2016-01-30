<?php
	
namespace upvotr;

class upvotr {
	
	function __construct() {
		add_action('parse_request', array($this, 'action_check'));
		register_activation_hook(__FILE__, array($this, 'check_versions'));
	}
	
	function check_versions() {
		global $wp_version;
		$wp = '4.1';
		$php = '5.3';
		if (version_compare(PHP_VERSION, $php, '<')) {
			$flag = 'PHP';
		} elseif (version_compare($wp_version, $wp, '<')) {
			$flag = 'WordPress';
		} else return;
		$version = 'PHP' == $flag ? $php : $wp;
		deactivate_plugins(basename( __FILE__ ));
		
		$error_type = __('Plugin Activation Error', 'culture-object');
		$error_string = sprintf(
			/* Translators: 1: Either WordPress or PHP, depending on the version mismatch 2: Required version number */
			__('Culture Object requires %1$s version %2$s or greater.', 'culture-object'),
			$flag,
			$version
		);
		
		wp_die('<p>'.$error_string.'</p>', $error_type,  array('response'=>200, 'back_link'=>TRUE));
	}
	
	function action_check() {
		if (isset($_GET['upvote']) && !empty($_GET['upvote']) && intval($_GET['upvote']) == $_GET['upvote']) {
			$user = get_current_user_id();
			if (empty($user)) return;
			try {
				$this->add_post_upvote_for_user($_GET['upvote'],$user);
				$path = preg_replace('/\??upvote=[0-9]*/', '', $_SERVER["REQUEST_URI"]);
				wp_redirect($path, 301);
				exit();
			} catch(Exception $e) {
				if (WP_DEBUG) echo "Upvote Failed: ".$e->getMessage();
				return false;
			}
		} else if (isset($_GET['downvote']) && !empty($_GET['downvote']) && intval($_GET['downvote']) == $_GET['downvote']) {
			$user = get_current_user_id();
			if (empty($user)) return;
			try {
				$this->remove_post_upvote_for_user($_GET['downvote'],$user);
				$path = preg_replace('/\??downvote=[0-9]*/', '', $_SERVER["REQUEST_URI"]);
				wp_redirect($path, 301);
				exit();
			} catch(Exception $e) {
				if (WP_DEBUG) echo "Downvote Failed: ".$e->getMessage();
				return false;
			}
		}
	}
	
	function get_post_upvotes($post) {
		if (!get_post($post)) throw new \Exception("Invalid Post ID");
		$upvotes = get_post_meta($post,'upvotr_upvotes',true);
		if (!is_array($upvotes)) return array();
		return array_keys($upvotes);
	}
	
	function get_post_upvote_count($post) {
		return count($this->get_post_upvotes($post));
	}
	
	function has_upvoted_post($post,$user) {
		if (intval($post) != $post || intval($user) != $user || $user == 0 || $post == 0) throw new \Exception('Both post and user fields must be integers and not 0');
		$upvotes = get_post_meta($post,'upvotr_upvotes',true);
		if ($upvotes) {
			if (isset($upvotes[$user])) {
				return true;
			}
		}
		return false;
	}
	
	function add_post_upvote_for_user($post,$user) {
		if (intval($post) != $post || intval($user) != $user || $user == 0 || $post == 0) throw new \Exception('Both post and user fields must be integers and not 0');
		if (!get_post($post)) throw new \Exception("Invalid Post ID");
		$upvotes = get_post_meta($post,'upvotr_upvotes',true);
		$upvotes[$user] = true;
		$upvotes_count = count($upvotes);
		update_post_meta($post, 'upvotr_upvotes', $upvotes);
		update_post_meta($post, 'upvotr_upvote_count', $upvotes_count);
	}
	
	function remove_post_upvote_for_user($post,$user) {
		if (intval($post) != $post || intval($user) != $user || $user == 0 || $post == 0) throw new \Exception('Both post and user fields must be integers and not 0');
		if (!get_post($post)) throw new \Exception("Invalid Post ID");
		$upvotes = get_post_meta($post,'upvotr_upvotes',true);
		unset($upvotes[$user]);
		$upvotes_count = count($upvotes);
		update_post_meta($post, 'upvotr_upvotes', $upvotes);
		update_post_meta($post, 'upvotr_upvote_count', $upvotes_count);
	}
	
	function get_users_upvotes($user) {
		$user_exists = get_user_by('id', $user);
		if (!$user_exists) throw new \Exception('Invalid User');
		$regex_search = 'i:'.strval($user).';';
		$upvotes = get_posts(array(
			'posts_per_page' => -1,
			'post_type' => 'any',
			'meta_query' => array(
				array(
					'key' => 'upvotr_upvotes',
					'value' => $regex_search,
					'compare' => 'REGEXP'
				)
			)
		));
		return $upvotes;
	}
	
	function get_users_upvote_count($user) {
		return count(get_users_upvotes($user));
	}
}