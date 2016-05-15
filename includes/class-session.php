<?php

class Session 
{
    const version = '1.0';

    public function __construct() 
    {
        $this->start_session();
    }
    
    public function start_session() 
    {
        if( session_id() == '' ) 
        {
            session_start();
        }
    }
    
    public function set_session( $name, $value ) 
    {
        $_SESSION[ $name ] = $value;
    }
    
    public function get_session( $name ) 
    {
        if( isset( $_SESSION[ $name ] ) ) 
        {
            return $_SESSION[ $name ];
        }
        
        return false;
    }
    
    public function remove_session( $name ) 
    {
        unset( $_SESSION[ $name ] );
    }
}