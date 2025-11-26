$(function () {


  $.ajax({
      url: "../get_tags.php",
      method: "GET",
      dataType: "json",
      success: function (data) {
          $("#demo-input").autocomplete({
              source: data
          });
      }
  });


  $(".auto").autocomplete({
      minLength: 1,
      source: function (request, response) {
          $.ajax({
              url: "../search_tags.php",
              method: "GET",
              data: { q: request.term },
              dataType: "json",
              success: function (data) {
                  response(data);
              }
          });
      }
  });

 
  $(".auto-category").autocomplete({
      minLength: 0,
      source: function (request, response) {
          $.ajax({
              url: "../get_categories.php",
              method: "GET",
              dataType: "json",
              success: function (data) {
                  response(data);
              }
          });
      }
  });

});  
