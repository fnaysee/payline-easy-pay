<?php
/**
 * The scripts boot class
 * all needed data are already loaded before or in this class.
 */
final class Loader
{
    const version = '1.0';
    
    private static $_instance = null;
    
    public static function __instance()
    {
        if( is_null( self::$_instance ) )
            self::$_instance = new self;
        
        return self::$_instance;
    }
    
    public function __construct()
    {
        $this->init();
    }
    
    private function init()
    {
        $this->fill_vars();
        do_action( 'vars_filled' );
        
        //on next versions
        //data()->plugins->load_active_plugins();
        //do_action( 'plugins_functions_loaded' );

        data()->templates->load_template_functions();
        do_action( 'template_functions_loaded' );
        
        if( isset( $_GET['after_payment'] ) )
        {
            data()->payline = new Payline;
        }
        
        if( ENABLEADMIN && ( isset( $_GET['login'] ) || isset( $_GET['admin'] ) || isset( $_GET['logout'] ) ) )
        {
            require_once ADMINPATH . 'includes/class-login.php';
            data()->login = new Login;
            
            if( ! data()->login->is_user_logged_in() )
            {
                if( isset( $_POST['login_form'] ) )
                {
                    data()->login->verify();
                }
                else
                {
                    data()->login->load_login_page();
                }
            }
            elseif( isset( $_GET['logout'] ) || data()->login->login_is_old() )
            {
                data()->login->logout_the_user();
            }
            else
            {
                include_once ADMINPATH . 'includes/class-users.php';
                include_once ADMINPATH . 'includes/class-user.php';
                include_once ADMINPATH . 'index.php';
                data()->users = new Users;
                data()->current_user = new User( data()->sessions->get_session( 'user_session' ) );
            	data()->admin = new Admin;
                do_action( 'start_admin_load' );
                data()->admin->do_load_admin();
            }
            
            
            die();
        }
        elseif( isset( $_GET['login'] ) || isset( $_GET['admin'] ) )
        {
            echo 'Admin and login access disabled by administrator.';
            die();
        }
        
        
        do_action( 'template_load' );

    }
    
    private function fill_vars() 
    {
        if( class_exists( 'options' ) )
            data()->options = new Options;
        data()->site_title = data()->options->get_option( 'site_title' );
        //on next versions
        //if( class_exists( 'Plugin_Loader' ) )
        //    data()->plugins = new Plugin_Loader;
        if( class_exists( 'Template_Loader' ) )
            data()->templates = new Template_Loader;
        if( class_exists( 'Settings_API' ) )
            data()->settings_api = new Settings_API;
        if( class_exists( 'Payments' ) )
            data()->payment = new Payments;
        if( class_exists( 'Form' ) )
            data()->form = new Form;

    }
}

function epy()
{
    return Loader::__instance();
}
####################################################
# Helper functions for using in themes and plugins
####################################################
function get_site_info( $key )
{
    switch ( $key )
    {
        case 'active_template_path':
            return data()->templates->template_directory();
        case 'active_template_url':
            return data()->templates->template_url();
        case 'plugins_path':
            return PLUGINSPATH;
        case 'plugins_url':
            return PLUGINSURL;
        case 'templates_path':
            return TEMPLATESPATH;
        case 'templates_url':
            return TEMPLATESURL;
        case 'site_path':
            return ROOTPATH;
        case 'site_title':
            return data()->site_title;
        case 'site_url':
            return ROOTURL;
        default:
            return false;
    }
}

function the_active_template_path()
{
    echo get_site_info( 'active_template_url' );
}

function the_active_template_url()
{
    echo get_site_info( 'active_template_url' );
}

function the_plugins_path()
{
    echo get_site_info( 'plugins_path' );
}

function the_plugins_url()
{
    echo get_site_info( 'plugins_url' );
}

function the_templates_path()
{
    echo get_site_info( 'templates_path' );
}

function the_templates_url()
{
    echo get_site_info( 'templates_url' );
}

function the_site_path()
{
    echo get_site_info( 'site_path' );
}

function the_site_title()
{
    echo get_site_info( 'site_title' );
}

function the_site_url()
{
    echo get_site_info( 'site_url' );
}


### Templating functions

function the_template_head()
{
    do_action( 'template_head' );
}

function the_template_body()
{
    do_action( 'template_body' );
}

function the_template_footer()
{
    do_action( 'template_footer' );
}

function after_body_open()
{
    do_action( 'after_body_open_tag' );
}

function before_body_close()
{
    do_action( 'before_body_close_tag' );
}

function the_form()
{
    data()->form->the_payment_form();
}