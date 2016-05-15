<?php

class Misc
{
    public static $valid_image_types = array(
            'image/png' =>'',
            'image/jpg' =>'',
            'image/jpeg' =>'',
            'image/jpe' =>'',
            'image/gif' =>'',
            'image/tif' =>'',
            'image/tiff' =>'',
            'image/svg' =>'',
            'image/ico' =>'',
            'image/icon' =>'',
            'image/x-icon' => ''
        );
    
    public static function get_directories( $path, $full_path = true )
    {
        $results = scandir( $path );
        $files = array();
        foreach ( $results as $result ) 
        {
            if ( $result === '.' || $result === '..' ) 
                continue;

            if ( is_dir( $path . '/' . $result ) ) 
            {
                if( $full_path )
                {
                    $files[] = "{$path}/{$result}/";
                }
                else
                {
                	$files[] = $result;
                }
                
            }
        }
        
        return $files;
    }
    
    public static function get_files( $path )
    {
        $results = scandir( $path );
        $files = array();
        foreach ( $results as $result )
        {
            if ( $result === '.' || $result === '..' ) 
                continue;

            if ( is_file( $path . $result ) )
            {
                $files[] = $path . $result;
            }
        }
        
        return $files;
    }
    
    public static function sort_array( &$arr, $col, $dir = SORT_ASC ) 
    {
        $sort_col = array();
        foreach ( $arr as $key=> $row ) 
        {
            $sort_col[$key] = $row[$col];
        }

        array_multisort( $sort_col, $dir, $arr );
    }
    
    public static function is_substr_of( $str, $substr )
    {
        if( strpos( $str, $substr ) !== false )
        {
            return true;
        }
        
        return false;
    }
    
    public static function sanitize( $field )
    {
        return htmlspecialchars( trim( $field ) );
    }
    
    public static function get_valid_plugins_templates( $path )
    {
        $dirs = self::get_directories( $path, false );
        $directories = array();
        foreach ( $dirs as $dir )
        {
        	if( file_exists( $path . "{$dir}/about.xml" ) )
            {
                $directories[] = $dir;
            }
        }
        
        return $directories;
    }
    
    public static function maybe_add_slash( $link )
    {
        if( substr( $link, -1, 1 ) != '/' )
        {
            return $link . '/';
        }
        
        return $link;
    }
    
    public static function is_image( $url )
    {
        $url_headers = get_headers( $url, 1 );

        if( isset( $url_headers['Content-Type'] ) )
        {
            $type = strtolower( $url_headers['Content-Type'] );
            $valid_image_type = apply_filter( 'valid-image-types', self::$valid_image_type );
            
            if( isset( $valid_image_type[ $type ] ) )
            {
                return true;
            }
        }
        
        return false;
    }
    
    /**
     * Retrieve a modified URL query string.
     *
     * You can rebuild the URL and append a new query variable to the URL query by
     * using this function. You can also retrieve the full URL with query data.
     *
     * Adding a single key & value or an associative array. Setting a key value to
     * an empty string removes the key. Omitting oldquery_or_uri uses the $_SERVER
     * value. Additional values provided are expected to be encoded appropriately
     * with urlencode() or rawurlencode().
     *
     * @since 1.5.0
     *
     * @param string|array $param1 Either newkey or an associative_array.
     * @param string       $param2 Either newvalue or oldquery or URI.
     * @param string       $param3 Optional. Old query or URI.
     * @return string New URL query string.
     */
    public static function add_query_arg() 
    {
        $args = func_get_args();
        if ( is_array( $args[0] ) ) 
        {
            if ( count( $args ) < 2 || false === $args[1] )
                $uri = $_SERVER['REQUEST_URI'];
            else
                $uri = $args[1];
        }
        else 
        {
            if ( count( $args ) < 3 || false === $args[2] )
                $uri = $_SERVER['REQUEST_URI'];
            else
                $uri = $args[2];
        }

        if ( $frag = strstr( $uri, '#' ) )
            $uri = substr( $uri, 0, -strlen( $frag ) );
        else
            $frag = '';

        if ( 0 === stripos( $uri, 'http://' ) ) {
            $protocol = 'http://';
            $uri = substr( $uri, 7 );
        } 
        elseif ( 0 === stripos( $uri, 'https://' ) ) 
        {
            $protocol = 'https://';
            $uri = substr( $uri, 8 );
        } 
        else 
        {
            $protocol = '';
        }

        if ( strpos( $uri, '?' ) !== false ) 
        {
            list( $base, $query ) = explode( '?', $uri, 2 );
            $base .= '?';
        } 
        elseif ( $protocol || strpos( $uri, '=' ) === false ) 
        {
            $base = $uri . '?';
            $query = '';
        } 
        else 
        {
            $base = '';
            $query = $uri;
        }

        self::parse_str( $query, $qs );
        $qs = self::urlencode_deep( $qs ); // this re-URL-encodes things that were already in the query string
        if ( is_array( $args[0] ) ) {
            $kayvees = $args[0];
            $qs = array_merge( $qs, $kayvees );
        } else {
            $qs[ $args[0] ] = $args[1];
        }

        foreach ( $qs as $k => $v ) {
            if ( $v === false )
                unset( $qs[$k] );
        }

        $ret = self::build_query( $qs );
        $ret = trim( $ret, '?' );
        $ret = preg_replace( '#=(&|$)#', '$1', $ret );
        $ret = $protocol . $base . $ret . $frag;
        $ret = rtrim( $ret, '?' );
        return $ret;
    }
    
