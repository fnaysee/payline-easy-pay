<?php

final class Data
{
    public $db = null;
    
    public $sessions = null;
    
    public $options = null;
    
    public $templates = null;
    
    public $plugins = null;
    
    public $login = null;
    
    public $current_user = null;
    
    public $users = null;
    
    public $payline = null;
    
    public $payment = null;
    
    public $settings_api = null;
    
    public $form = null;
    
    public $captcha = null;
    
    public $site_title = null;
    
    private static $_instance = null;
    
    public static function __instance()
    {
        if( is_null( self::$_instance ) )
            self::$_instance = new self;
        
        return self::$_instance;
    }
  
    public function __construct()
    {
       $this->fill_vars();
    }
    
    private function fill_vars() 
    {
        if( class_exists( 'DB' ) )
            $this->db = new DB;
        if( class_exists( 'Session' ) )
            $this->sessions = new Session;
    }
}

function data()
{
    return Data::__instance();
}

