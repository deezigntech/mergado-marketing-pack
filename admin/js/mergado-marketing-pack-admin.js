(function( $ ) {
	'use strict';

	/**
	 * All of the code for your admin-facing JavaScript source
	 * should reside in this file.
	 *
	 * Note: It has been assumed you will write jQuery code here, so the
	 * $ function reference has been prepared for usage within the scope
	 * of this function.
	 *
	 * This enables you to define handlers, for when the DOM is ready:
	 *
	 * $(function() {
	 *
	 * });
	 *
	 * When the window is loaded:
	 *
	 * $( window ).load(function() {
	 *
	 * });
	 *
	 * ...and/or other possibilities.
	 *
	 * Ideally, it is not considered best practise to attach more than a
	 * single DOM-ready or window-load handler for a particular page.
	 * Although scripts in the WordPress core, Plugins and Themes may be
	 * practising this, we should strive to set a better example in our own work.
	 */


	$(function() {
		generateInlineCodeForGoogleReviews();
		deleteNoticeOnTab();
		deleteNoticeFromLink();

		var merg = $('.mergado_new_news');

		if(merg.length > 0) {
			$('.mergado-custom-icon').parent().append('<span class="mergado__topbar_icon">' + merg.attr('data-news') + '</span>')
		}

		// Copy to clipboard
		$('[data-copy-stash]').on('click', function (e) {
			e.preventDefault();
			var stash = $(this).attr('data-copy-stash');
			copyToClipboard(stash);
		});

		function copyToClipboard(text) {
			var $temp = $("<input>");
			$("body").append($temp);
			$temp.val(text).select();
			document.execCommand("copy");
			$temp.remove();
		}

		$('.mmp_feedBox__toggler').on('click', function () {
			toggleFeedBox($(this));
		});

		function toggleFeedBox(element)
		{ element.closest('.mmp_feedBox').toggleClass('mmp_feedBox--opened');
		}

		// Question if leaving and changed something on adsys page
		let searchParams = new URLSearchParams(window.location.search);
		var clickedSubmit = false;

		// On change of form set changed
		$("form :input").change(function() {
			$(this).closest('form').data('changed', true);
		});


		$('input[type="submit"], button[type="submit"]').on('click', function() {
			clickedSubmit = true;
		});

		if ($('body').hasClass('mergado_page_mergado-adsys')) {
			if ($('#mmpheader').length > 0) {
				$(window).bind('beforeunload', function () {
					var changed = false;
					$('form').each(function () {
						if ($(this).data('changed') && !clickedSubmit) {
							changed = true;
							return false;
						}
					});

					if (changed) {
						return false;
					}
				});
			}
		}

		// Enable/disable fields on adsys page
		toggleAllFields();

		$('[data-mmp-check-main]').on('click', function() {
			var fieldGroup = $(this).attr('data-mmp-check-main');
			toggleFields(fieldGroup);
		});

		function toggleAllFields()
		{

			$('[data-mmp-check-main]').each(function() {
				var fieldGroup = $(this).attr('data-mmp-check-main');
				toggleSubFields(fieldGroup);
			});

			$('[data-mmp-check-main]').each(function() {
				var fieldGroup = $(this).attr('data-mmp-check-main');
				toggleFields(fieldGroup);
			});
		}

		function toggleSubFields(fieldGroup) {
			if($("[data-mmp-check-main='" + fieldGroup + "']").is(':checked')) {
				var page = $('body:not(".mergado_page_mergado-cron")');

				if(typeof page !== 'undefined') {
					$("[data-mmp-check-subfield='" + fieldGroup + "']").parent().parent().removeClass('mmp-disabled');
				}
				$("[data-mmp-check-subfield='" + fieldGroup + "']").prop( "disabled", false );
			} else {
				if(typeof page !== 'undefined') {
					$("[data-mmp-check-subfield='" + fieldGroup + "']").parent().parent().addClass('mmp-disabled');
				}
				$("[data-mmp-check-subfield='" + fieldGroup + "']").prop( "disabled", true );
			}
		}

		function toggleFields(fieldGroup) {
			if($("[data-mmp-check-main='" + fieldGroup + "']").is(':checked')) {
				var page = $('body:not(".mergado_page_mergado-cron")');

				if(typeof page !== 'undefined') {
					$("[data-mmp-check-field='" + fieldGroup + "']:not([data-mmp-check-subfield='true'])").parent().parent().removeClass('mmp-disabled');
				}
				$("[data-mmp-check-field='" + fieldGroup + "']").prop( "disabled", false );
			} else {
				if(typeof page !== 'undefined') {
					$("[data-mmp-check-field='" + fieldGroup + "']:not([data-mmp-check-subfield='true'])").parent().parent().addClass('mmp-disabled');
				}
				$("[data-mmp-check-field='" + fieldGroup + "']").prop( "disabled", true );
			}
		}

		var locker = false;

		// Ajax reguest for CRONS
		$('.generateAjax').on('click', function (e) {
			e.preventDefault();
			$(':focus').blur();

			var feed = $(this).attr('data-feed');
			var token = $(this).attr('data-token');

			if(locker) {
				return;
			} else {
				locker = true;
			}

			$.ajax({
				type: "POST",
				url: 'admin-ajax.php',
				data: {
					action: 'ajax_generate_feed',
					feed: feed,
					token: token,
					// token: 'asdasd',
					dataType: 'json'
				},
				beforeSend: function() {
					$('.mmp-popup').addClass('active');
					$('.mmp-popup__button').addClass('disabled');
					$('.mmp-popup__loader').show();
				},
				success: function (data, status) {
					$('.mmp-popup__loader').hide();

					if (data) {
						var output = data['data'];
						if (!status || typeof output['error'] !== "undefined") {
							$('.mmp-popup__output').html(output['error']);
						} else {
							$('.mmp-popup__output').html(output['success']);
						}
					}
				},
				error: function() {
						$('.mmp-popup__loader').hide();
						$('.mmp-popup__output').html($('.mmp-popup').attr('data-500'));
				},
				complete: function() {
					locker = false;
					$('.mmp-popup__button').removeClass('disabled');
				}
			});
		});

		$('.saveAndImportRecursive').on('click', function (e) {
			e.preventDefault();
			var feed = $(this).attr('data-feed');
			var token = $(this).attr('data-token');
			var importUrl = $('#import_product_prices_url').val();
			saveImportUrlAndGenerate(feed, token, importUrl);
		});

		function saveImportUrlAndGenerate(feed, token ,importUrl)
		{
			$.ajax({
				type: "POST",
				url: 'admin-ajax.php',
				data: {
					action: 'ajax_save_import_url',
					token: token,
					dataType: 'json',
					url: importUrl,
				},
				beforeSend: function() {
					$('.mmp-popup').addClass('active');
					$('.mmp-popup__button').addClass('disabled');
					$('.mmp-popup__loader').show();
				},
				success: function (data, status) {
					importRecursive(feed, token);
				},
				error: function(jqXHR) {
					$('.mmp-popup__loader').hide();
					$('.mmp-popup__output').html(jqXHR.responseJSON.data.error);
					$('.mmp-popup__button').removeClass('disabled');
				},
			});
		}

		function importRecursive(feed, token)
		{
			$.ajax({
				type: "POST",
				url: 'admin-ajax.php',
				data: {
					action: 'ajax_generate_feed',
					feed: feed,
					token: token,
					dataType: 'json'
				},
				success: function (data, status) {
					$('.mmp-popup__loader').hide();

					if (data) {
						var output = data['data'];

						if (output['feedStatus'] === 'finished') {
							$('.mmp-popup__loader').hide();
							$('.mmp-popup__output').html(output['success']);
							$('.mmp-popup__button').removeClass('disabled');
						}
					}

					importRecursive(feed, token);
				},
				error: function(jqXHR) {
					if (jqXHR.status === 424) {
						$('.mmp-popup__loader').hide();
						$('.mmp-popup__output').html(jqXHR.responseJSON.data.error);
						$('.mmp-popup__button').removeClass('disabled');
					} else {
						mmpLowerImportProductFeedPerStepAndCallNextRun(feed, token);
					}
				},
			});
		}

		function mmpLowerImportProductFeedPerStepAndCallNextRun(feed, token)
		{
			$.ajax({
				type: "POST",
				url: 'admin-ajax.php',
				data: {
					action: 'ajax_lower_cron_product_step',
					feed: 'import',
					token: token,
					dataType: 'json'
				},
				success: function (data, status) {
					importRecursive(feed, token);
				},
				error: function (jqXHR) {
					$('.mmp-popup__loader').hide();
					$('.mmp-popup__output').html(jqXHR.responseJSON.data.error);
					$('.mmp-popup__button').removeClass('disabled');
				}
			});
		}

		$('.mmp-popup__button').on('click', function(e) {
			e.preventDefault();
			if(!$(this).hasClass('disabled')) {
				$('.mmp-popup').removeClass('active');
				$('.mmp-popup__output').html('');
				// window.location.reload();
				// location.replace('/wp-admin/admin.php?page=mergado-cron'); // Partially ajax .. meh
			}
		});

		// Close rating
		$('.mmp-close-cross').on('click', function (e) {
			e.preventDefault();
			$(this).closest('.mergado-updated-notice.news').hide();

			var cookie = $(this).attr('data-cookie');
			var token = $(this).attr('data-token');

			$.ajax({
				type: "POST",
				url: 'admin-ajax.php',
				data: {
					action: 'ajax_cookie',
					cookie: cookie,
					token: token,
					dataType: 'json'
				}
			});
		});

		// Set news readed rating
		$('.mmp-readed-cross').on('click', function (e) {
			e.preventDefault();
			$(this).parent().hide();

			var todo = $(this).attr('data-todo');
			var id = $(this).attr('data-id');
			var token = $(this).attr('data-token');

			$.ajax({
				type: "POST",
				url: 'admin-ajax.php',
				data: {
					action: 'ajax_news',
					todo: todo,
					id: id,
					token: token,
					dataType: 'json'
				}
			});
		});

		if ($('iframe').length > 0) {
			$('iframe').iFrameResize([{}]);
		}
	});

	function generateInlineCodeForGoogleReviews()
	{
		addGoogleReviewsInlineCode();

		// $('#gr_badge_position').on('change', function () {
			// if ($(this).val() == '2') {
			// 	addGoogleReviewsInlineCode();
			// }  else {
			// 	removeGoogleReviewsInlineCode();
			// }
		// });

		$('#gr_merchant_id').on('input', function () {
			removeGoogleReviewsInlineCode();
			addGoogleReviewsInlineCode();
		});
	}

	function addGoogleReviewsInlineCode()
	{
		var merchantId = $('#gr_merchant_id').val();
		$('#gr_badge_position').parent().append('<div id="gr_badge_position_inline_code">' +
				'<p><strong>Inline code:</strong></p>' +
				'<code>' +
				'&lt;g:ratingbadge merchant_id=' + merchantId + '&gt;&lt;/g:ratingbadge&gt;' +
				'</code>' +
				'</div>'
		);
	}

	function removeGoogleReviewsInlineCode()
	{
		$('#gr_badge_position_inline_code').remove();
	}

	function deleteNoticeOnTab()
	{
		$('[data-mmp-tab-button]').on('click', function () {
			$('.deleteOnTab').hide();
		});
	}

	function deleteNoticeFromLink()
	{
		var currentURL = window.location.href;
		var url = removeParamFromUrl('flash', currentURL);
		window.history.replaceState({}, document.title, url);
	}

	function removeParamFromUrl(key, sourceURL) {
		var rtn = sourceURL.split("?")[0],
				param,
				params_arr = [],
				queryString = (sourceURL.indexOf("?") !== -1) ? sourceURL.split("?")[1] : "";
		if (queryString !== "") {
			params_arr = queryString.split("&");
			for (var i = params_arr.length - 1; i >= 0; i -= 1) {
				param = params_arr[i].split("=")[0];
				if (param === key) {
					params_arr.splice(i, 1);
				}
			}
			if (params_arr.length) rtn = rtn + "?" + params_arr.join("&");
		}
		return rtn;
	}
})( jQuery );
