<?php

class Form {

    const version = '1.1';
    
    const messages_session = 'form_messages';
    
    private static $form_fields = null;
    
    private static $active_fields = null;
    
    private static $required_fields = null;
    
    private static $fields_html = null;
    
    private $current_payment_data = null;
    
    protected $messages = null;
    
    public function __construct()
    {
        $this->set_form_fields();

        $this->flush_form_messages();
        if( isset( $_POST['payment_form'] ) && $_POST['payment_form'] == '1' ) 
        {
            $this->validate_form();
        }
    }
    
    private function extract_payment_session()
    {
        if( ! isset( $_GET['session_key'] ) ) 
        {
            return;
        }
        
        $payment = data()->sessions->get_session( 'payment_infos' );
        if( ! $payment )
        {
            return;
        }

        $temp = $_GET['session_key'];
        $session_key = null;

        foreach( $payment as $key => $pay ) 
        {
            if( $key == $temp ) 
            {
                $this->current_payment_data = $pay;
                $session_key = $key;
                break;
            }
        }

        $payment[ $session_key ]['errors'] = array();
        data()->sessions->set_session( 'payment_infos', $payment );
    }
    
    public function add_message( $message, $type, $from = null ) 
    {
        $messages = $this->get_messages();
        $messages[] = array(
                    'message'   => $message,
                    'type'      => $type,
                    'from'      => ( is_null( $from ) ) ? 'form' : $from
                );
        
        data()->sessions->set_session( self::messages_session, $messages );
    }
    
    private function get_messages() {
        $messages_session = data()->sessions->get_session( self::messages_session );
        return ( $messages_session ) ? $messages_session : array();
    }
    
    public function flush_messages( $from = null ) {
        if( ! is_null( $from ) ) 
        {
            $messages = $this->get_messages();
            foreach( $messages as $msg => $val ) 
            {
                if( $from == $val['from'] ) 
                {
                    unset( $messages[ $msg ] );
                }
            }
            data()->sessions->set_session( self::messages_session, $messages );
        }
        else 
        {
            data()->sessions->remove_session( self::messages_session );
        }
    }
    
    public function flush_form_messages() 
    {
        $this->flush_messages( 'form' );
    }
    
    public function set_form_fields() 
    {
        $form_fields = data()->options->get_option('payment_form_fields');
        $active_fields = data()->options->get_option('payment_form_active_fields');
        $required_fields = data()->options->get_option('payment_form_required_fields');
        self::$active_fields = ( is_array( $active_fields ) ) ? $active_fields : array();
        self::$required_fields = ( is_array( $required_fields ) ) ? $required_fields : array();
        self::$fields_html = array();

        $temp = array();
        foreach( $form_fields as $val ) 
        {
            if( array_key_exists( $val['name'], self::$active_fields ) ) 
            {
                $temp[] = array(
                    'title' => $val['title'],
                    'field' => array(
                        'id'        => $val['id'],
                        'name'      => "form-fields[{$val['name']}]",
                        'real_name' => $val['name'],
                        'type'      => ( isset( $val['type'] ) ) ? $val['type'] : '',
                        'value'     => ''
                    )
                );
            }
        }
        self::$form_fields = $temp;
        foreach( $temp as $item ) {
            self::$fields_html[] = "<tr><th>{$item['title']}</th><td>" . data()->settings_api->get_a_form_field( $item['field'] ) . "</td></tr>";
  //"<div><label class='title' for='{$item['field']['id']}'>{$item['title']}</label><span class='the-fields'>" . data()->settings_api->get_a_form_field( $item['field'] ) . "</span></div>";
        }
        
        return apply_filter( "payment_form_fields", self::$fields_html );
    }
    
    public function get_form_fields()
    {
        return implode( '', self::$fields_html );
    }
    
