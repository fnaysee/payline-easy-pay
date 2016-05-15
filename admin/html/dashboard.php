<?php defined( 'ROOTPATH' ) or exit; ?>
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
<div class="dashboard">
    <h1>داشبورد</h1>
    <br />
    <p>
        در این صفحه می توانید آخرین گزارش های سیستم را مشاهده فرمایید.
    </p>
    <table id="data">
        <tr>
            <th>نسخه php</th>
            <td><?php echo PHP_VERSION; ?></td>
        </tr>
        <tr>
            <th>پوسته فعال</th>
            <td><?php echo data()->options->get_option( 'active_template' ); ?></td>
        </tr>
        <?php do_action( 'dashboard_page_table' ); ?>
    </table>
</div>
