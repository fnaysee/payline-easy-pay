<?php

class Install
{
    private $pdo = null;
    
    private $messages = array();
    
    public function print_install_page()
    {
        include_once INCLUDESPATH . 'lib/abstract/class-settings-api.php';
        $settings_api = new Settings_API;
        include_once INCLUDESPATH . 'html/install.php';
        die();
    }
    
    public function validate()
    {
        $has_error = false;
        if( ! isset( $_POST['installation'] ) )
        {
            $this->messages[] = 'فرم نصب به درستی تکمیل نشده است، اطلاعات را مجددا تکمیل نمایید.';
            $has_error = true;
        }
        
        if( $has_error == false && ( ! isset( $_POST['admin_user'] ) || ! isset( $_POST['admin_pass'] ) || ! isset( $_POST['admin_email'] ) 
            || ! isset( $_POST['admin_pass_confirm'] ) || ! isset( $_POST['db_host'] ) || ! isset( $_POST['db_name'] ) || ! isset( $_POST['db_user'] ) 
            || ! isset( $_POST['site_url'] ) || ! isset( $_POST['site_title'] ) || ! isset( $_POST['site_email'] ) ) )
        {
            $this->messages[] = 'همه فیلدها الزامی می باشند.';
            $has_error = true;
        }
        
        if( ! $has_error )
        {
            $info = array(
                    'admin_user'            => Misc::sanitize( $_POST['admin_user'] ),
                    'admin_pass'            => Misc::sanitize( $_POST['admin_pass'] ),
                    'admin_pass_confirm'    => Misc::sanitize( $_POST['admin_pass_confirm'] ),
                    'admin_email'           => Misc::sanitize( $_POST['admin_email'] ),
                    'db_host'               => Misc::sanitize( $_POST['db_host'] ),
                    'db_name'               => Misc::sanitize( $_POST['db_name'] ),
                    'db_user'               => Misc::sanitize( $_POST['db_user'] ),
                    'db_pass'               => ( isset( $_POST['db_pass'] ) )? $_POST['db_pass'] : '',
                    'site_url'              => Misc::maybe_add_slash( Misc::sanitize( $_POST['site_url'] ) ),
                    'site_title'            => Misc::sanitize( $_POST['site_title'] ),
                    'site_mail'             => Misc::sanitize( $_POST['site_email'] )
                );
            
            if( $has_error == false && ( empty( $info['admin_user'] ) || empty( $info['admin_pass'] ) || empty( $info['admin_email'] ) 
                || empty( $info['admin_pass_confirm'] ) || empty( $info['db_host'] ) || empty( $info['db_name'] )
                || empty( $info['db_user'] ) || empty( $info['site_url'] ) || empty( $info['site_mail'] ) ) )
            {
                $this->messages[] = 'تنها فیلد کلمه عبور دسترسی به دیتابیس می تواند خالی باشد.';
                $has_error = true;
            }
            
            if( ! $this->can_connect_to_db( $info['db_host'], $info['db_name'], $info['db_user'], $info['db_pass'] )  )
            {
                $this->messages[] = 'اطلاعات وارد شده جهت اتصال به دیتابیس صحیح نمی باشد.';
                $has_error = true;
            }
        }

        if( ! $has_error && ! $this->tables_are_ok( false, $info['db_host'], $info['db_name'], $info['db_user'], $info['db_pass'] ) )
        {
            $this->create_tables( $info['db_host'], $info['db_name'], $info['db_user'], $info['db_pass'] );
        }
        
        if( ! $has_error )
        {
            if( ! empty( $info['site_mail'] ) )
            {
                try
                {
                    $this->pdo->query( "INSERT INTO options (name,value) VALUES ('site_email','{$info['site_mail']}') ON DUPLICATE KEY UPDATE name=VALUES(name),value=VALUES(value)" );
                }
                catch (PDOException $ex)
                {
                    $this->messages[] = 'خطا در هنگام درج ایمیل سایت Error: ' . $ex->getMessage();
                    $has_error = true;
                }
            }
            else
            {
                $this->messages[] = 'ایمیل سایت نمی تواند خالی باشد.';
                $has_error = true;
            }
        }
        
        if( ! $has_error )
        {
                include_once ADMINPATH . 'includes/class-users.php';
                $users = new Users;
                if( $users->is_valid_password( $info['admin_pass'] ) )
                {
                    if( $info['admin_pass'] == $info['admin_pass_confirm']  )
                    {
                        $info['admin_pass'] = $users->crypt_the_password( $info['admin_pass'] );
                        
                        try
                        {
                            $this->pdo->query( "INSERT INTO users(name,pass,mail,role) VALUES ('{$info['admin_user']}','{$info['admin_pass']}','{$info['admin_email']}','admin') ON DUPLICATE KEY UPDATE name='{$info['admin_user']}',pass='{$info['admin_pass']}',mail='{$info['admin_email']}',role='admin'" );
                        }
                        catch (PDOException $ex)
                        {
                            $this->messages[] = 'Error: ' . $ex->getMessage();
                            $has_error = true;
                        }
                    }
                    else
                    {
                        $this->messages[] = 'کلمه عبور و تایید آن با یکدیگر مطابقت ندارند.';
                        $has_error = true;
                    }
                }
                else
                {
                    $this->messages[] = 'کلمه عبور باید دارای حداقل 6 کاراکتر باشد (به جز کاراکتر فضای خالی)';
                    $has_error = true;
                }
        }
        
        if( ! $has_error )
        {
            try
            {
                $this->edit_the_config( $info['site_url'], $info['db_host'], $info['db_name'], $info['db_user'], $info['db_pass'] );
            }
            catch ( Exception $ex )
            {
                $this->messages[] = 'اسکریپت قادر به ویرایش فایل کانفیگ خود نمی باشد. لطفا فایل کانفیگ موجود در روت اسکریپت را دستی ویرایش نمایید.';
                $has_error = true;
            }
            
            if( isset( $info['site_title'] ) &&  ! empty( $info['site_title'] ) )
            {
                $site_title = $info['site_title'];
            }
            else
            {
                $site_title = 'پرداخت سریع و ایمن توسط پی لاین';
            }
            $fields = $this->get_default_data();
            $query = "INSERT INTO options (name,value) VALUES ('site_title','{$site_title}'),('active_template','default'),('payment_form_fields','" . serialize( $fields['payment_form_fields'] ) . "'),('payment_form_active_fields','" . serialize( $fields['payment_form_active_fields'] ) . "'),('payment_form_required_fields','" . serialize( $fields['payment_form_required_fields'] ) . "'),('payment_form_amount_field','" . serialize( $fields['payment_form_amount_field'] ) . "') ON DUPLICATE KEY UPDATE name=VALUES(name),value=VALUES(value)";
            
            try
            {
                $result = $this->pdo->query( 'SELECT 1 FROM options LIMIT 1' );
                if( ! empty( $result ) )
                {
                    $this->pdo->query( $query );
                }
            }
            catch ( PDOException $ex )
            {
                $this->messages[] = 'خطا هنگام درج اطلاعات پیشفرض در دیتابیس.' . $ex->getMessage();
                $has_error = true;
            }
        }
        
        if( $has_error )
        {
            $this->print_install_page();
        }
        else
        {
        	header("Location: {$info['site_url']}?login" );
        }
        
    }
    