    public function get_amount_field() 
    {
        $byuser = false;
        if( $amount_field = data()->options->get_option( 'payment_form_amount_field' ) ) 
        {
            if( $amount_field['type'] == 'fixed' ) 
            {
                $amounts = "<tr><th>مبلغ</th><td>";
                    //"<div class='amount-field'><label class='title' for='payment-amount'>مبلغ&nbsp;&nbsp;</label><div class='the-fields' >";
                $items = array();
                foreach( $amount_field['fixed'] as $amount ) 
                {
                    $items[] = array(
                            'text' => "<label for='{$amount['id']}'>" . $amount['value'] . "<span class='currency'>ریال</span></label>",
                            'value' => $amount['value'],
                            'checked' => false
                        );
                }
                $field = array(
                        'name' => 'payment-amount',
                        'title' => 'مبلغ',
                        'type'  => 'radio',
                        'choices' => $items
                    );
                
                $amounts .= data()->settings_api->get_a_form_field( $field ) . "</td></tr>";
                    //"</div></div>";
                return $amounts;
            }
            else
            {
                $byuser = true;
            }
        }
        else 
        {
            $byuser = true;
        }
        
        if( $byuser ) 
        {
            return "<tr><th>مبلغ</th><td><input id='payment-amount' type='text' name='payment-amount' class='amount-text-field'  /> ریال </td></tr>";
                //"<div class='amount-field'><label class='title' for='payment-amount'>مبلغ : </label><span class='the-fields'><input id='payment-amount' type='text' name='payment-amount' class='amount-text-field'  /> ریال </span></div>";
        }
        
        return  '';
    }
    
    protected function submit_button() 
    {
        return apply_filter( "payment_form_submit", "<tr><th></th><td><input type='submit' name='submit-btn' class='form-submit button' value='پرداخت'/></td></tr>" );
        //"<div class='pay-btn'><input type='submit' name='submit-btn' class='form-submit' value='پرداخت'/></div>"
    }
    
    public function get_the_form()
    {
        $form = "<form class='payment-form' action='" . ROOTURL . "index.php?payment_req' method='post'>
                    <input type='hidden' name='payment_form' value='1' />" 
            . $this->get_messages_html()
            . "<table>"
            . $this->get_form_fields()
            . $this->get_amount_field()
            . $this->captcha()
            . $this->submit_button()
            . "</table></form>"
            . $this->form_scripts();
        
        return apply_filter( "payment_form", $form );
    }
    
    public function get_the_payment_form() 
    {
        $this->extract_payment_session();
        $this->form_styles();
        
        //if user is not set a message for success paymet then do nothing else if session is ok then print the user message instead of form
        $is_success_payment = false;
        $successfull_payment = data()->options->get_option('successfull_payment');
        if( isset( $_GET['session_key'] ) ) 
        {
            if( $successfull_payment && isset( $successfull_payment['show_form_after_successfull_payment'] ) && trim( $successfull_payment['successfull_payment_message'] ) != '' ) 
            {
                $temp2 = $this->current_payment_data;
            
                if( ! empty( $temp2 ) ) 
                {
                    foreach( $temp2['errors'] as $item ) 
                    {
                        if( $item['type'] == 'updated' ) 
                        {
                            $is_success_payment = true;
                            break;
                        }
                    }
                }
            }
        }
        
        if( $is_success_payment ) 
        {
            $data = str_replace( '[TRANS_ID]', $this->current_payment_data['txn_id'], $successfull_payment['successfull_payment_message'] );
            return "<div id='form-wrap'><div class='payment-form'>{$data}</div></div>";
        }
        else 
        {
            return '<div id="form-wrap">' . $this->get_the_form() . '</div>';
        }

        
    }

