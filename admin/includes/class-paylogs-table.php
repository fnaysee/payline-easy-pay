<?php
require_once ADMINPATH . 'includes/class-list-table.php';
class Paylogs_table extends List_Table
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
                    'theid'      => 'ردیف',  //the name is other than id to prevent conflict between the table id column
                    'details' => 'جزئیات',
                    'status'  => 'وضعیت',
                    'txn_id'  => 'شناسه تراکنش',
                    'date'    => 'تاریخ',
                    'amount'  => 'مبلغ'
                ),
                'post_name'  => 'paylogs',
                'table_name' => 'paylogs_table',
                'actions' => array( 'delete' => 'حذف' )
            );
        $this->per_page = 15;
        $this->prepare_the_items();
    }
    
    protected function custome_styles( )
    {
?>
<style>
    #grid_table .details, #grid_table .amount, #grid_table .date, #grid_table .txn_id, #grid_table .status{
        width: 15%;
    }

    #grid_table .theid{
        text-align:center;
        width: 30px;
    }

    #grid_table .details{
        width: 14%;
    }

    #grid_table .date{
        width: 20%;
    }
</style>
<?php
    }
    
    public function process_bulk_action()
    {
        if( 'delete' === $this->current_action() ) 
        {
			if( isset( $_POST['paylogs'] ) && is_array( $_POST['paylogs'] ) )
            {

                data()->payment->delete_payments( $_POST['paylogs'] );

				$this->type = 'updated';
				$this->message = count( $_POST['paylogs'] ) . ' گزارش پرداخت با موفقیت حذف گردید.';
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
	protected function prepare_items( $search = NULL ) 
    {
        $this->process_bulk_action();
		$this->show_message();
        
        $this->total_items = data()->payment->get_payments_count(); // tanzime dastiye tedade record haye mojood.
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
            $result = data()->payment->get_limited_number_of_searched_payments( $search, $per_page, $offset );
        }
        else
        {
            $result = data()->payment->get_limited_payments( $per_page, $offset );
        }
        
		$found_data = $this->beautify_results( $result );
        $this->data = $found_data;
        Misc::sort_array( $this->data, $this->id_name, SORT_DESC );

        $fakeoffset = $this->total_items - $offset - $per_page;
        foreach( $this->data as $item => $val )
        {
            $this->data[ $item ]['theid'] = $fakeoffset + $item + 1;
        }
	}
    
    function beautify_results( $arr ) 
    {
        $temp = array();
        if( is_array( $arr ) ) 
        {
            foreach( $arr as $key ) 
            {
                $key['details'] = sprintf('<a href="?admin&page=%s&action=%s&item_id=%d">'. 'مشاهده جزئیات' . '</a>', $_REQUEST['page'], 'show_info', $key['id'] );
                $temp[] = $key;
            }
        }

        return $temp;
    }
	
	/**
     * Shows list of errors
     */
	function show_message(){
		if($this->message != null && $this->type != null )
			echo '<div id="table-error" class="' . $this->type . ' table-error"><p><strong>' . $this->message . '</strong></p></div>';
	}
}

