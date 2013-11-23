<?php
/**
 * Footer template.
 *
 * @package P3
 */
?>
<div id="footer">
	<p>
	    <?php printf( __( 'Theme %1$s designed and maintained by %2$s', 'p3' ), 'P3', '<a href="http://www.kano.ro/" rel="designer">KANO</a>'); ?>
	</p>
</div>

<div id="notify"></div>

<div id="help">
	<dl class="directions">
		<dt>c</dt><dd><?php _e( 'compose new post', 'P3' ); ?></dd>
		<dt>j</dt><dd><?php _e( 'next post/next comment', 'P3' ); ?></dd>
		<dt>k</dt> <dd><?php _e( 'previous post/previous comment', 'P3' ); ?></dd>
		<dt>r</dt> <dd><?php _e( 'reply', 'P3' ); ?></dd>
		<dt>e</dt> <dd><?php _e( 'edit', 'P3' ); ?></dd>
		<dt>o</dt> <dd><?php _e( 'show/hide comments', 'P3' ); ?></dd>
		<dt>t</dt> <dd><?php _e( 'go to top', 'P3' ); ?></dd>
		<dt>l</dt> <dd><?php _e( 'go to login', 'P3' ); ?></dd>
		<dt>h</dt> <dd><?php _e( 'show/hide help', 'P3' ); ?></dd>
		<dt><?php _e( 'shift + esc', 'P3' ); ?></dt> <dd><?php _e( 'cancel', 'P3' ); ?></dd>
	</dl>
</div>

<?php wp_footer(); ?>

</body>
</html>