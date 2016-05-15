<?php

class Payline {
    
    const version = '1.0';
    
    protected $posted_data = null;
    
    protected $session_key = null;
    
    public function __construct() 
    {
        if( isset( $_GET['after_payment'] ) && $_GET['after_payment'] == '1' )
        {
            if( ! isset( $_GET['pay_id'] ) || ! is_numeric( $_GET['pay_id'] ) )
                die( 'شناسه پرداخت نامعتبر است.' );
            $this->posted_data = $_POST;
            $this->do_on_return();       
        }
    }
    
    /**
     * Any thing that must be done befor sending to gateway
     */
    protected function before_send( $payment ) 
    {
        $gtw_data = data()->options->get_option( 'payline_settings' );
        if( isset( $gtw_data['payline_test_mode'] ) ) 
        {
            $url = 'http://payline.ir/payment-test/gateway-send';
            $api = 'adxcv-zzadq-polkjsad-opp13opoz-1sdf455aadzmck1244567';
        }
        else 
        {
            $url = 'http://payline.ir/payment/gateway-send';
            $api = ( isset( $gtw_data['payline_api'] ) ) ? $gtw_data['payline_api'] : '';
        }

        $amount = intval( $payment['amount'] ); // Required
        
        $redirect = $this->return_url( $payment['id'], true );

        $ch = curl_init();         
        curl_setopt( $ch, CURLOPT_URL, $url );          
        curl_setopt( $ch, CURLOPT_POSTFIELDS, "api={$api}&amount={$amount}&redirect={$redirect}" );
        curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, FALSE );
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
        $res = curl_exec( $ch );
        curl_close( $ch );

