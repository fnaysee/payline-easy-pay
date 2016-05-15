<?php defined( 'ROOTPATH' ) or exit; ?>
<div class="content">
    <div class="rightbar">
        <div class="title">پشتیبانی</div>
        <div class="mohtava">
            <a target="_self" href="<?php the_site_url(); ?>?support">
                <img class="support_img" border="0" src="<?php the_templates_url(); ?>default/images/support.png"></a>
        </div>
    </div>

    <div class="leftbar">
        <p class="admin_note">
            <?php 
            $text = data()->options->get_option( 'support_page_text' );
            if( $text )
                echo $text;
            else{
            ?>
            جهت برقراری ارتباط با ما فرم پشتیبانی را به دقت تکمیل نمایید.
            <br />
            نکته: در صورتی که تراکنشی انجام داده اید، حتما شماره تراکنش را در توضیحات ذکر نمایید.
        
        <?php } ?>
        </p>
        <?php $support->support_form(); ?>
    </div>
    <br style="clear: both;">
</div>
