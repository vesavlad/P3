<?php
/**
 * Script handler.
 *
 * @package P3
 * @since P3 1.1
 */
class P3_JS {

	static function init() {
		add_action( 'wp_enqueue_scripts', array( 'P3_JS', 'enqueue_scripts' ) );
		add_action( 'wp_enqueue_scripts', array( 'P3_JS', 'enqueue_styles' ) );
		add_action( 'wp_head', array( 'P3_JS', 'print_options' ), 1 );

		/**
		 * Register scripts
		 */
		wp_register_script(
			'jeditable',
			P3_JS_URL . '/jquery.jeditable.js',
			array( 'jquery' ),
			'1.6.2-rc2' );

		wp_register_script(
			'caret',
			P3_JS_URL . '/caret.js',
			array('jquery'),
			'20101025' );

		wp_register_script(
			'jquery-ui-autocomplete-html',
			P3_JS_URL . '/jquery.ui.autocomplete.html.js',
			array( 'jquery-ui-autocomplete' ),
			'20101025' );

		wp_register_script(
			'jquery-ui-autocomplete-multiValue',
			P3_JS_URL . '/jquery.ui.autocomplete.multiValue.js',
			array( 'jquery-ui-autocomplete' ),
			'20110405' );

		wp_register_script(
			'jquery-ui-autocomplete-match',
			P3_JS_URL . '/jquery.ui.autocomplete.match.js',
			array( 'jquery-ui-autocomplete', 'caret' ),
			'20110405' );

		/**
		 * Bundle containing scripts included when the user is logged in.
		 * Includes, in order:
		 *     jeditable, caret, jquery-ui-autocomplete,
		 *     jquery-ui-autocomplete-html, jquery-ui-autocomplete-multiValue,
		 *     jquery-ui-autocomplete-match
		 *
		 * Build the bundle with the bin/bundle-user-js shell script.
		 *
		 * @TODO: Improve bundle building/dependency process.
		 */
		wp_register_script(
			'P3-user-bundle',
			P3_JS_URL . '/P3.user.bundle.js',
			array( 'jquery-ui-core', 'jquery-ui-widget', 'jquery-ui-position' ),
			'20130819' );

		wp_register_script(
			'scrollit',
			P3_JS_URL .'/jquery.scrollTo-min.js',
			array( 'jquery' ),
			'20120402' );

		wp_register_script(
			'wp-locale',
			P3_JS_URL . '/wp-locale.js',
			array(),
			'20130819' );

		// Media upload script registered based on info in script-loader.
		wp_register_script(
			'media-upload',
			'/wp-admin/js/media-upload.js',
			array( 'thickbox' ),
			'20110113' );

		wp_register_script(
			'P3-spin',
			P3_JS_URL .'/spin.js',
			array( 'jquery' ),
			'20120704'
		);
	}

	static function enqueue_styles() {
		if ( is_home() && is_user_logged_in() )
			wp_enqueue_style( 'thickbox' );

		if ( is_user_logged_in() ) {
			wp_enqueue_style( 'jquery-ui-autocomplete', P3_JS_URL . '/jquery.ui.autocomplete.css', array(), '1.8.11' );
		}
	}

