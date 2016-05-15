<?php defined( 'ROOTPATH' ) or exit; ?>

<div class="mohtava" style="line-height: 20px; margin-top: -5px;">
        <?php 
    foreach ( $items as $item )
    {
    ?>
<a href="<?php echo $item['link']; ?>" title="<?php echo $item['title']; ?>" style="display: block; text-align: right;"><?php echo $item['title']; ?></a>
    <?php
    }
    ?>
<a href="<?php echo ROOTURL; ?>index.php?logout" style="display: block; text-align: right;">خـروج</a>
</div>