<?php

defined( 'ROOTPATH' ) or header("Location: ../index.php?login=1&pos=admin");

final class Admin 
{
    private static $registered_pages = null;
    
    private $page = null;
    
    public $requested_page_title = null;
    
    private $old_user = false;
    
    public function __construct()
    {
        $this->set_vars();
    }
    
    private function set_vars()
    {
        if( ! isset( $_GET['page'] ) )
        {
        	$this->page = 'dashboard';
            $this->requested_page_title = self::$registered_pages[ 'dashboard' ]['title'];
        }
        elseif( isset( self::$registered_pages[ $_GET['page'] ] ) )
        {
            $this->page = $_GET['page'];
            $this->requested_page_title = self::$registered_pages[ $_GET['page'] ]['title'];
        }
        else
        {
            $this->page = $_GET['page'];
            $this->requested_page_title = 'صفحه ناشناس';
        }
        
    }
    
    public function do_load_admin()
    {
        $this->register_admin_page( 'داشبورد', array( $this, 'print_dashboard_page' ) , 'dashboard', INCLUDESURL . 'images/dashboard-20px.png', true, 1 );
        $this->register_admin_page( 'تنظیمات', array( $this, 'print_settings_page' ) , 'settings', INCLUDESURL . 'images/settings-20px.png', true, 5 );
        $this->register_admin_page( 'فرم پرداخت', array( $this, 'print_payform_page' ) , 'payform', INCLUDESURL . 'images/payform-20px.png', true, 2 );
        $this->register_admin_page( 'گزارش پرداخت ها', array( $this, 'print_paylogs_page' ) , 'paylogs', INCLUDESURL . 'images/paylogs-20px.png', true, 3 );
        if( data()->db->tableExists( "`pay-information`" ) !== false )
        {
            $this->old_user =  true;
            $this->register_admin_page( 'گزارش های قدیمی', array( $this, 'print_oldpaylogs_page' ) , 'oldpaylogs', INCLUDESURL . 'images/paylogs-20px.png', false, 4 );
        }
        $this->set_vars();
        $this->create_hooks();
        $this->load_main_content();
    }
    
    public function register_admin_page( $title, $callback, $hook_name, $icon = null, $autoload = false, $priority = 10 )
    {
        self::$registered_pages[ $hook_name ] = array(
                'title'    => $title,
                'link'     => ROOTURL . "index.php?admin&page={$hook_name}",
                'callback' => $callback,
                'hook'     => $hook_name,
                'icon'     => $icon,
                'autoload' => $autoload,
                'priority' => $priority
            );
    }
    
    private function create_hooks()
    {
        foreach ( self::$registered_pages as $page )
        {
            if( $page['autoload'] )
            {
                add_action( "admin_load_{$page['hook']}_page", $page['callback'] );
            }
        }
        
        if( $this->old_user )
        {
            add_action( 'admin_load_oldpaylogs_page', array( $this, 'print_oldpaylogs_page' ) );
        }
    }
    
    public function load_main_content()
    {
        include_once ADMINPATH . 'html/admin.php';
    }
    
    public function the_admin_sidebar()
    {
        $items = self::$registered_pages;
        Misc::sort_array( $items, 'priority' );
        include_once ADMINPATH . 'html/admin-sidebar.php';
    }
    
    public function the_admin_content()
    {
        $found = false;
        foreach ( self::$registered_pages as $page )
        {
            if( $page['hook'] == $this->page )
            {
                if( action_exists( "admin_load_{$page['hook']}_page" ) )
                {
                    do_action( "admin_load_{$page['hook']}_page" );
                    $found = true;
                    break;
                }
            }
        }
        
        if( ! $found )
        {
            include_once ADMINPATH . 'html/admin-content.php';
        }
    }
    
    public function print_dashboard_page()
    {
        include_once ADMINPATH . 'html/dashboard.php';
    }
    
    public function print_settings_page()
    {
        include_once ADMINPATH . 'includes/class-settings-page.php';
        $settings = new Settings_Page;
        include_once ADMINPATH . 'html/settings.php';
    }
    
    public function print_paylogs_page()
    {
        
        include_once ADMINPATH . 'includes/class-paylogs-page.php';
        $logs = new Paylogs_Page;
        include_once ADMINPATH . 'html/paylogs.php';
    }
    
    public function print_payform_page()
    {
        include_once ADMINPATH . 'includes/class-payform-page.php';
        $form = new Payform_Page;
        include_once ADMINPATH . 'html/payform.php';
    }
    
    public function print_oldpaylogs_page()
    {
        
        include_once ADMINPATH . 'includes/class-oldpaylogs-table.php';
        $logs = new Old_Paylogs_Table;
        include_once ADMINPATH . 'html/oldpaylogs.php';
    }
}

function the_admin_sidebar()
{
    if( ! is_null( data()->admin ) )
	    data()->admin->the_admin_sidebar(); 
}

function the_admin_page_content()
{
    if( ! is_null( data()->admin ) )
        data()->admin->the_admin_content();
}

function register_admin_page( $title, $callback, $hook_name, $icon, $autoload )
{
    if( ! is_null( data()->admin ) )
        data()->admin->register_admin_page( $title, $callback, $hook_name, $icon, $autoload );
    else
    {
    	die('خطا: بخش ادمین هنوز بارگزاری نشده است.');
    }
    
}

function the_admin_page_title()
{
    echo data()->admin->requested_page_title;
}