	static function enqueue_scripts() {
		global $wp_locale;

		// Generate dependencies for P3
		$depends = array( 'jquery', 'utils', 'jquery-color', 'comment-reply',
			'scrollit', 'wp-locale', 'P3-spin');

		if ( is_user_logged_in() ) {
			$depends[] = 'jeditable';
			$depends[] = 'jquery-ui-autocomplete-html';
			$depends[] = 'jquery-ui-autocomplete-multiValue';
			$depends[] = 'jquery-ui-autocomplete-match';

			// media upload
			if ( is_home() ) {
				$depends[] = 'media-upload';
			}
		}

		// Enqueue P3 JS
		wp_enqueue_script( 'P3js',
			P3_JS_URL . '/p3.js',
			$depends,
			'20140102'
		);

		wp_localize_script( 'P3js', 'P3txt', array(
			'tags'                  => '<br />' . __( 'Tags:' , 'P3' ),
			'tagit'                 => __( 'Tag it', 'P3' ),
			'citation'              => __( 'Citation', 'P3' ),
			'title'                 => __( 'Post Title', 'P3' ),
			'goto_homepage'         => __( 'Go to homepage', 'P3' ),
			// the number is calculated in the javascript in a complex way, so we can't use ngettext
			'n_new_updates'         => __( '%d new update(s)', 'P3' ),
			'n_new_comments'        => __( '%d new comment(s)', 'P3' ),
			'jump_to_top'           => __( 'Jump to top', 'P3' ),
			'not_posted_error'      => __( 'An error has occurred, your post was not posted', 'P3' ),
			'update_posted'         => __( 'Your update has been posted', 'P3' ),
			'loading'               => __( 'Loading...', 'P3' ),
			'cancel'                => __( 'Cancel', 'P3' ),
			'save'                  => __( 'Save', 'P3' ),
			'hide_threads'          => __( 'Hide threads', 'P3' ),
			'show_threads'          => __( 'Show threads', 'P3' ),
			'unsaved_changes'       => __( 'Your comments or posts will be lost if you continue.', 'P3' ),
			'date_time_format'      => __( '%1$s <em>on</em> %2$s', 'P3' ),
			'date_format'           => get_option( 'date_format' ),
			'time_format'           => get_option( 'time_format' ),
			// if we don't convert the entities to characters, we can't get < and > inside
			'l10n_print_after'      => 'try{convertEntities(P3txt);}catch(e){};',
			'autocomplete_prompt'   => __( 'After typing @, type a name or username to find a member of this site', 'P3' ),
			'no_matches'            => __( 'No matches.', 'P3' ),
			'comment_cancel_ays'    => __( 'Are you sure you would like to clear this comment? Its contents will be deleted.', 'P3' ),
			'oops_not_logged_in'    => __( 'Oops! Looks like you are not logged in.', 'P3' ),
			'please_log_in'         => __( 'Please log in again', 'P3' ),
			'whoops_maybe_offline'  => __( 'Whoops! Looks like you are not connected to the server. P3 could not connect with WordPress.', 'P3' ),
			'required_filed'        => __( 'This field is required.', 'P3' ),
		) );

		if ( P3_is_iphone() ) {
			wp_enqueue_script(
				'iphone',
				get_template_directory_uri() . '/js/iphone.js',
				array( 'jquery' ),
				'20120402',
				true
			);
		}

		add_action( 'wp_head', array( 'P3_JS', 'locale_script_data' ), 2 );
	}

	static function locale_script_data() {
		global $wp_locale;
		?>
		<script type="text/javascript">
		//<![CDATA[
		var wpLocale = <?php echo get_js_locale( $wp_locale ); ?>;
		//]]>
		</script>
		<?php
	}

	static function ajax_url() {
		global $current_blog;

		// Generate the ajax url based on the current scheme
		$admin_url = admin_url( 'admin-ajax.php?P3ajax=true', is_ssl() ? 'https' : 'http' );
		// If present, take domain mapping into account
		if ( isset( $current_blog->primary_redirect ) )
			$admin_url = preg_replace( '|https?://' . preg_quote( $current_blog->domain ) . '|', 'http://' . $current_blog->primary_redirect, $admin_url );
		return $admin_url;
	}

	static function ajax_read_url() {
		return add_query_arg( 'P3ajax', 'true', get_feed_link( 'P3.ajax' ) );
	}

	static function print_options() {
		$mentions = P3_get( 'mentions' );

		get_currentuserinfo();
		$page_options['nonce']= wp_create_nonce( 'ajaxnonce' );
		$page_options['prologue_updates'] = 1;
		$page_options['prologue_comments_updates'] = 1;
		$page_options['prologue_tagsuggest'] = 1;
		$page_options['prologue_inlineedit'] = 1;
		$page_options['prologue_comments_inlineedit'] = 1;
		$page_options['is_single'] = (int)is_single();
		$page_options['is_page'] = (int)is_page();
		$page_options['is_front_page'] = (int)is_front_page();
		$page_options['is_first_front_page'] = (int)(is_front_page() && !is_paged() );
		$page_options['is_user_logged_in'] = (int)is_user_logged_in();
		$page_options['login_url'] = wp_login_url( ( ( !empty($_SERVER['HTTPS'] ) && strtolower($_SERVER['HTTPS']) == 'on' ) ? 'https://' : 'http://' ) . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] );
?>
		<script type="text/javascript">
			// <![CDATA[

