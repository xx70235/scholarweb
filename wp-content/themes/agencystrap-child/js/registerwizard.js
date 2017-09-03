jQuery(function($){

    jQuery.validator.addMethod("isMobile", function(value, element) {
        var length = value.length;
        var mobile = /^(13[0-9]{9})|(18[0-9]{9})|(14[0-9]{9})|(17[0-9]{9})|(15[0-9]{9})$/;
        return this.optional(element) || (length == 11 && mobile.test(value));
    }, "请正确填写您的手机号码"),

        jQuery.validator.addMethod("strongPassword", function(value, element) {
            var length = value.length;
            var strong = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)[a-zA-Z\d]{8,}$/;
            return this.optional(element) || (length >8 && strong.test(value));
        }, "密码必须包含至少一个大写字母，至少一个小写字母和数字，并且不少于9位"),


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
            // return true;
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
        if($("form[name='form']").valid())
        {
            var institution = jQuery("#institution").val();
            jQuery("#institution-30").val(institution);

            var director = jQuery("#director").val();
            jQuery("#director-30").val(director);

            var director_phone = jQuery("#director_phone").val();
            jQuery("#director_phone-30").val(director_phone);

            var director_email = jQuery("#director_email").val();
            jQuery("#director_email-30").val(director_email);

            var publisher = jQuery("#publisher").val();
            jQuery("#publisher-30").val(publisher);

            var publisher_phone = jQuery("#publisher_phone").val();
            jQuery("#publisher_phone-30").val(publisher_phone);

            var publisher_email = jQuery("#publisher_email").val();
            jQuery("#publisher_email-30").val(publisher_email);

            var address = jQuery("#address").val();
            jQuery("#address-30").val(address);

            var website = jQuery("#website").val();
            jQuery("#website-30").val(website);

            return true;
        }
        else{
            return false;
        }
    },

    onFinished: function (event, currentIndex)
    {

        $("form[name!='form']").submit();

    }


}),
    $("form[name!='form']").validate({
        errorPlacement: function errorPlacement(error, element) { element.before(error); },
        rules: {
            "user_login-30":"required",
            "user_password-30":{
                required:true,

                strongPassword:true
            },

            "confirm_user_password-30": {
                required:true,
                equalTo: "#user_password-30"

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
            publisher_email:{
                required:true,
                email: true
             },
            publisher:"required",
            publisher_phone:{
                required:true,
                isMobile:true
            },
            director_email:{
                required:true,
                email: true
            },
            address:"required",
            website:"required"
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

    jQuery("div[data-key='institution']").hide();
    jQuery("div[data-key='director']").hide();
    jQuery("div[data-key='director_phone']").hide();
    jQuery("div[data-key='director_email']").hide();
    jQuery("div[data-key='publisher']").hide();
    jQuery("div[data-key='publisher_phone']").hide();
    jQuery("div[data-key='publisher_email']").hide();
    jQuery("div[data-key='address']").hide();
    jQuery("div[data-key='website']").hide();

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
    jQuery("#director_phone").attr("placeholder","填写招生部门/学术团队负责人联系电话");
    jQuery("#director_email").attr("placeholder","填写招生部门/学术团队负责人邮箱，请使用学术机构后缀邮箱，不接受公共邮箱");
    jQuery("#director_email").removeAttr("max");
    jQuery("#director_email").removeAttr("min");
    jQuery("#publisher_phone").removeAttr("max");
    jQuery("#publisher_phone").removeAttr("min");
    jQuery("#institution").removeAttr("max");
    jQuery("#institution").removeAttr("min");
    jQuery("#institution").attr("placeholder","例如xx大学xx实验室/博士后流动站/教授课题组");

    jQuery("#director").removeAttr("max");
    jQuery("#director").removeAttr("min");
    jQuery("#director").attr("placeholder","填写招生部门/学术团队负责人真实姓名");
    jQuery("#publisher").removeAttr("max");
    jQuery("#publisher").removeAttr("min");
    jQuery("#publisher").attr("placeholder","填写信息发布人真实姓名");

    jQuery("#address").removeAttr("max");
    jQuery("#address").removeAttr("min");
    jQuery("#address").attr("placeholder","填写信息发布人联系地址");

    jQuery("#website").removeAttr("max");
    jQuery("#website").removeAttr("min");
    jQuery("#website").attr("placeholder","填写机构或团队或负责人个人主页，未填写将审核不通过");

    jQuery(".req-text").hide();

});