        switch( $res ) {
            case '-1' :   
                $this->add_message( 'کد api ارسالی با کد api ثبت شده در پی لاین سازگار نیست.', 'error' );
                data()->payment->complete_payment( $payment, 'failed' );
                header( 'Location: ' . $payment['pay_url'] );
                die();
            case '-2' :  
                $this->add_message( 'مبلغ یک مقدار عددی است و حداقل باید 1000 ریال باشد.', 'error' );
                data()->payment->complete_payment( $payment, 'failed' );
                header( 'Location: ' . $payment['pay_url'] );
                die();    
            case '-3' : 
                $this->add_message( 'آدرس بازگشت از درگاه یک مقدار نال می باشد.', 'error' );
                data()->payment->complete_payment( $payment, 'failed' );
                header( 'Location: ' . $payment['pay_url'] ); 
                die();    
            case '-4' :
                $this->add_message( 'چنین درگاهی وجود ندارد و یا هنوز در انتظار تایید می باشد!', 'error' );
                data()->payment->complete_payment( $payment, 'failed' );
                header( 'Location: ' . $payment['pay_url'] );
                die();    
            default :
                if( $res > 0 && is_numeric( $res ) )
                {
                    if( isset( $gtw_data['payline_test_mode'] ) ) 
                    {
                        $go = "http://payline.ir/payment-test/gateway-{$res}";
                    }
                    else 
                    {
                        $go = "http://payline.ir/payment/gateway-{$res}";
                    }
                    
                    header("Location: {$go}");
                    die();
                }
                else {
                    $this->add_message( 'خطا در ارسال به پی لاین', 'error' );
                    return;
                }
        }
    }
    
    /**
     * Any thing that must be done when user is returned from gateway
     */
    public function on_return( $payment, $post ) 
    {
        $gtw_data = data()->options->get_option( 'payline_settings' );
        if( isset( $gtw_data['payline_test_mode'] ) ) 
        {
            $api = 'adxcv-zzadq-polkjsad-opp13opoz-1sdf455aadzmck1244567';
            $url = 'http://payline.ir/payment-test/gateway-result-second';
        }
        else 
        {
            $api = ( isset( $gtw_data['payline_api'] ) ) ? $gtw_data['payline_api'] : '';
            $url = 'http://payline.ir/payment/gateway-result-second';
        }
        
        if( ! isset( $post['trans_id'] ) || ! isset( $post['id_get'] ) )
        {
            $this->add_message( 'به نظر می رسد درگاه پی لاین دچار مشکلی شده و یا قواعد اتصال به درگاه تغییر کرده لطفا مسئول سایت را از این موضوع مطلع نمایید.', 'error' );
            data()->payment->complete_payment( $payment, 'failed' );
            header("Location: {$payment['pay_url']}");
            die();
        }
        
        $trans_id = $_POST['trans_id']; 
        $id_get = $_POST['id_get']; 
    
        $ch = curl_init();     
        curl_setopt( $ch, CURLOPT_URL, $url );     
        curl_setopt( $ch, CURLOPT_POSTFIELDS, "api=$api&id_get=$id_get&trans_id=$trans_id" );
        curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, FALSE );
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
        $res = curl_exec( $ch );
        curl_close( $ch ); 
        
        switch( $res ) 
        {
            case '-1' :   
                $this->add_message( 'کد api ارسالی با کد api ثبت شده در پی لاین سازگار نیست.', 'error' );
                data()->payment->complete_payment( $payment, 'failed' );
                header( "Location: {$payment['pay_url']}" );
                die();
            case '-2' :  
                $this->add_message( 'شماره تراکنش ارسالی نامعتبر است.', 'error' );
                data()->payment->complete_payment( $payment, 'failed' );
                header( "Location: {$payment['pay_url']}" );
                die();    
            case '-3' : 
                $this->add_message( 'id_get ارسالی نامعتبر می باشد.', 'error' );
                data()->payment->complete_payment( $payment, 'failed' );
                header( "Location: {$payment['pay_url']}" );
                die();    
            case '-4' :
                $this->add_message( 'چنین تراکنشی وجود ندارد و یا قبلا با موفقیت به اتمام رسیده است. همچنین امکان دارد عملیات پرداخت توسط کاربر لغو شده باشد.', 'error' );
                data()->payment->complete_payment( $payment, 'failed' );
                header( "Location: {$payment['pay_url']}" );
                die();    
            case '1' :
                data()->payment->complete_payment( $payment, 'completed', $trans_id );
                $payment['txn_id'] = $trans_id;
                $pay_session = data()->sessions->get_session( 'payment_infos' );
                $pay_session[ $this->session_key ] = $payment;
                data()->sessions->set_session( 'payment_infos', $pay_session );
                $this->add_message( "پرداخت شما با موفقیت دریافت شد. شناسه تراکنش شما: {$trans_id}", 'updated' );
                header( "Location: {$payment['pay_url']}" );
                die(); 
            default:
                $this->add_message( 'نتیجه بررسی تراکنش شما نامعتبر است. ' . $res, 'error' );
                data()->payment->complete_payment( $payment, 'failed' );
                header( "Location: {$payment['pay_url']}" );
                die();
        }
    }
    
    public function send_to_gateway( $payment ) 
    {
        $pay_session = data()->sessions->get_session( 'payment_infos' );
        if( ! $pay_session )
            die( 'اطلاعات پرداخت موجود نیست' );
        $statuses = array( 'completed', 'canceled', 'failed' );
        foreach( $pay_session as $key => $pay ) 
        {
            if( ( time() - $pay['session_creation_time'] ) > 900 || in_array( $pay['details']['status']['name'], $statuses ) ) 
            {
                unset( $pay_session[ $key ] );
                continue;
            }

            if( $pay['id'] == $payment['id'] ) 
            {
                $this->session_key = $key;
                break;
            }
        }
        
        if( is_null( $this->session_key ) )
            die( 'این عملیات پرداخت منقضی شده است.' );
        
        $payment['pay_url'] = Misc::add_query_arg( array( 'session_key' => $this->session_key ), $payment['pay_url'] );
        $pay_session[ $this->session_key ] = $payment;
        data()->sessions->set_session( 'payment_infos', $pay_session );
            
        $this->before_send( $payment );
    }

    /**
     * Handling events after user returned from payment gateway
     */
    public function do_on_return() {
        
        $temp = data()->payment->get_payment_by_id( $_GET['pay_id'] );
        $temp = $temp[0];
        if( ! isset( $temp['id'] ) )
            die( 'هیچ تراکنشی با این شناسه ثبت نشده است.' );
        
        $payment = data()->sessions->get_session( 'payment_infos' );
        if( ! $payment )
            die( 'اطلاعات این تراکنش ثبت نشده است.' );
        
        $temp2 = array();
        foreach( $payment as $key => $pay ) 
        {
            if( $pay['id'] == $temp['id'] )
            {
                $temp2 = $pay;
                $this->session_key = $key;
                break;
            }
        }
            
        if( empty( $temp2 ) ) 
        {
            die( 'سشنی با این شناسه موجود نیست.' );
        }

        $this->on_return( $temp2, $this->posted_data );
    }
    
    /**
     * Url that we must redirect the user to, after returning from gateway
     * 
     * @param mixed $payment_id 
     * @return string
     */
    public function form_page_url() 
    {
        $url = ( isset( $_SERVER['HTTPS'] ) ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
        return $url;
    }
    
    /**
     * Url that users go into when gateway sends them to our website
     * 
     * @param int $id //payment id
     * @return mixed
     */
    public function return_url( $id, $encode = false ) 
    {
        $url = ROOTURL;
        $url = Misc::add_query_arg(  array( 'after_payment' => '1', 'pay_id' => $id ), $url );
        if( $encode )
            return urlencode( $url );
        else
            return $url;
    }
    
    public function add_message( $message, $type ) 
    {
        $session_key = $this->session_key;
        $pay_session = data()->sessions->get_session( 'payment_infos' ); 
        $pay_session[ $session_key ]['errors'][] = array( 'message' => $message, 'type' => $type );
        data()->sessions->set_session( 'payment_infos', $pay_session );
    }
}