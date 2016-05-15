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
            $text = data()->options->get_option( 'main_page_text' );
            if( $text )
                echo $text;
            else{
            ?>
            لطفا اطلاعات خود را با دقت وارد نمایید.
            <br />
            این متن را می توانید از طریق پنل تنظیمات تغییر دهید.
        <?php } ?>
        </p>
        <?php the_form(); ?>
    </div>
    <br style="clear: both;">
</div>
