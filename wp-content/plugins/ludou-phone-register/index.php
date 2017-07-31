<?php
/*
  Plugin Name: Ludou Phone Register
  Plugin URI: https://www.ludou.org/wordpress-register-check-user-phone-via-sms.html
  Description: 后台用户注册表单添加手机号，并通过短信验证码验证手机号
  Version: 1.0
  Author: 露兜
  Author URI: https://www.ludou.org
 */

if (!isset($_SESSION)) {
   session_start();
   session_regenerate_id(TRUE);
}

define('LCR_PLUGIN_URL', plugin_dir_url(__FILE__));
$table_name = 'ludou_phone_register';

// 初始化插件选项
register_activation_hook(__FILE__, 'ludou_phone_init');
register_deactivation_hook(__FILE__, 'ludou_phone_uninstall');

function ludou_phone_init() {
   add_option('Activated_ludou_phone', 'ludou_phone');
}

function ludou_phone_load_plugin() {

   if (is_admin() && get_option('Activated_ludou_phone') == 'ludou_phone') {

      delete_option('Activated_ludou_phone');

      global $wpdb, $table_name;

      // 创建插件数据库
      $charset_collate = $wpdb->get_charset_collate();

      $sql = "CREATE TABLE IF NOT EXISTS $table_name (
               `phone` varchar(14) NOT NULL COMMENT '手机号',
               `code` char(4) NOT NULL COMMENT '验证码',
               `time` bigint(20) unsigned NOT NULL COMMENT '时间戳'
               ) $charset_collate;

               ALTER TABLE $table_name
                  ADD PRIMARY KEY (`phone`),
                  ADD UNIQUE KEY `phone` (`phone`,`code`);";

      require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
      dbDelta($sql);
   }
}

add_action('admin_init', 'ludou_phone_load_plugin');

function ludou_phone_uninstall() {
//   global $wpdb, $table_name;
//
//   $sql = "DROP TABLE IF EXISTS $table_name";
//   $wpdb->query($sql);
}

/**
 * 后台注册模块，添加注册表单,修改新用户通知。
 */
if (!function_exists('wp_new_user_notification')) :

   /**
    * Notify the blog admin of a new user, normally via email.
    *
    * @since 2.0
    *
    * @param int $user_id User ID
    * @param string $plaintext_pass Optional. The user's plaintext password
    */
   function wp_new_user_notification($user_id, $deprecated = null, $notify = '') {
      if ($deprecated !== null) {
         _deprecated_argument(__FUNCTION__, '4.3.1');
      }

      global $wpdb, $wp_hasher;
      $user = get_userdata($user_id);

      // The blogname option is escaped with esc_html on the way into the database in sanitize_option
      // we want to reverse this for the plain text arena of emails.
      $blogname = wp_specialchars_decode(get_option('blogname'), ENT_QUOTES);

      if ('user' !== $notify) {
         $switched_locale = switch_to_locale(get_locale());
         $message = sprintf(__('New user registration on your site %s:'), $blogname) . "\r\n\r\n";
         $message .= sprintf(__('Username: %s'), $user->user_login) . "\r\n\r\n";
         $message .= sprintf(__('Email: %s'), $user->user_email) . "\r\n";

         @wp_mail(get_option('admin_email'), sprintf(__('[%s] New User Registration'), $blogname), $message);

         if ($switched_locale) {
            restore_previous_locale();
         }
      }
   }

endif;

add_filter('send_password_change_email', '__return_false');

add_filter('user_contactmethods', 'ludou_add_contact_fields');

// define the um_before_new_user_register callback
/**
 * 前台注册时 先到这里
 * @param $args
 */

function action_um_before_new_user_register( $args ) {
    // make action magic happen here...

    echo 123;
};

// add the action
add_action( 'um_before_new_user_register', 'action_um_before_new_user_register', 10, 1 );



function ludou_add_contact_fields($contactmethods) {
   global $current_user;

   if ($current_user->roles[0] == 'administrator')
      $contactmethods['phone'] = '手机号';

   return $contactmethods;
}

// 生成4位随机数字
function generateCode() {
   return mt_rand(0, 9) . mt_rand(100, 999);
}

// 验证是否为手机号
function isPhone($phone) {
   if ((empty($phone) || !preg_match("/^(((13[0-9]{1})|(15[0-9]{1})|(17[0-9]{1})|(18[0-9]{1}))+\d{8})$/", $phone)))
      return 0;
   else
      return 1;
}

/**
 * 给某个手机号发短信验证码
 * @param $phone
 * @return int
 */
