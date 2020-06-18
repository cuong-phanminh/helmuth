(function($) {
	'use strict';
	$(document).ready(function() {

		var googleRecaptchaV2 = $('.stlsr_google_recaptcha_v2');
		var googleRecaptchaV3 = $('.stlsr_google_recaptcha_v3');
		var captcha = $('.stlsr input[name="captcha"]:checked').val();
		if('google_recaptcha_v2' === captcha) {
			googleRecaptchaV2.show();
		} else if('google_recaptcha_v3' === captcha) {
			googleRecaptchaV3.show();
		}

		$(document).on('change', '.stlsr input[name="captcha"]', function() {
			var captcha = this.value;
			var stlsrCaptcha = $('.stlsr_captcha');
			stlsrCaptcha.hide();
			if('google_recaptcha_v2' === captcha) {
				googleRecaptchaV2.fadeIn();
			} else if('google_recaptcha_v3' === captcha) {
				googleRecaptchaV3.fadeIn();
			}
		});

		var loginRecaptcha = $('.stlsr_login_captcha');
		var lostPasswordRecaptcha = $('.stlsr_lostpassword_captcha');
		var registerRecaptcha = $('.stlsr_register_captcha');
		var commentRecaptcha = $('.stlsr_comment_captcha');

		if($('#stlsr_login_captcha_enable').is(':checked')) {
			loginRecaptcha.show();
		}
		if($('#stlsr_lostpassword_captcha_enable').is(':checked')) {
			lostPasswordRecaptcha.show();
		}
		if($('#stlsr_register_captcha_enable').is(':checked')) {
			registerRecaptcha.show();
		}
		if($('#stlsr_comment_captcha_enable').is(':checked')) {
			commentRecaptcha.show();
		}

		$(document).on('change', '#stlsr_login_captcha_enable', function() {
			if($(this).is(':checked')) {
				loginRecaptcha.fadeIn();
			} else {
				loginRecaptcha.hide();
			}
		});
		$(document).on('change', '#stlsr_lostpassword_captcha_enable', function() {
			if($(this).is(':checked')) {
				lostPasswordRecaptcha.fadeIn();
			} else {
				lostPasswordRecaptcha.hide();
			}
		});
		$(document).on('change', '#stlsr_register_captcha_enable', function() {
			if($(this).is(':checked')) {
				registerRecaptcha.fadeIn();
			} else {
				registerRecaptcha.hide();
			}
		});
		$(document).on('change', '#stlsr_comment_captcha_enable', function() {
			if($(this).is(':checked')) {
				commentRecaptcha.fadeIn();
			} else {
				commentRecaptcha.hide();
			}
		});

		$(document).on('click', '.st-alert-box-dismiss', function() {
			$(this).parent().fadeOut(300, function() {
				$(this).remove();
			});
		});

		var loadingContainer = $('<span/>', {
			'class': 'st-loading-container'
		});
		var loadingImage = $('<img/>', {
			'src': stlsradminurl + 'images/spinner.gif',
			'class': 'st-loading-image'
		});

		function stlsrBeforeSubmit(formBtn) {
			$('.st-text-danger').remove();
			$('.st-is-invalid').removeClass("st-is-invalid");
			$('.st-alert-box').remove();
			formBtn.prop('disabled', true);
			loadingContainer.insertAfter(formBtn);
			loadingImage.appendTo(loadingContainer);
			return true;
		}

		function stlsrSuccessMessage(message, formId) {
			var alertBox = '' +
			'<div class="st-alert-box notice notice-success is-dismissible">' +
				'<p>' +
					'<strong>' + message + '</strong>' +
				'</p>' +
				'<button type="button" class="st-alert-box-dismiss notice-dismiss"></button>' +
			'</div>';
			$(alertBox).insertBefore(formId);
		}

		function stlsrErrorMessage(message, formId, statusText = '') {
			if(statusText) {
				var statusText = ' ' + statusText;
			}
			var errorSpan = '' +
			'<div class="st-alert-box notice notice-error is-dismissible">' +
				'<p>' +
					'<strong>' + message + '</strong>' + statusText +
				'</p>' +
				'<button type="button" class="st-alert-box-dismiss notice-dismiss"></button>' +
			'</div>';
			$(errorSpan).insertBefore(formId);
		}

		// Save captcha settings.
		var saveCaptchaFormId = '#stlsr-save-captcha-form';
		var saveCaptchaForm = $(saveCaptchaFormId);
		var saveCaptchaBtn = $('#stlsr-save-captcha-btn');
		saveCaptchaForm.ajaxForm({
			beforeSubmit: function(arr, $form, options) {
				return stlsrBeforeSubmit(saveCaptchaBtn);
			},
			success: function(response) {
				if(response.success) {
					stlsrSuccessMessage(response.data.message, saveCaptchaFormId);
				} else {
					if(response.data && $.isPlainObject(response.data)) {
						stlsrFormErrors(saveCaptchaFormId, response);
					} else {
						stlsrErrorMessage(response.data, saveCaptchaFormId);
					}
				}
			},
			error: function(response) {
				saveCaptchaBtn.prop('disabled', false);
				stlsrErrorMessage(response.status, saveCaptchaFormId, response.statusText);
			},
			complete: function(event, xhr, settings) {
				saveCaptchaBtn.prop('disabled', false);
				loadingContainer.remove();
			}
		});

		// Clear error logs.
		var clearErrorLogsFormId = '#stlsr-clear-error-logs-form';
		var clearErrorLogsForm = $(clearErrorLogsFormId);
		var clearErrorLogsBtn = $('#stlsr-clear-error-logs-btn');
		$(document).on('click', '#stlsr-clear-error-logs-btn', function(e) {
			e.preventDefault();
			if(confirm(clearErrorLogsBtn.data('message'))) {
				clearErrorLogsForm.ajaxSubmit({
					beforeSubmit: function(arr, $form, options) {
						return stlsrBeforeSubmit(clearErrorLogsBtn);
					},
					success: function(response) {
						if(response.success) {
							stlsrSuccessMessage(response.data.message, clearErrorLogsFormId);
	                		$('.stlsr-error-logs-body').load(location.href + " " + '.stlsr-error-logs-body', function () {});
						} else {
							if(response.data && $.isPlainObject(response.data)) {
								stlsrFormErrors(clearErrorLogsFormId, response);
							} else {
								stlsrErrorMessage(response.data, clearErrorLogsFormId);
							}
						}
					},
					error: function(response) {
						clearErrorLogsBtn.prop('disabled', false);
						stlsrErrorMessage(response.status, clearErrorLogsFormId, response.statusText);
					},
					complete: function(event, xhr, settings) {
						clearErrorLogsBtn.prop('disabled', false);
						loadingContainer.remove();
					}
				});
			}
		});

		// Reset plugin.
		var resetPluginFormId = '#stlsr-reset-plugin-form';
		var resetPluginForm = $(resetPluginFormId);
		var resetPluginBtn = $('#stlsr-reset-plugin-btn');
		$(document).on('click', '#stlsr-reset-plugin-btn', function(e) {
			e.preventDefault();
			if(confirm(resetPluginBtn.data('message'))) {
				resetPluginForm.ajaxSubmit({
					beforeSubmit: function(arr, $form, options) {
						return stlsrBeforeSubmit(resetPluginBtn);
					},
					success: function(response) {
						if(response.success) {
							stlsrSuccessMessage(response.data.message, resetPluginFormId);
						} else {
							if(response.data && $.isPlainObject(response.data)) {
								stlsrFormErrors(resetPluginFormId, response);
							} else {
								stlsrErrorMessage(response.data, resetPluginFormId);
							}
						}
					},
					error: function(response) {
						resetPluginBtn.prop('disabled', false);
						stlsrErrorMessage(response.status, resetPluginFormId, response.statusText);
					},
					complete: function(event, xhr, settings) {
						resetPluginBtn.prop('disabled', false);
						loadingContainer.remove();
					}
				});
			}
		});

	});
})(jQuery);
