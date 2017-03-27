// Main application js file

/**
 * Submit form with ajax
 */
jQuery(document).on('submit', 'form.async', function (event) {
    ajaxFormSubmit(event);
    return false;
});

var ajaxFormSubmit = function (event) {
    event.defaultPrevented = true;

    var form  = jQuery(event.target);
    var formData       = new FormData;

    var serializedForm = form.serializeArray();

    for (var i in serializedForm) {
        var input = serializedForm[i];
        formData.append(input.name, input.value);
    }


    var callback = function () {};
    if (jQuery(event.target).data('controller') !== undefined && jQuery(event.target).data('action') !== undefined) {
        var dispatcherResource = getDispatcherResource(
            jQuery(event.target).data('controller'),
            jQuery(event.target).data('action')
        );
        callback = dispatcherResource.action;

    } else {
        callback = function (response) {
            console.log(response.error);
            if (!($.isEmptyObject(response.error))) {
                Validate.showErrorsMessages(response.error);
            } else {
                Validate.redirect(response.redirect);
            }
        }
    }
    jQuery.ajax(
        {
            url: form.attr('action'),
            data: formData,
            processData: false,
            contentType: false,
            method: 'post',
            success: callback
        }
    );
};
/**
 * @type {{showErrorsMassages: Validate.showErrorsMessages, redirect: Validate.redirect}}
 */
var Validate = {

    /**
     * show errors block under form fields
     * @param errors
     */
    showErrorsMessages: function(errors) {
        jQuery('.errors-block').remove();

        $.each(errors, function (index) {

            item = errors[index];

            if (jQuery("input[name='" + item.field + "']").closest('.form-group').length === 0) {
                jQuery("input[name='" + item.field + "']").closest('.input-group').after('<div class="label errors-block label-danger" style="padding-top: -15px;">' + item.message + '</div>');
            }

            jQuery("input[name='" + item.field + "']").closest('.form-group').append('<div class="label errors-block label-danger">' + item.message + '</div>');
            jQuery("select[name='" + item.field + "']").after('<span class="label errors-block label-danger">' + item.message + '</span>');
        });
    },

    redirect: function (url) {
        if (url !== undefined) {
            window.location.assign(url);
        }
    }
};