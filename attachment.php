<?php
/**
 * Displays a single attachment.
 *
 * @package P3
 */
?>
<?php get_header(); ?>

<div id="postpage">

	<div class="sleeve_main">

		<div id="main">

			<?php if ( have_posts() ) : ?>

				<?php while ( have_posts( ) ) : the_post(); ?>

					<div <?php post_class( 'post' ); ?> id="post-<?php the_ID( ); ?>">

						<h2><a href="<?php echo get_permalink( $post->post_parent ); ?>" rev="attachment"><?php echo get_the_title( $post->post_parent ); ?></a> &raquo; <?php the_title(); ?></h2>

						<div class="entry">

							<?php if ( wp_attachment_is_image() ) :

								$attachments = array_values( get_children( array( 'post_parent' => $post->post_parent, 'post_status' => 'inherit', 'post_type' => 'attachment', 'post_mime_type' => 'image', 'order' => 'ASC', 'orderby' => 'menu_order ID' ) ) );
								foreach ( $attachments as $k => $attachment ) {
									if ( $attachment->ID == $post->ID )
										break;
								}
								$k++;
								// If there is more than 1 image attachment in a gallery
								if ( count( $attachments ) > 1 ) {
									if ( isset( $attachments[ $k ] ) )
										$next_attachment_url = get_attachment_link( $attachments[ $k ]->ID );
									else
										$next_attachment_url = get_attachment_link( $attachments[ 0 ]->ID );
								} else {
									$next_attachment_url = wp_get_attachment_url();
								}

								$metadata = wp_get_attachment_metadata();
								printf( __( 'Full size is %s pixels', 'P3' ),
									sprintf( '<a href="%1$s" title="%2$s">%3$s &times; %4$s</a>',
										wp_get_attachment_url(),
										esc_attr( __( 'Link to full-size image', 'P3' ) ),
										$metadata['width'],
										$metadata['height']
									)
								);

								edit_post_link( __( 'Edit this entry.', 'P3' ), ' <span class="meta-sep">|</span> ', '' );
							?>

								<div class="attachment-image">
									<p><a href="<?php echo $next_attachment_url; ?>" title="<?php echo esc_attr( get_the_title() ); ?>" rel="attachment"><?php echo wp_get_attachment_image( $post->ID, array( $content_width - 8, 700 ) ); ?></a></p>
									<div class="caption"><?php if ( !empty($post->post_excerpt) ) the_excerpt(); ?></div>
									<div class="image-description"><?php if ( !empty($post->post_content) ) the_content(); ?></div>
								</div>

								<div class="navigation attachment">
									<div class="alignleft"><?php previous_image_link(); ?></div>
									<div class="alignright"><?php next_image_link(); ?></div>
								</div>

							<?php else : ?>
								<p><?php _e( 'View file:', 'P3' ); ?> <a href="<?php echo wp_get_attachment_url(); ?>" title="<?php echo esc_attr( get_the_title() ); ?>" rel="attachment"><?php echo basename( get_permalink() ); ?></a></p>
								<?php the_content( __( '(More ...)' , 'P3' ) ); ?>
								<?php wp_link_pages(); ?>
								<?php edit_post_link( __( 'Edit this entry.', 'P3' ), '<p>', '</p>' ); ?>

							<?php endif; ?>

							<div class="bottom-of-entry">&nbsp;</div>

							<?php comments_template(); ?>

						</div> <!-- .entry -->

					</div> <!-- .post -->

				<?php endwhile; ?>

			<?php endif; ?>

		</div> <!-- #main -->

	</div>

</div>

<?php get_footer(); ?>
