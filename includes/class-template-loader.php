<?php

class Template_Loader
{
    const version = '1.0';
    
    private $is_first_check = true;
    
    private $templates_data = null;
    
    public $active_template = null;
    
    public $active_template_info = null;
    
    private $functions_loaded = false;
    
    public function __construct()
    {
        $this->active_template = 'default/';
        $this->init();
    }
    
    private function init()
    {
        $this->set_active_template();
    }
    
    public function set_active_template()
    {
        $at = data()->options->get_option( 'active_template' );
        if ( $at )
        {
            $this->active_template = $at . '/';
        }
    }
    
    public function load_template_functions()
    {
        if( ! $this->functions_loaded )
        {
            do_action( 'template_redirect' );
            include_once $this->template_directory() . "functions.php";
        }
    }
    
    public function get_available_templates()
    {
        return Misc::get_valid_plugins_templates( TEMPLATESPATH );
    }
    
    public function set_templates_data()
    {
        $templates = $this->get_available_templates();
        $data = array();
        if( $this->is_first_check )
        {
            include_once INCLUDESPATH . "lib/class-xml-parser.php";
            $parser = new SimpleLargeXMLParser();
            foreach ( $templates as $temp )
            {
                if( file_exists( TEMPLATESPATH . "{$temp}/about.xml" ) )
                {
                    $parser->loadXML( TEMPLATESPATH . "{$temp}/about.xml" );
                    $xml_data = $parser->parseXML("//EasyPayTemplate");
                    $data[ $temp ] = $xml_data;
                    if( $temp == $this->active_template )
                    {
                        $this->active_template_info = $xml_data;
                    }
                }
            }
        }
        else
        {
        	$data = $this->templates_data;
        }
        
        $this->is_first_check = false;
        $this->templates_data = $data;
    }
    
    public function get_templates_data()
    {
        if( ! is_null( $this-> templates_data ) )
            return $this->templates_data;
        
        $this->set_templates_data();
        return $this->templates_data;
    }
    
    
    public function template_directory()
    {
        return TEMPLATESPATH . $this->active_template;
    }
    
    public function template_url()
    {
        return TEMPLATESURL . $this->active_template;
    }
}