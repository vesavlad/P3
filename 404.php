<?php
/**
 * 404 Post not found template.
 *
 * @package P3
 */
?>
<?php get_header(); ?>
<div id="wrapper">
	<?php get_sidebar('main'); ?>
	<div class="sleeve_main">

		<div id="main">

			<h2><?php _e( 'Not Found', 'P3' ); ?></h2>
			<p><?php _e( 'Apologies, but the page you requested could not be found. Perhaps searching will help.', 'P3' ); ?></p>
			<?php get_search_form(); ?>

		</div> <!-- main -->

	</div> <!-- sleeve -->
	<div class="clear"></div>
	<?php get_sidebar('footer'); ?>
</div> <!-- // wrapper -->
<?php get_footer(); ?>