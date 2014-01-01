<?php
/**
 * @package P3
 */

require_once( get_template_directory() . '/inc/utils.php' );
require_once( get_template_directory() . '/inc/mr-image-resize.php');
P3_maybe_define( 'P3_INC_PATH', get_template_directory()     . '/inc' );
P3_maybe_define( 'P3_INC_URL',  get_template_directory_uri() . '/inc' );
P3_maybe_define( 'P3_JS_PATH',  get_template_directory()     . '/js'  );
P3_maybe_define( 'P3_JS_URL',   get_template_directory_uri() . '/js'  );

add_filter( 'ot_show_pages', '__return_false' );
add_filter( 'ot_show_new_layout', '__return_false' );
add_filter( 'ot_theme_mode', '__return_true' );
load_template( trailingslashit( get_template_directory() ) . 'option-tree/ot-loader.php' );
load_template( trailingslashit( get_template_directory() ) . 'inc/theme-options.php' );

class P3 {
	/**
	 * DB version.
	 *
	 * @var int
	 */
	var $db_version = 4;

	/**
	 * Options.
	 *
	 * @var array
	 */
	var $options = array();

	/**
	 * Option name in DB.
	 *
	 * @var string
	 */
	var $option_name = 'P3_manager';

	/**
	 * Components.
	 *
	 * @var array
	 */
	var $components = array();

	/**
	 * Includes and instantiates the various P3 components.
	 */
	function P3() {
		// Fetch options
		$this->options = get_option( $this->option_name );
		if ( false === $this->options )
			$this->options = array();

		// Include the P3 components
		$includes = array( 'compat', 'terms-in-comments', 'js-locale',
			'mentions', 'search', 'js', 'widgets/recent-tags', 'widgets/recent-comments', 'widgets/submenu',
			'list-creator' );

		require_once( P3_INC_PATH . "/template-tags.php" );

		// Logged-out/unprivileged users use the add_feed() + ::ajax_read() API rather than the /admin-ajax.php API
		// current_user_can( 'read' ) should be equivalent to is_user_member_of_blog() || is_super_admin()
		if ( defined( 'DOING_AJAX' ) && DOING_AJAX && ( P3_user_can_post() || current_user_can( 'read' ) ) )
			$includes[] = 'ajax';

		foreach ( $includes as $name ) {
			require_once( P3_INC_PATH . "/$name.php" );
		}

		// Add the default P3 components
		$this->add( 'mentions',             'P3_Mentions'             );
		$this->add( 'search',               'P3_Search'               );
		$this->add( 'post-list-creator',    'P3_Post_List_Creator'    );
		$this->add( 'comment-list-creator', 'P3_Comment_List_Creator' );

		// Bind actions
		add_action( 'init',       array( &$this, 'init'             ) );
		add_action( 'admin_init', array( &$this, 'maybe_upgrade_db' ), 5 );
	}

	function init() {
		// Load language pack
		load_theme_textdomain( 'P3', get_template_directory() . '/languages' );

		// Set up the AJAX read handler
		add_feed( 'P3.ajax', array( $this, 'ajax_read' ) );
	}

	function ajax_read() {
		if ( ! defined( 'DOING_AJAX' ) ) {
			define( 'DOING_AJAX', true );
		}

		require_once( P3_INC_PATH . '/ajax-read.php' );

		P3Ajax_Read::dispatch();
	}

	/**
	 * Will upgrade the database if necessary.
	 *
	 * When upgrading, triggers actions:
	 *    'P3_upgrade_db_version'
	 *    'P3_upgrade_db_version_$number'
	 *
	 * Flushes rewrite rules automatically on upgrade.
	 */
	function maybe_upgrade_db() {
		if ( ! isset( $this->options['db_version'] ) || $this->options['db_version'] < $this->db_version ) {
			$current_db_version = isset( $this->options['db_version'] ) ? $this->options['db_version'] : 0;

			do_action( 'P3_upgrade_db_version', $current_db_version );
			for ( ; $current_db_version <= $this->db_version; $current_db_version++ ) {
				do_action( "P3_upgrade_db_version_$current_db_version" );
			}

			// Flush rewrite rules once, so callbacks don't have to.
			flush_rewrite_rules();

			$this->set_option( 'db_version', $this->db_version );
			$this->save_options();
		}
	}

