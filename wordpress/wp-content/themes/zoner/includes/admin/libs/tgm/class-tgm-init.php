<?php
/**
 * This file represents an example of the code that themes would use to register
 * the required plugins.
 *
 * It is expected that theme authors would copy and paste this code into their
 * functions.php file, and amend to suit.
 *
 * @package    TGM-Plugin-Activation
 * @subpackage Anaglyh
 * @version    2.4.0
 * @author     Thomas Griffin <thomasgriffinmedia.com>
 * @author     Gary Jones <gamajo.com>
 * @copyright  Copyright (c) 2014, Thomas Griffin
 * @license    http://opensource.org/licenses/gpl-2.0.php GPL v2 or later
 * @link       https://github.com/thomasgriffin/TGM-Plugin-Activation
 */

/**
 * Include the TGM_Plugin_Activation class.
 */
require_once dirname( __FILE__ ) . '/class-tgm-plugin-activation.php';

add_action( 'tgmpa_register', 'zoner_register_required_plugins' );
/**
 * Register the required plugins for this theme.
 *
 * In this example, we register two plugins - one included with the TGMPA library
 * and one from the .org repo.
 *
 * The variable passed to tgmpa_register_plugins() should be an array of plugin
 * arrays.
 *
 * This function is hooked into tgmpa_init, which is fired within the
 * TGM_Plugin_Activation class constructor.
 */
