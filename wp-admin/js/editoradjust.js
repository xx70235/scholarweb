jQuery(document).ready(function() {
    jQuery(".hndle ui-sortable-handle").add("<span class=\"required\">*</span>");
    });

jQuery(function($){
    $("form[name='post']").validate({
        errorPlacement: function errorPlacement(error, element) { element.before(error); },
        rules: {
            "title":"required",
            "dp1505703301765":"required",
            "acf-field-details":"required",
            "dp1505703301766":"required",
            "acf-field-institute":"required",
            "new-tag-job_title":"required",
            "new-tag-location":"required",
            "new-tag-keywords":"required",
            "new-tag-first_level_discipline":"required",
            "new-tag-second_level_discipline":"required",


        }
    });
}