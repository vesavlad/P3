<?php
/**
 * Theme options page.
 *
 * @package P3
 * @since unknown
 */

add_action( 'admin_menu', array( 'P3_Options', 'init' ) );

class P3_Options {

	static function init() {
		global $plugin_page;

		add_theme_page( __( 'Theme Options', 'P3' ), __( 'Theme Options', 'P3' ), 'edit_theme_options', 'P3-options-page', array( 'P3_Options', 'page' ) );

		if ( 'P3-options-page' == $plugin_page ) {
			wp_enqueue_script( 'farbtastic' );
			wp_enqueue_style( 'farbtastic' );
			wp_enqueue_script( 'colorpicker' );
			wp_enqueue_style( 'colorpicker' );


			wp_enqueue_style( 'thickbox' ); // Stylesheet used by Thickbox
    		wp_enqueue_script( 'thickbox' );
    		wp_enqueue_script( 'media-upload' );
    		wp_enqueue_script( 'p3-upload', get_template_directory_uri() . '/inc/p3-upload.js', array( 'thickbox', 'media-upload' ) );


		}
	}

	static function page() {

        register_setting( 'P3options', 'P3options' );
    
    /*
		register_setting( 'P3ops', 'prologue_show_titles' );
		register_setting( 'P3ops', 'P3_allow_users_publish' );
		register_setting( 'P3ops', 'P3_prompt_text' );
		register_setting( 'P3ops', 'P3_hide_sidebar' );
		register_setting( 'P3ops', 'P3_background_color' );
		register_setting( 'P3ops', 'P3_background_image' );
		register_setting( 'P3ops', 'P3_hide_threads' );
		register_setting( 'P3ops', 'P3_header_user_image');
    */

		$P3options = get_option('P3options');
	/*
		$prologue_show_titles_val    = get_option( 'prologue_show_titles' );
		$P3_allow_users_publish_val  = get_option( 'P3_allow_users_publish' );
		$P3_prompt_text_val          = get_option( 'P3_prompt_text' );
		$P3_hide_sidebar             = get_option( 'P3_hide_sidebar' );
		$P3_background_color         = get_option( 'P3_background_color' );
		$P3_background_image         = get_option( 'P3_background_image' );
		$P3_hide_threads             = get_option( 'P3_hide_threads' );
		$P3_header_user_image 		 = get_option( 'P3_header_user_image');
 	*/

		if ( isset( $_POST[ 'action' ] ) && esc_attr( $_POST[ 'action' ] ) == 'update' ) {
			check_admin_referer( 'P3ops-options' );

			if ( isset( $_POST[ 'prologue_show_titles' ] ) )
				$P3options["prologue_show_titles"] = intval( $_POST[ 'prologue_show_titles' ] );
			else
				$P3options["prologue_show_titles"] = 0;

			if ( isset( $_POST[ 'P3_allow_users_publish' ] ) )
				$P3options["P3_allow_users_publish"] = intval( $_POST[ 'P3_allow_users_publish' ] );
			else
				$P3options["P3_allow_users_publish"] = 0;

			if ( isset( $_POST[ 'P3_background_color_hex' ] ) ) {
				// color value must be 3 or 6 hexadecimal characters
				if ( preg_match( '/^#?([a-f0-9]{3}){1,2}$/i', $_POST['P3_background_color_hex'] ) ) {
					$P3options["P3_background_color"] = $_POST['P3_background_color_hex'];
					// if color value doesn't have a preceding hash, add it
					if ( false === strpos( $P3options["P3_background_color"], '#' ) )
						$P3options["P3_background_color"] = '#' . $P3options["P3_background_color"];
				} else {
					$P3options["P3_background_color"] = '';
				}
			}

			if ( esc_attr( $_POST[ 'P3_prompt_text' ] ) != __( "Whatcha' up to?" ) )
				$P3options["P3_prompt_text"] = esc_attr( $_POST[ 'P3_prompt_text' ] );

			if ( isset( $_POST[ 'P3_hide_sidebar' ] ) )
				$P3options["P3_hide_sidebar"] = intval( $_POST[ 'P3_hide_sidebar' ] );
			else
				$P3options["P3_hide_sidebar"] = false;

			if ( isset( $_POST[ 'P3_hide_threads' ] ) )
				$P3options["P3_hide_threads"] = $_POST[ 'P3_hide_threads' ];
			else
				$P3options["P3_hide_threads"] = false;

			if ( isset( $_POST['P3_background_image'] ) )
				$P3options["P3_background_image"] = $_POST[ 'P3_background_image' ];
			else
				$P3options["P3_background_image"] = 'disolve';

			if ( isset( $_POST['P3_header_user_image'] ) )
				$P3options["P3_header_user_image"] = $_POST[ 'P3_header_user_image' ];
			else
				$P3options["P3_header_user_image"] = "";

			/*
			update_option( 'prologue_show_titles', $prologue_show_titles_val );
			update_option( 'P3_allow_users_publish', $P3_allow_users_publish_val );
			update_option( 'P3_prompt_text', $P3_prompt_text_val );
			update_option( 'P3_hide_sidebar', $P3_hide_sidebar );
			update_option( 'P3_background_color', $P3_background_color );
			update_option( 'P3_background_image', $P3_background_image );
			update_option( 'P3_hide_threads', $P3_hide_threads );
			update_option( 'P3_header_user_image', $P3_header_user_image);
			*/
			update_option( 'P3options', $P3options);

		?>
			<div class="updated"><p><strong><?php _e( 'Options saved.', 'P3' ); ?></strong></p></div>
		<?php

			} ?>

		<div class="wrap">
	    <?php echo "<h2>" . __( 'P3 Options', 'P3' ) . "</h2>"; ?>

		<form enctype="multipart/form-data" name="form1" method="post" action="<?php echo esc_attr( str_replace( '%7E', '~', $_SERVER['REQUEST_URI'] ) ); ?>">

			<h3 style="font-family: georgia, times, serif; font-weight: normal; border-bottom: 1px solid #ddd; padding-bottom: 5px">
				<?php _e( 'Functionality Options', 'P3' ); ?>
			</h3>

			<?php settings_fields( 'P3ops' ); ?>

			<table class="form-table">
				<tbody>
					<tr valign="top">
						<th scope="row"><?php _e( 'Posting Access:', 'P3' ); ?></th>
						<td>

						<input id="P3_allow_users_publish" type="checkbox" name="P3_allow_users_publish" <?php checked( $P3options["P3_allow_users_publish"], 1 ); ?> value="1" />

						<?php if ( defined( 'IS_WPCOM' ) && IS_WPCOM )
								$msg = __( 'Allow any WordPress.com member to post', 'P3' );
							  else
							  	$msg = __( 'Allow any registered member to post', 'P3' );
						 ?>

						<label for="P3_allow_users_publish"><?php echo $msg; ?></label>

						</td>
					</tr>
					<tr valign="top">
						<th scope="row"><?php _e( 'Hide Threads:', 'P3' ); ?></th>
						<td>

						<input id="P3_hide_threads" type="checkbox" name="P3_hide_threads" <?php checked( $P3options["P3_hide_threads"], 1 ); ?> value="1" />
						<label for="P3_hide_threads"><?php _e( 'Hide comment threads by default on all non-single views', 'P3' ); ?></label>

						</td>
					</tr>
				</tbody>
			</table>
			<p>&nbsp;</p>


			<h3 style="font-family: georgia, times, serif; font-weight: normal; border-bottom: 1px solid #ddd; padding-bottom: 5px">
				<?php _e( 'Design Options', 'P3' ); ?>
			</h3>

			<table class="form-table">
				<tbody>
					<tr valign="top">
						<th scope="row"><?php _e( 'Custom Background Color:', 'P3' ); ?></th>
						<td>
							<input id="pickcolor" type="button" class="button" name="pickcolor" value="<?php esc_attr_e( 'Pick a Color', 'P3' ); ?> "/>
							<input name="P3_background_color_hex" id="P3_background_color_hex" type="text" value="<?php echo esc_attr( $P3options["P3_background_color"] ); ?>" />
							<div id="colorPickerDiv" style="z-index: 100;background:#eee;border:1px solid #ccc;position:absolute;display:none;"> </div>
						</td>
					</tr>

					<tr valign="top">
						<th scope="row"><?php _e( 'Custom Header Image:', 'P3' ); ?></th>
						<td>
							<input type='button' class='button button-upload' value='<?php esc_attr_e( 'Pick Image', 'P3' ); ?>'/>
							<input type='text' id='logo' class='regular-text text-upload' name='P3_header_user_image' value='<?php echo esc_url( $P3options["P3_header_user_image"] ); ?>'/></br>
       						<img style='max-width: 300px; display: block;' src='<?php echo esc_url( $P3options["P3_header_user_image"] ); ?>' class='preview-upload' />
    
						</td>
					</tr>

					<tr valign="top">
						<th scope="row"><?php _e( 'Background Image:', 'P3' ); ?></th>
						<td>
							<input type="radio" id="bi_none" name="P3_background_image" value="none" <?php checked( $P3options["P3_background_image"], 'none' ); ?>/> <label for="bi_none"><?php _e( 'None', 'P3' ); ?></label><br />
							<input type="radio" id="bi_disolve" name="P3_background_image" value="disolve" <?php checked( $P3options["P3_background_image"], 'disolve' ); ?>/> <label for="bi_disolve"><?php _e( 'Disolve', 'P3'); ?></label><br />
							<input type="radio" id="bi_bubbles" name="P3_background_image" value="bubbles"<?php checked( $P3options["P3_background_image"], 'bubbles' ); ?>/> <label for="bi_bubbles"><?php _e( 'Bubbles', 'P3' ); ?></label><br />
							<input type="radio" id="bi_polka" name="P3_background_image" value="dots" <?php checked( $P3options["P3_background_image"], 'dots' ); ?>/> <label for="bi_polka"><?php _e( 'Polka Dots', 'P3' ); ?></label><br />
							<input type="radio" id="bi_squares" name="P3_background_image" value="squares" <?php checked( $P3options["P3_background_image"], 'squares' ); ?>/> <label for="bi_squares"><?php _e( 'Squares', 'P3' ); ?></label><br />
							<input type="radio" id="bi_plaid" name="P3_background_image" value="plaid" <?php checked( $P3options["P3_background_image"], 'plaid' ); ?>/> <label for="bi_plaid"><?php _e( 'Plaid', 'P3' ); ?></label><br />
							<input type="radio" id="bi_stripes" name="P3_background_image" value="stripes" <?php checked( $P3options["P3_background_image"], 'stripes' ); ?> /> <label for="bi_stripes"><?php _e( 'Stripes', 'P3' ); ?></label><br />
							<input type="radio" id="bi_santa" name="P3_background_image" value="santa" <?php checked( $P3options["P3_background_image"], 'santa' ); ?>/> <label for="bi_santa"><?php _e( 'Santa', 'P3' ); ?></label>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row"><?php _e( 'Sidebar display:', 'P3' ); ?></th>
						<td>
							<input id="P3_hide_sidebar" type="checkbox" name="P3_hide_sidebar" <?php checked( $P3options["P3_hide_sidebar"], 1 ); ?> value="1" />
							<label for="P3_hide_sidebar"><?php _e( 'Hide the Sidebar', 'P3' ); ?></label>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row"><?php _e( 'Post prompt:', 'P3' ); ?></th>
						<td>
							<input id="P3_prompt_text" type="input" name="P3_prompt_text" value="<?php echo ($P3options["P3_prompt_text"] == __("Whatcha' up to?") ) ? __("Whatcha' up to?") : stripslashes( $P3options["P3_prompt_text"] ); ?>" />
				 			(<?php _e( 'if empty, defaults to <strong>Whatcha up to?</strong>', 'P3' ); ?>)
						</td>
					</tr>
					<tr>
						<th><?php _e( 'Post Titles:', 'P3' )?></th>
						<td>
							<input id="prologue_show_titles" type="checkbox" name="prologue_show_titles" <?php checked( $P3options["prologue_show_titles"], 1 ); ?> value="1" />
							<label for="prologue_show_titles"><?php _e( 'Display titles', 'P3' ); ?></label>
						</td>
					</tr>
				</tbody>
			</table>

			<p>
			</p>

			<p class="submit">
				<input type="submit" name="Submit" value="<?php esc_attr_e( 'Update Options', 'P3' ); ?>" />
			</p>

		</form>
		</div>

<script type="text/javascript">
/* <![CDATA[ */
	var farbtastic;

	function pickColor(color) {
		jQuery('#P3_background_color_hex').val(color);
		farbtastic.setColor(color);
	}

	jQuery(document).ready(function() {
		jQuery('#pickcolor').click(function() {
			jQuery('#colorPickerDiv').show();
		});

		jQuery('#hidetext' ).click(function() {
			toggle_text();
		});

		farbtastic = jQuery.farbtastic( '#colorPickerDiv', function(color) { pickColor(color); });
	});

	jQuery(document).mousedown(function(){
		// Make the picker disappear, since we're using it in an independant div
		hide_picker();
	});

	function colorDefault() {
		pickColor( '#<?php echo HEADER_TEXTCOLOR; ?>' );
	}

	function hide_picker(what) {
		var update = false;
		jQuery('#colorPickerDiv').each(function(){
			var id = jQuery(this).attr( 'id' );
			if (id == what) {
				return;
			}
			var display = jQuery(this).css( 'display' );
			if (display == 'block' ) {
				jQuery(this).fadeOut(2);
			}
		});
	}

	function toggle_text(force) {
		if (jQuery('#textcolor').val() == 'blank' ) {
			//Show text
			jQuery(buttons.toString()).show();
			jQuery('#textcolor').val( '<?php echo HEADER_TEXTCOLOR; ?>' );
			jQuery('#hidetext').val( '<?php _e( 'Hide Text', 'P3' ); ?>' );
		}
		else {
			//Hide text
			jQuery(buttons.toString()).hide();
			jQuery('#textcolor').val( 'blank' );
			jQuery('#hidetext').val( '<?php _e( 'Show Text', 'P3' ); ?>' );
		}
	}
/* ]]> */
</script>

<?php
	}
}