	/**
	 * COMPONENTS API
	 */
	function add( $component, $class ) {
		$class = apply_filters( "P3_add_component_$component", $class );
		if ( class_exists( $class ) )
			$this->components[ $component ] = new $class();
	}
	function get( $component ) {
		return $this->components[ $component ];
	}
	function remove( $component ) {
		unset( $this->components[ $component ] );
	}

	/**
	 * OPTIONS API
	 */
	function get_option( $key ) {
		return isset( $this->options[ $key ] ) ? $this->options[ $key ] : null;
	}
	function set_option( $key, $value ) {
		return $this->options[ $key ] = $value;
	}
	function save_options() {
		update_option( $this->option_name, $this->options );
	}
}

$GLOBALS['P3'] = new P3;

function P3_get( $component = '' ) {
	global $P3;
	return empty( $component ) ? $P3 : $P3->get( $component );
}
function P3_get_option( $key ) {
	return $GLOBALS['P3']->get_option( $key );
}
function P3_set_option( $key, $value ) {
	return $GLOBALS['P3']->set_option( $key, $value );
}
function P3_save_options() {
	return $GLOBALS['P3']->save_options();
}




/**
 * ----------------------------------------------------------------------------
 * NOTE: Ideally, the rest of this file should be moved elsewhere.
 * ----------------------------------------------------------------------------
 */

if ( ! isset( $content_width ) )
	$content_width = 732;

$themecolors = array(
	'bg'     => 'ffffff',
	'text'   => '555555',
	'link'   => '6b6b6b',
	'border' => 'f1f1f1',
	'url'    => 'd54e21',
);


/**
 * Setup P3 Theme.
 *
 * Hooks into the after_setup_theme action.
 *
 * @uses P3_get_supported_post_formats()
 */
function P3_setup() {
	require_once( get_template_directory() . '/inc/custom-header.php' );
	P3_setup_custom_header();

	if ( function_exists( 'add_theme_support' ) ) {
		add_theme_support( 'post-thumbnails' );
	}

	add_theme_support( 'automatic-feed-links' );
	add_theme_support( 'post-formats', P3_get_supported_post_formats( 'post-format' ) );

	add_theme_support( 'custom-background', apply_filters( 'P3_custom_background_args', array( 'default-color' => 'f1f1f1' ) ) );

	add_filter( 'the_content', 'make_clickable', 12 ); // Run later to avoid shortcode conflicts

	register_nav_menus( array(
		'primary' => __( 'Primary Menu', 'P3' ),
	) );

	if ( is_admin() && false === get_option( 'posttitles-hide' ) )
		add_option( 'posttitles-hide', 1 );
}
add_filter( 'after_setup_theme', 'P3_setup' );

function P3_register_main_sidebar() {
	register_sidebar( array(
		'name' => __( 'Main Sidebar', 'P3' ),
		'id' => 'p3_main_sidebar',
	) );
}
add_filter( 'widgets_init', 'P3_register_main_sidebar' );

function P3_register_footer_bar() {
	register_sidebar( array(
		'name' => __( 'Footer Bar', 'P3' ),
		'id' => 'p3_footer_bar',
	) );
}
add_filter( 'widgets_init', 'P3_register_footer_bar');

function P3_background_color() {
	$background_color = ot_get_option('background-color');

	if ( '' != $background_color ) :
	?>
	<style type="text/css">
		body {
			background-color: <?php echo esc_attr( $background_color ); ?>;
		}
	</style>
	<?php endif;
}
add_action( 'wp_head', 'P3_background_color' );

function P3_background_image() {
	$P3_background_image = ot_get_option('background-image');

	if ( 'none' == $P3_background_image || '' == $P3_background_image )
		return false;
	if ( 'disolve' == $P3_background_image): 
?>
	<style type="text/css">
		body {
			background-image: url( <?php echo get_template_directory_uri() . '/i/backgrounds/disolve.png' ?> );
			background-position: top center;
			background-repeat: repeat-x;
			background-attachment: fixed;
			background-color: #d8d8d8;
		}
	</style>
<?php
	else:
?>
	<style type="text/css">
		body {
			background-image: url( <?php echo get_template_directory_uri() . '/i/backgrounds/pattern-' . sanitize_key( ot_get_option('background-image') ) . '.png' ?> );
		}
	</style>
<?php
	endif;
}
add_action( 'wp_head', 'P3_background_image' );

