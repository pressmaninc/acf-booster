// Control the counter setting screen in field group settings
(function($) {
  $(document).ready(function(){
    acf.addAction('append_field/name=show_count', suspend_tmpload_counter);
    // Control when fields are added
    $(document).on("click", "a , .button button-primary button-large add-field", function () {
      $('tr[class="acf-field acf-field-true-false acf-field-setting-show_count"]').hide();
      render_counter_setting();
    });
    // Added switch when limit number of characters is set
    $(document).on("blur", 'input[type="number"][id*=-maxlength]', function () {
      var setting_switch = $(this).parents('.acf-field-settings').find('tr[class="acf-field acf-field-true-false acf-field-setting-show_count"]');
      if($(this).val() != ""){
        setting_switch.show();
      }
      else{
        setting_switch.hide();
      }
    });
    // Avoid infinite loops
    function suspend_tmpload_counter (){
      $('tr[class="acf-field acf-field-true-false acf-field-setting-show_count"]').hide();
      render_counter_setting();
    }
    //Switcher drawing function
    function render_counter_setting (){
      $('input[type="number"][id*=-maxlength]').each(function() {
        var setting_switch = $(this).parents('.acf-field-settings').find('tr[class="acf-field acf-field-true-false acf-field-setting-show_count"]');
        if($(this).val() != ""){
          setting_switch.show();
        }
        else{
          setting_switch.hide();
        }
      });
    }
  });
})(jQuery);
