<?php
final class Paylogs_Page
{
    private $fields = null;
    
    private $goto_info_page = false;
    
    public function __construct( )
    {
        if( isset( $_GET['item_id'] ) ) 
        {
            $payment = data()->payment->get_payment_by_id( $_GET['item_id'] );
            if( ! empty( $payment ) )
            {
                $this->fields = $payment[0];
                $this->goto_info_page = true;
            }
        }
    }
    
    public function choose_content()
    {
        if( ! $this->goto_info_page )
        {
            include_once ADMINPATH . 'includes/class-paylogs-table.php';
            $list = new Paylogs_table;
            $list->print_table();
        }
        else 
        {
            $this->print_the_page();
        }
    }
    
    public function print_the_page( )
    {
        $payment = $this->fields['details'];
        $this->print_styles();

?>
<table id="data">
    <?php if( is_array( $payment ) && ! empty( $payment ) ) {
              foreach( $payment as $field ) {
    ?>
    <tr>
        <th>
            <?php echo $field['title']; ?>
        </th>
        <td>
            <?php echo $field['value']; ?>
        </td>
    </tr>
    <?php  
              }
          }
          do_action( 'paylogs_page_table' ); ?>
</table>
<?php
    }
    
    public function print_styles( )
    {
        ?>
        <style>
            table,tbody,tr{
                width: 100%;
            }

            th,td{
                display: inline-block;
                padding: 5px;
            }

            th{
                width: 30%;
            }

            td{
                width: 60%;
            }
        </style>
        <?php
    }
    
}
