$(document).ready( function() {
  $('.plus1-vote-class').live('submit', function(e) {
    e.preventDefault();
    var path = $(this).attr('action').split('?');
    var arg = path[1].split('&');
    var token = '';
    for (i = 0; i < arg.length; i++) {
      if (arg[i].indexOf('token') != -1) {
        token = arg[i];
      }
    }
    $.get(path[0] + '?' + token, function(data) {
      current_category.change(); // Refresh result
    });
  });
});
