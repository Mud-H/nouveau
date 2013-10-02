<?php
namespace NV\Hooks;

/**
 * Contains functions for reconfiguring the admin back-end. Generally, method names should match the hook name for
 * easy identification. In cases where a generic hook is utilized, a more logical method name should be used.
 */
class Theme {


    /**
     * Sets up basic theme features.
     *
     * Used by action hook: 'after_setup_theme'
     *
     * @uses self::languages();
     */
    public static function after_setup_theme()
    {
        load_theme_textdomain( 'nvLangScope', trailingslashit(THEME_DIR).'assets/languages' );

        add_theme_support('automatic-feed-links');
        add_theme_support('custom-header', array(
            'height'        => 200,
            'width'         => 980,
            'flex-height'   => true,
            'flex-width'    => true,
            'default-image' => 'http://placehold.it/980x200', //or try '%s/assets/images/headers/img.jpg',
//            'random-default'      => false,
//            'header-text'         => true,    //Bool. Allow header text?
//            'default-text-color'  => '',
//            'uploads'             => true,    //Allow upload of custom headers
//            'wp-head-callback'    => '',
//            'admin-head-callback' => '',
//            'admin-preview-callback => '',
        ));
        add_theme_support('custom-background', array(
//            'default-image'             => '',
            'default-color'               => '#fff',
//            'wp-head-callback'          => '_custom_background_cb',
//            'admin-head-callback'       => '',
//            'admin-preview-callback'    => '',
        ));
        add_theme_support('post-thumbnails');
        add_theme_support('post-formats', array(
            'aside',
            'audio',
            'chat',
            'gallery',
            'image',
            'link',
            'quote',
            'status',
            'video',
        ));

        register_nav_menu('primary', __('Primary Menu', 'nvLangScope'));
        register_nav_menu('footer', __('Footer Menu', 'nvLangScope'));

        /*
         * Set up any default values needed for theme options. If a default value
         * is needed, it can be provided as a second parameter. This will NOT
         * overwrite any existing options with these names.
         */
        add_option('register_notify', true); //Setting for registration notifications to admins
        add_option('password_notify', true); //Setting for password reset notifications to admins
//        add_option('nouveau_example_checkbox');
//        add_option('nouveau_example_radio');
//        add_option('nouveau_example_text', 'This is example default text.');
//        add_option('nouveau_example_select');

    }


    /**
     * Enqueues styles and scripts
     *
     * Used by action hook: 'wp_enqueue_scripts'
     */
    public static function enqueue_assets()
    {
        /** STYLES **/
        // Base stylesheet (compiled SASS)
        wp_enqueue_style( 'app', NV_CSS.'/app.css' );

        // WordPress's required styles.css (will override compiled SASS)
        wp_enqueue_style( 'styles', get_bloginfo( 'stylesheet_url' ), array( 'app' ) );


        /** SCRIPTS **/
        // Get rid of WordPress's default jQuery so we can use something else...
//        wp_dequeue_script( 'jquery' );

        // ...so we can use our own copy of jQuery...
//        wp_enqueue_script( 'jquery', NV_JS.'/jquery.min.js');

        // ...OR use our own copy of Zepto (leave the handle 'jquery' for best compatibility).
//        wp_enqueue_script( 'jquery', NV_JS.'/zepto.min.js', array(), false, true );

        // Load modernizr in the head
        wp_enqueue_script( 'modernizr', NV_JS.'/custom.modernizr.min.js', false, false );

        // Load foundation in the footer
        wp_enqueue_script( 'foundation', NV_JS.'/foundation.min.js', array( 'jquery' ), false, true );
        wp_enqueue_script( 'zf-alerts', NV_JS.'/foundation.alerts.min.js', array( 'jquery', 'foundation' ), false, true );
        wp_enqueue_script( 'zf-clearing', NV_JS.'/foundation.clearing.min.js', array( 'jquery', 'foundation' ), false, true );
        wp_enqueue_script( 'zf-cookie', NV_JS.'/foundation.cookie.min.js', array( 'jquery', 'foundation' ), false, true );
        wp_enqueue_script( 'zf-dropdown', NV_JS.'/foundation.dropdown.min.js', array( 'jquery', 'foundation' ), false, true );
        wp_enqueue_script( 'zf-forms', NV_JS.'/foundation.forms.min.js', array( 'jquery', 'foundation' ), false, true );
        wp_enqueue_script( 'zf-joyride', NV_JS.'/foundation.joyride.min.js', array( 'jquery', 'foundation' ), false, true );
        wp_enqueue_script( 'zf-magellan', NV_JS.'/foundation.magellan.min.js', array( 'jquery', 'foundation' ), false, true );
        wp_enqueue_script( 'zf-orbit', NV_JS.'/foundation.orbit.min.js', array( 'jquery', 'foundation' ), false, true );
        wp_enqueue_script( 'zf-placeholder', NV_JS.'/foundation.placeholder.min.js', array( 'jquery', 'foundation' ), false, true );
        wp_enqueue_script( 'zf-reveal', NV_JS.'/foundation.reveal.min.js', array( 'jquery', 'foundation' ), false, true );
        wp_enqueue_script( 'zf-section', NV_JS.'/foundation.section.min.js', array( 'jquery', 'foundation' ), false, true );
        wp_enqueue_script( 'zf-tooltips', NV_JS.'/foundation.tooltips.min.js', array( 'jquery', 'foundation' ), false, true );
        wp_enqueue_script( 'zf-topbar', NV_JS.'/foundation.topbar.min.js', array( 'jquery', 'foundation' ), false, true );

        // You can also use CodeKit to concatenate all of the above. If so, just enqueue this...
//        wp_enqueue_script( 'foundation', NV_JS.'/foundation.full.min.js', array( 'jquery' ), false, true );

        //Load our custom js
        wp_enqueue_script( 'app', NV_JS.'/app.min.js', array( 'jquery', 'foundation' ), false, true );

    }


