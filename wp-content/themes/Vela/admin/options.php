<?php
if (file_exists(dirname(__FILE__).'/redux-framework/framework.php')) {
    require_once( dirname(__FILE__).'/redux-framework/framework.php' );
}

/** Wyde AJAX Importer **/
include_once get_template_directory() . '/admin/importer/class-wyde-ajax-importer.php';

// Theme Options config
if (!class_exists('Wyde_Theme_Options')) {

    class Wyde_Theme_Options {

        public $args        = array();
        public $sections    = array();
        public $theme;
        public $ReduxFramework;

        public function __construct() {

            if (!class_exists('ReduxFramework')) {
                return;
            }


            if ( true == Redux_Helpers::isTheme( __FILE__ ) ) {
                $this->initSettings();
                add_action('init', array($this, 'onUpdated'), 10);
            } else {
                add_action('plugins_loaded', array($this, 'initSettings'), 10);
            }

            if ( is_admin() && isset($_GET['page']) && $_GET['page'] == 'theme-options' ) {
                add_action( 'admin_enqueue_scripts', array($this, 'load_scripts') );
            }


        }

        public function initSettings() {

            $this->theme = wp_get_theme();

            $this->setArguments();

            $this->setSections();

            if (!isset($this->args['opt_name'])) {
                return;
            }

            add_action( 'redux/loaded', array( $this, 'remove_demo' ) );

            $this->ReduxFramework = new ReduxFramework($this->sections, $this->args);
        }

        function load_scripts(){
            
            wp_enqueue_style('theme-options-style', get_template_directory_uri(). '/admin/importer/css/importer.css', null, '1.3.1');
	
            wp_register_script('ajax-importer-script', get_template_directory_uri(). '/admin/importer/js/ajax-importer.js', null, null, true);
            wp_enqueue_script( 'ajax-importer-script');
            
            $import_taks = array(__('Pages', 'Vela'), __('Posts', 'Vela'), __('Portfolios', 'Vela'), __('Team Members', 'Vela'), __('Testimonials', 'Vela'), __('Widgets', 'Vela'), __('Sliders', 'Vela'), __('Settings', 'Vela'));
            wp_localize_script('ajax-importer-script', 'ajax_importer_settings', array('import_url' => admin_url( 'admin-ajax.php' ), 'data_dir' => get_template_directory_uri() . '/admin/importer/data/', 'import_tasks' => $import_taks) );

        }

        public function onUpdated(){
            global $wyde_theme_options;
            $updated = isset( $_GET['settings-updated'] )? $_GET['settings-updated']:'';
            if($updated == 'true' && array_key_exists('portfolio_slug', $wyde_theme_options->ReduxFramework->transients['changed_values'])){
                flush_rewrite_rules();
            }
        }

        function remove_demo() {
            if (class_exists('ReduxFrameworkPlugin')) {
                remove_filter('plugin_row_meta', array(ReduxFrameworkPlugin::instance(), 'plugin_metalinks'), null, 2);
                remove_action('admin_notices', array(ReduxFrameworkPlugin::instance(), 'admin_notices'));
            }
        }
        
        public function setSections() {

            $template_directory = esc_url( get_template_directory_uri() );
            
            $import_fields = array(
                    array(
                        'id'        => 'section_import',
                        'type'      => 'section',
                        'title'     => __('Import Demo', 'Vela'),
                        'subtitle'  => __('Please make sure you have required plugins installed and activated to receive that portion of the content. This is recommended to do on fresh installs.', 'Vela'),
                        'indent'    => true
                    ),
                    array(
                        'id'        => 'notice-warning',
                        'type'      => 'info',
                        'notice'    => true,
                        'style'     => 'warning',
                        'icon'      => 'el el-warning-sign',
                        'title'     => __('WARNING:', 'Vela'),
                        'desc'      => __('Importing demo content will replace your current settings and append your pages and posts. It can also take a minute to complete. <br />You can use <a href="https://wordpress.org/plugins/wordpress-reset/" target="_blank">WordPress Reset</a> plugin to remove all existing data before importing.', 'Vela')
                    ),
                    array(
                        'id'        => 'raw_import',
                        'type'      => 'raw',
                        'content'   => 
                        '<div class="import-wrapper">'
                        .'<h4>Choose a Demo</h4>'
                        .'<div class="demo-content-list">'
                        .'<a id="demo-content-1" href="#" class="demo-item"><img src="'. $template_directory .'/admin/importer/images/vela1.jpg" alt="Modern"/><strong>Modern</strong></a>'
                        .'<a id="demo-content-2" href="#" class="demo-item"><img src="'. $template_directory .'/admin/importer/images/vela2.jpg" alt="Agency"/><strong>Agency</strong></a>'
                        .'<a id="demo-content-3" href="#" class="demo-item"><img src="'. $template_directory .'/admin/importer/images/vela3.jpg" alt="One Page Business"/><strong>One Page Business</strong></a>'
                        .'<a id="demo-content-4" href="#" class="demo-item"><img src="'. $template_directory .'/admin/importer/images/vela4.jpg" alt="One Page Agency"/><strong>One Page Agency</strong></a>'
                        .'<a id="demo-content-5" href="#" class="demo-item"><img src="'. $template_directory .'/admin/importer/images/vela5.jpg" alt="Personal Portfolio"/><strong>Personal Portfolio</strong></a>'
                        .'</div>'
                        .'</div>',
                    )
            );

            $imported = isset( $_GET['imported'] )? $_GET['imported']:'';
            
            if($imported == 'success' ){
                
                array_unshift($import_fields, array(
                        'id'        => 'notice-success',
                        'type'      => 'info',
                        'notice'    => true,
                        'style'     => 'success',
                        'icon'      => 'el el-info-circle',
                        'title'     => __('Success!', 'Vela'),
                        'desc'      => __('The demo content has been successfully imported.', 'Vela')
                    ));

            }else if($imported == 'error' ){
                
                array_unshift($import_fields, array(
                        'id'        => 'notice-fail',
                        'type'      => 'info',
                        'notice'    => true,
                        'style'     => 'critical',
                        'icon'      => 'el el-info-circle',
                        'title'     => __('ERROR!', 'Vela'),
                        'desc'      => __('An error occurred while importing demo data, please try again later.', 'Vela')
                    ));

            }
            
            //Home
            $this->sections['home'] = array(
                'title'     => __('Home', 'Vela'),
                'heading'   => false,
                'icon'      => 'el-icon-home',
                'fields'    => $import_fields
            );

            $predefined_colors = array();
            for($i = 1; $i <=10; $i++){
                $predefined_colors[strval($i)] = array('alt' => '',  'img' => $template_directory . '/images/colors/'.$i.'.png');
            }

            //Styling
            $this->sections['styling'] = array(
                'icon'      => 'el-icon-brush',
                'title'     => __('Styling', 'Vela'),
                'heading'   => false,
                'fields'    => array(
                   array(
                        'id'        => 'predefined_color',
                        'type'      => 'image_select',
                        'title'     => __('Predefined Colors', 'Vela'),
                        'subtitle'  => __('Select a predefined color schemes.', 'Vela'),
                        'options'   => $predefined_colors,
                        'default'   => '1'
                   ),
                   array(
                        'id'        => 'custom_color',
                        'type'      => 'switch',
                        'title'     => __('Custom Color Scheme', 'Vela'),
                        'subtitle'  => __('Customize your own color scheme.', 'Vela'),
                        'default'   => 0
                   ),
                   array(
                        'id'        => 'color_scheme',
                        'type'      => 'color',
                        'title'     => __('Color Scheme', 'Vela'),
                        'subtitle'  => __('Choose your own color scheme.', 'Vela'),
                        'required'  => array('custom_color', '=', 1),
                        'transparent'   => false,
                        'default'   => '#FA5C5D'
                   ),
                   array(
                        'id'        => 'layout',
                        'type'      => 'select',
                        'title'     => __('Layout', 'Vela'),
                        'subtitle'  => __('Select layout for your site.', 'Vela'),
                        'options'   => array(
                            'wide' => 'Wide',
                            'boxed'=> 'Boxed'
                        ),
                        'default'   => 'wide',
                    ),
                    array(
                        'id'        => 'section_layout_boxed',
                        'type'      => 'section',
                        'title'     => 'Boxed Mode Options',
                        'subtitle'  => 'Customize background for boxed layout.',
                        'required'  => array('layout', '=', 'boxed'),
                        'indent'    => true
                    ),
                    array(
                        'id'        => 'boxed_shadow',
                        'type'      => 'switch',
                        'required'  => array('layout', '=', 'boxed'),
                        'title'     => __('Box Shadow', 'Vela'),
                        'subtitle'  => __('Attaches drop-shadow to the boxed layout.', 'Vela'),
                        'default'   => 1
                    ),
                    array(
                        'id'        => 'background_mode',
                        'type'      => 'select',
                        'title'     => __('Background', 'Vela'),
                        'subtitle'  => __('Select background type.', 'Vela'),
                        'options'   => array(
                            'color' => 'Color',
                            'image' => 'Image',
                            'pattern'=>'Pattern'
                        ),
                        'default'   => 'color',
                    ),
                    array(
                            'id'        => 'background_color',
                            'type'      => 'background',
                            'title'     => __('Background Color', 'Vela'),
                            'required'  => array(array('layout', '=', 'boxed'), array('background_mode', '=', 'color')),
                            'background-image' => false,
                            'background-position' => false,
                            'background-repeat' => false,
                            'background-size' => false,
                            'background-attachment' => false,
                            'preview'   =>false,
                            'output'    => array('body'),
                            'subtitle'  => __('Select a background color for your site.', 'Vela'),
                            'default'   => array(
                                'background-color'  => ''
                            )
                    ),
                    array(
                        'id'        => 'background_image',
                        'type'      => 'background',
                        'required'  => array(array('layout', '=', 'boxed'), array('background_mode', '=', 'image')),
                        'title'     => __('Background Image', 'Vela'),
                        'output'    => array('body'),
                        'subtitle'  => __('Customize background image for your site.', 'Vela'),
                        'preview' => true,
                        'default'   => array(
                            'background-repeat' => 'no-repeat',
                            'background-size' => 'cover',
                            'background-attachment' => 'fixed',
                            'background-position' => 'center center',
                        )
                    ),
                    array(
                        'id'        => 'background_pattern',
                        'type'      => 'image_select',
                        'required'  => array(array('layout', '=', 'boxed'), array('background_mode', '=', 'pattern')),
                        'title'     => __('Background Pattern', 'Vela'),
                        'subtitle'  => __('Select a background pattern.', 'Vela'),
                        'width'     => '50px',
                        'height'    => '50px',
                        'options'   => array(
                            '1'    => array('alt' =>'Pattern 1', 'img'   => $template_directory . '/images/patterns/1.png'),
                            '2'    => array('alt' =>'Pattern 2', 'img'   => $template_directory . '/images/patterns/2.png'),
                            '3'    => array('alt' =>'Pattern 3', 'img'   => $template_directory . '/images/patterns/3.png'),
                            '4'    => array('alt' =>'Pattern 4', 'img'   => $template_directory . '/images/patterns/4.png'),
                            '5'    => array('alt' =>'Pattern 5', 'img'   => $template_directory . '/images/patterns/5.png'),
                            '6'    => array('alt' =>'Pattern 6', 'img'   => $template_directory . '/images/patterns/6.png'),
                            '7'    => array('alt' =>'Pattern 7', 'img'   => $template_directory . '/images/patterns/7.png'),
                            '8'    => array('alt' =>'Pattern 8', 'img'   => $template_directory . '/images/patterns/8.png'),
                            '9'    => array('alt' =>'Pattern 9', 'img'   => $template_directory . '/images/patterns/9.png'),
                            '10'    => array('alt' =>'Pattern 10', 'img'   => $template_directory . '/images/patterns/10.png')
                         ),
                        'default'   => '1'
                    ),
                    array(
                        'id'        => 'background_pattern_overlay',
                        'type'      => 'switch',
                        'required'  => array('background_mode', '=', 'image'),
                        'title'     => __('Background Pattern Overlay', 'Vela'),
                        'subtitle'  => __('Apply an overlay pattern to the background.', 'Vela'),
                        'default'   => false
                    ),
                    array(
                        'id'        => 'background_pattern_fixed',
                        'type'      => 'switch',
                        'required'  => array('background_mode', '=', 'pattern'),
                        'title'     => __('Fixed Background Pattern', 'Vela'),
                        'subtitle'  => __('The background pattern is fixed with regard to the viewport.', 'Vela'),
                        'default'   => false,
                    )
                 )
            );

            //Favicon
            // If it is older than WordPress 4.3
            if ( ! ( function_exists( 'wp_site_icon' ) ) ) {
            
                $this->sections['favicon'] = array(
                    'icon'      => 'el-icon-star',
                    'title'     => __('Favicon', 'Vela'),
                    'heading'   => false,
                    'fields'    => array(
                        array(
                            'id'        => 'section_favicon',
                            'type'      => 'section',
                            'title'     => __('Favicon', 'Vela'),
                            'subtitle'  => __('Customize a favicon for your site.', 'Vela'),
                            'indent'    => true
                        ),
                        array(
                            'id'        => 'favicon_image',
                            'type'      => 'media',
                            'url'       => true,
                            'title'     => __('Favicon Image (.PNG)', 'Vela'),
                            'readonly'  => false,
                            'subtitle'  => __('Upload a favicon image for your site, or you can specify an image URL directly.', 'Vela'),
                            'desc'      => __('Icon dimension:', 'Vela').' 16px * 16px or 32px * 32px',
                            'default'   => array(        
                                                'url'=> $template_directory .'/images/favicon.png'
                                          ),
                        ),
                        array(
                            'id'        => 'favicon',
                            'type'      => 'media',
                            'url'       => true,
                            'title'     => __('Favicon (.ICO)', 'Vela'),
                            'readonly'  => false,
                            'subtitle'  => __('Upload a favicon for your site, or you can specify an icon URL directly.', 'Vela'),
                            'desc'      => __('Icon dimension:', 'Vela').' 16px * 16px',

                        ),
                        array(
                            'id'        => 'favicon_iphone',
                            'type'      => 'media',
                            'url'       => true,
                            'title'     => __('Apple iPhone Icon', 'Vela'),
                            'height'     => '57px',
                            'readonly'  => false,
                            'subtitle'  => __('Favicon for Apple iPhone.', 'Vela'),
                            'desc'      => __('Icon dimension:', 'Vela').' 57px * 57px',
                        ),
                        array(
                            'id'        => 'favicon_iphone_retina',
                            'type'      => 'media',
                            'url'       => true,
                            'title'     => __('Apple iPhone Icon (Retina Version)', 'Vela'),
                            'height'     => '57px',
                            'readonly'  => false,
                            'subtitle'  => __('Favicon for Apple iPhone Retina Version.', 'Vela'),
                            'desc'      => __('Icon dimension:', 'Vela').' 114px  * 114px',
                        ),
                        array(
                            'id'        => 'favicon_ipad',
                            'type'      => 'media',
                            'url'       => true,
                            'title'     => __('Apple iPad Icon', 'Vela'),
                            'height'     => '72px',
                            'readonly'  => false,
                            'subtitle'  => __('Favicon for Apple iPad.', 'Vela'),
                            'desc'      => __('Icon dimension:', 'Vela').' 72px * 72px',
                        ),
                        array(
                            'id'        => 'favicon_ipad_retina',
                            'type'      => 'media',
                            'url'       => true,
                            'title'     => __('Apple iPad Icon (Retina Version)', 'Vela'),
                            'height'     => '57px',
                            'readonly'  => false,
                            'subtitle'  => __('Favicon for Apple iPad Retina Version.', 'Vela'),
                            'desc'      => __('Icon dimension:', 'Vela').' 144px  * 144px',
                        )
                    
                ));
            }

            //Header
            $this->sections['header'] = array(
                'icon'      => 'el-icon-lines',
                'title'     => __('Header', 'Vela'),
                'heading'   => false,
                'fields'    => array(
                    array(
                        'id'        => 'section_header',
                        'type'      => 'section',
                        'title'     => __('Header', 'Vela'),
                        'subtitle'  => __('Customize the header section.', 'Vela'),
                        'indent'    => true
                    ),
                    array(
                        'id'        => 'header_sticky',
                        'type'      => 'switch',
                        'title'     => __('Sticky Header', 'Vela'),
                        'subtitle'  => __('Enable sticky header.', 'Vela'),
                        'default'   => 1,
                    ),
                    array(
                        'id'        => 'header_transparent',
                        'type'      => 'switch',
                        'title'     => __('Transparent Header', 'Vela'),
                        'subtitle'  => __('Enable transparent header to display header overlay sliders and page content.', 'Vela'),
                        'default'   => 1,
                    ),
                    array(
                        'id'        => 'header_fluid',
                        'type'      => 'switch',
                        'title'     => __('Fluid Header', 'Vela'),
                        'subtitle'  => __('Turn on to use fluid header or off to use fixed header width.', 'Vela'),
                        'default'   => 0,
                    ),
                    array(
                        'id'        => 'header_layout',
                        'type'      => 'image_select',
                        'title'     => __('Layout', 'Vela'),
                        'subtitle'  => __('Select header layout.', 'Vela'),
                        'options'   => array(
                            '1' => array('alt' => 'Version 1', 'img' => $template_directory . '/images/headers/v1.jpg'),
                            '2' => array('alt' => 'Version 2', 'img' => $template_directory . '/images/headers/v2.jpg'),
                            '3' => array('alt' => 'Version 3', 'img' => $template_directory . '/images/headers/v3.jpg'),
                            '4' => array('alt' => 'Version 4', 'img' => $template_directory . '/images/headers/v4.jpg'),
                            '5' => array('alt' => 'Version 5', 'img' => $template_directory . '/images/headers/v5.jpg'),
                            '6' => array('alt' => 'Version 6', 'img' => $template_directory . '/images/headers/v6.jpg'),
                            '7' => array('alt' => 'Version 7', 'img' => $template_directory . '/images/headers/v7.jpg'),
                        ),
                        'default'   => '1'
                    ),
                    array(
                        'id'        => 'menu_style',
                        'type'      => 'select',
                        'title'     => __('Menu Style', 'Vela'),
                        'subtitle'  => __('Select a menu style. Choose "Light" for the header which has "Dark" background color.', 'Vela'),
                        'options'   => array(
                            'light' => 'Light',
                            'dark' => 'Dark',
                        ),
                        'default'   => 'light'
                    ),
                    array(
                        'id'        => 'top_bar_menu',
                        'type'      => 'select',
                        'title'     => __('Top Bar Menu', 'Vela'),
                        'subtitle'  => __('Select the position of header top bar menu.', 'Vela'),
                        'options'   => array(
                            '0' => 'Hide',
                            '1' => 'Left',
                            '2' => 'Right'
                        ),
                        'default'   => '1'
                    ),
                    array(
                        'id'        => 'menu_social_icon',
                        'type'      => 'select',
                        'title'     => __('Social Icons', 'Vela'),
                        'subtitle'  => __('Select the position of header top bar social media icons.', 'Vela'),
                        'options'   => array(
                            '0' => 'Hide',
                            '1' => 'Left',
                            '2' => 'Right'
                        ),
                        'default'   => '2'
                    ),
                    array(
                        'id'        => 'menu_search_icon',
                        'type'      => 'switch',
                        'title'     => __('Search Icon', 'Vela'),
                        'subtitle'  => __('Show search icon in header.', 'Vela'),
                        'default'   => 1
                    ),
                    array(
                        'id'        => 'menu_shop_cart',
                        'type'      => 'switch',
                        'title'     => __('Shopping Cart Icon', 'Vela'),
                        'subtitle'  => __('Show shopping cart icon in header.', 'Vela'),
                        'default'   => 1
                    ),
                    array(
                        'id'        => 'header_position',
                        'type'      => 'select',
                        'title'     => __('Header Position', 'Vela'),
                        'subtitle'  => __('Select header position.', 'Vela'),
                        'options'   => array(
                            '1' => 'Top of Page',
                            '2' => 'Top of Content (Below Slider)'
                        ),
                        'default'   => 1,
                    ),
                    array(
                        'id'        => 'section_logo',
                        'type'      => 'section',
                        'title'     => __('Logo', 'Vela'),
                        'subtitle'  => __('Customize a logo image for your site.', 'Vela'),
                        'indent'    => true
                    ),
                    array(
                        'id'        => 'logo_image',
                        'type'      => 'media',
                        'url'       => true,
                        'title'     => __('Logo', 'Vela'),
                        'height'     => '50px',
                        'readonly'  => false,
                        'subtitle'  => __('Logo image for your site, or you can specify an image URL directly.', 'Vela'),
                        'desc'      => __('Recommended height: 50px.', 'Flora'),
                        'default'  => array(        
                                            'url'=> $template_directory .'/images/logo.png'
                                      )
                    ),
                    array(
                        'id'        => 'logo_image_retina',
                        'type'      => 'media',
                        'url'       => true,
                        'title'     => __('Logo (Retina Version)', 'Vela'),
                        'height'     => '50px',
                        'readonly'  => false,
                        'subtitle'  => __('Retina version for your logo image, this image should be exactly 2x the size of normal logo.', 'Vela'),
                        'desc'      => __('Recommended height: 100px.', 'Flora'),
                        'default'  => array(        
                                            'url'=> $template_directory .'/images/logo@2x.png'
                                      )
                    ),
                    array(
                        'id'        => 'light_logo_image',
                        'type'      => 'media',
                        'url'       => true,
                        'title'     => __('Light Logo', 'Flora'),
                        'height'    => '50px',
                        'readonly'  => false,
                        'subtitle'  => __('Light version of logo image for dark background.', 'Flora'),
                        'desc'      => __('Recommended height: 50px.', 'Flora'),
                        'default'   => array(        
                                    'url'=> $template_directory .'/images/light-logo.png'
                        ),
                    ),
                    array(
                        'id'        => 'light_logo_image_retina',
                        'type'      => 'media',
                        'url'       => true,
                        'title'     => __('Light Logo (Retina Version)', 'Flora'),
                        'height'    => '50px',
                        'readonly'  => false,
                        'subtitle'  => __('Retina version for your light logo image, this image should be exactly 2x the size of light logo', 'Flora'),
                        'desc'      => __('Recommended height: 100px.', 'Flora'),
                        'default'   => array(        
                                    'url'=> $template_directory .'/images/light-logo@2x.png'
                        ),
                    ),
                    array(
                        'id'        => 'logo_image_sticky',
                        'type'      => 'media',
                        'url'       => true,
                        'title'     => __('Sticky Logo', 'Vela'),
                        'height'    => '45px',
                        'readonly'  => false,
                        'subtitle'  => __('Sticky header logo.', 'Vela'),
                        'desc'      => __('Recommended height: 25px.', 'Flora'),
                        'default'  => array(        
                                            'url'=> $template_directory .'/images/logo-sticky.png'
                        )
                    ),
                    array(
                        'id'        => 'logo_image_sticky_retina',
                        'type'      => 'media',
                        'url'       => true,
                        'title'     => __('Sticky Logo (Retina Version)', 'Vela'),
                        'height'     => '45px',
                        'readonly'  => false,
                        'subtitle'  => __('Retina version for sticky header logo.', 'Vela'),
                        'desc'      => __('Recommended height: 50px.', 'Flora'),
                        'default'  => array(        
                                            'url'=> $template_directory .'/images/logo-sticky@2x.png'
                        )
                    )
                 )
            );

            //Footer
            $this->sections['footer'] = array(
                'icon'      => 'el-icon-th-large',
                'title'     => __('Footer', 'Vela'),
                'heading'   => false,
                'fields'    => array(
                    array(
                        'id'        => 'section_footer_widget',
                        'type'      => 'section',
                        'title'     => __('Footer Widget Area', 'Vela'),
                        'subtitle'  => __('Customize the footer widget area.', 'Vela'),
                        'indent'    => true
                    ),
                    array(
                        'id'        => 'footer_widget',
                        'type'      => 'switch',
                        'title'     => __('Footer Widget Area', 'Vela'),
                        'subtitle'  => __('Display footer widgets.', 'Vela'),
                        'default'   => 0,
                    ),
                    array(
                        'id'        => 'footer_widget_color',
                        'type'      => 'color',
                        'required'  => array('footer_widget', '=', 1),
                        'title'     => __('Text Color', 'overlap'),
                        'subtitle'  => __('Set a footer widget area text color.', 'Vela'),
                        'transparent' => false,
                        'output'    => array('#footer-widget'),
                        'default'   => '',
                    ),
                    array(
                        'id'        => 'footer_widget_background',
                        'type'      => 'background',
                        'required'  => array('footer_widget', '=', 1),
                        'title'     => __('Footer Widget Area Background', 'Vela'),
                        'subtitle'  => __('Set a footer widget area background.', 'Vela'),
                        'output'    => array('#footer-widget'),
                        'background-repeat' => false,
                        'background-attachment' =>false,
                        'default'   => array(
                            'background-color'  => '#1e1e1e',
                            'background-size'   => 'cover',
                            'background-position'   => 'center bottom'
                        ),
                    ),
                    array(
                        'id'        => 'footer_background_parallax',
                        'type'      => 'switch',
                        'required'  => array('footer_widget', '=', 1),
                        'title'     => __('Footer Background Parallax', 'Vela'),
                        'subtitle'  => __('Enable parallax background scrolling.', 'Vela'),
                        'default'   => 0,
                    ),
                    array(
                        'id'        => 'footer_widget_columns',
                        'type'      => 'select',
                        'required'  => array('footer_widget', '=', 1),
                        'title'     => __('Footer Widget Columns', 'Vela'),
                        'subtitle'  => __('Select the number of columns to display in the footer widget area.', 'Vela'),
                        'options'   => array(
                            '1' => '1',
                            '2' => '2',
                            '3' => '3',
                            '4' => '4'
                        ),
                        'default'   => '4'
                    ),
                    array(
                        'id'        => 'section_footer_bar',
                        'type'      => 'section',
                        'title'     => __('Footer Bottom Bar', 'Vela'),
                        'subtitle'  => __('Customize the footer bottom bar.', 'Vela'),
                        'indent'    => true
                    ),
                    array(
                        'id'        => 'footer_bar',
                        'type'      => 'switch',
                        'title'     => __('Footer Bottom Bar', 'Vela'),
                        'subtitle'  => __('Display a footer bar at the bottom of the page.', 'Vela'),
                        'default'   => 1,
                    ),
                    array(
                        'id'        => 'footer_bar_color',
                        'type'      => 'color',
                        'required'  => array('footer_bar', '=', 1),
                        'title'     => __('Text Color', 'overlap'),
                        'subtitle'  => __('Set a footer bottom bar text color.', 'Vela'),
                        'transparent' => false,
                        'output'    => array('#footer-bottom, #footer-text, #footer-nav a'),
                        'default'   => '',
                    ),
                    array(
                        'id'        => 'footer_bar_background',
                        'type'      => 'background',
                        'required'  => array('footer_bar', '=', 1),
                        'title'     => __('Footer Bar Background', 'Vela'),
                        'subtitle'  => __('Set a footer bottom bar background.', 'Vela'),
                        'output'    => array('#footer-bottom'),
                        'background-repeat' => false,
                        'background-attachment' =>false,
                        'default'   => array(
                            'background-color'  => '#161616',
                            'background-size'   => 'cover',
                            'background-position'   => 'center bottom'
                        ),
                    ),
                    array(
                        'id'        => 'footer_bar_columns',
                        'type'      => 'select',
                        'required'  => array('footer_bar', '=', 1),
                        'title'     => __('Footer Bar Columns', 'Vela'),
                        'subtitle'  => __('Select a footer bottom bar columns.', 'Vela'),
                        'options'   => array(
                            '1' => '1',
                            '2' => '2',
                        ),
                        'default'   => '2'
                    ),
                    array(
                        'id'        => 'footer_text_show',
                        'type'      => 'switch',
                        'required'  => array('footer_bar', '=', 1),
                        'title'     => __('Show Footer Text', 'Vela'),
                        'subtitle'  => __('Display a footer text.', 'Vela'),
                        'default'   => 1,
                    ),
                    array(
                        'id'        => 'footer_text',
                        'type'      => 'editor',
                        'required'  => array('footer_text_show', '=', 1),
                        'title'     => __('Footer Text', 'Vela'),
                        'subtitle'  => __('Enter your footer text here.', 'Vela'),
                        'args'   => array(
                            'teeny'            => false,
                            'textarea_rows'    => 3
                        ),
                        'default'   => '&copy;'. date('Y') .' Vela - Premium WordPress Theme. Powered by <a href="https://wordpress.org/" target="_blank">WordPress</a>.',
                    ),
                    array(
                        'id'        => 'footer_bar_menu',
                        'type'      => 'select',
                        'required'  => array('footer_bar', '=', 1),
                        'title'     => __('Footer Menu', 'Vela'),
                        'subtitle'  => __('Select a footer bar menu items.', 'Vela'),
                        'options'   => array(
                            '0' => 'Hide',
                            '1' => 'Footer Menu',
                            '2' => 'Social Media Icons',
                        ),
                        'default'   => '1'
                    )
                )
            );

            //Page
            $this->sections['page'] = array(
                'icon'      => 'el-icon-website',
                'title'     => __('Page', 'Vela'),
                'heading'     => false,
                'fields'    => array(
                    array(
                        'id'        => 'section_page_title',
                        'type'      => 'section',
                        'title'     => __('Title', 'Vela'),
                        'subtitle'  => __('Default settings for page title.', 'Vela'),
                        'indent'    => true
                    ),
                    array(
                        'id'        => 'title_align',
                        'type'      => 'select',
                        'title'     => __('Title Alignment', 'Vela'),
                        'subtitle'  => __('Select the title alignment.', 'Vela'),
                        'options'   => array(
                            'left' => 'Left',
                            'center' => 'Center',
                            'right'=> 'Right'
                        ),
                        'default'   => 'center',
                    ),
                    array(
                        'id'        => 'title_background_mode',
                        'type'      => 'select',
                        'title'     => __('Background', 'Vela'),
                        'subtitle'  => __('Select background type.', 'Vela'),
                        'options'   => array(
                            'none' => 'None',
                            'color' => 'Color',
                            'image' => 'Image',
                            'video'=> 'Video'
                        ),
                        'default'   => 'none',
                    ),
                    array(
                        'id'        => 'title_background_video',
                        'type'      => 'media',
                        'required'  => array('title_background_mode', '=', 'video'),
                        'title'     => __('Background Video', 'Vela'),
                        'subtitle'  => __('Customize background video.', 'Vela'),
                        'url'       => true,
                        'mode' => false,
                        'readonly'  => false
                    ),
                    array(
                        'id'        => 'title_background_image',
                        'type'      => 'background',
                        'required'  => array('title_background_mode', 'contains', 'i'),
                        'title'     => __('Background Image', 'Vela'),
                        'background-color' => false,
                        'subtitle'  => __('Customize background image.', 'Vela'),
                        'default'   => array(
                            'background-repeat' => 'no-repeat',
                            'background-size' => 'cover',
                            'background-attachment' => 'fixed',
                            'background-position' => 'center center',
                        )
                    ),
                    array(
                        'id'        => 'title_background_color',
                        'type'      => 'color',
                        'required'  => array('title_background_mode', '!=', 'none'),
                        'title'     => __('Background Color', 'Vela'),
                        'subtitle'  => __('Select a background color.', 'Vela')
                    ),
                    array(
                        'id'        => 'title_background_parallax',
                        'type'      => 'switch',
                        'required'  => array('title_background_mode', '=', 'image'),
                        'title'     => __('Parallax Background', 'Vela'),
                        'subtitle'  => __('Enable parallax background when scrolling.', 'Vela'),
                        'default'   => 1,
                    ),
                    array(
                        'id'        => 'title_overlay',
                        'type'      => 'select',
                        'required'  => array('title_background_mode', 'contains', 'i'),
                        'title'     => __('Title Background Overlay', 'Vela'),
                        'subtitle'  => __('Apply an overlay to the title background.', 'Vela'),
                        'options'   => array(
                            'none' => 'None',
                            'color' => 'Color Overlay',
                            'pattern' => 'Pattern Overlay',
                        ),
                        'default'   => 'none',
                    ),
                    array(
                        'id'       => 'title_overlay_color',
                        'type'     => 'color',
                        'required'  => array('title_overlay', '=', 'color'),
                        'title'    => __('Overlay Color', 'Vela'), 
                        'subtitle' => __( 'Select background color overlay.', 'Vela' ),
                        'transparent'   => false,
                        'default'  => '',
                        'validate' => 'color',
                    ),
                    array(
				        'id'      => 'title_mask',
                        'type' => 'select',
                        'required'  => array('title_background_mode', '!=', 'none'),
				        'title'    => __( 'Content Mask', 'Vela' ),
				        'subtitle'    => __( 'Select content mask style.', 'Vela' ),
                        'options'   => array(
                                'none'  => 'None',
                                '00' =>'0/100', 
                                '10' => '10/90',
                                '20' => '20/80',
                                '30' => '30/70',
                                '40' => '40/60',
                                '50' => '50/50',
                                '60' => '60/40',
                                '70' => '70/30',
                                '80' => '80/20',
                                '90' => '90/10',
                                '100' => '100/0',
                        ),
				        'default' => 'none'
			        ),
                    array(
				        'id'      => 'title_mask_color',
                        'type'    => 'color',
				        'title'    => __( 'Mask Color', 'Vela' ),
				        'subtitle'    => __( 'Select content mask color.', 'Vela' ),
                        'required'  => array('title_mask', '!=', 'none'),
				        'default' => '#ffffff'
			        ),
                    array(
                        'id'        => 'section_page_background',
                        'type'      => 'section',
                        'title'     => __('Background', 'Vela'),
                        'subtitle'  => __('Default settings for page background.', 'Vela'),
                        'indent'    => true
                    ),
                    array(
                        'id'        => 'page_background_mode',
                        'type'      => 'select',
                        'title'     => __('Background', 'Vela'),
                        'subtitle'  => __('Select background type.', 'Vela'),
                        'options'   => array(
                            'none' => 'None',
                            'color' => 'Color',
                            'image' => 'Image',
                            'video'=> 'Video',
                        ),
                        'default'   => 'color',
                    ),
                    array(
                        'id'        => 'page_background_image',
                        'type'      => 'background',
                        'required'  => array('page_background_mode', '=', 'image'),
                        'title'     => __('Background Image', 'Vela'),
                        'background-color' => false,
                        'subtitle'  => __('Customize background image.', 'Vela'),
                        'default'   => array(
                            'background-repeat' => 'no-repeat',
                            'background-size' => 'cover',
                            'background-attachment' => 'fixed',
                            'background-position' => 'center center',
                        )
                    ),
                    array(
                        'id'        => 'page_background_video',
                        'type'      => 'media',
                        'required'  => array('page_background_mode', '=', 'video'),
                        'title'     => __('Background Video', 'Vela'),
                        'subtitle'  => __('Customize background video.', 'Vela'),
                        'url'       => true,
                        'mode' => false,
                        'readonly'  => false
                    ),
                    array(
                        'id'        => 'page_background_color',
                        'type'      => 'color',
                        'required'  => array('page_background_mode', '!=', 'none'),
                        'title'     => __('Background Color', 'Vela'),
                        'subtitle'  => __('Select a background color.', 'Vela'),
                        'default'   => '#ffffff'
                    ),
                    array(
                        'id'        => 'page_overlay',
                        'type'      => 'select',
                        'required'  => array('page_background_mode', 'contains', 'i'),
                        'title'     => __('Background Overlay', 'Vela'),
                        'subtitle'  => __('Apply an overlay to the background.', 'Vela'),
                        'options'   => array(
                            'none' => 'None',
                            'color' => 'Color Overlay',
                            'pattern' => 'Pattern Overlay',
                        ),
                        'default'   => 'none',
                    ),
                    array(
                        'id'       => 'page_overlay_color',
                        'type'     => 'color',
                        'required'  => array('page_overlay', '=', 'color'),
                        'title'    => __('Overlay Color', 'Vela'), 
                        'subtitle' => __( 'Select background color overlay.', 'Vela' ),
                        'transparent'   => false,
                        'default'  => '',
                        'validate' => 'color',
                    ),
                    array(
                        'id'        => 'page_background_parallax',
                        'type'      => 'switch',
                        'required'  => array('page_background_mode', '=', 'image'),
                        'title'     => __('Parallax Background', 'Vela'),
                        'subtitle'  => __('Enable parallax background when scrolling.', 'Vela'),
                        'default'   => 1,
                    ),
                    array(
                        'id'        => 'section_page_options',
                        'type'      => 'section',
                        'title'     => __('Page Options', 'Vela'),
                        'subtitle'  => __('Customize page options.', 'Vela'),
                        'indent'    => true
                    ),
                    array(
                        'id'        => 'onepage',
                        'type'      => 'switch',
                        'title'     => __('One Page Website', 'Vela'),
                        'subtitle'  => __('When you turn this option on, your frontpage will automatically retrieve page content from primary menu.', 'Vela'),
                        'default'   => false,
                    ),
                    array(
                        'id'        => 'mobile_zoom',
                        'type'      => 'switch',
                        'title'     => __('Page Zooming on Mobile', 'Vela'),
                        'subtitle'  => __('Allow the user to zoom in and out of a page.', 'Vela'),
                        'default'   => false,
                    ),
                    array(
                        'id'        => 'mobile_animation',
                        'type'      => 'switch',
                        'title'     => __('Animation on Mobile', 'Vela'),
                        'subtitle'  => __('Turn on this option to enable animation on mobile.', 'Vela'),
                        'default'   => false,
                    ),
                    array(
                        'id'        => 'page_og_support',
                        'type'      => 'switch',
                        'title'     => __('Open Graph Support', 'Vela'),
                        'subtitle'  => __('Turn on to enable any web page on your website to become a rich object in a social graph.', 'Vela'),
                        'default'   => true,
                    ),
                    array(
                        'id'        => 'smooth_scroll',
                        'type'      => 'switch',
                        'title'     => __('Smooth Scrolling', 'Vela'),
                        'subtitle'  => __('Enable a smooth scrolling.', 'Vela'),
                        'default'   => true,
                    ),
                    array(
                        'id'        => 'preload_images',
                        'type'      => 'switch',
                        'title'     => __('Preload Images', 'Vela'),
                        'subtitle'  => __('Preloading images definitely helps users enjoy a better experience when viewing your content.', 'Vela'),
                        'default'   => false,
                    ),
                    array(
                        'id'        => 'page_loader',
                        'type'      => 'image_select',
                        'title'     => __('Page Loader', 'Vela'),
                        'subtitle'  => __('Select a page loader animation.', 'Vela'),
                        'options'   => array(
                            '1' => array('alt' => 'Wandering Cubes', 'img' => $template_directory . '/images/loaders/1.png'),
                            '2' => array('alt' => 'Double Bounce',  'img' => $template_directory . '/images/loaders/2.png'),
                            '3' => array('alt' => 'Wave', 'img' => $template_directory . '/images/loaders/3.png'),
                            '4' => array('alt' => 'Pulse', 'img' => $template_directory . '/images/loaders/4.png'),
                            '5' => array('alt' => 'Chasing Dots', 'img' => $template_directory . '/images/loaders/5.png'),
                            '6' => array('alt' => 'Three Bounce', 'img' => $template_directory . '/images/loaders/6.png'),
                            '7' => array('alt' => 'Circle', 'img' => $template_directory . '/images/loaders/7.png'),
                        ),
                        'default'   => '1',
                    ),
                    array(
                        'id'        => 'totop_button',
                        'type'      => 'switch',
                        'title'     => __('Back To Top Button', 'Vela'),
                        'subtitle'  => __('Enable a back to top button on your pages.', 'Vela'),
                        'default'   => true,
                    ),
                    array(
                        'id'        => 'page_comments',
                        'type'      => 'switch',
                        'title'     => __('Allow Comments', 'Vela'),
                        'subtitle'  => __('Allow comments on regular pages.', 'Vela'),
                        'default'   => false,
                    ),
                    array(
                        'id'        => 'section_ajax_options',
                        'type'      => 'section',
                        'title'     => __('AJAX Options', 'Vela'),
                        'subtitle'  => __('Turn on or off the AJAX page features.', 'Vela'),
                        'indent'    => true
                    ),
                    array(
                        'id'        => 'ajax_page',
                        'type'      => 'switch',
                        'title'     => __('Ajax Page Transitions', 'Vela'),
                        'subtitle'  => __('Enable ajax page transitions.', 'Vela'),
                        'default'   => true,
                    ),
                    array(
                        'id'        => 'ajax_page_transition',
                        'type'      => 'select',
                        'required'  => array('ajax_page', '=', 1),
                        'title'     => __('Transition Effect', 'Vela'),
                        'subtitle'  => __('Select a page transition effect.', 'Vela'),
                        'options'   => array(
                            'fade' => 'Fade',
                            'slideToggle' => 'Slide Toggle',
                            'slideLeft' => 'Slide to Left',
                            'slideRight'=> 'Slide to Right',
                            'slideUp'=> 'Slide Up',
                            'slideDown'=> 'Slide Down',
                        ),
                        'default'   => 'fade',
                    ),
                    array(
                        'id'        => 'ajax_page_exclude_urls',
                        'type'      => 'multi_text',
                        'required'  => array('ajax_page', '=', 1),
                        'title'     => __('Excluded URLs', 'Vela'),
                        'subtitle'  => __('Excludes the specific links from AJAX Page Loader. This option allows you to exclude the page which contain some plugins that\'s conflict with the AJAX Loader. E.g. /shop/, /cart/, /checkout/, /gallery/, etc.', 'Vela'),
                        'add_text'  => __('Add New', 'Vela'),
                        'default'   => array(
                            '/shop/',
                            '/product/',
                            '/cart/',
                            '/checkout/',
                            '/my-account/',
                        ),
                    ),

                )
            );


            //Blog
            $this->sections['blog'] = array(
                'icon'      => 'el-icon-edit',
                'title'     => __('Blog', 'Vela'),
                'heading'   => false,
                'fields'    => array(
                    array(
                        'id'        => 'section_blog',
                        'type'      => 'section',
                        'title'     => __('Blog', 'Vela'),
                        'subtitle'  => __('Customize blog page for the assigned Posts page in Settings > Reading.', 'Vela'),
                        'indent'    => true
                    ),
                    array(
                        'id'        => 'blog_title',
                        'type'      => 'text',
                        'title'     => __('Page Title', 'Vela'),
                        'subtitle'  => __('This text will display in the page title of the assigned blog page.', 'Vela'),
                        'default'   => 'Blog'
                    ),
                    array(
                        'id'        => 'blog_sidebar',
                        'type'      => 'image_select',
                        'title'     => __('Sidebar', 'Vela'),
                        'subtitle'  => __('Select blog page sidebar position.', 'Vela'),
                        'options'   => array(
                            '1' => array('alt' => 'No Sidebar', 'img' => $template_directory . '/images/columns/1.png'),
                            '2' => array('alt' => 'One Left', 'img' => $template_directory . '/images/columns/2.png'),
                            '3' => array('alt' => 'One Right', 'img' => $template_directory . '/images/columns/3.png'),
                        ),
                        'default'   => '3'
                    ),
                    array(
                        'id'        => 'blog_layout',
                        'type'      => 'image_select',
                        'title'     => __('Layout', 'Vela'),
                        'subtitle'  => __('Select blog posts view.', 'Vela'),
                        'options'   => array(
                            'large' => array('alt' => 'Large', 'img' => $template_directory . '/images/blogviews/large.png'),
                            'medium' => array('alt' => 'Medium',  'img' => $template_directory . '/images/blogviews/medium.png'),
                            'masonry' => array('alt' => 'Masonry', 'img' => $template_directory . '/images/blogviews/masonry.png'),
                        ),
                        'default'   => 'large'
                    ),
                    array(
                        'id'        => 'blog_grid_columns',
                        'type'      => 'select',
                        'required'  => array('blog_layout', '=', 'masonry'),
                        'title'     => __('Masonry Columns', 'Vela'),
                        'subtitle'  => __('Select the number of grid columns.', 'Vela'),
                        'options'   => array(
                            '2' => '2 Columns',
                            '3' => '3 Columns',
                            '4' => '4 Columns'
                        ),
                        'default'   => '3'

                    ),
                    array(
                        'id'        => 'blog_pagination',
                        'type'      => 'select',
                        'title'     => __('Pagination Type', 'Vela'),
                        'subtitle'  => __('Select the pagination type for blog page.', 'Vela'),
                        'options'   => array(
                            '1' => 'Numeric Pagination',
                            '2' => 'Infinite Scroll',
                            '3' => 'Next and Previous',
                        ),
                        'default'   => '1'
                    ),
                    array(
                        'id'        => 'section_blog_single',
                        'type'      => 'section',
                        'title'     => __('Blog Single Post', 'Vela'),
                        'subtitle'  => __('Customize blog single post.', 'Vela'),
                        'indent'    => true
                    ),
                    array(
                        'id'        => 'blog_single_sidebar',
                        'type'      => 'image_select',
                        'title'     => __('Sidebar', 'Vela'),
                        'subtitle'  => __('Select blog single post sidebar position.', 'Vela'),
                        'options'   => array(
                            '1' => array('alt' => 'No Sidebar', 'img' => $template_directory . '/images/columns/1.png'),
                            '2' => array('alt' => 'One Left', 'img' => $template_directory . '/images/columns/2.png'),
                            '3' => array('alt' => 'One Right', 'img' => $template_directory . '/images/columns/3.png'),
                        ),
                        'default'   => '3'
                    ),
                    array(
                        'id'        => 'blog_single_tags',
                        'type'      => 'switch',
                        'title'     => __('Post Tags', 'Vela'),
                        'subtitle'  => __('Display post tags.', 'Vela'),
                        'default'   => 1
                    ),
                    array(
                        'id'        => 'blog_single_author',
                        'type'      => 'switch',
                        'title'     => __('Author Box', 'flora'),
                        'subtitle'  => __('Display author description box.', 'flora'),
                        'default'   => false
                    ),
                    array(
                        'id'        => 'blog_single_nav',
                        'type'      => 'switch',
                        'title'     => __('Post Navigation', 'Vela'),
                        'subtitle'  => __('Display next and previous posts.', 'Vela'),
                        'default'   => 1
                    ),
                    array(
                        'id'        => 'blog_single_comment',
                        'type'      => 'switch',
                        'title'     => __('Comments', 'Vela'),
                        'subtitle'  => __('Display comments box.', 'Vela'),
                        'default'   => 1
                    ),
                    array(
                        'id'        => 'blog_single_related',
                        'type'      => 'switch',
                        'title'     => __('Related Posts', 'Vela'),
                        'subtitle'  => __('Display related posts.', 'Vela'),
                        'default'   => 1
                    ),
                    array(
                        'id'        => 'blog_single_related_title',
                        'type'      => 'text',
                        'required'  => array('blog_single_related', '=', 1),
                        'title'     => __('Related Posts Title', 'Vela'),
                        'subtitle'  => __('The title of related posts box.', 'Vela'),
                        'default'   => 'Related Posts'
                    ),
                    array(
                        'id'        => 'blog_single_related_posts',
                        'type'      => 'select',
                        'required'  => array('blog_single_related', '=', 1),
                        'title'     => __('Number of related posts', 'Vela'),
                        'subtitle'  => __('Select the number of posts to show in related posts.', 'Vela'),
                        'options'   => array(
                            '1' => '1',
                            '2' => '2',
                            '3' => '3',
                            '4' => '4',
                            '5' => '5',
                            '6' => '6',
                            '7' => '7',
                            '8' => '8',
                            '9' => '9',
                            '10' => '10',
                        ),
                        'default'   => '4'

                    ),
                    array(
                        'id'        => 'section_blog_meta',
                        'type'      => 'section',
                        'title'     => __('Blog Meta', 'Vela'),
                        'subtitle'  => __('Customize blog meta data options.', 'Vela'),
                        'indent'    => true
                    ),
                    array(
                        'id'        => 'blog_meta_author',
                        'type'      => 'switch',
                        'title'     => __('Blog Author Name', 'Vela'),
                        'subtitle'  => __('Display blog author meta data.', 'Vela'),
                        'default'   => 1
                    ),
                    array(
                        'id'        => 'blog_meta_date',
                        'type'      => 'switch',
                        'title'     => __('Post Date', 'Vela'),
                        'subtitle'  => __('Display blog post date.', 'Vela'),
                        'default'   => 1
                    ),
                    array(
                        'id'        => 'blog_meta_time',
                        'type'      => 'switch',
                        'title'     => __('Post Time', 'Vela'),
                        'subtitle'  => __('Display blog post time.', 'Vela'),
                        'default'   => 1
                    ),
                    array(
                        'id'        => 'blog_meta_category',
                        'type'      => 'switch',
                        'title'     => __('Category', 'Vela'),
                        'subtitle'  => __('Display blog meta category.', 'Vela'),
                        'default'   => 1
                    ),
                    array(
                        'id'        => 'blog_meta_comment',
                        'type'      => 'switch',
                        'title'     => __('Comment', 'Vela'),
                        'subtitle'  => __('Display blog meta comment.', 'Vela'),
                        'default'   => 1
                    ),
                    array(
                        'id'        => 'blog_meta_share',
                        'type'      => 'switch',
                        'title'     => __('Social Sharing Icons', 'Vela'),
                        'subtitle'  => __('Display blog social sharing icons.', 'Vela'),
                        'default'   => 1
                    )


                )
            );

            //Portfolio
            $this->sections['portfolio'] = array(
                'icon'      => 'el-icon-edit',
                'title'     => __('Portfolio', 'Vela'),
                'heading'   => false,
                'fields'    => array(
                    array(
                        'id'        => 'portfolio_slug',
                        'type'      => 'text',
                        'title'     => __('Portfolio Slug', 'Vela'),
                        'subtitle'  => __('Change/Rewrite the permalink when you use the permalink type as %postname%.', 'Vela'),
                        'default'   => 'portfolio-item'
                    ),
                    array(
                        'id'        => 'portfolio_sidebar',
                        'type'      => 'image_select',
                        'title'     => __('Sidebar', 'Vela'),
                        'subtitle'  => __('Select portfolio single post sidebar position.', 'Vela'),
                        'options'   => array(
                            '1' => array('alt' => 'No Sidebar', 'img' => $template_directory . '/images/columns/1.png'),
                            '2' => array('alt' => 'One Left', 'img' => $template_directory . '/images/columns/2.png'),
                            '3' => array('alt' => 'One Right', 'img' => $template_directory . '/images/columns/3.png'),
                        ),
                        'default'   => '3'
                    ),    
                    array(
                        'id'        => 'portfolio_lightbox_size',
                        'type'      => 'select',
                        'title'     => __('Lightbox Image Size', 'Vela'),
                        'subtitle'  => __('Select portfolio lightbox image size.', 'Vela'),
                        'options'   => array(
                            'blog-large' => 'Large (800 x 450)',
                            'blog-full' => 'Full (1024 x 768)',
                            'full' => 'Original',
                        ),
                        'default'   => 'full'
                    ),
                    array(
                        'id'        => 'portfolio_nav',
                        'type'      => 'switch',
                        'title'     => __('Post Navigation', 'Vela'),
                        'subtitle'  => __('Display next and previous posts.', 'Vela'),                        
                        'default'   => 1
                    ),
                    array(
                        'id'        => 'portfolio_home',
                        'type'      => 'switch',
                        'required'  => array('portfolio_nav', '=', 1),
                        'title'     => __('Home Button', 'Vela'),
                        'subtitle'  => __('Display the "Home" button on portfolio single post.', 'Vela'),
                        'default'   => 0
                    ),
                    array(
                        'id'        => 'portfolio_home_url',
                        'type'      => 'text',
                        'required'  => array('portfolio_home', '=', 1),
                        'title'     => __('Portfolio Home Page', 'Vela'),
                        'subtitle'  => __('The portfolio home page URL for the "Home" button on portfolio single post.', 'Vela'),
                        'default'   =>  get_site_url(get_current_blog_id(), '/porfolio') 
                    ),
                    array(
                        'id'        => 'portfolio_related',
                        'type'      => 'switch',
                        'title'     => __('Related Posts', 'Vela'),
                        'subtitle'  => __('Display related posts.', 'Vela'),
                        'default'   => 1
                    ),
                    array(
                        'id'        => 'portfolio_related_title',
                        'type'      => 'text',
                        'required'  => array('portfolio_related', '=', 1),
                        'title'     => __('Related Posts Title', 'Vela'),
                        'subtitle'  => __('The title of related posts box.', 'Vela'),
                        'default'   => 'Related Projects'
                    ),
                    array(
                        'id'        => 'portfolio_related_posts',
                        'type'      => 'select',
                        'required'  => array('portfolio_related', '=', 1),
                        'title'     => __('Number of related posts', 'Vela'),
                        'subtitle'  => __('Select the number of posts to show in related posts.', 'Vela'),
                        'options'   => array(
                            '1' => '1',
                            '2' => '2',
                            '3' => '3',
                            '4' => '4',
                            '5' => '5',
                            '6' => '6',
                            '7' => '7',
                            '8' => '8',
                            '9' => '9',
                            '10' => '10',
                        ),
                        'default'   => '4'

                    )
                )
            );

            //WooCommerce
            $this->sections['woocommerce'] = array(
                'icon'      => 'el-icon-shopping-cart',
                'title'     => __('WooCommerce', 'Vela'),
                'heading'   => false,
                'fields'    => array(
                    array(
                        'id'        => 'product_items',
                        'type'      => 'text',
                        'title'     => __('Number of Products per Page', 'Vela'),
                        'subtitle'  => __('Enter the number of products per page.', 'Vela'),
                        'validate'  => 'numeric',
                        'default'   => '8'
                        
                    ),
                    array(
                        'id'        => 'section_shop_single',
                        'type'      => 'section',
                        'title'     => __('Single Product', 'Vela'),
                        'subtitle'  => __('Customize shop single product.', 'Vela'),
                        'indent'    => true
                    ),
                    array(
                        'id'        => 'shop_single_sidebar',
                        'type'      => 'image_select',
                        'title'     => __('Shop Single Sidebar', 'Vela'),
                        'subtitle'  => __('Select shop single product sidebar position.', 'Vela'),
                        'options'   => array(
                            '1' => array('alt' => 'No Sidebar', 'img' => $template_directory . '/images/columns/1.png'),
                            '2' => array('alt' => 'One Left', 'img' => $template_directory . '/images/columns/2.png'),
                            '3' => array('alt' => 'One Right', 'img' => $template_directory . '/images/columns/3.png'),
                        ),
                        'default'   => '1'
                    ),
                    array(
                        'id'        => 'related_product_items',
                        'type'      => 'select',
                        'title'     => __('Number of Related Products', 'Vela'),
                        'subtitle'  => __('Select the number of related products.', 'Vela'),
                        'options'   => array(
                            '1' => '1',
                            '2' => '2',
                            '3' => '3',
                            '4' => '4',
                            '5' => '5',
                            '6' => '6',
                            '7' => '7',
                            '8' => '8',
                            '9' => '9',
                            '10' => '10',
                        ),
                        'default'   => '4'
                    )
                  )
            );


            //Search
            $this->sections['search'] = array(
                'icon'      => 'el-icon-search',
                'title'     => __('Search', 'Vela'),
                'heading'   => false,
                'fields'    => array(
                    array(
                        'id'        => 'search_sidebar',
                        'type'      => 'image_select',
                        'title'     => __('Sidebar', 'Vela'),
                        'subtitle'  => __('Select search page sidebar position.', 'Vela'),
                        'options'   => array(
                            '1' => array('alt' => 'No Sidebar',       'img' => $template_directory . '/images/columns/1.png'),
                            '2' => array('alt' => 'One Left',  'img' => $template_directory . '/images/columns/2.png'),
                            '3' => array('alt' => 'One Right', 'img' => $template_directory . '/images/columns/3.png'),
                        ),
                        'default'   => '1'
                    ),
                    array(
                        'id'        => 'search_items',
                        'type'      => 'text',
                        'title'     => __('Number of Search Results Per Page', 'Vela'),
                        'subtitle'  => __('Enter the number of search results per page.', 'Vela'),
                        'validate'  => 'numeric',
                        'default'   => '8'
                        
                    ),
                    array(
                        'id'        => 'search_show_image',
                        'type'      => 'switch',
                        'title'     => __('Show Featured Image.', 'Vela'),
                        'subtitle'  => __('Display featured images in search results.', 'Vela'),
                        'default'   => 1
                    ),
                    array(
                        'id'        => 'search_show_date',
                        'type'      => 'switch',
                        'title'     => __('Show Post Date.', 'Vela'),
                        'subtitle'  => __('Display post date in search results.', 'Vela'),
                        'default'   => 1
                    ),
                    array(
                        'id'        => 'search_show_author',
                        'type'      => 'switch',
                        'title'     => __('Show Author.', 'Vela'),
                        'subtitle'  => __('Display post author in search results.', 'Vela'),
                        'default'   => 1
                    ),
                    array(
                        'id'        => 'ajax_search',
                        'type'      => 'switch',
                        'title'     => __('Ajax Search', 'Vela'),
                        'subtitle'  => __('Enable ajax auto suggest search.', 'Vela'),
                        'default'   => 1
                    ),
                    array(
                        'id'        => 'section_ajax_search',
                        'type'      => 'section',
                        'required'  => array('ajax_search', '=', 1),
                        'indent'    => true
                    ),
                    array(
                        'id'        => 'search_post_type',
                        'type'      => 'checkbox',
                        'title'     => __('Post Types', 'Vela'),
                        'subtitle'  => __('Select post types for ajax auto suggest search.', 'Vela'),
                        'data'  => 'post_types',
                        'default'   => array(
                            'page' => 1,
                            'post' => 1,
                            'portfolio' => 1,
                            'product'   => 1
                        )
                    ),
                    array(
                        'id'        => 'search_suggestion_items',
                        'type'      => 'select',
                        'title'     => __('Number of Suggestion Items.', 'Vela'),
                        'subtitle'  => __('Enter the number of search suggestion items per post type.', 'Vela'),
                        'options'  => array(
                            '1' => '1',
                            '2' => '2',
                            '3' => '3',
                            '4' => '4',
                            '5' => '5',
                            '6' => '6',
                            '7' => '7',
                            '8' => '8',
                            '9' => '9',
                            '10' => '10',
                        ),
                        'default'   => '5'
                        
                    )
                  )
            );

            //Social Media
            $social_fields = array();

            $social_icons = wyde_get_social_icons(); // get social icons from inc/custom-functions.php

            foreach($social_icons as $key => $value){
               $social_fields[] = array(
                        'id'        => 'social_'. wyde_string_to_underscore_name($value),
                        'type'      => 'text',
                        'title'     => $value,
               ); 
            }

            $this->sections['social'] = array(
                'icon'      => 'el-icon-group',
                'title'     => __('Social Media', 'Vela'),
                'fields'    => $social_fields
            );

            //Typography
            $this->sections['typography'] = array(
                'icon'      => 'el-icon-font',
                'title'     => __('Typography', 'Vela'),
                'desc'     => __('Customize font options for your site.', 'Vela'),
                'fields'    => array(
                    array(
                        'id'            => 'font_body',
                        'type'          => 'typography',
                        'title'         => 'Body',
                        'subtitle'      => __('Font options for main body text.', 'Vela'),
                        'google'        => true,    
                        'font-style'    => false, 
                        'all_styles'    => true,  
                        'letter-spacing'=> true,
                        'font-backup'   => true,
                        'units'         => 'px',
                        'output'        => array('body'),
                        'default'       => array(
                            'font-family'   => 'Lato',
                            'google'        => true,
                            'font-size'     => '15px',
                            'line-height'   => '22px',
                            'font-backup'   => "Arial, Helvetica, sans-serif"
                        ),
                        'preview' => array('text' => 'Vela Body Text <br /> 1234567890 <br /> ABCDEFGHIJKLMNOPQRSTUVWXYZ <br /> abcdefghijklmnopqrstuvwxyz'),
                    ),
                    
                    array(
                        'id'            => 'font_menu',
                        'type'          => 'typography',
                        'title'         => 'Menu',
                        'subtitle'      => __('Font options for main navigation menu.', 'Vela'),
                        'google'        => true,    
                        'font-style'    => false, 
                        'color'    => false, 
                        'font-size'    => false, 
                        'text-align'    => false,
                        'all_styles'    => true,
                        'letter-spacing'=> true,
                        'font-backup'   => true,
                        'line-height'   => false,   
                        'units'         => 'px', 
                        'output'        => array('#header .nav-wrapper > #nav > ul > li > a'),
                        'default'       => array(
                            'font-family'   => 'Oswald',
                            'google'        => true,
                            'letter-spacing'    => '1px',
                            'font-backup'   => "Arial, Helvetica, sans-serif"
                        ),
                        'preview' => array('text' => 'Vela Main Menu <br /> 1234567890 <br /> ABCDEFGHIJKLMNOPQRSTUVWXYZ <br /> abcdefghijklmnopqrstuvwxyz'),
                    ),                     
                    array(
                        'id'            => 'font_h1',
                        'type'          => 'typography',
                        'title'         => 'H1',
                        'subtitle'      => __('Font options for H1.', 'Vela'),
                        'google'        => true,    
                        'font-style'    => false, 
                        'all_styles'    => true,
                        'letter-spacing'=> true,
                        'font-backup'   => true,
                        'units'         => 'px', 
                        'output'        => array('h1'),
                        'default'       => array(
                            'font-family'   => 'Oswald',
                            'google'        => true,
                            'font-size'     => '48px',
                            'line-height'   => '58px',
                            'font-backup'   => "Arial, Helvetica, sans-serif"
                        ),
                        'preview' => array('text' => 'Vela Main Menu <br /> 1234567890 <br /> ABCDEFGHIJKLMNOPQRSTUVWXYZ <br /> abcdefghijklmnopqrstuvwxyz'),
                    ),                     
                    array(
                        'id'            => 'font_h2',
                        'type'          => 'typography',
                        'title'         => 'H2',
                        'subtitle'      => __('Font options for H2.', 'Vela'),
                        'google'        => true,    
                        'font-style'    => false, 
                        'all_styles'    => true,
                        'letter-spacing'=> true,
                        'font-backup'   => true,
                        'units'         => 'px', 
                        'output'        => array('h2'),
                        'default'       => array(
                            'font-family'   => 'Oswald',
                            'google'        => true,
                            'font-size'     => '40px',
                            'line-height'   => '52px',
                            'font-backup'   => "Arial, Helvetica, sans-serif"
                         ),
                        'preview' => array('text' => 'Vela Main Menu <br /> 1234567890 <br /> ABCDEFGHIJKLMNOPQRSTUVWXYZ <br /> abcdefghijklmnopqrstuvwxyz'),
                    ),                     
                    array(
                        'id'            => 'font_h3',
                        'type'          => 'typography',
                        'title'         => 'H3',
                        'subtitle'      => __('Font options for H3.', 'Vela'),
                        'google'        => true,    
                        'font-style'    => false, 
                        'all_styles'    => true,
                        'letter-spacing'=> true,
                        'font-backup'   => true,
                        'units'         => 'px', 
                        'output'        => array('h3'),
                        'default'       => array(
                            'font-family'   => 'Oswald',
                            'google'        => true,
                            'font-size'     => '22px',
                            'line-height'   => '28px',
                            'font-backup'   => "Arial, Helvetica, sans-serif"
                         ),
                        'preview' => array('text' => 'Vela Main Menu <br /> 1234567890 <br /> ABCDEFGHIJKLMNOPQRSTUVWXYZ <br /> abcdefghijklmnopqrstuvwxyz'),
                    ),                     
                    array(
                        'id'            => 'font_title',
                        'type'          => 'typography',
                        'title'         => __('Title Text', 'Vela'),
                        'subtitle'      => __('Font options for title text in visual composer elements, revolution slider and shortcodes in your website.', 'Vela'),
                        'google'        => true,    
                        'font-size'     => false, 
                        'line-height'   => false, 
                        'color'         => false, 
                        'text-align'    => false, 
                        'font-style'    => false, 
                        'font-weight'   => false, 
                        'subsets'       => false, 
                        'all_styles'    => false,
                        'letter-spacing'=> false,
                        'font-backup'   => true,
                        'units'         => 'px', 
                        'output'        => array('h4, h5, h6, .post-title, .post-title a', '.counter-box p, .vc_pie_chart .vc_pie_chart_value, .vc_progress_bar .vc_single_bar .vc_label, .wpb_accordion .wpb_accordion_wrapper .wpb_accordion_header, .wpb_tabs_nav a'),
                        'default'       => array(
                            'font-family'   => 'Oswald',
                            'google'        => true,
                            'font-backup'   => "Arial, Helvetica, sans-serif"
                         ),
                        'preview' => array('text' => 'Vela Main Menu <br /> 1234567890 <br /> ABCDEFGHIJKLMNOPQRSTUVWXYZ <br /> abcdefghijklmnopqrstuvwxyz'),
                    )                    
                )
            );

            /***************************** 
            * Google API Options
            ******************************/
            $this->sections['google_api'] = array(
                'icon'      => 'el el-map-marker',
                'title'     => esc_html__('Google Maps', 'overlap'),
                'heading'     => false,
                'fields'    => array(
                    array(
                        'id'        => 'google_maps_api_key',
                        'type'      => 'text',
                        'title'     => esc_html__('Google Maps API Key', 'overlap'),
                        'subtitle'  => __('Enter your Google Maps API key, <a href="https://developers.google.com/maps/documentation/javascript/get-api-key#key" target="_blank">Get an API key</a>', 'overlap'),
                        'default'   => ''
                    )
                )
            );


            //Advanced
            $this->sections['advanced'] = array(
                'icon'      => 'el-icon-wrench',
                'title'     => __('Advanced', 'Vela'),
                'heading'   => false,
                'fields'    => array(
                    array(
                        'id'        => 'head_script',
                        'type'      => 'ace_editor',
                        'title'     => __('Head Content', 'Vela'),
                        'subtitle'  => __('Enter HTML/JavaScript/StyleSheet code that insert into the head tag. You can also add a Google Analytics code, Google Verification code or custom Meta HTTP Content here.', 'Vela'),
                        'mode'  => 'html'
                        
                    ),
                    array(
                        'id'        => 'footer_script',
                        'type'      => 'ace_editor',
                        'title'     => __('Body Content', 'Vela'),
                        'subtitle'  => __('Enter HTML/JavaScript/StyleSheet code that insert into the body tag. This will be added into the footer template of your theme.', 'Vela'),
                        'mode'  => 'html'
                        
                    ),
                  )
            );
                       
            //Import / Export Settings
            $this->sections['import_export'] = array(
                'title'     => __('Import / Export', 'Vela'),
                'desc'      => __('Import and Export your Redux Framework settings from file, text or URL.', 'Vela'),
                'icon'      => 'el-icon-refresh',
                'fields'    => array(
                    array(
                        'id'            => 'opt-import-export',
                        'type'          => 'import_export',
                        'title'         => 'Import Export',
                        'subtitle'      => 'Save and restore your Theme options',
                        'full_width'    => false,
                    ),
                ),
            );                     

            $this->theme    = wp_get_theme();
            $item_name      = $this->theme->get('Name');
            $screenshot     = $this->theme->get_screenshot();
            $class          = $screenshot ? 'has-screenshot' : '';

            $customize_title = __('Customize', 'Vela') . ' '. $this->theme->display('Name');

            ob_start();
            ?>
            <div id="current-theme" class="<?php echo esc_attr($class); ?>">
            <?php if ($screenshot) : ?>
                <?php if (current_user_can('edit_theme_options')) : ?>
                <img src="<?php echo esc_url($screenshot); ?>" alt="<?php echo esc_attr($item_name);?>" />
                <?php endif; ?>
                <img class="hide-if-customize" src="<?php echo esc_url($screenshot); ?>" alt="<?php echo esc_attr($item_name);?>" />
                <?php endif; ?>
                <h4><?php echo esc_html( $this->theme->display('Name') ); ?></h4>
                <div>
                    <p><?php echo __('By', 'Vela'). ' '. $this->theme->display('Author') ; ?></p>
                    <p><?php echo __('Version', 'Vela'). ' '. esc_html( $this->theme->display('Version') ); ?></p>
                    <p class="theme-description"><?php echo wp_kses_post( $this->theme->display('Description') ); ?></p>
                    <p><?php echo __('Tags', 'Vela'). ' '. esc_html( $this->theme->display('Tags') ); ?></p>
                    <?php
                    if ($this->theme->parent()) {
                       echo '<p class="howto">' . __('This <a href="http://codex.wordpress.org/Child_Themes">child theme</a> requires its parent theme', 'Vela') . ', '. esc_html( $this->theme->parent()->display('Name') ). '</p>';
                    }
                    ?>
                </div>
            </div>
            <?php
            $item_info = ob_get_clean();

            $this->sections[] = array(
                'type' => 'divide',
            );

            // Theme Information
            $this->sections['theme_info'] = array(
                'icon'      => 'el el-info-circle',
                'title'     => __('Theme Information', 'Vela'),
                'desc'      => '<p class="description">' . __('For more information and features about this theme, please visit', 'Vela') . ' <a href="'. esc_url( $this->theme->display('AuthorURI') ) .'" target="_blank">'. esc_url( $this->theme->display('AuthorURI') ) . '</a>.</p>',
                'fields'    => array(
                    array(
                        'id'        => 'raw-theme-info',
                        'type'      => 'raw',
                        'content'   => $item_info,
                    )
                ),
            );

        }

        /**
         Set framework arguments
         * */
        public function setArguments() {

            $this->args = array(
                'display_name' => $this->theme->get('Name'),
                'display_version' =>  $this->theme->get('Version'),
                'opt_name' => 'wyde_options',
                'page_slug' => 'theme-options',
                'page_title' =>  __('Theme Options', 'Vela'),
                'menu_type' => 'menu',
                'menu_title' => __('Theme Options', 'Vela'),
                'page_parent'  => 'themes.php',
                'allow_sub_menu' => false,
                'customizer' => false,
                'update_notice' => false,
                'dev_mode'  => false,
                'admin_bar' => true,
                'admin_bar_icon'    => 'dashicons-admin-generic',
                'hints' => 
                        array(
                          'icon' => 'el-icon-question-sign',
                          'icon_position' => 'right',
                          'icon_size' => 'normal',
                          'tip_style' => 
                          array(
                            'color' => 'light',
                          ),
                          'tip_position' => 
                          array(
                            'my' => 'top left',
                            'at' => 'bottom right',
                          ),
                          'tip_effect' => 
                          array(
                            'show' => 
                            array(
                              'duration' => '500',
                              'event' => 'mouseover',
                            ),
                            'hide' => 
                            array(
                              'duration' => '500',
                              'event' => 'mouseleave unfocus',
                            ),
                          ),
                ),
                'output' => true,
                'compiler'  => true,
                'output_tag' => true,
                'menu_icon' => '',
                'page_icon' => 'icon-themes',
                'page_permissions' => 'manage_options',
                'save_defaults' => true,
                'show_import_export' => true,
                'transient_time' => '3600',
                'network_sites' => true,
                'disable_tracking'  => true,
                'google_api_key'   => 'AIzaSyBss9ufj66aGyREW-VQdhuDSJ4opKsD-4U',
                'async_typography' => false,
                'intro_text' => '',
                'footer_text' => '',
                'footer_credit' => '<span id="footer-thankyou">Thank you for creating with <a href="https://wordpress.org/">WordPress</a>.</span>'
              );


            // SOCIAL ICONS -> Setup custom links in the footer for quick links in your panel footer icons.
            $this->args['share_icons'][] = array(
                'url'   => 'http://themeforest.net/user/Wyde',
                'title' => 'Help',
                'icon'  => 'el-icon-question-sign'
            );
            $this->args['share_icons'][] = array(
                'url'   => 'http://themeforest.net/user/Wyde/follow',
                'title' => 'Follow us on ThemeForest',
                'icon'  => 'el-icon-heart'
            );
            $this->args['share_icons'][] = array(
                'url'   => 'http://themeforest.net/downloads',
                'title' => 'Give me a rating on ThemeForest',
                'icon'  => 'el-icon-star'
            );

        }

    }
    
    global $wyde_theme_options;
    $wyde_theme_options = new Wyde_Theme_Options();
}