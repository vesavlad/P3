/**
 * Handles toggling the main navigation and new post form menu on iPhone.
 */
jQuery( document ).ready( function( $ ) {
	var $mastnav    = $( '#header-navmenu' ),
		$postToggle = $( '#mobile-post-button' ),
		$postbox    = $( '#postbox' ),
		$menu       = $mastnav.find( '.menu' ),
	    timeout     = false;

	$postToggle.click( function( e ) {
		e.preventDefault();
		if ( $postbox.is( ':visible' ) )
			$postbox.slideUp( 'fast' );
		else
			$postbox.slideDown( 'fast' );
	} );

	$mastnav.find( '.site-navigation' ).removeClass( 'main-navigation' ).addClass( 'main-small-navigation' );
	$mastnav.find( '.site-navigation h1' ).removeClass( 'assistive-text' ).addClass( 'menu-toggle' );

	$( '.menu-toggle' ).click( function( e ) {
		e.preventDefault();
		if ( $menu.is( ':visible' ) )
			$menu.slideUp( 'fast' );
		else
			$menu.slideDown( 'fast' );
	} );
} );

