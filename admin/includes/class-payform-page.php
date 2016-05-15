<?php
final class Payform_Page
{
    private $settings_name;
    
    private $messages;
    
	public function __construct( )
    {
        if( isset( $_POST['payform_page_submit'] ) )
        {
            $this->verify();
        }
        
        $this->settings_name = 'payform_page';
        $successfull_payment = data()->options->get_option( 'successfull_payment' );
        $this->fields = array(
            array( 
                'name'      => $this->settings_name . '[successfull_payment]',
                'type'      => 'checkbox',
                'title'     => 'مخفی سازی فرم پس از پرداخت موفق',
                'classes'   => 'show_form_after_successfull_payment',
                'table'     => true,
                'choices'   => array(
                    array(
                        'name'      => "[show_form_after_successfull_payment]",
                        'text'      => "<label for='show_form_after_successfull_payment'>اگر می خواهید پس از پرداخت موفق فرم مخفی شده و  پیام زیر به نمایش درآید، این گزینه را فعال نمایید.</label>",
                        'checked'   => ( isset( $successfull_payment['show_form_after_successfull_payment'] ) ) ? true : false,
                        'value'     => '1'
                    )
                )
            ),
            array( 
                'name'      => $this->settings_name . '[successfull_payment][successfull_payment_message]',
                'type'      => 'textarea',
                'title'     => 'پیام پرداخت موفق',
                'text'      => '<div>برای نمایش شناسه تراکنش به کاربر می توانید از عبارت [TRANS_ID] در متن پیامتان استفاده نمایید.</div>',
                'content'   => ( isset( $successfull_payment['successfull_payment_message'] ) ) ? $successfull_payment['successfull_payment_message'] : '' ,
                'classes'   => 'successfull_payment_message'
            )
        );
    }
    
