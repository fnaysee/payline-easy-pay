<?php

class Plugin_Loader
{
    const version = '1.0';
    
    private $is_first_run = true;
    
    private $plugins_loaded = false;
    
    public $plugins_data = null;
    
    //public function __construct()
    //{

    //}
    
    //private function init()
    //{
    //}
    
    public function load_active_plugins()
    {
        if( ! $this->plugins_loaded )
        {
            if( $plugins = data()->options->get_option( 'active_plugins' ) )
            {
                $this->include_plugins( $plugins );
            }
            $this->plugins_loaded = true;
        }
    }
    
    private function include_plugins( $plugins )
    {
        foreach ( $plugins as $plugin )
        {
            if( file_exists( PLUGINSPATH . "{$plugin}/functions.php" ) )
            {
        	    include_once PLUGINSPATH . "{$plugin}/functions.php";
            }
        }
    }
    
    public function get_available_plugins()
    {
        return Misc::get_valid_plugins_templates( PLUGINSPATH );
    }
    
    public function get_plugins_data()
    {
        $plugins = $this->get_available_plugins();
        $data = array();
        if( $this->is_first_run )
        {
            foreach ( $plugins as $temp )
            {
                if( $xml_data = simplexml_load_file( TEMPLATESPATH . "{$temp}/about.xml" ) )
                {
                    foreach ( $xml_data as $plugin_data )
                    {
                    	$data[ $temp ] = $plugin_data;
                    }
                }
            }
        }
        else
        {
        	$data = $this->plugins_data;
        }
        
        $this->is_first_check = false;
        $this->plugins_data = $data;
        return $data;
    }
}