/**
 * Add a custom class to the body tag for the background image theme option.
 *
 * This dynamic class is used to style the bundled background
 * images for retina screens. Note: The background images that
 * ship with P3 have been deprecated as of P3 1.5. For backwards
 * compatibility, P3 will still recognize them if the option was
 * set before upgrading.
 *
 * @since P3 1.5
 */
function P3_body_class_background_image( $classes ) {
	$image = ot_get_option('background-image');

	if ( empty( $image ) || 'none' == $image )
		return $classes;

	$classes[] = esc_attr( 'P3-background-image-' . $image );

	return $classes;
}
add_action( 'body_class', 'P3_body_class_background_image' );

// Content Filters
function P3_title( $before = '<h2>', $after = '</h2>', $echo = true ) {
	if ( is_page() )
		return;

	if ( is_single() && false === P3_the_title( '', '', false ) ) { ?>
		<h2 class="transparent-title"><?php the_title(); ?></h2><?php
		return true;
	} else {
		P3_the_title( $before, $after, $echo );
	}
}

/**
 * Generate a nicely formatted post title
 *
 * Ignore empty titles, titles that are auto-generated from the
 * first part of the post_content
 *
 * @package WordPress
 * @subpackage P3
 * @since 1.0.5
 *
 * @param    string    $before    content to prepend to title
 * @param    string    $after     content to append to title
 * @param    string    $echo      echo or return
 * @return   string    $out       nicely formatted title, will be boolean(false) if no title
 */
function P3_the_title( $before = '<h2>', $after = '</h2>', $echo = true ) {
	global $post;
	
	$temp = $post;
	$t = apply_filters( 'the_title', $temp->post_title );
	$title = $temp->post_title;
	$content = $temp->post_content;
	$pos = 0;
	$out = '';

	// Don't show post title if turned off in options or title is default text
	if ( 1 == (int) ot_get_option('posttitles-hide') || 'Post Title' == $title )
		return false;

	$content = trim( $content );
	$title = trim( $title );
	$title = preg_replace( '/\.\.\.$/', '', $title );
	$title = str_replace( "\n", ' ', $title );
	$title = str_replace( '  ', ' ', $title);
	$content = str_replace( "\n", ' ', strip_tags( $content) );
	$content = str_replace( '  ', ' ', $content );
	$content = trim( $content );
	$title = trim( $title );

	// Clean up links in the title
	if ( false !== strpos( $title, 'http' ) )  {
		$split = @str_split( $content, strpos( $content, 'http' ) );
		$content = $split[0];
		$split2 = @str_split( $title, strpos( $title, 'http' ) );
		$title = $split2[0];
	}

	// Avoid processing an empty title
	if ( '' == $title )
		return false;

	// Avoid processing the title if it's the very first part of the post content
	// Which is the case with most "status" posts
	$pos = strpos( $content, $title );
	if ( false === $pos || 0 < $pos ) {
		if ( is_single() )
			$out = $before . $t . $after;
		else
			$out = $before . '<a href="' . get_permalink( $temp->ID ) . '">' . $t . '&nbsp;</a>' . $after;

		if ( $echo )
			echo $out;
		else
			return $out;
	}

	return false;
}

function P3_comments( $comment, $args ) {
	$GLOBALS['comment'] = $comment;

	if ( !is_single() && get_comment_type() != 'comment' )
		return;

	$depth          = prologue_get_comment_depth( get_comment_ID() );
	$can_edit_post  = current_user_can( 'edit_post', $comment->comment_post_ID );

	$reply_link     = prologue_get_comment_reply_link(
		array( 'depth' => $depth, 'max_depth' => $args['max_depth'], 'before' => ' | ', 'reply_text' => __( 'Reply', 'P3' ) ),
		$comment->comment_ID, $comment->comment_post_ID );

	$content_class  = 'commentcontent';
	if ( $can_edit_post )
		$content_class .= ' comment-edit';

	?>
	<li id="comment-<?php comment_ID(); ?>" <?php comment_class(); ?>>
		<?php do_action( 'P3_comment' ); ?>

		<?php echo get_avatar( $comment, 32 ); ?>
		<h4>
			<?php echo get_comment_author_link(); ?>
			<span class="meta">
				<?php echo P3_date_time_with_microformat( 'comment' ); ?>
				<span class="actions">
					<a class="thepermalink" href="<?php echo esc_url( get_comment_link() ); ?>" title="<?php esc_attr_e( 'Permalink', 'P3' ); ?>"><?php _e( 'Permalink', 'P3' ); ?></a>
					<?php
					echo $reply_link;

					if ( $can_edit_post )
						edit_comment_link( __( 'Edit', 'P3' ), ' | ' );

					?>
				</span>
			</span>
		</h4>
		<div id="commentcontent-<?php comment_ID(); ?>" class="<?php echo esc_attr( $content_class ); ?>"><?php
				echo apply_filters( 'comment_text', $comment->comment_content, $comment );

				if ( $comment->comment_approved == '0' ): ?>
					<p><em><?php esc_html_e( 'Your comment is awaiting moderation.', 'P3' ); ?></em></p>
				<?php endif; ?>
		</div>
	<?php
}

