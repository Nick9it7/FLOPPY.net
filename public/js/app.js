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
    var formData = new FormData;

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
            } else if(!($.isEmptyObject(response.redirect))) {
                Validate.redirect(response.redirect);
            } else if (!($.isEmptyObject(response.note))) {
                Validate.note(response.note)
            } else if (response.subscribe) {
                Validate.subscribe(response.subscribe)
            } else if (response.unsubscribe) {
                Validate.unsubscribe(response.unsubscribe)
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
    },

    note: function (note) {
        $('.added_file_block h1').empty();
        $('.added_file_block').prepend('' +
            '<div class="row added">' +
            '   <div class="col-md-1 text-center first">' +
            '       <i class="glyphicon glyphicon-floppy-saved"></i>' +
            '   </div>' +
            '   <div class="col-md-10">' +
            '       <div class="description">' + note.text + '</div>' +
            '   </div>' +
            '   <div class="col-md-1 text-center first">' +
            '       <form action="/file/download" method="post">' +
            '           <input type="hidden" name="fileName" value="' + note.file + '">' +
            '           <button type="submit" class="btn btn-default">' +
            '               <i class="glyphicon glyphicon-save"></i>' +
            '           </button>' +
            '       </form>' +
            '   </div>' +
            '</div>'
        );
        $('.description input[name="desc"]').val('');
    },

    subscribe: function (subscribe) {
        $('.buttom button span').text('Unsubscribe');
        $('.buttom').attr('action', '/anotheruser/unsubscribe');
    },

    unsubscribe: function (unsubscribe) {
        $('.buttom button span').text('Subscribe');
        $('.buttom').attr('action', '/anotheruser/Subscribe');
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


$(document).ready(function () {
    /**
     * Upload user photo
     */
    $("input[name='photo']").change(function () {
        var file = event.target.files;
        var id = event.target.id;

        var data = new FormData();

        var error = 0;

        if (!file[0].type.match('image.*')) {
            alert('Тільки зображення. Виберіть інший файл');
            error = 1;
        } else if (file[0].size > 7048810) {
            alert('Розмір файла перевищує 2 Mb. Виберіть інший файл');
            error = 1;
        } else data.append('image', file[0], file[0].name);


        if (!error) {
            $.ajax({
                url: "/index/photo",
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
     * Show subscribers list
     */
    $('.subscription').on('click', function () {
        $.ajax({
            url: "/index/subscribelist",
            type: "POST",
            processData: false,
            contentType: false,
            success: function (res) {
                $('#subscribe').empty();
                if (res.users.length > 0) {
                    $.each(res.users, function (index, value) {
                        $('#subscribe').append('' +
                            '<div class="row">' +
                            '   <div class="col-xs-3">' +
                            '       <img src="' + value['photo'] +'" alt="" class="img-rounded center_img sub" >' +
                            '   </div>' +
                            '   <div class="col-xs-3">' +
                            '       <p>' + value['name'] +'</p>' +
                            '   </div>' +
                            '   <div class="col-xs-2 col-xs-offset-3">' +
                            '       <form method="post" action="/anotheruser/show">' +
                            '           <input name="name" type="hidden" value="' + value['name'] + '">' +
                            '           <button class="btn btn-primary" type="submit">Перейти</button>' +
                            '       </form>' +
                            '   </div>' +
                            '</div>' +
                            '<hr>'
                        );
                    });
                } else {
                    $('#subscribe').append('' +
                        '<div class="row" style="text-align: center">' +
                        '   <span style="font-size: large">Ви ні на кого не підписані</span>' +
                        '</div>' +
                        '<hr>'
                    );
                }
            }
        });
    });
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
                    '<div class="search-message">',
                    'Збігів не знайдено',
                    '</div>'
                ].join('\n'),
                suggestion: function(data) {
                    return '<div id="fined" class="search-message"><strong>' + data + '</strong> </div>';
                }
            }
        }
    );
});
