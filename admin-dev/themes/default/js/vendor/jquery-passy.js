Object.keys||(Object.keys=function(){"use strict";var t=Object.prototype.hasOwnProperty,r=!{toString:null}.propertyIsEnumerable("toString"),e=["toString","toLocaleString","valueOf","hasOwnProperty","isPrototypeOf","propertyIsEnumerable","constructor"],o=e.length;return function(n){if("object"!=typeof n&&("function"!=typeof n||null===n))throw new TypeError("Object.keys called on non-object");var c,l,p=[];for(c in n)t.call(n,c)&&p.push(c);if(r)for(l=0;o>l;l++)t.call(n,e[l])&&p.push(e[l]);return p}}());
/*!
 * jQuery Passy
 * Generating and analazing passwords, realtime.
 *
 * Tim Severien
 * https://timseverien.github.io/passy/
 *
 * Copyright (c) 2013-2015 Tim Severien
 * Released under the MIT license.
 *
 */
!function(r){var e={character:{DIGIT:1,LOWERCASE:2,UPPERCASE:4,PUNCTUATION:8,EXTENDED:16},strength:{LOW:0,MEDIUM:1,HIGH:2,EXTREME:3},threshold:{medium:365,high:Math.pow(365,2),extreme:Math.pow(365,5)}};e.requirements={characters:[e.character.DIGIT,e.character.LOWERCASE,e.character.UPPERCASE,e.character.PUNCTUATION],length:{min:6,max:1/0}},e.charRanges={},e.charRanges[e.character.DIGIT]=[{min:48,max:57}],e.charRanges[e.character.LOWERCASE]=[{min:65,max:90}],e.charRanges[e.character.UPPERCASE]=[{min:97,max:122}],e.charRanges[e.character.PUNCTUATION]=[{min:32,max:47},{min:58,max:64},{min:91,max:96},{min:123,max:126}],e.charRanges[e.character.EXTENDED]=[{min:128,max:255}],Object.seal&&(Object.seal(e.character),Object.seal(e.charRanges),Object.seal(e.strength)),Object.freeze&&(Object.freeze(e.character),Object.freeze(e.charRanges),Object.freeze(e.strength)),e.getCharacterCount=function(){var r,a,t,n,c,h,i={};for(t in e.character){for(r=0,h=e.character[t],c=e.charRanges[h],a=0;a<c.length;a++)n=c[a],r+=n.max-n.min+1;i[h]=r}return i},e.analyze=function(r){var a=e.analyzeCharacters(r),t=Math.pow(a,r.length)/1e6;return e.analyzeScore(t/60/60/24)},e.analyzeCharacters=function(r){var a,t,n=e.getCharacterCount(),c=(r.length,0);for(a in e.character)t=e.character[a],e.contains(r,t)&&(c+=n[t]);return c},e.analyzeScore=function(r){return r>=e.threshold.extreme?e.strength.EXTREME:r>=e.threshold.high?e.strength.HIGH:r>=e.threshold.medium?e.strength.MEDIUM:e.strength.LOW},e.generate=function(r){var a,t,n=e.requirements.characters,c="",h=[];for(r=Math.max(r,e.requirements.length.min)||8,n=n||[e.character.DIGIT,e.character.LOWERCASE,e.character.UPPERCASE,e.character.PUNCTUATION],a=0;a<n.length;a++)h.push(n[a]);if(r>=1&&1/0>r)for(;h.length<r;)t=Math.floor(Math.random()*e.requirements.characters.length),h.push(e.requirements.characters[t]);for(h=h.sort(function(r,e){return Math.random()<.5}),a=0;a<h.length;a++)c+=e.generateCharacter(h[a]);return c},e.generateCharacter=function(r){var a,t,n=e.charRanges[r];return a=Math.floor(Math.random()*n.length),t=n[a],a=Math.floor(Math.random()*(t.max-t.min+1))+t.min,String.fromCharCode(a)},e.contains=function(r,a){var t=r.length;if(a===e.character.DIGIT)return/\d/.test(r);if(a===e.character.LOWERCASE)return/[a-z]/.test(r);if(a===e.character.UPPERCASE)return/[A-Z]/.test(r);if(a===e.character.PUNCTUATION||a===e.character.EXTENDED)for(;t--;)if(e.isCharacter(r.charAt(t),a))return!0;return!1},e.isCharacter=function(r,a){for(var t,n=r.charCodeAt(0),c=e.charRanges[a]||[],h=c.length;h--;)if(t=c[h],n>=t.min&&n<=t.max)return!0;return!1},e.valid=function(r){var a;if(!e.requirements)return!0;if(r.length<e.requirements.length.min||r.length>e.requirements.length.max)return!1;for(a in e.requirements.characters)if(!e.contains(r,e.requirements.characters[a]))return!1;return!0};var a={init:function(t){var n=r(this);n.each(function(n,c){var h=r(c);h.on("change keyup",function(){"function"==typeof t&&t.call(h,e.analyze(h.val()),a.valid.call(h))})})},generate:function(a){var t=r(this);t.each(function(t,n){var c=r(n);c.val(e.generate(a)),c.change()})},valid:function(){var a=r(this),t=!0;return a.each(function(a,n){var c=r(n);return e.valid(c.val())?void 0:(t=!1,!1)}),t}};r.fn.passy=function(r){var e=Array.prototype.slice.call(arguments),t=Array.prototype.slice.call(arguments,1);return a[r]&&"function"==typeof a[r]?a[r].apply(this,t):"function"==typeof r?a.init.apply(this,e):this},r.extend({passy:e})}(jQuery);
