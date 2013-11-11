<?php
/**
 * Mentions.
 *
 * @package P3
 * @since P3 1.3.1
 */


/**
 * Handler for the mentions taxonomy.
 *
 * @package P3
 * @since unknown
 */
class P3_Mentions extends P3_Terms_In_Comments {
	var $names          = array();
	var $users          = array();
	var $mentions_regex = '/\B@([\w-\.]+)\b/';

	function P3_Mentions() {
		P3_maybe_define( 'P3_MENTIONS_TAXONOMY', 'mentions', 'P3_mentions_taxonomy' );
		P3_maybe_define( 'P3_MENTIONS_SLUG',     'mentions', 'P3_mentions_slug'     );

		// Hooks
		add_action( 'init',              array( &$this, 'init'            ), 0 );
		add_filter( 'the_content',       array( &$this, 'mention_links'   ), 5 );
		add_filter( 'comment_text',      array( &$this, 'mention_links'   ), 5 );
		add_filter( 'P3_found_mentions', array( &$this, 'filter_mentions' ), 5 );

		parent::P3_Terms_In_Comments( P3_MENTIONS_TAXONOMY );
	}

	/**
	 * Register P3 mentions taxonomy.
	 */
	function init() {
		$taxonomy_args = apply_filters( 'P3_mentions_taxonomy_args', array(
			'show_ui'           => false,
			'show_in_nav_menus' => false,
			'rewrite'           => array( 'slug' => P3_MENTIONS_SLUG ),
		) );

		register_taxonomy( P3_MENTIONS_TAXONOMY, 'post', $taxonomy_args );
	}

	/**
	 * Generates array of users indexed by user ID, and
	 * an array of user_nicenames, indexed by user ID.
	 *
	 * @return array An array of user objects indexed by user ID.
	 */
	function load_users() {

		// Cache the user information.
		if ( ! empty( $this->users ) )
	 		return $this->users;

		$users = get_users();
		foreach ( $users as $user ) {
			$this->users[ $user->ID ] = $user;
			$this->names[ $user->ID ] = $user->user_nicename;
		}

		return $this->users;
	}

	function update_post_terms( $post_id, $post ) {
		return $this->find_mentions( $post->post_content );
	}

	function update_comment_terms( $comment_id, $comment ) {
		return $this->find_mentions( $comment->comment_content );
	}

	function find_mentions( $content ) {
		if ( ! preg_match_all( $this->mentions_regex, $content, $matches ) )
			return array();

		// Filters found mentions. Passes original found mentions and content as args.
		return apply_filters( 'P3_found_mentions', $matches[1], $matches[1], $content );
	}

	function filter_mentions( $mentions ) {
		$this->load_users();
		return array_intersect( $mentions, $this->names );
	}

	/**
	 * Parses and links mentions within a string.
	 * Run on the_content.
	 *
	 * @param string $content The content.
	 * @return string The linked content.
	 */
	function mention_links( $content ) {
		global $current_user;

		$names  = $this->find_mentions( $content );
		$names  = array_unique( $names );
		$slug   = P3_MENTIONS_SLUG;
		$search = is_search() ? substr( get_search_query( false ), 1 ) : '';

		foreach ( $names as $name ) {
			$classes = 'mention';
			// If we're searching for this name, highlight it.
			if ( $name === $search )
				$classes .= ' mention-highlight';

			if ( is_user_logged_in() && $name === $current_user->user_login )
				$classes .= ' mention-current-user';

			$url = get_term_link( $name, P3_MENTIONS_TAXONOMY );
			if ( is_wp_error( $url ) )
				continue;

			$replacement = "<a href='" . esc_url( $url ) . "' class='$classes'>@$name</a>";
			$replacement = apply_filters( 'P3_mention_link', $replacement, $name );
			$content     = preg_replace( "/@$name\b/i", $replacement, $content );
		}

		return $content;
	}

	/**
	 * Generates the user information for the mentions autocomplete feature.
	 *
	 * @return array User information.
	 */
	function user_suggestion() {

		// Membership check
		$user = wp_get_current_user();
		if ( ! is_super_admin() && function_exists( 'is_user_member_of_blog' ) && ! is_user_member_of_blog( $user->ID ) )
			return;

		// Capability check
		if ( ! current_user_can( 'edit_posts' ) )
			return;

		$this->load_users();

		$js_users = array();

		foreach( $this->users as $user ) {
			$js_users[] = array(
				'name'      => $user->display_name,
				'username'  => ( isset( $user->user_nicename ) ? $user->user_nicename : $user->display_name ),
				'gravatar'  => get_avatar( $user->user_email, 32 ),
			);
		}

		return apply_filters( 'P3_user_suggestion', $js_users );
	}
}

?>