(function($){
    var methods = {
        init : function( options ) {

            var settings = {
                offset: true
            ,   bgfixed: true
            ,   invert: true
            }

            return this.each(function(){
                if ( options ){
                    $.extend(settings, options);
                } 
                
                var 
                    $this = $(this)
                ,   windowSelector = $(window)
                ,   documentSelector = $(document)
                ,   thisHeight = 0
                ,   thisOffsetTop
                ,   image_url = ''
                ,   image_width = ''
                ,   image_height = ''
                ,   msie8 = Boolean(navigator.userAgent.match(/MSIE ([8]+)\./))
                ;
                
                _constructor();
                function _constructor(){
                    image_url = $this.data("source-url");
                    image_width = parseFloat($this.data("source-width"));
                    image_height = parseFloat($this.data("source-height"));

                    $this.css({'background-image': 'url('+image_url+')'});
                 /*   if(settings.bgfixed){
                        $this.css({'background-attachment': 'fixed'});
                    }*/

                    addEventsFunction();                    
                }
                
                function addEventsFunction(){
                    //------------------ window scroll event -------------//
                    windowSelector.on('scroll',
                        function(){
                            if(settings.offset){
                                mainScrollFunction();
                            }
                        }
                    ).trigger('scroll');
                    //------------------ window resize event -------------//
                    windowSelector.on("resize",
                        function(){
                            $this.width(windowSelector.width());
                            $this.css({'width' : 'auto' /*,'margin-left' : Math.floor(windowSelector.width()*-0.5), 'left' : '50%' */ });

                            if(settings.offset){
                                mainResizeFunction();
                            }
                        }
                    ).trigger('resize');
                }
                //------------------ window scroll function -------------//
                function mainScrollFunction(){
                    parallaxEffect();
                }
                //------------------ window resize function -------------//
                function mainResizeFunction(){                    
                    parallaxEffect();
                }
                
                function parallaxEffect(){
                    var 
                        documentScrollTop
                    ,   startScrollTop
                    ,   endScrollTop
                    ,   visibleScrollValue
                    ;

                    thisHeight = $this.outerHeight();
                    windowHeight = windowSelector.height();
                    thisOffsetTop = $this.offset().top;
                    documentScrollTop = documentSelector.scrollTop();
                    startScrollTop = documentScrollTop + windowHeight;
                    endScrollTop = documentScrollTop - thisHeight;

                    if( ( startScrollTop > thisOffsetTop ) && ( endScrollTop < thisOffsetTop ) ){
                        visibleScrollValue = startScrollTop - endScrollTop;
                        pixelScrolled = documentScrollTop - (thisOffsetTop - windowHeight);
                        percentScrolled = pixelScrolled / visibleScrollValue;

                        if(settings.invert){
                            deltaTopScrollVal = percentScrolled * 100;
                            $this.css({'background-position': '50% '+deltaTopScrollVal+'%'});
                        }else{
                            deltaTopScrollVal = (1-percentScrolled) * 100;
                            $this.css({'background-position': '50% '+deltaTopScrollVal+'%'});
                        }
                    }
                }

            });
        },
        destroy    : function( ) { },
        reposition : function( ) { },
        update     : function( content ) { }
    };

    $.fn.ParallaxBackground = function( method ){ 
        
        if ( methods[method] ) {
            return methods[method].apply( this, Array.prototype.slice.call( arguments, 1 ));
        } else if ( typeof method === 'object' || ! method ) {
            return methods.init.apply( this, arguments );
        } else {
            $.error( 'Method with name ' +  method + ' is not exist for jQuery' );
        }
         
        
    }//end plugin
})(jQuery);