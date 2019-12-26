// Check function use of Gutenberg
function acfb_isGutenbergActive() {
    return typeof wp !== 'undefined' && typeof wp.blocks !== 'undefined';
}

// Declare constants if Gutenberg is used
if(acfb_isGutenbergActive()){
  const { getEditedPostAttribute } = wp.data.select( 'core/editor' );
  const title = getEditedPostAttribute( 'title' );
  const content = getEditedPostAttribute( 'content' );
}

// Detect prohibited characters in text field
(function($) {
  acf.fields.word_check = acf.field.extend({
		type: 'text',
		events: {
			'blur input': 'change_count',
      'focusin input': 'lock_save',
		},
    lock_save: function(e){
      if(acfb_isGutenbergActive()){
        wp.data.dispatch( 'core/editor' ).lockPostSaving( 'acf' );
      }
    },
    // Check the word when the field value is updated
		change_count: function(e){
      var target_field = e.$el.attr('id');
      $.ajax({
        type: 'POST',
        url: ajaxurl,
        data: {
            'action' : 'check_words',
            'target_field' : target_field,
        },
      // Validate prohibited words and input values received from endpoints
    success: function( response ){
        response = JSON.parse(response);
        if(response != 'undefined'){
          var badword = response.split(",");
          if($.inArray(e.$el.val(), badword) != -1 && !e.$el.val() == ""){
              // Check for duplicate alerts
              if(!($(`#alert-${target_field}`).length)){
                // Block post and display alert if using Gutenberg
                if(acfb_isGutenbergActive()){
                  wp.data.dispatch( 'core/notices' ).createErrorNotice( 'Contains NG word', { id: 'NG_NOTICE',isDismissible: true} );
                }
                // Display an alert if prohibited words are included
                e.$el.before(`<div class="notice notice-error" name="notice-flag" id="alert-${target_field}">Contains NG word</div>`);
              }
              return;
            }
            else{
              // Turn off alert when input no longer contains prohibited words
              $(`#alert-${target_field}`).remove();
              // Unlock if you are using Gutenberg
              if(acfb_isGutenbergActive() && !($('div[name="notice-flag"]')[0])){
                wp.data.dispatch( 'core/notices' ).removeNotice( 'NG_NOTICE' );
                wp.data.dispatch( 'core/editor' ).unlockPostSaving( 'acf' );
              }
              return;
            }
          }
        }
      });
    return;
		}
	});
})(jQuery);

// Detect prohibited characters in textarea field
(function($) {
  acf.fields.word_check_textarea = acf.field.extend({
		type: 'textarea',

		events: {
			'blur textarea': 'change_count',
      'focusin input': 'lock_save',
		},
    lock_save: function(e){
      if(acfb_isGutenbergActive()){
        wp.data.dispatch( 'core/editor' ).lockPostSaving( 'acf' );
      }
    },
    // Check the word when the field value is updated
		change_count: function(e){
      var target_field = e.$el.attr('id');
      $.ajax({
        type: 'POST',
        url: ajaxurl,
        data: {
            'action' : 'check_words',
            'target_field' : target_field,
        },
    // Validate prohibited words and input values received from endpoints
    success: function( response ){
        response = JSON.parse(response);
        if(response != 'undefined'){
          var badword = response.split(",");
          if($.inArray(e.$el.val(), badword) != -1 && !e.$el.val() == ""){
              // Check for duplicate alerts
              if(!($(`#alert-${target_field}`).length)){
                // Block post and display alert if using Gutenberg
                if(acfb_isGutenbergActive()){
                  wp.data.dispatch( 'core/notices' ).createErrorNotice( 'Contains NG word', { id: 'NG_NOTICE',isDismissible: true} );
                }
                // Display an alert if prohibited words are included
                e.$el.before(`<div class="notice notice-error" name="notice-flag" id="alert-${target_field}">Contains NG word</div>`);
              }
              return;
            }
            else{
              // Turn off alert when input no longer contains prohibited words
              $(`#alert-${target_field}`).remove();
              // Unlock if you are using Gutenberg
              if(acfb_isGutenbergActive() && !($('div[name="notice-flag"]')[0])){
                wp.data.dispatch( 'core/notices' ).removeNotice( 'NG_NOTICE' );
                wp.data.dispatch( 'core/editor' ).unlockPostSaving( 'acf' );
              }
              return;
            }
          }
        }
      });
    return;
		}
	});
})(jQuery);
