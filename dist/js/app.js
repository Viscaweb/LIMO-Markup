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
      var subMenu = {
        selectors: {
          anchors:     '.mdl-layout__drawer .mdl-navigation__parent > a',
          subMenu:     '.mdl-sub-navigation'
        },
        classNames: {
          open:        'mdl-navigation__open'
        },
        menuIsOpen: function(menuItem){
          return menuItem.hasClass(this.classNames.open) ? true : false;
        },
        addOpenClasses: function(menuItem){
          menuItem.addClass(this.classNames.open);
        },
        removeOpenClasses: function(menuItem){
          menuItem.removeClass(this.classNames.open);
        },
        closeOpenMenus: function(menuItem){
          menuItem.siblings(this.selectors.openItems).each(function(i, el){
            var menuElement = $(el);

            $(this.selectors.subMenu, el).slideUp(function(){
              this.removeOpenClasses(menuElement);
            }.bind(this));
          }.bind(this));
        },
        openMenu: function(menuItem){
          var subMenu = menuItem.find(this.selectors.subMenu);

          if( this.menuIsOpen(menuItem) ) {
            subMenu.slideUp(function(){
              this.removeOpenClasses(menuItem);
            }.bind(this));
          } else {
            subMenu.slideDown(function(){
              this.addOpenClasses(menuItem);
            }.bind(this));
          }
        },
        init: function(){
          // Bind the main event
          $(this.selectors.anchors).on('click',function(e){
            e.preventDefault();
            var anchor = $(e.target);
            var menuItem = anchor.parent();

            this.closeOpenMenus(menuItem);
            this.openMenu(menuItem);

          }.bind(this));
        }
      };

      // Initalize sub menu open/close interaction
      subMenu.init();

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
