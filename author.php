<?php
/**
 * Author template.
 *
 * @package P3
 */
?>
<?php get_header(); ?>
<div id="wrapper">
	<?php get_sidebar('main'); ?>

<div class="sleeve_main">
	<?php if ( P3_user_can_post() && !is_archive() ) : ?>
		<?php locate_template( array( 'post-form.php' ), true ); ?>
	<?php endif; ?>
	<div id="main">

		<?php if ( have_posts() ) : ?>

		<h2>
			<?php printf( _x( 'Updates from %s', 'Author name', 'P3' ), P3_get_archive_author() ); ?>

			<span class="controls">
				<a href="#" id="togglecomments"> <?php _e( 'Toggle Comment Threads', 'P3' ); ?></a> | <a href="#directions" id="directions-keyboard"><?php _e( 'Keyboard Shortcuts', 'P3' ); ?></a>
			</span>
		</h2>

		<ul id="postlist">
			<?php while ( have_posts() ) : the_post(); ?>
	    		<?php P3_load_entry(); ?>
			<?php endwhile; ?>
		</ul>

		<?php else : ?>

		<h2><?php _e( 'Not Found', 'P3' ); ?></h2>
		<p><?php _e( 'Apologies, looks like this author does not have any posts.', 'P3' ); ?></p>

		<?php endif; // end have_posts() ?>

		<div class="navigation">
			<p class="nav-older"><?php next_posts_link( __( '&larr; Older posts', 'P3' ) ); ?></p>
			<p class="nav-newer"><?php previous_posts_link( __( 'Newer posts &rarr;', 'P3' ) ); ?></p>
		</div>

	</div> <!-- main -->

</div> <!-- sleeve -->

<div class="clear"></div>
	<?php get_sidebar('footer'); ?>
</div> <!-- // wrapper -->
<?php get_footer(); ?>