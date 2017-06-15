<div class="um <?php echo $this->get_class( $mode ); ?> um-<?php echo $form_id; ?>">

	<div class="um-form">
	
		<form method="post" action="">
	
		<?php

			do_action("um_before_form", $args);

//			do_action("um_before_{$mode}_fields", $args);

			do_action("um_main_{$mode}_fields", $args);

			do_action("um_after_form_fields", $args);?>
            <label for="sms_verify">短信验证：</label> <input id="sms_verify" type="text"> <input id="sms_verify_button" value="获取验证码" type="button" onclick=alert("是否可以在此插入短信验证"); />

		<?php do_action("um_after_{$mode}_fields", $args);

			do_action("um_after_form", $args);
			
		?>
		
		</form>
	
	</div>
	