    public function print_add_field( )
    {
        $active_fields = data()->options->get_option( 'payment_form_active_fields' );
        $required_fields = data()->options->get_option( 'payment_form_required_fields' );
        $last_field_number = data()->options->get_option( 'last_field_number' );
?>
<tr>
    <th>فیلدهای فرم</th>
    <td>
        <div class="add-field-block">
            <input class="settings-name" type="hidden" value="<?php echo $this->settings_name; ?>" />
            <input class="last-field" name="<?php echo $this->settings_name; ?>[last_field_number]" type="hidden" value="<?php echo ( ( is_numeric( $last_field_number ) ) ? $last_field_number : 100 ); ?>" />
            <?php
        if( ( $fields = data()->options->get_option( 'payment_form_fields' ) ) ) 
        {
            ?>

            <?php
            foreach( $fields as $cus_field => $val ) { 
                if( ! isset( $val['type'] ) ) $val['type'] = 'text';
                
            ?>
            <div class="custome-field">
                <input class="field-id" type="hidden" name="<?php echo $this->settings_name; ?>[payment_form_fields][<?php echo $val['name']; ?>][id]" style="width:150px" value="<?php echo $val['id']; ?>" />
                <input class="field-name" type="hidden" name="<?php echo $this->settings_name; ?>[payment_form_fields][<?php echo $val['name']; ?>][name]" style="width:150px" value="<?php echo $val['name']; ?>" />
                <label>عنوان فیلد</label>
                <input class="field-title" type="text" name="<?php echo $this->settings_name; ?>[payment_form_fields][<?php echo $val['name']; ?>][title]" style="width:150px" value="<?php echo $val['title']; ?>" />
                <label>نوع فیلد </label>
                <select class="field-type" name="<?php echo $this->settings_name; ?>[payment_form_fields][<?php echo $val['name']; ?>][type]" style="width:150px" >
                    <option value="text"      <?php echo ( ( $val['type'] == 'text' ) ?  'selected="selected"' : ''); ?> >فیلد متنی</option>
                    <option value="textarea"  <?php echo ( ( $val['type'] == 'textarea' ) ?  'selected="selected"' : ''); ?> >فیلد متن چند خطی </option>
                    <option value="password"  <?php echo ( ( $val['type'] == 'password' ) ?  'selected="selected"' : ''); ?> >فیلد پسورد </option>
                </select>

                <input id="<?php echo $val['id']; ?>-active" class="field-active" type="checkbox" name="<?php echo $this->settings_name; ?>[payment_form_active_fields][<?php echo $val['name']; ?>]" <?php echo ( ( array_key_exists( $val['name'], $active_fields ) )? 'checked="checked"' : '' ) ?> />
                <label for="<?php echo $val['id']; ?>-active">فعال</label>

                <input id="<?php echo $val['id']; ?>-req" class="field-required" type="checkbox" name="<?php echo $this->settings_name; ?>[payment_form_required_fields][<?php echo $val['name']; ?>]" <?php echo ( ( array_key_exists( $val['name'], $required_fields ) )? 'checked="checked"' : '' ) ?> />
                <label for="<?php echo $val['id']; ?>-req" >الزامی</label>

                <input type="button" class="form_btn button remove-field" name="submit" value="حذف" title=" حذف فیلد"  />
            </div>
            <?php 
            }
        }
            ?>
            <input type="button" class="form_btn button add-field" name="submit" value="افزودن فیلد" />

            <div class="sample-field">
                <input class="field-id" type="hidden" name="<?php echo $this->settings_name; ?>[payment_form_fields][custome-field-1][id]" style="width:150px" value="custome-field-1" />
                <input class="field-name" type="hidden" name="<?php echo $this->settings_name; ?>[payment_form_fields][custome-field-1][name]" style="width:150px" value="custome-field-1" />
                <label>عنوان فیلد </label>
                <input class="field-title" type="text" name="<?php echo $this->settings_name; ?>[payment_form_fields][custome-field-1][title]" style="width:150px"  />
                <label>نوع فیلد </label>
                <select class="field-type" name="<?php echo $this->settings_name; ?>[payment_form_fields][custome-field-1][type]" style="width:150px" >
                    <option value="text">فیلد متنی </option>
                    <option value="textarea">فیلد متن چند خطی </option>
                    <option value="password">فیلد پسورد </option>
                </select>

                <input id="custome-field-1-active" class="field-active" type="checkbox" name="<?php echo $this->settings_name; ?>[payment_form_active_fields][custome-field-1]" />
                <label for="custome-field-1-active" class="field-active-label">فعال</label>

                <input id="custome-field-1-req" class="field-required" type="checkbox" name="<?php echo $this->settings_name; ?>[payment_form_required_fields][custome-field-1]" />
                <label for="custome-field-1-req" class="field-required-label">الزامی</label>

                <input type="button" class="form_btn button remove-field" name="submit" value="حذف" title="حذف فیلد " />
            </div>

        </div>
    </td>
</tr>
<?php
    }
    