function send_sms($phone) {
   require_once plugin_dir_path(__FILE__) . 'config.php';
   require_once plugin_dir_path(__FILE__) . 'lib/leancloundsms.php';


    //实例化类:两个参数分别为申请通过后的 App Key 和 App Secret
   $leancloundsms = new leancloundsms($id, $key);

   //短信接收号码: API请求参数rec_num的值
   $leancloundsms->sms_mobile($phone);

   //发送短信: 返回boolean值 TRUE 为成功 FALSE 为失败或发生异常
   $res = $leancloundsms->send();

   if (!$res)
      return 0;
   else
      return 1;
}

/**
 * 根据手机号 code 验证是否正确
 * @param $phone
 * @param $code
 * @return int
 */
function verify_code($phone, $code) {
    require_once plugin_dir_path(__FILE__) . 'config.php';
    require_once plugin_dir_path(__FILE__) . 'lib/leancloundsms.php';


    //实例化类:两个参数分别为申请通过后的 App Key 和 App Secret
    $leancloundsms = new leancloundsms($id, $key);

    //短信接收号码: API请求参数rec_num的值
    $leancloundsms->sms_mobile($phone);

    $leancloundsms->sms_code($code);

    //发送短信: 返回boolean值 TRUE 为成功 FALSE 为失败或发生异常
    $res = $leancloundsms->verify();

    if (!$res)
        return 0;
    else
        return 1;
}

/* 修改注册表单 */

function ludou_show_phone_field($args) {
   ?>

        <script>
            var ajaxurl = "<?php echo admin_url('admin-ajax.php'); ?>", pic_no = "<?php echo constant("LCR_PLUGIN_URL"); ?>img/no.png", captcha = "<?php echo constant("LCR_PLUGIN_URL"); ?>captcha/captcha.php";
        </script>

        <script src="<?php echo constant("LCR_PLUGIN_URL"); ?>check.js"></script>



    <div class="um-field um-field-user_login um-field-text" data-key="user_login">
        <div class="um-field-label">
            <label for="phone">手机号</label>
            <span class="um-tip um-tip-w" title="验证手机号">
                <i class="um-icon-help-circled"></i>
            </span>
            <div class="um-clear">

            </div>
        </div>
        <div class="um-field-area">
            <input id="phone" class="um-form-field valid " type="text"  value="<?php echo empty($_POST['phone']) ? '' : $_POST['phone']; ?>" name="phone" autocomplete="off" />
        </div>
    </div>

    <div class="um-field um-field-user_login um-field-text" data-key="user_login">
        <div class="um-field-label">
            <label for="code">短信验证码</label>
            <span class="um-tip um-tip-w" title="获取短信验证码">
<input id="sendSmsBtn" type="button" value="获取短信验证码" class="button button-secondary" />
</span>
            <div class="um-clear">

            </div>
        </div>
        <div class="um-field-area">
            <input id="code" class="um-form-field valid " type="text" value="<?php echo empty($_POST['code']) ? '' : $_POST['code']; ?>" name="code" />
        </div>
    </div>

<div class="um-field um-field-user_login um-field-text" data-key="user_login">
    <div class="um-field-label">

        <div class="um-clear">

        </div>
    </div>
    <div class="um-field-area">
    <input id="mainAcceptIpt" type="checkbox" checked="checked" tabindex="-1" class=""> 我已阅读并同意
    <a href="http://www.tschlr.com/用户协议_2017/" target="_blank" tabindex="-1">用户协议
    </a>
    </div>
</div>


   <input type="hidden" name="token" value="<?php
   echo wp_create_nonce( plugin_basename(__FILE__) ); ?>">
   <?php
}

/* 处理表单提交的数据 */

function ludou_check_phone_fields($login) {
    global $ultimatemember;
    global $wpdb;

   if(empty($_POST['token']) || !wp_verify_nonce( $_POST['token'], plugin_basename(__FILE__) ))
      wp_die('非法请求！');

   $phone = trim($_POST['phone']);
   if (!isPhone($phone)) {
       $ultimatemember->form->add_error('user_login', '手机号不正确，请确认');
      $_POST['phone'] = '';
      $_POST['code'] = '';
   }
   else {
      $phone_exist = $wpdb->get_var($wpdb->prepare("SELECT `user_id` FROM `" . $wpdb->prefix . "usermeta` WHERE `meta_key` = 'phone' AND `meta_value` = %s;", $phone));

      if (!empty($phone_exist)) {
          $ultimatemember->form->add_error('user_login', '该手机号已在本站注册过');
         $_POST['phone'] = '';
         $_POST['code'] = '';
      }
      else {
         if (empty($_POST['code'])) {
             $ultimatemember->form->add_error('user_login', '请填写短信验证码');

         }
         else {
             $verify_status = verify_code($phone, $_POST['code']);
             if ($verify_status == 0) {
                 $ultimatemember->form->add_error('user_login', '短信验证码不正确');
             }
         }
      }
   }
}

