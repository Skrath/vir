// This is the main ajax handler. All ajax calls should be going
// through this function.
//
// data_callback will be called on each individual returned item,
// while callback will be called on the parent data.
//
// Errors are automatically handled via the insert_error() function.
function ajax_submit(params, data_callback, callback) {
    var url = '../process/ajax-post.php';

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
                               insert_error(data.output.error_message);
                           }
                       } else {
                           insert_error(data.error_messages);
                       }
                   });
}

function insert_error(error_string) {
    var error_box = jQuery('div#error_box');
    var error_list = jQuery('div#error_box ul#error_list');
    var error = jQuery('<li>' + error_string + '</li>').css('display', 'none');

    if (error_box.css('display') == 'none') {
        error_box.slideDown();
    }

    error_list.append(error);
    error.slideDown();
}
