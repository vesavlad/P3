<?php
/**
 * Front-end post form.
 *
 * @package P3
 */
?>
<script type="text/javascript">
/* <![CDATA[ */
	jQuery(document).ready(function($) {
		$('#post_format').val($('#post-types a.selected').attr('id'));
		$('#post-types a').click(function(e) {
			$('.post-input').hide();
			$('#post-types a').removeClass('selected');
			$(this).addClass('selected');
			if ($(this).attr('id') == 'post') {
				$('#posttitle').val("<?php echo esc_js( __('Post Title', 'P3') ); ?>");
			} else {
				$('#posttitle').val('');
			}
			$('#postbox-type-' + $(this).attr('id')).show();
			$('#post_format').val($(this).attr('id'));
			return false;
		});
	});
/* ]]> */
</script>

<?php $post_format = isset( $_GET['p'] ) ? $_GET['p'] : 'status'; ?>
<div id="postbox">
		<ul id="post-types">
			<li><a id="status" class="post-format-button"<?php if ( 'status' == $post_format ) : ?> class="selected"<?php endif; ?> href="<?php echo site_url( '?p=status' ); ?>" title="<?php esc_attr_e( 'Status Update', 'P3' ); ?>"><?php _e( 'Status Update', 'P3' ); ?></a></li>
			<li><a id="post" class="post-format-button"<?php if ( 'post' == $post_format || 'standard' == $post_format ) : ?> class="selected"<?php endif; ?> href="<?php echo site_url( '?p=post' ); ?>" title="<?php esc_attr_e( 'Blog Post', 'P3' ); ?>"><?php _e( 'Blog Post', 'P3' ); ?></a></li>
			<li><a id="quote" class="post-format-button"<?php if ( 'quote' == $post_format ) : ?> class="selected"<?php endif; ?> href="<?php echo site_url( '?p=quote' ); ?>" title="<?php esc_attr_e( 'Quote', 'P3' ); ?>"><?php _e( 'Quote', 'P3' ); ?></a></li>
			<li><a id="link" class="post-format-button"<?php if ( 'link' == $post_format ) : ?> class="selected"<?php endif; ?> href="<?php echo site_url( '?p=link' ); ?>" title="<?php esc_attr_e( 'Link', 'P3' ); ?>"><?php _e( 'Link', 'P3' ); ?></a></li>
		</ul>

		<div class="avatar">
			<?php echo get_avatar( get_current_user_id(), 48 ); ?>
		</div>

		<div class="inputarea">

			<form id="new_post" name="new_post" method="post" action="<?php echo site_url(); ?>/">
				<?php if ( 'status' == $post_format || empty( $post_format ) ) : ?>
				<label for="posttext" id="post-prompt">
					<?php P3_user_prompt(); ?>
				</label>
				<?php endif; ?>

				<div id="postbox-type-post" class="post-input <?php if ( 'post' == $post_format || 'standard' == $post_format ) echo ' selected'; ?>">
					<input type="text" name="posttitle" id="posttitle" value=""
						onfocus="this.value=(this.value=='<?php echo esc_js( __( 'Post Title', 'P3' ) ); ?>') ? '' : this.value;"
						onblur="this.value=(this.value=='') ? '<?php echo esc_js( __( 'Post Title', 'P3' ) ); ?>' : this.value;" />
				</div>
				<?php if ( current_user_can( 'upload_files' ) ): ?>
				<div id="media-buttons" class="hide-if-no-js">
					<?php P3_media_buttons(); ?>
				</div>
				<?php endif; ?>
				<textarea class="expand70-200" name="posttext" id="posttext" rows="4" cols="60"></textarea>
				<div id="postbox-type-quote" class="post-input <?php if ( 'quote' == $post_format ) echo " selected"; ?>">
					<label for="postcitation" class="invisible"><?php _e( 'Citation', 'P3' ); ?></label>
						<input id="postcitation" name="postcitation" type="text"
							value="<?php esc_attr_e( 'Citation', 'P3' ); ?>"
							onfocus="this.value=(this.value=='<?php echo esc_js( __( 'Citation', 'P3' ) ); ?>') ? '' : this.value;"
							onblur="this.value=(this.value=='') ? '<?php echo esc_js( __( 'Citation', 'P3' ) ); ?>' : this.value;" />
				</div>
				<label class="post-error" for="posttext" id="posttext_error"></label>
				<div class="postrow">
					<input id="tags" name="tags" type="text" autocomplete="off"
						value="<?php esc_attr_e( 'Tag it', 'P3' ); ?>"
						onfocus="this.value=(this.value=='<?php echo esc_js( __( 'Tag it', 'P3' ) ); ?>') ? '' : this.value;"
						onblur="this.value=(this.value=='') ? '<?php echo esc_js( __( 'Tag it', 'P3' ) ); ?>' : this.value;" />
					<input id="submit" type="submit" value="<?php esc_attr_e( 'Post it', 'P3' ); ?>" />
				</div>
				<input type="hidden" name="post_format" id="post_format" value="<?php echo esc_attr( $post_format ); ?>" />
				<span class="progress spinner-post-new" id="ajaxActivity"></span>

				<?php do_action( 'P3_post_form' ); ?>

				<input type="hidden" name="action" value="post" />
				<?php wp_nonce_field( 'new-post' ); ?>
			</form>

		</div>

		<div class="clear"></div>

</div> <!-- // postbox -->
