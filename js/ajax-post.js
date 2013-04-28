// This is the main ajax handler. All ajax calls should be going
// through this function.
//
// data_callback will be called on each individual returned item,
// while callback will be called on the parent data.
//
// Errors are automatically handled via the insertError() function.
function ajax_submit(params, data_callback, callback) {
    var url = 'ajax/public.php';

    jQuery.getJSON(url, params,
                   function (data) {
                       if (data.valid) {
                           if (data.output.success) {
                               jQuery(data.output.data).each(function(index, item) {
                                   if (typeof data_callback == 'function') {
                                       data_callback(item);
                                   }
                               });
                               if (typeof callback == 'function') {
                                   callback(data);
                               }
                           } else {
                               insertError(data.output.error_message);
                           }
                       } else {
                           insertError(data.error_messages);
                       }
                   });
}

function insertError(errorString) {

    var newPosition = {
        my: "right bottom",
        at: "right bottom",
        of: window,
    }

    $(messageList).each(function(index) {
        newPosition = {
            my: "right bottom",
            at: "right top",
            of: this.parent()
        };
    });

    var errorDialog =
        $('<div title="Error" class="dialog_message"><li>' + errorString + '</li></div>');

    $( errorDialog ).dialog({
        dialogClass: "dialog_message",
        autoOpen: false,
        draggable: false,
        resizable: false,
        minHeight: 100,
        position: newPosition,
        close: function(event, ui) {
            removeMessage(errorDialog);
        },
        show: {
            effect: "slide",
            direction: "right",
            duration: 250
        },
        hide: {
            effect: "fade",
            duration: 500
        }
    });

    errorDialog.dialog("open");

    messageList.push(errorDialog);
}

function removeMessage(messageObject) {
    messageList.splice(messageList.lastIndexOf(messageObject), 1);
    messageObject.dialog("destroy");
    messageObject.detach();
}