    public function edit_the_config( $site_url, $db_host, $db_name, $db_user, $db_pass )
    {
        $config_path = ROOTPATH . 'config.php';
        $config_file = file( $config_path );
        $new_config = $config_file;
        $matches = array( "'ROOTURL'", "'DBHOST'", "'DBNAME'", "'DBUSER'", "'DBUSERPASS'", "'ISFIRSTRUN'" );
        $values  = array( "'{$site_url}'", "'{$db_host}'", "'{$db_name}'", "'{$db_user}'", "'{$db_pass}'", "false" );
        
        foreach ( $config_file as $line_num => $line )
        {
            foreach ( $matches as $pos => $item )
            {
                if( strstr( $line, $item ) !== false )
                {
                    $new_config[ $line_num ] = "define( {$item}, {$values[ $pos ]} );\r\n";
                }
            }
        }
        
        $handle = fopen( $config_path, 'w' );
		foreach( $new_config as $line )
        {
			fwrite( $handle, $line );
		}
		fclose( $handle );
		chmod( $config_path, 0666 );
    }
    
    public function can_connect_to_db( $db_host, $db_name, $db_user, $db_pass )
    {
        try 
        {
            $this->connect_to_db( $db_host, $db_name, $db_user, $db_pass );
        }
        catch ( PDOException $ex )
        {
            if( DBNAME != '__database_name__' || DBUSER != '__database_user_name__' || DBUSERPASS != '__database_user_password__' )
                $this->messages[] = "اطلاعات اتصال به دیتابیس صحیح نمی باشد.";
            return false;
        }
        
        return true;
    }
    
