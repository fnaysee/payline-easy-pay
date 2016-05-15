<?php

class Support
{
    private $messages;
	public function __construct( )
    {
        if( isset( $_POST['support_form'] ) )
        {
            $this->verify();
        }
    }
    
    public function verify()
    {
        $data = array();
        $has_error = false;
        
        $fields = array(
                'name'          => 'نام',
                'email'         => 'ایمیل',
                'subject'       => 'موضوع',
                'description'   => 'توضیحات'
            );
        foreach ( $fields as $field => $val )
        {
            if( isset( $_POST[ $field ] ) )
            {
                $data[ $field ] = Misc::sanitize( $_POST[ $field ] );
                
                if( empty( $data[ $field ] ) )
                {
                    $this->messages[] = "فیلد {$val} نمی تواند خالی باشد";
                    $has_error = true;
                }
                
                if( $has_error == false && $field == 'email' && ! preg_match( '/^([\w\.\-_]+)?\w+@[\w-_]+(\.\w+){1,}$/', $data['email'] ) )
                {
                    $this->messages[] = "اطلاعات وارد شده در فیلد ایمیل نامعتبر است.";
                    $has_error = true;
                }
            }
            else
            {
                $this->messages[] = "فیلد {$val} الزامیست";
                $has_error = true;
            }
        }
        
        if( ! $has_error )
        {
        	if( $this->send_mail( $data ) )
            {
                $this->messages[] = "درخواست پشتیبانی شما با موفقیت ارسال گردید و به زودی با شما ارتباط برقرار خواهیم نمود.";
            }
            else
            {
                $this->messages[] = "ایمیل پشتیبانی ارسال نشد، لطفا بعدا مجددا تلاش نمایید.";
            	//$this->support_form();
            }
        }
    }
    
    public function support_form( )
    {
        $this->messages();
?>
<form method="post" action="<?php echo ROOTURL; ?>index.php?support" class="support_form">
    <input type="hidden" name="support_form" value="1" />
    <table >
        <tr>
            <th>
                نام
            </th>
            <td>
                <input type="text" name="name" placeholder="نام" />
            </td>
        </tr>
        <tr>
            <th>
                ایمیل
            </th>
            <td>
                <input type="text" name="email" placeholder="ایمیل" />
            </td>
        </tr>
        <tr>
            <th>
                موضوع
            </th>
            <td>
                <input type="text" name="subject" placeholder="موضوع" />
            </td>
        </tr>
        <tr>
            <th>
                توضیحات
            </th>
            <td>
                <textarea name="description"></textarea>
            </td>
        </tr>
        <tr>
            <th></th>
            <td>
                <input type="submit" name="submit_btn" class="submit_btn button" value="ارسال" />
            </td>
        </tr>
    </table>
</form>
<?php

    }
    
    public function messages()
    {
        if( empty( $this->messages ) )
            return;
        
        echo "<div class='messages'>";
        foreach ( $this->messages as $message )
        {
        	echo "<div class='error'>{$message}</div>";
        }
        echo "</div>";
    }
    
    public function send_mail( $data )
    {
        if( $email = data()->options->get_option( 'site_email' ) )
        {
            $body = "نام: {$data['name']}<br> ایمیل: {$data['email']}<br> پیام: {$data['description']}";
            $mail_server = data()->options->get_option( 'mail_server' );
            $host = 'localhost';
            if( $mail_server )
            { 
                if( isset( $mail_server['smtp_host'] ) && Misc::sanitize( $mail_server['smtp_host'] ) != '' )
                {
                    $host = $mail_server['smtp_host'];
                }
                
                if( isset( $mail_server['smtp_use_authontication'] ) )
                {
                    $authontication = true;
                    $mailuser = $mail_server['email_user'];
                    $mailpass = $mail_server['email_pass'];
                }
                else
                {
                    $authontication = false;
                    $mailuser = '';
                    $mailpass = '';
                }
                
            }
            else
            {
                $authontication = false;
                $mailuser = '';
                $mailpass = '';
            }
            
            include_once INCLUDESPATH . 'lib/phpmailer/class-phpmailer.php';
            $mail = new PHPMailer( );
            $mail->IsSMTP();
            try {	
                $mail->Host       = $host; //(do not touch these)
                $mail->SMTPAuth   = $authontication;  // using SMTP authentication ? (do not touch these)
                $mail->Username   = $mailuser;       //SMTP user (do not touch these)
                $mail->Password   = $mailpass;        //SMTP pass (do not touch these)
                $mail->AddReplyTo( $data['email'], 'آسان پرداخت پی لاین' ); // a button to send reply to sender
                $mail->AddAddress( $email, $data['name'] ); // receiver, we use site email for sending emails to (do not touch these)
                $mail->SetFrom( 'info@easypay.com', 'آسان پرداخت پی لاین' ); // sender email you can use your own address here
                $mail->Subject = $data['subject'];
                $mail->AltBody = 'Barnameye shoma az in email poshtibani nemikonad'; //if admin inbox not supports html
                $mail->CharSet = 'UTF-8'; // character set to show non english characters
                $mail->ContentType = 'text/html'; // content is in html 
                $mail->MsgHTML("<html dir='rtl'><head><meta charset='utf-8' /><style>body,div{direction:rtl;}</style></head><body><img src='" . INCLUDESURL . "images/payline.png'><br>{$body}<br><br><br><br>ارسال شده توسط آسان پرداخت پی لاین</body></html>");
                $mail->Send(); // send
                return true;
            } 
            catch (phpmailerException $e) {
                //echo $e->errorMessage(); 
                return false;
            } 
        }
        else
        {
        	return false;
        }
    }
}
