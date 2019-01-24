var App = (function () {


  //Core private functions & variables

  return {
    init: function (options) {

      /*Trigger mdl-drawer with brand-logo*/
        $(".mdl-layout-brand-logo").click(function( e ){
          var drawer = $(".mdl-layout__drawer");
          drawer.toggleClass("is-visible");
        });

      /*Left drawer sub menus open/close interaction*/
      $('.mdl-layout__drawer .mdl-navigation__parent > a').on('cick',function( e ){
        alert("hey");
        e.preventDefault();
      });

      /*Hide header on scroll*/
        var lsHeader = $(".mdl-layout--fixed-header .mdl-layout__header");
        var lsContent = $(".mdl-layout__content");

        var elHeight  = 0,
          elTop       = 0,
          dHeight     = 0,
          wHeight     = 0,
          wScrollCurrent  = 0,
          wScrollBefore = 0,
          wScrollDiff   = 0;

        lsContent.on( 'scroll', function(){
          elHeight    = lsHeader.outerHeight();
          dHeight     = lsContent[0].scrollHeight;
          wHeight     = lsContent.outerHeight();
          wScrollCurrent  = lsContent.scrollTop();
          wScrollDiff = wScrollBefore - wScrollCurrent;
          elTop       = parseInt( lsHeader.css( 'margin-top' ) ) + wScrollDiff;

          if( wScrollCurrent <= 0 ) { // scrolled to the very top; element sticks to the top
            lsHeader.css( 'margin-top', 0 );

          } else if( wScrollDiff > 0 && wScrollCurrent + wHeight <= dHeight - 10 ) { // scrolled up; element slides in
            lsHeader.css( 'margin-top', elTop > 0 ? 0 : elTop );
          } else if( wScrollDiff < 0 ) { // scrolled down
            if( wScrollCurrent + wHeight <= dHeight ) {
              lsHeader.css( 'margin-top', Math.abs( elTop ) > elHeight ? -elHeight : elTop );
            }
          }

          wScrollBefore = wScrollCurrent;
        });
    }
  };

})();
