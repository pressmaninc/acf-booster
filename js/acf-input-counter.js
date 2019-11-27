// Display a counter in the text field
(function($) {
	acf.fields.text_counter = acf.field.extend({
		type: 'text',

		events: {
			'input input': 'change_count',
		},
		// Set the number to display each time the value is changed
		change_count: function(e){
			var $max = e.$el.attr('maxlength');
			if (typeof($max) == 'undefined' || e.$el.closest('.acf-input').find('.count').length == 0) {
				return;
			}
			var $value = e.$el.val();
			var $length = $value.length;
			e.$el.closest('.acf-input').find('.count').text($length);
		},

	});
})(jQuery);

// Display a counter in the textarea field
(function($) {
	acf.fields.textarea_counter = acf.field.extend({
		type: 'textarea',

		events: {
			'input textarea': 'change_count',
		},
		// Set the number to display each time the value is changed
		change_count: function(e){
			var $max = e.$el.attr('maxlength');
			if (typeof($max) == 'undefined') {
				return;
			}
			var $value = e.$el.val();
			var $length = $value.length;
			e.$el.closest('.acf-input').find('.count').text($length);
		},

	});
})(jQuery);
