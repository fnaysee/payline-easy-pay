<?php 
defined( 'ROOTPATH' ) or exit;
?>
<!DOCTYPE html>
<!--[if IE 7]> <html class="ie ie7" dir="rtl" lang="fa-IR" prefix="og: http://ogp.me/ns#"> <![endif]-->
<!--[if IE 8]> <html class="ie ie8" dir="rtl" lang="fa-IR" prefix="og: http://ogp.me/ns#"> <![endif]-->
<!--[if !(IE 7) | !(IE 8) ]><!-->
<html dir="rtl" lang="fa-IR" prefix="og: http://ogp.me/ns#">
<!--<![endif]-->
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>پنل مدیریت آسان پرداخت پی لاین</title>
    <meta name="description" content="سامانه پرداخت آنلاین">
    <meta name="keywords" content="پرداخت آنلاین,فامو,payline,پی لاین">
    <link href="<?php echo INCLUDESURL; ?>css/admin.css" rel="stylesheet" type="text/css">
    <link href="<?php echo INCLUDESURL; ?>images/favicon.ico" rel="shortcut icon">
    <script src="<?php echo INCLUDESURL; ?>js/jquery.js" type="text/javascript"></script>
    <?php do_action('admin_head'); ?>
</head>
<body>
    <div class="logo">
        <h1 class="title">پنل مدیر</h1>
    </div>
    <div class="topmenu">
        <div class="top_menu_item"><a href="<?php echo ROOTURL; ?>">صفحه نخست</a></div>
        <div class="top_menu_item"><a href="<?php echo ROOTURL; ?>admin">ورود مدیر</a></div>
    </div>

    <!--SIDEBAR-->
    <div class="content">
        <div class="rightbar">
            <div class="title">تنظیمات</div>
            <?php the_admin_sidebar(); ?>
        </div>
        <!--/SIDEBAR-->

        <!--CONTENT-->
        <div class="leftbar">
            <?php the_admin_page_content(); ?>
        </div>
        <br style="clear: both;">
    </div>
    <!--/CONTENT-->

    <!--FOOTER-->
    <div class="footer">
        آسان پرداخت 
        <a href="http://payline.ir" target="_blank">پی لاین</a><br />
        طرح پوسته : فامو
        . طراحی و پیاده سازی: فرحان نیسی
    </div>
</body>
</html>
<!--/FOOTER-->
