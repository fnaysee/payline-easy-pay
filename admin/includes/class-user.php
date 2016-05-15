<?php

class User {
    
    const version = '1.0';
    
    public $id = null;
    
    public $name = null;
    
    public $pass = null;
    
    public $description = null;
    
    public $mail = null;
    
    public $role = null;
    
    public function __construct( $user_name ) 
    {
        $this->fill_user_data( $user_name );
    }
    
    private function fill_user_data( $user_name )
    {
        $user = data()->users->get_user( $user_name );
        if( $user )
        {
            $this->id           = $user['id'];
            $this->name         = $user['name'];
            $this->description  = $user['description'];
            $this->mail         = $user['mail'];
            $this->role         = $user['role'];
        }
        
        return false; //user not exists
    }
    
    public function save_user()
    {
        $user = array(
                'name'          => $this->name,
                'pass'          => $this->pass,
                'description'   => $this->description,
                'mail'          => $this->mail,
                'role'          => $this->role,
            );
        data()->users->set_user( $user );
    }
}