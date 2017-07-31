jQuery(function($){

    jQuery.validator.addMethod("isMobile", function(value, element) {
        var length = value.length;
        var mobile = /^(13[0-9]{9})|(18[0-9]{9})|(14[0-9]{9})|(17[0-9]{9})|(15[0-9]{9})$/;
        return this.optional(element) || (length == 11 && mobile.test(value));
    }, "请正确填写您的手机号码"),

    $("#register-form").steps({
    headerTag: "h3",
    bodyTag: "section",
    transitionEffect: "slideLeft",
    autoFocus: true,

    labels: {

        finish: "提交",
        next: "下一步",
        previous: "上一步"

    },
    onStepChanging: function (event, currentIndex, newIndex)
    {
        $("form").validate().settings.ignore = ":disabled,:hidden";
        if (currentIndex === 0)
        {
            return $("form[name!='form']").valid();
        }
        if (currentIndex > newIndex)
        {
            return true;
        }

    },

    onStepChanged: function (event, currentIndex, priorIndex)
    {
        if (currentIndex === 1)
        {
            var user_login = jQuery("#user_login-30").val();
            jQuery("#user_login").val(user_login);
            var email =  jQuery("#user_email-30").val();
            jQuery("#user_email").val(email);
            var password = jQuery("#user_password-30").val();
            jQuery("#password").val(password);
            jQuery("#publisher_email").val(email);
            var password = jQuery("#password").val();
            jQuery("#password").val(password);
            var phone = jQuery("#phone").val();
            jQuery("#publisher_phone").val(phone);

        }

    },

    onFinishing: function (event, currentIndex)
    {
        $("form[name='form']").validate().settings.ignore = ":disabled,:hidden";
        return $("form[name='form']").valid();
    },

    onFinished: function (event, currentIndex)
    {

        $("form[name!='form']").submit();
         // alert("Submitted!");


    }


}),
    $("form[name!='form']").validate({
        errorPlacement: function errorPlacement(error, element) { element.before(error); },
        rules: {
            "user_login-30":"required",
            "user_password-30":{
                required:true,
                minlength:8
            },

            "confirm_user_password-30": {
                required:true,
                equalTo: "#user_password-30",
                minlength:8
            },
            "user_email-30":{
                required:true,
                email: true
            },
            phone:{
                required:true,
                isMobile:true
            },
            code:"required",
        }
    }),

    $("form[name='form']").validate({
        errorPlacement: function errorPlacement(error, element) { element.before(error); },
        rules: {
            mainAcceptIpt:"required",
            institution:"required",
            director:"required",
            director_phone:{
                required:true,
                isMobile:true
            },
            publisher:"required",
            publisher_phone:{
                required:true,
                isMobile:true
            }
        }
    });

});

jQuery(document).ready(function(){
    jQuery(".um-button").remove();
    jQuery("#user_login").hide();
    jQuery("#user_email").hide();
    jQuery("#password").hide();
    jQuery("label[for='user_login']").hide();
    jQuery("label[for='user_email']").hide();
    jQuery("label[for='password']").hide();

    jQuery("input[type='text']").css("width","100%");
    jQuery("input[type='text']").css("height","40px");
    jQuery("input[type='email']").css("width","100%");
    jQuery("input[type='email']").css("height","40px");
    jQuery("input[type='submit']").remove();
    jQuery("fieldset").css("margin-top","40px");
    jQuery("legend").remove();
    jQuery(".um-row").removeAttr("style");
    jQuery(".div_text").css("width","100%");
    jQuery("fieldset").css("margin","0px");

    jQuery(".wizard>.actions").css("background","#fff");
    jQuery(".wizard>.actions").css("min-height","0px");

    jQuery("#director_phone").removeAttr("max");
    jQuery("#director_phone").removeAttr("min");
    jQuery("#publisher_phone").removeAttr("max");
    jQuery("#publisher_phone").removeAttr("min");
    jQuery("#institution").removeAttr("max");
    jQuery("#institution").removeAttr("min");
    jQuery("#director").removeAttr("max");
    jQuery("#director").removeAttr("min");
    jQuery("#publisher").removeAttr("max");
    jQuery("#publisher").removeAttr("min");
    jQuery("#address").removeAttr("max");
    jQuery("#address").removeAttr("min");
    jQuery("#website").removeAttr("max");
    jQuery("#website").removeAttr("min");
    jQuery(".req-text").hide();

});



