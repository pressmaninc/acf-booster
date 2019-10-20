(function($) {
  $(document).ready(function(){
    acf.addAction('append_field/name=show_count', suspend_tmpload_counter);
    $(document).on("click", "a , .button button-primary button-large add-field", function () {
      $('tr[class="acf-field acf-field-true-false acf-field-setting-show_count"]').hide();
      render_counter_setting();
    });
    $(document).on("blur", 'input[type="number"][id*=-maxlength]', function () {
      var setting_switch = $(this).parents('.acf-field-settings').find('tr[class="acf-field acf-field-true-false acf-field-setting-show_count"]');
      if($(this).val() != ""){
        setting_switch.show();
      }
      else{
        setting_switch.hide();
      }
    });
    function suspend_tmpload_counter (){
      $('tr[class="acf-field acf-field-true-false acf-field-setting-show_count"]').hide();
      render_counter_setting();
    }
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
//setting_switch.insertAfter('tr[class="acf-field acf-field-number acf-field-setting-maxlength"]');
