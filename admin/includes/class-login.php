<?php

class Login
{
    public $user = null;

    public $pass = null;

    private $messages = array();

    public function verify()
    {
        $this->user = htmlspecialchars( trim( $_POST['user_name'] ) );
        $this->pass = htmlspecialchars( trim( $_POST['user_pass'] ) );

        if( ! empty( $this->user ) && ! empty( $this->pass ) )
        {
            if( $this->is_valid_user( $this->user, md5( $this->pass ) ) )
            {
                if( session_id == '' )
                    session_start();
                data()->sessions->set_session( 'user_session', $this->user );
                data()->sessions->set_session( 'last_login', time() );
                header( "Location: " . ROOTURL . "?admin" );
            }
            else
            {
                $this->messages[] = 'اطلاعات کاربری نامعتبر است.';
            }
        }
        else{
            $this->messages[] = 'نام کاربری و کلمه عبور الزامی می باشند.';
        }
        
        $this->load_login_page();
    }

    public function is_valid_user( $username, $password )
    {
        $query = "SELECT name,pass FROM users WHERE name=:username and pass=:password";
        
        $user = data()->db->query( $query , array(
				'username' => $username,
				'password' => $password
			)
        );
        
        if( ! empty( $user ) )
        {
            return true;
        }
        
        return false;
    }

    public function is_user_logged_in()
    {
        //var_dump( $_SESSION );
        if( session_id() == '' )
            session_start();
        //Currently we assume there is only one admin for this script
        $admin_session = data()->sessions->get_session( 'user_session' );
        if( $admin_session )
            return true;
        
        return false;
    }

    public function load_login_page()
    {
        include_once INCLUDESPATH . 'lib/abstract/class-settings-api.php';
        $settings_api = new Settings_API;
        include_once INCLUDESPATH . 'html/login.php';
    }

    public function logout_the_user( )
    {
        data()->sessions->remove_session( 'user_session' );
        header( "Location: " . ROOTURL . 'index.php?login' );
    }
    
    public function login_is_old()
    {
        if( time() - data()->sessions->get_session('last_login') > 18600 )
        {
            return true;
        }
        
        return false;
    }
}