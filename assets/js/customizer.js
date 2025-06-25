( function( $ ) {
 
    /* Link Color */
    wp.customize( 'color_link', function( value ) {
        value.bind( function( to ) {
            var color_link_css = 'a,a:hover,a:focus{color:' +  to + '}';
            $( '#my-link-color-css' ).html( color_link_css );
        } );
    } );
 
    /* Site Title Color */
    wp.customize( 'color_site_title', function( value ) {
        value.bind( function( to ) {
            var site_title_css = '#site-title a,#site-title a:hover,#site-title a:focus{color:' +  to + '}';
            $( '#nevertheless-site-title-color-css' ).html( site_title_css );
        } );
    } );
    
} )( jQuery );