    public function the_payment_form() 
    {
        echo $this->get_the_payment_form();
    }
    
    
    public function validate_form() 
    {
        $data = array();
        $temp = '';
        $has_error = false;
        
        if( ! empty( self::$form_fields ) && ! isset( $_POST['form-fields'] ) ) 
        {
            $this->add_message( 'لطفا فیلدهای فرم را حذف نکنید.', 'error' );
            return;
        }
        
        foreach( self::$form_fields as $field ) 
        {
            if( isset( $_POST['form-fields'][ $field['field']['real_name'] ] ) ) 
            {
                if( ( ( $temp = Misc::sanitize( $_POST['form-fields'][ $field['field']['real_name'] ] ) ) == '' ) && array_key_exists( $field['field']['real_name'], self::$required_fields ) ) 
                {
                    $this->add_message( $field['title'] . " الزامی است" , 'error' );
                    $has_error = true;
                }
                else 
                {
                    $data['details'][ $field['field']['real_name'] ]['value'] = $temp;
                    $data['details'][ $field['field']['real_name'] ]['title'] = $field['title'];
                }
            }
        }

        if( ! isset( $_POST['payment-amount'] ) || ( $temp = Misc::sanitize( $_POST['payment-amount'] ) ) == '' ) 
        {
            $this->add_message( 'فیلد مبلغ الزامیست', 'error' );
            $has_error = true;
        }
        else 
        {
            if( is_numeric( $temp ) )
            {
                $data['amount'] = $temp;
            }
            else
            {
                $this->add_message( 'مبلغ مقداری عددی است.', 'error' );
                $has_error = true;
            }
        }
        
        $cryptinstall = "./includes/lib/crypt/cryptographp.fct.php";
        if( ! function_exists( 'dsp_crypt' ) )
            require_once INCLUDESPATH . "lib/crypt/cryptographp.fct.php";
        
        if( ! chk_crypt( $_POST['payment-captcha'] ) ) 
        {
            $this->add_message( 'کد امنیتی صحیح نیست.', 'error' );
            $has_error = true;
        }

        do_action( 'form_validation' );
        
        if( ! $has_error ) 
        {
            data()->payment->create_payment( $data );
        }
    }
    
    public function form_styles() 
    {
        $back_type = data()->options->get_option( 'form_background_type' );
        $back_url = data()->options->get_option( 'form-background-image' );
        $wrapper_styles = '#form-wrap { display: block;';
        if( $back_url != '0' )
        {
            $wrapper_styles .= "background-image: url({$back_url});";
            if( $back_type == '' || $back_type == 'parallax' )
                $wrapper_styles .= "background-attachment: fixed;  background-position: top center; background-size: cover; ";
            else
                $wrapper_styles .= " background-size: cover;";
        }
        $wrapper_styles .= '}';
        
        ?>
        <style type="text/css">
            .captcha-container table th, .captcha-container table td{
                width: auto;

            }
            <?php echo data()->options->get_option( 'form_custome_styles' ); ?>
        </style>
        <?php
    }
    
    public function get_messages_html() 
    {
        $msgs = $this->get_messages();
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
        
        $gtw_messages = $this->gateway_messages();
        if( ! is_null( $gtw_messages ) ) 
        {
            foreach( $gtw_messages as $msg )
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
        
        $output .= '</div>';
        
        apply_filter( 'form_messages', $output );
        
        return $output;
    }
    
    public function gateway_messages() 
    {
        if ( ! is_null( $this->current_payment_data ) )
        {
        	return $this->current_payment_data['errors'];
        }
        
        return null;
    }
    
    public function captcha()
    {
        $cryptinstall = "./includes/lib/crypt/cryptographp.fct.php";
        if( ! function_exists( 'dsp_crypt' ) )
            require_once INCLUDESPATH . "lib/crypt/cryptographp.fct.php";
        return "<tr class='captcha-container'><th></th><td>" . dsp_crypt() . "</td></tr><tr><th>تایید هویت: </th><td><input id='payment-captcha' type='text' name='payment-captcha' class=''  /></td></tr>";
            //"<div class='captcha-field'><label class='title' for='payment-captcha'>تاییدیه : </label><span class='the-fields'>" . dsp_crypt() . "<input id='payment-captcha' type='text' name='payment-captcha' class=''  /></span></div>";
    }
    
    public function form_scripts()
    {
        ?>
        <script type="text/javascript">

        </script>
<?php
    }
    
}