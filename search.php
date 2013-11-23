<?php
/**
 * Search result template.
 *
 * @package P3
 */
?>
<?php get_header(); ?>
<div id="wrapper">
	<?php get_sidebar('main'); ?>

<div class="sleeve_main">

	<div id="main">
		<h2>
			<?php printf( __( 'Search Results for: %s', 'P3' ), get_search_query() ); ?>
			<span class="controls">
				<a href="#" id="togglecomments"> <?php _e( 'Toggle Comment Threads', 'P3' ); ?></a> | <a href="#directions" id="directions-keyboard"><?php _e( 'Keyboard Shortcuts', 'P3' ); ?></a>
			</span>
		</h2>

		<?php if ( have_posts() ) : ?>

			<ul id="postlist">
			<?php while ( have_posts() ) : the_post(); ?>
				<?php P3_load_entry(); ?>
			<?php endwhile; ?>
			</ul>

		<?php else : ?>

			<div class="no-posts">
			    <h3><?php _e( 'No posts found!', 'P3' ); ?></h3>
				<p><?php _e( 'Apologies, but the page you requested could not be found. Perhaps searching will help.', 'P3' ); ?></p>
				<?php get_search_form(); ?>
			</div>

		<?php endif ?>

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
