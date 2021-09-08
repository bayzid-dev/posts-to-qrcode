
// in jQuery $ = document.getElementById/Class(.class/ #id)

(function ($) {
  $(document).ready(function () {
      
    var currentValue = $("#pqrc_toggle").val();
        $(".toggle").minitoggle({
            on: currentValue == 1 ? true : false
        });

        if(currentValue == 1 ){
            $(".toggle .toggle-handle").attr('style', 'transform: translate3d(36px, 0px, 0px)');
        }

    $(".toggle").on("toggle", function (e) {
      if (e.isActive) 
      $("#pqrc_toggle").val(1);
      else 
      $("#pqrc_toggle").val(0);
    });
  });
})(jQuery);
