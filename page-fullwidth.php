<?php
/**
 * Static page template.
 * Template Name: Fullwidth Page
 * Description: Page with no sidebars
 * @package P3
 */
?>
<?php get_header(); ?>
<div id="wrapper">
	<div class="sleeve_main fullwidth">

		<div id="main">
			<h2><?php the_title(); ?></h2>

			<ul id="postlist">
			<?php if ( have_posts() ) : ?>

				<?php while ( have_posts() ) : the_post(); ?>
					<?php
						if ( has_post_thumbnail() ) {
						    // the current post has a thumbnail
						    if(is_front_page())
						    	post_thumbnail_with_size(996,255,"c"); //larger image for frontpage
						    else
						    	post_thumbnail_with_size(996,200,"c");
						}
					?>

					<?php P3_load_entry(); ?>
				<?php endwhile; ?>

			<?php endif; ?>
			</ul>

		</div> <!-- main -->

	</div> <!-- sleeve -->
</div> <!-- // wrapper -->
<?php get_footer(); ?>
