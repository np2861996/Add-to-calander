jQuery(document).ready( function($){

    $(function() {
        $(".calender-button a").on("click", function(e) {
            $(".calender-button .wrap").addClass("active");
            e.stopPropagation()
        });
        $(document).on("click", function(e) {
            if ($(e.target).is(".calender-button .wrap") === false) {
            $(".calender-button .wrap").removeClass("active");
            }
        });
        });


})