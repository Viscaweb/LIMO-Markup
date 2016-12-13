var App = (function () {


  //Core private functions & variables

  return {
    init: function (options) {
      
      /*Trigger mdl-drawer with brand-logo*/
        $(".mdl-layout-brand-logo").click(function( e ){
          var drawer = $(".mdl-layout__drawer");
          drawer.toggleClass("is-visible");
        });
      
      /*Hide header on scroll*/
        var lsHeader = $(".mdl-layout--fixed-header .mdl-layout__header");
        var lsContent = $(".mdl-layout__content");
        var lsHeaderHeight = $(lsHeader).outerHeight();

        // Set content padding-top
        // init controller
        var controller = new ScrollMagic.Controller({container: lsContent[0] });

        // Set the auto hide scroll offset
        var hideScrollOffset = lsHeaderHeight, cont = 0, contPos = 0;
        var hideScroll = new ScrollMagic.Scene()
          .addTo(controller).on('update', function( e ){
            var direction = controller.info("scrollDirection");

            if( direction == "FORWARD" ){
              if( cont < lsHeaderHeight - (contPos - e.scrollPos) && cont <= lsHeaderHeight && e.scrollPos > 0 ){
                lsHeader.css({"margin-top" : -Math.abs(cont) });
                cont =  e.scrollPos - contPos;
              }else if( cont >= lsHeaderHeight ) {
                lsHeader.css({"margin-top" : -Math.abs(lsHeaderHeight) });
                contPos = e.scrollPos;
              }
            }else if( direction == "REVERSE" ){
              if( cont > lsHeaderHeight - (contPos - e.scrollPos) && cont > 0 ){
                lsHeader.css({"margin-top" : (contPos - e.scrollPos) - lsHeaderHeight });
                cont = lsHeaderHeight - (contPos - e.scrollPos);
              }else if( cont <= 0 ) {
                lsHeader.css({"margin-top" : 0 });
                contPos = e.scrollPos;
              }
            }
          });
    }
  };
 
})();