<?php

class Hooks
{
    
    private static $action_hooks = array();
    
    private static $filter_hooks = array();
    
    private static $_instance = null;
    
    public static function __instance()
    {
        if( is_null( self::$_instance ) )
            self::$_instance = new self;
        
        return self::$_instance;
    }
    
    public static function do_action( $action_name )
    {
        if ( ! array_key_exists( $action_name, self::$action_hooks ) )
            return;
        $action_hooks = self::$action_hooks[ $action_name ];
        Misc::sort_array( $action_hooks, 'priority' );
        foreach ( $action_hooks as $item )
        {
            call_user_func( $item['callback'], $item['args'] );
        }
    }
    
    public static function add_action( $action_name, $callback, $args, $priority )
    {
        //if ( ! array_key_exists( $action_name, self::$action_hooks ) )
        //    return;

        self::$action_hooks[ $action_name ][] = array(
                'callback' => $callback,
                'args' => $args,
                'priority' => $priority
            );
    }
    
    public static function action_exists( $action_name )
    {
        if ( array_key_exists( $action_name, self::$action_hooks ) )
        {
            return true;
        }
        
        return false;
    }
    
    public static function apply_filters( $filter_name, $data )
    {
        if ( ! array_key_exists( $filter_name, self::$filter_hooks ) )
            return $data;
        
        $result = $data;
        
        //$filter_hooks = Misc::sort_array( self::$filter_hooks[ $filter_name ], 'priority' );
        $filter_hooks = self::$filter_hooks[ $filter_name ];
        Misc::sort_array( $filter_hooks, 'priority' );
        foreach ( $filter_hooks as $item )
        {
            $result = call_user_func( $item['callback'], $result );
        }
        
        return $result;
    }

    public static function add_filter( $filter_name, $callback, $priority )
    {
        //if ( ! array_key_exists( $filter_name, self::$filter_hooks ) )
        //    return;
        
        self::$filter_hooks[ $filter_name ][] = array(
                'callback' => $callback,
                'priority' => $priority
            );
    }
    
    public static function filter_exists( $filter_name )
    {
        if ( array_key_exists( $filter_name, self::$filter_hooks ) )
        {
            return true;
        }
        
        return false;
    }
}

function add_action( $action_name, $callback, $args = array(), $priority = 10 )
{
	Hooks::add_action( $action_name, $callback, $args, $priority );
}

function do_action( $action_name )
{
	Hooks::do_action( $action_name );
}

function add_filter( $filter_name, $callback, $priority = 10 )
{
	Hooks::add_filter( $filter_name, $callback, $priority );
}

function apply_filter( $filter_name, $data )
{
	return Hooks::apply_filters( $filter_name, $data );
}

function action_exists( $action_name )
{
	return Hooks::action_exists( $action_name );
}

function filter_exists( $filter_name )
{
	return Hooks::action_exists( $filter_name );
}
