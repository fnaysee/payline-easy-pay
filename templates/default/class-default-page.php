<?php
final class Default_Template_Page
{
    private $fields = null;
    
    private $settings_name;
    
    private $messages;
    
	public function __construct( )
    {
        $this->settings_name = 'default_page';
        if( isset( $_POST['default_page_submit'] ) )
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
<form action="<?php echo ROOTURL; ?>index.php?admin&page=default" method="post" >
    <input type="hidden" name="default_page_submit" value="true" />
    <table id="data">
        <?php 
        foreach ( $this->fields as $item )
        {
            echo "<tr><th scope='row'>{$item['title']}</th><td>";
            echo data()->settings_api->get_a_form_field( $item );
            echo "</td></tr>";
        }
        
        do_action( 'default_page_table' ); 
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
    .messages_area {
        width: 500px;
        height: 200px;
    }

    th {
        display: block;
        width: 100%;
        padding-top: 10px;
    }
</style>
<?php
    }
    
    private function verify()
    {
        $has_error = false;
        if( isset( $_POST[ $this->settings_name ] ) )
        {
            if( isset( $_POST[ $this->settings_name ]['main_page_text'] ) )
            {
                data()->options->add_option( 'main_page_text', Misc::sanitize( $_POST[ $this->settings_name ]['main_page_text'] ) );
            }
            
            if( isset( $_POST[ $this->settings_name ]['support_page_text'] ) )
            {
                data()->options->add_option( 'support_page_text', Misc::sanitize( $_POST[ $this->settings_name ]['support_page_text'] ) );
            }
            
            if( isset( $_POST[ $this->settings_name ]['mail_server'] ) )
            {
                data()->options->add_option( 'mail_server', $_POST[ $this->settings_name ]['mail_server'] );
            }
            
            if( ! $has_error )
            {
                $this->messages[] = array( 'message' => 'تغییرات با موفقیت ذخیره شد.', 'type' => 'updated' );
            }
            $this->set_fields();
        }
    }
    
    private function set_fields()
    {     
        $mail_data = data()->options->get_option( 'mail_server');
        $this->fields = array(
                array(
                    'name' => $this->settings_name . '[main_page_text]',
                    'table' => true,
                    'title' => 'متن کناری صفحه اصلی',
                    'type' => 'textarea',
                    'classes' => 'messages_area',
                    'content' => ( data()->options->get_option( 'main_page_text') )? data()->options->get_option( 'main_page_text') : ''
                ),
                array(
                    'name' => $this->settings_name . '[support_page_text]',
                    'table' => true,
                    'title' => 'متن کناری فرم پشتیبانی',
                    'type' => 'textarea',
                    'classes' => 'messages_area',
                    'content' => ( data()->options->get_option( 'support_page_text') )? data()->options->get_option( 'support_page_text') : ''
                ),
                array(
                    'name' => $this->settings_name . '[mail_server][smtp_host]',
                    'table' => true,
                    'title' => 'آدرس سرور smtp',
                    'text'  => 'اگر ایمیل های پشتیبانی ارسال نمی شوند می توانید اطلاعات زیر را تکمیل نمایید',
                    'type' => 'text',
                    'value' => ( isset( $mail_data['smtp_host'] ) )? $mail_data['smtp_host'] : 'localhost'
                ),
                array(
                    'name' => $this->settings_name . '[mail_server]',
                    'title' => 'استفاده از حساب کاربری',
                    'type' => 'checkbox',
                    'choices' => array(
                        array(
                            'name' => '[smtp_use_authontication]',
                            'value' => 'active',
                            'text' => 'اگر برای ارسال ایمیل از طریق smtp به حساب کاربری نیاز است این گزینه را فعال کرده و اطلاعات زیر را تکمیل نمایید.',
                            'checked' => ( isset( $mail_data['smtp_use_authontication'] ) )? true : false,
                        )
                    )
                ),
                array(
                    'name' => $this->settings_name . '[mail_server][email_user]',
                    'table' => true,
                    'title' => 'نام کاربری',
                    'type' => 'text',
                    'text' => 'مثال: user@domain.com',
                    'value' => ( isset( $mail_data['email_user'] ) )? $mail_data['email_user'] : ''
                ),
                array(
                    'name' => $this->settings_name . '[mail_server][email_pass]',
                    'table' => true,
                    'title' => 'کلمه عبور',
                    'type' => 'text',
                    'value' => ( isset( $mail_data['email_pass'] ) )? $mail_data['email_pass'] : ''
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