    public function print_amount_field() {
        $field = ( data()->options->get_option( 'payment_form_amount_field' ) ) ? data()->options->get_option( 'payment_form_amount_field' ) : array();
        $fixed_prices = ( isset( $field['fixed'] ) && isset( $field['fixed'] ) ) ? $field['fixed']  : array() ;
        $last_price_number = data()->options->get_option( 'last_price_number' );
?>
<tr>
    <th>مبالغ فرم</th>
    <td>
        <div>
            <input class="last-price" name="<?php echo $this->settings_name; ?>[last_price_number]" type="hidden" value="<?php echo ( ( is_numeric( $last_price_number ) ) ? $last_price_number : 100 ); ?>" />
            <div>
                <label for="amount-field-fixed">
                    <input id="amount-field-fixed" class="amount-type fixed" type="radio" name="<?php echo $this->settings_name; ?>[payment_form_amount_field][type]" value="fixed" <?php echo ( ( $field['type'] == 'fixed' ) ? 'checked="checked"' : '' ); ?> />مبالغ معین
                </label>
                <div class="amount-price-block">
                    <?php foreach( $fixed_prices as $fp => $val ) {
                    ?>
                    <div class="custome-amount-field">
                        <input class="amount-field-id" type="hidden" name="<?php echo $this->settings_name; ?>[payment_form_amount_field][fixed][<?php echo $val['name']; ?>][id]" value="<?php echo $val['id']; ?>">
                        <input class="amount-field-name" type="hidden" name="<?php echo $this->settings_name; ?>[payment_form_amount_field][fixed][<?php echo $val['name']; ?>][name]" value="<?php echo $val['name']; ?>">
                        <input class="amount-field-type" type="hidden" name="<?php echo $this->settings_name; ?>[payment_form_amount_field][fixed][<?php echo $val['name']; ?>][type]" value="radio">

                        <label for="<?php echo $val['id']; ?>" class="amount-field-value-label">مبلغ</label>
                        <input id="<?php echo $val['id']; ?>" class="amount-field-value" type="text" name="<?php echo $this->settings_name; ?>[payment_form_amount_field][fixed][<?php echo $val['name']; ?>][value]" style="width:50px;" value="<?php echo $val['value']; ?>">

                        <input type="button" class="form_btn button remove-field" name="submit" value="حذف" title="حذف مبلغ" />
                    </div>
                    <?php } ?>

                    <input type="button" class="form_btn button add-amount" name="submit" value="افزودن مبلغ" />
                </div>

                <label for="amount-field-user-input">
                    <input id="amount-field-user-input" class="amount-type user-input" type="radio" name="<?php echo $this->settings_name; ?>[payment_form_amount_field][type]" value="user-input" <?php echo ( ( $field['type'] == 'user-input' ) ? 'checked="checked"' : '' ); ?> />توسط کاربر تعیین شود
                </label>
            </div>
            <div class="sample-amount-field">
                <input class="amount-field-id" type="hidden" name="" value="">
                <input class="amount-field-name" type="hidden" name="" value="">
                <input class="amount-field-type" type="hidden" name="" value="radio">

                <label for="" class="amount-field-value-label">مبلغ</label>
                <input id="" class="amount-field-value" type="text" name="" style="width: 50px;" value="">

                <input type="button" class="form_btn button remove-field" name="submit" value="حذف" title="حذف مبلغ" />
            </div>
        </div>
    </td>
</tr>
<?php
    }
    
