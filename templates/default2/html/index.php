<?php defined( 'ROOTPATH' ) or exit;  ?>
<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="utf-8" />
    <title><?php the_site_title(); ?></title>
    <?php the_template_head(); ?>
</head>
<body>
    <?php after_body_open(); ?>
    <div id="support"><a href="<?php the_site_url(); ?>?support" target="_blank"><img src="<?php echo the_active_template_url(); ?>images/support.png" />پشتیبانی</a></div>
    <div class="wrapp">
        <div class="main">
            <h1 class="site-title">
                <a href="http://payline.ir/">
                    <img src="<?php echo the_active_template_url(); ?>images/logo2.png" />
                </a>
                <a href="<?php the_site_url(); ?>">
                    <?php the_site_title(); ?>
                </a>
            </h1>
            <?php the_template_body(); ?>
        </div>
        <div class="footer">
            <?php the_template_footer(); ?>
        </div>
    </div>
    <?php before_body_close(); ?>
</body>
</html>