function get_tags_with_count( $post, $format = 'list', $before = '', $sep = '', $after = '' ) {
	$posttags = get_the_tags($post->ID, 'post_tag' );

	if ( !$posttags )
		return '';

	foreach ( $posttags as $tag ) {
		if ( $tag->count > 1 && !is_tag($tag->slug) ) {
			$tag_link = '<a href="' . get_tag_link( $tag ) . '" rel="tag">' . $tag->name . ' ( ' . number_format_i18n( $tag->count ) . ' )</a>';
		} else {
			$tag_link = $tag->name;
		}

		if ( $format == 'list' )
			$tag_link = '<li>' . $tag_link . '</li>';

		$tag_links[] = $tag_link;
	}

	return apply_filters( 'tags_with_count', $before . join( $sep, $tag_links ) . $after, $post );
}

function tags_with_count( $format = 'list', $before = '', $sep = '', $after = '' ) {
	global $post;
	echo get_tags_with_count( $post, $format, $before, $sep, $after );
}

function P3_title_from_content( $content ) {
	$title = P3_excerpted_title( $content, 8 ); // limit title to 8 full words

	// Try to detect image or video only posts, and set post title accordingly
	if ( empty( $title ) ) {
		if ( preg_match("/<object|<embed/", $content ) )
			$title = __( 'Video Post', 'P3' );
		elseif ( preg_match( "/<img/", $content ) )
			$title = __( 'Image Post', 'P3' );
	}

	return $title;
}

function P3_excerpted_title( $content, $word_count ) {
	$content = strip_tags( $content );
	$words = preg_split( '/([\s_;?!\/\(\)\[\]{}<>\r\n\t"]|\.$|(?<=\D)[:,.\-]|[:,.\-](?=\D))/', $content, $word_count + 1, PREG_SPLIT_NO_EMPTY );

	if ( count( $words ) > $word_count ) {
		array_pop( $words ); // remove remainder of words
		$content = implode( ' ', $words );
		$content = $content . '...';
	} else {
		$content = implode( ' ', $words );
	}

	$content = trim( strip_tags( $content ) );

	return $content;
}

function P3_add_reply_title_attribute( $link ) {
	return str_replace( "rel='nofollow'", "rel='nofollow' title='" . __( 'Reply', 'P3' ) . "'", $link );
}
add_filter( 'post_comments_link', 'P3_add_reply_title_attribute' );

function P3_fix_empty_titles( $data, $postarr ) {
	if ( 'post' != $data['post_type'] )
		return $data;

	if ( ! empty( $postarr['post_title'] ) )
		return $data;

	$data['post_title'] = P3_title_from_content( $data['post_content'] );

	return $data;
}
add_filter( 'wp_insert_post_data', 'P3_fix_empty_titles', 10, 2 );

function P3_add_head_content() {
	if ( is_home() && is_user_logged_in() ) {
		include_once( ABSPATH . '/wp-admin/includes/media.php' );
	}
}
add_action( 'wp_head', 'P3_add_head_content' );

