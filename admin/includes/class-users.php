<?php

class Users {
    
    const version = '1.0';
    
    public function __construct() 
    {
        
    }
    
    public function set_user( $user_data )
    {
        data()->db->query( "INSERT INTO users (id,name,pass,mail,description,role) VALUES ('{$user_data['id']}','{$user_data['name']}','{$user_data['pass']}','{$user_data['mail']}','{$user_data['description']}','{$user_data['role']}') ON DUPLICATE KEY UPDATE id='{$user_data['id']}', name='{$user_data['name']}', pass='{$user_data['pass']}', mail='{$user_data['mail']}', description='{$user_data['description']}', role='{$user_data['role']}';" );
        return true;
    }
    
    /**
     * Returns false if user not exists or user data if user exists
     * 
     * @param string $key 
     * @return mixed
     */
    public function get_user( $key ) 
    {
        $username = Misc::sanitize( $key );
        if( ! empty( $username ) )
        {
            $result = data()->db->query( "SELECT * FROM users WHERE name='{$key}'" );
            if( ! empty( $result ) )
            {
                return $result[0];
            }
        }
        return false;
    }
    
    public function is_valid_password( $password )
    {
        if( preg_match( '/^\w{6,}$/i', $password ) )
        {
            return true;
        }
        
        return false;
    }
    
    public function crypt_the_password( $password )
    {
        return md5( $password );
    }
}