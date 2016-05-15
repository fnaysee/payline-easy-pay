<?php defined( 'ROOTPATH' ) or exit;  ?>
<!DOCTYPE html>
<!--[if IE 7]> <html class="ie ie7" dir="rtl" lang="fa-IR" prefix="og: http://ogp.me/ns#"> <![endif]-->
<!--[if IE 8]> <html class="ie ie8" dir="rtl" lang="fa-IR" prefix="og: http://ogp.me/ns#"> <![endif]-->
<!--[if !(IE 7) | !(IE 8) ]><!-->
<html dir="rtl" lang="fa-IR" prefix="og: http://ogp.me/ns#">
<!--<![endif]-->
<head>
    <title><?php the_site_title(); ?></title>
    <?php the_template_head(); ?>
</head>
<body>
    <?php after_body_open(); ?>

    <div class="logo_wrapp">
        <div class="logo">
        </div>
        <h1 class="title"><?php the_site_title(); ?></h1>
    </div>

    <div class="topmenu">
        <div class="top_menu_item"><a href="<?php echo ROOTURL; ?>">صفحه نخست</a></div>
        <div class="top_menu_item"><a href="<?php echo ROOTURL; ?>admin">ورود مدیر</a></div>
    </div>

    <?php the_template_body(); ?>

    <div class="bank"></div>
    <h2>شما می توانید با استفاده از کلیه کارت های بانکی عضو شتاب ، عملیات پرداخت را سریع و مطمئن از طریق درگاه پی لاین انجام دهید.</h2>


    <!--FOOTER-->
    <div class="footer">
        آسان پرداخت 
        <a href="http://payline.ir" target="_blank">پی لاین</a><br />
        طرح پوسته : فامو
        . طراحی و پیاده سازی: فرحان نیسی
    <?php the_template_footer(); ?>
    </div>
    <!--/FOOTER-->
    <?php before_body_close(); ?>
</body>
</html>
