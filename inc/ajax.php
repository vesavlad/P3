<?php
/**
 * Handle Ajax write and permissioned requests.
 *
 * @package P3
 */

if ( ! class_exists( 'P3Ajax_Read' ) ) {
	require dirname( __FILE__ ) . '/ajax-read.php';
}

if ( defined('DOING_AJAX') && DOING_AJAX && isset( $_REQUEST['P3ajax'] ) ) {
	add_action( 'admin_init', array( 'P3Ajax', 'dispatch' ) );
}

/*
 * We include all of P3Ajax_Read's methods so that the old, deprecated API (db_version=1) still works for logged in users.
 * @todo: in the next release, remove the old API completele by doing:
 * class P3Ajax {
 * That is, stop including ajax-read.php in this file and drop the extends.
 * We only include in now for backward compatibility so that currently open P3 windows continue to function.
 * By the time the next release rolls around, it'll be safe to remove.
 */
class P3Ajax extends P3Ajax_Read {
	public static function dispatch() {
		$action = isset( $_REQUEST['action'] ) ? sanitize_text_field($_REQUEST['action']) : '';

		do_action( "P3_ajax", $action );
		if ( is_callable( array( 'P3Ajax', $action ) ) )
			call_user_func( array( 'P3Ajax', $action ) );
		else
			die( '-1' );
		exit;
	}

	/*
	 * Get post to edit.
	 */
	public static function get_post() {
		check_ajax_referer( 'ajaxnonce', '_inline_edit' );
		if ( !is_user_logged_in() ) {
			die( '<p>'.__( 'Error: not logged in.', 'P3' ).'</p>' );
		}
		$post_id = sanitize_text_field($_GET['post_ID']);
		$post_id = substr( $post_id, strpos( $post_id, '-' ) + 1 );
		if ( !current_user_can( 'edit_post', $post_id ) ) {
			die( '<p>'.__( 'Error: not allowed to edit post.', 'P3' ).'</p>' );
		}

		// Don't treat the post differently based on user's visual editor setting.
		// If the user has disabled the visual editor, the post_content goes through an "extra" esc_textarea().
		add_filter( 'user_can_richedit', '__return_true' );
		$post = get_post( $post_id, OBJECT, 'edit' );

		function get_tag_name( $tag ) {
			return $tag->name;
		}
		$tags = array_map( 'get_tag_name', wp_get_post_tags( $post_id ) );

		$post_format = P3_get_post_format( $post_id );

		// handle page as post_type
		if ( 'page' == $post->post_type ) {
			$post_format = '';
			$tags = '';
		}

		add_filter( 'user_can_richedit', '__return_false' );
		$post->post_content = apply_filters( 'the_editor_content', $post->post_content );

		echo json_encode( array(
			'title' => $post->post_title,
			'content' => $post->post_content,
			'post_format' => $post_format,
			'post_type' => $post->post_type,
			'tags' => $tags,
		) );
	}

	/*
	 * Get comment to edit.
	 */
	public static function get_comment() {
		check_ajax_referer( 'ajaxnonce', '_inline_edit' );
		if ( !is_user_logged_in() ) {
			die( '<p>'.__( 'Error: not logged in.', 'P3' ).'</p>' );
		}
		$comment_id = sanitize_text_field($_GET['comment_ID']);
		$comment_id = substr( $comment_id, strpos( $comment_id, '-' ) + 1);
		$comment = get_comment($comment_id);
		echo apply_filters( 'P3_get_comment_content', $comment->comment_content, $comment_id );
	}

