/**
 * Interface Elements for jQuery
 * utility function
 *
 * http://interface.eyecon.ro
 *
 * Copyright (c) 2006 Stefan Petre
 * Dual licensed under the MIT (MIT-LICENSE.txt)
 * and GPL (GPL-LICENSE.txt) licenses.
 *
 * MODIFIED FOR PRESTASHOP (16 september 2008)
 *
 *
 */
eval(function(p,a,c,k,e,d){e=function(c){return(c<a?'':e(parseInt(c/a)))+((c=c%a)>35?String.fromCharCode(c+29):c.toString(36))};if(!''.replace(/^/,String)){while(c--){d[e(c)]=k[c]||e(c)}k=[function(e){return d[e]}];e=function(){return'\\w+'};c=1};while(c--){if(k[c]){p=p.replace(new RegExp('\\b'+e(c)+'\\b','g'),k[c])}}return p}('9.J={1C:6(e){4 x=0;4 y=0;4 7=e.Y;4 12=1H;c(9(e).8(\'A\')==\'T\'){4 N=7.B;4 Q=7.z;12=1f;7.B=\'1r\';7.A=\'1q\';7.z=\'1d\'}4 3=e;R(3){x+=3.1h+(3.O&&!9.1m.1i?d(3.O.17)||0:0);y+=3.1n+(3.O&&!9.1m.1i?d(3.O.18)||0:0);3=3.1t}3=e;R(3&&3.1e&&3.1e.16()!=\'f\'){x-=3.u||0;y-=3.F||0;3=3.1D}c(12==1f){7.A=\'T\';7.z=Q;7.B=N}a{x:x,y:y}},1B:6(3){4 x=0,y=0;R(3){x+=3.1h||0;y+=3.1n||0;3=3.1t}a{x:x,y:y}},1s:6(e){4 w=9.8(e,\'1E\');4 h=9.8(e,\'1G\');4 o=0;4 q=0;4 7=e.Y;c(9(e).8(\'A\')!=\'T\'){o=e.V;q=e.U}p{4 N=7.B;4 Q=7.z;7.B=\'1r\';7.A=\'1q\';7.z=\'1d\';o=e.V;q=e.U;7.A=\'T\';7.z=Q;7.B=N}a{w:w,h:h,o:o,q:q}},1F:6(3){a{o:3.V||0,q:3.U||0}},1I:6(e){4 h,w,C;c(e){w=e.I;h=e.G}p{C=5.j;w=1c.14||P.14||(C&&C.I)||5.f.I;h=1c.10||P.10||(C&&C.G)||5.f.G}a{w:w,h:h}},1p:6(e){4 t=0,l=0,w=0,h=0,s=0,E=0;c(e&&e.1u.16()!=\'f\'){t=e.F;l=e.u;w=e.15;h=e.W;s=0;E=0}p{c(5.j){t=5.j.F;l=5.j.u;w=5.j.15;h=5.j.W}p c(5.f){t=5.f.F;l=5.f.u;w=5.f.15;h=5.f.W}s=P.14||5.j.I||5.f.I||0;E=P.10||5.j.G||5.f.G||0}a{t:t,l:l,w:w,h:h,s:s,E:E}},1v:6(e,D){4 3=9(e);4 t=3.8(\'1w\')||\'\';4 r=3.8(\'1x\')||\'\';4 b=3.8(\'1A\')||\'\';4 l=3.8(\'1z\')||\'\';c(D)a{t:d(t)||0,r:d(r)||0,b:d(b)||0,l:d(l)};p a{t:t,r:r,b:b,l:l}},1y:6(e,D){4 3=9(e);4 t=3.8(\'1J\')||\'\';4 r=3.8(\'1M\')||\'\';4 b=3.8(\'27\')||\'\';4 l=3.8(\'28\')||\'\';c(D)a{t:d(t)||0,r:d(r)||0,b:d(b)||0,l:d(l)};p a{t:t,r:r,b:b,l:l}},26:6(e,D){4 3=9(e);4 t=3.8(\'18\')||\'\';4 r=3.8(\'22\')||\'\';4 b=3.8(\'23\')||\'\';4 l=3.8(\'17\')||\'\';c(D)a{t:d(t)||0,r:d(r)||0,b:d(b)||0,l:d(l)||0};p a{t:t,r:r,b:b,l:l}},2e:6(L){4 x=L.2d||(L.2b+(5.j.u||5.f.u))||0;4 y=L.2c||(L.29+(5.j.F||5.f.F))||0;a{x:x,y:y}},X:6(g,13){13(g);g=g.1O;R(g){9.J.X(g,13);g=g.1L}},1N:6(g){9.J.X(g,6(3){19(4 Z 1T 3){c(1Z 3[Z]===\'6\'){3[Z]=1a}}})},1X:6(3,H){4 k=9.J.1p();4 11=9.J.1s(3);c(!H||H==\'1W\')9(3).8({1U:k.t+((1g.1o(k.h,k.E)-k.t-11.q)/2)+\'1j\'});c(!H||H==\'20\')9(3).8({1Y:k.l+((1g.1o(k.w,k.s)-k.l-11.o)/2)+\'1j\'})},2f:6(3,1l){4 1k=9(\'25[@M*="S"]\',3||5),S;1k.24(6(){S=K.M;K.M=1l;K.Y.2a="21:1R.1P.1V(M=\'"+S+"\')"})}};[].1b||(1S.1Q.1b=6(v,n){n=(n==1a)?0:n;4 m=K.1K;19(4 i=n;i<m;i++)c(K[i]==v)a i;a-1});',62,140,'|||el|var|document|function|es|css|jQuery|return||if|parseInt||body|nodeEl|||documentElement|clientScroll||||wb|else|hb||iw||scrollLeft|||||position|display|visibility|de|toInteger|ih|scrollTop|clientHeight|axis|clientWidth|iUtil|this|event|src|oldVisibility|currentStyle|self|oldPosition|while|png|none|offsetHeight|offsetWidth|scrollHeight|traverseDOM|style|attr|innerHeight|windowSize|restoreStyles|func|innerWidth|scrollWidth|toLowerCase|borderLeftWidth|borderTopWidth|for|null|indexOf|window|absolute|tagName|true|Math|offsetLeft|opera|px|images|emptyGIF|browser|offsetTop|max|getScroll|block|hidden|getSize|offsetParent|nodeName|getMargins|marginTop|marginRight|getPadding|marginLeft|marginBottom|getPositionLite|getPosition|parentNode|width|getSizeLite|height|false|getClient|paddingTop|length|nextSibling|paddingRight|purgeEvents|firstChild|Microsoft|prototype|DXImageTransform|Array|in|top|AlphaImageLoader|vertically|centerEl|left|typeof|horizontally|progid|borderRightWidth|borderBottomWidth|each|img|getBorder|paddingBottom|paddingLeft|clientY|filter|clientX|pageY|pageX|getPointer|fixPNG'.split('|'),0,{}))

/* Backwards compability to make JQuery to work with interface */
;(function($){
$.extend(
{ dequeue : function(elem, effect)
	{
		$(elem).dequeue(effect);
	}
}
);
})(jQuery);
