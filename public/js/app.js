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
            console.log(response);
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

            jQuery("input[name='" + item.field + "']").closest('.form-group').append('<div class="error label errors-block label-danger">' + item.message + '</div>');
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
    var inputPass = event.target.parentNode.childNodes[1];
    inputPass.type = 'text';
});
jQuery('form .eye').mouseup(function (event) {
    var inputPass = event.target.parentNode.childNodes[1];
    inputPass.type = 'password';
});

/**
 * Select img file for user profile
 */
$(".center_img").on('click',function(){
    $("input[name='photo']").click();
});

/**
 * Upload user photo
 */

$("input[name='photo']").change(function () {
    var file = event.target.files;
    var id = event.target.id;

    var data = new FormData();

    var error = 0;

    if(!file[0].type.match('image.*')) {
        alert('Images only. Select another file');
        error = 1;
    } else if(file.size > 1048576) {
        alert('Too large Payload ( < 1 Mb). Select another file');
        error = 1;
    } else {
        data.append('image', file[0], file[0].name);
    }

    if(!error) {
        $.ajax({
            url: "/index/photo/?user=" + id,
            type: "POST",
            data: data,
            processData: false,
            contentType: false,
            success: function (res) {
                $('.center_img').attr('src', res);
            }
        });
    }
});

/**
 * Drog and drop files
 */
$(function(){
    Dropzone.options.myAwesomeDropzone = {
        thumbnailWidth: 80,
        thumbnailHeight: 80,
        maxFilesize: 200000,
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
                $('.add form input[type="hidden"]').attr('value', file.name);

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

    $('.typeahead').typeahead({
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