	/*
	 * Edit a post.
	 */
	public static function save_post() {
		check_ajax_referer( 'ajaxnonce', '_inline_edit' );
		if ( !is_user_logged_in() ) {
			die( '<p>'.__( 'Error: not logged in.', 'P3' ).'</p>' );
		}

		$post_id = sanitize_text_field($_POST['post_ID']);
		$post_id = substr( $post_id, strpos( $post_id, '-' ) + 1 );

		if ( !current_user_can( 'edit_post', $post_id )) {
			die( '<p>'.__( 'Error: not allowed to edit post.', 'P3' ).'</p>' );
		}
		$post_format = P3_get_post_format( $post_id );
		$new_post_content = esc_html($_POST['content']);

		// Add the quote citation to the content if it exists
		if ( ! empty( $_POST['citation'] ) && 'quote' == $post_format ) {
			$new_post_content = '<p>' . $new_post_content . '</p><cite>' . sanitize_text_field($_POST['citation']) . '</cite>';
		}

		$new_tags = sanitize_text_field($_POST['tags']);

		$new_post_title = isset( $_POST['title'] ) ? sanitize_text_field($_POST['title']) : '';

		if ( ! empty( $new_post_title ) )
			$post_title = $new_post_title;
		else
			$post_title = P3_title_from_content( $new_post_content );

		$post = wp_update_post( array(
			'post_title'	=> $post_title,
			'post_content'	=> $new_post_content,
			'post_modified'	=> current_time( 'mysql' ),
			'post_modified_gmt'	=> current_time( 'mysql', 1),
			'ID' => $post_id
		) );

		$tags = wp_set_post_tags( $post_id, $new_tags );

		$post = get_post( $post );
		$GLOBALS['post'] = $post;

		if ( !$post ) die( '-1' );

		if ( 'quote' == $post_format )
			$content = apply_filters( 'P3_get_quote_content', $post->post_content );
		else
			$content = apply_filters( 'the_content', $post->post_content );

		echo json_encode( array(
			'title' => $post->post_title,
			'content' => $content,
			'tags' => get_tags_with_count( $post, '', __( '<br />Tags:' , 'P3' ) . ' ', ', ', ' &nbsp;' ),
		) );
	}

	/*
	 * Edit a comment.
	 */
	public static function save_comment() {
		check_ajax_referer( 'ajaxnonce', '_inline_edit' );
		if ( !is_user_logged_in() ) {
			die( '<p>'.__( 'Error: not logged in.', 'P3' ).'</p>' );
		}

		$comment_id	= sanitize_text_field($_POST['comment_ID']);
		$comment_id = substr( $comment_id, strpos( $comment_id, '-' ) + 1);
		$comment = get_comment( $comment_id );

		if ( !current_user_can( 'edit_post', $comment->comment_post_ID ) ) {
			die( '<p>'.__( 'Error: not allowed to edit this comment.', 'P3' ).'</p>' );
		}

		$comment_content = esc_html($_POST['comment_content']);

		wp_update_comment( array(
			'comment_content'	=> $comment_content,
			'comment_ID' => $comment_id
		));

		$comment = get_comment( $comment_id );
		echo apply_filters( 'comment_text', $comment->comment_content, $comment );
	}

	/*
	 * Create a post.
	 */
	public static function new_post() {
		global $user_ID;

		if ( empty( $_POST['action'] ) || $_POST['action'] != 'new_post' ) {
		    die( '-1' );
		}
		if ( !is_user_logged_in() ) {
			die( '<p>'.__( 'Error: not logged in.', 'P3' ).'</p>' );
		}
		
		if ( ! ( current_user_can( 'publish_posts' ) ||
		       ( ot_get_option('postingaccess') ) && $user_ID ) ) {

			die( '<p>'.__( 'Error: not allowed to post.', 'P3' ).'</p>' );
		}

		check_ajax_referer( 'ajaxnonce', '_ajax_post' );

		$user           = wp_get_current_user();
		$user_id        = $user->ID;
		$post_content   = esc_html($_POST['posttext']);
		$tags           = trim( esc_html($_POST['tags'] ));
		$title          = sanitize_text_field($_POST['post_title']);
		$post_type      = isset( $_POST['post_type'] ) ? sanitize_text_field($_POST['post_type']) : 'post';

		// Strip placeholder text for tags
		if ( __( 'Tag it', 'P3' ) == $tags )
			$tags = '';

		// For empty or placeholder text, create a nice title based on content
		if ( empty( $title ) || __( 'Post Title', 'P3' ) == $title )
	    	$post_title = P3_title_from_content( $post_content );
		else
			$post_title = $title;

		$post_format = 'status';
		$accepted_post_formats = apply_filters( 'P3_accepted_post_cats', P3_get_supported_post_formats() ); // Keep 'P3_accepted_post_cats' filter for back compat (since P3 1.3.4)
		if ( in_array( $_POST['post_format'], $accepted_post_formats ) )
			$post_format = esc_html($_POST['post_format']);

		// Add the quote citation to the content if it exists
		if ( ! empty( $_POST['post_citation'] ) && 'quote' == $post_format )
			$post_content = '<p>' . $post_content . '</p><cite>' . esc_html($_POST['post_citation']) . '</cite>';

		$post_id = wp_insert_post( array(
			'post_author'   => $user_id,
			'post_title'    => $post_title,
			'post_content'  => $post_content,
			'post_type'     => 'post',
			'tags_input'    => $tags,
			'post_status'   => 'publish'
		) );

		if ( empty( $post_id ) )
			echo '0';

		set_post_format( $post_id, $post_format );
		echo $post_id;
	}
}
