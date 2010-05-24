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
			$('#edit-query').removeAttr("disabled");
			$('#edit-query').focus();
			$('#advanced-search').removeClass('advanced');
		} else {
			$('#edit-query').val('');
			$('#edit-query').attr('disabled', 'disabled');
			$('#advanced-search').addClass('advanced');
		}
	});

	/**
	 * Ajax inline search
	 */
	$('#rotioai-form').submit(function(e) {
		e.preventDefault();
		fetch(0, null, true);
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

	var fetch = function(page, query_link, is_form_submit) {

    if (page == null) {
			page = 0;
		}
		
		var query = jQuery.trim($('#edit-query').val());
		
		var req = {
		  'page': page,
		  'is_form_submit': is_form_submit,
		  'query': query
		};
		
    if (query_link) {
		  var q = query_link.split('=');
		  req[q[0]] = q[1];
		  rotioai_form_reset()
		  $('#edit-advanced-keywords-'+q[0]).val(q[1]);
		  $('#edit-query').val('');
		}

		var fields = get_fields();
		jQuery.extend(req, fields);

		$('#rotisearch-box').load('rotisearch/result #rotisearch-result', req, function() {
				$('ul.pager a').click(function(e) {
					e.preventDefault();
					var page = String(this).match(/page=(\d+)/);
					if (page != null) {
						page = page[1];
					} else {
						page = 0;
					}
					fetch(page);
				});

				$('a.roti-query').click(function(e) {
					e.preventDefault();
          var query_link = $(this).attr('href');
					fetch(0, query_link);
				});

				// Handle fieldset behavior
				if (!jQuery.isEmptyObject(fields)) {
					$('#edit-query').val('');
					/*
					$('#edit-advanced-keywords-title').val(title);
					$('#edit-advanced-keywords-body').val(body);
					$('#edit-advanced-keywords-tag').val(decodeURI(tag));
					$('#edit-advanced-keywords-from-date').val(from);
					$('#edit-advanced-keywords-to-date').val(to);
					*/

					$('#edit-query').attr('disabled', 'disabled');
					$('.fieldset-wrapper').attr('style', 'display:block;');
					$('#advanced-search').removeClass('collapsed');
					$('#advanced-search').addClass('advanced');
				} else {
					$('#edit-query').val(query);

					$('#edit-query').removeAttr('disabled');
					$('#advanced-search').addClass('collapsed');
					$('#advanced-search').removeClass('advanced');
				}

				if (!jQuery.isEmptyObject(fields) || query) {
					window.location.hash = '#rotisearch-box';
				}

			}).effect('highlight', {}, 1000);
	};
	
	var query = jQuery.trim($('#edit-query').val());
  if (get_fields() || query) {
    fetch(0, null, true);
  }
	
});

/**
 * Something wrong with default form reset.
 * So, use this function instead. 
 */
function rotioai_form_reset() {
  $('#advanced-search .form-item input').val('');
}
