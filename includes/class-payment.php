<?php

class Payments
{

    const version = '1.0';
    
    private $trans_id = null;
    
    private static $txn_table = 'transactions';

    public function create_payment( $data ) 
    {
        if( ! is_array( $data ) )
            die('payment data must be array');
        if( empty( $data ) )
            die('could not create a payment from empty array');
        
        $data['details']['status'] = array( 'name' => 'pending', 'title' => 'وضعیت', 'value' => 'در انتظار تایید' );


        $amount = $data['amount'];

        $payment = array(
                'details'           => serialize( $data['details'] ),
                'status'            => 'در انتظار تایید',
                'txn_id'            => '',
                'amount'            => $amount,
                'date'              => $this->get_date()
            );

        $pay_id = $this->store_payment( $payment );

        $payment['id'] = $pay_id;
        $payment['pay_url'] = apply_filter( 'pay_url', ROOTURL );
        //$payment['pay_url'] = remove_query_arg( 'session_key', $payment['pay_url'] );
        $payment['details'] = unserialize( $payment['details'] );
        $payment['errors'] = array();
        $payment['session_creation_time'] = time();
        
        $temp = data()->sessions->get_session( 'payment_infos' );
        
        if( is_array( $temp ) ) 
        {
            $temp[] = $payment;
        }
        else {
            $temp = array( $payment );
        }

        data()->sessions->set_session( 'payment_infos', $temp );
        data()->payline = new Payline;
        data()->payline->send_to_gateway( $payment );
    }
    
    protected function store_payment( $payment )
    {
        $sql = "INSERT INTO " . self::$txn_table . "(details,date,txn_id,amount,status) VALUES ('{$payment['details']}','{$payment['date']}','{$payment['txn_id']}','{$payment['amount']}','{$payment['status']}')";
        $result = data()->db->query( $sql );
        if( $result )
        {
            return $this->get_payment_id( $payment );
        }
        return false;
    }
    
    public function complete_payment( $payment, $status, $trans_id = '' ) 
    {
        $status_arr = array();
        switch ( $status ) 
        {
            case 'completed':
                $status_arr['name'] = $status;
                $status_arr['value'] = 'تکمیل شده';
                break;
            case 'canceled':
                $status_arr['name'] = $status;
                $status_arr['value'] = 'لغو شده';
                break;
            default:
                $status_arr['name'] = $status;
                $status_arr['value'] = 'عملیات ناموفق';
                break;
        }
        $tmp = $payment;
        $tmp['details']['status']['name'] = $status_arr['name'];
        $tmp['details']['status']['value'] = $status_arr['value'];
        $temp = array(
                'details'           => serialize( $tmp['details'] ),
                'status'            => $status_arr['value'],
                'txn_id'            => $trans_id,
                'amount'            => $payment['amount'],
                'date'              => $this->get_date()
            );
        $this->update_payment_by_id( $payment['id'], $temp );
        
    }
    
    public function get_payment_id( $payment )
    {
        $result = data()->db->query( "SELECT id FROM " . self::$txn_table . " WHERE details='{$payment['details']}' AND date='{$payment['date']}' AND txn_id='{$payment['txn_id']}' AND amount='{$payment['amount']}' AND status='{$payment['status']}'" );
        if( ! empty( $result ) )
        {
            return $result[0]['id'];
        }
        
        return false;
    }
    
    protected function update_payment_by_id( $id, $args = array() ) 
    {
        $sql = "UPDATE " . self::$txn_table . " SET details='{$args['details']}',date='{$args['date']}',txn_id='{$args['txn_id']}',amount='{$args['amount']}',status='{$args['status']}' WHERE id='{$id}'";
        $result = data()->db->query( $sql );
        return $result;
    }
    
    /**
     * Returns limited number of available rows in payments table
     * 
     * @param int $limit 
     * @param int $offset 
     * @return array
     */
	public function get_limited_payments( $limit, $offset )
    {
		$sql = "SELECT * FROM " . self::$txn_table . " LIMIT {$offset},{$limit}";
        
		$result = data()->db->query( $sql );
        if( ! empty( $result ) )
            $result = $this->unserialize_arr( $result );
        return $result;
	}
    
    /**
     * Returns limited number of available rows in payments table (based on user search)
     * 
     * @param int $limit 
     * @param int $offset 
     * @return array
     */
	public function get_limited_number_of_searched_payments( $search, $limit, $offset )
    {
		$result = data()->db->query( "SELECT * FROM " . self::$txn_table . " WHERE details LIKE '%{$search}%' OR status LIKE '%{$search}%' OR txn_id LIKE '%{$search}%' OR amount LIKE '%{$search}%' LIMIT {$offset},{$limit}" );
        if( ! empty( $result ) )
            $result = $this->unserialize_arr( $result );
        
        return $result;
	}
    
    public function get_payments_by_id_arr( $id_arr ) 
    {
        $count = count( $id_arr );
        $sql = "SELECT * FROM " . self::$txn_table . "WHERE ";
        foreach( $id_arr as $key=>$val )
        {
            if( $count != $key + 1  )
                $sql .= "id = '$val' OR ";
            else
                $sql .= "id = '$val'";
        }
        
        $result = data()->db->query( $sql );
        if( !empty( $result ) )
            $result = $this->unserialize_arr( $result );
        return $result;
    }
    
    public function get_payment_by_id( $id ) 
    {
		$sql = "SELECT * FROM " . self::$txn_table . " WHERE id='{$id}'";
        
		$result = data()->db->query( $sql );
        if( ! empty( $result ) )
            $result = $this->unserialize_arr( $result );
        
        return $result;       
    }
    
    public function delete_payments( $arr ) 
    {
        $count = count( $arr );
        $sql = "DELETE FROM " . self::$txn_table . " WHERE id IN (";
        foreach( $arr as $key=>$val )
            if( $count != $key + 1 )
                $sql .= $val['id'] . ",";
            else
                $sql .= $val['id'] . ")";
        data()->db->query( $sql );
    }
    
    /**
     * To unserialize fields that was serialized
     * 
     * @param array $arr 
     * @return array
     */
	public function unserialize( $arr ) 
    {
        $temp = $arr;
		if( isset( $arr['details'] ) )
        {
			$temp['details'] = @unserialize( $arr['details'] );
        }
		return $temp;
	}
    
    /**
     * To unserialize array of fields that was serialized
     * 
     * @param array $arr 
     * @return array
     */
	public function unserialize_arr( $arr ) 
    {
		$data = array();
		foreach( $arr as $key )
        {
			if( isset( $key['details'] ) )
            {
				$key['details'] = @unserialize( $key['details'] );
            }
			$data[] = $key;
		}
		return $data;
	}
    
    /**
     * Gets the count of payments
     * 
     * @return int
     */
	public function get_payments_count( $is_search = false, $search = NULL ) 
    {
        if( $is_search )
            $sql = "SELECT count(*) FROM " . self::$txn_table . " WHERE details LIKE '%{$search}%' OR status LIKE '%{$search}%' OR txn_id LIKE '%{$search}%' OR amount LIKE '%{$search}%'";
        else
            $sql = "SELECT count(*) FROM " . self::$txn_table . ";";
		$result = data()->db->query( $sql );
        return $result[0]['count(*)'];
	}
    
    public function get_date() 
    {
        include_once INCLUDESPATH . "lib/class-jdatetime.php";
        if( class_exists( 'jDateTime' ) ) 
        {
            return jDateTime::date( 'Y-m-d H:i:s' );
        }
        else
            return date( 'Y-m-d H:i:s' );
    }
}