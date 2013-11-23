<?php

/*  Initialize the options before anything else. 
/* ------------------------------------ */
add_action( 'admin_init', 'custom_theme_options', 1 );


/*  Build the custom settings & update OptionTree.
/* ------------------------------------ */
function custom_theme_options() {
	
	// Get a copy of the saved settings array.
	$saved_settings = get_option( 'option_tree_settings', array() );

	// Custom settings array that will eventually be passed to the OptionTree Settings API Class.
	$custom_settings = array(

/*  Help pages
/* ------------------------------------ */	
	'contextual_help' => array(
      'content'       => array( 
        array(
          'id'        => 'general_help',
          'title'     => 'Documentation',
          'content'   => '
			<p>First, a friendly warning: Please remember that the "Reset Options" button resets <strong>ALL</strong> options. That means, if you reset your styling options, all your custom sidebars and other settings will be reset as well.</p>
			<p><i>Frequently Asked Questions:</i></p>
			<p><strong>Q: Styling options do not work, why?</strong> &mdash; A: Make sure that the dynamic.css file has server permissions set to chmod 0777, so that it is writable. You may also need to empty cache.</p>
		'
        )
      )
    ),
	
/*  Admin panel sections
/* ------------------------------------ */	
	'sections'        => array(
		array(
			'id'		=> 'functionality',
			'title'		=> 'Functionality'
		),
		array(
			'id'		=> 'general',
			'title'		=> 'General'
		),
		array(
			'id'		=> 'header',
			'title'		=> 'Header'
		),
	),
	
/*  Theme options
/* ------------------------------------ */
	'settings'        => array(
		// Functionality: Posting Access
		array(
			'id'		=> 'postingaccess',
			'label'		=> 'Posting Access',
			'desc'		=> 'If checked allow any registered member to post',
			'type'		=> 'checkbox',
			'section'	=> 'functionality',
			'choices'	=> array(
				array( 
					'value' => '1',
					'label' => 'Allow any registered member to post',
					'std'	=> '1'
				)
			)
		),
		// Functionality: Hide Threads
		array(
			'id'		=> 'hidethreads',
			'label'		=> 'Hide Threads',
			'desc'		=> 'If checked hide comment threads by default on all non-single views',
			'type'		=> 'checkbox',
			'section'	=> 'functionality',
			'choices'	=> array(
				array( 
					'value' => '1',
					'label' => 'Hide comment threads by default on all non-single views',
					'std'	=> '1'
				)
			)
		),


		//General: Background-color
		array(
			'id'		=> 'background-color',
			'label'		=> 'Background color for website',
			'desc'		=> 'Select a background color for your website or live this blank to use the default color',
			'type'		=> 'colorpicker',
			'section'	=> 'general'
		),


		// General: Background-image
		array(
			'id'		=> 'background-image',
			'label'		=> 'Background image for website',
			'desc'		=> 'Select the background image that you want to use on your website',
			'type'		=> 'radio',
			'std'		=> '1',
			'section'	=> 'general',
			'choices'	=> array(
				array( 
					'value' => 'none',
					'label' => 'No image'
				),
				array( 
					'value' => 'disolve',
					'label' => 'Disolve'
				),
				array( 
					'value' => 'bubbles',
					'label' => 'Bubbles'
				),
				array( 
					'value' => 'dots',
					'label' => 'Dots'
				),
				array( 
					'value' => 'squares',
					'label' => 'Squares'
				),
				array( 
					'value' => 'plaid',
					'label' => 'Plaid'
				),
				array( 
					'value' => 'stripes',
					'label' => 'Stripes'
				),
				array( 
					'value' => 'santa',
					'label' => 'Santa'
				)
			)
		),
		// General: Main sidebar
		array(
			'id'		=> 'mainsidebar-hide',
			'label'		=> 'Hide main sidebar',
			'desc'		=> 'If checked the main sidebar will not show on site',
			'type'		=> 'checkbox',
			'section'	=> 'general',
			'choices'	=> array(
				array( 
					'value' => '1',
					'label' => 'Hide',
					'std'	=> '1'
				)
			)
		),
		// General: Footer bar
		array(
			'id'		=> 'footerbar-hide',
			'label'		=> 'Hide footer bar',
			'desc'		=> 'If checked the footer bar will not show on site',
			'type'		=> 'checkbox',
			'section'	=> 'general',
			'choices'	=> array(
				array( 
					'value' => '1',
					'label' => 'Hide',
					'std'	=> '1'
				)
			)
		),
		// General: Post prompt
		array(
			'id'		=> 'post-prompt',
			'label'		=> 'Post prompt',
			'desc'		=> 'Text used for post prompt. (if empty, defaults to: Whatcha up to?)',
			'type'		=> 'text',
			'section'	=> 'general'
		),
		// General: Post Titles
		array(
			'id'		=> 'posttitles-hide',
			'label'		=> 'Post Titles',
			'desc'		=> 'If checked Post Titles will not show on site',
			'type'		=> 'checkbox',
			'section'	=> 'general',
			'choices'	=> array(
				array( 
					'value' => '1',
					'label' => 'Disable',
					'std'	=> '1'
				)
			)
		),
		

		// General: Favicon
		array(
			'id'		=> 'favicon',
			'label'		=> 'Favicon',
			'desc'		=> 'Upload a 16x16px Png/Gif image that will be your favicon',
			'type'		=> 'upload',
			'section'	=> 'general'
		),
		
		
		// Header: Custom Logo
		array(
			'id'		=> 'custom-logo',
			'label'		=> 'Custom Logo',
			'desc'		=> 'Upload your custom logo image. Set logo max-height in styling options.',
			'type'		=> 'upload',
			'section'	=> 'header'
		),
		// Header: Site Description
		array(
			'id'		=> 'site-description',
			'label'		=> 'Site Description',
			'desc'		=> 'The description that appears next to your logo',
			'type'		=> 'checkbox',
			'section'	=> 'header',
			'choices'	=> array(
				array( 
					'value' => '1',
					'label' => 'Disable'
				)
			)
		),
	)
);

/*  Settings are not the same? Update the DB
/* ------------------------------------ */
	if ( $saved_settings !== $custom_settings ) {
		update_option( 'option_tree_settings', $custom_settings ); 
	} 
}
