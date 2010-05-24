$(document).ready(function() {  
  $('optgroup[label=Global] option').each(function() {
    var oai_wrap = $(this).parent().parent().parent().parent('.oai-wrap');
    var dc_forms = oai_wrap.children('.form-item');
    
    
    //oai_wrap.children('').append($('<a href="#" class="global-close">x</a>'));
    
    $(this).after($('<a class="global-edit" href="#">edit</a>').click(function(e) {
      e.preventDefault();
      var form = dc_forms.children('.' + $(this).prev().html());
      
      $('.global-field').hide();
      $('.global-field').parent().hide();
      form.parent().css({
        'position':'absolute', 
        'top': 23, 
        'width': '100%',
        'height': 30,
        'background-color': '#888888'
      });
      form.parent().show();
      form.show()
    }));
  });
  
  
  
})