function P3_new_post_noajax() {
	if ( empty( $_POST['action'] ) || $_POST['action'] != 'post' )
	    return;

	if ( !is_user_logged_in() )
		auth_redirect();

	if ( !current_user_can( 'publish_posts' ) ) {
		wp_redirect( home_url( '/' ) );
		exit;
	}

	$current_user = wp_get_current_user();

	check_admin_referer( 'new-post' );

	$user_id        = $current_user->ID;
	$post_content   = sanitize_text_field($_POST['posttext']);
	$tags           = sanitize_text_field($_POST['tags']);

	$post_title = P3_title_from_content( $post_content );

	$post_id = wp_insert_post( array(
		'post_author'   => $user_id,
		'post_title'    => $post_title,
		'post_content'  => $post_content,
		'tags_input'    => $tags,
		'post_status'   => 'publish'
	) );

	$post_format = 'status';
	if ( in_array( $_POST['post_format'], P3_get_supported_post_formats() ) )
		$post_format = sanitize_text_field($_POST['post_format']);

	set_post_format( $post_id, $post_format );

	wp_redirect( home_url( '/' ) );

	exit;
}
add_filter( 'template_redirect', 'P3_new_post_noajax' );

/**
 * iPhone viewport meta tag.
 *
 * Hooks into the wp_head action late.
 *
 * @uses P3_is_iphone()
 * @since P3 1.4
 */
function P3_viewport_meta_tag() {
	if ( P3_is_iphone() )
		echo '<meta name="viewport" content="initial-scale = 1.0, maximum-scale = 1.0, user-scalable = no"/>';
}
add_action( 'wp_head', 'P3_viewport_meta_tag', 1000 );

/**
 * iPhone Stylesheet.
 *
 * Hooks into the wp_enqueue_scripts action late.
 *
 * @uses P3_is_iphone()
 * @since P3 1.4
 */
function P3_iphone_style() {
	if ( P3_is_iphone() ) {
		wp_enqueue_style(
			'P3-iphone-style',
			get_template_directory_uri() . '/style-iphone.css',
			array(),
			'20120402'
		);
	}
}
add_action( 'wp_enqueue_scripts', 'P3_iphone_style', 1000 );

/**
 * Print Stylesheet.
 *
 * Hooks into the wp_enqueue_scripts action.
 *
 * @since P3 1.5
 */
function P3_print_style() {
	wp_enqueue_style( 'P3', get_stylesheet_uri() );
	wp_enqueue_style( 'P3-print-style', get_template_directory_uri() . '/style-print.css', array( 'P3' ), '20120807', 'print' );

	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) )
		wp_enqueue_script( 'comment-reply' );
}
add_action( 'wp_enqueue_scripts', 'P3_print_style' );

/*
	Modified to replace query string with blog url in output string
*/
function prologue_get_comment_reply_link( $args = array(), $comment = null, $post = null ) {
	global $user_ID;

	if ( post_password_required() )
		return;

	$defaults = array( 'add_below' => 'commentcontent', 'respond_id' => 'respond', 'reply_text' => __( 'Reply', 'P3' ),
		'login_text' => __( 'Log in to Reply', 'P3' ), 'depth' => 0, 'before' => '', 'after' => '' );

	$args = wp_parse_args($args, $defaults);
	if ( 0 == $args['depth'] || $args['max_depth'] <= $args['depth'] )
		return;

	extract($args, EXTR_SKIP);

	$comment = get_comment($comment);
	$post = get_post($post);

	if ( 'open' != $post->comment_status )
		return false;

	$link = '';

	$reply_text = esc_html( $reply_text );

	if ( get_option( 'comment_registration' ) && !$user_ID )
		$link = '<a rel="nofollow" href="' . site_url( 'wp-login.php?redirect_to=' . urlencode( get_permalink() ) ) . '">' . esc_html( $login_text ) . '</a>';
	else
		$link = "<a rel='nofollow' class='comment-reply-link' href='". get_permalink($post). "#" . urlencode( $respond_id ) . "' title='". __( 'Reply', 'P3' )."' onclick='return addComment.moveForm(\"" . esc_js( "$add_below-$comment->comment_ID" ) . "\", \"$comment->comment_ID\", \"" . esc_js( $respond_id ) . "\", \"$post->ID\")'>$reply_text</a>";
	return apply_filters( 'comment_reply_link', $before . $link . $after, $args, $comment, $post);
}

function prologue_comment_depth_loop( $comment_id, $depth )  {
	$comment = get_comment( $comment_id );

	if ( isset( $comment->comment_parent ) && 0 != $comment->comment_parent ) {
		return prologue_comment_depth_loop( $comment->comment_parent, $depth + 1 );
	}
	return $depth;
}

