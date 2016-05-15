<?php

class Captcha
{
    protected $config = null;
    
	public function __construct( )
    {
        if( isset( $_GET['get_captcha'] ) )
        {
            add_action( 'vars_filled', array( $this, 'set_captcha' ) );
        }
    }
    
    public function set_captcha()
    {
        if( session_id() == '' )
            session_start();
        
        require_once INCLUDESPATH . "lib/simplecaptcha/class-simplecaptcha.php";
        $captcha = new SimpleCaptcha( $this->config );

        data()->sessions->set_session( 'captcha_code', $captcha->Code );
        die();
    }
    
    public function get_captcha_code()
    {
        if( $code = data()->sessions->get_session( 'captcha_code' ) )
        {
            return $code;
        }
        
        return false;
    }
}
