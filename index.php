<?php
/*****************************************************************************************************
 * Project Name : Payline Easy Pay
 * Description : Script to run a simple and easy to use but powerfull payment system for payline users
 * 
 * Project Version : 1.1
 * 
 *****************************************************************************************************/
require_once 'config.php';
final class Easy_Pay
{
    const version = '1.1';
    
    public $install;
    
    private static $_instance = null;
    
    public static function __instance() 
    {
        if( is_null( self::$_instance ) )
            self::$_instance = new self();
        
        return self::$_instance;
    }
    
	public function __construct()
    {
        if (DEBUG)
        {
            ini_set('display_errors', 'On');
            error_reporting(E_ALL);
        }
        else
        {
            ini_set('display_errors', 'Off');
            error_reporting(0);
        }
        
        $this->init();
    }
    
    private function init()
    {
        $this->include_general_files();
        
        if( ISFIRSTRUN )
        {
            if( class_exists( 'Install' ) )
            {
                $this->install = new Install;
            }

            if( isset( $_GET['install'] ) )
            {
                $this->install->validate();
                die();
            }

            if( ! $this->install->can_connect_to_db( DBHOST, DBNAME, DBUSER, DBUSERPASS ) )
            {
                $this->install->print_install_page();
                exit;
            }
            
            if( ! $this->install->tables_are_ok() )
            {
                $this->install->print_install_page();
                die();
            }
        }
        data();
        epy();
    }
    
    private function include_general_files()
    {
        $files = array(
                'lib/abstract/class-settings-api.php',
                'lib/indieteq/class-db-helper.php',
                'class-options.php',
                'class-session.php',
                'lib/class-misc.php',
                'lib/class-hooks.php',
                'class-install.php',
                //'class-plugin-loader.php',    #on next versions
                'class-template-loader.php',
                'class-form.php',
                'class-payment.php',
                'class-payline.php',
                'class-data.php',
                'class-loader.php'
            );

        foreach( $files as $file )
        {
            require_once INCLUDESPATH . $file;
        }
    }
}

function easy_pay()
{
    return Easy_Pay::__instance();
}

easy_pay();