/* 保存表单提交的数据 */

function ludou_save_phone_fields($user_id) {
   global $wpdb, $table_name;
   update_user_meta($user_id, 'phone', $_POST['phone']);

   // 删除过期验证码
   $wpdb->query(
     $wpdb->prepare(
       "DELETE FROM `$table_name`
		 WHERE `phone` = %s
		", $_POST['phone']
     )
   );

   $userdata = array();
   $userdata['ID'] = $user_id;
   $userdata['user_pass'] = $_POST['user_pass'];

   wp_update_user($userdata);
}

function ludou_phone_remove_default_password_nag() {
   global $user_ID;
   delete_user_setting('default_password_nag', $user_ID);
   update_user_option($user_ID, 'default_password_nag', false, true);
}

function ludou_phone_change_translated_text($translated_text, $untranslated_text, $domain) {
   if ($untranslated_text === 'A password will be e-mailed to you.' || $untranslated_text === 'Registration confirmation will be emailed to you.')
      return '';
   else if ($untranslated_text === 'Registration complete. Please check your e-mail.' || $untranslated_text === 'Registration complete. Please check your email.')
      return '注册成功！';
   else
      return $translated_text;
}

add_filter('gettext', 'ludou_phone_change_translated_text', 20, 3);
add_action('admin_init', 'ludou_phone_remove_default_password_nag');

//add_action('register_form', 'ludou_show_phone_field');// 在注册页面加上 图片验证码 手机号码 获取验证码等几个input

//add_action('um_before_form_is_loaded', 'ludou_show_phone_field');// 在注册页面加上 图片验证码 手机号码 获取验证码等几个input
//add_action('um_after_form', 'ludou_show_phone_field');// 在注册页面加上 图片验证码 手机号码 获取验证码等几个input

//add_action('um_before_form', 'ludou_show_phone_field');// 在注册页面加上 图片验证码 手机号码 获取验证码等几个input
//
//add_action('um_after_form_fields', 'ludou_show_phone_field');// 在注册页面加上 图片验证码 手机号码 获取验证码等几个input
//
//add_action('um_main_register_fields', 'ludou_show_phone_field');// 在注册页面加上 图片验证码 手机号码 获取验证码等几个input
//
add_action('um_after_register_fields', 'ludou_show_phone_field');// 在注册页面加上 图片验证码 手机号码 获取验证码等几个input


//
//add_action('um_before_form_is_loaded', 'ludou_show_phone_field');// 在注册页面加上 图片验证码 手机号码 获取验证码等几个input




add_action('register_post', 'ludou_check_phone_fields', 10, 3);
//add_action('um_before_new_user_register', 'ludou_check_phone_fields', 10, 3);
//add_action('um_submit_form_register', 'ludou_check_phone_fields', 10, 3);
add_action('um_submit_form_errors_hook__registration', 'ludou_check_phone_fields', 10, 3);



add_action('user_register', 'ludou_save_phone_fields');


add_action('um_user_login', 'userLoginAction');


function my_login_redirect( $redirect_to, $request, $user ) {
    //is there a user to check?
    if ( isset( $user->roles ) && is_array( $user->roles ) ) {
        //check for admins
        if ( in_array( 'administrator', $user->roles ) ) {
            // redirect them to the default place
            return $redirect_to;
        } else {
            return home_url();
        }
    } else {
        return $redirect_to;
    }
}

add_filter( 'login_redirect', 'my_login_redirect', 10, 3 );


function userLoginAction($args) {
    echo 123;
    $result = um_set_redirect_url('http://www.baidu.com');

}

//add_filter('login_redirect', 'new_login_redirect', 10, 3);
//function new_login_redirect($args) {
//
//    return '/admin/index.php';
//}



// 发送手机短信验证码
add_action('wp_ajax_sendSms', 'sendSms');
add_action('wp_ajax_nopriv_sendSms', 'sendSms');

function sendSms() {
   $error = '';
   if ('POST' != $_SERVER['REQUEST_METHOD']) {
      header('Allow: POST');
      header('HTTP/1.1 405 Method Not Allowed');
      header('Content-Type: text/plain');
      $error = '非法访问';
   }

   if (empty($_SERVER['HTTP_X_REQUESTED_WITH']) || strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) != 'xmlhttprequest') {
      $error = '非法访问';
   }

   if(!check_ajax_referer( plugin_basename(__FILE__), 'token', false ))
      $error = '非法访问';

