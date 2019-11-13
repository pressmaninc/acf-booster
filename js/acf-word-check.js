(function($) {
  acf.fields.word_check = acf.field.extend({
		type: 'text',

		events: {
			'blur input': 'change_count',
		},

		change_count: function(e){
      var target_field = e.$el.attr('id');
      $.ajax({
        type: 'POST',
        url: ajaxurl,
        data: {
            'action' : 'check_words',
            'target_field' : target_field,
        },
        success: function( response ){
          response = JSON.parse(response);
          if(response != 'undefined'){
            var badword = response.split(",");
            var i = 0;
            for ( i = 0  ; i < badword.length ; i ++ ) {
              if ( e.$el.val().indexOf(badword[i]) != -1 ) {
                if(!($(`#alert-${target_field}`).length)){
                  e.$el.before(`<div class="notice notice-error" id="alert-${target_field}">Contains NG word</div>`);
                }
                return;
              }
              else{
                $(`#alert-${target_field}`).remove();
                return;
              }
            }
          }
        }
      });
    return;
		}
	});
})(jQuery);

(function($) {
  acf.fields.word_check_textarea = acf.field.extend({
		type: 'textarea',

		events: {
			'blur textarea': 'change_count',
		},

		change_count: function(e){
      var target_field = e.$el.attr('id');
      $.ajax({
        type: 'POST',
        url: ajaxurl,
        data: {
            'action' : 'check_words',
            'target_field' : target_field,
        },
        success: function( response ){
          response = JSON.parse(response);
          if(response != 'undefined'){
            var badword = response.split(",");
            var i = 0;
            for ( i = 0  ; i < badword.length ; i ++ ) {
              if ( e.$el.val().indexOf(badword[i]) != -1) {
                if(!($(`#alert-${target_field}`).length)){
                  e.$el.before(`<div class="notice notice-error" id="alert-${target_field}">Contains NG word</div>`);
                }
                return;
              }
              else{
                $(`#alert-${target_field}`).remove();
                return;
              }
            }
          }
        }
      });
    return;
		}
	});
})(jQuery);
