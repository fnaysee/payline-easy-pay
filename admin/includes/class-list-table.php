<?php

class List_Table
{
    protected $settings = null;
    
    protected $offset = null;
    
    protected $data = null;
    
    protected $current_offset = null;
    
    protected $id_name = 'id';
    
    protected $current_page = null;
    
    protected $per_page;
    
    //public function __construct()
    //{
    //    $this->settings = array(
    //            'show_search' => true,
    //            'show_checkbox' => true,
    //            'show_bulk_actions' => true,
    //            'columns' => array(                
    //                'col1' => 'ستون 1'
    //            ),
    //            'post_name' => 'example_arr',
    //            'table_name' => 'mytable',
    //            'actions' => array( 'delete' => 'حذف', 'edit' => 'ویرایش' )
    //        );
    //    $this->per_page = 15;
    //}
    
    protected function current_action()
    {
        if( isset( $_POST['bulk_actions_btn'] ) && isset( $_POST['current_action'] ) )
        {
            return $_POST['current_action'];
        }
        
        return '';
    }

    protected function get_the_current_page()
    {
        //Override in extended class
    }
    
    public function search()
    {
        if( $this->settings['show_search'] && isset( $_POST['table_search'] ) )
        {
            return $_POST['table_search'];
        }
        
        return null;
    }
    
    public function prepare_the_items()
    {
        $this->prepare_items( $this->search() );  //This is only in the extended class
    }
    
    protected function total_pages()
    {
        $count = $this->total_items;
        if( $count > $this->per_page )
        {
            $max_page_number = intval( ceil( $count / $this->per_page ) );
        }
        else
        {
            $max_page_number = 1;
        }
        
        return $max_page_number;
    }
    
    
    
    public function header_html()
    {
        echo "<thead><tr>";

        foreach ( $this->settings['columns'] as $col => $val )
        {
            if( $col == $this->id_name && $this->settings['show_checkbox'] )
            {
                echo "<th scope='row' class='cb'><input type='checkbox' class='check_all' name='' value='' ></th>";
            }
            else
            {
                echo "<th class='$col'>{$val}</th>";
            }
        }
        
        echo '</tr></thead>';
    }
    
    public function maybe_add_checkbox()
    {
        if( $this->settings['show_checkbox'] && ! isset( $this->settings['columns'][ $this->id_name ] ) )
        {
            $this->settings['columns'] = array( $this->id_name => '' ) + $this->settings['columns'];
        }
    }
    
    public function footer_html()
    {
        echo "<tfoot><tr>";
        foreach ( $this->settings['columns'] as $col => $val )
        {
            if( $col == $this->id_name && $this->settings['show_checkbox'] )
            {
                echo "<th scope='row' class='cb'><input type='checkbox' class='check_all' name='' value='1' ></th>";
            }
            else
            {
                echo "<th class='$col'>{$val}</th>";
            }
        }
        
        echo'</tr></tfoot>';
    }
    
    public function content_html()
    {
        echo '<tbody>';
        $data = $this->data;
        $columns = array( $this->id_name => '' ) + $this->settings['columns'];

        if( ! empty( $data ) )
        {
            foreach ( $data as $row => $val )
            {
                echo '<tr>';
                foreach ( $columns as $col_name => $col_title )
                {
                    foreach ( $val as $col => $value )
                    {
                        if( $col_name == $col )
                        {
                            if( $col == $this->id_name && $this->settings['show_checkbox'] )
                            {
                                echo "<th scope='row' class='cb'><input class='column_checkbox' type='checkbox' name='{$this->settings['post_name']}[][{$this->id_name}]' value='{$value}' ></th>";
                            }
                            else
                            {
                                echo "<td class='{$col}'>{$value}</td>";
                            }
                        }
                    }
                }
                echo '</tr>';
            }
        }
        else
        {
        	echo "<tr><td colspan='" . count( $this->settings['columns'] ) . "'>هیچ موردی یافت نشد.</td></tr>";
        }

        echo '</tbody>';
    }
    
    public function maybe_search_html()
    {
        if( $this->settings['show_search'] )
        {
            echo "<div class='table_search'>";
            echo "<input type='text' name='table_search' value='" . ( isset( $_POST['table_search'] )? $_POST['table_search'] : '' ) . "' />";
            echo "<input type='submit' class='grid_btn button' name='table_submit' value='جستجو' />";
            echo "</div>";
        }
    }
    
