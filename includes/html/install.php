<?php 
defined( 'ROOTPATH' ) or exit; 
$templates = Misc::get_valid_plugins_templates( TEMPLATESPATH );
$fields = array(
        array(
            'name' => 'admin_user',
            'table' => true,
            'title' => 'نام کاربری ادمین',
            'type' => 'text',
            'classes' => 'dir-ltr',
            'value' => ( isset( $_POST['admin_user'] ) )? $_POST['admin_user'] : ''
        ),
        array(
            'name' => 'admin_pass',
            'table' => true,
            'title' => 'کلمه عبور ادمین',
            'type' => 'password',
            'classes' => 'dir-ltr',
            'value' => ( isset( $_POST['admin_pass'] ) )? $_POST['admin_pass'] : ''
        ),
        array(
            'name' => 'admin_pass_confirm',
            'table' => true,
            'title' => 'تایید کلمه عبور',
            'type' => 'password',
            'classes' => 'dir-ltr',
            'value' => ( isset( $_POST['admin_pass_confirm'] ) )? $_POST['admin_pass_confirm'] : ''
        ),
        array(
            'name' => 'admin_email',
            'table' => true,
            'title' => 'ایمیل ادمین',
            'type' => 'text',
            'classes' => 'dir-ltr',
            'value' => ( isset( $_POST['admin_email'] ) )? $_POST['admin_email'] : ''
        ),
        array(
            'name' => 'site_email',
            'table' => true,
            'title' => 'ایمیل سایت',
            'type' => 'text',
            'classes' => 'dir-ltr',
            'value' => ( isset( $_POST['site_email'] ) )? $_POST['site_email'] : ''
        ),
        array(
            'name' => 'site_url',
            'table' => true,
            'title' => 'آدرس سایت',
            'type' => 'text',
            'classes' => 'dir-ltr',
            'value' => ( isset( $_POST['site_url'] ) )? $_POST['site_url'] : 'http://example.com/'
        ),
        array(
            'name' => 'site_title',
            'table' => true,
            'title' => 'عنوان سایت',
            'type' => 'text',
            'value' => ( isset( $_POST['site_title'] ) )? $_POST['site_title'] : ''
        ),
        array(
            'name' => 'db_host',
            'table' => true,
            'title' => 'آدرس هاست دیتابیس',
            'type' => 'text',
            'classes' => 'dir-ltr',
            'value' => ( isset( $_POST['db_host'] ) )? $_POST['db_host'] : 'localhost'
        ),
        array(
            'name' => 'db_name',
            'table' => true,
            'title' => 'نام دیتابیس',
            'type' => 'text',
            'classes' => 'dir-ltr',
            'value' => ( isset( $_POST['db_name'] ) )? $_POST['db_name'] : ''
        ),
        array(
            'name' => 'db_user',
            'table' => true,
            'title' => 'نام کاربری دیتابیس',
            'type' => 'text',
            'classes' => 'dir-ltr',
            'value' => ( isset( $_POST['db_user'] ) )? $_POST['db_user'] : ''
        ),
        array(
            'name' => 'db_pass',
            'table' => true,
            'title' => 'کلمه عبور دسترسی به دیتابیس',
            'type' => 'text',
            'classes' => 'dir-ltr',
            'value' => ( isset( $_POST['db_pass'] ) )? $_POST['db_pass'] : ''
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
    <title>نصب آسان پرداخت پی لاین</title>
    <meta name="description" content="سامانه پرداخت آنلاین">
    <meta name="keywords" content="پرداخت آنلاین,payline,پی لاین">
    <link href="includes/css/admin.css" rel="stylesheet" type="text/css">
    <link href="includes/images/favicon.ico" rel="shortcut icon">
    <style>
        .leftbar {
            width: auto;
            float:none;
        }

            .leftbar ul {
                list-style: inside;
            }

        h2{
            text-align: right;
            font-size: 18px;
        }

        table, tbody, tr {
            width: 100%;
        }

        th, td {
            display: inline-block;
            padding: 5px;
        }

        th {
            width: 30%;
        }

        td {
            width: 60%;
        }

        .support_img {
            width: 150px;
        }

        .dir-ltr{
            direction: ltr;
            text-align: left;
        }
    </style>
</head>
<body>
    <div class="logo">
        <h1 class="title">نصب کننده</h1>
    </div>
    <div class="topmenu">
    </div>
    <div class="content">
        <div class="leftbar">
            <h1>به نصب کننده ی آسان پرداخت پی لاین خوش آمدید.
            </h1>
            <h2>پیش از ادامه به نکات زیر توجه نمایید:
            </h2>
            <div>
                <ul>
                    <li>در برخی شرکت های هاستینگ ممکن است امکان ویرایش فایل کانفیگ برای اسکریپت فراهم نباشد در این صورت می توانید این فایل را دستی ویرایش نمایید.
                    </li>
                    <li>در صورتی که اسکریپت را در یک زیر پوشه قرار داده اید، آدرس پوشه ی اسکریپت را نیز به آدرس دامنه اضافه نموده و حتما یک اسلش در انتهای آدرس قرار دهید.
                    </li>
                    <li>پس از نصب، اسکریپت به طور خودکار امکان دستیابی به نصب کننده ی خود را غیر فعال می کند برای فعال کردن دستی نصب کننده و نصب مجدد، در فایل کانفیگ مقدار مقابل عبارت ISFIRSTRUN را به false تغییر دهید. ولی دقت نمایید که با وجود فعال بودن نصب کننده تنها زمانی به نصب کننده هدایت می شوید که اطلاعات اتصال به دیتابیس صحیح نبوده و یا تیبل های اسکریپت در دیتابیس موجود نباشند.
                    </li>
                </ul>
            </div>
            <form class="form" action="?install=1" method="post">
                <input type="hidden" name="installation" value="1" />
                <?php 
                foreach ( $this->messages as $message )
                {
        	        echo "<div class='error'>{$message}</div>";
                }
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
                            <input class="button" type="submit" value="ذخیره"></td>
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
