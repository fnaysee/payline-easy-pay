<?php
final class Settings_Page
{
    private $fields = null;
    
    private $settings_name;
    
    private $messages;
    
	public function __construct( )
    {
        $this->settings_name = 'settings_page';
        if( isset( $_POST['settings_page_submit'] ) )
        {
            $this->verify();
        }
        else
        {
            $this->set_fields();
        }
    }
    
    public function print_the_page()
    {
        $this->print_page_styles();
        $this->messages();
?>
<form action="<?php echo ROOTURL; ?>index.php?admin&page=settings" method="post" >
    <input type="hidden" name="settings_page_submit" value="true" />
    <table id="data">
        <?php 
        foreach ( $this->fields as $item )
        {
            echo "<tr><th >{$item['title']}</th><td>";
            echo data()->settings_api->get_a_form_field( $item );
            echo "</td></tr>";
        }
        
        do_action( 'settings_page_table' ); 
        ?>
    </table>
    <input type="submit" class="submit_btn button" value="ذخیره" />
</form>
<?php
    }
    
    public function print_page_styles( )
    {
?>
<style>
    th{display:block;width:100%;}
    td{display:block;width:100%;}
</style>
<?php
    }
    
    private function verify()
    {
        $has_error = false;
        if( isset( $_POST['settings_page'] ) )
        {
            if( isset( $_POST['settings_page']['site_title'] ) )
            {
                data()->options->add_option( 'site_title', Misc::sanitize( $_POST['settings_page']['site_title'] ) );
            }
            
            if( isset( $_POST['settings_page']['site_email'] ) )
            {
                data()->options->add_option( 'site_email', Misc::sanitize( $_POST['settings_page']['site_email'] ) );
            }

            if( isset( $_POST['settings_page']['site_template'] ) )
            {
                data()->options->add_option( 'active_template', Misc::sanitize( $_POST['settings_page']['site_template'] ) );
            }
            
            if( isset( $_POST['settings_page']['payline_settings'] ) )
            {
                data()->options->add_option( 'payline_settings', $_POST['settings_page']['payline_settings'] );
            }
            
            if( ! isset( $_POST['settings_page']['user_name'] ) || ! isset( $_POST['settings_page']['user_pass'] ) || ! isset( $_POST['settings_page']['user_mail'] ) )
            {
                $has_error = true;
                $this->messages[] = array( 'message' => 'نام کاربری، کلمه عبور و ایمیل ادمین الزامیست', 'type' => 'error' );
            }
            
            if( ! $has_error )
            {
                $user_data = array(
                        'name'        => Misc::sanitize( $_POST['settings_page']['user_name'] ),
                        'pass'        => Misc::sanitize( $_POST['settings_page']['user_pass'] ),
                        'mail'        => Misc::sanitize( $_POST['settings_page']['user_mail'] ),
                        'description' => Misc::sanitize( ( isset( $_POST['settings_page']['user_description'] )? $_POST['settings_page']['user_description'] : '' ) ),
                    );
                
                if( ! empty( $user_data['name'] ) && ! empty( $user_data['pass'] ) && ! empty( $user_data['mail'] ) )
                {
                    if( data()->users->is_valid_password( $user_data['pass'] ) )
                    {
                        $user_data['pass'] = data()->users->crypt_the_password( $user_data['pass'] );
                        
                        $result = data()->db->query( "SELECT name FROM users WHERE name='{$user_data['name']}' AND id NOT IN ('" . data()->current_user->id . "')" );
                        if( ! empty( $result ) )
                        {
                            $has_error = true;
                            $this->messages[] = array( 'message' => 'این نام کاربری موجوداست', 'type' => 'error' );
                        }
                        
                        if( ! $has_error )
                        {
                            $user_data['id'] = data()->current_user->id;
                            $user_data['role'] = data()->current_user->role;
                            data()->users->set_user( $user_data );
                            data()->sessions->remove_session( 'user_session' );
                        }
                    }
                    else
                    {
                        $this->messages[] = array( 'message' => 'کلمه عبور باید دارای : حداقل 8 کاراکتر، یک کاراکتر لاتین بزرگ، یک کاراکتر لاتین کوچک و یک عدد باشد. همچنین می توان در آن از علائم نیز استفاده نمود.', 'type' => 'error' );
                        $has_error = true;
                    }
                }
                else
                {
                    $this->messages[] = array( 'message' => 'نام کاربری، کلمه عبور و ایمیل نمی تواند خالی باشد.', 'type' => 'error' );
                    $has_error = true;
                }
            }
            
            if( ! $has_error )
            {
                $this->messages[] = array( 'message' => 'اطلاعات با موفقیت ذخیره شد، به دلیل ویرایش اطلاعات کاربری هم اکنون از سیستم خارج شدید.', 'type' => 'updated' );
                $this->set_fields();
            }
            else
            {
            	$this->set_fields();
            }
            
        }
    }
    
