<?php 
defined( 'ROOTPATH' ) or exit;

?>
<div class="oldpaylogs">
    <h1>گزارش های پرداخت قدیمی</h1>
    <br />
    <?php 
    $logs->print_table();
    ?>
</div>
