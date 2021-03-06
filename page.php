<?php
/**
 * Static page template.
 *
 * @package P3
 */
?>
<?php get_header(); ?>
<div id="wrapper">
	<?php get_sidebar('main'); ?>

<div class="sleeve_main">

	<div id="main">
		<h2><?php the_title(); ?></h2>

		<ul id="postlist">
		<?php if ( have_posts() ) : ?>

			<?php while ( have_posts() ) : the_post(); ?>
				<?php
					if ( has_post_thumbnail() && !P3_is_iphone() ) {
					    // the current post has a thumbnail
					    post_thumbnail_with_size(774, 250);
					} else {
					    // the current post lacks a thumbnail
					}
				?>
				<?php P3_load_entry(); ?>
			<?php endwhile; ?>

		<?php endif; ?>
		</ul>

	</div> <!-- main -->

</div> <!-- sleeve -->

<div class="clear"></div>
	<?php get_sidebar('footer'); ?>
</div> <!-- // wrapper -->
<?php get_footer(); ?>