//   if (empty($_POST['captcha_code']) || empty($_SESSION['ludou_lcr_secretword'])) {
//      $error = '图片验证码错误';
//   }
//   else {
//      $secretword = explode("-", $_SESSION['ludou_lcr_secretword']);
//
//      if (time() - $secretword[1] > 120)
//         $error = '验证码已过期，请重新输入';
//      else if (trim(strtolower($_POST['captcha_code'])) != $secretword[0])
//         $error = '图片验证码错误';
//   }

   global $wpdb, $table_name;

   // 删除过期验证码
   $wpdb->query(
     $wpdb->prepare(
       "DELETE FROM `$table_name`
		 WHERE `time` < %s
		", (time() - 600)
     )
   );

   $phone = trim($_POST['phone']);
   if (!isPhone($phone)) {
      $error = '手机号不正确!';
   }
   else {
      $user_id = $wpdb->get_var($wpdb->prepare("SELECT `user_id` FROM `" . $wpdb->prefix . "usermeta` WHERE `meta_key` = 'phone' AND `meta_value` = %s;", $phone));

      if ($_POST['admin'] == 1) {
         global $current_user;
         if ($current_user->ID == $user_id)
            $error = '手机号没有修改';
      }
      elseif (!empty($user_id)) {
         $error = '该手机号已在本站注册过';
      }
   }

   // 上次发送短信的时间，防止短信攻击
   $time = $wpdb->get_var($wpdb->prepare("SELECT `time` FROM `$table_name` WHERE `phone` = %s;", $phone));

   if (!empty($time) && (time() - $time) < 61)
      $error = '获取验证码太频繁';

   if (empty($error)) {
      $code = generateCode();

      if (empty($time))
         $db = $wpdb->insert($table_name, array('phone' => $phone, 'code' => $code, 'time' => time()), array('%s', '%s', '%d'));
      else
         $db = $wpdb->update($table_name, array('code' => $code, 'time' => time()), array('phone' => $phone), array('%s', '%d'), array('%s'));

      if($db) {
         $send_status = send_sms($phone);
         $result['vHTML'] = ($send_status == 1) ? '' : '验证码发送失败';
      }
      else {
         $result['vHTML'] = '数据库错误！';
      }
   }
   else {
      $result['vHTML'] = $error;
   }

   $result['type'] = "success";
   $result = json_encode($result);
   echo $result;

   exit();
}

function ludou_phone_add_menu() {
   global $current_user;
   if ($current_user->roles[0] == get_option('default_role'))
      add_submenu_page('users.php', '修改手机号', '修改手机号', 'read', 'ludou-phone-menu', 'ludou_phone_add_menu_page');
}

