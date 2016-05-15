<?php

class Default_Template
{
    public $path = null;
    
    private static $_instance = null;
    
    public static function __instance( )
    {
        if( is_null( self::$_instance ) )
            self::$_instance = new self;
        
        return self::$_instance;
    }
    
	public function __construct( )
    {
        $this->path = get_site_info( 'active_template_path' );
        
        //Call the template core in template_load action hook
        add_action( 'template_load', array( $this, 'load_template' ) );
        
        ##########################################################################
        # We use this hooks in all templates to let others, hook into our template
        ##########################################################################
        //Add the template head tag files in template_head action hook
        add_action( 'template_head', array( $this, 'template_head_contents' ) );
        
        if( ! isset( $_GET['support'] ) )
        {
            //Add the main template contents in template_body action hook
            add_action( 'template_body', array( $this, 'template_body_contents' ) );
        }
        else
        {
            //Add the main template contents in template_body action hook
            add_action( 'template_body', array( $this, 'support_page_body_contents' ) );
        }
        
        add_action( 'start_admin_load', array( $this, 'register_template_admin_page' ) );
        
    }
    
    public function load_template()
    {
        include_once $this->path . 'html/index.php';
    }
    
    public function template_body_contents()
    {
        include_once $this->path . 'html/body.php';
    }
    
    public function template_head_contents()
    {
        include_once $this->path . 'html/head.php';
    }
    
    public function support_page_body_contents( )
    {
        include_once $this->path . 'class-support.php';
        $support = new Support;
        include_once $this->path . 'html/support.php';
    }
    
    public function register_template_admin_page( )
    {
        if( function_exists( 'register_admin_page' ) )
        {
            register_admin_page(  'تنظیمات پوسته پیشفرض', array( $this, 'print_default_template_admin_page' ) , 'default', INCLUDESURL . 'images/payform-20px.png', true, 11 );
        }
    }
    
    public function print_default_template_admin_page( )
    {
        include_once $this->path . "class-default-page.php";
        $form = new Default_Template_Page;
        include_once $this->path . "html/admin-page.php";
    }  
}

Default_Template::__instance();
