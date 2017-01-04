var App = (function () {


  //Core private functions & variables

  return {
    init: function (options) {
      
      /*Trigger mdl-drawer with brand-logo*/
        $(".mdl-layout-brand-logo").click(function( e ){
          var drawer = $(".mdl-layout__drawer");
          drawer.toggleClass("is-visible");
        });
        
    }
  };
 
})();