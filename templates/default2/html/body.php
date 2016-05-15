<?php defined( 'ROOTPATH' ) or exit; ?>
<div class="rightpanel">
    <?php the_form(); ?>
</div>
<div class="leftpanel">
    <?php 
    $text = data()->options->get_option( 'main_page_text' );
    if( $text )
        echo $text;
    else{
    ?>
        <br />
لطفا اطلاعات خود را با دقت وارد نمایید.
<br />
این متن را می توانید از طریق پنل تنظیمات تغییر دهید.
    <?php }
    
    ?>
</div>