function prologue_get_comment_depth( $comment_id ) {
	return prologue_comment_depth_loop( $comment_id, 1 );
}

function prologue_comment_depth( $comment_id ) {
	echo prologue_get_comment_depth( $comment_id );
}

function prologue_poweredby_link() {
	return apply_filters( 'prologue_poweredby_link', sprintf( '<a href="%1$s" rel="generator">%2$s</a>', esc_url( __('http://wordpress.org/', 'P3') ), sprintf( __('Proudly powered by %s.', 'P3'), 'WordPress' ) ) );
}


function post_thumbnail_with_size($width=734, $height=150, $alignment = "c")
{
	$large_image_url = wp_get_attachment_image_src( get_post_thumbnail_id(), 'large');
	$thumbnail = mr_image_resize($large_image_url[0], $width, $height, true, $alignment, false);
	echo "<div id='post-image'><img src='".$thumbnail."' alt=''/></div>";
}

function P3_hidden_main_sidebar_css() {
	$hide_sidebar = P3_get_hide_main_sidebar();
		$sleeve_margin = ( is_rtl() ) ? 'margin-left: 0;' : 'margin-right: 0;';
	if ( '' != $hide_sidebar ) :
	?>
	<style type="text/css">
		.sleeve_main { <?php echo $sleeve_margin;?> }
		#wrapper { background: transparent; }
		#header, #footer, #navigation, #wrapper { width: 760px; }
	</style>
	<?php endif;
}
add_action( 'wp_head', 'P3_hidden_main_sidebar_css' );

// Network signup form
function P3_before_signup_form() {
	echo '<div class="sleeve_main"><div id="main">';
}
add_action( 'before_signup_form', 'P3_before_signup_form' );

function P3_after_signup_form() {
	echo '</div></div>';
}
add_action( 'after_signup_form', 'P3_after_signup_form' );


/*  Custom favicon
/* ------------------------------------ */
function P3_favicon() {
	if ( ot_get_option('favicon') ) {
		echo '<link rel="shortcut icon" href="'.ot_get_option('favicon').'" />'."\n";
	}
}
add_filter( 'wp_head', 'P3_favicon' );

/**
 * Returns accepted post formats.
 *
 * The value should be a valid post format registered for P3, or one of the back compat categories.
 * post formats: link, quote, standard, status
 * categories: link, post, quote, status
 *
 * @since P3 1.3.4
 *
 * @param string type Which data to return (all|category|post-format)
 * @return array
 */
function P3_get_supported_post_formats( $type = 'all' ) {
	$post_formats = array( 'link', 'quote', 'status' );

	switch ( $type ) {
		case 'post-format':
			break;
		case 'category':
			$post_formats[] = 'post';
			break;
		case 'all':
		default:
			array_push( $post_formats, 'post', 'standard' );
			break;
	}

	return apply_filters( 'P3_get_supported_post_formats', $post_formats );
}

/**
 * Is site being viewed on an iPhone or iPod Touch?
 *
 * For testing you can modify the output with a filter:
 * add_filter( 'P3_is_iphone', '__return_true' );
 *
 * @return bool
 * @since P3 1.4
 */
function P3_is_iphone() {
	$output = false;

	if ( ( strstr( $_SERVER['HTTP_USER_AGENT'], 'iPhone' ) && ! strstr( $_SERVER['HTTP_USER_AGENT'], 'iPad' ) ) || isset( $_GET['iphone'] ) && $_GET['iphone'] )
		$output = true;

	$output = (bool) apply_filters( 'P3_is_iphone', $output );

	return $output;
}

/**
 * Filters wp_title to print a neat <title> tag based on what is being viewed.
 *
 * @since P3 1.5
 */
function P3_wp_title( $title, $sep ) {
	global $page, $paged;

	if ( is_feed() )
		return $title;

	// Add the blog name
	$title .= get_bloginfo( 'name' );

	// Add the blog description for the home/front page.
	$site_description = get_bloginfo( 'description', 'display' );
	if ( $site_description && ( is_home() || is_front_page() ) )
		$title .= " $sep $site_description";

	// Add a page number if necessary:
	if ( $paged >= 2 || $page >= 2 )
		$title .= " $sep " . sprintf( __( 'Page %s', 'P3' ), max( $paged, $page ) );

	return $title;
}
add_filter( 'wp_title', 'P3_wp_title', 10, 2 );