    public function print_page_js( )
    {
?>
<script type="text/javascript">
    jQuery(document).ready(function ($) {
        var temp = $('.sample-field, .sample-amount-field').clone();
        $('.sample-field, .sample-amount-field').remove();
        $('body').append(temp);

        //For add field in admin

        var busy = false;
        $('.add-field-block .add-field').click(function () {
            if (busy)
                return;

            busy = true;

            var customeFields = parseInt($('.last-field').val(), 10) + 1;
            var temp = $('.sample-field').addClass('cloned').clone();
            $('.sample-field.cloned').removeClass('cloned');

            $(this).before(temp);
            
            setTimeout(function () {
                $('.cloned .field-id').attr({
                    'name': $('.settings-name').val() + '[payment_form_fields][custome-field-' + customeFields + '][id]',
                    'value': 'custome-field-' + customeFields
                });
                $('.cloned .field-name').attr({
                    'name': $('.settings-name').val() + '[payment_form_fields][custome-field-' + customeFields + '][name]',
                    'value': 'custome-field-' + customeFields
                });
                $('.cloned .field-title').attr('name', $('.settings-name').val() + '[payment_form_fields][custome-field-' + customeFields + '][title]');
                $('.cloned .field-type').attr('name', $('.settings-name').val() + '[payment_form_fields][custome-field-' + customeFields + '][type]');
                $('.cloned .field-active').attr({
                    'name': $('.settings-name').val() + '[payment_form_acive_fields][custome-field-' + customeFields + ']',
                    'id': 'custome-field-' + customeFields,
                });
                $('.cloned .field-active-label').attr({ 'for': 'custome-field-' + customeFields });
                $('.cloned .field-required').attr({
                    'name': $('.settings-name').val() + '[payment_form_required_fields][custome-field-' + customeFields + ']',
                    'id': 'custome-field-' + customeFields + '-req',
                });
                $('.cloned .field-required-label').attr({ 'for': 'custome-field-' + customeFields + '-req' });

                $('.cloned').addClass('custome-field').removeClass('cloned').removeClass('sample-field');
                busy = false;
            }, 200);

            $('.last-field').val(customeFields);

        });

        $('.add-field-block').on('click', '.remove-field', function () {
            $(this).parent().remove();
        });


        //For add field in admin

        var busy2 = false;
        $('.amount-price-block .add-amount').click(function () {
            if (busy2)
                return;

            busy2 = true;

            var customeAmounts = parseInt($('.last-price').val(), 10) + 1;
            var temp = $('.sample-amount-field').addClass('cloned').clone();
            $('.sample-amount-field.cloned').removeClass('cloned');

            $(this).before(temp);

            setTimeout(function () {
                $('.cloned .amount-field-id').attr({
                    'name': $('.settings-name').val() + '[payment_form_amount_field][fixed][payment-amount-' + customeAmounts + '][id]',
                    'value': 'payment-amount-' + customeAmounts
                });
                $('.cloned .amount-field-name').attr({
                    'name': $('.settings-name').val() + '[payment_form_amount_field][fixed][payment-amount-' + customeAmounts + '][name]',
                    'value': 'payment-amount-' + customeAmounts
                });
                $('.cloned .amount-field-type').attr('name', $('.settings-name').val() + '[payment_form_amount_field][fixed][payment-amount-' + customeAmounts + '][type]');
                $('.cloned .amount-field-value').attr({
                    'name': $('.settings-name').val() + '[payment_form_amount_field][fixed][payment-amount-' + customeAmounts + '][value]',
                    'id': 'payment-amount-' + customeAmounts
                });
                $('.cloned .amount-field-value-label').attr({ 'for': 'payment-amount-' + customeAmounts });

                $('.cloned').addClass('custome-amount-field').removeClass('cloned').removeClass('sample-amount-field');

                $('.last-price').val(customeAmounts);
                busy2 = false;
            }, 200);

        });

        $('.amount-price-block').on('click', '.remove-field', function () {
            $(this).parent().remove();
        });
    });
</script>
<?php
    }
    
    
    public function print_the_page()
    {
        $this->print_page_styles();
        $this->messages();
?>
<form action="<?php echo ROOTURL; ?>index.php?admin&page=payform" method="post" >
    <input type="hidden" name="payform_page_submit" value="true" />
    <table id="data">
        <?php 
        $this->print_add_field();
        $this->print_amount_field();
        
        foreach( $this->fields as $item )
        {
            echo "<tr><th scope='row'>{$item['title']}</th><td>";
            echo data()->settings_api->get_a_form_field( $item );
            echo "</td></tr>";
        }
        ?>


        <?php do_action( 'payform_page_table' ); ?>
    </table>
    <input type="submit" class="submit_btn button" value="ذخیره" />
</form>
<?php
        $this->print_page_js();
    }
    
    public function print_page_styles( )
    {
?>
<style>
    .sample-field, .sample-amount-field {
        display: none;
    }

    .custome-styles {
        width: 500px;
        height: 400px;
        direction: ltr;
    }

    .successfull_payment_message {
        width: 500px;
        height: 200px;
    }
</style>
<?php
    }
    
    private function verify()
    {
        if( isset( $_POST['payform_page'] ) )
        {
            foreach ( $_POST['payform_page'] as $key => $val )
            {
                data()->options->add_option( $key, $val );
            }
        }
        
        $this->messages[] = array( 'message' => 'تغییرات با موفقیت ذخیره شد.', 'type' => 'updated' );
    }
    
    public function messages() 
    {
        $msgs = $this->messages;
        $output = '<div class="messages" >';
        if( ! empty( $msgs ) ) 
        {
            foreach( $msgs as $msg )
            {
                $output .= '<div class="';
                if( $msg['type'] == 'error' )
                {
                    $output .= 'error"';
                }
                elseif( $msg['type'] == 'updated' )
                {
                    $output .= 'updated"';
                }
                
                $output .= ">{$msg['message']}</div>";
            }

        }
        
        echo $output . "</div>";
    }
}
