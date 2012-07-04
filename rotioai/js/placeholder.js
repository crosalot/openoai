$(document).ready(function() {
  var wrapper = $('#edit-query-block-wrapper');
  var label = $('label', wrapper);
  var input = $('input', wrapper);
  var check = function() {
    if (input.val() == '') {
      label.show();
    } else {
      label.hide();
    }
  };
  if (Drupal.settings.rotioai.keywordText != '') {
    // Change text
    label.html(Drupal.settings.rotioai.keywordText);
    // Bind event.
    input.focus(function(e) {
      label.hide();
    }).blur(function(e) {
      check();
    });

    // Initial display.
    check();
  }
});
