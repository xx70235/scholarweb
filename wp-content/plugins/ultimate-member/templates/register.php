<div class="um <?php echo $this->get_class( $mode ); ?> um-<?php echo $form_id; ?>">


    <div class="um-form">

        <form method="post" action="">

            <?php

            do_action("um_before_form", $args);

            do_action("um_before_{$mode}_fields", $args);

            do_action("um_main_{$mode}_fields", $args);

            do_action("um_after_form_fields", $args);?>

<!--            <div class="um-field um-field-address um-field-text" data-key="address">-->
<!--                <div class="um-field-label">-->
<!--                    <label for="mobile-phone">手机号码</label>-->
<!--                    <span class="um-tip um-tip-w" title="手机号码">-->
<!--                        <i class="um-icon-help-circled"></i></span>-->
<!--                    <div class="um-clear"></div>-->
<!--                </div>-->
<!--                <div class="um-field-area">-->
<!--                    <input autocomplete="off" class="um-form-field valid " type="text" name="mobile-phone" id="mobile-phone" value="" placeholder="" data-validate="" data-key="address">-->
<!--                    <input id="sms_verify_button" value="获取验证码 um" type="button"; />-->
<!--                </div>-->
<!--            </div>-->
<!---->
<!--            <div class="um-field um-field-address um-field-text" data-key="address">-->
<!--                <div class="um-field-label">-->
<!--                    <label for="sms-code">短信验证码</label>-->
<!--                    <span class="um-tip um-tip-w" title="短信验证码">-->
<!--                        <i class="um-icon-help-circled"></i></span>-->
<!--                    <div class="um-clear"></div>-->
<!--                </div>-->
<!--                <div class="um-field-area">-->
<!--                    <input autocomplete="off" class="um-form-field valid " type="text" name="sms-code" id="sms-code" value="" placeholder="" data-validate="" data-key="address">-->
<!--                </div>-->
<!--            </div>-->

            <!--<label for="sms_verify">手机号：</label> <input id="sms_verify" type="text"> <input id="sms_verify_button" value="获取验证码" type="button" onclick=alert("是否可以在此插入短信验证"); />

            <div>
                <label for="sms_verify">验证码：</label> <input id="sms_verify" type="text">

            </div>
-->
            <?php do_action("um_after_{$mode}_fields", $args);

            do_action("um_after_form", $args);

            ?>

        </form>

    </div>