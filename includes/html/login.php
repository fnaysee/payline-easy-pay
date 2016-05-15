<?php 
defined( 'ROOTPATH' ) or exit; 
$fields = array(
        array(
            'name' => 'user_name',
            'table' => true,
            'title' => 'نام کاربری',
            'type' => 'text',
            'classes' => 'dir-ltr',
            'value' => ( isset( $_POST['user_name'] ) )? $_POST['user_name'] : ''
        ),
        array(
            'name' => 'user_pass',
            'table' => true,
            'title' => 'کلمه عبور',
            'type' => 'password',
            'classes' => 'dir-ltr',
            'value' => ( isset( $_POST['user_pass'] ) )? $_POST['user_pass'] : ''
        )
    );


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
    <style>
        table, tbody, tr {
            width: 100%;
        }

        th, td {
            display: inline-block;
            padding: 5px;
        }

        th {
            width: 20%;
        }

        td {
            width: 60%;
        }

        .support_img {
            width: 150px;
        }
    </style>
</head>
<body>
    <div class="logo">
        <h1 class="title">پنل مدیر</h1>
    </div>
    <div class="topmenu">
        <div class="top_menu_item"><a href="<?php echo ROOTURL; ?>">صفحه نخست</a></div>
        <div class="top_menu_item"><a href="<?php echo ROOTURL; ?>admin">ورود مدیر</a></div>
    </div>
    <div class="content">
        <div class="rightbar">
            <div class="title">پشتیبانی</div>
            <div class="mohtava">
                <a target="_self" href="<?php the_site_url(); ?>?support">
                    <img class="support_img" border="0" src="<?php the_templates_url(); ?>default/images/support.png"></a>
            </div>
        </div>
        <div class="leftbar">
            <form class="form" action="<?php echo ROOTURL; ?>?login=1" method="post">
                <input type="hidden" name="login_form" value="1" />
                <?php 
                foreach ( $this->messages as $message )
                {?>
                <div class="error"><?php echo $message; ?></div>
                <?php }
                
                ?>
                <table width="100%">
                    <?php 
                    foreach ( $fields as $item )
                    {
                        echo "<tr><th >" .  ( isset( $item['title'] )? $item['title'] : '' ) . "</th><td>";
                        echo $settings_api->get_a_form_field( $item );
                        echo "</td></tr>";
                    }
                    ?>
                    <tr>
                        <th></th>
                        <td>
                            <input class="button" type="submit" value="ورود"></td>
                    </tr>
                </table>
            </form>
        </div>
        <br style="clear: both;">
    </div>
    <!--FOOTER-->
    <div class="footer">
        آسان پرداخت 
        <a href="http://payline.ir" target="_blank">پی لاین</a><br />
        طرح پوسته : فامو
        . طراحی و پیاده سازی: فرحان نیسی
    </div>
    <!--/FOOTER-->
</body>
</html>
