$(document).ready(function() {
	/**
	 * Add date picker
	 */
	$('#edit-advanced-keywords-from-date').datepicker({dateFormat: 'yy-mm-dd'});
	$('#edit-advanced-keywords-to-date').datepicker({dateFormat: 'yy-mm-dd'});

	/**
	 * Toggle advanced search fieldset
	 */
	$('#advanced-search legend a').click(function(e) {
		if ($('#advanced-search').hasClass('advanced')) {
			rotioai_form_reset();
			//$('#edit-query').removeAttr("disabled");
			$('#edit-query').focus();
			$('#advanced-search').removeClass('advanced');
		} else {
			//$('#edit-query').val('');
			//$('#edit-query').attr('disabled', 'disabled');
			$('#advanced-search').addClass('advanced');
		}
	});

	/**
	 * Ajax inline search
	 */
	$('#rotioai-form').submit(function(e) {
		e.preventDefault();
		fetch(0, null, null, true);
	});

	var get_fields = function() {
	  var fields = {};
    $('#advanced-search .form-item').each(function (i, item) {
      var value = $(item).children('input').val();
      if (value) {
	      fields[$(item).next().html()] = value;
	    }
	  });
	  return fields;
	}

  var query_link = '';
  var sort_link = '';
  var subject = '';
  var type = '';

	var fetch = function(page, query_link, sort_link, is_form_submit, still) {


    if (page == null) {
			page = 0;
		}
		
		var query = jQuery.trim($('#edit-query').val());
		var quick = jQuery.trim($('#edit-quick').val());
		
		var req = {
		  'page': page,
		  'is_form_submit': is_form_submit,
		  'query': query,
		  'quick': quick
		};
		
    if (query_link) {
		  var q = query_link.split('=');
		  req[q[0]] = q[1];
		  //rotioai_form_reset()
		  $('#edit-advanced-keywords-'+q[0]).val(q[1]);
		  $('#edit-query').val('');
		}
    if (sort_link) {
      req['sort'] = sort_link;
		}

		var fields = get_fields();
		jQuery.extend(req, fields);
		$('#roti-loading').show();
		if (!still && (!jQuery.isEmptyObject(fields) || query)) {
				window.location.hash = '#rotisearch-box';
		}
		$('#rotisearch-box').load('rotisearch/result #rotisearch-result', req, function() {
				$('#rotisearch-result ul.pager li a').click(function(e) {
					e.preventDefault();
					var page = String(this).match(/page=(\d+)/);
					if (page != null) {
						page = page[1];
					} else {
						page = 0;
					}
					fetch(page);
				});

				$('a.roti-query').live('click', function(e) {
					e.preventDefault();
          query_link = $(this).attr('href');
					fetch(0, query_link, sort_link);
				});

				// Handle fieldset behavior
				if (!jQuery.isEmptyObject(fields)) {
					//$('#edit-query').val('');
					/*
					$('#edit-advanced-keywords-title').val(title);
					$('#edit-advanced-keywords-body').val(body);
					$('#edit-advanced-keywords-tag').val(decodeURI(tag));
					$('#edit-advanced-keywords-from-date').val(from);
					$('#edit-advanced-keywords-to-date').val(to);
					*/

					//$('#edit-query').attr('disabled', 'disabled');
					$('.fieldset-wrapper').attr('style', 'display:block;');
					$('#advanced-search').removeClass('collapsed');
					$('#advanced-search').addClass('advanced');
				} else {
					$('#edit-query').val(query);

					//$('#edit-query').removeAttr('disabled');
					$('#advanced-search').addClass('collapsed');
					$('#advanced-search').removeClass('advanced');
				}
      
			$('#roti-loading').hide();
			}).effect('highlight', {}, 1000);

	};

  $('a.roti-query-static').live('click', function(e) {
    e.preventDefault();
    query_link = $(this).attr('href');
    $('a.roti-query-static').removeClass('active');
    $(this).addClass('active');
    fetch(0, query_link, sort_link);
  });

  $('a.roti-sort-static').live('click', function(e) {
    e.preventDefault();
    sort_link = $(this).attr('href');
    $('a.roti-sort-static').removeClass('active');
    $(this).addClass('active');
    fetch(0, query_link, sort_link);
  });
  
  $('#node-form input.roti-query-static').live('change', function(e) {
    // Use for refresh vote
    current_category = $(this);
    
    query_link = $(this).parent().text();
    $('#node-form input.roti-query-static').parent().removeClass('active');
    $(this).parent().addClass('active');
    fetch(0, 'subject=' + query_link, sort_link, false, true);
  });
  
  $('#node-form #edit-submit-1').live('click', function(e) {
    e.preventDefault();
    $('#roti-sort-html').hide();
    query_link = $('#edit-body').val();
    $('#node-form').hide();
    fetch(0, 'query=' + subject + ' ' + query_link, sort_link, null, true);
    
    $('#node-form').parent().append('<div id="idea-preview"></div><div id="idea-post"></div>');
    $('#idea-preview').append($('.user-widget').first().html());
    $('#idea-preview').append('<h2>ไอเดียของคุณ</h2>')
      .append('<span>' + query_link + '</span>');
    
    $('#idea-post').append('<input type="button" id="post-submit" value="ไม่ตรงกับไอเดียของฉัน, ดำเนินการต่อ" />')
      .append('<span>หรือ <a id="post-cancel" href="#">กลับไปแก้ไข</a>');
    
    $('#post-submit').click( function() {
      $('#node-form').submit();
    });
    
    $('#post-cancel').click( function(e) {
      e.preventDefault();
      $('#roti-sort-html').show();
      $('#idea-preview').detach();
      $('#idea-post').detach();
      
      $('#node-form').show();
    });
  });
	
  var query = jQuery.trim($('#edit-query').val());
  if (!$('#node-form input.roti-query-static').get(0) && (get_fields() || query)) {
    fetch(0, null, null, true, true);
  }

  if (typeof Drupal.settings.rotioai != 'undefined') {
    $('#edit-query').val(Drupal.settings.rotioai.query);
    $('#rotioai-form').submit();
  }
	
	
});

/**
 * Something wrong with default form reset.
 * So, use this function instead. 
 */
function rotioai_form_reset() {
  $('#advanced-search .form-item input').val('');
}
