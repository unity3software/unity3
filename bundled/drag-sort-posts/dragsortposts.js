jQuery(document).ready( function($) {
 
  "use strict";
  
  //Attach sortable to the tbody, NOT tr
  $("tbody#the-list").sortable({
      cursor: "move",
      axis: "y",
      start: function(e, ui){
          ui.placeholder.height(ui.item.height());
      },
      update: function(event, ui) {
          var serialized = {};

          $(this).children().each( function(index, el) {
            serialized[el.id.split("-")[1]] = index;
          });

          $.post(ajaxurl, {
              action:'unity3_drag_sort_posts',
              posts: serialized
          });
      }
  });
 
});