    /**
     * Removes an item or list from the query string.
     *
     * @since 1.5.0
     *
     * @param string|array $key   Query key or keys to remove.
     * @param bool         $query Optional. When false uses the $_SERVER value. Default false.
     * @return string New URL query string.
     */
    public static function remove_query_arg( $key, $query = false ) 
    {
        if ( is_array( $key ) ) 
        { // removing multiple keys
            foreach ( $key as $k )
                $query = self::add_query_arg( $k, false, $query );
            return $query;
        }
        
        return self::add_query_arg( $key, false, $query );
    }
    
    public static function parse_str( $string, &$array ) 
    {
        parse_str( $string, $array );
        if ( get_magic_quotes_gpc() )
            $array = self::stripslashes_deep( $array );
        /**
         * Filter the array of variables derived from a parsed string.
         *
         * @since 2.3.0
         *
         * @param array $array The array populated with variables.
         */
        $array = apply_filter( 'wp_parse_str', $array );
    }
    
    public static function parse_args( $args, $defaults = '' ) {
        if ( is_object( $args ) )
            $r = get_object_vars( $args );
        elseif ( is_array( $args ) )
            $r =& $args;
        else
            self::parse_str( $args, $r );

        if ( is_array( $defaults ) )
            return array_merge( $defaults, $r );
        return $r;
    }
    
    public static function stripslashes_deep( $value ) 
    {
        if ( is_array($value) ) 
        {
            $value = array_map('stripslashes_deep', $value);
        } 
        elseif ( is_object($value) ) 
        {
            $vars = get_object_vars( $value );
            foreach ($vars as $key=>$data) 
            {
                $value->{$key} = self::stripslashes_deep( $data );
            }
        } 
        elseif ( is_string( $value ) ) 
        {
            $value = stripslashes($value);
        }

        return $value;
    }
    
    public static function urlencode_deep( $value ) 
    {
        $value = is_array( $value ) ? array_map( array( __CLASS__, 'urlencode_deep' ), $value ) : urlencode( $value );
        return $value;
    }
    
    public static function build_query( $data ) 
    {
        return self::_http_build_query( $data, null, '&', '', false );
    }
    
    public static function _http_build_query( $data, $prefix = null, $sep = null, $key = '', $urlencode = true ) 
    {
        $ret = array();

        foreach ( (array) $data as $k => $v ) 
        {
            if ( $urlencode )
                $k = urlencode($k);
            if ( is_int($k) && $prefix != null )
                $k = $prefix.$k;
            if ( !empty($key) )
                $k = $key . '%5B' . $k . '%5D';
            if ( $v === null )
                continue;
            elseif ( $v === FALSE )
                $v = '0';

            if ( is_array($v) || is_object($v) )
                array_push($ret,self::_http_build_query($v, '', $sep, $k, $urlencode ) );
            elseif ( $urlencode )
                array_push( $ret, $k . '=' . urlencode( $v ) );
            else
                array_push( $ret, $k . '=' . $v );
        }

        if ( null === $sep )
            $sep = ini_get( 'arg_separator.output' );

        return implode( $sep, $ret );
    }
    
    /**
     * Returns a submit button, with provided text and appropriate class
     *
     * @since 3.1.0
     *
     * @param string $text The text of the button (defaults to 'Save Changes')
     * @param string $type The type of button. One of: primary, secondary, delete
     * @param string $name The HTML name of the submit button. Defaults to "submit". If no id attribute
     *               is given in $other_attributes below, $name will be used as the button's id.
     * @param bool $wrap True if the output button should be wrapped in a paragraph tag,
     * 			   false otherwise. Defaults to true
     * @param array|string $other_attributes Other attributes that should be output with the button,
     *                     mapping attributes to their values, such as array( 'tabindex' => '1' ).
     *                     These attributes will be output as attribute="value", such as tabindex="1".
     *                     Defaults to no other attributes. Other attributes can also be provided as a
     *                     string such as 'tabindex="1"', though the array format is typically cleaner.
     */
    public static function get_submit_button( $text = null, $type = 'primary large', $name = 'submit', $wrap = true, $other_attributes = null ) {
        if ( ! is_array( $type ) )
            $type = explode( ' ', $type );

        $button_shorthand = array( 'primary', 'small', 'large' );
        $classes = array( 'button' );
        foreach ( $type as $t ) {
            if ( 'secondary' === $t || 'button-secondary' === $t )
                continue;
            $classes[] = in_array( $t, $button_shorthand ) ? 'button-' . $t : $t;
        }
        $class = implode( ' ', array_unique( $classes ) );

        if ( 'delete' === $type )
            $class = 'button-secondary delete';

        $text = $text ? $text : __( 'Save Changes' );

        // Default the id attribute to $name unless an id was specifically provided in $other_attributes
        $id = $name;
        if ( is_array( $other_attributes ) && isset( $other_attributes['id'] ) ) {
            $id = $other_attributes['id'];
            unset( $other_attributes['id'] );
        }

        $attributes = '';
        if ( is_array( $other_attributes ) ) {
            foreach ( $other_attributes as $attribute => $value ) {
                $attributes .= $attribute . '="' . $value . '" '; // Trailing space is important
            }
        } else if ( !empty( $other_attributes ) ) { // Attributes provided as a string
            $attributes = $other_attributes;
        }

        $button = '<input type="submit" name="' . $name . '" id="' . $id . '" class="' . $class;
        $button	.= '" value="' . $text . '" ' . $attributes . ' />';

        if ( $wrap ) {
            $button = '<p class="submit">' . $button . '</p>';
        }

        return $button;
    }
}