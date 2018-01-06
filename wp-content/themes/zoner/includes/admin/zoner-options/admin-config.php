<?php
if (!class_exists('zoner_config')) {
	
    class zoner_config {

        public $args        = array();
        public $sections    = array();
        public $theme;
        public $ReduxFramework;

        public function __construct() {
             if (!class_exists('ReduxFrameworkPlugin') && !class_exists('ReduxFramework')) { //then set only default values
             	global $zoner_config;
             	$this->theme = wp_get_theme();
             	$this->setSections();
	            $zoner_config =  $this->loadDefault();
	            return false;
             }

            // This is needed. Bah WordPress bugs.  ;)
            if (  true == redux_Helpers::isTheme(__FILE__) ) {
                $this->initSettings();
            } else {
                add_action('plugins_loaded', array($this, 'initSettings'), 10);
            }

        }
         public function loadDefault() {
         	$default = array();
         	$defaultCss = '';
         	$defaultCssRules = '';
         	$googleFontList ='';
         	$all_options = $this->sections;
         	foreach ($all_options as $key => $field) {
             		foreach ($field['fields'] as $option) {

             			if (!empty($option['type']) && $option['type']=='typography'){ //load dynamic typography
             				if (isset($option['default'])){
             					foreach ($option['default'] as $key => $value) {
             						if ($key!='google'){
             							$defaultCssRules .= $key.':'.$value.';';
             						if ($key=='font-family'){
             							$googleFontList[$value] = $value;
             						}	

             						}
             					}
             					$defaultCss .= $option['output'][0].'{'.$defaultCssRules.'}';
             					$defaultCssRules ='';
             					continue;
	             				}
	             			}

	             		if (isset($option['default'])){ //load default options
							$default[$option['id']] = $option['default'];
	             		}
             	  }		
             }

             if (!empty($googleFontList)){
             	$googleFontList = implode('|', $googleFontList);
             	$googleFontList = str_replace(', ', '|', $googleFontList);
             	$googleFontList = str_replace(' ', '+', $googleFontList);
             	$default['dynamic-css'] = '@import url("http://fonts.googleapis.com/css?family='.$googleFontList.'");'.$defaultCss;
             }else{
             	$default['dynamic-css'] = $defaultCss;
             }
             return $default;
         }
		
		public function zonerAddCustomStylesConfig() {
			 wp_register_style('zoner-custom-config', get_template_directory_uri().'/includes/admin/zoner-options/patterns/css/admin-config.css');
			 wp_enqueue_style( 'zoner-custom-config' );
		}
		
		function disable_redux_notice() {
			echo '<style>.redux-notice, .rAds, .rAds span, #redux_rAds { display: none;}</style>';
		}
		
        public function initSettings() {
			add_action('admin_head', array($this,'disable_redux_notice'));
        	$this->zonerAddCustomStylesConfig();
            $this->theme = wp_get_theme();
            $this->setArguments();
            $this->setHelpTabs();
            $this->setSections();
            if (!isset($this->args['opt_name'])) return;
            add_action( 'zoner/loaded', array( $this, 'remove_demo' ) );
			
			$this->ReduxFramework = new ReduxFramework($this->sections, $this->args);
        }

        /**

          This is a test function that will let you see when the compiler hook occurs.
          It only runs if a field	set with compiler=>true is changed.

         * */
        function compiler_action($options, $css) {}

        /**

          Custom function for filtering the sections array. Good for child themes to override or add to the sections.
          Simply include this function in the child themes functions.php file.

          NOTE: the defined constants for URLs, and directories will NOT be available at this point in a child theme,
          so you must use get_template_directory_uri() if you want to use any of the built in icons

         * */
        function dynamic_section($sections) {
            //$sections = array();
            $sections[] = array(
                'title' => __('Section via hook', 'zoner'),
                'desc' => __('<p class="description">This is a section created by adding a filter to the sections array. Can be used by child themes to add/remove sections from the options.</p>', 'zoner'),
                'icon' => 'el-icon-paper-clip',
                'fields' => array()
            );

            return $sections;
        }


        // Remove the demo link and the notice of integrated demo from the redux-framework plugin
        function remove_demo() {
			// Used to hide the demo mode link from the plugin page. Only used when Zoner is a plugin.
			if ( class_exists( 'ReduxFrameworkPlugin' ) ) {
					remove_filter( 'plugin_row_meta', array(
						ReduxFrameworkPlugin::instance(),
						'plugin_metalinks'
					), null, 2 );

					// Used to hide the activation notice informing users of the demo panel. Only used when Zoner is a plugin.
					remove_action( 'admin_notices', array( ReduxFrameworkPlugin::instance(), 'admin_notices' ) );
			}
        }
		
		public function getBookmarkUserContent () {
			global $zoner, $wpdb, $wp_roles;
			$out_html = '';
			
			$allUsers = array();
			$allUsers = $wpdb->get_results( "SELECT user_id FROM $wpdb->zoner_bookmark bkmr where bkmr.is_choose = 1 GROUP BY user_id ORDER BY user_id ASC ");
			
			if (!empty($allUsers)) {
				$out_html = '<section class="bookmarks-property">';
				
				foreach ($allUsers as $user) {
					$user_id = '';
					$user_id   = $user->user_id;
					
				    $user_info = get_userdata($user_id);
					
					$roles = array();
					if (!empty($user_info)) {
						$roles = $user_info->roles;
						
						$out_html .= '<section class="user">';
							$out_html .= '<a class="user-name-link" href="'. get_edit_user_link( $user_id ).'"><h2 class="user-name">'.zoner_get_user_name($user_info).'</h2></a>';
							$out_html .= '<h4 class="user-role">'.__('Roles', 'zoner'). ': ' .implode(',', $roles). '</h4>';
						
						$all_user_bproperties = $zoner->bookmark->zoner_get_all_bookmark_by_user($user->user_id);
						if (!empty($all_user_bproperties)) {
							$out_html .= '<section class="properties">';
								$out_html .= '<div class="properties-list">';
							foreach ($all_user_bproperties as $property) {
								$property_type = $zoner->property->get_property($property->property_id);
								
								if (!empty($property_type->id)) {
									$out_html .= '<div id="property-'.$property_type->id.'" class="property">';
										$out_html  .= '<a href="'.$property_type->link.'" title="'.$property_type->title.'" target="_blank">';	
											$attachment_id = 0;
											$attachment_id = get_post_thumbnail_id( $property->property_id );
											
											if ($attachment_id != 0) {
												$prop_image = wp_get_attachment_image_src( $attachment_id);
												$out_html  .= '<div class="thumbnail-wrapper"><img class="img-responsive" src="'.$prop_image[0].'" alt="" /></div>';
											} else {
												$out_html  .= '<div class="thumbnail-wrapper"><img class="img-responsive" src="http://placehold.it/150x150" alt="" /></div>';
											}								
											
											$out_html  .= '<h4 class="property-title">'.$property_type->title.'</h4>';
										$out_html .= '</a>';
									$out_html .= '</div>';
								}
								
							}
								$out_html .= '</div>';
							$out_html .= '</section>';
							
						}
						$out_html .= '</section>';
					}	
				}
				$out_html .= '</section>';
			}
			
			return $out_html;
			
		}
		
        public function setSections() {
            global $zoner;
			
			// Background Patterns Reader
            $sample_patterns_path   = get_template_directory_uri().'/includes/admin/zoner-options/patterns/';
            $sample_patterns_url    = get_template_directory_uri().'/includes/admin/zoner-options/patterns/';
            $sample_patterns        = array();

            ob_start();

            $ct             = wp_get_theme();
            $this->theme    = $ct;
            $item_name      = $this->theme->get('Name');
            $tags           = $this->theme->Tags;
            $screenshot     = $this->theme->get_screenshot();
            $class          = $screenshot ? 'has-screenshot' : '';

            $customize_title = sprintf(__('Customize &#8220;%s&#8221;', 'zoner'), $this->theme->display('Name'));
            
            ?>
			
            <div id="current-theme" class="<?php echo esc_attr($class); ?>">
            <?php if ($screenshot) : ?>
                <?php if (current_user_can('edit_theme_options')) : ?>
                        <a href="<?php echo wp_customize_url(); ?>" class="load-customize hide-if-no-customize" title="<?php echo esc_attr($customize_title); ?>">
                            <img src="<?php echo esc_url($screenshot); ?>" alt="<?php esc_attr_e('Current theme preview'); ?>" />
                        </a>
                <?php endif; ?>
                    <img class="hide-if-customize" src="<?php echo esc_url($screenshot); ?>" alt="<?php esc_attr_e('Current theme preview'); ?>" />
                <?php endif; ?>

                <h4><?php echo $this->theme->display('Name'); ?></h4>

                <div>
                    <ul class="theme-info">
                        <li><?php printf(__('By %s', 'zoner'), $this->theme->display('Author')); ?></li>
                        <li><?php printf(__('Version %s', 'zoner'), $this->theme->display('Version')); ?></li>
                        <li><?php echo '<strong>' . __('Tags', 'zoner') . ':</strong> '; ?><?php printf($this->theme->display('Tags')); ?></li>
                    </ul>
                    <p class="theme-description"><?php echo $this->theme->display('Description'); ?></p>
            <?php
            if ($this->theme->parent()) {
                printf(' <p class="howto">' . __('This <a href="%1$s">child theme</a> requires its parent theme, %2$s.') . '</p>', __('http://codex.wordpress.org/Child_Themes', 'zoner'), $this->theme->parent()->display('Name'));
            }
            ?>

                </div>
            </div>

            <?php
            $item_info = ob_get_contents();

            ob_end_clean();

            $sampleHTML = '';
            if (file_exists(dirname(__FILE__) . '/info-html.html')) {
                /** @global WP_Filesystem_Direct $wp_filesystem  */
                global $wp_filesystem;
                if (empty($wp_filesystem)) {
                    require_once(ABSPATH . '/wp-admin/includes/file.php');
                    WP_Filesystem();
                }
                $sampleHTML = $wp_filesystem->get_contents(dirname(__FILE__) . '/info-html.html');
            }
			
			
			/*General Section*/
			$this->sections[] = array(
                'title'     => __('General', 'zoner'),
                'icon'      => $sample_patterns_url . 'images/icons/general.png',
				'icon_type'	=> 'image',
                'fields'    => array (
						
						array(
							'id'        => 'tracking-code',
							'type'      => 'text',
							'title'     => __('Google Analytics ID', 'zoner'),
							'subtitle'  => __("Paste your web analytics tracking id here (UA-XXXXX-X).", 'zoner'),
							'validate'  => 'no_html',
							'default'   => ''
						),
						
						array(
							'id'        => 'smoothscroll',
							'type'      => 'checkbox',
							'title'     => __('Enhanced scrolling', 'zoner'),
							'subtitle'  => __('Select to enable scrolling library.', 'zoner'),
							'desc'      => __('Yes', 'zoner'),
							'class'		=> 'icheck',
							'default'   => '1'
						),
						array(
							'id'       => 'property-agent-conversation',
							'type'     => 'switch',
							'title'    => __('Message system', 'zoner'),
							'subtitle' => __('Show converstion button (only if user logged in).', 'zoner'),
							'default'  => true,
						),
						array(
							'id'       => 'property-agent-form',
							'type'     => 'switch',
							'title'    => __('Contact agent form', 'zoner'),
							'subtitle' => __('Show agent contact form on pages.', 'zoner'),
							'default'  => true,
						),
				)
			);
			
			/*General Section*/
			$this->sections[] = array(
                'title'     => __('Logo', 'zoner'),
                'icon'      => $sample_patterns_url . 'images/icons/logo-options.png',
				'icon_type'	=> 'image',
                'fields'    => array (
							array(
								'id'        => 'logo',
								'type'      => 'media',
								'url'       => false,
								'title'     => __('Logo', 'zoner'),
								'subtitle'  => __('Change your Logo here, upload or enter the URL to your logo image.', 'zoner'),
								'default'   => array('url' => $sample_patterns_url . 'images/logo.png'),
								
							),
							
							array(
								'id'        => 'logo-retina',
								'type'      => 'media',
								'url'       => false,
								'title'     => __('Logo Retina ', 'zoner'),
								'subtitle'  => __('Upload your Retina Logo. This should be your Logo in double size (If your logo is 100 x 20px, it should be 200 x 40px)', 'zoner'),
								'default'   => array ('url' => $sample_patterns_url . 'images/logo@2x.png'),
							),

							 array(
								'id'                => 'logo-dimensions',
								'type'              => 'dimensions',
								'units'    => array('em','px','%'),
								'units_extended'    => 'true',  
								'title'             => __('Original Logo (Width/Height)', 'zoner'),
								'subtitle'          => __("If Retina Logo uploaded, please enter the (width/height) of the Standard Logo you've uploaded (not the Retina Logo)", 'zoner'),
								'default'           => array(
									'width'     => 94, 
									'height'    => 22,
								)
							),
							
							array(
								'id'        => 'favicon',
								'type'      => 'media',
								'url'       => false,
								'title'     => __('Favicon', 'zoner'),
								'subtitle'  => __('A favicon is a 16x16 pixel icon that represents your site; upload your custom Favicon here.', 'zoner'),
								'default'   => array('url' => $sample_patterns_url . 'favicon/favicon-16x16.png'),
							),
							
							array(
								'id'        => 'favicon-iphone',
								'type'      => 'media',
								'url'       => false,
								'title'     => __('Favicon iPhone', 'zoner'),
								'subtitle'  => __('Upload a custom favicon for iPhone (57x57 pixel png).', 'zoner'),
								'default'   => array('url' => $sample_patterns_url . 'favicon/favicon-57x57.png'),
							),
							
							array(
								'id'        => 'favicon-iphone-retina',
								'type'      => 'media',
								'url'       => false,
								'title'     => __('Favicon iPhone Retina', 'zoner'),
								'subtitle'  => __('Upload a custom favicon for iPhone retina (114x114 pixel png).', 'zoner'),
								'default'   => array('url' => $sample_patterns_url . 'favicon/favicon-114x114.png'),
							),
							
							array(
								'id'        => 'favicon-ipad',
								'type'      => 'media',
								'url'       => false,
								'title'     => __('Favicon iPad', 'zoner'),
								'subtitle'  => __('Upload a custom favicon for iPad (72x72 pixel png).', 'zoner'),
								'default'   => array('url' => $sample_patterns_url . 'favicon/favicon-72x72.png'),
							),
							
							array(
								'id'        => 'favicon-ipad-retina',
								'type'      => 'media',
								'url'       => false,
								'title'     => __('Favicon iPad Retina', 'zoner'),
								'subtitle'  => __('Upload a custom favicon for iPhone retina (144x144 pixel png).', 'zoner'),
								'default'   => array('url' => $sample_patterns_url . 'favicon/favicon-144x144.png'),
							),
				)
			);	
			
			
			/*Display options Section*/
			$this->sections[] = array(
                'title'     => __('Blog options', 'zoner'),
                'icon'      => $sample_patterns_url . 'images/icons/display-options.png',
				'icon_type'	=> 'image',
                'fields'    => array (
					array(
                        'id'        => 'pp-comments',
                        'type'      => 'select',
                        'title'     => __('Display Comments', 'zoner'),
                        'subtitle'  => __('Choose where users are allowed to post comment in your website.', 'zoner'),
						'std'		=> 'post',
                        
                        'options'   => array(
                            'post'  => __('Posts Only', 'zoner'), 
                            'page'  => __('Pages Only', 'zoner'), 
							'both'  => __('Posts/Pages show', 'zoner'), 
							'none'	=> __('Hide all', 'zoner'), 
                        ),
                        'default'   => 'post'
                    ),
                     array(
                        'id'        => 'excerpt',
                        'type'      => 'select',
                        'title'     => __('Select Post Preview', 'zoner'),
                        'subtitle'  => __('Select showing full post, excerpt or title only', 'zoner'),
                        'options'   =>  array(
                            '1'     => __("Full post (before <-more->)", 'zoner'),
                            '2'     => __("Excerpt", 'zoner'),
                            '3'     => __("Only Title", 'zoner'),
                        ),
                        'default'   => '1'
                    ),
                    
                    array(
                        'id'        => 'excerpt-numwords',
                        'type'      => 'text',
                        'required'  => array('excerpt', '=', '2'),
                        'title'     => __('Number of words', 'zoner'),
                        'subtitle'      => __('Type number of words for excerpt', 'zoner'),
                        'default'   => '20'
                    ),
					array(
                        'id'        => 'pp-breadcrumbs',
                        'type'      => 'checkbox',
                        'title'     => __('Display Breadcrumbs', 'zoner'),
                        'subtitle'  => __('Display dynamic breadcrumbs on each page of your website.', 'zoner'),
                        'desc'      => __('Yes', 'zoner'),
						'class'		=> 'icheck',
                        'default'   => '1'
                    ),		
					array(
                        'id'        => 'pp-post',
                        'type'      => 'image_select',
                        'title'     => __('Single post layout', 'zoner'),
                        'subtitle'  => __('Select main content and sidebar alignment.', 'zoner'),
                        'options'   => ((class_exists('ReduxFrameworkPlugin'))?(array(
								'1' => array('alt' => '1 Column',       'img' => ReduxFramework::$_url . 'assets/img/1col.png'),
								'2' => array('alt' => '2 Column Left',  'img' => ReduxFramework::$_url . 'assets/img/2cl.png'),
								'3' => array('alt' => '2 Column Right', 'img' => ReduxFramework::$_url . 'assets/img/2cr.png'),
							)):0),
                        'default'   => '3'
                    ),
					array(
                        'id'        => 'pp-date',
                        'type'      => 'checkbox',
                        'title'     => __('Display date for posts', 'zoner'),
                        'default'   => true,
						'class'		=> 'icheck',
						'desc'      => __('Yes', 'zoner'),
                    ),	
					array(
                        'id'        => 'pp-thumbnail',
                        'type'      => 'checkbox',
                        'title'     => __('Display thumbnails for posts', 'zoner'),
                        'default'   => true,
						'class'		=> 'icheck',
						'desc'      => __('Yes', 'zoner'),
                    ),	
					array(
                        'id'        => 'pp-tags',
                        'type'      => 'checkbox',
                        'title'     => __('Display tags for posts', 'zoner'),
                        'default'   => true,
						'class'		=> 'icheck',
						'desc'      => __('Yes', 'zoner'),
                    ),	
					array(
                        'id'        => 'pp-authors',
                        'type'      => 'checkbox',
                        'title'     => __('Display authors for posts', 'zoner'),
                        'default'   => true,
						'class'		=> 'icheck',
						'desc'      => __('Yes', 'zoner'),
                    ),
					array(
                        'id'        => 'pp-about-author',
                        'type'      => 'checkbox',
                        'title'     => __('Display about the Author', 'zoner'),
                        'default'   => true,
						'class'		=> 'icheck',
						'desc'      => __('Yes', 'zoner'),
                    ),		
				)	
			);	
				
			/*Styling options Section*/
			$this->sections[] = array(
                'title'     => __('Styling', 'zoner'),
                'icon'      => $sample_patterns_url . 'images/icons/styling.png',
				'icon_type'	=> 'image',
                'fields'    => array (
					array(
                        'id'        => 'body-background',
                        'type'      => 'background',
                        'output'    => array('body'),
                        'title'     => __('Body Background', 'zoner'),
                        'subtitle'  => __('Body background with image, color, etc.', 'zoner'),
						'transparent'	=> false,
						'default'   => array(
							'background-color' => '#ffffff',
							'background-repeat'	=> 'inherit',
							'background-attachment'	=> 'inherit',
							'background-position'	=> 'top center',
							'background-size'		=> 'inherit',
						)
                    ),
					
					array(
							'id'       => 'global-color-scheme',
							'type'     => 'image_select', 
							'presets'  => true,
							'title'    => __('Color Scheme', 'zoner'),
							'subtitle' => __('Choose main color scheme.', 'zoner'),
							'default'  => '0',
							'std'	   => '0',
							'options'  => array(
									'0'  => array(
														'alt'   => __('Blue', 'zoner'),
														'img'   => $sample_patterns_url.'images/blue.png',
														'presets' => array(
															'zoner-searchbox-advancedcolor' 	=> '#1396e2',
															'underline-item-color'				=> '#1396e2',
															'footer-thumbnails-mask-color' 		=> '#1396e2',
															
															'submenu-itembg-color' => array(    
																						'regular'   => '#f3f3f3',
																						'hover'     => '#1396e2',
																						'active'    => '#1396e2',
																						),
															'content-link-color' => array (	
																						'regular'   => '#1396e2',
																						'hover'     => '#2a6496',
																						'active'    => '#2a6496',
																					),
														)
													),
									'1' => array(
														'alt'   => __('Brown', 'zoner'),
														'img'   => $sample_patterns_url.'images/brown.png',
														'presets' => array(
															'zoner-searchbox-advancedcolor' 	=> '#998675',
															'underline-item-color'				=> '#998675',
															'footer-thumbnails-mask-color' 		=> '#998675',
															
															'submenu-itembg-color' => array(    
																						'regular'   => '#f3f3f3',
																						'hover'     => '#998675',
																						'active'    => '#998675',
																						),
															'content-link-color' => array (	
																						'regular'   => '#998675',
																						'hover'     => '#2a6496',
																						'active'    => '#2a6496',
															),
														)
													),
									'2' => array(
														'alt'   => __('Green', 'zoner'),
														'img'   => $sample_patterns_url.'images/green.png',
														'presets' => array(
															'zoner-searchbox-advancedcolor' => '#00c109',
															'underline-item-color'			=> '#00c109',
															'footer-thumbnails-mask-color' 	=> '#00c109',
															'submenu-itembg-color' => array(    
																						'regular'   => '#f3f3f3',
																						'hover'     => '#00c109',
																						'active'    => '#00c109',
																						),
															'content-link-color' => array (	
																						'regular'   => '#00c109',
																						'hover'     => '#2a6496',
																						'active'    => '#2a6496',
															),
														)
													),
									'3' => array(
														'alt'   => __('Grey', 'zoner'),
														'img'   => $sample_patterns_url.'images/grey.png',
														'presets' => array(
															'zoner-searchbox-advancedcolor' => '#707070',
															'underline-item-color'	=> '#707070',
															'footer-thumbnails-mask-color' => '#707070',
															
															'submenu-itembg-color' => array(    
																						'regular'   => '#f3f3f3',
																						'hover'     => '#707070',
																						'active'    => '#707070',
																						),
															'content-link-color' => array (	
																						'regular'   => '#707070',
																						'hover'     => '#2a6496',
																						'active'    => '#2a6496',
															),
														)
													),
									'4' => array(
														'alt'   => __('Magenta', 'zoner'),
														'img'   => $sample_patterns_url.'images/magenta.png',
														'presets' => array(
															'zoner-searchbox-advancedcolor' => '#e83183',
															'underline-item-color'			=> '#e83183',
															'footer-thumbnails-mask-color' 	=> '#e83183',
															
															'submenu-itembg-color' => array(    
																						'regular'   => '#f3f3f3',
																						'hover'     => '#e83183',
																						'active'    => '#e83183',
																						),
															'content-link-color' => array (	
																						'regular'   => '#e83183',
																						'hover'     => '#2a6496',
																						'active'    => '#2a6496',
															),
														)
													),
									'5' => array(
														'alt'   => __('Orange', 'zoner'),
														'img'   => $sample_patterns_url.'images/orange.png',
														'presets' => array(
															'zoner-searchbox-advancedcolor' => '#f7941d',
															'underline-item-color'			=> '#f7941d',
															'footer-thumbnails-mask-color' 	=> '#f7941d',
															
															'submenu-itembg-color' => array(    
																						'regular'   => '#f3f3f3',
																						'hover'     => '#f7941d',
																						'active'    => '#f7941d',
																						),
															'content-link-color' => array (	
																						'regular'   => '#f7941d',
																						'hover'     => '#2a6496',
																						'active'    => '#2a6496',
															),
														)
													),
									'6' => array(
														'alt'   => __('Red', 'zoner'),
														'img'   => $sample_patterns_url.'images/red.png',
														'presets' => array(
															'zoner-searchbox-advancedcolor' => '#e2372f',
															'underline-item-color'			=> '#e2372f',
															'footer-thumbnails-mask-color' 	=> '#e2372f',
															
															'submenu-itembg-color' => array(    
																						'regular'   => '#f3f3f3',
																						'hover'     => '#e2372f',
																						'active'    => '#e2372f',
																						),
															'content-link-color' => array (	
																						'regular'   => '#e2372f',
																						'hover'     => '#2a6496',
																						'active'    => '#2a6496',
															),
														)
													),
									'7' => array(
														'alt'   => __('Violet', 'zoner'),
														'img'   => $sample_patterns_url.'images/violet.png',
														'presets' => array(
															'zoner-searchbox-advancedcolor' => '#7c00c3',
															'underline-item-color'			=> '#7c00c3',
															'footer-thumbnails-mask-color' 	=> '#7c00c3',
															
															'submenu-itembg-color' => array(    
																						'regular'   => '#f3f3f3',
																						'hover'     => '#7c00c3',
																						'active'    => '#7c00c3',
																						),
															'content-link-color' => array (	
																						'regular'   => '#7c00c3',
																						'hover'     => '#2a6496',
																						'active'    => '#2a6496',
															),
														)
													)																			
							
							)							
						),
					array(
						'id'        => 'advanced-color-scheme',
						'type'      => 'switch',
						'title'     => __('Enable advanced Color Scheme', 'zoner'),
						'default'   =>  0,
						'on'        => 'On',
						'off'       => 'Off',
					),
					array(
						'id' 	=> 'advanced-color-scheme-section',
						'type' 	=> 'section',
						'title' => __('Advanced color scheme', 'zoner'),
						'indent' => true,
						'required'  => array('advanced-color-scheme', '=', '1')
					),

					array(
						'id'        => 'zoner-advanced-color-primary',
						'type'      => 'color',
						'title'     => __('Primary color', 'zoner'),
						'default'   => '#1396e2',
						'validate'  => 'color',
						'transparent'	=> false,
						'required'  => array('advanced-color-scheme', '=', '1')
					),

					array(
						'id'        => 'zoner-advanced-color-secondary',
						'type'      => 'color',
						'title'     => __('Secondary color', 'zoner'),
						'default'   => '#1396e2',
						'validate'  => 'color',
						'transparent'	=> false,
						'required'  => array('advanced-color-scheme', '=', '1')
					),

					array(
						'id'        => 'zoner-searchbox-advancedcolor-cust',
						'type'      => 'color',
						'title'     => __('Searchbox advanced color', 'zoner'),
						'default'   => '#1396e2',
						'validate'  => 'color',
						'transparent'	=> false,
						'required'  => array('advanced-color-scheme', '=', '1')
					),

					array(
						'id'        => 'underline-item-color-cust',
						'type'      => 'color',
						'title'     => __('Underline item color', 'zoner'),
						'default'   => '#1396e2',
						'validate'  => 'color',
						'transparent'	=> false,
						'required'  => array('advanced-color-scheme', '=', '1')
					),

					array(
						'id'        => 'footer-thumbnails-mask-color-cust',
						'type'      => 'color',
						'title'     => __('Footer thumbnails mask color', 'zoner'),
						'default'   => '#1396e2',
						'validate'  => 'color',
						'transparent'	=> false,
						'required'  => array('advanced-color-scheme', '=', '1')
					),

					array(
						'id'        => 'submenu-itembg-color-cust',
						'type'      => 'link_color',
						'title'     => __('Submenu item color', 'zoner'),
						'output'      => array('a'),
						'default'   => array(
							'regular'   => '#f3f3f3',
							'hover'     => '#1396e2',
							'active'    => '#1396e2',
						)
					),
					array(
						'id'        => 'content-link-color-cust',
						'type'      => 'link_color',
						'title'     => __('Content link color', 'zoner'),
						'output'      => array('a'),
						'default'   => array(
							'regular'   => '#1396e2',
							'hover'     => '#2a6496',
							'active'    => '#2a6496',
						)
					)
				)
				
			);

			$header_sections_fileds   = array();
			$header_sections_fileds[] =	array(
                        'id'        => 'header-background-color',
                        'type'      => 'color',
                        'title'     => __('Header background color', 'zoner'),
                        'default'   => '#FFFFFF',
                        'validate'  => 'color',
						'transparent'	=> false
                    );
					
			$header_sections_fileds[] =	array(
						'id'        => 'show-secondary-nav',
						'type'      => 'checkbox',
						'title'     => __('Show secondary navigation', 'zoner'),
						'subtitle'  => __('Select to show secondary navigation.', 'zoner'),
						'desc'      => __('Yes', 'zoner'),
						'default'   => '1'
					);
					
			$header_sections_fileds[] = array(
				'id'				=> 'global-fixed-header',
				'type'			=> 'switch',
				'title'			=> __('Fixed header', 'zoner'), 
				'subtitle'	=> __('Select to display fixed header on all pages.', 'zoner'),
				'default'		=> false,
			);
					
			$header_sections_fileds[] =	array(
						'id'       => 'header-phone',
						'type'     => 'text',
						'title'    => __('Phone', 'zoner'), 
						'subtitle'      => __('Edit phone', 'zoner'),
						'validate'  => 'no_html',
					);	
			
			$header_sections_fileds[] =	array(
						'id'       => 'header-email',
						'type'     => 'text',
						'title'    => __('Email', 'zoner'), 
						'subtitle'      => __('Edit email', 'zoner'),
						'validate'  => 'email',
						'default'	=> get_bloginfo( 'admin_email' )
						
					);	
			
			
			$header_sections_fileds[] = array(
						'id'       => 'register-agency-account',
						'type'     => 'switch',
						'title'    => __('Create agency account', 'zoner'), 
						'subtitle'      => __('Select to display link in header.', 'zoner'),
						'default'  => true,
			);
			
			$header_sections_fileds[] = array(
						'id'       => 'register-profile-link',
						'type'     => 'switch',
						'title'    => __('Show register link', 'zoner'), 
						'subtitle'      => __('Select to display register link in header.', 'zoner'),
						'default'  => true,
			);
			
			$header_sections_fileds[] = array(
						'id'       => 'sign-in-link',
						'type'     => 'switch',
						'title'    => __('Show sign in link', 'zoner'), 
						'subtitle'      => __('Select to display sign in link in header.', 'zoner'),
						'default'  => true,
			);

            $header_sections_fileds[] = array(
                'id'       => 'subheader-compare',
                'type'     => 'switch',
                'title'    => __('Show compare button', 'zoner'),
                'subtitle'      => __('Select to display compare button in header.', 'zoner'),
                'default'  => true,
            );
            $header_sections_fileds[] = array(
                'id'       => 'subheader-add-property',
                'type'     => 'switch',
                'title'    => __('Show add property button', 'zoner'),
                'subtitle' => __('Select to display add property in header.', 'zoner'),
                'default'  => true,
            );
			
			/*Register*/
	
			$PageOptions = null;
			$PageOptions = $zoner->zoner_get_all_options_translated_pages(array(
				'id'	 	 => 'page-register-account',
				'title'	 	 => __('Register page', 'zoner'), 
				'subtitle'	 => __('Choose page for top menu link "Register".', 'zoner'),
				'exist_page' => 'create-an-account'
			));
			
			if (!empty($PageOptions)) {
				foreach($PageOptions as $opt) {
					$header_sections_fileds[] = $opt;
				}
			}
			
			/* Sign In */
			 
			$PageOptions = null;
			$PageOptions = $zoner->zoner_get_all_options_translated_pages(array(
				'id'	 		=> 'page-signin',
				'title'	 		=> __('Sign In page', 'zoner'), 
				'subtitle'		=> __('Choose page for top menu link "Sign In".', 'zoner'),
				'exist_page' 	=> 'sign-in'
			));
			
			if (!empty($PageOptions)) {
				foreach($PageOptions as $opt) {
					$header_sections_fileds[] = $opt;
				}
			}
			
			/* Compare */
			
			$PageOptions = null;
			$PageOptions = $zoner->zoner_get_all_options_translated_pages(array(
				'id'	 		=> 'page-compare',
				'title'	 		=> __('Compare page', 'zoner'), 
				'subtitle'		=> __('Choose page for compare property list.', 'zoner'),
				'exist_page' 	=> 'compare'
			));
			
			if (!empty($PageOptions)) {
				foreach($PageOptions as $opt) {
					$header_sections_fileds[] = $opt;
				}
			}
			
			if ( function_exists('icl_object_id') ) {
				$header_sections_fileds[] = array(
                        'id'        => 'wmpl-flags-box',
                        'type'      => 'checkbox',
                        'title'     => __('WPML box', 'zoner'),
                        'subtitle'  => __('Select to enable WPML box.', 'zoner'),
                        'desc'      => __('Yes', 'zoner'),
						'class'		=> 'icheck',
                        'default'   => '1'
                    );
			}
			
			/*Header Section*/
			$this->sections[] = array(
                'title'     => __('Header', 'zoner'),
                'icon'      => $sample_patterns_url . 'images/icons/header.png',
				'icon_type' => 'image',
                'fields'    => $header_sections_fileds
				
			);
			
			
			$specific_properties = array();
			$specific_properties_args = array(
				'posts_per_page'   => -1,
				'orderby'          => 'title',
				'order'            => 'ASC',
				'post_type'        => 'property',
				'post_status'      => 'publish'
			);
			$list_properties = get_posts($specific_properties_args);
			if (!empty($list_properties)) {
				foreach($list_properties as $prop) {
					$specific_properties[$prop->ID] = $prop->post_title;
				}
			}
			
			$home_page_variation = array();
			$home_page_variation['0'] = array(
											'title' => __('Default header', 'zoner'),
											'img' => $sample_patterns_url . 'images/homepage_variation/default.png',
										);
								
			/*Google map*/
			$home_page_variation['1'] = array(
                                    'title'  => __('Map Full Screen', 'zoner'),
                                    'img' => $sample_patterns_url . 'images/homepage_variation/google-map-fullscreen.png',
                                );
								
			$home_page_variation['2'] = array(
                                    'title'  => __('Map Fixed Height', 'zoner'),
                                    'img' => $sample_patterns_url . 'images/homepage_variation/google-map-fixed-height.png',
                                );
								
			$home_page_variation['3'] = array(
                                    'title'  => __('Map Fixed Navigation', 'zoner'),
                                    'img' => $sample_patterns_url . 'images/homepage_variation/google-map-fixed-navigation.png',
                                );
								
			$home_page_variation['4'] = array(
                                    'title'  => __('Map with Horizontal Search Box', 'zoner'),
                                    'img' => $sample_patterns_url . 'images/homepage_variation/horizontal-search-floated.png',
                                );
								
			$home_page_variation['5'] = array(
                                    'title'  => __('Map with Advanced Horizontal Search Box', 'zoner'),
                                    'img' => $sample_patterns_url . 'images/homepage_variation/horizontal-search-advanced.png',
                                );
			/*Slider*/
			$home_page_variation['11'] = array(
                                    'title'  => __('Property Slider', 'zoner'),
                                    'img' => $sample_patterns_url . 'images/homepage_variation/slider.png',
                                );
								
			$home_page_variation['12'] = array(
                                    'title'  => __('Property Slider with Search box', 'zoner'),
                                    'img' => $sample_patterns_url . 'images/homepage_variation/slider-search-box.png',
                                );
								
			$home_page_variation['13'] = array(
                                    'title'  => __('Property Slider with Horizontal Search Box', 'zoner'),
                                    'img' => $sample_patterns_url . 'images/homepage_variation/horizontal-search-floated-slider.png',
                                );
								
			$home_page_variation['14'] = array(
                                    'title'  => __('Property Slider with Advanced Horizontal Search Box', 'zoner'),
                                    'img' => $sample_patterns_url . 'images/homepage_variation/horizontal-search-slider.png',
                                );
			
			$revsliders = array();
			$revsliders[0] = __( 'No slider', 'zoner' );
	
			/*Revolution slider integrate*/		
			if ( ! function_exists( 'is_plugin_active' ) )
			require_once( ABSPATH . '/wp-admin/includes/plugin.php' );
	
			if (function_exists( 'is_plugin_active' )) {
				if (is_plugin_active( 'revslider/revslider.php' ) ) {
					global $wpdb;
					
					$home_page_variation['15'] = array(
														'title'  	=> __('Revolution Slider', 'zoner'),
														'img' 	 	=> $sample_patterns_url . 'images/homepage_variation/slider.png',
													);
														
					$home_page_variation['16'] = array(
														'title'  	=> __('Revolution Slider with Search box', 'zoner'),
														'img' 		=> $sample_patterns_url . 'images/homepage_variation/slider-search-box.png',
													);
					$home_page_variation['17'] = array(
														'title'  	=> __('Revolution Slider with Horizontal Search Box', 'zoner'),
														'img' 		=> $sample_patterns_url . 'images/homepage_variation/horizontal-search-floated-slider.png',
													);
					$home_page_variation['18'] = array(
														'title'  	=> __('Revolution Slider with Advanced Horizontal Search Box', 'zoner'),
														'img' 		=> $sample_patterns_url . 'images/homepage_variation/horizontal-search-slider.png',
													);
					
					$rs = $wpdb->get_results("SELECT id, title, alias FROM " . $wpdb->prefix . "revslider_sliders ORDER BY id ASC LIMIT 999");
					if ($rs) {
						foreach ( $rs as $slider ) {
							$revsliders[$slider->alias] = $slider->title;
						}
					} 	
				}				
			}						
								
			/*Home page*/
			$this->sections[] = array(
				'title'      => __('Home page', 'zoner'),
                'icon'      => $sample_patterns_url . 'images/icons/homepage.png',
				'icon_type' => 'image',
                'fields'     => array(
						
						array(
                            'id'       	=> 'header-front-page-variations',
                            'type'     	=> 'image_select',
                            'title'     => __('Variations of homepage', 'zoner'),
                            'subtitle'  => __('Choose variation of display homepage.', 'zoner'),
                            'options'  	=> $home_page_variation,
                            'default'  	=> '0',
							'std'	   	=> '0'
                        ),
						
						array(
							'id' 	=> 'slider-options',
							'type' 	=> 'section',
							'title' => __('Slider Options', 'zoner'),
							'indent' 	=> true,
							'required'  => array('header-front-page-variations', '>', '10'),
						),
						
						array(
							'id'        => 'slider-scount',
							'type'      => 'text',
							'title'     => __('Enter the number of displayed properties.', 'zoner'),
							'default'   => '5',
							'required'  => array(
								array('header-front-page-variations', '>', '10'),
								array('header-front-page-variations', '<', '15'),
							),	
						),	
						
						array(
							'id'        => 'slider-specific-properties',
							'type'      => 'select',
							'title'     => __('Specific properties', 'zoner'),
							'subtitle'  => __('Select a specific properties to output in slider.', 'zoner'),
							'options'   => $specific_properties,
							'multi'		=> true,
							'placeholder' => __('Select properties', 'zoner'),
							'required'  => array(
								array('header-front-page-variations', '>', '10'),
								array('header-front-page-variations', '<', '15'),
							),	
						),
						
						array(
							'id'        => 'slider-sorderby',
							'type'      => 'select',
							'title'     => __('Choose Order By properties', 'zoner'),
							'options'   => array (
													'ID' 	=> 'Id',
													'title' => 'Title',
													'name'	=> 'Name',
													'date'	=> 'Date',
													'rand'	=> 'Rand'
												  ),
							'default'	=> 'date',
							'std'		=> 'date',
							'required'  => array(
								array('header-front-page-variations', '>', '10'),
								array('header-front-page-variations', '<', '15'),
							),	
						),
						
						array(
							'id'        => 'slider-only-gallery-images',
							'type'      => 'checkbox',
							'title'     => __('Select to display images from the gallery', 'zoner'),
							'default'   => false,
							'class'		=> 'icheck',
							'desc'      => __('Yes', 'zoner'),
							'required'  => array(
								array('header-front-page-variations', '>', '10'),
								array('header-front-page-variations', '<', '15'),
							),	
						),	
					
						array(
							'id'        => 'slider-sorder',
							'type'      => 'select',
							'title'     => __('Choose Order properties', 'zoner'),
							'options'   => array (
													'ASC' => 'ASC',
													'DESC' => 'DESC',
												  ),
							'default'	=> 'DESC',
							'std'		=> 'DESC',
							'required'  => array(
													array('slider-sorderby', '!=', 'rand'),
													array('header-front-page-variations', '>', '10'),
													array('header-front-page-variations', '<', '15')
												)	
						),
						
						array(
							'id'        => 'slider-rev-index',
							'type'      => 'select',
							'title'     => __('Choose revolution slider', 'zoner'),
							'options'   => $revsliders,
							'default'	=> '0',
							'std'		=> '0',
							'required'  => array(
								array('header-front-page-variations', '>', '14'),
							),	
						),
				)
			);	
			
			/*Maps settigns*/
			$this->sections[] = array(
				'title'      => __('Maps', 'zoner'),
				'icon'      => $sample_patterns_url . 'images/icons/maps.png',
				'icon_type' => 'image',
				'fields'     => array(
									
						array(
						'id'        => 'gm-or-osm',
						'type'      => 'select',
						'title'     => __('Choose global type of the map (GoogleMap or OpenStreetMap)', 'zoner'),
						'options'   => array( 
															'0' => 	__("Google Map", "zoner"),
															'1' =>	__("Open Street Map", "zoner") ,
														),
						'default'	=> '0',
						'std'		=> '0',
						),
						
						array(
							'id'        => 'google-maps-api-key',
							'type'      => 'text',
							'title'     => __('Google Maps Api Key', 'zoner'),
							'subtitle'  => __('Enter your google maps API key.', 'zoner'),
							'desc'			=> '<a href="https://developers.google.com/maps/documentation/javascript/get-api-key" target="_blank"">Where can I take it?</a>',
							'default'   => ''
						),	
						
						array(
							'id' 	=> 'default-maps-hedline-start',
							'type' 	=> 'section',
							'title' => __('Global maps settings', 'zoner'),
							'indent' => true 
						),
					
						array(
							'id'        => 'prop-ggmaps-marker',
							'type'      => 'media',
							'url'       => false,
							'title'     => __('Maps Marker', 'zoner'),
							'subtitle'  => __('Change marker icon for property location.', 'zoner'),
							'default'   => array('url' => $sample_patterns_url . 'images/icons/marker.png'),
							
						),
						
						array(
							'id'        => 'geo-center-lat',
							'type'      => 'text',
							'title'     => __('Latitude', 'zoner'),
							'subtitle'  => __('Default coordinats.', 'zoner'),
							'default'   => '40.7056308'
						),	
					
						array(
							'id'        => 'geo-center-lng',
							'type'      => 'text',
							'title'     => __('Longitude', 'zoner'),
							'subtitle'  => __('Default coordinats.', 'zoner'),
							'default'   => '-73.9780035'
						),	
						
					array(
							'id'        => 'maps-global-zoom',
							'type'      => 'select',
							'title'     => __('Choose zoom for maps', 'zoner'),
							'options'   => array (
													'3'  => '3',
													'4'  => '4',
													'5'  => '5',
													'6'  => '6',
													'7'  => '7',
													'8'  => '8',
												'9'  => '9',
												'10' => '10',
												'11' => '11',
												'12' => '12',
												'13' => '13',
												'14' => '14 - Default',
												'15' => '15',
												'16' => '16',
												'17' => '17',
												'18' => '18',
												'19' => '19',
												'20' => '20',
												'21' => '21',
											  ),
						'default'	=> '14',
						'std'		=> '14',
					),
						
					array(
						'id'        => 'maps-global-type',
						'type'      => 'select',
						'title'     => __('Choose type for map', 'zoner'),
						'options'   => array( 
										'0' => 	__("Custom roadmap skin", "zoner"), 
										'1' =>	__("Roadmap", "zoner") , 
										'2' => 	__("Satellite", "zoner"), 
										'3' =>  __("Hybrid", "zoner"), 
										'4' =>  __("Terrain", "zoner"),
										),
						'default'	=> '0',
						'std'		=> '0',
					),
						
					array(
						'id'        => 'map-markers-ff',
						'type'      => 'checkbox',
						'title'     => __('Get markers from file', 'zoner'),
						'subtitle'  => __('Do not use with cache markers.', 'zoner'),
						'default'   => false,
						'class'		=> 'icheck',
						'desc'      => __('Yes', 'zoner'),
					),	
						
					array(
						'id'        => 'map-use-cache',
						'type'      => 'checkbox',
						'title'     => __('Enable cache markers', 'zoner'),
						'default'   => false,
						'class'		=> 'icheck',
						'desc'      => __('Yes', 'zoner'),
					),	
						
					array(
							'id' 	=> 'default-maps-hedline-end',
							'type'   => 'section',
							'indent' => false,
					),
						
					array(
						'id' 	=> 'single-property-section-start',
						'type' 	=> 'section',
						'title' => __('Property pages ("Add", "Edit").', 'zoner'),
						'indent' => true 
					),
					
					array(
							'id'        => 'single-prop-ggmaps-marker',
							'type'      => 'media',
							'url'       => false,
							'title'     => __('Maps Marker', 'zoner'),
							'subtitle'  => __('Change marker icon for single property location.', 'zoner'),
							'default'   => array('url' => $sample_patterns_url . 'images/icons/marker.png'),
							
						),
						
						array(
							'id'        => 'single-geo-center-lat',
							'type'      => 'text',
							'title'     => __('Latitude', 'zoner'),
							'subtitle'  => __('Default coordinats.', 'zoner'),
							'default'   => '40.7056308'
						),	
					
						array(
							'id'        => 'single-geo-center-lng',
							'type'      => 'text',
							'title'     => __('Longitude', 'zoner'),
							'subtitle'  => __('Default coordinats.', 'zoner'),
							'default'   => '-73.9780035'
						),	
						
						array(
							'id'        => 'single-maps-global-zoom',
							'type'      => 'select',
							'title'     => __('Choose zoom for maps', 'zoner'),
							'options'   => array (
													'3'  => '3',
													'4'  => '4',
													'5'  => '5',
													'6'  => '6',
													'7'  => '7',
													'8'  => '8',
													'9'  => '9',
													'10' => '10',
													'11' => '11',
													'12' => '12',
													'13' => '13',
													'14' => '14 - Default',
													'15' => '15',
													'16' => '16',
													'17' => '17',
													'18' => '18',
													'19' => '19',
													'20' => '20',
													'21' => '21',
												  ),
							'default'	=> '14',
							'std'		=> '14',
						),
					
						array(
							'id' 	=> 'single-property-map-section-end',
							'type'   => 'section',
							'indent' => false,
						),
						
				)
			);	
				
			
			$array_of_features = array();
			$args = array();	
			$args = array('orderby' => 'name', 'order' => 'ASC', 'hide_empty' => false); 
		
			$property_features = array();
			$property_features = get_terms('property_features', $args);
			if (!empty($property_features)) {
				foreach ($property_features as $features) {
					$array_of_features[$features->term_id] =  $features->name;
				}
			}
			
			$this->sections[] = array(
				'title'      => __('Advanced search', 'zoner'),
                'icon'      => $sample_patterns_url . 'images/icons/advanced-search.png',
				'icon_type' => 'image',
                'fields'     => array(
					
						array(
                            'id'       => 'zoner-searchbox',
                            'type'     => 'sorter',
                            'title'    => __('Search box fields.', 'zoner'),
                            'subtitle' => __('Choose available fields to searchbox.', 'zoner'),
                            'compiler' => 'true',
                            'options'  => array(
                                'enabled'  => apply_filters('zoner_searchbox_enabled_fields', array(
                                    'keyword' 	 => __('Keyword', 'zoner'),
                                    'status' 	 => __('Status', 'zoner'),
									'type' 	 	 => __('Type', 'zoner'),
									'country' 	 => __('Country', 'zoner'),
									'city' 	 	 => __('City',  'zoner'),
									'price'		 => __('Price', 'zoner'),
                                )),
                                'disabled' => apply_filters('zoner_searchbox_disabled_fields', array(
									'category'	 => __('Category',  'zoner'),
									'zip' 		 => __('Zip Code', 'zoner'),
									'condition'	 => __('Condition', 'zoner'),
									'payment'	 => __('Payment', 	'zoner'),
									'rooms'	 	 => __('Rooms', 'zoner'),
									'beds'	 	 => __('Beds', 'zoner'),
									'baths'	 	 => __('Baths', 'zoner'),
									'garages' 	 => __('Garages', 'zoner'),
									'district' 	 => __('District',  'zoner'),
									'area' 	     => __('Area', 'zoner'),
								)),
                            
                            ),
                        ),
						
						array(
							'id'        => 'specific-countries',
							'type'      => 'select',
							'title'     => __('Specific Countries', 'zoner'),
							'subtitle'  => __('Select a country to restrict the filter list.', 'zoner'),
							'std'		=> '',
							'options' 	=> $zoner->countries->countries,
							'multi'		=> true,
							'placeholder' => __('Select country', 'zoner')
						),
						
						array(
							'id'        => 'specific-features',
							'type'      => 'select',
							'title'     => __('Specific Features', 'zoner'),
							'subtitle'  => __('Select a features to restrict the filter list.', 'zoner'),
							'options'   => $array_of_features,
							'multi'		=> true,
							'placeholder' => __('Select features', 'zoner')
						),
						
						array(
							'id'        => 'query-operator',
							'type'      => 'select',
							'title'     => __('Query Operator', 'zoner'),
							'subtitle'  => __('Select a query operator.', 'zoner'),
							'options'   => array("OR" => __('OR', 'zoner'), "AND" => __('AND', 'zoner')),
							'default'	=> "OR"
						),
					
						array(
							'id'        => 'zoner-searchbox-submit',
							'type'      => 'text',
							'title'     => __('Submit button page', 'zoner'),
							'default'   => __('Search', 'zoner'),
						),	
						
						array(
							'id'        => 'zoner-searchbox-advancedimg',
							'type'      => 'media',
							'url'       => false,
							'title'     => __('Advanced searchbox background image.', 'zoner'),
							'default'   => array('url' => $sample_patterns_url . 'images/searchbox-bg.jpg'),
						),
						
						array(
							'id'        => 'zoner-searchbox-advancedcolor',
							'type'      => 'color',
							'title'     => __('Advanced searchbox background color.', 'zoner'),
							'default'   => '#1396e2',
							'validate'  => 'color',
							'transparent' => false,
						),
						
						array(
							'id'        => 'zoner-searchbox-advancedfcolor',
							'type'      => 'color',
							'title'     => __('Advanced searchbox font color.', 'zoner'),
							'default'   => '#fff',
							'validate'  => 'color',
							'transparent' => false,
						),
				)
			);	
			
			/*Menu Section*/
			$this->sections[] = array(
                'title'     => __('Menu', 'zoner'),
                'icon'      => $sample_patterns_url . 'images/icons/menu.png',
				'icon_type' => 'image',
                'fields'    => array(
					
					array(
                        'id'        => 'menu-link-color',
                        'type'      => 'link_color',
                        'title'     => __('Menu item color', 'zoner'),
                        'default'   => array(
                            'regular'   => '#2a2a2a',
                            'hover'     => '#2a2a2a',
                            'active'    => '#2a2a2a',
                        )
                    ),
					array(
                        'id'        => 'submenu-link-color',
                        'type'      => 'link_color',
                        'title'     => __('Submenu item color', 'zoner'),
                        'default'   => array(
                            'regular'   => '#5a5a5a',
                            'hover'     => '#ffffff',
                            'active'    => '#ffffff',
                        )
                    ),
					
					array(
                        'id'        => 'submenu-itembg-color',
                        'type'      => 'link_color',
                        'title'     => __('Submenu item background color', 'zoner'),
                        'default'   => array(
                            'regular'   => '#f3f3f3',
                            'hover'     => '#1396e2',
                            'active'    => '#1396e2',
                        )
                    ),
					
					array(
                        'id'        => 'submenu-color',
                        'type'      => 'color',
                        'title'     => __('Submenu background color', 'zoner'),
                        'default'   => '#f3f3f3',
                        'validate'  => 'color',
						'transparent'	=> false
                    ),
					
					array(
                        'id'        => 'submenu-itemborder-color',
                        'type'      => 'color_rgba',
                        'title'     => __('Submenu item border color', 'zoner'),
                        'default'   => array('color' => '#000000', 'alpha' => '0.1'),
                        'mode'      => 'color',
                        'validate'  => 'colorrgba',
						'transparent'	=> false
                    ),
					
					array(
                        'id'        => 'underline-item-color',
                        'type'      => 'color',
                        'title'     => __('Menu underline item color.', 'zoner'),
                        'default'   => '#1396e2',
                        'validate'  => 'color',
						'transparent'	=> false
                    ),
				)
			);
				
			/*Font Styles Section*/
			$this->sections[] = array(
                'title'     => __('Font styles', 'zoner'),
                'icon'      => $sample_patterns_url . 'images/icons/fonts.png',
				'icon_type' => 'image',
                'fields'    => array(
						array(
								'id'          => 'general-typography',
								'type'        => 'typography', 
								'title'       => __('General Text Font Style', 'zoner'),
								'google'      => true, 
								'subsets'	  => true,
								'font-backup' => false,
								'line-height' => false,	
								'text-align'  => false,	
								'output'      => array('body'),
								'units'       =>'px',
								'subtitle'	  => __('Select typography for general text.', 'zoner'),
								'default'     => array(
														'color'       => '#5a5a5a', 
														'font-weight'  => '400', 
														'font-family' => 'Roboto', 
														'google'      => true,
														'font-size'   => '14px'
												),
								'preview' => array('text' => 'sample text')				
							 ),
							array(
								'id'          => 'hone-typography',
								'type'        => 'typography', 
								'title'       => __('H1 Font Style.', 'zoner'),
								'google'      => true, 
								'subsets'	  => true,
								'font-backup' => false,
								'line-height' => false,	
								'text-align'  => false,	
								'output'      => array('h1'),
								'units'       =>'px',
								'subtitle'	  => __('Select the typography you want for header H1.', 'zoner'),
								'default'     => array(
														'color'       => '#5a5a5a', 
														'font-weight'  => '300', 
														'font-family' => 'Roboto', 
														'google'      => true,
														'font-size'   => '28px'
												),
								'preview' => array('text' => 'sample text')								
							 ),
							array(
								'id'          => 'htwo-typography',
								'type'        => 'typography', 
								'title'       => __('H2 Font Style.', 'zoner'),
								'google'      => true, 
								'subsets'	  => true,
								'font-backup' => false,
								'line-height' => false,	
								'text-align'  => false,	
								'output'      => array('h2'),
								'units'       =>'px',
								'subtitle'	  => __('Select the typography you want for header H2.', 'zoner'),
								'default'     => array(
														'color'       => '#5a5a5a', 
														'font-weight'  => '300', 
														'font-family' => 'Roboto', 
														'google'      => true,
														'font-size'   => '24px',
												),
								'preview' => array('text' => 'sample text'),				
							 ), 
							 array(
								'id'          => 'hthree-typography',
								'type'        => 'typography', 
								'title'       => __('H3 Font Style.', 'zoner'),
								'google'      => true, 
								'subsets'	  => true,
								'font-backup' => false,
								'line-height' => false,	
								'text-align'  => false,	
								'output'      => array('h3'),
								'units'       =>'px',
								'subtitle'	  => __('Select the typography you want for header H3.', 'zoner'),
								'default'     => array(
														'color'       => '#5a5a5a', 
														'font-weight'  => '300', 
														'font-family' => 'Roboto', 
														'google'      => true,
														'font-size'   => '18px', 
												),
								'preview' => array('text' => 'sample text'),				
							 ), 
							 
							 array(
								'id'          => 'hfour-typography',
								'type'        => 'typography', 
								'title'       => __('H4 Font Style.', 'zoner'),
								'google'      => true, 
								'subsets'	  => true,
								'font-backup' => false,
								'line-height' => false,	
								'text-align'  => false,	
								'output'      => array('h4'),
								'units'       =>'px',
								'subtitle'	  => __('Select the typography you want for header H4.', 'zoner'),
								'default'     => array(
														'color'       => '#5a5a5a', 
														'font-weight'  => '400', 
														'font-family' => 'Roboto', 
														'google'      => true,
														'font-size'   => '14px',
												),
								'preview' => array('text' => 'sample text'),				
							 ), 
							 array(
								'id'          => 'hfive-typography',
								'type'        => 'typography', 
								'title'       => __('H5 Font Style.', 'zoner'),
								'google'      => true, 
								'subsets'	  => true,
								'font-backup' => false,
								'line-height' => false,	
								'text-align'  => false,	
								'output'      => array('h5'),
								'units'       =>'px',
								'subtitle'	  => __('Select the typography you want for header H5.', 'zoner'),
								'default'     => array(
														'color'       => '#5a5a5a', 
														'font-weight'  => '400', 
														'font-family' => 'Roboto', 
														'google'      => true,
														'font-size'   => '14px',
												),
								'preview' => array('text' => 'sample text'),				
							 ), 
							 
							 array(
								'id'          => 'hsix-typography',
								'type'        => 'typography', 
								'title'       => __('H6 Font Style.', 'zoner'),
								'google'      => true, 
								'subsets'	  => true,
								'font-backup' => false,
								'line-height' => false,	
								'text-align'  => false,	
								'output'      => array('h6'),
								'units'       =>'px',
								'subtitle'	  => __('Select the typography you want for header H6.', 'zoner'),
								'default'     => array(
														'color'       => '#5a5a5a', 
														'font-weight'  => '400', 
														'font-family' => 'Roboto', 
														'google'      => true,
														'font-size'   => '14px',
												),
								'preview' => array('text' => 'sample text'),				
							 ), 
							 
							 array(
								'id'          => 'p-typography',
								'type'        => 'typography', 
								'title'       => __('"p" Font Style.', 'zoner'),
								'google'      => true, 
								'subsets'	  => true,
								'font-backup' => false,
								'line-height' => true,	
								'text-align'  => true,	
								'output'      => array('p'),
								'units'       =>'px',
								'subtitle'	  => __('Select the typography you want for tag "p".', 'zoner'),
								'default'     => array(
														'color'       => '#5a5a5a', 
														'font-weight'  => '400', 
														'font-family' => 'Arial, Helvetica, sans-serif', 
														'google'      => true,
														'font-size'   => '14px',
														'line-height' => '20px',
														'text-align'  => 'inherit'
													),
								'preview' => array('text' => 'sample text'),				
							 ), 
							 
							 
							 array(
								'id'            => 'p-opacity',
								'type'          => 'slider',
								'title'         => __('Transparency for content', 'zoner'),
								'subtitle'      => __('Set the opacity for the content part', 'zoner'),
								'default'       => .7,
								'min'           => 0,
								'step'          => .1,
								'max'           => 1,
								'resolution'    => 0.1,
								'display_value' => 'label'
							),
							 
							 array(
									'id'        => 'content-link-color',
									'type'      => 'link_color',
									'title'     => __('Link style.', 'zoner'),
									'subtitle'  => __('Select the typography you want for tag "a".', 'zoner'),
									'output'      => array('a'),
									'default'   => array(
										'regular'   => '#1396e2',
										'hover'     => '#2a6496',
										'active'    => '#2a6496',
							)
                    ),
				)
			);
			
			
			
			$propertySectionFields   = null;
			$propertySectionFields[] = array(
                        'id'        => 'prop-layout',
                        'type'      => 'image_select',
                        'title'     => __('Layout', 'zoner'),
                        'subtitle'  => __('Choose position sidebar for single property page', 'zoner'),
                        'options'   => ((class_exists('ReduxFrameworkPlugin'))?(array(
								'1' => array('alt' => '1 Column',       'img' => ReduxFramework::$_url . 'assets/img/1col.png'),
								'2' => array('alt' => '2 Column Left',  'img' => ReduxFramework::$_url . 'assets/img/2cl.png'),
								'3' => array('alt' => '2 Column Right', 'img' => ReduxFramework::$_url . 'assets/img/2cr.png'),
							)):0),
                        'default' => '3'
                    );
			
			$propertySectionFields[] = 	array(
                        'id'        => 'prop-single-crop',
                        'type'      => 'checkbox',
                        'title'     => __('Gallery image crop', 'zoner'),
                        'subtitle'  => __('Choose option to crop image on single property', 'zoner'),
                        'desc'      => __('Yes', 'zoner'),
						'class'		=> 'icheck',
						'default'   => '1'
                    );
			
			$propertySectionFields[] = 	array(
						'id'			=>	'property-admin-confirmation',
						'type'			=>	'checkbox',
						'title'			=>	__('Properties admin confirmation', 'zoner'),
						'subtitle'	=>	__('New properties must be approved by admin', 'zoner'),
						'desc'			=>	__('Yes', 'zoner'),
						'class'			=>	'icheck',
						'default'		=>	'1'
					);
					
			$propertySectionFields[] =	array(
						'id'		=> 'property-social-links',
						'type'    	=> 'switch',
						'title'   	=> __('Share links', 'zoner'),
						'subtitle'	=> __('Show share links on single property.', 'zoner'),
						'default' 	=> false,
					);	
			
			/*Acrhive Property Page's*/
			
			$PageOptions = null;
			$PageOptions = $zoner->zoner_get_all_options_translated_pages(array(
				'id'	 	 => 'page-property-archive',
				'title'	 	 => __('Property Loop Page', 'zoner'),
				'subtitle'	 => sprintf( __( 'The base page can also be used in your <a href="%s">property permalinks</a>.', 'zoner' ), admin_url( 'options-permalink.php' ) ),
				'exist_page' => 'properties',
			));
			
			if (!empty($PageOptions)) {
				foreach($PageOptions as $opt) {
					$propertySectionFields[] = $opt;
				}
			}
					
			$propertySectionFields[] = array(
                        'id'        => 'page-property-num-of-posts',
                        'type'      => 'text',
                        'title'     => __('Number property on loop page ', 'zoner'),
                        'default'   => '24'
                    );
					
			$propertySectionFields[] =	array(
						'id'       => 'property-default-orderby',
						'type'     => 'select',
						'title'    => __('Property default catalog ordering', 'zoner'), 
						'subtitle' => __('Select the default sort for properties.' ,'zoner'),
						'std'		=> 'menu_order',
                        'options' => apply_filters('zoner_default_orderby_options', 
									array(
										'menu_order' 	=> __( 'Default sorting', 'zoner'),
										'rating'     	=> __( 'Sort by Rating (asc)',  'zoner'),
										'rating-desc'  	=> __( 'Sort by Rating (desc)',  'zoner'),
									    //'featured'     	=> __( 'Sort by Featured (asc)',  'zoner'),
									    //'featured-desc'	=> __( 'Sort by Featured (desc)',  'zoner'),
										'date'       	=> __( 'Sort by most recent',  'zoner'),
										'price'      	=> __( 'Sort by price (asc)',  'zoner'),
										'price-desc' 	=> __( 'Sort by price (desc)', 'zoner'),
										'rand' 		 	=> __( 'Sort by random', 'zoner' ),
									)
								),
						
                        'default'   => 'menu_order'
					);
					
			$propertySectionFields[] =	array(
						'id'       => 'page-property-grid',
						'type'     => 'select',
						'title'    => __('Property Grid Type', 'zoner'), 
						'subtitle' => __('Select the type of grid to display items.' ,'zoner'),
						'std'		=> '1',
                        'options' => array(
							'1'  => __( 'Masonry listing', 'zoner'),
							'2'	 => __( 'Grid listing', 'zoner'),
							'3'  => __( 'Lines listing', 'zoner')
						),
                        'default'   => '1'
					);
					
			$propertySectionFields[] =	array(
                        'id'        => 'page-property-excerpt-limit',
                        'type'      => 'text',
                        'title'     => __('Words limit', 'zoner'),
						'subtitle' => __('Choose words count to change excerpt length.' ,'zoner'),
                        'default'   => '22'
                    );
					
			$propertySectionFields[] = array(
						'id'        => 'area-unit',
						'type'      => 'select',
						'title'     => __('Property area units', 'zoner'),
						'subtitle'  => __('Choose default area units.', 'zoner'),
						'options'	=> $zoner->property->get_area_units_values(),
						'default'   => 0,
					);

			$propertySectionFields[] = array(
						'id'       => 'prop-enabling-fields',
						'type'     => 'sorter',
						'title'    => __('Property fields', 'zoner'),
						'subtitle' => __('Choose enabling fields on add/edit property page', 'zoner'),
						'compiler' => 'true',
						'options'  => array(
							'enabled'  => apply_filters('zoner_add_property_front_enabled_fields', array(
								'description' => __('Description',  'zoner'),
								'country' 	 => __('Country', 'zoner'),
								'state' 	 => __('State', 'zoner'),
								'city' 	 	 => _x( 'Town / City', 'Town / City', 'zoner' ),
								'zip' 		 => __('Postcode / Zip', 'zoner'),
								'district'	 => __('District',  'zoner'),
								'condition'	 => __('Condition', 'zoner'),
								'payment'	 => __('Payment interval', 'zoner'),
								'type'	 	 => __('Property Type', 'zoner'),
								'status' 	 => __('Status', 'zoner'),
								'rooms'	 	 => __('Rooms', 'zoner'),
								'beds'	 	 => __('Beds', 'zoner'),
								'baths'	 	 => __('Baths', 'zoner'),
								'garages' 	 => __('Garages', 'zoner'),
								'area_units' => __('Area units', 'zoner'),
								'rating'     => __('User rating', 'zoner'),
								'featured_image'  => __('Image', 'zoner'),
								'files' 	 => __('Files', 'zoner'),
								'gallery' 	 => __('Gallery', 'zoner'),
								'floor_plans' => __('Floor Plans', 'zoner'),
								'video' => __('Video Presentations', 'zoner'),
							)),
							'disabled' => apply_filters('zoner_add_property_front_disabled_fields', array(
							)),
						),
					);

			$propertySectionFields[] = array(
						'id' 	=> 'similar-property',
						'type' 	=> 'section',
						'title' => __('Similar property', 'zoner'),
						'indent' => true 
					);
					
			$propertySectionFields[] = array(
                        'id'        => 'show-by-type',
                        'type'      => 'checkbox',
                        'title'     => __('Filter by type', 'zoner'),
                        'subtitle'  => __('Choose to display property by type.', 'zoner'),
                        'desc'      => __('Yes', 'zoner'),
						'class'		=> 'icheck',
						'default'   => '1'
                    );
					
			$propertySectionFields[] = array(
                        'id'        => 'show-by-status',
                        'type'      => 'checkbox',
                        'title'     => __('Filter by status', 'zoner'),
                        'subtitle'  => __('Choose to display property by status.', 'zoner'),
                        'desc'      => __('Yes', 'zoner'),
						'class'		=> 'icheck',
						'default'   => '1'
            );
					
			$propertySectionFields[] = array(
                        'id'        => 'show-by-country',
                        'type'      => 'checkbox',
                        'title'     => __('Filter by country', 'zoner'),
                        'subtitle'  => __('Choose to display property by country.', 'zoner'),
                        'desc'      => __('Yes', 'zoner'),
						'class'		=> 'icheck',
						'default'   => '0'
                    );
					
			$propertySectionFields[] = array(
                        'id'        => 'show-by-state',
                        'type'      => 'checkbox',
                        'title'     => __('Filter by state', 'zoner'),
                        'subtitle'  => __('Choose to display property by state.', 'zoner'),
                        'desc'      => __('Yes', 'zoner'),
						'class'		=> 'icheck',
						'default'   => '0'
                    );
					
			$propertySectionFields[] = array(
                        'id'        => 'show-by-city',
                        'type'      => 'checkbox',
                        'title'     => __('Filter by city', 'zoner'),
                        'subtitle'  => __('Choose to display property by city.', 'zoner'),
                        'desc'      => __('Yes', 'zoner'),
						'class'		=> 'icheck',
						'default'   => '1'
                    );
					
			$propertySectionFields[] = array(
                        'id'        => 'show-by-cat',
                        'type'      => 'checkbox',
                        'title'     => __('Filter by category', 'zoner'),
                        'subtitle'  => __('Choose to display property by category.', 'zoner'),
                        'desc'      => __('Yes', 'zoner'),
						'class'		=> 'icheck',
						'default'   => '1'
                    );
					
			$propertySectionFields[] = array(
						'id' 	=> 'currency-options',
						'type' 	=> 'section',
						'title' => __('Currency Options', 'zoner'),
						'indent' => true 
					);
					
			$propertySectionFields[] = array(
                        'id'        => 'currency',
                        'type'      => 'select',
                        'title'     => __('Currency', 'zoner'),
                        'subtitle'  => __('Choose default currency symbol.', 'zoner'),
						'std'		=> 'USD',
                        'options'   => $zoner->currency->get_zoner_currency_dropdown_settings(),
						'default'	=> 'USD',
					);
					
			$propertySectionFields[] = array(
                        'id'        => 'currency-position',
                        'type'      => 'select',
                        'title'     => __('Currency Position', 'zoner'),
                        'subtitle'  => __('Choose position for currency symbol.', 'zoner'),
						'std'		=> 'left',
                        'options' => array(
							'left'        => __( 'Left', 'zoner' ) . ' ($99.99)',
							'right'       => __( 'Right', 'zoner' ) . ' (99.99$)',
							'left_space'  => __( 'Left with space', 'zoner' ) . ' ($ 99.99)',
							'right_space' => __( 'Right with space', 'zoner' ) . ' (99.99 $)'
						),
                        'default'   => 'left'
                    );
					
			$propertySectionFields[] = array(
                        'id'        => 'thousand-sep',
                        'type'      => 'text',
                        'title'     => __('Thousand Separator', 'zoner'),
                        'default'   => ','
                    );
					
			$propertySectionFields[] = array(
                        'id'        => 'decimal-sep',
                        'type'      => 'text',
                        'title'     => __('Decimal Separator', 'zoner'),
                        'default'   => '.'
                    );

			$propertySectionFields[] = array(
                        'id'        => 'number-decimal',
                        'type'      => 'spinner',
                        'title'     => __('Number of Decimals', 'zoner'),
                        'default'   => '2',
                        'min'       => '0',
                        'step'      => '1',
                        'max'       => '10',
                    );
					
			$propertySectionFields[] = array(
                        'id'        => 'profile-localization-currency',
                        'type'      => 'checkbox',
                        'title'     => __('Allow the user to use currency localization', 'zoner'),
                        'subtitle'  => __('Choose flag to allow', 'zoner'),
                        'desc'      => __('Yes', 'zoner'),
						'class'		=> 'icheck',
						'default'   => '0'
                    );
					
			$propertySectionFields[] = array(
						'id' 	=> 'cs-options',
						'type' 	=> 'section',
						'title' => __('Base Country', 'zoner'),
						'indent' => true 
					);
					
			$propertySectionFields[] = array(
                        'id'        => 'default-country',
                        'type'      => 'select',
                        'title'     => __('Country', 'zoner'),
                        'subtitle'  => __('Choose default country.', 'zoner'),
						'std'		=> 'US',
                        'options' => $zoner->countries->countries,
                        'default'   => 'US',
						'placeholder' => __('Select country', 'zoner')
                    );
			
			/*-----------TIPS IN INSERT|UPDATE PROPERTIES---------------*/
			$propertySectionFields[] = array(
						'id' 	=> 'tips-options',
						'type' 	=> 'section',
						'title' => __('Edit property tips ', 'zoner'),
						'indent' => true
					);

			$propertySectionFields[] = array(
						'id'        => 'tips-header-fields-update',
						'type'      => 'text',
						'title'     => __('Edit property tips header', 'zoner'),
						'subtitle'  => __('Header for edit property page tips in field (1).', 'zoner'),
						'default'   => 'Edit Property fields'
					);
			$propertySectionFields[] = array(
						'id'        => 'tips-text-fields-update',
						'type'      => 'textarea',
						'title'     => __('Edit property tips text', 'zoner'),
						'subtitle'  => __('Text for edit property page tips in field (1).', 'zoner'),
						'default'   => 'Carefully check entered information and than click button to update them.'
					);
			$propertySectionFields[] = array(
						'id'        => 'tips-header-fields-insert',
						'type'      => 'text',
						'title'     => __('Add property tips header', 'zoner'),
						'subtitle'  => __('Header for add property page tips in field (1).', 'zoner'),
						'default'   => 'Edit Property fields'
					);
			
			$propertySectionFields[] = array(
						'id'        => 'tips-text-fields-insert',
						'type'      => 'textarea',
						'title'     => __('Add property tips text', 'zoner'),
						'subtitle'  => __('Text for add property page tips in field (1).', 'zoner'),
						'default'   => 'Carefully check entered information and than click button to submit them.'
					);
			
			$propertySectionFields[] = array(
						'id'        => 'tips-header-submit-update',
						'type'      => 'text',
						'title'     => __('Submit edit property header tips', 'zoner'),
						'subtitle'  => __('Header for edit property page tips in field (2).', 'zoner'),
						'default'   => 'Update Property'
					);
					
			$propertySectionFields[] = array(
						'id'        => 'tips-text-submit-update',
						'type'      => 'textarea',
						'title'     => __('Submit edit property text tips', 'zoner'),
						'subtitle'  => __('Text for edit property page tips in field (2).', 'zoner'),
						'default'   => 'Carefully check entered information and than click button to update them.'
					);
			
			$propertySectionFields[] = array(
						'id'        => 'tips-header-submit-insert',
						'type'      => 'text',
						'title'     => __('Submit add property header tips', 'zoner'),
						'subtitle'  => __('Header for add property page tips in field (2).', 'zoner'),
						'default'   => 'Submit Property'
					);
			$propertySectionFields[] = array(
						'id'        => 'tips-text-submit-insert',
						'type'      => 'textarea',
						'title'     => __('Submit add property text tip', 'zoner'),
						'subtitle'  => __('Text for add property page tips in field (2).', 'zoner'),
						'default'   => 'Carefully check entered information and than click button to submit them.'
					);
			/*-----------END TIPS IN INSERT|UPDATE PROPERTIES---------------*/
			
			/*Property Section*/
			$this->sections[] = array(
                'title'     => __('Property', 'zoner'),
                'icon'      => $sample_patterns_url . 'images/icons/property-options.png',
				'icon_type' => 'image',
				'fields'    => $propertySectionFields
			);
			
			/*Membership packages*/
			$this->sections[] = array(
                'title'     => __('Membership', 'zoner'),
                'icon'      => $sample_patterns_url . 'images/icons/membership.png',
				'icon_type' => 'image',
				'fields'    => array(
						
						array(
							'id'        => 'adminbar-displayed',
							'type'      => 'select',
							'title'     => __('Admin bar display', 'zoner'),
							'subtitle'  => __('Choose options for admin bar displaying.', 'zoner'),
							'std'		=> '1',
							'options' 	=> array(
												'1' => __('Display for all', 'zoner'),
												'2' => __('For admin only', 'zoner'),
												'3' => __('Off', 'zoner'),
									
											),
							'default'   => '1',
							'placeholder' => __('Select admin bar options', 'zoner')
						),
					
						
						array(
							'id'        => 'paid-system',
							'type'      => 'switch',
							'title'     => __('Enable Paid System', 'zoner'),
							'default'   =>  0,
							'on'        => 'On',
							'off'       => 'Off',
						),
						
						array(
							'id'        => 'paid-type-properties',
							'type'      => 'select',
							'title'     => __('Paid type', 'zoner'),
							'options'   => array (
													'0' => __('Membership', 'zoner'),
													'1' => __('Pay for each Property', 'zoner')
												 ),
							'default'	=> '0',
							'std'		=> '0',
							'required'  => array('paid-system', '=', '1'),
						),
						
						array(
							'id'        => 'price-per-property',
							'type'      => 'text',
							'title'     => __('Price per submit', 'zoner'),
							'default'   => '10',
							'required'  => array(
									array('paid-system', '=', '1'),
									array('paid-type-properties', '=', '1'),
							),
						),
						
						
						array(
							'id'        => 'price-per-featured-property',
							'type'      => 'text',
							'title'     => __('Price per featured', 'zoner'),
							'default'   => '5',
							'required'  => array(
									array('paid-system', '=', '1'),
									array('paid-type-properties', '=', '1'),
							),
						),
						
						array(
							'id'        => 'paid-api-method',
							'type'      => 'select',
							'title'     => __('PayPal & Stripe API method', 'zoner'),
							'options'   => array (
													'sandbox' => __('SandBox', 'zoner'),
													'live' 	  => __('Live', 'zoner')
												 ),
							'default'	=> 'sandbox',
							'std'		=> 'sandbox',
							'required'  => array('paid-system', '=', '1'),
						),
					
						array(
							'id'        => 'paid-currency',
							'type'      => 'select',
							'title'     => __('Currency For Paid Submission', 'zoner'),
							'std'		=> 'USD',
							'options'   => $zoner->membership->get_available_currency_paid_values(),
							'default'	=> 'USD',
							'required'  => array('paid-system', '=', '1'),
						),
						/*Free package section*/
						
						array(
							'id' 		=> 'free-package-start',
							'type'  	=> 'section',
							'title' 	=> __('Free package', 'redux-framework-demo'),
							'indent' 	=> true ,
							'required'  => array(
									array('paid-system', '=', '1'),
									array('paid-type-properties', '=', '0'),
							),
						),
						
						array(
							'id'        => 'free-package-name',
							'type'      => 'text',
							'title'     => __('Package name', 'zoner'),
							'default'   => __('Free', 'zoner'),
							'required'  => array(
									array('paid-system', '=', '1'),
									array('paid-type-properties', '=', '0'),
							),
						),
						
						array(
							'id'        => 'free-limit-properties',
							'type'      => 'text',
							'title'     => __('How many properties are included?', 'zoner'),
							'default'   => '20',
							'required'  => array(
									array('paid-system', '=', '1'),
									array('paid-type-properties', '=', '0'),
							),
						),
						
						array(
							'id'        => 'free-limit-featured',
							'type'      => 'text',
							'title'     => __('How many featured properties are included?', 'zoner'),
							'default'   => '2',
							'required'  => array(
									array('paid-system', '=', '1'),
									array('paid-type-properties', '=', '0'),
							),
						),
						
						array(
							'id'        => 'free-unlimited-properties',
							'type'      => 'checkbox',
							'title' 	=> __('Unlimited properties', 'zoner'),
							'desc'      => __('Yes', 'zoner'),
							'class'		=> 'icheck',
							'required'  => array(
									array('paid-system', '=', '1'),
									array('paid-type-properties', '=', '0'),
							),
						),		
						
						array(
							'id'        => 'free-unlimited-featured',
							'type'      => 'checkbox',
							'title' 	=> __('Unlimited featured properties', 'zoner'),
							'desc'      => __('Yes', 'zoner'),
							'class'		=> 'icheck',
							'required'  => array(
									array('paid-system', '=', '1'),
									array('paid-type-properties', '=', '0'),
							),
						),		
						
						array(
							'id'        => 'free-add-agency',
							'type'      => 'checkbox',
							'title' 	=> __('Create agency', 'zoner'),
							'desc'      => __('Yes', 'zoner'),
							'class'		=> 'icheck',
							'required'  => array(
									array('paid-system', '=', '1'),
									array('paid-type-properties', '=', '0'),
							),
						),		
						
						array(
							'id'        => 'free-available',
							'type'      => 'checkbox',
							'title' 	=> __('Available package', 'zoner'),
							'desc'      => __('Yes', 'zoner'),
							'class'		=> 'icheck',
							'required'  => array(
									array('paid-system', '=', '1'),
									array('paid-type-properties', '=', '0'),
							),
							'default'   => true,
						),		
						
						array(
							'id'     => 'free-package-end',
							'type'   => 'section',
							'indent' => false,
							'required'  => array(
									array('paid-system', '=', '1'),
									array('paid-type-properties', '=', '0'),
							),
						),
						/*BACS Options*/
						  array(
				              'id'        => 'bacs-system',
				              'type'      => 'switch',
				              'title'     => __('Enable BACS System', 'zoner'),
				              'default'   =>  0,
				              'on'        => 'On',
				              'off'       => 'Off',
				              'required'  => array('paid-system', '=', '1'),
				             ),
				            array(
				              'id'        => 'bacs-account-name',
				              'type'      => 'text',
				              'title'     => __('Account name', 'zoner'),
				              'required'  => array(
				                  array('bacs-system', '=', '1')
				              ),
				            ),
				            array(
				              'id'        => 'bacs-account-num',
				              'type'      => 'text',
				              'title'     => __('Account number', 'zoner'),
				              'required'  => array(
				                  array('bacs-system', '=', '1')
				              ),
				            ),
				            array(
				              'id'        => 'bacs-bank-name',
				              'type'      => 'text',
				              'title'     => __('Bank name', 'zoner'),
				              'description'=>__('Example: 10-19-20', 'zoner'),
				              'required'  => array(
				                  array('bacs-system', '=', '1')
				              ),
				            ),
				            array(
				              'id'        => 'bacs-sort-code',
				              'type'      => 'text',
				              'title'     => __('Sort code', 'zoner'),
				              'description'=>__('Example: 10-19-20', 'zoner'),
				              'required'  => array(
				                  array('bacs-system', '=', '1')
				              ),
				            ),
				            array(
				              'id'        => 'bacs-iban',
				              'type'      => 'text',
				              'title'     => __('IBAN', 'zoner'),
				              'description'=>__('Example: GB-19-LOYD30961700709943', 'zoner'),
				              'required'  => array(
				                  array('bacs-system', '=', '1')
				              ),
				            ),
				            array(
				              'id'        => 'bacs-swift',
				              'type'      => 'text',
				              'title'     => __('BIC / Swift', 'zoner'),
				              'description'=>__('Example: UBSWUS33CHI', 'zoner'),
				              'required'  => array(
				                  array('bacs-system', '=', '1')
				              ),
				            ),
						
						/*PayPal Options*/
						array(
							'id'        => 'membership-paypal',
							'type'      => 'switch',
							'title'     => __('Enable PayPal', 'zoner'),
							'default'   =>  0,
							'on'        => 'On',
							'off'       => 'Off',
							'required'  => array('paid-system', '=', '1'),
						),
						
						array(
							'id'        => 'paypal-api-username',
							'type'      => 'text',
							'title'     => __('PayPal API User Name', 'zoner'),
							'default'   => '',
							'required'  => array('membership-paypal', '=', '1'),
						),
						
						array(
							'id'        	=> 'paypal-api-password',
							'type'      	=> 'password',
							'title'     	=> __('PayPal API Password ', 'zoner'),
							'default'   	=> '',
							'required'  => array('membership-paypal', '=', '1'),
						),
						
						array(
							'id'        => 'paypal-api-signature',
							'type'      => 'text',
							'title'     => __('PayPal API Signature', 'zoner'),
							'default'   => '',
							'required'  => array('membership-paypal', '=', '1'),
						),

						/*Stripe Options*/
						
						array(
							'id'        => 'membership-stripe',
							'type'      => 'switch',
							'title'     => __('Enable Stripe', 'zoner'),
							'default'   =>  0,
							'on'        => 'On',
							'off'       => 'Off',
							'required'  => array('paid-system', '=', '1'),
						),
						
						array(
							'id'        => 'stripe-secret-key',
							'type'      => 'text',
							'title'     => __('Stripe Secret Key', 'zoner'),
							'default'   => '',
							'required'  => array('membership-stripe', '=', '1'),
						),
						
						array(
							'id'        => 'stripe-publishable-key',
							'type'      => 'text',
							'title'     => __('Stripe Publishable Key', 'zoner'),
							'default'   => '',
							'required'  => array('membership-stripe', '=', '1'),
						),
						
					
						
				)
			);	
			
			
			
			/*Bookmarks properties by user*/
			$bookmark_content = '';
			$bookmark_content = $this->getBookmarkUserContent();
			
			$bookmark_sections_fileds   = array();
			if (!empty($bookmark_content)) {
				$bookmark_sections_fileds[] = array(
                                'id'       => 'bookmarks-property',
                                'type'     => 'raw',
                                'markdown' => true,
                                'content'  => $bookmark_content
                            );
			} else {
				$bookmark_sections_fileds[] = array(
								'id'    => 'info_bookmarks',
								'type'  => 'info',
								'title' => __('Upps!', 'zoner'),
								'style' => 'info',
								'desc'  => __('Nothing is added to bookmarks.', 'zoner')
							);
			}
			
			$this->sections[] = array(
				'title'     => __('Bookmarks', 'zoner'),
				'icon'      => $sample_patterns_url . 'images/icons/heart.png',
				'icon_type' => 'image',
				'fields' 	=> $bookmark_sections_fileds
                );
					
			
			/*Email Setting*/
			$this->sections[] = array(
                'title'     => __('Email', 'zoner'),
                'icon'      => $sample_patterns_url . 'images/icons/email-options.png',
				'icon_type' => 'image',
				'fields'    => array(
					
					array(
                        'id'        => 'emails-from-name',
                        'type'      => 'text',
                        'title'     => __('From Name', 'zoner'),
                        'default'   => get_bloginfo('name')
                    ),
					
					array(
                        'id'        => 'emails-from-email',
                        'type'      => 'text',
                        'title'     => __('From email address', 'zoner'),
                        'default'   => get_bloginfo('admin_email'),
						'validate' => 'email'
                    ),
					
					array(
                        'id'        => 'emails-footer-text',
                        'type'      => 'textarea',
                        'title'     => __('Footer text', 'zoner'),
                        'default'   => sprintf(__("With respect, \nteam %1s", 'zoner'), get_bloginfo( 'name' ))
                    ),
					
					array(
                        'id'        => 'emails-smtp',
                        'type'      => 'switch',
                        'title'     => __('Additional SMTP Settings', 'zoner'),
                        'default'   =>  0,
                        'on'        => 'On',
                        'off'       => 'Off',
                    ),
					
					array(
                        'id'        => 'emails-smtp-host',
                        'type'      => 'text',
                        'title'     => __('SMTP Host', 'zoner'),
                        'default'   => 'smtp.example.com',
						'required'  => array('emails-smtp', '=', '1'),
                    ),
					
					array(
                        'id'        => 'emails-smtp-port',
                        'type'      => 'text',
                        'title'     => __('SMTP Port', 'zoner'),
                        'default'   => 25,
						'required'  => array('emails-smtp', '=', '1'),
                    ),
					
					array(
						'id'       => 'emails-type-enc',
						'type'     => 'radio',
						'title'    => __('Type of Encription', 'zoner'), 
						'options'  => array(
												'1' => __('None', 'zoner'), 
												'2' => __('SSL', 'zoner'),  
												'3' => __('TLS', 'zoner'), 
						),
						'default' => '1',
						'required'  => array('emails-smtp', '=', '1'),
					),
					
					array(
                        'id'        => 'emails-authentication',
                        'type'      => 'switch',
                        'title'     => __('SMTP Authentication', 'zoner'),
                        'default'   =>  1,
                        'on'        => 'On',
                        'off'       => 'Off',
						'required'  => array('emails-smtp', '=', '1'),
                    ),
					
					array(
                        'id'        => 'emails-smtp-user',
                        'type'      => 'text',
                        'title'     => __('SMTP Username', 'zoner'),
                        'default'   => '',
						'required'  => array(
								array('emails-authentication', '=', '1'),
								array('emails-smtp', '=', '1'),
							),
                    ),
					
					array(
						'id'        	=> 'emails-smtp-pass',
						'type'      	=> 'password',
						'title'     	=> __('SMTP Password ', 'zoner'),
						'default'   	=> '',
						'required'  => array(
								array('emails-authentication', '=', '1'),
								array('emails-smtp', '=', '1'),
							),
					),
					
				)
			);	
			
			/*Social Logins*/
			$this->sections[] = array(
                'title'     => __('Social connect', 'zoner'),
                'icon'      => $sample_patterns_url . 'images/icons/social-connect.png',
				'icon_type' => 'image',
				'fields'    => array(
					
					/*Facebook connect*/
					array(
						'id' 	=> 'facebook-connect',
						'type' 	=> 'section',
						'title' => __('Facebook', 'zoner'),
						'indent' => true 
					),					
					
					array(
                        'id'        => 'facebook-api-key',
                        'type'      => 'text',
                        'title'     => __('App ID', 'zoner'),
                    ),
					
					array(
                        'id'        => 'facebook-secret-code',
                        'type'      => 'text',
                        'title'     => __('Facebook secret code', 'zoner'),
                    ),

					
					/*Google connect*/
					array(
						'id' 	=> 'google-connect',
						'type' 	=> 'section',
						'title' => __('Google', 'zoner'),
						'indent' => true 
					),					
					
					array(
                        'id'        => 'google-oauth-client-id',
                        'type'      => 'text',
                        'title'     => __('Google OAuth client id', 'zoner'),
                    ),
					
					array(
                        'id'        => 'google-client-secret',
                        'type'      => 'text',
                        'title'     => __('Google Client Secret', 'zoner'),
                    ),
					
					array(
                        'id'        => 'google-api-key',
                        'type'      => 'text',
                        'title'     => __('Google Api key', 'zoner'),
                    ),
				)
			);	
				
			/*Social Liks*/
			$this->sections[] = array(
                'title'     => __('Social Links', 'zoner'),
                'desc'      => __('Add link to your social media profiles. Icons with link will be display in footer.', 'zoner'),
                'icon'      => $sample_patterns_url . 'images/icons/social-links.png',
				'icon_type' => 'image',
				'subsection' => true,
				'fields'    => array(
					array(
                        'id'        => 'facebook-url',
                        'type'      => 'text',
                        'title'     => __('Facebook', 'zoner'),
                        'validate'  => 'url',
                        'default'   => ''
                    ),
					array(
                        'id'        => 'twitter-url',
                        'type'      => 'text',
                        'title'     => __('Twitter', 'zoner'),
                        'validate'  => 'url',
                        'default'   => ''
                    ),
					array(
                        'id'        => 'linkedin-url',
                        'type'      => 'text',
                        'title'     => __('LinkedIn', 'zoner'),
                        'validate'  => 'url',
                        'default'   => ''
                    ),
					array(
                        'id'        => 'myspace-url',
                        'type'      => 'text',
                        'title'     => __('MySpace', 'zoner'),
                        'validate'  => 'url',
                        'default'   => ''
                    ),
					array(
                        'id'        => 'gplus-url',
                        'type'      => 'text',
                        'title'     => __('Google Plus+', 'zoner'),
                        'validate'  => 'url',
                        'default'   => ''
                    ),
					array(
                        'id'        => 'dribbble-url',
                        'type'      => 'text',
                        'title'     => __('Dribbble', 'zoner'),
                        'validate'  => 'url',
                        'default'   => ''
                    ),
					array(
                        'id'        => 'flickr-url',
                        'type'      => 'text',
                        'title'     => __('Flickr', 'zoner'),
                        'validate'  => 'url',
                        'default'   => ''
                    ),
					array(
                        'id'        => 'youtube-url',
                        'type'      => 'text',
                        'title'     => __('You Tube', 'zoner'),
                        'validate'  => 'url',
                        'default'   => ''
                    ),
					array(
                        'id'        => 'delicious-url',
                        'type'      => 'text',
                        'title'     => __('Delicious', 'zoner'),
                        'validate'  => 'url',
                        'default'   => ''
                    ),
					array(
                        'id'        => 'deviantart-url',
                        'type'      => 'text',
                        'title'     => __('Deviantart', 'zoner'),
                        'validate'  => 'url',
                        'default'   => ''
                    ),
					array(
                        'id'        => 'rss-url',
                        'type'      => 'text',
                        'title'     => __('RSS', 'zoner'),
                        'validate'  => 'url',
                        'default'   => ''
                    ),
					array(
                        'id'        => 'instagram-url',
                        'type'      => 'text',
                        'title'     => __('Instagram', 'zoner'),
                        'validate'  => 'url',
                        'default'   => ''
                    ),
					array(
                        'id'        => 'pinterest-url',
                        'type'      => 'text',
                        'title'     => __('Pinterest', 'zoner'),
                        'validate'  => 'url',
                        'default'   => ''
                    ),
					array(
                        'id'        => 'vimeo-url',
                        'type'      => 'text',
                        'title'     => __('Vimeo', 'zoner'),
                        'validate'  => 'url',
                        'default'   => ''
                    ),
					array(
                        'id'        => 'picassa-url',
                        'type'      => 'text',
                        'title'     => __('Picassa', 'zoner'),
                        'validate'  => 'url',
                        'default'   => ''
                    ),
					array(
                        'id'        => 'tumblr-url',
                        'type'      => 'text',
                        'title'     => __('Tumblr', 'zoner'),
                        'validate'  => 'url',
                        'default'   => ''
                    ),
					array(
                        'id'        => 'email-address',
                        'type'      => 'text',
                        'title'     => __('E-mail', 'zoner'),
                        'validate'  => 'email',
                        'msg'       => 'custom error message',
                        'default'   => ''
                    ),
					array(
                        'id'        => 'skype-username',
                        'type'      => 'text',
                        'title'     => __('Skype', 'zoner'),
                        'default'   => ''
                    ),
				)	
			);	
			
			
			/*Default Page*/
			
			$DefPages = null;
			$DefPages[] = array(
                        'id'        => 'pp-agency-archive-layout',
                        'type'      => 'image_select',
                        'title'     => __('Archive agency layout', 'zoner'),
                        'subtitle'  => __('Select main content and sidebar alignment.', 'zoner'),
                        'options'   => ((class_exists('ReduxFrameworkPlugin'))?(array(
								'1' => array('alt' => '1 Column',       'img' => ReduxFramework::$_url . 'assets/img/1col.png'),
								'2' => array('alt' => '2 Column Left',  'img' => ReduxFramework::$_url . 'assets/img/2cl.png'),
								'3' => array('alt' => '2 Column Right', 'img' => ReduxFramework::$_url . 'assets/img/2cr.png'),
							)):0),
                        'default'   => '3'
                    );
					
			$DefPages[] = array(
                        'id'        => 'pp-author-agents-layout',
                        'type'      => 'image_select',
                        'title'     => __('Author & Agent layout', 'zoner'),
                        'subtitle'  => __('Select main content and sidebar alignment.', 'zoner'),
                        'options'   => ((class_exists('ReduxFrameworkPlugin'))?(array(
								'1' => array('alt' => '1 Column',       'img' => ReduxFramework::$_url . 'assets/img/1col.png'),
								'2' => array('alt' => '2 Column Left',  'img' => ReduxFramework::$_url . 'assets/img/2cl.png'),
								'3' => array('alt' => '2 Column Right', 'img' => ReduxFramework::$_url . 'assets/img/2cr.png'),
							)):0),
                        'default'   => '3'
                    );
					
			$DefPages[] = array(
                        'id'        => '404-image',
                        'type'      => 'media',
                        'title'     => __('404 background.', 'zoner'),
                        'subtitle'  => __('Upload a background for your 404 page.', 'zoner'),
						'default'   => array('url' =>  $sample_patterns_url . 'images/error-page-background.png'),
                    );
					
			$DefPages[] = array(
                        'id'        => '404-text',
                        'type'      => 'text',
                        'title'     => __('404 text', 'zoner'),
                        'default'   => '404'
                    );
			
			/*Adding Property*/	
			
			$PageOptions = null;
			$PageOptions = $zoner->zoner_get_all_options_translated_pages(array(
				'id'	 		=> 'page-tasp',
				'title'	 		=> __('"Thank You" Page after adding property', 'zoner'), 
				'subtitle'		=> __('Select the page after the user has added his property.', 'zoner'),
				'exist_page' 	=> 'thank-after-submit-property'
			));
			
			if (!empty($PageOptions)) {
				foreach($PageOptions as $opt) {
					$DefPages[] = $opt;
				}
			}
			
			/*Adding Agency*/	
			
			$PageOptions = null;
			$PageOptions = $zoner->zoner_get_all_options_translated_pages(array(
				'id'	 		=> 'page-tasa',
				'title'	 		=> __('"Thank You" Page after adding agency', 'zoner'), 
				'subtitle'		=> __('Select the page after the user has added his agency.', 'zoner'),
				'exist_page' 	=> 'thank-after-submit-agency'
			));
			
			if (!empty($PageOptions)) {
				foreach($PageOptions as $opt) {
					$DefPages[] = $opt;
				}
			}
			
			$this->sections[] = array(
                'title'     => __('Default pages', 'zoner'),
                'icon'      => $sample_patterns_url . 'images/icons/default-pages.png',
				'icon_type' => 'image',
				'fields'    => $DefPages
			);	
				
			/*Footer Section*/
			$this->sections[] = array(
                'title'     => __('Footer', 'zoner'),
                'icon'      => $sample_patterns_url . 'images/icons/footer.png',
				'icon_type' => 'image',
				'fields'    => array(
					
					array(
                        'id'        => 'footer-text',
                        'type'      => 'editor',
                        'title'     => __('Copyright section', 'zoner'),
                        'subtitle'  => __('Replace default theme copyright information and links', 'zoner'),
                        'default'   => '&#169; <a target="_blank" title="Web Design" href="http://www.theme-starz.com/">Theme Starz</a> &amp; <a target="_blank" title="WordPress Development" href="http://fruitfulcode.com/">Fruitful Code</a>, Powered by <a target="_blank" href="http://wordpress.org/">WordPress</a>',
                    ),
					
					array(
                        'id'        => 'footer-issocial',
                        'type'      => 'checkbox',
                        'title'     => __('Social icons', 'zoner'),
                        'desc'      => __('Enable social icons.', 'zoner'),
                        'default'   => '1',
						'class'		=> 'icheck',
                    ),
					
					array(
                        'id'        => 'footer-widget-areas',
                        'type'      => 'image_select',
                        'title'     => __('Footer column areas', 'zoner'),
                        'options'   => array(
                            '0' => array('alt' => 'No widgets areas.',  'img' => $sample_patterns_url . 'images/footer-widgets-0.png'),
                            '1' => array('alt' => '1 column area.', 	'img' => $sample_patterns_url . 'images/footer-widgets-1.png'),
							'2' => array('alt' => '2 column area.', 	'img' => $sample_patterns_url . 'images/footer-widgets-2.png'),
							'3' => array('alt' => '3 column area.', 	'img' => $sample_patterns_url . 'images/footer-widgets-3.png'),
							'4' => array('alt' => '4 column area.', 	'img' => $sample_patterns_url . 'images/footer-widgets-4.png')
                        ), 
                        'default' => '4'
                    ),
					
					
					array(
                        'id'        => 'switch-footer-thumbnails',
                        'type'      => 'switch',
                        'title'     => __('Enable Footer Property Thumbnails', 'zoner'),
                        'default'   =>  1,
                        'on'        => 'On',
                        'off'       => 'Off',
                    ),
					
					array(
                        'id'        => 'footer-thumbnails-mask-color',
                        'type'      => 'color',
                        'title'     => __('Footer copyright part thumbnails mask color', 'zoner'),
                        'default'   => '#1396e2',
                        'validate'  => 'color',
						'transparent'	=> false,
						'required'  => array('switch-footer-thumbnails', '=', '1'),
                    ),
					
					array(
                        'id'        => 'footer-copyright-color',
                        'type'      => 'color',
                        'title'     => __('Footer copyright part font color', 'zoner'),
                        'default'   => '#ffffff',
                        'validate'  => 'color',
						'transparent'	=> false
                    ),
					
					array(
                        'id'        => 'footer-copyright-bg-color',
                        'type'      => 'color',
                        'title'     => __('Footer copyright part background color', 'zoner'),
                        'default'   => '#073855',
                        'validate'  => 'color',
						'transparent'	=> false
                    ),
				)
			);
				
			
			/*Custom Section*/
			$this->sections[] = array(
                'title'     => __('Custom Code', 'zoner'),
                'icon'      => $sample_patterns_url . 'images/icons/custom-code.png',
				'icon_type' => 'image',
                'fields'    => array (
					array(
                        'id'        => 'custom-css',
                        'type'      => 'ace_editor',
                        'title'     => __('CSS Code', 'zoner'),
                        'subtitle'  => __('Paste your CSS code here.', 'zoner'),
                        'mode'      => 'css',
                        'theme'     => 'chrome',
                        'desc'      => '',
                        'default'   => ""
                    ),
					array(
                        'id'        => 'custom-js',
                        'type'      => 'ace_editor',
                        'title'     => __('JS Code', 'zoner'),
                        'subtitle'  => __('Paste your JS code here.', 'zoner'),
                        'mode'      => 'javascript',
                        'theme'     => 'chrome',
                        'desc'      => '',
                        'default'   => ""
                    ),
				)
			);
			
		
            $theme_info  = '<div class="redux-framework-section-desc">';
				$theme_info .= '<p class="redux-framework-theme-data description theme-uri">' . __('<strong>Theme URL:</strong> ', 'zoner') . '<a href="' . $this->theme->get('ThemeURI') . '" target="_blank">' . $this->theme->get('ThemeURI') . '</a></p>';
				$theme_info .= '<p class="redux-framework-theme-data description theme-author">' . __('<strong>Author:</strong> ', 'zoner') . $this->theme->get('Author') . '</p>';
				$theme_info .= '<p class="redux-framework-theme-data description theme-version">' . __('<strong>Version:</strong> ', 'zoner') . $this->theme->get('Version') . '</p>';
				$theme_info .= '<p class="redux-framework-theme-data description theme-description">' . $this->theme->get('Description') . '</p>';
				$tabs = $this->theme->get('Tags');
				if (!empty($tabs)) {
					$theme_info .= '<p class="redux-framework-theme-data description theme-tags">' . __('<strong>Tags:</strong> ', 'zoner') . implode(', ', $tabs) . '</p>';
				}
            $theme_info .= '</div>';

            if (file_exists(dirname(__FILE__) . '/../README.md')) {
                $this->sections['theme_docs'] = array(
                    'icon'      => 'el-icon-list-alt',
                    'title'     => __('Documentation', 'zoner'),
                    'fields'    => array(
                        array(
                            'id'        => '17',
                            'type'      => 'raw',
                            'markdown'  => true,
                            'content'   => file_get_contents(dirname(__FILE__) . '/../README.md')
                        ),
                    ),
                );
            }
            
            
            $this->sections[] = array(
                'title'     => __('Import / Export', 'zoner'),
                'desc'      => __('Import and Export your zoner Framework settings from file, text or URL.', 'zoner'),
                'icon'      => $sample_patterns_url . 'images/icons/import-export.png',
				'icon_type' => 'image',
                'fields'    => array(
                    array(
                        'id'            => 'opt-import-export',
                        'type'          => 'import_export',
                        'title'         => 'Import Export',
                        'subtitle'      => 'Save and restore your zoner options',
                        'full_width'    => false,
                    ),
                ),
            );                     
            
			
			$this->sections = apply_filters('zoner_admin_fields', $this->sections);
			
            if (file_exists(trailingslashit(dirname(__FILE__)) . 'README.html')) {
                $tabs['docs'] = array(
                    'icon'      => 'el-icon-book',
                    'title'     => __('Documentation', 'zoner'),
                    'content'   => nl2br(file_get_contents(trailingslashit(dirname(__FILE__)) . 'README.html'))
                );
            }
        }

        public function setHelpTabs() {}
		
        public function setArguments() {
            $theme 		 = wp_get_theme(); 
			$source_path = get_template_directory_uri().'/includes/admin/zoner-options/patterns/';
			
            $this->args = array(
                // TYPICAL -> Change these values as you need/desire
                'opt_name'          => 'zoner_config',        	 // This is where your data is stored in the database and also becomes your global variable name.
                'display_name'      => $theme->get('Name'),      // Name that appears at the top of your panel
                'display_version'   => $theme->get('Version'),   // Version that appears at the top of your panel
                'menu_type'         => 'menu',                   // Specify if the admin menu should appear or not. Options: menu or submenu (Under appearance only)
                'allow_sub_menu'    => false,                    // Show the sections below the admin menu item or not
                'menu_title'        => __('Zoner options', 'zoner'),
                'page_title'        => __('Zoner options', 'zoner'),
                
                // You will need to generate a Google API key to use this feature.
                // Please visit: https://developers.google.com/fonts/docs/developer_api#Auth
                'google_api_key' 	   => '861074126314', // Must be defined to add google fonts to the typography module
				'google_update_weekly' => false,
                
                'async_typography'  => false,                   // Use a asynchronous font on the front end or font string
                'admin_bar'         => true,                    // Show the panel pages on the admin bar
                'global_variable'   => '',                      // Set a different name for your global variable other than the opt_name
                'dev_mode'          => false,                   // Show the time the page took to load, etc
                'customizer'        => false,                   // Enable basic customizer support
				'update_notice'     => true,
        
                // OPTIONAL -> Give you extra features
                'page_priority'     => null,                    // Order where the menu appears in the admin area. If there is any conflict, something will not show. Warning.
                'page_parent'       => 'themes.php',            // For a full list of options, visit: http://codex.wordpress.org/Function_Reference/add_submenu_page#Parameters
                'page_permissions'  => 'manage_options',        // Permissions needed to access the options panel.
                'menu_icon'         => '',  // Specify a custom URL to an icon
                'last_tab'          => '',                      // Force your panel to always open to a specific tab (by id)
                'page_icon'         => 'menu-icon',         	// Icon displayed in the admin panel next to your menu_title
                'page_slug'         => 'zoner_options',      	// Page slug used to denote the panel
                'save_defaults'     => true,                    // On load save the defaults to DB before user clicks save or not
                'default_show'      => false,                   // If true, shows the default value next to each field that is not the default value.
                'default_mark'      => '',                     // What to print by the field's title if the value shown is default. Suggested: *
                'show_import_export' => false,                  // Shows the Import/Export panel when not used as a field.
                
                // CAREFUL -> These options are for advanced use only
                'transient_time'    => 60 * MINUTE_IN_SECONDS,
                'output'            => true,                   // Global shut-off for dynamic CSS output by the framework. Will also disable google fonts output
                'output_tag'        => true,                   // Allows dynamic CSS to be generated for customizer and google fonts, but stops the dynamic CSS from going to the head
                'footer_credit'     => '<span id="footer-thankyou">' . __( 'Zoner Options panel created using "Reduxe Framework".', 'zoner'). '</span>',                     // Disable the footer credit of zoner. Please leave if you can help it.
                
                // FUTURE -> Not in use yet, but reserved or partially implemented. Use at your own risk.
                'database'              => '', 	  // possible: options, theme_mods, theme_mods_expanded, transient. Not fully functional, warning!
                'system_info'           => false, // REMOVE
				'page_type'				=> 'submenu',
				
				'header_list_links'		=> array(
					array('link' => 'http://support.fruitfulcode.com/hc/en-us/requests/new', 'name' => __('Contact Support', 'zoner')),
					array('link' => 'http://themes.fruitfulcode.com/zoner/documentation/', 'name' => __('Documentation', 'zoner')),
					array('link' => 'http://support.fruitfulcode.com/hc/en-us/categories/200198223-Zoner', 'name' => __('Faq', 'zoner')),
				),

                // HINTS
                'hints' => array(
                    'icon'          => 'icon-question-sign',
                    'icon_position' => 'right',
                    'icon_color'    => 'lightgray',
                    'icon_size'     => 'normal',
                    'tip_style'     => array(
                        'color'         => 'light',
                        'shadow'        => true,
                        'rounded'       => false,
                        'style'         => '',
                    ),
                    'tip_position'  => array(
                        'my' => 'top left',
                        'at' => 'bottom right',
                    ),
                    'tip_effect'    => array(
                        'show'          => array(
                            'effect'        => 'slide',
                            'duration'      => '500',
                            'event'         => 'mouseover',
                        ),
                        'hide'      => array(
                            'effect'    => 'slide',
                            'duration'  => '500',
                            'event'     => 'click mouseleave',
                        ),
                    ),
                )
            );


            // SOCIAL ICONS -> Setup custom links in the footer for quick links in your panel footer icons.
            // $this->args['share_icons'][] = array(
            //     'url'   => 'https://github.com/Fruitfulcode',
            //     'title' => 'Visit us on GitHub',
            //     'img'   => esc_url($source_path . 'images/icons/github.png'), 
            // );
            // $this->args['share_icons'][] = array(
            //     'url'   => 'https://www.facebook.com/fruitfulc0de',
            //     'title' => 'Like us on Facebook',
            //     'img'   => esc_url($source_path . 'images/icons/facebook.png'), 
            // );
            // $this->args['share_icons'][] = array(
            //     'url'   => 'https://twitter.com/fruitfulcode',
            //     'title' => 'Follow us on Twitter',
            //     'img'   => esc_url($source_path . 'images/icons/twitter.png'), 
            // );
            

            // Panel Intro text -> before the form
            if (!isset($this->args['global_variable']) || $this->args['global_variable'] !== false) {
                if (!empty($this->args['global_variable'])) {
                    $v   = $this->args['global_variable'];
                } else {
                    $v = str_replace('-', '_', $this->args['opt_name']);
                }
                 $this->args['intro_text'] = '';
			   //sprintf(__('', 'zoner'), $v);
            } else {
                 $this->args['intro_text'] = '';
            }

            // Add content after the form.
            $this->args['footer_text'] 	= '';
        }

    }
}

function initZonerConfig() {
	global $zonerConfig;
	$zonerConfig = new zoner_config();
}
add_action('init', 'initZonerConfig', 1);
