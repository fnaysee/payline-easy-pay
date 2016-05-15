<?php

class Options {
    
    const version = '1.0';
    
    public function __construct() 
    {
        
    }
    
    public function add_option( $key, $val, $group = 'general' ) 
    {
        if( is_array( $val ) || is_object( $val ) )
        {
            $strval = serialize( $val );
        }
        else
        {
        	$strval = $val;
        }
        
        $query = "INSERT INTO options (name,value,group_name) VALUES (':key',':strval',':group') ON DUPLICATE KEY UPDATE name=VALUES(name), value=VALUES(value), group_name=VALUES(group_name);";
        data()->db->query( $query, array(
                'name' => $key,
                'value' => $strval,
                'group' => $group
            )
        );
        return true;
    }
    
    public function add_options( $args )
    {
        $query = "INSERT INTO options (name,value) VALUES ";
        $end = end( $args );
        $option_groups = $this->option_groups();
        foreach ( $args as $option )
        {
            if( ! isset( $option['key'] ) || ! isset( $option['value'] ) )
            {
                continue;
            }
            
            if( ! isset( $option['group'] ) || ! in_array( $option['group'], $option_groups ) )
            {
                $option['group'] = 'general';
            }
            
            if( $option !== $end )
            {
        	    $query .= "('{$option['key']}','{$option['value']}','{$option['group']}'),";
            }
            else
            {
            	$query .= "('{$option['key']}','{$option['value']}','{$option['group']}') ";
            }
        }
        
        $query .= " ON DUPLICATE KEY UPDATE name=VALUES(name), value=VALUES(value), group_name=VALUES(group);";
        data()->db->query( $query );
    }
    
    /**
     * Returns false if option not exists or content of value column from database if option exists
     * 
     * @param string $key 
     * @return mixed
     */
    public function get_option( $key ) 
    {
        $result = data()->db->query( "SELECT value FROM options WHERE name='{$key}'" );
        if( ! empty( $result ) )
        {
            $data = @unserialize( $result[0]['value'] );
            if ( trim( $result[0]['value'] ) === 'b:0;' || $data === false ) 
            {
                return $result[0]['value'];
            } 
            else 
            {
                return $data;
            }
        }
        
        return false;

    }
    
    public function get_options( $keys ) 
    {
        $query = "SELECT name,value FROM options WHERE ";
        $end = end( $keys );
        
        foreach ( $keys as $key )
        {
            if( $key !== $end )
            {
        	    $query .= "name='{$key}' OR ";
            }
            else
            {
            	$query .= "name='{$key}';";
            }
        }
        
        $result = data()->db->query( $query );
        $output = array();
        foreach ( $result as $item )
        {
            $item['value'] = @serialize( $item['value'] );
        	$output[] = $item;
        }
        
        return $output;
    }
    
    private function option_groups()
    {
        $groups = array( 'admin', 'template', 'plugins', 'general' );
        return apply_filter( 'option_groups', $groups );
    }

}