    public function pagination_html()
    {
        $pages = $this->total_pages();
        echo "<div class='table_pagination'><input type='submit' name='previous_page' class='grid_btn button' value='قبلی'><span class='page_numbers'>";
        
        for( $i = 1; $i <= $pages; $i++ )
        {
            echo "<input type='submit' class='grid_btn button' name='requested_page' value='{$i}'>";
        }
        
        echo "</span><input type='submit' class='grid_btn button' name='next_page' value='بعدی'></div>";
    }
    
    public function maybe_bulk_actions_html( )
    {
        if( $this->settings['show_bulk_actions'] )
        {
            echo "<div class='actions'><select name='current_action'>";
            foreach ( $this->settings['actions'] as $action_name => $action_title )
            {
                echo "<option value='{$action_name}'>{$action_title}</option>";
            }
            
            echo "</select><input type='submit' class='grid_btn button' name='bulk_actions_btn' value='اجرا'></div>";
        }
    }
    
    
    protected function prepare_items( )
    {
        //grab data from your database table and fill the $this->data 
    }

    public function print_table()
    {
        $this->table_styles();
        echo "<form id='{$this->settings['table_name']}' action='' method='post'>
        <input type='hidden' name='table_name' value='{$this->settings['table_name']}' />
        <input type='hidden' name='total_items' value='{$this->total_items}' />
        <input type='hidden' name='current_page' value='{$this->current_page}' />";
        $this->maybe_add_checkbox();
        $this->maybe_search_html();
        $this->maybe_bulk_actions_html();
        echo "<table id='grid_table'>";
        $this->header_html();
        $this->content_html();
        $this->footer_html();
        echo "</table>";
        $this->pagination_html();
        echo "</form>";
        $this->table_js();
    }
    
    public function table_styles()
    {
?>
<style>
    #grid_table, #grid_table tr, #grid_table thead, #grid_table tfoot {
        width: 100%;
    }

    #grid_table {
        background-color: #fff;
        border: 1px solid #ccc;
        box-shadow: 0 0 5px #e1e1e1;
        color: #121212;
        table-layout: fixed;
        border-spacing: 0px;
        width: 100%;
        clear: both;
        margin: 0px;
    }

    #grid_table {
        word-wrap: break-word;
    }

    #grid_table thead {
        border-bottom: 1px solid #ccc;
    }

    #grid_table tfoot {
        border-top: 1px solid #ccc;
    }

    #grid_table tr {
        padding: 5px 8px;
    }

    #grid_table tbody tr:nth-child(2n-1) {
        background-color: #e5e5e5;
    }

    #grid_table tbody tr:hover {
        background-color: #97f0e0;
    }

    #grid_table th, #grid_table td {
        padding: 8px 3px;
        display: inline-block;
    }

    #grid_table a {
        text-decoration: none;
        color: #c95402;
    }

    .table_search , .actions, .table_pagination{
        display: inline-block;
        padding: 4px;
    }

    .table_pagination{
        text-align:center;
        width: 100%;
    }

    .table_pagination .button{
        margin-left:10px;
    }

    .actions select{
        height: 22px;
        margin-left: 2px;
    }

    input[type='checkbox'] {
        border: 1px solid #B4B9BE;
        background: #FFF none repeat scroll 0% 0%;
        color: #555;
        clear: none;
        cursor: pointer;
        display: inline-block;
        line-height: 0;
        height: 16px;
        margin: 0px 8px 0px 0px;
        outline: 0px none;
        padding: 0px !important;
        text-align: center;
        vertical-align: middle;
        width: 16px;
        min-width: 16px;
        box-shadow: 0px 1px 2px rgba(0, 0, 0, 0.1) inset;
        transition: border-color 0.05s ease-in-out 0s;
    }
</style>
<?php
        $this->custome_styles(); 
    }
    
    protected function custome_styles( )
    {
        //complete it in your extended class
    }
    
    private function table_js()
    {
        ?>
<script type="text/javascript" >
    jQuery(document).ready(function ($) {
        $('.check_all').click(function () {
            $('input.column_checkbox, input.check_all').not(this).prop('checked', this.checked);
        });
    });
</script>
<?php
    }
    
}
