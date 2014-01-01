<?php
/**
 * Single post template.
 *
 * @package P3
 */
?>
<?php get_header(); ?>
<div id="wrapper">
	<?php get_sidebar('main'); ?>
	
<div class="sleeve_main">

	<div id="main">

		<?php if ( have_posts() ) : ?>

			<?php while ( have_posts() ) : the_post(); ?>

				<div class="controls">
					<a href="#" id="togglecomments"><?php _e( 'Hide threads', 'P3' ); ?></a>
					<span class="sep">&nbsp;|&nbsp;</span>
					<a href="#directions" id="directions-keyboard"><?php  _e( 'Keyboard Shortcuts', 'P3' ); ?></a>
					<span class="single-action-links"><?php do_action( 'P3_action_links' ); ?></span>
				</div>

				<ul id="postlist">
					<?php
						if ( has_post_thumbnail() && !P3_is_iphone()) {
						    // the current post has a thumbnail
						    post_thumbnail_with_size();
						} else {
						    // the current post lacks a thumbnail
						}
					?>

		    		<?php P3_load_entry(); ?>
				</ul>

			<?php endwhile; ?>

		<?php else : ?>

			<ul id="postlist">
				<li class="no-posts">
			    	<h3><?php _e( 'No posts yet!', 'P3' ); ?></h3>
				</li>
			</ul>

		<?php endif; ?>

		<div class="navigation">
			<p class="nav-older"><?php previous_post_link( '%link', _x( '&larr; %title', 'Previous post link', 'P3' ) ); ?></p>
			<p class="nav-newer"><?php next_post_link( '%link', _x( '%title &rarr;', 'Next post link', 'P3' ) ); ?></p>
		</div>

	</div> <!-- main -->

</div> <!-- sleeve -->

<div class="clear"></div>
	<?php get_sidebar('footer'); ?>
</div> <!-- // wrapper -->
<?php get_footer(); ?>