function zoner_register_required_plugins() {
    $plugins = array(
        
		array(
            'name'      => 'Maintenance',
            'slug'      => 'maintenance',
            'required'  => false
        ),

		array(
            'name'      => 'WP Retina 2x',
            'slug'      => 'wp-retina-2x',
            'required'  => false
        ),
		
		array(
			'name'     				=> 'Revolution Slider', // The plugin name
			'slug'     				=> 'revslider', 		// The plugin slug (typically the folder name)
			'source'   				=> 'http://fruitfulcode.wpengine.com/themeforest/revslider.zip', // The plugin source
			'required' 				=> false, 	// If false, the plugin is only 'recommended' instead of required
			'version' 				=> '5.0.3', 		// E.g. 1.0.0. If set, the active plugin must be this version or higher, otherwise a notice is presented
			'force_activation' 		=> false, 	// If true, plugin is activated upon theme activation and cannot be deactivated until theme switch
			'force_deactivation' 	=> true, 	// If true, plugin is deactivated upon theme switch, useful for theme-specific plugins
			'external_url' 			=> '', 		// If set, overrides default API URL and points to an external URL
		),
		
		array(
			'name'     				=> 'WPBakery Visual Composer', // The plugin name
			'slug'     				=> 'js_composer', 		// The plugin slug (typically the folder name)
			'source'   				=> 'http://fruitfulcode.wpengine.com/themeforest/js_composer.zip', // The plugin source
			'required' 				=> true, 	// If false, the plugin is only 'recommended' instead of required
			'version' 				=> '4.6.2', 		// E.g. 1.0.0. If set, the active plugin must be this version or higher, otherwise a notice is presented
			'force_activation' 		=> false, 	// If true, plugin is activated upon theme activation and cannot be deactivated until theme switch
			'force_deactivation' 	=> false, 	// If true, plugin is deactivated upon theme switch, useful for theme-specific plugins
			'external_url' 			=> '', 		// If set, overrides default API URL and points to an external URL
		),
		
        array(
            'name'                  => 'Redux Framework', // The plugin name
            'slug'                  => 'redux-framework',       // The plugin slug (typically the folder name)
            'required'              => true,    // If false, the plugin is only 'recommended' instead of required
        ),

		array(
			'name'     				=> 'Envato Wordpress Toolkit', // The plugin name
			'slug'     				=> 'envato-wordpress-toolkit-master', 		// The plugin slug (typically the folder name)
			'source'   				=> 'http://fruitfulcode.wpengine.com/themeforest/envato-wordpress-toolkit-master.zip', // The plugin source
			'required' 				=> false, 	// If false, the plugin is only 'recommended' instead of required
			'version' 				=> '', 		// E.g. 1.0.0. If set, the active plugin must be this version or higher, otherwise a notice is presented
			'force_activation' 		=> false, 	// If true, plugin is activated upon theme activation and cannot be deactivated until theme switch
			'force_deactivation' 	=> false, 	// If true, plugin is deactivated upon theme switch, useful for theme-specific plugins
			'external_url' 			=> '', 		// If set, overrides default API URL and points to an external URL
		),
		
    );

    $config = array(
        'id'           => 'tgm_zoner',		// Unique ID for hashing notices for multiple instances of TGMPA.
        'default_path' => '', // Default absolute path to pre-packaged plugins.
        'menu'         		=> 'tgmpa-install-plugins', // Menu slug.
		'parent_slug'  => 'themes.php',                // Default parent
        'has_notices'  => true,                    // Show admin notices or not.
        'dismissable'  => true,                    // If false, a user cannot dismiss the nag message.
        'dismiss_msg'  => '',                      // If 'dismissable' is false, this message will be output at top of nag.
        'is_automatic' => false,                   // Automatically activate plugins after installation or not.
        'message'      => '',                      // Message to output right before the plugins table.
        'strings'      => array(
            'page_title'                      => __( 'Install Required Plugins', 'zoner' ),
            'menu_title'                      => __( 'Install Plugins', 'zoner' ),
            'installing'                      => __( 'Installing Plugin: %s', 'zoner' ), // %s = plugin name.
            'oops'                            => __( 'Something went wrong with the plugin API.', 'zoner' ),
            'notice_can_install_required'     => _n_noop( 'This theme requires the following plugin: %1$s.', 'This theme requires the following plugins: %1$s.', 'zoner' ), // %1$s = plugin name(s).
            'notice_can_install_recommended'  => _n_noop( 'This theme recommends the following plugin: %1$s.', 'This theme recommends the following plugins: %1$s.', 'zoner' ), // %1$s = plugin name(s).
            'notice_cannot_install'           => _n_noop( 'Sorry, but you do not have the correct permissions to install the %s plugin. Contact the administrator of this site for help on getting the plugin installed.', 'Sorry, but you do not have the correct permissions to install the %s plugins. Contact the administrator of this site for help on getting the plugins installed.', 'zoner' ), // %1$s = plugin name(s).
            'notice_can_activate_required'    => _n_noop( 'The following required plugin is currently inactive: %1$s.', 'The following required plugins are currently inactive: %1$s.', 'zoner' ), // %1$s = plugin name(s).
            'notice_can_activate_recommended' => _n_noop( 'The following recommended plugin is currently inactive: %1$s.', 'The following recommended plugins are currently inactive: %1$s.', 'zoner' ), // %1$s = plugin name(s).
            'notice_cannot_activate'          => _n_noop( 'Sorry, but you do not have the correct permissions to activate the %s plugin. Contact the administrator of this site for help on getting the plugin activated.', 'Sorry, but you do not have the correct permissions to activate the %s plugins. Contact the administrator of this site for help on getting the plugins activated.', 'zoner' ), // %1$s = plugin name(s).
            'notice_ask_to_update'            => _n_noop( 'The following plugin needs to be updated to its latest version to ensure maximum compatibility with this theme: %1$s.', 'The following plugins need to be updated to their latest version to ensure maximum compatibility with this theme: %1$s.', 'zoner' ), // %1$s = plugin name(s).
            'notice_cannot_update'            => _n_noop( 'Sorry, but you do not have the correct permissions to update the %s plugin. Contact the administrator of this site for help on getting the plugin updated.', 'Sorry, but you do not have the correct permissions to update the %s plugins. Contact the administrator of this site for help on getting the plugins updated.', 'zoner' ), // %1$s = plugin name(s).
            'install_link'                    => _n_noop( 'Begin installing plugin', 'Begin installing plugins', 'zoner' ),
            'activate_link'                   => _n_noop( 'Begin activating plugin', 'Begin activating plugins', 'zoner' ),
            'return'                          => __( 'Return to Required Plugins Installer', 'zoner' ),
            'plugin_activated'                => __( 'Plugin activated successfully.', 'zoner' ),
            'complete'                        => __( 'All plugins installed and activated successfully. %s', 'zoner' ), // %s = dashboard link.
            'nag_type'                        => 'updated' // Determines admin notice type - can only be 'updated', 'update-nag' or 'error'.
        )
    );

    tgmpa( $plugins, $config );

}
