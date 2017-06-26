var wait = 60; // 获取验证码短信时间间隔

jQuery(document).ready(function ($) {

   function countdown() {
      if (wait > 0) {
         $('#sendSmsBtn').val(wait + '秒后重新获取验证码');
         wait--;
         setTimeout(countdown, 1000);
      } else {
         document.getElementById('captcha_img').src = captcha + '?v=' + Math.random();
         $("#CAPTCHA").val('');
         $("#CAPTCHA").focus();
         $('#sendSmsBtn').val('获取短信验证码').attr("disabled", false).fadeTo("slow", 1);
         wait = 60;
      }
   }

   $('#sendSmsBtn').click(function () {

      var phone = $("input[name=phone]").val();
      if (phone == '' || !phone.match(/^(((13[0-9]{1})|(15[0-9]{1})|(17[0-9]{1})|(18[0-9]{1}))+\d{8})$/)) {
         $("#sendSmsBtnErr").html('<img src="' + pic_no + '" style="vertical-align:middle;" alt=""/> ' + '手机号不正确').slideDown();
         $("#phone").focus();
         setTimeout(function () {
            $("#sendSmsBtnErr").slideUp()
         }, 3000);
         return;
      }

      var captcha_code = $("input[name=captcha_code]").val();
      var token = $("input[name=token]").val();
      if (captcha_code == '' || captcha_code.length != 5) {
         $("#captchaErr").html('<img src="' + pic_no + '" style="vertical-align:middle;" alt=""/> ' + '填写错误').slideDown();
         $("#CAPTCHA").focus();
         setTimeout(function () {
            $("#captchaErr").slideUp()
         }, 3000);
         return;
      }

      var admin = 0;
      if ($("#admin_check").length)
         admin = 1;

      $.ajax({
         type: "post",
         dataType: "json",
         url: ajaxurl,
         data: {
            action: "sendSms",
            phone: phone,
            captcha_code: captcha_code,
            token: token,
            admin: admin
         },
         success: function (response) {
            if (response.type == "success") {
               if (response.vHTML == '图片验证码错误') {
                  $("#captchaErr").html('<img src="' + pic_no + '" style="vertical-align:middle;" alt="" /> 验证码错误').slideDown();
                  $("#CAPTCHA").focus();
                  setTimeout(function () {
                     $("#captchaErr").slideUp()
                  }, 3000);
               } else if (response.vHTML == '验证码已过期，请重新输入') {
                  document.getElementById('captcha_img').src = captcha + '?v=' + Math.random();
                  $("#CAPTCHA").val('');
                  $("#CAPTCHA").focus();
                  $("#captchaErr").html('<img src="' + pic_no + '" style="vertical-align:middle;" alt="" /> 验证码已过期，请重新输入').slideDown();
                  setTimeout(function () {
                     $("#captchaErr").slideUp()
                  }, 3000);
               } else if (response.vHTML != '') {
                  $("#sendSmsBtnErr").html('<img src="' + pic_no + '" style="vertical-align:middle;" alt=""/> ' + response.vHTML).slideDown();
                  $("#phone").focus();
                  setTimeout(function () {
                     $("#sendSmsBtnErr").slideUp()
                  }, 3000);
               } else {
                  $('#sendSmsBtn').attr("disabled", true).fadeTo("slow", 0.5);
                  countdown();
               }
            }
         }
      });


   });

});