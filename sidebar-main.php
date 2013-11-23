<?php
/**
 * Sidebar template.
 *
 * @package P3
 */
?>
<?php if ( !P3_get_hide_main_sidebar() ) : ?>
	<div id="main-sidebar">
	<?php do_action( 'before_sidebar' ); ?>

		<ul>
			<?php
			if ( !function_exists( 'dynamic_sidebar' ) || !dynamic_sidebar('p3_main_sidebar') ) {
				the_widget( 'P3_Recent_Comments', array(), array( 'before_widget' => '<li> ', 'after_widget' => '</li>', 'before_title' =>'<h2>', 'after_title' => '</h2>' ) );
				the_widget( 'P3_Recent_Tags', array(), array( 'before_widget' => '<li> ', 'after_widget' => '</li>', 'before_title' =>'<h2>', 'after_title' => '</h2>' ) );
			}
			?>
		</ul>

		<div class="clear"></div>

	</div> <!-- // sidebar -->
<?php endif; ?>