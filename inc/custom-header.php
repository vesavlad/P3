<?php
/**
 * Setup and callbacks for WordPress custom header feature.
 *
 * @package P3
 * @since P3 1.0.3
 */

/**
 * Setup the WordPress core custom header feature.
 *
 * Use add_theme_support to register support for WordPress 3.4+
 * as well as provide backward compatibility for previous versions.
 * Use feature detection of get_custom_header() which was introduced
 * in WordPress 3.4.
 *
 * @uses P3_header_style()
 * @uses P3_admin_header_style()
 *
 * @package P3
 * @since P3 1.0.3
 */
function P3_setup_custom_header() {
	add_theme_support( 'custom-header', apply_filters( 'P3_custom_header_args', array(
		'width'               => 2500,
		'height'              => 255,
		'default-image'       => '',
		'default-text-color'  => '3478e3',
		'wp-head-callback'    => 'P3_header_style',
		'admin-head-callback' => 'P3_admin_header_style',
	) ) );
}

/**
 * Styles for the Custom Header admin UI.
 *
 * @package P3
 * @since P3 1.1
 */
function P3_admin_header_style() {
?>
	<style type="text/css">
	#headimg {
		background: url('<?php echo esc_url( get_header_image() ); ?>') repeat;
		background-position: top center;
		padding: 0 0 0 10px;
	}
	#headimg a {
		width: <?php echo get_custom_header()->width; ?>px;
		height: <?php echo get_custom_header()->height; ?>px;
	}
	#headimg h1 {
		font-family: "HelveticaNeue-Light", "Helvetica Neue Light", "Helvetica Neue", Helvetica, Arial, sans-serif;
		font-weight: 200;
		margin: 0;
		padding-top: 20px;
		text-align: center;
	}
	#headimg h1 a {
		color: #<?php header_textcolor(); ?>;
		border-bottom: none;
		font-size: 40px;
		margin: -0.4em 0 0 0;
		text-decoration: none;

	}
	#headimg #desc {
		color: #<?php header_textcolor(); ?>;
		font-family: "HelveticaNeue-Light", "Helvetica Neue Light", "Helvetica Neue", Helvetica, Arial, sans-serif;
		font-size: 13px;
		font-weight: 400;
		margin-top: 1em;
		text-align: center;
	}

	<?php if ( 'blank' == get_header_textcolor() ) : ?>
	#headimg h1,
	#headimg #desc {
		display: none;
	}
	#headimg h1 a,
	#headimg #desc {
		color: #<?php echo get_header_textcolor(); ?>;
	}
	<?php endif; ?>

	</style>
<?php
}

/**
 * Styles to display custom header in template files.
 *
 * @package P3
 * @since P3 1.0.3
 */
function P3_header_style() {
?>
	<style id="P3-header-style" type="text/css">
	<?php if ( '' != get_header_image() ) : ?>
		#header {
			background: url('<?php echo esc_url( get_header_image() ); ?>') repeat-x center top;
			height: 215px;
			width: 100%;
			background-attachment: fixed;
			overflow: hidden;
		}
		#header a.secondary {
			display: block;
			position: absolute;
			top: 0;
			width: <?php echo get_custom_header()->width; ?>px;
			height: <?php echo get_custom_header()->height; ?>px;
		}
		#header a.secondary:hover {
			border: 0;
		}

		#header .sleeve {
			background-color: transparent;
			margin-top: 0;
			margin-right: 0;
			position: relative;
			height: <?php echo get_custom_header()->height; ?>px;
			-webkit-box-shadow: none !important;
			-moz-box-shadow: none !important;
			box-shadow: none !important;
		}
		#navigation{
			margin-top:0px;
		}
		
	<?php endif;

	$text_color = get_header_textcolor();
	if ( 'blank' == $text_color ) : ?>
		#header h1,
		#header small {
			padding: 0;
			text-indent: -1000em;
		}
		#header .image{
			display: none;
		}
	<?php elseif ( $text_color != get_theme_support( 'custom-header', 'default-text-color' ) ) : ?>
		#header h1 a,
		#header small {
			color: #<?php header_textcolor(); ?>;
		}
	<?php endif; ?>
	</style>
<?php
}