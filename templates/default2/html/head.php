<?php defined( 'ROOTPATH' ) or exit; ?>
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="<?php echo the_active_template_url() . 'css/style.css' ?>" />
<style>
    body{
        background-image: url(<?php echo the_active_template_url(); ?>images/background.jpg);
        background-attachment: fixed;
        background-position: top center;
        background-size: cover;
    }

    .rightpanel{
        opacity: 0;
    }
    
</style>
<script type="text/javascript" src="<?php echo INCLUDESURL; ?>js/jquery.js"></script>
<script>
    jQuery(document).ready(function ($) {
        $('.rightpanel').css('marginRight', '-200px');
        $('.rightpanel').show();
        $('.rightpanel').animate({ opacity: 1, marginRight: '0px' }, 1300);
        setTimeout(function () {
            $('.rightpanel').css('marginRight', 'auto');
        },1350 );
    });
</script>