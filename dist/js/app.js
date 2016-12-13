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
        var lsHeader = document.querySelector(".mdl-layout--fixed-header .mdl-layout__header");
        // construct an instance of Headroom, passing the element
        var headroom  = new Headroom(lsHeader);
        // initialise
        headroom.init(); 
        alert("??");
    }
  };
 
})();