    public function tables_are_ok( $add_error = true, $db_host = DBHOST, $db_name = DBNAME, $db_user = DBUSER, $db_pass = DBUSERPASS )
    {
        $tables = array( 'options', 'transactions', 'users' );
        
        $this->can_connect_to_db( $db_host, $db_name, $db_user, $db_pass );
        foreach ( $tables as $table )
        {
            try 
            {
                $result = $this->pdo->query("SELECT 1 FROM {$table} LIMIT 1");
            }
            catch (Exception $ex)
            {
                if( $add_error )
                {
                    $this->messages[] = "جدول {$table} در دیتابیس موجود نیست.";
                }
                return false;
            }
        }
        
        return $result !== false;
    }
    
    public function create_tables( )
    {
        //$db = new DB;
        
        $this->pdo->query("CREATE TABLE IF NOT EXISTS options (
					  id bigint(11) NOT NULL auto_increment,
                      name varchar(200) NOT NULL,
					  value TEXT NOT NULL,
                      group_name varchar(20) NOT NULL DEFAULT 'general',
					  PRIMARY KEY id (id),
                      UNIQUE name (name)
				) DEFAULT CHARACTER SET utf8");
        $this->pdo->query("CREATE TABLE IF NOT EXISTS transactions (
					  id bigint(11) NOT NULL auto_increment,
                      details TEXT NOT NULL,
					  date varchar(100) NOT NULL,
                      status varchar(50) NOT NULL,
                      txn_id varchar(150) NOT NULL,
                      amount varchar(50) NOT NULL,
					  UNIQUE KEY id (id)
				) DEFAULT CHARACTER SET utf8");
        $this->pdo->query("CREATE TABLE IF NOT EXISTS users (
					  id int NOT NULL auto_increment,
                      name varchar(30) NOT NULL,
					  pass text NOT NULL,
                      mail varchar(250) NOT NULL,
                      description TEXT DEFAULT '' NOT NULL,
                      role varchar(50) NOT NULL,
					  UNIQUE KEY id (id),
                      UNIQUE name (name)
				) DEFAULT CHARACTER SET utf8");
    }
    
    public function get_default_data()
    {
        $fields = array(
                    'payment_form_fields' => array(
                        array(
                            'id'        => 'default_field_name',
                            'name'      => 'payer-name',
                            'title'     => 'نام',

                        ),
                        array(
                            'id'        => 'default_field_family',
                            'name'      => 'payer-family',
                            'title'     => 'نام خانوادگی',

                        ),
                        array(
                            'id'        => 'default_field_web',
                            'name'      => 'payer-web',
                            'title'     => 'وب سایت',

                        ),                
                        array(
                            'id'        => 'default_field_email',
                            'name'      => 'payer-email',
                            'title'     => 'ایمیل',
                        ),                
                        array(
                            'id'        => 'default_field_phone',
                            'name'      => 'payer-phone',
                            'title'     =>  'تلفن تماس',
                        ),                
                        array(
                            'id'        => 'default_field_comment',
                            'name'      => 'payer-comment',
                            'title'     => 'توضیحات',
                            'type'      => 'textarea'
                        )
                    ),
                    'payment_form_active_fields' => array( 
                        'payer-name', 
                        'payer-family', 
                        'payer-web', 
                        'payer-email', 
                        'payer-phone', 
                        'payer-comment' 
                    ),
                    'payment_form_required_fields' => array( 
                        'payer-name', 
                        'payer-email'
                    ),
                    'payment_form_amount_field' => array(
                        'type' => 'user-input',
                        'fixed' => array(
                            'payment-amount-1' => array(
                                'id'        => 'payment-amount-1',
                                'name'      => 'payment-amount-1',
                                'title'     => '',
                                'type'      => 'radio',
                                'value'     => '2000'
                            )
                        )
                    )
            );
        return $fields;
    }
    
    public function connect_to_db( $db_host = DBHOST, $db_name = DBNAME, $db_user = DBUSER, $db_pass = DBUSERPASS )
    {
        $settings = array( 'dbtype' => DBTYPE, 'host' => $db_host, 'dbname' => $db_name, 'user' => $db_user, 'password' => $db_pass );
        $dsn = $settings["dbtype"] . ':dbname=' . $settings["dbname"] . ';host='.$settings["host"] . '';
        $this->pdo = new PDO( $dsn, $settings["user"], $settings["password"], array( PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8", PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION ) );
        //$result = $this->pdo->query("SELECT 1 FROM options LIMIT 1");
    }
}
