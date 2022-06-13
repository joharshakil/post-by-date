jQuery( document ).ready( function($) {
    var pageNumber = 0;
    function load_posts() {
        //debugger;
        pageNumber++;
        var str = 'nonce=' + pbd_front_data.pbd_security + '&cat=' + post_category + '&pageNumber=' + pageNumber + '&ppp=' + post_limit + '&date=' + post_date + '&action=more_post_ajax';
        jQuery.ajax({
            type: "POST",
            dataType: "html",
            url: pbd_front_data.ajax_url,
            data: str,
            success: function(data){
                var $data = $(data);
                if($data.length){
                    jQuery("#pbd-posts").append($data);
                    jQuery("#more_posts").css('display', 'block');
                } else{
                    jQuery("#more_posts").css('display', 'none');
                    jQuery("#pbd-posts").append(`<strong>${pbd_front_data.no_more_posts}</strong>`);
                }
            },
            error : function(jqXHR, textStatus, errorThrown) {
                $loader.html(jqXHR + " :: " + textStatus + " :: " + errorThrown);
            }

        });
        return false;
    }

    load_posts();

    jQuery("#more_posts").on("click",function( event ) { // When btn is pressed.
        event.preventDefault();        
        load_posts();
    });
});