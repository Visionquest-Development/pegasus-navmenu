jQuery(function($) {




    //var toggleExpand = false;
    //$('.dropdown').dropdown();
    //$('.dropdown').on('click', function(){
        //console.log( this );
        //$( this ).find("> .dropdown-menu").find('li').removeClass('dropdown');

        //$( this ).attr("aria-expanded", !toggleExpand );
        //$( this ).find("> .dropdown-menu").toggleClass("show");
        //$( this ).find("> .dropdown-menu").toggleClass("show").closest("li").removeClass("dropdown");
        //toggleExpand = !toggleExpand;
    //});
    $('.navbar').each(function( ) {
        if ( $( this ).hasClass('navbar-dark') ) {
            $( this ).find('.dropdown-menu').addClass('bg-dark');
        }
        if ( $( this ).hasClass('bg-dark') ) {
            $( this ).find('.dropdown-menu').addClass('bg-dark');
            $( this ).find('.dropdown-item').hover(function() {
                $( this ).addClass('bg-dark');

            });
        }
    });





});