    private function set_fields()
    {
        $this->settings_name = 'settings_page';
        $payline = data()->options->get_option( 'payline_settings' );
        $templates = data()->templates->get_templates_data();
        $templates_data = array();
        $active_template = data()->options->get_option( 'active_template' );

        foreach ( $templates as $template => $val )
        {
        	$templates_data[] = array(
                    'value' => $template,
                    'text' => $val[0]['TemplateName'][0],
                    'selected' => ( $template == $active_template ) ? true : false
                );
        }
        
        $this->fields = array(
                array(
                    'name' => $this->settings_name . '[site_title]',
                    'table' => true,
                    'title' => 'عنوان سایت',
                    'type' => 'text',
                    'value' => data()->site_title
                ),
                array(
                    'name' => $this->settings_name . '[site_email]',
                    'table' => true,
                    'title' => 'ایمیل سایت',
                    'type' => 'text',
                    'value' => ( data()->options->get_option( 'site_email') )? data()->options->get_option( 'site_email') : ''
                ),
                array(
                    'name' => $this->settings_name . '[site_template]',
                    'table' => true,
                    'title' => 'پوسته فعال',
                    'type' => 'select',
                    'options' => $templates_data
                ),
                array(
                    'name' => $this->settings_name . '[payline_settings][payline_api]',
                    'table' => true,
                    'title' => 'کلید اتصال به پی لاین',
                    'type' => 'text',
                    'value' => ( $payline && isset( $payline['payline_api'] ) ) ? $payline['payline_api'] : ''
                ),
                array(
                    'name' => $this->settings_name . '[payline_settings]',
                    'title' => 'حالت آزمایشی پی لاین',
                    'type' => 'checkbox',
                    'choices' => array(
                        array(
                            'name'  => "[payline_test_mode]",
                            'text'  => 'برای فعال سازی حالت آزمایشی پی لاین این گزینه را فعال نمایید.',
                            'value' => 'active',
                            'checked' => ( $payline && isset( $payline['payline_test_mode'] ) ) ? true : false
                        )
                    )
                ),
                array(
                    'name' => $this->settings_name . '[user_name]',
                    'table' => true,
                    'title' => 'نام کاربری',
                    'type' => 'text',
                    'classes' => 'dir-ltr',
                    'value' => data()->current_user->name
                ),
                array(
                    'name' => $this->settings_name . '[user_mail]',
                    'table' => true,
                    'title' => 'ایمیل مدیر',
                    'type' => 'text',
                    'classes' => 'dir-ltr',
                    'value' => data()->current_user->mail
                ),
                array(
                    'name' => $this->settings_name . '[user_pass]',
                    'table' => true,
                    'title' => 'کلمه عبور',
                    'type' => 'text',
                    'classes' => 'dir-ltr',
                    'value' => ( isset( $_POST['user_pass'] ) )? $_POST['user_pass'] : ''
                ),
                array(
                    'name' => $this->settings_name . '[user_description]',
                    'table' => true,
                    'title' => 'توضیحات',
                    'type' => 'textarea',
                    'content' => data()->current_user->description
                )
            );
    }
    
    public function messages() 
    {
        $msgs = $this->messages;
        $output = '<div class="messages" >';
        if( ! empty( $msgs ) ) 
        {
            foreach( $msgs as $msg )
            {
                $output .= '<div class="';
                if( $msg['type'] == 'error' )
                {
                    $output .= 'error"';
                }
                elseif( $msg['type'] == 'updated' )
                {
                    $output .= 'updated"';
                }
                
                $output .= ">{$msg['message']}</div>";
            }

        }
        
        echo $output . "</div>";
    }
}
