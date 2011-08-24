/**
 * jQuery bxSlider v3.0
 * http://bxslider.com
 *
 * Copyright 2010, Steven Wanderski
 * http://stevenwanderski.com
 *
 * Free to use and abuse under the MIT license.
 * http://www.opensource.org/licenses/mit-license.php
 * 
 */
(function($){$.fn.bxSlider=function(options){var defaults={mode:'horizontal',infiniteLoop:true,hideControlOnEnd:false,controls:true,speed:500,easing:'swing',pager:false,pagerSelector:null,pagerType:'full',pagerLocation:'bottom',pagerShortSeparator:'/',pagerActiveClass:'pager-active',nextText:'next',nextImage:'',nextSelector:null,prevText:'prev',prevImage:'',prevSelector:null,captions:false,captionsSelector:null,auto:false,autoDirection:'next',autoControls:false,autoControlsSelector:null,autoStart:true,autoHover:false,autoDelay:0,pause:3000,startText:'start',startImage:'',stopText:'stop',stopImage:'',ticker:false,tickerSpeed:5000,tickerDirection:'next',tickerHover:false,wrapperClass:'bx-wrapper',startingSlide:0,displaySlideQty:1,moveSlideQty:1,randomStart:false,onBeforeSlide:function(){},onAfterSlide:function(){},onLastSlide:function(){},onFirstSlide:function(){},onNextSlide:function(){},onPrevSlide:function(){},buildPager:null}
var options=$.extend(defaults,options);var base=this;var $parent='';var $origElement='';var $children='';var $outerWrapper='';var $firstChild='';var childrenWidth='';var childrenOuterWidth='';var wrapperWidth='';var wrapperHeight='';var $pager='';var interval='';var $autoControls='';var $stopHtml='';var $startContent='';var $stopContent='';var autoPlaying=true;var loaded=false;var childrenMaxWidth=0;var childrenMaxHeight=0;var currentSlide=0;var origLeft=0;var origTop=0;var origShowWidth=0;var origShowHeight=0;var tickerLeft=0;var tickerTop=0;var isWorking=false;var firstSlide=0;var lastSlide=$children.length-1;this.goToSlide=function(number,stopAuto){if(!isWorking){isWorking=true;currentSlide=number;options.onBeforeSlide(currentSlide,$children.length,$children.eq(currentSlide));if(typeof(stopAuto)=='undefined'){var stopAuto=true;}
if(stopAuto){if(options.auto){base.stopShow(true);}}
slide=number;if(slide==firstSlide){options.onFirstSlide(currentSlide,$children.length,$children.eq(currentSlide));}
if(slide==lastSlide){options.onLastSlide(currentSlide,$children.length,$children.eq(currentSlide));}
if(options.mode=='horizontal'){$parent.animate({'left':'-'+getSlidePosition(slide,'left')+'px'},options.speed,options.easing,function(){isWorking=false;options.onAfterSlide(currentSlide,$children.length,$children.eq(currentSlide));});}else if(options.mode=='vertical'){$parent.animate({'top':'-'+getSlidePosition(slide,'top')+'px'},options.speed,options.easing,function(){isWorking=false;options.onAfterSlide(currentSlide,$children.length,$children.eq(currentSlide));});}else if(options.mode=='fade'){setChildrenFade();}
checkEndControls();if(options.moveSlideQty>1){number=Math.floor(number/options.moveSlideQty);}
makeSlideActive(number);showCaptions();}}
this.goToNextSlide=function(stopAuto){if(typeof(stopAuto)=='undefined'){var stopAuto=true;}
if(stopAuto){if(options.auto){base.stopShow(true);}}
if(!options.infiniteLoop){if(!isWorking){var slideLoop=false;currentSlide=(currentSlide+(options.moveSlideQty));if(currentSlide<=lastSlide){checkEndControls();options.onNextSlide(currentSlide,$children.length,$children.eq(currentSlide));base.goToSlide(currentSlide);}else{currentSlide-=options.moveSlideQty;}}}else{if(!isWorking){isWorking=true;var slideLoop=false;currentSlide=(currentSlide+options.moveSlideQty);if(currentSlide>lastSlide){currentSlide=currentSlide%$children.length;slideLoop=true;}
options.onNextSlide(currentSlide,$children.length,$children.eq(currentSlide));options.onBeforeSlide(currentSlide,$children.length,$children.eq(currentSlide));if(options.mode=='horizontal'){var parentLeft=(options.moveSlideQty*childrenOuterWidth);$parent.animate({'left':'-='+parentLeft+'px'},options.speed,options.easing,function(){isWorking=false;if(slideLoop){$parent.css('left','-'+getSlidePosition(currentSlide,'left')+'px');}
options.onAfterSlide(currentSlide,$children.length,$children.eq(currentSlide));});}else if(options.mode=='vertical'){var parentTop=(options.moveSlideQty*childrenMaxHeight);$parent.animate({'top':'-='+parentTop+'px'},options.speed,options.easing,function(){isWorking=false;if(slideLoop){$parent.css('top','-'+getSlidePosition(currentSlide,'top')+'px');}
options.onAfterSlide(currentSlide,$children.length,$children.eq(currentSlide));});}else if(options.mode=='fade'){setChildrenFade();}
if(options.moveSlideQty>1){makeSlideActive(Math.ceil(currentSlide/options.moveSlideQty));}else{makeSlideActive(currentSlide);}
showCaptions();}}}
this.goToPreviousSlide=function(stopAuto){if(typeof(stopAuto)=='undefined'){var stopAuto=true;}
if(stopAuto){if(options.auto){base.stopShow(true);}}
if(!options.infiniteLoop){if(!isWorking){var slideLoop=false;currentSlide=currentSlide-options.moveSlideQty;if(currentSlide<0){currentSlide=0;if(options.hideControlOnEnd){$('.bx-prev',$outerWrapper).hide();}}
checkEndControls();options.onPrevSlide(currentSlide,$children.length,$children.eq(currentSlide));base.goToSlide(currentSlide);}}else{if(!isWorking){isWorking=true;var slideLoop=false;currentSlide=(currentSlide-(options.moveSlideQty));if(currentSlide<0){negativeOffset=(currentSlide%$children.length);if(negativeOffset==0){currentSlide=0;}else{currentSlide=($children.length)+negativeOffset;}
slideLoop=true;}
options.onPrevSlide(currentSlide,$children.length,$children.eq(currentSlide));options.onBeforeSlide(currentSlide,$children.length,$children.eq(currentSlide));if(options.mode=='horizontal'){var parentLeft=(options.moveSlideQty*childrenOuterWidth);$parent.animate({'left':'+='+parentLeft+'px'},options.speed,options.easing,function(){isWorking=false;if(slideLoop){$parent.css('left','-'+getSlidePosition(currentSlide,'left')+'px');}
options.onAfterSlide(currentSlide,$children.length,$children.eq(currentSlide));});}else if(options.mode=='vertical'){var parentTop=(options.moveSlideQty*childrenMaxHeight);$parent.animate({'top':'+='+parentTop+'px'},options.speed,options.easing,function(){isWorking=false;if(slideLoop){$parent.css('top','-'+getSlidePosition(currentSlide,'top')+'px');}
options.onAfterSlide(currentSlide,$children.length,$children.eq(currentSlide));});}else if(options.mode=='fade'){setChildrenFade();}
if(options.moveSlideQty>1){makeSlideActive(Math.ceil(currentSlide/options.moveSlideQty));}else{makeSlideActive(currentSlide);}
showCaptions();}}}
this.goToFirstSlide=function(stopAuto){if(typeof(stopAuto)=='undefined'){var stopAuto=true;}
base.goToSlide(firstSlide,stopAuto);}
this.goToLastSlide=function(){if(typeof(stopAuto)=='undefined'){var stopAuto=true;}
base.goToSlide(lastSlide,stopAuto);}
this.getCurrentSlide=function(){return currentSlide;}
this.getSlideCount=function(){return $children.length;}
this.stopShow=function(changeText){clearInterval(interval);if(typeof(changeText)=='undefined'){var changeText=true;}
if(changeText&&options.autoControls){$autoControls.html($startContent).removeClass('stop').addClass('start');autoPlaying=false;}}
this.startShow=function(changeText){if(typeof(changeText)=='undefined'){var changeText=true;}
setAutoInterval();if(changeText&&options.autoControls){$autoControls.html($stopContent).removeClass('start').addClass('stop');autoPlaying=true;}}
this.stopTicker=function(changeText){$parent.stop();if(typeof(changeText)=='undefined'){var changeText=true;}
if(changeText&&options.ticker){$autoControls.html($startContent).removeClass('stop').addClass('start');autoPlaying=false;}}
this.startTicker=function(changeText){if(options.mode=='horizontal'){if(options.tickerDirection=='next'){var stoppedLeft=parseInt($parent.css('left'));var remainingDistance=(origShowWidth+stoppedLeft)+$children.eq(0).width();}else if(options.tickerDirection=='prev'){var stoppedLeft=-parseInt($parent.css('left'));var remainingDistance=(stoppedLeft)-$children.eq(0).width();}
var finishingSpeed=(remainingDistance*options.tickerSpeed)/origShowWidth;moveTheShow(tickerLeft,remainingDistance,finishingSpeed);}else if(options.mode=='vertical'){if(options.tickerDirection=='next'){var stoppedTop=parseInt($parent.css('top'));var remainingDistance=(origShowHeight+stoppedTop)+$children.eq(0).height();}else if(options.tickerDirection=='prev'){var stoppedTop=-parseInt($parent.css('top'));var remainingDistance=(stoppedTop)-$children.eq(0).height();}
var finishingSpeed=(remainingDistance*options.tickerSpeed)/origShowHeight;moveTheShow(tickerTop,remainingDistance,finishingSpeed);if(typeof(changeText)=='undefined'){var changeText=true;}
if(changeText&&options.ticker){$autoControls.html($stopContent).removeClass('start').addClass('stop');autoPlaying=true;}}}
this.initShow=function(){$parent=$(this);$origElement=$parent.clone();$children=$parent.children();$outerWrapper='';$firstChild=$parent.children(':first');childrenWidth=$firstChild.width();childrenMaxWidth=0;childrenOuterWidth=$firstChild.outerWidth();childrenMaxHeight=0;wrapperWidth=getWrapperWidth();wrapperHeight=getWrapperHeight();isWorking=false;$pager='';currentSlide=0;origLeft=0;origTop=0;interval='';$autoControls='';$stopHtml='';$startContent='';$stopContent='';autoPlaying=true;loaded=false;origShowWidth=0;origShowHeight=0;tickerLeft=0;tickerTop=0;firstSlide=0;lastSlide=$children.length-1;$children.each(function(index){if($(this).outerHeight()>childrenMaxHeight){childrenMaxHeight=$(this).outerHeight();}
if($(this).outerWidth()>childrenMaxWidth){childrenMaxWidth=$(this).outerWidth();}});if(options.randomStart){var randomNumber=Math.floor(Math.random()*$children.length);currentSlide=randomNumber;origLeft=childrenOuterWidth*(options.moveSlideQty+randomNumber);origTop=childrenMaxHeight*(options.moveSlideQty+randomNumber);}else{currentSlide=options.startingSlide;origLeft=childrenOuterWidth*(options.moveSlideQty+options.startingSlide);origTop=childrenMaxHeight*(options.moveSlideQty+options.startingSlide);}
initCss();if(options.pager&&!options.ticker){if(options.pagerType=='full'){showPager('full');}else if(options.pagerType=='short'){showPager('short');}}
if(options.controls&&!options.ticker){setControlsVars();}
if(options.auto||options.ticker){if(options.autoControls){setAutoControlsVars();}
if(options.autoStart){setTimeout(function(){base.startShow(true);},options.autoDelay);}else{base.stopShow(true);}
if(options.autoHover&&!options.ticker){setAutoHover();}}
if(options.moveSlideQty>1){makeSlideActive(Math.ceil(currentSlide/options.moveSlideQty));}else{makeSlideActive(currentSlide);}
checkEndControls();if(options.captions){showCaptions();}
options.onAfterSlide(currentSlide,$children.length,$children.eq(currentSlide));}
this.destroyShow=function(){clearInterval(interval);$('.bx-next, .bx-prev, .bx-pager, .bx-auto',$outerWrapper).remove();$parent.unwrap().unwrap().removeAttr('style');$parent.children().removeAttr('style').not('.pager').remove();$children.removeClass('pager');}
this.reloadShow=function(){base.destroyShow();base.initShow();}
function initCss(){setChildrenLayout(options.startingSlide);if(options.mode=='horizontal'){$parent.wrap('<div class="'+options.wrapperClass+'" style="width:'+wrapperWidth+'px; position:relative;"></div>').wrap('<div class="bx-window" style="position:relative; overflow:hidden; width:'+wrapperWidth+'px;"></div>').css({width:'999999px',position:'relative',left:'-'+(origLeft)+'px'});$parent.children().css({width:childrenWidth,'float':'left',listStyle:'none'});$outerWrapper=$parent.parent().parent();$children.addClass('pager');}else if(options.mode=='vertical'){$parent.wrap('<div class="'+options.wrapperClass+'" style="width:'+childrenMaxWidth+'px; position:relative;"></div>').wrap('<div class="bx-window" style="width:'+childrenMaxWidth+'px; height:'+wrapperHeight+'px; position:relative; overflow:hidden;"></div>').css({height:'999999px',position:'relative',top:'-'+(origTop)+'px'});$parent.children().css({listStyle:'none',height:childrenMaxHeight});$outerWrapper=$parent.parent().parent();$children.addClass('pager');}else if(options.mode=='fade'){$parent.wrap('<div class="'+options.wrapperClass+'" style="width:'+childrenMaxWidth+'px; position:relative;"></div>').wrap('<div class="bx-window" style="height:'+childrenMaxHeight+'px; width:'+childrenMaxWidth+'px; position:relative; overflow:hidden;"></div>');$parent.children().css({listStyle:'none',position:'absolute',top:0,left:0,zIndex:98});$outerWrapper=$parent.parent().parent();$children.not(':eq('+currentSlide+')').fadeTo(0,0);$children.eq(currentSlide).css('zIndex',99);}
if(options.captions&&options.captionsSelector==null){$outerWrapper.append('<div class="bx-captions"></div>');}}
function setChildrenLayout(){if(options.mode=='horizontal'||options.mode=='vertical'){var $prependedChildren=getArraySample($children,0,options.moveSlideQty,'backward');$.each($prependedChildren,function(index){$parent.prepend($(this));});var totalNumberAfterWindow=($children.length+options.moveSlideQty)-1;var pagerExcess=$children.length-options.displaySlideQty;var numberToAppend=totalNumberAfterWindow-pagerExcess;var $appendedChildren=getArraySample($children,0,numberToAppend,'forward');if(options.infiniteLoop){$.each($appendedChildren,function(index){$parent.append($(this));});}}}
function setControlsVars(){if(options.nextImage!=''){nextContent=options.nextImage;nextType='image';}else{nextContent=options.nextText;nextType='text';}
if(options.prevImage!=''){prevContent=options.prevImage;prevType='image';}else{prevContent=options.prevText;prevType='text';}
showControls(nextType,nextContent,prevType,prevContent);}
function setAutoInterval(){if(options.auto){if(!options.infiniteLoop){if(options.autoDirection=='next'){interval=setInterval(function(){currentSlide+=options.moveSlideQty;if(currentSlide>lastSlide){currentSlide=currentSlide%$children.length;}
base.goToSlide(currentSlide,false);},options.pause);}else if(options.autoDirection=='prev'){interval=setInterval(function(){currentSlide-=options.moveSlideQty;if(currentSlide<0){negativeOffset=(currentSlide%$children.length);if(negativeOffset==0){currentSlide=0;}else{currentSlide=($children.length)+negativeOffset;}}
base.goToSlide(currentSlide,false);},options.pause);}}else{if(options.autoDirection=='next'){interval=setInterval(function(){base.goToNextSlide(false);},options.pause);}else if(options.autoDirection=='prev'){interval=setInterval(function(){base.goToPreviousSlide(false);},options.pause);}}}else if(options.ticker){options.tickerSpeed*=10;$('.pager',$outerWrapper).each(function(index){origShowWidth+=$(this).width();origShowHeight+=$(this).height();});if(options.tickerDirection=='prev'&&options.mode=='horizontal'){$parent.css('left','-'+(origShowWidth+origLeft)+'px');}else if(options.tickerDirection=='prev'&&options.mode=='vertical'){$parent.css('top','-'+(origShowHeight+origTop)+'px');}
if(options.mode=='horizontal'){tickerLeft=parseInt($parent.css('left'));moveTheShow(tickerLeft,origShowWidth,options.tickerSpeed);}else if(options.mode=='vertical'){tickerTop=parseInt($parent.css('top'));moveTheShow(tickerTop,origShowHeight,options.tickerSpeed);}
if(options.tickerHover){setTickerHover();}}}
function moveTheShow(leftCss,distance,speed){if(options.mode=='horizontal'){if(options.tickerDirection=='next'){$parent.animate({'left':'-='+distance+'px'},speed,'linear',function(){$parent.css('left',leftCss);moveTheShow(leftCss,origShowWidth,options.tickerSpeed);});}else if(options.tickerDirection=='prev'){$parent.animate({'left':'+='+distance+'px'},speed,'linear',function(){$parent.css('left',leftCss);moveTheShow(leftCss,origShowWidth,options.tickerSpeed);});}}else if(options.mode=='vertical'){if(options.tickerDirection=='next'){$parent.animate({'top':'-='+distance+'px'},speed,'linear',function(){$parent.css('top',leftCss);moveTheShow(leftCss,origShowHeight,options.tickerSpeed);});}else if(options.tickerDirection=='prev'){$parent.animate({'top':'+='+distance+'px'},speed,'linear',function(){$parent.css('top',leftCss);moveTheShow(leftCss,origShowHeight,options.tickerSpeed);});}}}
function setAutoControlsVars(){if(options.startImage!=''){startContent=options.startImage;startType='image';}else{startContent=options.startText;startType='text';}
if(options.stopImage!=''){stopContent=options.stopImage;stopType='image';}else{stopContent=options.stopText;stopType='text';}
showAutoControls(startType,startContent,stopType,stopContent);}
function setAutoHover(){$outerWrapper.find('.bx-window').hover(function(){if(autoPlaying){base.stopShow(false);}},function(){if(autoPlaying){base.startShow(false);}});}
function setTickerHover(){$parent.hover(function(){if(autoPlaying){base.stopTicker(false);}},function(){if(autoPlaying){base.startTicker(false);}});}
function setChildrenFade(){$children.not(':eq('+currentSlide+')').fadeTo(options.speed,0).css('zIndex',98);$children.eq(currentSlide).css('zIndex',99).fadeTo(options.speed,1,function(){isWorking=false;if(jQuery.browser.msie){$children.eq(currentSlide).get(0).style.removeAttribute('filter');}
options.onAfterSlide(currentSlide,$children.length,$children.eq(currentSlide));});};function makeSlideActive(number){if(options.pagerType=='full'&&options.pager){$('a',$pager).removeClass(options.pagerActiveClass);$('a',$pager).eq(number).addClass(options.pagerActiveClass);}else if(options.pagerType=='short'&&options.pager){$('.bx-pager-current',$pager).html(currentSlide+1);}}
function showControls(nextType,nextContent,prevType,prevContent){var $nextHtml=$('<a href="" class="bx-next"></a>');var $prevHtml=$('<a href="" class="bx-prev"></a>');if(nextType=='text'){$nextHtml.html(nextContent);}else{$nextHtml.html('<img src="'+nextContent+'" />');}
if(prevType=='text'){$prevHtml.html(prevContent);}else{$prevHtml.html('<img src="'+prevContent+'" />');}
if(options.prevSelector){$(options.prevSelector).append($prevHtml);}else{$outerWrapper.append($prevHtml);}
if(options.nextSelector){$(options.nextSelector).append($nextHtml);}else{$outerWrapper.append($nextHtml);}
$nextHtml.click(function(){base.goToNextSlide();return false;});$prevHtml.click(function(){base.goToPreviousSlide();return false;});}
function showPager(type){var pagerQty=$children.length;if(options.moveSlideQty>1){if($children.length%options.moveSlideQty!=0){pagerQty=Math.ceil($children.length/options.moveSlideQty);}else{pagerQty=$children.length/options.moveSlideQty;}}
var pagerString='';if(options.buildPager){for(var i=0;i<pagerQty;i++){pagerString+=options.buildPager(i,$children.eq(i*options.moveSlideQty));}}else if(type=='full'){for(var i=1;i<=pagerQty;i++){pagerString+='<a href="" class="pager-link pager-'+i+'">'+i+'</a>';}}else if(type=='short'){pagerString='<span class="bx-pager-current">'+(options.startingSlide+1)+'</span> '+options.pagerShortSeparator+' <span class="bx-pager-total">'+$children.length+'<span>';}
if(options.pagerSelector){$(options.pagerSelector).append(pagerString);$pager=$(options.pagerSelector);}else{var $pagerContainer=$('<div class="bx-pager"></div>');$pagerContainer.append(pagerString);if(options.pagerLocation=='top'){$outerWrapper.prepend($pagerContainer);}else if(options.pagerLocation=='bottom'){$outerWrapper.append($pagerContainer);}
$pager=$('.bx-pager',$outerWrapper);}
$pager.children().click(function(){if(options.pagerType=='full'){var slideIndex=$pager.children().index(this);if(options.moveSlideQty>1){slideIndex*=options.moveSlideQty;}
base.goToSlide(slideIndex);}
return false;});}
function showCaptions(){var caption=$('img',$children.eq(currentSlide)).attr('title');if(caption!=''){if(options.captionsSelector){$(options.captionsSelector).html(caption);}else{$('.bx-captions',$outerWrapper).html(caption);}}else{if(options.captionsSelector){$(options.captionsSelector).html(' ');}else{$('.bx-captions',$outerWrapper).html(' ');}}}
function showAutoControls(startType,startContent,stopType,stopContent){$autoControls=$('<a href="" class="bx-start"></a>');if(startType=='text'){$startContent=startContent;}else{$startContent='<img src="'+startContent+'" />';}
if(stopType=='text'){$stopContent=stopContent;}else{$stopContent='<img src="'+stopContent+'" />';}
if(options.autoControlsSelector){$(options.autoControlsSelector).append($autoControls);}else{$outerWrapper.append('<div class="bx-auto"></div>');$('.bx-auto',$outerWrapper).html($autoControls);}
$autoControls.click(function(){if(options.ticker){if($(this).hasClass('stop')){base.stopTicker();}else if($(this).hasClass('start')){base.startTicker();}}else{if($(this).hasClass('stop')){base.stopShow(true);}else if($(this).hasClass('start')){base.startShow(true);}}
return false;});}
function checkEndControls(){if(!options.infiniteLoop&&options.hideControlOnEnd){if(currentSlide==firstSlide){$('.bx-prev',$outerWrapper).hide();}else{$('.bx-prev',$outerWrapper).show();}
if(currentSlide==lastSlide){$('.bx-next',$outerWrapper).hide();}else{$('.bx-next',$outerWrapper).show();}}}
function getSlidePosition(number,side){if(side=='left'){var position=$('.pager',$outerWrapper).eq(number).position().left;}else if(side=='top'){var position=$('.pager',$outerWrapper).eq(number).position().top;}
return position;}
function getWrapperWidth(){var wrapperWidth=$firstChild.outerWidth()*options.displaySlideQty;return wrapperWidth;}
function getWrapperHeight(){var wrapperHeight=$firstChild.outerHeight()*options.displaySlideQty;return wrapperHeight;}
function getArraySample(array,start,length,direction){var sample=[];var loopLength=length;var startPopulatingArray=false;if(direction=='backward'){array=$.makeArray(array);array.reverse();}
while(loopLength>0){$.each(array,function(index,val){if(loopLength>0){if(!startPopulatingArray){if(index==start){startPopulatingArray=true;sample.push($(this).clone());loopLength--;}}else{sample.push($(this).clone());loopLength--;}}else{return false;}});}
return sample;}
this.each(function(){base.initShow();});return this;}
jQuery.fx.prototype.cur=function(){if(this.elem[this.prop]!=null&&(!this.elem.style||this.elem.style[this.prop]==null)){return this.elem[this.prop];}
var r=parseFloat(jQuery.css(this.elem,this.prop));return r;}})(jQuery);