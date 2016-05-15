<?php defined( 'ROOTPATH' ) or exit; ?>
<div class="rightpanel">
    <h2 class="support_form_title">فرم پشتیبانی</h2>
    <?php $support->support_form(); ?>
</div>
<div class="leftpanel">
        <?php 
        $text = data()->options->get_option( 'support_page_text' );
    if( $text )
        echo $text;
    else{
    ?>
<br />
جهت برقراری ارتباط با ما فرم پشتیبانی را به دقت تکمیل نمایید.
<br />
نکته: در صورتی که تراکنشی انجام داده اید، حتما شماره تراکنش را در توضیحات ذکر نمایید.
    <?php }
    
    ?>
</div>