function ludou_phone_add_menu_page() {
   global $current_user, $wpdb, $table_name;

   $old_phone = get_user_meta($current_user->ID, 'phone', true);

   if (!empty($_POST['check'])) {
      if(empty($_POST['token']) || !wp_verify_nonce( $_POST['token'], plugin_basename(__FILE__) ))
         wp_die('非法请求！');

      $errors = '';

      if (empty($_POST['captcha_code']) || empty($_SESSION['ludou_lcr_secretword'])) {
         $errors .= '图片验证码填写错误';
      }
      else {
         $secretword = explode("-", $_SESSION['ludou_lcr_secretword']);

         if (time() - $secretword[1] > 120)
            $errors .= '图片验证码已过期，请刷新页面后重新输入';
         else if (trim(strtolower($_POST['captcha_code'])) != $secretword[0])
            $errors .= '图片验证码填写错误';
      }

      unset($_SESSION['ludou_lcr_secretword']);

      $phone = trim($_POST['phone']);
      if (!isPhone($phone)) {
         $errors .= "<strong>错误</strong>：手机号不正确!!<br />";
         $_POST['phone'] = '';
         $_POST['code'] = '';
      }
      else {
         $phone_exist = $wpdb->get_var($wpdb->prepare("SELECT `user_id` FROM `" . $wpdb->prefix . "usermeta` WHERE `meta_key` = 'phone' AND `meta_value` = %s AND `user_id` != %d;", $phone, $current_user->ID));

         if ($phone == $old_phone) {
            $errors .= "<strong>错误</strong>：" . $phone . " 手机号没有修改<br />";
            $_POST['code'] = '';
         }
         elseif (!empty($phone_exist)) {
            $errors .= "<strong>错误</strong>：" . $phone . " 该手机号已被他人注册过<br />";
            $_POST['phone'] = '';
            $_POST['code'] = '';
         }
         else if (empty($_POST['code'])) {
            $errors .= "<strong>错误</strong>：请填写短信验证码<br />";
         }
         else {
             $verify_status = verify_code($phone, $_POST['code']);
//            $code = $wpdb->get_var($wpdb->prepare("SELECT `code` FROM `$table_name` WHERE `phone` = %s;", $phone));
//            if (empty($code)) {
//               $errors .= "<strong>错误</strong>：请先获取短信验证码<br />";
//               $_POST['code'] = '';
//            }
//            else if ($code != $_POST['code']) {
//               $errors .= "<strong>错误</strong>：短信验证码不正确<br />";
//               $_POST['code'] = '';
//            }
         }
      }

      if (empty($errors)) {
         $ok = update_user_meta($current_user->ID, 'phone', $phone);

         // 删除过期验证码
         $wpdb->query(
           $wpdb->prepare(
             "DELETE FROM `$table_name` WHERE `phone` = %s;", $phone
           )
         );
      }
   }
   ?>
   <script>
      var ajaxurl = "<?php echo admin_url('admin-ajax.php'); ?>", pic_no = "<?php echo constant("LCR_PLUGIN_URL"); ?>img/no.png", captcha = "<?php echo constant("LCR_PLUGIN_URL"); ?>captcha/captcha.php";
   </script>
   <script src="//cdn.bootcss.com/jquery/3.2.1/jquery.min.js"></script>
   <script src="<?php echo constant("LCR_PLUGIN_URL"); ?>check.js"></script>

   <div class="wrap" id="profile-page">
      <h1>修改手机号</h1>
      <?php
      if (!empty($errors)) {
         echo '<div class="error notice is-dismissible" id="message"><p>' . $errors . '</p>
           <button type="button" class="notice-dismiss"><span class="screen-reader-text">忽略此通知。</span></button></div>';
      }
      else if ($ok) {
         echo '<div id="message" class="updated notice is-dismissible">
		<p><strong>手机号已修改为 ' . $old_phone . '</strong></p>
		<button type="button" class="notice-dismiss"><span class="screen-reader-text">忽略此通知。</span></button></div>';
      }
      ?>
      <form id="your-profile" action="#" method="post">
         <p>
            <label for="phone">手机号 &nbsp;<span id="sendSmsBtnErr" style="color:#ff5c57;font-size: 12px;"></span> <br/>
               <input id="phone" class="regular-text ltr" type="text" size="15" value="<?php
               echo empty($_POST['phone']) ? $old_phone : $_POST['phone'];
               ?>" name="phone" autocomplete="off" />
            </label>
         </p>
         <p>
            <label for="CAPTCHA">图片验证码1111 &nbsp;<span id="captchaErr" style="color:#ff5c57;font-size: 12px;"></span> <br/>
               <input id="CAPTCHA" class="regular-text ltr" type="text" size="10" value="" name="captcha_code" autocomplete="off" />
            </label>
         </p>
         <p>
            <label>
               <img id="captcha_img" src="<?php echo constant("LCR_PLUGIN_URL"); ?>captcha/captcha.php" title="看不清?点击更换" alt="看不清?点击更换" onclick="document.getElementById('captcha_img').src = '<?php echo constant("LCR_PLUGIN_URL"); ?>captcha/captcha.php?' + Math.random();document.getElementById('CAPTCHA').focus();return false;" />
               看不清？<a href="javascript:void(0)" onclick="document.getElementById('captcha_img').src = '<?php echo constant("LCR_PLUGIN_URL"); ?>captcha/captcha.php?' + Math.random();document.getElementById('CAPTCHA').focus();return false;">点击更换11</a>
            </label>
         </p>
         <p>
            <label for="code">短信验证码 <br/>
               <input id="code" class="regular-text ltr" type="text" size="4" value="<?php echo empty($_POST['code']) ? '' : $_POST['code']; ?>" name="code" />
               &nbsp;<input id="sendSmsBtn" type="button" value="获取短信验证码" class="button button-secondary" />
            </label>
         </p>
         <input type="hidden" name="check" id="admin_check" value="1" />
         <input type="hidden" name="token" value="<?php echo wp_create_nonce( plugin_basename(__FILE__) ); ?>">
         <p class="submit"><input type="submit" name="submit" id="submit" class="button button-primary" value="更新手机号"></p>
      </form>
   </div>
   <?php
}

add_action('admin_menu', 'ludou_phone_add_menu');
