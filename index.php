<?php
/**
 * Main template file.
 *
 * This is the most generic template file in a WordPress theme
 * and one of the two required files for a theme (the other being style.css).
 * It is used to display a page when nothing more specific matches a query.
 * E.g., it puts together the home page when no home.php file exists.
 * Learn more: http://codex.wordpress.org/Template_Hierarchy
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
		<h2>
			<?php if ( is_home() or is_front_page() ) : ?>

				<?php _e( 'Recent Updates', 'P3' ); ?> <?php if ( P3_get_page_number() > 1 ) printf( __( 'Page %s', 'P3' ), P3_get_page_number() ); ?>

			<?php else : ?>

				<?php printf( _x( 'Updates from %s', 'Month name', 'P3' ), get_the_time( 'F, Y' ) ); ?>

			<?php endif; ?>

			<span class="controls">
				<a href="#" id="togglecomments"> <?php _e( 'Toggle Comment Threads', 'P3' ); ?></a> | <a href="#directions" id="directions-keyboard"><?php _e( 'Keyboard Shortcuts', 'P3' ); ?></a>
			</span>
		</h2>

		<ul id="postlist">
		<?php if ( have_posts() ) : ?>

			<?php while ( have_posts() ) : the_post(); ?>
				<?php
					if ( has_post_thumbnail() ) {
					    // the current post has a thumbnail
					    post_thumbnail_with_size();
					} else {
					    // the current post lacks a thumbnail
					}
				?>
	    		<?php P3_load_entry(); ?>
			<?php endwhile; ?>

		<?php else : ?>

			<li class="no-posts">
		    	<h3><?php _e( 'No posts yet!', 'P3' ); ?></h3>
			</li>

		<?php endif; ?>
		</ul>

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
