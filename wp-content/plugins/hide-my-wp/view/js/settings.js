jQuery(document).ready(function () {

    var elems = Array.prototype.slice.call(document.querySelectorAll('.js-switch'));

    elems.forEach(function (html) {
        new Switchery(html, {color: 'green'});
    });

    jQuery('#hmw_settings').find('select[name=hmw_mode]').on('change', function () {
        jQuery('#hmw_settings').find('.tab-panel').hide();
        jQuery('#hmw_settings').find('.hmw_' + jQuery(this).val()).show();
    });

    jQuery('#hmw_settings').find('#hmw_support button').on('click', function () {
        var form = jQuery('#hmw_settings').find('#hmw_support');
        if ( form.find("input#hmw_email").val() == ''){
            form.find("input#hmw_email").fadeIn(100).fadeOut(100).fadeIn(100).fadeOut(100).fadeIn(100);
            return;
        }
        if ( form.find("textarea#hmw_question").val() == ''){
            form.find("textarea#hmw_question").fadeIn(100).fadeOut(100).fadeIn(100).fadeOut(100).fadeIn(100);
            return;
        }
        jQuery.post(
            form.attr('action'),
            {
                action: 'hmw_support',
                email: form.find("input#hmw_email").val(),
                message: form.find("textarea#hmw_question").val(),
                hmw_nonce: form.attr('nonce')
            }
        ).done(function (response) {
            if (typeof response.success !== 'undefined') {
                form.find("#hmw_success").show();
                form.find(".form-group").hide();

            } else {
                form.find("#hmw_error").show();
                form.find(".form-group").hide();
            }
        }).fail(function (response) {
            form.find("#hmw_error").show();
            form.find(".form-group").hide();
        }, 'json');
    });


});

