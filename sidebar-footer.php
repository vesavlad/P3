<?php
/**
 * Sidebar template.
 *
 * @package P3
 */
?>	
<?php if ( !P3_get_hide_footer_bar() ) : ?>
	<div id="footer-sidebar">
	<?php do_action( 'before_sidebar' ); ?>

		<ul>
			<?php
			if ( !function_exists( 'dynamic_sidebar' ) || !dynamic_sidebar('p3_footer_bar') ) {
				the_widget( 'P3_Recent_Comments', array(), array( 'before_widget' => '<li> ', 'after_widget' => '</li>', 'before_title' =>'<h2>', 'after_title' => '</h2>' ) );
				the_widget( 'P3_Recent_Tags', array(), array( 'before_widget' => '<li> ', 'after_widget' => '</li>', 'before_title' =>'<h2>', 'after_title' => '</h2>' ) );
			}
			?>
		</ul>

		<div class="clear"></div>

	</div> <!-- // sidebar -->
<?php endif; ?>