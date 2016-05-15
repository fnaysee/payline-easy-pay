<?php
require_once ADMINPATH . 'includes/class-list-table.php';
class Old_Paylogs_table extends List_Table
{
	protected $message = null;
	protected $type = null;

	/**
     * Class constructor
     */
	function __construct(){
        $this->settings = array(
                'show_search'       => true,
                'show_checkbox'     => true,
                'show_bulk_actions' => true,
                'columns'           => array(
                    'theid'         => 'ردیف',
                    'trans_id'      => 'شماره تراکنش',
                    'amount'        => 'مبلغ',
                    'name'          => 'نام',
                    'email'         => 'ایمیل',
                    'mobile'        => 'موبایل',
                    'description'   => 'توضیحات',
                    'stamp'         => 'تاریخ',
                    'status'        => 'وضعیت'
                ),
                'post_name'  => 'oldpaylogs',
                'table_name' => 'oldpaylogs_table',
                'actions' => array( 'delete' => 'حذف' )
            );
        $this->id_name = 'id_incremenet';
        $this->per_page = 15;
        $this->prepare_the_items();
    }
    
    protected function custome_styles()
    {
?>
<style>
    #grid_table .trans_id, #grid_table .amount, #grid_table .name, #grid_table .email, #grid_table .mobile, #grid_table .description, #grid_table .stamp, #grid_table .status{
        width: 10%;
    }

    #grid_table th, #grid_table td{
        padding:8px 1px;
    }

    #grid_table .cb input{
        margin:0;
    }

    #grid_table .theid{
        text-align:center;
        width: 28px;
    }

    #grid_table .trans_id{
        width: 14%;
    }
</style>
<?php
    }
    
    public function process_bulk_action( )
    {
        if( 'delete' === $this->current_action() ) 
        {
			if( isset( $_POST['oldpaylogs'] ) && is_array( $_POST['oldpaylogs'] ) )
            {

                $this->delete_payments( $_POST['oldpaylogs'] );

				$this->type = 'updated';
				$this->message = count( $_POST['oldpaylogs'] ) . ' گزارش پرداخت با موفقیت حذف گردید.';
			}
			else
            {
				$this->type = 'error';
				$this->message = '  هیچ لیست پرداختی موجود نیست. ';
			}
		}
    }
    
    protected function get_the_current_page()
    {
        if( ! isset( $_POST['current_page'] ) )
        {
            $this->current_page = 1;
        }
        else
        {
            $this->current_page = $_POST['current_page'];
        }
        
        return $this->current_page;
    }
    
	/**
     * Prepares items
     */
	function prepare_items( $search = NULL ) 
    {
        $this->process_bulk_action();
		$this->show_message();
        
        $this->total_items = $this->get_payments_count(); // tanzime dastiye tedade record haye mojood.
        $current_page = $this->get_the_current_page(); // be total_items niaz darad
        $per_page = $this->per_page;
        
        $pages = $this->total_pages();

        $requested_page = 1;
        if( isset( $_POST['next_page'] ) )
        {
            $requested_page = $current_page + 1;
            if( $requested_page > $pages )
            {
                $requested_page = $pages;
            }
        }
        elseif( isset( $_POST['previous_page'] ) )
        {
            $requested_page = $current_page - 1;
            if( $requested_page < 1 )
            {
                $requested_page = 1;
            }
        }
        elseif( isset( $_POST['requested_page'] ) )
        {
            $requested_page = intval( $_POST['requested_page'] );
            
            if( $requested_page < 1 )
            {
                $requested_page = 1;
            }
            elseif( $requested_page > $pages )
            {
                $requested_page = $pages;
            }
        }
        
        $offset = $this->total_items - ( $this->per_page * $requested_page );
        
        //tanzime offset va per_page
        if( ( $this->total_items / $this->per_page ) > 0 && $requested_page == $pages )
        {
            $per_page = $per_page + $offset;
            $offset = 0;
        }
        
        $this->current_page = $requested_page;
        
        if( $search != NULL && trim( $search ) != '' )
        {
            $result = $this->get_limited_number_of_searched_payments( $search, $per_page, $offset );
        }
        else
        {
            $result = $this->get_limited_payments( $per_page, $offset );
        }
        
        $this->data = $result;
        Misc::sort_array( $this->data, $this->id_name, SORT_DESC );
        
        $fakeoffset = $this->total_items - $offset - $per_page;
        foreach( $this->data as $item => $val )
        {
            $this->data[ $item ]['theid'] = $fakeoffset + $item + 1;
        }
	}
	
	/**
     * Shows list of errors
     */
	function show_message()
    {
		if($this->message != null && $this->type != null )
			echo '<div id="table-error" class="' . $this->type . ' table-error"><p><strong>' . $this->message . '</strong></p></div>';
	}
    
    public function delete_payments( $arr ) 
    {
        $count = count( $arr );
        $sql = "DELETE FROM `pay-information` WHERE id_incremenet IN (";
        foreach( $arr as $key=>$val )
            if( $count != $key + 1 )
                $sql .= $val['id_incremenet'] . ",";
            else
                $sql .= $val['id_incremenet'] . ")";
        data()->db->query( $sql );
    }
    
    /**
     * Returns limited number of available rows in payments table (based on user search)
     * 
     * @param mixed $search 
     * @param int $limit 
     * @param int $offset 
     * @return array
     */
	public function get_limited_number_of_searched_payments( $search, $limit, $offset )
    {
		$result = data()->db->query( "SELECT * FROM `pay-information` WHERE trans_id LIKE '%{$search}%' OR amount LIKE '%{$search}%' OR name LIKE '%{$search}%' OR email LIKE '%{$search}%' OR mobile LIKE '%{$search}%' OR description LIKE '%{$search}%' OR stamp LIKE '%{$search}%' OR status LIKE '%{$search}%' LIMIT {$limit} OFFSET {$offset}" );
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
		$sql = "SELECT * FROM `pay-information` LIMIT {$limit} OFFSET {$offset}";
		$result = data()->db->query( $sql );
        return $result;
	}
    
    /**
     * Gets the count of payments
     * 
     * @return int
     */
	public function get_payments_count( $is_search = false, $search = NULL )
    {
        if( $is_search )
            $sql = "SELECT count(*) FROM `pay-information` WHERE trans_id LIKE '%{$search}%' OR amount LIKE '%{$search}%' OR name LIKE '%{$search}%' OR email LIKE '%{$search}%' OR mobile LIKE '%{$search}%' OR description LIKE '%{$search}%' OR stamp LIKE '%{$search}%' OR status LIKE '%{$search}%'";
        else
            $sql = "SELECT count(*) FROM `pay-information`;";
		$result = data()->db->query( $sql );
        return $result[0]['count(*)'];
	}
}