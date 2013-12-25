<?php


/**
 * Returns the submenu items of the parent menu item.
 * @param $sorted_menu_items
 * @param $args
 * @return mixed
 */
function theme_wp_nav_menu_sub_menu_objects( $sorted_menu_items, $args ) {
    if ( isset( $args->sub_menu ) ) {
        $root_id = 0;
 
        // find the current menu item
        foreach ( $sorted_menu_items as $menu_item ) {
            if ( $menu_item->current ) {
                // set the root id based on whether the current menu item has a parent or not
                $root_id = ( $menu_item->menu_item_parent ) ? $menu_item->menu_item_parent : $menu_item->ID;
                break;
            }
        }
 
        $menu_item_parents = array();
        foreach ( $sorted_menu_items as $key => $item ) {
            // init menu_item_parents
            if ( $item->ID == $root_id ) $menu_item_parents[] = $item->ID;
 
            if ( in_array( $item->menu_item_parent, $menu_item_parents ) ) {
                // part of sub-tree: keep!
                $menu_item_parents[] = $item->ID;
            } else {
                // not part of sub-tree: away with it!
                unset( $sorted_menu_items[$key] );
            }
        }
 
        return $sorted_menu_items;
 
    } else {
        return $sorted_menu_items;
    }
}
 
add_filter( 'wp_nav_menu_objects', 'theme_wp_nav_menu_sub_menu_objects', 10, 2 );
 
function theme_primary_menu() {
    $primary = wp_nav_menu( array(
        'theme_location'  => 'primary',
        'container'       => '',
        'container_class' => '',
        'echo'            => false,
        'fallback_cb'     => '',
        'items_wrap'      => '<ul id="%1$s" class="%2$s sub-menu">%3$s</ul>',
        'depth'           => 1,
    ));
 
    return $primary;
}
    
function theme_subnav_menu() {
    $subnav = wp_nav_menu( array(
        'theme_location'  => 'primary',
        'container'       => '',
        'container_class' => '',
        'fallback_cb'     => '',
        'items_wrap'      => '<ul id="%1$s" class="%2$s sub-menu">%3$s</ul>',
        'echo'            => false,
        'sub_menu'        => true
    ) );
 
    $menu_items = substr_count( $subnav, 'class="menu-item ' );
 
    if ( $menu_items != 0 ) {
        return $subnav;
    }
 
}


/**
 * Recent Tags widget.
 *
 * @package P3
 * @since 1.1.4
 */

class P3_Submenu extends WP_Widget {
	/**
	 * Widget constructor 
     *
	 * @desc sets default options and controls for widget
	 */
	function P3_Submenu () {
		/* Widget settings */
		$widget_ops = array (
			'classname' => 'widget_submenu',
			'description' => __( 'Show submenu' )			
		);

		/* Create the widget */
		$this->WP_Widget( 'submenu-widget', __( 'P3 Submenu' ), $widget_ops );
	}
	
	/**
	 * Displaying the widget
	 *
	 * Handle the display of the widget
	 * @param array
	 * @param array
	 */
	function widget( $args, $instance ) {
		$submenu = theme_subnav_menu();	
		global $post;
		extract( $args );
		echo $before_widget;
		$title = apply_filters('widget_title', do_shortcode($instance['title']), $instance, $this->id_base);
		echo $before_title . $title . $after_title;
		if(!empty($submenu))
		{
			echo $submenu;
		}else{
			echo theme_primary_menu();
		}
		echo $after_widget;	
	}
	
	/**
	 * Update and save widget
	 *
	 * @param array $new_instance
	 * @param array $old_instance
	 * @return array New widget values
	 */
	function update ( $new_instance, $old_instance ) {	
		$old_instance['title'] = strip_tags( $new_instance['title'] );
	    	
		return $old_instance;
	}
	
	/**
	 * Creates widget controls or settings
	 *
	 * @param array Return widget options form
	 */
	function form ( $instance ) { 
		$title = ( isset( $instance['title'] ) ) ? esc_attr( $instance['title'] ) : '';
		$title_id = $this->get_field_id( 'title' );
		$title_name = $this->get_field_name( 'title' );
		?>
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php echo __( 'Title',"P3" ); ?>:</label>
			<input type="text" class="widefat" id="<?php echo $title_id ?>" name="<?php echo $title_name ?>" value="<?php echo $title; ?>" />	
		</p>
              
        <?php
	}
}


register_widget( 'P3_Submenu' );