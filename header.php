<?php
/**
 * Header template.
 *
 * @package P3
 */
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" <?php language_attributes(); ?>>
<head profile="http://gmpg.org/xfn/11">
<meta http-equiv="Content-Type" content="<?php bloginfo( 'html_type' ); ?>; charset=<?php bloginfo( 'charset' ); ?>" />
<title><?php wp_title( '|', true, 'right' ); ?></title>
<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>" />
<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>

<div id="header">
<?php do_action( 'before' ); ?>
	<div class="image">
		<?php $P3Options = get_option('P3Options');?>
		<?php if($P3Options['P3_header_user_image']!=""):?>
			<img src="<?php echo esc_url($P3Options['P3_header_user_image']) ?>" alt="<?php bloginfo( 'name' ); ?>" height="120px" />
		<?php else: ?>
			<img src="<?php bloginfo('template_directory');?>/i/logo.jpg" alt="<?php bloginfo( 'name' ); ?>" height="120px" />
		<?php endif;?>
	</div>
	<div class="sleeve">
		<h1><a href="<?php echo home_url( '/' ); ?>"><?php bloginfo( 'name' ); ?></a></h1>
		<?php if ( get_bloginfo( 'description' ) ) : ?>
			<small><?php bloginfo( 'description' ); ?></small>
		<?php endif; ?>
		<a class="secondary" href="<?php echo home_url( '/' ); ?>"></a>

		<?php if ( current_user_can( 'publish_posts' ) ) : ?>
			<a href="" id="mobile-post-button" style="display: none;"><?php _e( 'Post', 'P3' ) ?></a>
		<?php endif; ?>
	</div>
</div>

<?php if ( has_nav_menu( 'primary' ) ) : ?>
<div id="navigation" role="navigation" class="site-navigation main-navigation">
	<h1 class="assistive-text"><?php _e( 'Menu', 'P3' ); ?></h1>
	<div class="assistive-text skip-link"><a href="#main" title="<?php esc_attr_e( 'Skip to content', 'P3' ); ?>"><?php _e( 'Skip to content', 'P3' ); ?></a></div>
	<?php wp_nav_menu( array(
		'theme_location' => 'primary',
		'fallback_cb'    => '__return_false',
	) ); ?>
</div>
<?php endif; ?>

<div id="wrapper">

	<?php get_sidebar('main'); ?>