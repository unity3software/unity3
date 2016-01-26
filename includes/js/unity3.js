jQuery(document).ready( function($) {

    //adminimize helper function
    $('.settings_page_adminimize-adminimize').on('click', 'input[type="checkbox"]', function(e) {
        if (e.shiftKey) {
          var checkstate = $(this).is(":checked");
          //check all sibling checkboxes
          $(this).parents('tr').find('input[type="checkbox"]').prop('checked', checkstate);
        }
    });

});