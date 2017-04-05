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

/**
 * Show/hide password
 */
jQuery('form .eye').mousedown(function (event) {
   $('input[name="password"]').attr('type', 'text');
});
jQuery('form .eye').mouseup(function (event) {
    $('input[name="password"]').attr('type', 'password');
});

/**
 * Drog and drop files
 */
$(function(){
    //var drop = $('#template-preview').html();
    Dropzone.options.myAwesomeDropzone = {
        thumbnailWidth: 80,
        thumbnailHeight: 80,
        maxFilesize: 20000,
        //previewTemplate: drop,
        //previewsContainer: '#template-preview',
        autoProcessQueue: false,
        maxFiles: 1,
        uploadMultiple: false,
        init:function(){
            var self = this;

            self.options.addRemoveLinks = true;
            self.options.dictRemoveFile = "Видалити";
            self.options.dictCancelUpload = "Відмінити";
            self.options.dictFileTooBig = "Файл дуже великий";
            self.options.dictMaxFilesExceeded = "Не можна загружати більше файлів";
            self.options.dictDefaultMessage = "Перетягніть сюди файл";
            self.options.dictCancelUploadConfirmation = "Ви впевненні, що бажаєте відмінити загрузку?";

            var submitButton = document.querySelector(".start");

            submitButton.addEventListener("click", function() {
                self.processQueue();
            });
            self.on("addedfile", function (file) {

            });

            self.on("sending", function (file) {


            });

            self.on("success", function (file) {
                jQuery('#myModal').modal('toggle');
                $('#hiddenFile').attr('value', file.name);
            });

            self.on("totaluploadprogress", function (progress) {

            });

            self.on("removedfile", function (file) {
                $.ajax({
                    url: '/file/delete',
                    method: 'post',
                    data: {
                        fileName: file.name
                    },
                    success: function () {
                        $('#hiddenFile').attr('value', '');
                    }
                });
            });
        }
    };
});

/**
 * Search users
 */
$(document).ready(function () {
    var substringMatcher = function(strs) {
        return function findMatches(q, cb) {
            var matches, substringRegex;

            matches = [];

            substrRegex = new RegExp(q, 'i');

            $.each(strs, function(i, str) {
                if (substrRegex.test(str)) {
                    matches.push(str);
                }
            });

            cb(matches);
        };
    };

    var user = {};


    $('.typeahead').on('focus', function (e) {
        $.ajax({
            url: '/index/search',
            success: function (data) {
                $.each(data, function (index) {
                    user[index] = data[index];
                });
            }
        });
    });

    $('#the-basics .typeahead').typeahead({
            hint: true,
            highlight: true,
            minLength: 1
        },
        {
            name: 'user',
            source: substringMatcher(user),
            templates: {
                empty: [
                    '<div class="empty-message">',
                    'Збігів не знайдено',
                    '</div>'
                ].join('\n'),
                suggestion: function(data) {
                    return '<div><strong>' + data + '</strong> </div>';
                }
            }

        }
    )
});
