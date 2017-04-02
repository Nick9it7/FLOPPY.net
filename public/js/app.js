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

$(function(){
    Dropzone.options.myAwesomeDropzone = {
        maxFilesize: 20000,
        addRemoveLinks: true,
        init:function(){
            var self = this;

            self.options.addRemoveLinks = true;
            self.options.dictRemoveFile = "Видалити";

            self.on("addedfile", function (file) {
                file.previewElement.querySelector(".start").onclick = function () {
                    myDropzone.enqueueFile(file);
                    //console.log('new file added ', file);
                }
            });

            self.on("sending", function (file) {
                //console.log('upload started', file);
                //$('.meter').show();
            });

            // File upload Progress
            self.on("totaluploadprogress", function (progress) {
                //console.log("progress ", progress);
                //$('.roller').width(progress + '%');
            });

            self.on("queuecomplete", function (progress) {
                //$('.meter').delay(999).slideUp(999);
            });

            // On removing file
            self.on("removedfile", function (file) {
                //myDropzone.removeFile(file);
            });
        }
    };
})
/**
var previewNode = document.querySelector("#template");
previewNode.id = "";
var previewTemplate = previewNode.parentNode.innerHTML;
previewNode.parentNode.removeChild(previewNode);

var myDropzone = new Dropzone(document.body, { // Make the whole body a dropzone
    url: "/file/upload", // Set the url
    thumbnailWidth: 80,
    thumbnailHeight: 80,
    parallelUploads: 20,
    previewTemplate: previewTemplate,
    dictDefaultMessage: 'drag and drop',
    autoProcessQueue: false,
    previewsContainer: "#previews",
    clickable: "#upload"
});

myDropzone.on("addedfile", function(file) {
    // Hookup the start button
    file.previewElement.querySelector(".start").onclick = function() { myDropzone.enqueueFile(file); };
});

// Update the total progress bar
myDropzone.on("totaluploadprogress", function(progress) {
    document.querySelector("#total-progress .progress-bar").style.width = progress + "%";
});

myDropzone.on("sending", function(file) {
    // Show the total progress bar when upload starts
    document.querySelector("#total-progress").style.opacity = "1";
    // And disable the start button
    file.previewElement.querySelector(".start").setAttribute("disabled", "disabled");
});

// Hide the total progress bar when nothing's uploading anymore
myDropzone.on("queuecomplete", function(progress) {
    document.querySelector("#total-progress").style.opacity = "0";
});

document.querySelector(".modal-footer .start").onclick = function() {
    myDropzone.enqueueFiles(myDropzone.getFilesWithStatus(Dropzone.ADDED));
};
document.querySelector(".modal-footer .cancel").onclick = function() {
    myDropzone.removeAllFiles(true);
};
/*************/

$(document).ready(function (f) {
    var substringMatcher = function(strs) {
        return function findMatches(q, cb) {
            var matches, substringRegex;

            // an array that will be populated with substring matches
            matches = [];

            // regex used to determine if a string contains the substring `q`
            substrRegex = new RegExp(q, 'i');

            // iterate through the pool of strings and for any string that
            // contains the substring `q`, add it to the `matches` array
            $.each(strs, function(i, str) {
                if (substrRegex.test(str)) {
                    matches.push(str);
                }
            });

            cb(matches);
        };
    };

    var states = ['Alabama', 'Alaska', 'Arizona', 'Arkansas', 'California',
        'Colorado', 'Connecticut', 'Delaware', 'Florida', 'Georgia', 'Hawaii',
        'Idaho', 'Illinois', 'Indiana', 'Iowa', 'Kansas', 'Kentucky', 'Louisiana',
        'Maine', 'Maryland', 'Massachusetts', 'Michigan', 'Minnesota',
        'Mississippi', 'Missouri', 'Montana', 'Nebraska', 'Nevada', 'New Hampshire',
        'New Jersey', 'New Mexico', 'New York', 'North Carolina', 'North Dakota',
        'Ohio', 'Oklahoma', 'Oregon', 'Pennsylvania', 'Rhode Island',
        'South Carolina', 'South Dakota', 'Tennessee', 'Texas', 'Utah', 'Vermont',
        'Virginia', 'Washington', 'West Virginia', 'Wisconsin', 'Wyoming'
    ];

    $('#the-basics .typeahead').typeahead({
            hint: true,
            highlight: true,
            minLength: 1
        },
        {
            name: 'states',
            source: substringMatcher(states)
        });
});
