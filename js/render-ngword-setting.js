(function($) {
  $(document).ready(function(){
    acf.addAction('append_field/name=unique_ng_word', suspend_tmpload_ngword);
    $(document).on("click", "a , .button button-primary button-large add-field", function () {
      $('tr[class="acf-field acf-field-text acf-field-setting-unique_ng_word"]').hide();
      render_ngword_setting();
    });
    $(document).on("change", 'input[type="radio"][id*="ng-type-select"]', function () {
      var unique_field = $(this).parents('.acf-field-settings').find('tr[class="acf-field acf-field-text acf-field-setting-unique_ng_word"]');
      if($(this).val() == 2){
        $(unique_field).show();
      }
      else{
        $(unique_field).hide();
      }
    });
    function suspend_tmpload_ngword (){
      $('tr[class="acf-field acf-field-text acf-field-setting-unique_ng_word"]').hide();
      render_ngword_setting();
    }
    function render_ngword_setting (){
        $('input[type="radio"][id*="ng-type-select-2"]:checked').each(function() {
        $(this).parents('.acf-field-settings').find('tr[class="acf-field acf-field-text acf-field-setting-unique_ng_word"]').show();
      });
    }
  });
})(jQuery);