			// P3 Configuration
			var ajaxUrl                 = "<?php echo esc_js( esc_url_raw( P3_JS::ajax_url() ) ); ?>";
			var ajaxReadUrl             = "<?php echo esc_js( esc_url_raw( P3_JS::ajax_read_url() ) ); ?>";
			var updateRate              = "30000"; // 30 seconds
			var nonce                   = "<?php echo esc_js( $page_options['nonce'] ); ?>";
			var login_url               = "<?php echo $page_options['login_url'] ?>";
			var templateDir             = "<?php echo esc_js( get_template_directory_uri() ); ?>";
			var isFirstFrontPage        = <?php echo $page_options['is_first_front_page'] ?>;
			var isFrontPage             = <?php echo $page_options['is_front_page'] ?>;
			var isSingle                = <?php echo $page_options['is_single'] ?>;
			var isPage                  = <?php echo $page_options['is_page'] ?>;
			var isUserLoggedIn          = <?php echo $page_options['is_user_logged_in'] ?>;
			var prologueTagsuggest      = <?php echo $page_options['prologue_tagsuggest'] ?>;
			var prologuePostsUpdates    = <?php echo $page_options['prologue_updates'] ?>;
			var prologueCommentsUpdates = <?php echo $page_options['prologue_comments_updates']; ?>;
			var getPostsUpdate          = 0;
			var getCommentsUpdate       = 0;
			var inlineEditPosts         = <?php echo $page_options['prologue_inlineedit'] ?>;
			var inlineEditComments      = <?php echo $page_options['prologue_comments_inlineedit'] ?>;
			var wpUrl                   = "<?php echo esc_js( site_url() ); ?>";
			var rssUrl                  = "<?php esc_js( get_bloginfo( 'rss_url' ) ); ?>";
			var pageLoadTime            = "<?php echo gmdate( 'Y-m-d H:i:s' ); ?>";
			//var original_title          = document.title;
			var commentsOnPost          = new Array;
			var postsOnPage             = new Array;
			var postsOnPageQS           = '';
			var currPost                = -1;
			var currComment             = -1;
			var commentLoop             = false;
			var lcwidget                = false;
			var hidecomments            = false;
			var commentsLists           = '';
			var newUnseenUpdates        = 0;
			var mentionData             = <?php echo json_encode( $mentions->user_suggestion() ); ?>;
			var P3CurrentVersion        = <?php echo (int) $GLOBALS['P3']->db_version; ?>;
			var P3StoredVersion         = <?php echo (int) $GLOBALS['P3']->get_option( 'db_version' ); ?>;
			// ]]>
		</script>
<?php }
}
add_action( 'init', array( 'P3_JS', 'init' ) );

function P3_toggle_threads() {
	$hide_threads = ot_get_option('hidethreads'); ?>

	<script type="text/javascript">
	/* <![CDATA[ */
		jQuery( document ).ready( function( $ ) {
			function hideComments() {
				$('.commentlist').hide();
				$('.discussion').show();
			}
			function showComments() {
				$('.commentlist').show();
				$('.discussion').hide();
			}
			<?php if ( (int) $hide_threads && ! is_singular()) : ?>
				hideComments();
			<?php endif; ?>

			$( "#togglecomments" ).click( function() {
				if ( $( '.commentlist' ).css( 'display' ) == 'none' ) {
					showComments();
				} else {
					hideComments();
				}
				return false;
			});
		});
	/* ]]> */
	</script><?php
}
add_action( 'wp_footer', 'P3_toggle_threads' );