    /**
     * Enqueues styles and scripts for the admin section
     *
     * Used by action hook: 'admin_enqueue_scripts'
     */
    public static function enqueue_admin_assets()
    {
        //Base admin styles
        wp_enqueue_style( 'nv-admin', NV_CSS.'/admin.css' );

        //Base admin scripts
        wp_enqueue_script( 'nv-admin', NV_CSS.'/admin.min.js', array('jquery'), false, false );
    }


    /**
     * This ensures that the 'sticky' class doesn't appear in any WordPress posts as that class has a very specific
     * function within Foundation (elements with that class will "stick" to the top of the window when you scroll
     * down). To get the best of both worlds, this function dynamically replaces WordPress's built-in 'sticky' class
     * with 'sticky-post' instead.
     *
     * Used by action hook: 'post_class'
     */
    public static function fix_sticky_class($classes)
    {
        $classes = array_diff($classes, array("sticky"));
        if ( is_sticky() ) {
            $classes[] = 'sticky-post';
        }
        return $classes;
    }


    /**
     * UNUSED.
     * Loads alternate languages, if available.
     *
     * @deprecated
     * @see self::setup()
     */
    public static function languages()
    {
        load_theme_textdomain( 'nvLangScope', trailingslashit(THEME_DIR).'assets/languages');

        $locale = get_locale();

        $locale_file = sprintf('%s/assets/languages/%s.php', untrailingslashit(THEME_DIR), $locale);
        if ( is_readable($locale_file) ) {
            require_once $locale_file;
        }
    }


    /**
     * Allows further customizations of the body_class() function.
     *
     * @param $classes
     * @param $args
     */
    public static function body_class($classes, $args = '')
    {
        //Do stuff!
        return $classes;
    }


    /**
     * Registers any sidebars that need to be used with the theme.
     *
     * Used by action hook: 'widget_init'
     */
    public static function sidebars()
    {

        register_sidebar(array(
            'name'          => __( 'Blog Sidebar', 'nvLangScope' ),
            'id'            => 'sidebar-1',
            'description'   => __( 'Drag widgets for Blog sidebar here. These widgets will only appear on the blog portion of your site.', 'nvLangScope' ),
            'before_widget' => '<aside id="%1$s" class="widget %2$s">',
            'after_widget'  => "</aside>",
            'before_title'  => '<h3 class="widget-title">',
            'after_title'   => '</h3>',
        ));
        register_sidebar(array(
            'name'          => __( 'Site Sidebar', 'nvLangScope' ),
            'id'            => 'sidebar-2',
            'description'   => __( 'Drag widgets for the Site sidebar here. These widgets will only appear on non-blog pages.', 'nvLangScope' ),
            'before_widget' => '<aside id="%1$s" class="widget %2$s">',
            'after_widget'  => "</aside>",
            'before_title'  => '<h3 class="widget-title">',
            'after_title'   => '</h3>',
        ));
        register_sidebar(array(
            'name'          => __( 'Footer', 'nvLangScope' ),
            'id'            => 'sidebar-3',
            'description'   => __( 'Drag footer widgets here.', 'nvLangScope' ),
            'before_widget' => '<aside id="%1$s" class="widget %2$s">',
            'after_widget'  => "</aside>",
            'before_title'  => '<h3 class="widget-title">',
            'after_title'   => '</h3>',
        ));
    }


}