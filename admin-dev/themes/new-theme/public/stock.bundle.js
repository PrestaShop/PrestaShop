<<<<<<< HEAD
!function(t){function e(r){if(n[r])return n[r].exports;var o=n[r]={i:r,l:!1,exports:{}};return t[r].call(o.exports,o,o.exports,e),o.l=!0,o.exports}var n={};e.m=t,e.c=n,e.i=function(t){return t},e.d=function(t,n,r){e.o(t,n)||Object.defineProperty(t,n,{configurable:!1,enumerable:!0,get:r})},e.n=function(t){var n=t&&t.__esModule?function(){return t.default}:function(){return t};return e.d(n,"a",n),n},e.o=function(t,e){return Object.prototype.hasOwnProperty.call(t,e)},e.p="",e(e.s=89)}({14:function(t,e,n){"use strict";function r(t){return t&&t.__esModule?t:{default:t}}Object.defineProperty(e,"__esModule",{value:!0});var o=n(6),i=r(o),a=n(86),s=r(a);i.default.use(s.default);var u={products:[]},c={addProducts:function(t,e){t.products=e}},l={},f={};e.default=new s.default.Store({state:u,getters:f,actions:l,mutations:c})},16:function(t,e,n){"use strict";var r=n(84)(n(28),n(85),null,null);r.options.__file="/Users/nMartin/Documents/PrestaShop/admin-dev/themes/new-theme/js/stock-page/components/app.vue",r.esModule&&Object.keys(r.esModule).some(function(t){return"default"!==t&&"__esModule"!==t}),r.options.functional,t.exports=r.exports},27:function(t,e,n){"use strict";function r(t){this.state=z,this.value=void 0,this.deferred=[];var e=this;try{t(function(t){e.resolve(t)},function(t){e.reject(t)})}catch(t){e.reject(t)}}function o(t,e){t instanceof Promise?this.promise=t:this.promise=new Promise(t.bind(e)),this.context=e}function i(t){}function a(t){}function s(t,e){return W(t,e)}function u(t){return t?t.replace(/^\s*|\s*$/g,""):""}function c(t){return t?t.toLowerCase():""}function l(t){return t?t.toUpperCase():""}function f(t){return"string"==typeof t}function p(t){return"function"==typeof t}function d(t){return null!==t&&"object"==typeof t}function v(t){return d(t)&&Object.getPrototypeOf(t)==Object.prototype}function h(t){return"undefined"!=typeof Blob&&t instanceof Blob}function m(t){return"undefined"!=typeof FormData&&t instanceof FormData}function y(t,e,n){var r=o.resolve(t);return arguments.length<2?r:r.then(e,n)}function g(t,e,n){return n=n||{},p(n)&&(n=n.call(e)),b(t.bind({$vm:e,$options:n}),t,{$options:n})}function _(t,e){var n,r;if(rt(t))for(n=0;n<t.length;n++)e.call(t[n],t[n],n);else if(d(t))for(r in t)X.call(t,r)&&e.call(t[r],t[r],r);return t}function b(t){return Q.call(arguments,1).forEach(function(e){$(t,e,!0)}),t}function w(t){return Q.call(arguments,1).forEach(function(e){for(var n in e)void 0===t[n]&&(t[n]=e[n])}),t}function E(t){return Q.call(arguments,1).forEach(function(e){$(t,e)}),t}function $(t,e,n){for(var r in e)n&&(v(e[r])||rt(e[r]))?(v(e[r])&&!v(t[r])&&(t[r]={}),rt(e[r])&&!rt(t[r])&&(t[r]=[]),$(t[r],e[r],n)):void 0!==e[r]&&(t[r]=e[r])}function x(t,e,n){var r=O(t),o=r.expand(e);return n&&n.push.apply(n,r.vars),o}function O(t){var e=["+","#",".","/",";","?","&"],n=[];return{vars:n,expand:function(r){return t.replace(/\{([^\{\}]+)\}|([^\{\}]+)/g,function(t,o,i){if(o){var a=null,s=[];if(e.indexOf(o.charAt(0))!==-1&&(a=o.charAt(0),o=o.substr(1)),o.split(/,/g).forEach(function(t){var e=/([^:\*]*)(?::(\d+)|(\*))?/.exec(t);s.push.apply(s,N(r,a,e[1],e[2]||e[3])),n.push(e[1])}),a&&"+"!==a){var u=",";return"?"===a?u="&":"#"!==a&&(u=a),(0!==s.length?a:"")+s.join(u)}return s.join(",")}return T(i)})}}}function N(t,e,n,r){var o=t[n],i=[];if(k(o)&&""!==o)if("string"==typeof o||"number"==typeof o||"boolean"==typeof o)o=o.toString(),r&&"*"!==r&&(o=o.substring(0,parseInt(r,10))),i.push(A(e,o,C(e)?n:null));else if("*"===r)Array.isArray(o)?o.filter(k).forEach(function(t){i.push(A(e,t,C(e)?n:null))}):Object.keys(o).forEach(function(t){k(o[t])&&i.push(A(e,o[t],t))});else{var a=[];Array.isArray(o)?o.filter(k).forEach(function(t){a.push(A(e,t))}):Object.keys(o).forEach(function(t){k(o[t])&&(a.push(encodeURIComponent(t)),a.push(A(e,o[t].toString())))}),C(e)?i.push(encodeURIComponent(n)+"="+a.join(",")):0!==a.length&&i.push(a.join(","))}else";"===e?i.push(encodeURIComponent(n)):""!==o||"&"!==e&&"?"!==e?""===o&&i.push(""):i.push(encodeURIComponent(n)+"=");return i}function k(t){return void 0!==t&&null!==t}function C(t){return";"===t||"&"===t||"?"===t}function A(t,e,n){return e="+"===t||"#"===t?T(e):encodeURIComponent(e),n?encodeURIComponent(n)+"="+e:e}function T(t){return t.split(/(%[0-9A-Fa-f]{2})/g).map(function(t){return/%[0-9A-Fa-f]/.test(t)||(t=encodeURI(t)),t}).join("")}function D(t,e){var n,r=this||{},o=t;return f(t)&&(o={url:t,params:e}),o=b({},D.options,r.$options,o),D.transforms.forEach(function(t){n=S(t,n,r.$vm)}),n(o)}function S(t,e,n){return function(r){return t.call(n,r,e)}}function j(t,e,n){var r,o=rt(e),i=v(e);_(e,function(e,a){r=d(e)||rt(e),n&&(a=n+"["+(i||r?a:"")+"]"),!n&&o?t.add(e.name,e.value):r?j(t,e,a):t.add(a,e)})}function V(t){var e=t.match(/^\[|^\{(?!\{)/),n={"[":/]$/,"{":/}$/};return e&&n[e[0]].test(t)}function M(t,e){e((t.client||(et?gt:_t))(t))}function P(t,e){return Object.keys(t).reduce(function(t,n){return c(e)===c(n)?n:t},null)}function I(t){if(/[^a-z0-9\-#$%&'*+.\^_`|~]/i.test(t))throw new TypeError("Invalid character in header field name");return u(t)}function L(t){return new o(function(e){var n=new FileReader;n.readAsText(t),n.onload=function(){e(n.result)}})}function R(t){return 0===t.type.indexOf("text")||t.type.indexOf("json")!==-1}function U(t){var e=this||{},n=bt(e.$vm);return w(t||{},e.$options,U.options),U.interceptors.forEach(function(t){n.use(t)}),n(new $t(t)).then(function(t){return t.ok?t:o.reject(t)},function(t){return t instanceof Error&&a(t),o.reject(t)})}function F(t,e,n,r){var o=this||{},i={};return n=ot({},F.actions,n),_(n,function(n,a){n=b({url:t,params:ot({},e)},r,n),i[a]=function(){return(o.$http||U)(H(n,arguments))}}),i}function H(t,e){var n,r=ot({},t),o={};switch(e.length){case 2:o=e[0],n=e[1];break;case 1:/^(POST|PUT|PATCH)$/i.test(r.method)?n=e[0]:o=e[0];break;case 0:break;default:throw"Expected up to 2 arguments [params, body], got "+e.length+" arguments"}return r.body=n,r.params=ot({},r.params,o),r}function B(t){B.installed||(nt(t),t.url=D,t.http=U,t.resource=F,t.Promise=o,Object.defineProperties(t.prototype,{$url:{get:function(){return g(t.url,this,this.$options.url)}},$http:{get:function(){return g(t.http,this,this.$options.http)}},$resource:{get:function(){return t.resource.bind(this)}},$promise:{get:function(){var e=this;return function(n){return new t.Promise(n,e)}}}}))}var q=0,J=1,z=2;r.reject=function(t){return new r(function(e,n){n(t)})},r.resolve=function(t){return new r(function(e,n){e(t)})},r.all=function(t){return new r(function(e,n){function o(n){return function(r){a[n]=r,(i+=1)===t.length&&e(a)}}var i=0,a=[];0===t.length&&e(a);for(var s=0;s<t.length;s+=1)r.resolve(t[s]).then(o(s),n)})},r.race=function(t){return new r(function(e,n){for(var o=0;o<t.length;o+=1)r.resolve(t[o]).then(e,n)})};var G=r.prototype;G.resolve=function(t){var e=this;if(e.state===z){if(t===e)throw new TypeError("Promise settled with itself.");var n=!1;try{var r=t&&t.then;if(null!==t&&"object"==typeof t&&"function"==typeof r)return void r.call(t,function(t){n||e.resolve(t),n=!0},function(t){n||e.reject(t),n=!0})}catch(t){return void(n||e.reject(t))}e.state=q,e.value=t,e.notify()}},G.reject=function(t){var e=this;if(e.state===z){if(t===e)throw new TypeError("Promise settled with itself.");e.state=J,e.value=t,e.notify()}},G.notify=function(){var t=this;s(function(){if(t.state!==z)for(;t.deferred.length;){var e=t.deferred.shift(),n=e[0],r=e[1],o=e[2],i=e[3];try{t.state===q?o("function"==typeof n?n.call(void 0,t.value):t.value):t.state===J&&("function"==typeof r?o(r.call(void 0,t.value)):i(t.value))}catch(t){i(t)}}})},G.then=function(t,e){var n=this;return new r(function(r,o){n.deferred.push([t,e,r,o]),n.notify()})},G.catch=function(t){return this.then(void 0,t)},"undefined"==typeof Promise&&(window.Promise=r),o.all=function(t,e){return new o(Promise.all(t),e)},o.resolve=function(t,e){return new o(Promise.resolve(t),e)},o.reject=function(t,e){return new o(Promise.reject(t),e)},o.race=function(t,e){return new o(Promise.race(t),e)};var K=o.prototype;K.bind=function(t){return this.context=t,this},K.then=function(t,e){return t&&t.bind&&this.context&&(t=t.bind(this.context)),e&&e.bind&&this.context&&(e=e.bind(this.context)),new o(this.promise.then(t,e),this.context)},K.catch=function(t){return t&&t.bind&&this.context&&(t=t.bind(this.context)),new o(this.promise.catch(t),this.context)},K.finally=function(t){return this.then(function(e){return t.call(this),e},function(e){return t.call(this),Promise.reject(e)})};var W,Z={},X=Z.hasOwnProperty,Y=[],Q=Y.slice,tt=!1,et="undefined"!=typeof window,nt=function(t){var e=t.config;W=t.nextTick,tt=e.debug||!e.silent},rt=Array.isArray,ot=Object.assign||E,it=function(t,e){var n=e(t);return f(t.root)&&!n.match(/^(https?:)?\//)&&(n=t.root+"/"+n),n},at=function(t,e){var n=Object.keys(D.options.params),r={},o=e(t);return _(t.params,function(t,e){n.indexOf(e)===-1&&(r[e]=t)}),r=D.params(r),r&&(o+=(o.indexOf("?")==-1?"?":"&")+r),o},st=function(t){var e=[],n=x(t.url,t.params,e);return e.forEach(function(e){delete t.params[e]}),n};D.options={url:"",root:null,params:{}},D.transforms=[st,at,it],D.params=function(t){var e=[],n=encodeURIComponent;return e.add=function(t,e){p(e)&&(e=e()),null===e&&(e=""),this.push(n(t)+"="+n(e))},j(e,t),e.join("&").replace(/%20/g,"+")},D.parse=function(t){var e=document.createElement("a");return document.documentMode&&(e.href=t,t=e.href),e.href=t,{href:e.href,protocol:e.protocol?e.protocol.replace(/:$/,""):"",port:e.port,host:e.host,hostname:e.hostname,pathname:"/"===e.pathname.charAt(0)?e.pathname:"/"+e.pathname,search:e.search?e.search.replace(/^\?/,""):"",hash:e.hash?e.hash.replace(/^#/,""):""}};var ut=function(t){return new o(function(e){var n=new XDomainRequest,r=function(r){var o=r.type,i=0;"load"===o?i=200:"error"===o&&(i=500),e(t.respondWith(n.responseText,{status:i}))};t.abort=function(){return n.abort()},n.open(t.method,t.getUrl()),t.timeout&&(n.timeout=t.timeout),n.onload=r,n.onabort=r,n.onerror=r,n.ontimeout=r,n.onprogress=function(){},n.send(t.getBody())})},ct=et&&"withCredentials"in new XMLHttpRequest,lt=function(t,e){if(et){var n=D.parse(location.href),r=D.parse(t.getUrl());r.protocol===n.protocol&&r.host===n.host||(t.crossOrigin=!0,t.emulateHTTP=!1,ct||(t.client=ut))}e()},ft=function(t,e){m(t.body)?t.headers.delete("Content-Type"):(d(t.body)||rt(t.body))&&(t.emulateJSON?(t.body=D.params(t.body),t.headers.set("Content-Type","application/x-www-form-urlencoded")):t.body=JSON.stringify(t.body)),e(function(t){return Object.defineProperty(t,"data",{get:function(){return this.body},set:function(t){this.body=t}}),t.bodyText?y(t.text(),function(e){if(0===(t.headers.get("Content-Type")||"").indexOf("application/json")||V(e))try{t.body=JSON.parse(e)}catch(e){t.body=null}else t.body=e;return t}):t})},pt=function(t){return new o(function(e){var n,r,o=t.jsonp||"callback",i=t.jsonpCallback||"_jsonp"+Math.random().toString(36).substr(2),a=null;n=function(n){var o=n.type,s=0;"load"===o&&null!==a?s=200:"error"===o&&(s=500),s&&window[i]&&(delete window[i],document.body.removeChild(r)),e(t.respondWith(a,{status:s}))},window[i]=function(t){a=JSON.stringify(t)},t.abort=function(){n({type:"abort"})},t.params[o]=i,t.timeout&&setTimeout(t.abort,t.timeout),r=document.createElement("script"),r.src=t.getUrl(),r.type="text/javascript",r.async=!0,r.onload=n,r.onerror=n,document.body.appendChild(r)})},dt=function(t,e){"JSONP"==t.method&&(t.client=pt),e()},vt=function(t,e){p(t.before)&&t.before.call(this,t),e()},ht=function(t,e){t.emulateHTTP&&/^(PUT|PATCH|DELETE)$/i.test(t.method)&&(t.headers.set("X-HTTP-Method-Override",t.method),t.method="POST"),e()},mt=function(t,e){_(ot({},U.headers.common,t.crossOrigin?{}:U.headers.custom,U.headers[c(t.method)]),function(e,n){t.headers.has(n)||t.headers.set(n,e)}),e()},yt="undefined"!=typeof Blob&&"undefined"!=typeof FileReader,gt=function(t){return new o(function(e){var n=new XMLHttpRequest,r=function(r){var o=t.respondWith("response"in n?n.response:n.responseText,{status:1223===n.status?204:n.status,statusText:1223===n.status?"No Content":u(n.statusText)});_(u(n.getAllResponseHeaders()).split("\n"),function(t){o.headers.append(t.slice(0,t.indexOf(":")),t.slice(t.indexOf(":")+1))}),e(o)};t.abort=function(){return n.abort()},t.progress&&("GET"===t.method?n.addEventListener("progress",t.progress):/^(POST|PUT)$/i.test(t.method)&&n.upload.addEventListener("progress",t.progress)),n.open(t.method,t.getUrl(),!0),t.timeout&&(n.timeout=t.timeout),t.credentials===!0&&(n.withCredentials=!0),t.crossOrigin||t.headers.set("X-Requested-With","XMLHttpRequest"),"responseType"in n&&yt&&(n.responseType="blob"),t.headers.forEach(function(t,e){n.setRequestHeader(e,t)}),n.onload=r,n.onabort=r,n.onerror=r,n.ontimeout=r,n.send(t.getBody())})},_t=function(t){var e=n(88);return new o(function(n){var r,o=t.getUrl(),i=t.getBody(),a=t.method,s={};t.headers.forEach(function(t,e){s[e]=t}),e(o,{body:i,method:a,headers:s}).then(r=function(e){var r=t.respondWith(e.body,{status:e.statusCode,statusText:u(e.statusMessage)});_(e.headers,function(t,e){r.headers.set(e,t)}),n(r)},function(t){return r(t.response)})})},bt=function(t){function e(e){return new o(function(o){function s(){n=r.pop(),p(n)?n.call(t,e,u):(i("Invalid interceptor of type "+typeof n+", must be a function"),u())}function u(e){if(p(e))a.unshift(e);else if(d(e))return a.forEach(function(n){e=y(e,function(e){return n.call(t,e)||e})}),void y(e,o);s()}s()},t)}var n,r=[M],a=[];return d(t)||(t=null),e.use=function(t){r.push(t)},e},wt=function(t){var e=this;this.map={},_(t,function(t,n){return e.append(n,t)})};wt.prototype.has=function(t){return null!==P(this.map,t)},wt.prototype.get=function(t){var e=this.map[P(this.map,t)];return e?e.join():null},wt.prototype.getAll=function(t){return this.map[P(this.map,t)]||[]},wt.prototype.set=function(t,e){this.map[I(P(this.map,t)||t)]=[u(e)]},wt.prototype.append=function(t,e){var n=this.map[P(this.map,t)];n?n.push(u(e)):this.set(t,e)},wt.prototype.delete=function(t){delete this.map[P(this.map,t)]},wt.prototype.deleteAll=function(){this.map={}},wt.prototype.forEach=function(t,e){var n=this;_(this.map,function(r,o){_(r,function(r){return t.call(e,r,o,n)})})};var Et=function(t,e){var n=e.url,r=e.headers,o=e.status,i=e.statusText;this.url=n,this.ok=o>=200&&o<300,this.status=o||0,this.statusText=i||"",this.headers=new wt(r),this.body=t,f(t)?this.bodyText=t:h(t)&&(this.bodyBlob=t,R(t)&&(this.bodyText=L(t)))};Et.prototype.blob=function(){return y(this.bodyBlob)},Et.prototype.text=function(){return y(this.bodyText)},Et.prototype.json=function(){return y(this.text(),function(t){return JSON.parse(t)})};var $t=function(t){this.body=null,this.params={},ot(this,t,{method:l(t.method||"GET")}),this.headers instanceof wt||(this.headers=new wt(this.headers))};$t.prototype.getUrl=function(){return D(this)},$t.prototype.getBody=function(){return this.body},$t.prototype.respondWith=function(t,e){return new Et(t,ot(e||{},{url:this.getUrl()}))};var xt={Accept:"application/json, text/plain, */*"},Ot={"Content-Type":"application/json;charset=utf-8"};U.options={},U.headers={put:Ot,post:Ot,patch:Ot,delete:Ot,common:xt,custom:{}},U.interceptors=[vt,ht,ft,dt,mt,lt],["get","delete","head","jsonp"].forEach(function(t){U[t]=function(e,n){return this(ot(n||{},{url:e,method:t}))}}),["post","put","patch"].forEach(function(t){U[t]=function(e,n,r){return this(ot(r||{},{url:e,method:t,body:n}))}}),F.actions={get:{method:"GET"},save:{method:"POST"},query:{method:"GET"},update:{method:"PUT"},remove:{method:"DELETE"},delete:{method:"DELETE"}},"undefined"!=typeof window&&window.Vue&&window.Vue.use(B),t.exports=B},28:function(t,e,n){"use strict";Object.defineProperty(e,"__esModule",{value:!0}),e.default={name:"app",computed:{products:function(){return this.$store.state.products}}}},4:function(t,e){var n;n=function(){return this}();try{n=n||Function("return this")()||(0,eval)("this")}catch(t){"object"==typeof window&&(n=window)}t.exports=n},5:function(t,e){function n(){throw new Error("setTimeout has not been defined")}function r(){throw new Error("clearTimeout has not been defined")}function o(t){if(l===setTimeout)return setTimeout(t,0);if((l===n||!l)&&setTimeout)return l=setTimeout,setTimeout(t,0);try{return l(t,0)}catch(e){try{return l.call(null,t,0)}catch(e){return l.call(this,t,0)}}}function i(t){if(f===clearTimeout)return clearTimeout(t);if((f===r||!f)&&clearTimeout)return f=clearTimeout,clearTimeout(t);try{return f(t)}catch(e){try{return f.call(null,t)}catch(e){return f.call(this,t)}}}function a(){h&&d&&(h=!1,d.length?v=d.concat(v):m=-1,v.length&&s())}function s(){if(!h){var t=o(a);h=!0;for(var e=v.length;e;){for(d=v,v=[];++m<e;)d&&d[m].run();m=-1,e=v.length}d=null,h=!1,i(t)}}function u(t,e){this.fun=t,this.array=e}function c(){}var l,f,p=t.exports={};!function(){try{l="function"==typeof setTimeout?setTimeout:n}catch(t){l=n}try{f="function"==typeof clearTimeout?clearTimeout:r}catch(t){f=r}}();var d,v=[],h=!1,m=-1;p.nextTick=function(t){var e=new Array(arguments.length-1);if(arguments.length>1)for(var n=1;n<arguments.length;n++)e[n-1]=arguments[n];v.push(new u(t,e)),1!==v.length||h||o(s)},u.prototype.run=function(){this.fun.apply(null,this.array)},p.title="browser",p.browser=!0,p.env={},p.argv=[],p.version="",p.versions={},p.on=c,p.addListener=c,p.once=c,p.off=c,p.removeListener=c,p.removeAllListeners=c,p.emit=c,p.binding=function(t){throw new Error("process.binding is not supported")},p.cwd=function(){return"/"},p.chdir=function(t){throw new Error("process.chdir is not supported")},p.umask=function(){return 0}},6:function(t,e,n){"use strict";(function(e,n){function r(t){return null==t?"":"object"==typeof t?JSON.stringify(t,null,2):String(t)}function o(t){var e=parseFloat(t);return isNaN(e)?t:e}function i(t,e){for(var n=Object.create(null),r=t.split(","),o=0;o<r.length;o++)n[r[o]]=!0;return e?function(t){return n[t.toLowerCase()]}:function(t){return n[t]}}function a(t,e){if(t.length){var n=t.indexOf(e);if(n>-1)return t.splice(n,1)}}function s(t,e){return Go.call(t,e)}function u(t){return"string"==typeof t||"number"==typeof t}function c(t){var e=Object.create(null);return function(n){return e[n]||(e[n]=t(n))}}function l(t,e){function n(n){var r=arguments.length;return r?r>1?t.apply(e,arguments):t.call(e,n):t.call(e)}return n._length=t.length,n}function f(t,e){e=e||0;for(var n=t.length-e,r=new Array(n);n--;)r[n]=t[n+e];return r}function p(t,e){for(var n in e)t[n]=e[n];return t}function d(t){return null!==t&&"object"==typeof t}function v(t){return Qo.call(t)===ti}function h(t){for(var e={},n=0;n<t.length;n++)t[n]&&p(e,t[n]);return e}function m(){}function y(t){return t.reduce(function(t,e){return t.concat(e.staticKeys||[])},[]).join(",")}function g(t,e){var n=d(t),r=d(e);if(!n||!r)return!n&&!r&&String(t)===String(e);try{return JSON.stringify(t)===JSON.stringify(e)}catch(n){return t===e}}function _(t,e){for(var n=0;n<t.length;n++)if(g(t[n],e))return n;return-1}function b(t){var e=!1;return function(){e||(e=!0,t())}}function w(t){return/native code/.test(t.toString())}function E(t){var e=(t+"").charCodeAt(0);return 36===e||95===e}function $(t,e,n,r){Object.defineProperty(t,e,{value:n,enumerable:!!r,writable:!0,configurable:!0})}function x(t){if(!bi.test(t)){var e=t.split(".");return function(t){for(var n=0;n<e.length;n++){if(!t)return;t=t[e[n]]}return t}}}function O(t){ki.target&&Ci.push(ki.target),ki.target=t}function N(){ki.target=Ci.pop()}function k(t,e){t.__proto__=e}function C(t,e,n){for(var r=0,o=n.length;r<o;r++){var i=n[r];$(t,i,e[i])}}function A(t,e){if(d(t)){var n;return s(t,"__ob__")&&t.__ob__ instanceof ji?n=t.__ob__:Si.shouldConvert&&!di()&&(Array.isArray(t)||v(t))&&Object.isExtensible(t)&&!t._isVue&&(n=new ji(t)),e&&n&&n.vmCount++,n}}function T(t,n,r,o){var i=new ki,a=Object.getOwnPropertyDescriptor(t,n);if(!a||a.configurable!==!1){var s=a&&a.get,u=a&&a.set,c=A(r);Object.defineProperty(t,n,{enumerable:!0,configurable:!0,get:function(){var e=s?s.call(t):r;return ki.target&&(i.depend(),c&&c.dep.depend(),Array.isArray(e)&&j(e)),e},set:function(n){var a=s?s.call(t):r;n===a||n!==n&&a!==a||("production"!==e.env.NODE_ENV&&o&&o(),u?u.call(t,n):r=n,c=A(n),i.notify())}})}}function D(t,n,r){if(Array.isArray(t))return t.length=Math.max(t.length,n),t.splice(n,1,r),r;if(s(t,n))return t[n]=r,r;var o=t.__ob__;return t._isVue||o&&o.vmCount?("production"!==e.env.NODE_ENV&&wi("Avoid adding reactive properties to a Vue instance or its root $data at runtime - declare it upfront in the data option."),r):o?(T(o.value,n,r),o.dep.notify(),r):(t[n]=r,r)}function S(t,n){if(Array.isArray(t))return void t.splice(n,1);var r=t.__ob__;if(t._isVue||r&&r.vmCount)return void("production"!==e.env.NODE_ENV&&wi("Avoid deleting properties on a Vue instance or its root $data - just set it to null."));s(t,n)&&(delete t[n],r&&r.dep.notify())}function j(t){for(var e=void 0,n=0,r=t.length;n<r;n++)e=t[n],e&&e.__ob__&&e.__ob__.dep.depend(),Array.isArray(e)&&j(e)}function V(t,e){if(!e)return t;for(var n,r,o,i=Object.keys(e),a=0;a<i.length;a++)n=i[a],r=t[n],o=e[n],s(t,n)?v(r)&&v(o)&&V(r,o):D(t,n,o);return t}function M(t,e){return e?t?t.concat(e):Array.isArray(e)?e:[e]:t}function P(t,e){var n=Object.create(t||null);return e?p(n,e):n}function I(t){for(var e in t.components){var n=e.toLowerCase();(zo(n)||ri.isReservedTag(n))&&wi("Do not use built-in or reserved HTML elements as component id: "+e)}}function L(t){var n=t.props;if(n){var r,o,i,a={};if(Array.isArray(n))for(r=n.length;r--;)o=n[r],"string"==typeof o?(i=Wo(o),a[i]={type:null}):"production"!==e.env.NODE_ENV&&wi("props must be strings when using array syntax.");else if(v(n))for(var s in n)o=n[s],i=Wo(s),a[i]=v(o)?o:{type:o};t.props=a}}function R(t){var e=t.directives;if(e)for(var n in e){var r=e[n];"function"==typeof r&&(e[n]={bind:r,update:r})}}function U(t,n,r){function o(e){var o=Vi[e]||Pi;f[e]=o(t[e],n[e],r,e)}"production"!==e.env.NODE_ENV&&I(n),L(n),R(n);var i=n.extends;if(i&&(t="function"==typeof i?U(t,i.options,r):U(t,i,r)),n.mixins)for(var a=0,u=n.mixins.length;a<u;a++){var c=n.mixins[a];c.prototype instanceof ye&&(c=c.options),t=U(t,c,r)}var l,f={};for(l in t)o(l);for(l in n)s(t,l)||o(l);return f}function F(t,n,r,o){if("string"==typeof r){var i=t[n];if(s(i,r))return i[r];var a=Wo(r);if(s(i,a))return i[a];var u=Zo(a);if(s(i,u))return i[u];var c=i[r]||i[a]||i[u];return"production"!==e.env.NODE_ENV&&o&&!c&&wi("Failed to resolve "+n.slice(0,-1)+": "+r,t),c}}function H(t,n,r,o){var i=n[t],a=!s(r,t),u=r[t];if(G(Boolean,i.type)&&(a&&!s(i,"default")?u=!1:G(String,i.type)||""!==u&&u!==Yo(t)||(u=!0)),void 0===u){u=B(o,i,t);var c=Si.shouldConvert;Si.shouldConvert=!0,A(u),Si.shouldConvert=c}return"production"!==e.env.NODE_ENV&&q(i,t,u,o,a),u}function B(t,n,r){if(s(n,"default")){var o=n.default;return"production"!==e.env.NODE_ENV&&d(o)&&wi('Invalid default value for prop "'+r+'": Props with type Object/Array must use a factory function to return the default value.',t),t&&t.$options.propsData&&void 0===t.$options.propsData[r]&&void 0!==t._props[r]?t._props[r]:"function"==typeof o&&"Function"!==z(n.type)?o.call(t):o}}function q(t,e,n,r,o){if(t.required&&o)return void wi('Missing required prop: "'+e+'"',r);if(null!=n||t.required){var i=t.type,a=!i||i===!0,s=[];if(i){Array.isArray(i)||(i=[i]);for(var u=0;u<i.length&&!a;u++){var c=J(n,i[u]);s.push(c.expectedType||""),a=c.valid}}if(!a)return void wi('Invalid prop: type check failed for prop "'+e+'". Expected '+s.map(Zo).join(", ")+", got "+Object.prototype.toString.call(n).slice(8,-1)+".",r);var l=t.validator;l&&(l(n)||wi('Invalid prop: custom validator check failed for prop "'+e+'".',r))}}function J(t,e){var n,r=z(e);return n="String"===r?typeof t==(r="string"):"Number"===r?typeof t==(r="number"):"Boolean"===r?typeof t==(r="boolean"):"Function"===r?typeof t==(r="function"):"Object"===r?v(t):"Array"===r?Array.isArray(t):t instanceof e,{valid:n,expectedType:r}}function z(t){var e=t&&t.toString().match(/^\s*function (\w+)/);return e&&e[1]}function G(t,e){if(!Array.isArray(e))return z(e)===z(t);for(var n=0,r=e.length;n<r;n++)if(z(e[n])===z(t))return!0;return!1}function K(t,n,r){if(ri.errorHandler)ri.errorHandler.call(null,t,n,r);else if("production"!==e.env.NODE_ENV&&wi("Error in "+r+":",n),!ii||"undefined"==typeof console)throw t}function W(t){return new Bi(void 0,void 0,void 0,String(t))}function Z(t){var e=new Bi(t.tag,t.data,t.children,t.text,t.elm,t.context,t.componentOptions);return e.ns=t.ns,e.isStatic=t.isStatic,e.key=t.key,e.isCloned=!0,e}function X(t){for(var e=t.length,n=new Array(e),r=0;r<e;r++)n[r]=Z(t[r]);return n}function Y(t){function e(){var t=arguments,n=e.fns;if(!Array.isArray(n))return n.apply(null,arguments);for(var r=0;r<n.length;r++)n[r].apply(null,t)}return e.fns=t,e}function Q(t,n,r,o,i){var a,s,u,c;for(a in t)s=t[a],u=n[a],c=Gi(a),s?u?s!==u&&(u.fns=s,t[a]=u):(s.fns||(s=t[a]=Y(s)),r(c.name,s,c.once,c.capture)):"production"!==e.env.NODE_ENV&&wi('Invalid handler for event "'+c.name+'": got '+String(s),i);for(a in n)t[a]||(c=Gi(a),o(c.name,n[a],c.capture))}function tt(t,e,n){function r(){n.apply(this,arguments),a(o.fns,r)}var o,i=t[e];i?i.fns&&i.merged?(o=i,o.fns.push(r)):o=Y([i,r]):o=Y([r]),o.merged=!0,t[e]=o}function et(t){for(var e=0;e<t.length;e++)if(Array.isArray(t[e]))return Array.prototype.concat.apply([],t);return t}function nt(t){return u(t)?[W(t)]:Array.isArray(t)?rt(t):void 0}function rt(t,e){var n,r,o,i=[];for(n=0;n<t.length;n++)null!=(r=t[n])&&"boolean"!=typeof r&&(o=i[i.length-1],Array.isArray(r)?i.push.apply(i,rt(r,(e||"")+"_"+n)):u(r)?o&&o.text?o.text+=String(r):""!==r&&i.push(W(r)):r.text&&o&&o.text?i[i.length-1]=W(o.text+r.text):(r.tag&&null==r.key&&null!=e&&(r.key="__vlist"+e+"_"+n+"__"),i.push(r)));return i}function ot(t){return t&&t.filter(function(t){return t&&t.componentOptions})[0]}function it(t){t._events=Object.create(null),t._hasHookEvent=!1;var e=t.$options._parentListeners;e&&ut(t,e)}function at(t,e,n){n?Ji.$once(t,e):Ji.$on(t,e)}function st(t,e){Ji.$off(t,e)}function ut(t,e,n){Ji=t,Q(e,n||{},at,st,t)}function ct(t){var e=/^hook:/;t.prototype.$on=function(t,n){var r=this,o=this;if(Array.isArray(t))for(var i=0,a=t.length;i<a;i++)r.$on(t[i],n);else(o._events[t]||(o._events[t]=[])).push(n),e.test(t)&&(o._hasHookEvent=!0);return o},t.prototype.$once=function(t,e){function n(){r.$off(t,n),e.apply(r,arguments)}var r=this;return n.fn=e,r.$on(t,n),r},t.prototype.$off=function(t,e){var n=this,r=this;if(!arguments.length)return r._events=Object.create(null),r;if(Array.isArray(t)){for(var o=0,i=t.length;o<i;o++)n.$off(t[o],e);return r}var a=r._events[t];if(!a)return r;if(1===arguments.length)return r._events[t]=null,r;for(var s,u=a.length;u--;)if((s=a[u])===e||s.fn===e){a.splice(u,1);break}return r},t.prototype.$emit=function(t){var e=this,n=e._events[t];if(n){n=n.length>1?f(n):n;for(var r=f(arguments,1),o=0,i=n.length;o<i;o++)n[o].apply(e,r)}return e}}function lt(t,e){var n={};if(!t)return n;for(var r,o,i=[],a=0,s=t.length;a<s;a++)if(o=t[a],(o.context===e||o.functionalContext===e)&&o.data&&(r=o.data.slot)){var u=n[r]||(n[r]=[]);"template"===o.tag?u.push.apply(u,o.children):u.push(o)}else i.push(o);return i.every(ft)||(n.default=i),n}function ft(t){return t.isComment||" "===t.text}function pt(t){for(var e={},n=0;n<t.length;n++)e[t[n][0]]=t[n][1];return e}function dt(t){var e=t.$options,n=e.parent;if(n&&!e.abstract){for(;n.$options.abstract&&n.$parent;)n=n.$parent;n.$children.push(t)}t.$parent=n,t.$root=n?n.$root:t,t.$children=[],t.$refs={},t._watcher=null,t._inactive=null,t._directInactive=!1,t._isMounted=!1,t._isDestroyed=!1,t._isBeingDestroyed=!1}function vt(t){t.prototype._update=function(t,e){var n=this;n._isMounted&&bt(n,"beforeUpdate");var r=n.$el,o=n._vnode,i=Ki;Ki=n,n._vnode=t,n.$el=o?n.__patch__(o,t):n.__patch__(n.$el,t,e,!1,n.$options._parentElm,n.$options._refElm),Ki=i,r&&(r.__vue__=null),n.$el&&(n.$el.__vue__=n),n.$vnode&&n.$parent&&n.$vnode===n.$parent._vnode&&(n.$parent.$el=n.$el)},t.prototype.$forceUpdate=function(){var t=this;t._watcher&&t._watcher.update()},t.prototype.$destroy=function(){var t=this;if(!t._isBeingDestroyed){bt(t,"beforeDestroy"),t._isBeingDestroyed=!0;var e=t.$parent;!e||e._isBeingDestroyed||t.$options.abstract||a(e.$children,t),t._watcher&&t._watcher.teardown();for(var n=t._watchers.length;n--;)t._watchers[n].teardown();t._data.__ob__&&t._data.__ob__.vmCount--,t._isDestroyed=!0,bt(t,"destroyed"),t.$off(),t.$el&&(t.$el.__vue__=null),t.__patch__(t._vnode,null)}}}function ht(t,n,r){t.$el=n,t.$options.render||(t.$options.render=zi,"production"!==e.env.NODE_ENV&&(t.$options.template&&"#"!==t.$options.template.charAt(0)||t.$options.el||n?wi("You are using the runtime-only build of Vue where the template compiler is not available. Either pre-compile the templates into render functions, or use the compiler-included build.",t):wi("Failed to mount component: template or render function not defined.",t))),bt(t,"beforeMount");var o;return o="production"!==e.env.NODE_ENV&&ri.performance&&yi?function(){var e=t._name,n="start "+e,o="end "+e;yi.mark(n);var i=t._render();yi.mark(o),yi.measure(e+" render",n,o),yi.mark(n),t._update(i,r),yi.mark(o),yi.measure(e+" patch",n,o)}:function(){t._update(t._render(),r)},t._watcher=new na(t,o,m),r=!1,null==t.$vnode&&(t._isMounted=!0,bt(t,"mounted")),t}function mt(t,n,r,o,i){var a=!!(i||t.$options._renderChildren||o.data.scopedSlots||t.$scopedSlots!==_i);if(t.$options._parentVnode=o,t.$vnode=o,t._vnode&&(t._vnode.parent=o),t.$options._renderChildren=i,n&&t.$options.props){Si.shouldConvert=!1,"production"!==e.env.NODE_ENV&&(Si.isSettingProps=!0);for(var s=t._props,u=t.$options._propKeys||[],c=0;c<u.length;c++){var l=u[c];s[l]=H(l,t.$options.props,n,t)}Si.shouldConvert=!0,"production"!==e.env.NODE_ENV&&(Si.isSettingProps=!1),t.$options.propsData=n}if(r){var f=t.$options._parentListeners;t.$options._parentListeners=r,ut(t,r,f)}a&&(t.$slots=lt(i,o.context),t.$forceUpdate())}function yt(t){for(;t&&(t=t.$parent);)if(t._inactive)return!0;return!1}function gt(t,e){if(e){if(t._directInactive=!1,yt(t))return}else if(t._directInactive)return;if(t._inactive||null==t._inactive){t._inactive=!1;for(var n=0;n<t.$children.length;n++)gt(t.$children[n]);bt(t,"activated")}}function _t(t,e){if(!(e&&(t._directInactive=!0,yt(t))||t._inactive)){t._inactive=!0;for(var n=0;n<t.$children.length;n++)_t(t.$children[n]);bt(t,"deactivated")}}function bt(t,e){var n=t.$options[e];if(n)for(var r=0,o=n.length;r<o;r++)try{n[r].call(t)}catch(n){K(n,t,e+" hook")}t._hasHookEvent&&t.$emit("hook:"+e)}function wt(){Wi.length=0,Zi={},"production"!==e.env.NODE_ENV&&(Xi={}),Yi=Qi=!1}function Et(){Qi=!0;var t,n,r;for(Wi.sort(function(t,e){return t.id-e.id}),ta=0;ta<Wi.length;ta++)if(t=Wi[ta],n=t.id,Zi[n]=null,t.run(),"production"!==e.env.NODE_ENV&&null!=Zi[n]&&(Xi[n]=(Xi[n]||0)+1,Xi[n]>ri._maxUpdateCount)){wi("You may have an infinite update loop "+(t.user?'in watcher with expression "'+t.expression+'"':"in a component render function."),t.vm);break}for(ta=Wi.length;ta--;)t=Wi[ta],r=t.vm,r._watcher===t&&r._isMounted&&bt(r,"updated");vi&&ri.devtools&&vi.emit("flush"),wt()}function $t(t){var e=t.id;if(null==Zi[e]){if(Zi[e]=!0,Qi){for(var n=Wi.length-1;n>=0&&Wi[n].id>t.id;)n--;Wi.splice(Math.max(n,ta)+1,0,t)}else Wi.push(t);Yi||(Yi=!0,mi(Et))}}function xt(t){ra.clear(),Ot(t,ra)}function Ot(t,e){var n,r,o=Array.isArray(t);if((o||d(t))&&Object.isExtensible(t)){if(t.__ob__){var i=t.__ob__.dep.id;if(e.has(i))return;e.add(i)}if(o)for(n=t.length;n--;)Ot(t[n],e);else for(r=Object.keys(t),n=r.length;n--;)Ot(t[r[n]],e)}}function Nt(t,e,n){oa.get=function(){return this[e][n]},oa.set=function(t){this[e][n]=t},Object.defineProperty(t,n,oa)}function kt(t){t._watchers=[];var e=t.$options;e.props&&Ct(t,e.props),e.methods&&jt(t,e.methods),e.data?At(t):A(t._data={},!0),e.computed&&Tt(t,e.computed),e.watch&&Vt(t,e.watch)}function Ct(t,n){var r=t.$options.propsData||{},o=t._props={},i=t.$options._propKeys=[],a=!t.$parent;Si.shouldConvert=a;var s=function(a){i.push(a);var s=H(a,n,r,t);"production"!==e.env.NODE_ENV?(ia[a]&&wi('"'+a+'" is a reserved attribute and cannot be used as component prop.',t),T(o,a,s,function(){t.$parent&&!Si.isSettingProps&&wi("Avoid mutating a prop directly since the value will be overwritten whenever the parent component re-renders. Instead, use a data or computed property based on the prop's value. Prop being mutated: \""+a+'"',t)})):T(o,a,s),a in t||Nt(t,"_props",a)};for(var u in n)s(u);Si.shouldConvert=!0}function At(t){var n=t.$options.data;n=t._data="function"==typeof n?n.call(t):n||{},v(n)||(n={},"production"!==e.env.NODE_ENV&&wi("data functions should return an object:\nhttps://vuejs.org/v2/guide/components.html#data-Must-Be-a-Function",t));for(var r=Object.keys(n),o=t.$options.props,i=r.length;i--;)o&&s(o,r[i])?"production"!==e.env.NODE_ENV&&wi('The data property "'+r[i]+'" is already declared as a prop. Use prop default value instead.',t):E(r[i])||Nt(t,"_data",r[i]);A(n,!0)}function Tt(t,e){var n=t._computedWatchers=Object.create(null);for(var r in e){var o=e[r],i="function"==typeof o?o:o.get;n[r]=new na(t,i,m,aa),r in t||Dt(t,r,o)}}function Dt(t,e,n){"function"==typeof n?(oa.get=St(e),oa.set=m):(oa.get=n.get?n.cache!==!1?St(e):n.get:m,oa.set=n.set?n.set:m),Object.defineProperty(t,e,oa)}function St(t){return function(){var e=this._computedWatchers&&this._computedWatchers[t];if(e)return e.dirty&&e.evaluate(),ki.target&&e.depend(),e.value}}function jt(t,n){var r=t.$options.props;for(var o in n)t[o]=null==n[o]?m:l(n[o],t),"production"!==e.env.NODE_ENV&&(null==n[o]&&wi('method "'+o+'" has an undefined value in the component definition. Did you reference the function correctly?',t),r&&s(r,o)&&wi('method "'+o+'" has already been defined as a prop.',t))}function Vt(t,e){for(var n in e){var r=e[n];if(Array.isArray(r))for(var o=0;o<r.length;o++)Mt(t,n,r[o]);else Mt(t,n,r)}}function Mt(t,e,n){var r;v(n)&&(r=n,n=n.handler),"string"==typeof n&&(n=t[n]),t.$watch(e,n,r)}function Pt(t){var n={};n.get=function(){return this._data};var r={};r.get=function(){return this._props},"production"!==e.env.NODE_ENV&&(n.set=function(t){wi("Avoid replacing instance root $data. Use nested data properties instead.",this)},r.set=function(){wi("$props is readonly.",this)}),Object.defineProperty(t.prototype,"$data",n),Object.defineProperty(t.prototype,"$props",r),t.prototype.$set=D,t.prototype.$delete=S,t.prototype.$watch=function(t,e,n){var r=this;n=n||{},n.user=!0;var o=new na(r,t,e,n);return n.immediate&&e.call(r,o.value),function(){o.teardown()}}}function It(t,n,r,o,i){if(t){var a=r.$options._base;if(d(t)&&(t=a.extend(t)),"function"!=typeof t)return void("production"!==e.env.NODE_ENV&&wi("Invalid Component definition: "+String(t),r));if(!t.cid)if(t.resolved)t=t.resolved;else if(!(t=qt(t,a,function(){r.$forceUpdate()})))return;ve(t),n=n||{},n.model&&Wt(t.options,n);var s=Jt(n,t);if(t.options.functional)return Lt(t,s,n,r,o);var u=n.on;n.on=n.nativeOn,t.options.abstract&&(n={}),Gt(n);var c=t.options.name||i;return new Bi("vue-component-"+t.cid+(c?"-"+c:""),n,void 0,void 0,void 0,r,{Ctor:t,propsData:s,listeners:u,tag:i,children:o})}}function Lt(t,e,n,r,o){var i={},a=t.options.props;if(a)for(var s in a)i[s]=H(s,a,e);var u=Object.create(r),c=function(t,e,n,r){return Zt(u,t,e,n,r,!0)},l=t.options.render.call(null,c,{props:i,data:n,parent:r,children:o,slots:function(){return lt(o,r)}});return l instanceof Bi&&(l.functionalContext=r,n.slot&&((l.data||(l.data={})).slot=n.slot)),l}function Rt(t,e,n,r){var o=t.componentOptions,i={_isComponent:!0,parent:e,propsData:o.propsData,_componentTag:o.tag,_parentVnode:t,_parentListeners:o.listeners,_renderChildren:o.children,_parentElm:n||null,_refElm:r||null},a=t.data.inlineTemplate;return a&&(i.render=a.render,i.staticRenderFns=a.staticRenderFns),new o.Ctor(i)}function Ut(t,e,n,r){if(!t.componentInstance||t.componentInstance._isDestroyed){(t.componentInstance=Rt(t,Ki,n,r)).$mount(e?t.elm:void 0,e)}else if(t.data.keepAlive){var o=t;Ft(o,o)}}function Ft(t,e){var n=e.componentOptions;mt(e.componentInstance=t.componentInstance,n.propsData,n.listeners,e,n.children)}function Ht(t){t.componentInstance._isMounted||(t.componentInstance._isMounted=!0,bt(t.componentInstance,"mounted")),t.data.keepAlive&&gt(t.componentInstance,!0)}function Bt(t){t.componentInstance._isDestroyed||(t.data.keepAlive?_t(t.componentInstance,!0):t.componentInstance.$destroy())}function qt(t,n,r){if(!t.requested){t.requested=!0;var o=t.pendingCallbacks=[r],i=!0,a=function(e){if(d(e)&&(e=n.extend(e)),t.resolved=e,!i)for(var r=0,a=o.length;r<a;r++)o[r](e)},s=function(n){"production"!==e.env.NODE_ENV&&wi("Failed to resolve async component: "+String(t)+(n?"\nReason: "+n:""))},u=t(a,s);return u&&"function"==typeof u.then&&!t.resolved&&u.then(a,s),i=!1,t.resolved}t.pendingCallbacks.push(r)}function Jt(t,e){var n=e.options.props;if(n){var r={},o=t.attrs,i=t.props,a=t.domProps;if(o||i||a)for(var s in n){var u=Yo(s);zt(r,i,s,u,!0)||zt(r,o,s,u)||zt(r,a,s,u)}return r}}function zt(t,e,n,r,o){if(e){if(s(e,n))return t[n]=e[n],o||delete e[n],!0;if(s(e,r))return t[n]=e[r],o||delete e[r],!0}return!1}function Gt(t){t.hook||(t.hook={});for(var e=0;e<ua.length;e++){var n=ua[e],r=t.hook[n],o=sa[n];t.hook[n]=r?Kt(o,r):o}}function Kt(t,e){return function(n,r,o,i){t(n,r,o,i),e(n,r,o,i)}}function Wt(t,e){var n=t.model&&t.model.prop||"value",r=t.model&&t.model.event||"input";(e.props||(e.props={}))[n]=e.model.value;var o=e.on||(e.on={});o[r]?o[r]=[e.model.callback].concat(o[r]):o[r]=e.model.callback}function Zt(t,e,n,r,o,i){return(Array.isArray(n)||u(n))&&(o=r,r=n,n=void 0),i&&(o=la),Xt(t,e,n,r,o)}function Xt(t,n,r,o,i){if(r&&r.__ob__)return"production"!==e.env.NODE_ENV&&wi("Avoid using observed data object as vnode data: "+JSON.stringify(r)+"\nAlways create fresh vnode data objects in each render!",t),zi();if(!n)return zi();Array.isArray(o)&&"function"==typeof o[0]&&(r=r||{},r.scopedSlots={default:o[0]},o.length=0),i===la?o=nt(o):i===ca&&(o=et(o));var a,s;if("string"==typeof n){var u;s=ri.getTagNamespace(n),a=ri.isReservedTag(n)?new Bi(ri.parsePlatformTagName(n),r,o,void 0,void 0,t):(u=F(t.$options,"components",n))?It(u,r,t,o,n):new Bi(n,r,o,void 0,void 0,t)}else a=It(n,r,t,o);return a?(s&&Yt(a,s),a):zi()}function Yt(t,e){if(t.ns=e,"foreignObject"!==t.tag&&t.children)for(var n=0,r=t.children.length;n<r;n++){var o=t.children[n];o.tag&&!o.ns&&Yt(o,e)}}function Qt(t,e){var n,r,o,i,a;if(Array.isArray(t)||"string"==typeof t)for(n=new Array(t.length),r=0,o=t.length;r<o;r++)n[r]=e(t[r],r);else if("number"==typeof t)for(n=new Array(t),r=0;r<t;r++)n[r]=e(r+1,r);else if(d(t))for(i=Object.keys(t),n=new Array(i.length),r=0,o=i.length;r<o;r++)a=i[r],n[r]=e(t[a],a,r);return n}function te(t,n,r,o){var i=this.$scopedSlots[t];if(i)return r=r||{},o&&p(r,o),i(r)||n;var a=this.$slots[t];return a&&"production"!==e.env.NODE_ENV&&(a._rendered&&wi('Duplicate presence of slot "'+t+'" found in the same render tree - this will likely cause render errors.',this),a._rendered=!0),a||n}function ee(t){return F(this.$options,"filters",t,!0)||ni}function ne(t,e,n){var r=ri.keyCodes[e]||n;return Array.isArray(r)?r.indexOf(t)===-1:r!==t}function re(t,n,r,o){if(r)if(d(r)){Array.isArray(r)&&(r=h(r));for(var i in r)if("class"===i||"style"===i)t[i]=r[i];else{var a=t.attrs&&t.attrs.type,s=o||ri.mustUseProp(n,a,i)?t.domProps||(t.domProps={}):t.attrs||(t.attrs={});s[i]=r[i]}}else"production"!==e.env.NODE_ENV&&wi("v-bind without argument expects an Object or Array value",this);return t}function oe(t,e){var n=this._staticTrees[t];return n&&!e?Array.isArray(n)?X(n):Z(n):(n=this._staticTrees[t]=this.$options.staticRenderFns[t].call(this._renderProxy),ae(n,"__static__"+t,!1),n)}function ie(t,e,n){return ae(t,"__once__"+e+(n?"_"+n:""),!0),t}function ae(t,e,n){if(Array.isArray(t))for(var r=0;r<t.length;r++)t[r]&&"string"!=typeof t[r]&&se(t[r],e+"_"+r,n);else se(t,e,n)}function se(t,e,n){t.isStatic=!0,t.key=e,t.isOnce=n}function ue(t){t.$vnode=null,t._vnode=null,t._staticTrees=null;var e=t.$options._parentVnode,n=e&&e.context;t.$slots=lt(t.$options._renderChildren,n),t.$scopedSlots=_i,t._c=function(e,n,r,o){return Zt(t,e,n,r,o,!1)},t.$createElement=function(e,n,r,o){return Zt(t,e,n,r,o,!0)}}function ce(t){t.prototype.$nextTick=function(t){return mi(t,this)},t.prototype._render=function(){var t=this,n=t.$options,r=n.render,o=n.staticRenderFns,i=n._parentVnode;if(t._isMounted)for(var a in t.$slots)t.$slots[a]=X(t.$slots[a]);t.$scopedSlots=i&&i.data.scopedSlots||_i,o&&!t._staticTrees&&(t._staticTrees=[]),t.$vnode=i;var s;try{s=r.call(t._renderProxy,t.$createElement)}catch(n){K(n,t,"render function"),s="production"!==e.env.NODE_ENV&&t.$options.renderError?t.$options.renderError.call(t._renderProxy,t.$createElement,n):t._vnode}return s instanceof Bi||("production"!==e.env.NODE_ENV&&Array.isArray(s)&&wi("Multiple root nodes returned from render function. Render function should return a single root node.",t),s=zi()),s.parent=i,s},t.prototype._o=ie,t.prototype._n=o,t.prototype._s=r,t.prototype._l=Qt,t.prototype._t=te,t.prototype._q=g,t.prototype._i=_,t.prototype._m=oe,t.prototype._f=ee,t.prototype._k=ne,t.prototype._b=re,t.prototype._v=W,t.prototype._e=zi,t.prototype._u=pt}function le(t){var e=t.$options.provide;e&&(t._provided="function"==typeof e?e.call(t):e)}function fe(t){var e=t.$options.inject;if(e)for(var n=Array.isArray(e),r=n?e:hi?Reflect.ownKeys(e):Object.keys(e),o=0;o<r.length;o++)for(var i=r[o],a=n?i:e[i],s=t;s;){if(s._provided&&a in s._provided){t[i]=s._provided[a];break}s=s.$parent}}function pe(t){t.prototype._init=function(t){"production"!==e.env.NODE_ENV&&ri.performance&&yi&&yi.mark("init");var n=this;n._uid=fa++,n._isVue=!0,t&&t._isComponent?de(n,t):n.$options=U(ve(n.constructor),t||{},n),"production"!==e.env.NODE_ENV?Mi(n):n._renderProxy=n,n._self=n,dt(n),it(n),ue(n),bt(n,"beforeCreate"),fe(n),kt(n),le(n),bt(n,"created"),"production"!==e.env.NODE_ENV&&ri.performance&&yi&&(n._name=gi(n,!1),yi.mark("init end"),yi.measure(n._name+" init","init","init end")),n.$options.el&&n.$mount(n.$options.el)}}function de(t,e){var n=t.$options=Object.create(t.constructor.options);n.parent=e.parent,n.propsData=e.propsData,n._parentVnode=e._parentVnode,n._parentListeners=e._parentListeners,n._renderChildren=e._renderChildren,n._componentTag=e._componentTag,n._parentElm=e._parentElm,n._refElm=e._refElm,e.render&&(n.render=e.render,n.staticRenderFns=e.staticRenderFns)}function ve(t){var e=t.options;if(t.super){var n=ve(t.super);if(n!==t.superOptions){t.superOptions=n;var r=he(t);r&&p(t.extendOptions,r),e=t.options=U(n,t.extendOptions),e.name&&(e.components[e.name]=t)}}return e}function he(t){var e,n=t.options,r=t.sealedOptions;for(var o in n)n[o]!==r[o]&&(e||(e={}),e[o]=me(n[o],r[o]));return e}function me(t,e){if(Array.isArray(t)){var n=[];e=Array.isArray(e)?e:[e];for(var r=0;r<t.length;r++)e.indexOf(t[r])<0&&n.push(t[r]);return n}return t}function ye(t){"production"===e.env.NODE_ENV||this instanceof ye||wi("Vue is a constructor and should be called with the `new` keyword"),this._init(t)}function ge(t){t.use=function(t){if(!t.installed){var e=f(arguments,1);return e.unshift(this),"function"==typeof t.install?t.install.apply(t,e):"function"==typeof t&&t.apply(null,e),t.installed=!0,this}}}function _e(t){t.mixin=function(t){this.options=U(this.options,t)}}function be(t){t.cid=0;var n=1;t.extend=function(t){t=t||{};var r=this,o=r.cid,i=t._Ctor||(t._Ctor={});if(i[o])return i[o];var a=t.name||r.options.name;"production"!==e.env.NODE_ENV&&(/^[a-zA-Z][\w-]*$/.test(a)||wi('Invalid component name: "'+a+'". Component names can only contain alphanumeric characters and the hyphen, and must start with a letter.'));var s=function(t){this._init(t)};return s.prototype=Object.create(r.prototype),s.prototype.constructor=s,s.cid=n++,s.options=U(r.options,t),s.super=r,s.options.props&&we(s),s.options.computed&&Ee(s),s.extend=r.extend,s.mixin=r.mixin,s.use=r.use,ri._assetTypes.forEach(function(t){s[t]=r[t]}),a&&(s.options.components[a]=s),s.superOptions=r.options,s.extendOptions=t,s.sealedOptions=p({},s.options),i[o]=s,s}}function we(t){var e=t.options.props;for(var n in e)Nt(t.prototype,"_props",n)}function Ee(t){var e=t.options.computed;for(var n in e)Dt(t.prototype,n,e[n])}function $e(t){ri._assetTypes.forEach(function(n){t[n]=function(t,r){return r?("production"!==e.env.NODE_ENV&&"component"===n&&ri.isReservedTag(t)&&wi("Do not use built-in or reserved HTML elements as component id: "+t),"component"===n&&v(r)&&(r.name=r.name||t,r=this.options._base.extend(r)),"directive"===n&&"function"==typeof r&&(r={bind:r,update:r}),this.options[n+"s"][t]=r,r):this.options[n+"s"][t]}})}function xe(t){return t&&(t.Ctor.options.name||t.tag)}function Oe(t,e){return"string"==typeof t?t.split(",").indexOf(e)>-1:t instanceof RegExp&&t.test(e)}function Ne(t,e){for(var n in t){var r=t[n];if(r){var o=xe(r.componentOptions);o&&!e(o)&&(ke(r),t[n]=null)}}}function ke(t){t&&(t.componentInstance._inactive||bt(t.componentInstance,"deactivated"),t.componentInstance.$destroy())}function Ce(t){var n={};n.get=function(){return ri},"production"!==e.env.NODE_ENV&&(n.set=function(){wi("Do not replace the Vue.config object, set individual fields instead.")}),Object.defineProperty(t,"config",n),t.util={warn:wi,extend:p,mergeOptions:U,defineReactive:T},t.set=D,t.delete=S,t.nextTick=mi,t.options=Object.create(null),ri._assetTypes.forEach(function(e){t.options[e+"s"]=Object.create(null)}),t.options._base=t,p(t.options.components,va),ge(t),_e(t),be(t),$e(t)}function Ae(t){for(var e=t.data,n=t,r=t;r.componentInstance;)r=r.componentInstance._vnode,r.data&&(e=Te(r.data,e));for(;n=n.parent;)n.data&&(e=Te(e,n.data));return De(e)}function Te(t,e){return{staticClass:Se(t.staticClass,e.staticClass),class:t.class?[t.class,e.class]:e.class}}function De(t){var e=t.class,n=t.staticClass;return n||e?Se(n,je(e)):""}function Se(t,e){return t?e?t+" "+e:t:e||""}function je(t){var e="";if(!t)return e;if("string"==typeof t)return t;if(Array.isArray(t)){for(var n,r=0,o=t.length;r<o;r++)t[r]&&(n=je(t[r]))&&(e+=n+" ");return e.slice(0,-1)}if(d(t)){for(var i in t)t[i]&&(e+=i+" ");return e.slice(0,-1)}return e}function Ve(t){return Va(t)?"svg":"math"===t?"math":void 0}function Me(t){if(!ii)return!0;if(Pa(t))return!1;if(t=t.toLowerCase(),null!=Ia[t])return Ia[t];var e=document.createElement(t);return t.indexOf("-")>-1?Ia[t]=e.constructor===window.HTMLUnknownElement||e.constructor===window.HTMLElement:Ia[t]=/HTMLUnknownElement/.test(e.toString())}function Pe(t){if("string"==typeof t){var n=document.querySelector(t);return n?n:("production"!==e.env.NODE_ENV&&wi("Cannot find element: "+t),document.createElement("div"))}return t}function Ie(t,e){var n=document.createElement(t);return"select"!==t?n:(e.data&&e.data.attrs&&void 0!==e.data.attrs.multiple&&n.setAttribute("multiple","multiple"),n)}function Le(t,e){return document.createElementNS(Sa[t],e)}function Re(t){return document.createTextNode(t)}function Ue(t){return document.createComment(t)}function Fe(t,e,n){t.insertBefore(e,n)}function He(t,e){t.removeChild(e)}function Be(t,e){t.appendChild(e)}function qe(t){return t.parentNode}function Je(t){return t.nextSibling}function ze(t){return t.tagName}function Ge(t,e){t.textContent=e}function Ke(t,e,n){t.setAttribute(e,n)}function We(t,e){var n=t.data.ref;if(n){var r=t.context,o=t.componentInstance||t.elm,i=r.$refs;e?Array.isArray(i[n])?a(i[n],o):i[n]===o&&(i[n]=void 0):t.data.refInFor?Array.isArray(i[n])&&i[n].indexOf(o)<0?i[n].push(o):i[n]=[o]:i[n]=o}}function Ze(t){return null==t}function Xe(t){return null!=t}function Ye(t,e){return t.key===e.key&&t.tag===e.tag&&t.isComment===e.isComment&&!t.data==!e.data}function Qe(t,e,n){var r,o,i={};for(r=e;r<=n;++r)o=t[r].key,Xe(o)&&(i[o]=r);return i}function tn(t){function n(t){return new Bi(A.tagName(t).toLowerCase(),{},[],void 0,t)}function r(t,e){function n(){0==--n.listeners&&o(t)}return n.listeners=e,n}function o(t){var e=A.parentNode(t);e&&A.removeChild(e,t)}function a(t,n,r,o,i){if(t.isRootInsert=!i,!s(t,n,r,o)){var a=t.data,u=t.children,c=t.tag;Xe(c)?("production"!==e.env.NODE_ENV&&(a&&a.pre&&T++,T||t.ns||ri.ignoredElements.length&&ri.ignoredElements.indexOf(c)>-1||!ri.isUnknownElement(c)||wi("Unknown custom element: <"+c+'> - did you register the component correctly? For recursive components, make sure to provide the "name" option.',t.context)),t.elm=t.ns?A.createElementNS(t.ns,c):A.createElement(c,t),h(t),p(t,u,n),Xe(a)&&v(t,n),f(r,t.elm,o),"production"!==e.env.NODE_ENV&&a&&a.pre&&T--):t.isComment?(t.elm=A.createComment(t.text),f(r,t.elm,o)):(t.elm=A.createTextNode(t.text),f(r,t.elm,o))}}function s(t,e,n,r){var o=t.data;if(Xe(o)){var i=Xe(t.componentInstance)&&o.keepAlive;if(Xe(o=o.hook)&&Xe(o=o.init)&&o(t,!1,n,r),Xe(t.componentInstance))return c(t,e),i&&l(t,e,n,r),!0}}function c(t,e){t.data.pendingInsert&&e.push.apply(e,t.data.pendingInsert),t.elm=t.componentInstance.$el,d(t)?(v(t,e),h(t)):(We(t),e.push(t))}function l(t,e,n,r){for(var o,i=t;i.componentInstance;)if(i=i.componentInstance._vnode,Xe(o=i.data)&&Xe(o=o.transition)){for(o=0;o<k.activate.length;++o)k.activate[o](Ua,i);e.push(i);break}f(n,t.elm,r)}function f(t,e,n){t&&(n?A.insertBefore(t,e,n):A.appendChild(t,e))}function p(t,e,n){if(Array.isArray(e))for(var r=0;r<e.length;++r)a(e[r],n,t.elm,null,!0);else u(t.text)&&A.appendChild(t.elm,A.createTextNode(t.text))}function d(t){for(;t.componentInstance;)t=t.componentInstance._vnode;return Xe(t.tag)}function v(t,e){for(var n=0;n<k.create.length;++n)k.create[n](Ua,t);O=t.data.hook,Xe(O)&&(O.create&&O.create(Ua,t),O.insert&&e.push(t))}function h(t){for(var e,n=t;n;)Xe(e=n.context)&&Xe(e=e.$options._scopeId)&&A.setAttribute(t.elm,e,""),n=n.parent;Xe(e=Ki)&&e!==t.context&&Xe(e=e.$options._scopeId)&&A.setAttribute(t.elm,e,"")}function m(t,e,n,r,o,i){for(;r<=o;++r)a(n[r],i,t,e)}function y(t){var e,n,r=t.data;if(Xe(r))for(Xe(e=r.hook)&&Xe(e=e.destroy)&&e(t),e=0;e<k.destroy.length;++e)k.destroy[e](t);if(Xe(e=t.children))for(n=0;n<t.children.length;++n)y(t.children[n])}function g(t,e,n,r){for(;n<=r;++n){var i=e[n];Xe(i)&&(Xe(i.tag)?(_(i),y(i)):o(i.elm))}}function _(t,e){if(e||Xe(t.data)){var n=k.remove.length+1;for(e?e.listeners+=n:e=r(t.elm,n),Xe(O=t.componentInstance)&&Xe(O=O._vnode)&&Xe(O.data)&&_(O,e),O=0;O<k.remove.length;++O)k.remove[O](t,e);Xe(O=t.data.hook)&&Xe(O=O.remove)?O(t,e):e()}else o(t.elm)}function b(t,n,r,o,i){for(var s,u,c,l,f=0,p=0,d=n.length-1,v=n[0],h=n[d],y=r.length-1,_=r[0],b=r[y],E=!i;f<=d&&p<=y;)Ze(v)?v=n[++f]:Ze(h)?h=n[--d]:Ye(v,_)?(w(v,_,o),v=n[++f],_=r[++p]):Ye(h,b)?(w(h,b,o),h=n[--d],b=r[--y]):Ye(v,b)?(w(v,b,o),E&&A.insertBefore(t,v.elm,A.nextSibling(h.elm)),v=n[++f],b=r[--y]):Ye(h,_)?(w(h,_,o),E&&A.insertBefore(t,h.elm,v.elm),h=n[--d],_=r[++p]):(Ze(s)&&(s=Qe(n,f,d)),u=Xe(_.key)?s[_.key]:null,Ze(u)?(a(_,o,t,v.elm),_=r[++p]):(c=n[u],"production"===e.env.NODE_ENV||c||wi("It seems there are duplicate keys that is causing an update error. Make sure each v-for item has a unique key."),Ye(c,_)?(w(c,_,o),n[u]=void 0,E&&A.insertBefore(t,_.elm,v.elm),_=r[++p]):(a(_,o,t,v.elm),_=r[++p])));f>d?(l=Ze(r[y+1])?null:r[y+1].elm,m(t,l,r,p,y,o)):p>y&&g(t,n,f,d)}function w(t,e,n,r){if(t!==e){if(e.isStatic&&t.isStatic&&e.key===t.key&&(e.isCloned||e.isOnce))return e.elm=t.elm,void(e.componentInstance=t.componentInstance);var o,i=e.data,a=Xe(i);a&&Xe(o=i.hook)&&Xe(o=o.prepatch)&&o(t,e);var s=e.elm=t.elm,u=t.children,c=e.children;if(a&&d(e)){for(o=0;o<k.update.length;++o)k.update[o](t,e);Xe(o=i.hook)&&Xe(o=o.update)&&o(t,e)}Ze(e.text)?Xe(u)&&Xe(c)?u!==c&&b(s,u,c,n,r):Xe(c)?(Xe(t.text)&&A.setTextContent(s,""),m(s,null,c,0,c.length-1,n)):Xe(u)?g(s,u,0,u.length-1):Xe(t.text)&&A.setTextContent(s,""):t.text!==e.text&&A.setTextContent(s,e.text),a&&Xe(o=i.hook)&&Xe(o=o.postpatch)&&o(t,e)}}function E(t,e,n){if(n&&t.parent)t.parent.data.pendingInsert=e;else for(var r=0;r<e.length;++r)e[r].data.hook.insert(e[r])}function $(t,n,r){if("production"!==e.env.NODE_ENV&&!x(t,n))return!1;n.elm=t;var o=n.tag,i=n.data,a=n.children;if(Xe(i)&&(Xe(O=i.hook)&&Xe(O=O.init)&&O(n,!0),Xe(O=n.componentInstance)))return c(n,r),!0;if(Xe(o)){if(Xe(a))if(t.hasChildNodes()){for(var s=!0,u=t.firstChild,l=0;l<a.length;l++){if(!u||!$(u,a[l],r)){s=!1;break}u=u.nextSibling}if(!s||u)return"production"===e.env.NODE_ENV||"undefined"==typeof console||D||(D=!0),!1}else p(n,a,r);if(Xe(i))for(var f in i)if(!S(f)){v(n,r);break}}else t.data!==n.text&&(t.data=n.text);return!0}function x(t,e){return e.tag?0===e.tag.indexOf("vue-component")||e.tag.toLowerCase()===(t.tagName&&t.tagName.toLowerCase()):t.nodeType===(e.isComment?8:3)}var O,N,k={},C=t.modules,A=t.nodeOps;for(O=0;O<Fa.length;++O)for(k[Fa[O]]=[],N=0;N<C.length;++N)void 0!==C[N][Fa[O]]&&k[Fa[O]].push(C[N][Fa[O]]);var T=0,D=!1,S=i("attrs,style,class,staticClass,staticStyle,key");return function(t,r,o,i,s,u){if(!r)return void(t&&y(t));var c=!1,l=[];if(t){var f=Xe(t.nodeType);if(!f&&Ye(t,r))w(t,r,l,i);else{if(f){if(1===t.nodeType&&t.hasAttribute("server-rendered")&&(t.removeAttribute("server-rendered"),o=!0),o){if($(t,r,l))return E(r,l,!0),t;"production"!==e.env.NODE_ENV&&wi("The client-side rendered virtual DOM tree is not matching server-rendered content. This is likely caused by incorrect HTML markup, for example nesting block-level elements inside <p>, or missing <tbody>. Bailing hydration and performing full client-side render.")}t=n(t)}var p=t.elm,v=A.parentNode(p);if(a(r,l,p._leaveCb?null:v,A.nextSibling(p)),r.parent){for(var h=r.parent;h;)h.elm=r.elm,h=h.parent;if(d(r))for(var m=0;m<k.create.length;++m)k.create[m](Ua,r.parent)}null!==v?g(v,[t],0,0):Xe(t.tag)&&y(t)}}else c=!0,a(r,l,s,u);return E(r,l,c),r.elm}}function en(t,e){(t.data.directives||e.data.directives)&&nn(t,e)}function nn(t,e){var n,r,o,i=t===Ua,a=e===Ua,s=rn(t.data.directives,t.context),u=rn(e.data.directives,e.context),c=[],l=[];for(n in u)r=s[n],o=u[n],r?(o.oldValue=r.value,an(o,"update",e,t),o.def&&o.def.componentUpdated&&l.push(o)):(an(o,"bind",e,t),o.def&&o.def.inserted&&c.push(o));if(c.length){var f=function(){for(var n=0;n<c.length;n++)an(c[n],"inserted",e,t)};i?tt(e.data.hook||(e.data.hook={}),"insert",f):f()}if(l.length&&tt(e.data.hook||(e.data.hook={}),"postpatch",function(){for(var n=0;n<l.length;n++)an(l[n],"componentUpdated",e,t)}),!i)for(n in s)u[n]||an(s[n],"unbind",t,t,a)}function rn(t,e){var n=Object.create(null);if(!t)return n;var r,o;for(r=0;r<t.length;r++)o=t[r],o.modifiers||(o.modifiers=Ba),n[on(o)]=o,o.def=F(e.$options,"directives",o.name,!0);return n}function on(t){return t.rawName||t.name+"."+Object.keys(t.modifiers||{}).join(".")}function an(t,e,n,r,o){var i=t.def&&t.def[e];i&&i(n.elm,t,n,r,o)}function sn(t,e){if(t.data.attrs||e.data.attrs){var n,r,o=e.elm,i=t.data.attrs||{},a=e.data.attrs||{};a.__ob__&&(a=e.data.attrs=p({},a));for(n in a)r=a[n],i[n]!==r&&un(o,n,r);ui&&a.value!==i.value&&un(o,"value",a.value);for(n in i)null==a[n]&&(Aa(n)?o.removeAttributeNS(Ca,Ta(n)):Na(n)||o.removeAttribute(n))}}function un(t,e,n){ka(e)?Da(n)?t.removeAttribute(e):t.setAttribute(e,e):Na(e)?t.setAttribute(e,Da(n)||"false"===n?"false":"true"):Aa(e)?Da(n)?t.removeAttributeNS(Ca,Ta(e)):t.setAttributeNS(Ca,e,n):Da(n)?t.removeAttribute(e):t.setAttribute(e,n)}function cn(t,e){var n=e.elm,r=e.data,o=t.data;if(r.staticClass||r.class||o&&(o.staticClass||o.class)){var i=Ae(e),a=n._transitionClasses;a&&(i=Se(i,je(a))),i!==n._prevClass&&(n.setAttribute("class",i),n._prevClass=i)}}function ln(t){function e(){(a||(a=[])).push(t.slice(v,o).trim()),v=o+1}var n,r,o,i,a,s=!1,u=!1,c=!1,l=!1,f=0,p=0,d=0,v=0;for(o=0;o<t.length;o++)if(r=n,n=t.charCodeAt(o),s)39===n&&92!==r&&(s=!1);else if(u)34===n&&92!==r&&(u=!1);else if(c)96===n&&92!==r&&(c=!1);else if(l)47===n&&92!==r&&(l=!1);else if(124!==n||124===t.charCodeAt(o+1)||124===t.charCodeAt(o-1)||f||p||d){switch(n){case 34:u=!0;break;case 39:s=!0;break;case 96:c=!0;break;case 40:d++;break;case 41:d--;break;case 91:p++;break;case 93:p--;break;case 123:f++;break;case 125:f--}if(47===n){for(var h=o-1,m=void 0;h>=0&&" "===(m=t.charAt(h));h--);m&&Ga.test(m)||(l=!0)}}else void 0===i?(v=o+1,i=t.slice(0,o).trim()):e();if(void 0===i?i=t.slice(0,o).trim():0!==v&&e(),a)for(o=0;o<a.length;o++)i=fn(i,a[o]);return i}function fn(t,e){var n=e.indexOf("(");return n<0?'_f("'+e+'")('+t+")":'_f("'+e.slice(0,n)+'")('+t+","+e.slice(n+1)}function pn(t){}function dn(t,e){return t?t.map(function(t){return t[e]}).filter(function(t){return t}):[]}function vn(t,e,n){(t.props||(t.props=[])).push({name:e,value:n})}function hn(t,e,n){(t.attrs||(t.attrs=[])).push({name:e,value:n})}function mn(t,e,n,r,o,i){(t.directives||(t.directives=[])).push({name:e,rawName:n,value:r,arg:o,modifiers:i})}function yn(t,e,n,r,o){r&&r.capture&&(delete r.capture,e="!"+e),r&&r.once&&(delete r.once,e="~"+e);var i;r&&r.native?(delete r.native,i=t.nativeEvents||(t.nativeEvents={})):i=t.events||(t.events={});var a={value:n,modifiers:r},s=i[e];Array.isArray(s)?o?s.unshift(a):s.push(a):i[e]=s?o?[a,s]:[s,a]:a}function gn(t,e,n){var r=_n(t,":"+e)||_n(t,"v-bind:"+e);if(null!=r)return ln(r);if(n!==!1){var o=_n(t,e);if(null!=o)return JSON.stringify(o)}}function _n(t,e){var n;if(null!=(n=t.attrsMap[e]))for(var r=t.attrsList,o=0,i=r.length;o<i;o++)if(r[o].name===e){r.splice(o,1);break}return n}function bn(t,e,n){var r=n||{},o=r.number,i=r.trim,a="$$v",s=a;i&&(s="(typeof "+a+" === 'string'? "+a+".trim(): "+a+")"),o&&(s="_n("+s+")");var u=wn(e,s);t.model={value:"("+e+")",expression:'"'+e+'"',callback:"function ("+a+") {"+u+"}"}}function wn(t,e){var n=En(t);return null===n.idx?t+"="+e:"var $$exp = "+n.exp+", $$idx = "+n.idx+";if (!Array.isArray($$exp)){"+t+"="+e+"}else{$$exp.splice($$idx, 1, "+e+")}"}function En(t){if(ma=t,ha=ma.length,ga=_a=ba=0,t.indexOf("[")<0||t.lastIndexOf("]")<ha-1)return{exp:t,idx:null};for(;!xn();)ya=$n(),On(ya)?kn(ya):91===ya&&Nn(ya);return{exp:t.substring(0,_a),idx:t.substring(_a+1,ba)}}function $n(){return ma.charCodeAt(++ga)}function xn(){return ga>=ha}function On(t){return 34===t||39===t}function Nn(t){var e=1;for(_a=ga;!xn();)if(t=$n(),On(t))kn(t);else if(91===t&&e++,93===t&&e--,0===e){ba=ga;break}}function kn(t){for(var e=t;!xn()&&(t=$n())!==e;);}function Cn(t,n,r){wa=r;var o=n.value,i=n.modifiers,a=t.tag,s=t.attrsMap.type;if("production"!==e.env.NODE_ENV){var u=t.attrsMap["v-bind:type"]||t.attrsMap[":type"];"input"===a&&u&&wa('<input :type="'+u+'" v-model="'+o+'">:\nv-model does not support dynamic input types. Use v-if branches instead.'),"input"===a&&"file"===s&&wa("<"+t.tag+' v-model="'+o+'" type="file">:\nFile inputs are read only. Use a v-on:change listener instead.')}if("select"===a)Dn(t,o,i);else if("input"===a&&"checkbox"===s)An(t,o,i);else if("input"===a&&"radio"===s)Tn(t,o,i);else if("input"===a||"textarea"===a)Sn(t,o,i);else{if(!ri.isReservedTag(a))return bn(t,o,i),!1;"production"!==e.env.NODE_ENV&&wa("<"+t.tag+' v-model="'+o+"\">: v-model is not supported on this element type. If you are working with contenteditable, it's recommended to wrap a library dedicated for that purpose inside a custom component.")}return!0}function An(t,e,n){var r=n&&n.number,o=gn(t,"value")||"null",i=gn(t,"true-value")||"true",a=gn(t,"false-value")||"false";vn(t,"checked","Array.isArray("+e+")?_i("+e+","+o+")>-1"+("true"===i?":("+e+")":":_q("+e+","+i+")")),yn(t,Wa,"var $$a="+e+",$$el=$event.target,$$c=$$el.checked?("+i+"):("+a+");if(Array.isArray($$a)){var $$v="+(r?"_n("+o+")":o)+",$$i=_i($$a,$$v);if($$c){$$i<0&&("+e+"=$$a.concat($$v))}else{$$i>-1&&("+e+"=$$a.slice(0,$$i).concat($$a.slice($$i+1)))}}else{"+e+"=$$c}",null,!0)}function Tn(t,e,n){var r=n&&n.number,o=gn(t,"value")||"null";o=r?"_n("+o+")":o,vn(t,"checked","_q("+e+","+o+")"),yn(t,Wa,wn(e,o),null,!0)}function Dn(t,e,n){var r=n&&n.number,o='Array.prototype.filter.call($event.target.options,function(o){return o.selected}).map(function(o){var val = "_value" in o ? o._value : o.value;return '+(r?"_n(val)":"val")+"})",i="var $$selectedVal = "+o+";";i=i+" "+wn(e,"$event.target.multiple ? $$selectedVal : $$selectedVal[0]"),yn(t,"change",i,null,!0)}function Sn(t,e,n){var r=t.attrsMap.type,o=n||{},i=o.lazy,a=o.number,s=o.trim,u=!i&&"range"!==r,c=i?"change":"range"===r?Ka:"input",l="$event.target.value";s&&(l="$event.target.value.trim()"),a&&(l="_n("+l+")");var f=wn(e,l);u&&(f="if($event.target.composing)return;"+f),vn(t,"value","("+e+")"),yn(t,c,f,null,!0),(s||a||"number"===r)&&yn(t,"blur","$forceUpdate()")}function jn(t){var e;t[Ka]&&(e=si?"change":"input",t[e]=[].concat(t[Ka],t[e]||[]),delete t[Ka]),t[Wa]&&(e=pi?"click":"change",t[e]=[].concat(t[Wa],t[e]||[]),delete t[Wa])}function Vn(t,e,n,r){if(n){var o=e,i=Ea;e=function(n){null!==(1===arguments.length?o(n):o.apply(null,arguments))&&Mn(t,e,r,i)}}Ea.addEventListener(t,e,r)}function Mn(t,e,n,r){(r||Ea).removeEventListener(t,e,n)}function Pn(t,e){if(t.data.on||e.data.on){var n=e.data.on||{},r=t.data.on||{};Ea=e.elm,jn(n),Q(n,r,Vn,Mn,e.context)}}function In(t,e){if(t.data.domProps||e.data.domProps){var n,r,o=e.elm,i=t.data.domProps||{},a=e.data.domProps||{};a.__ob__&&(a=e.data.domProps=p({},a));for(n in i)null==a[n]&&(o[n]="");for(n in a)if(r=a[n],"textContent"!==n&&"innerHTML"!==n||(e.children&&(e.children.length=0),r!==i[n]))if("value"===n){o._value=r;var s=null==r?"":String(r);Ln(o,e,s)&&(o.value=s)}else o[n]=r}}function Ln(t,e,n){return!t.composing&&("option"===e.tag||Rn(t,n)||Un(t,n))}function Rn(t,e){return document.activeElement!==t&&t.value!==e}function Un(t,e){var n=t.value,r=t._vModifiers;return r&&r.number||"number"===t.type?o(n)!==o(e):r&&r.trim?n.trim()!==e.trim():n!==e}function Fn(t){var e=Hn(t.style);return t.staticStyle?p(t.staticStyle,e):e}function Hn(t){return Array.isArray(t)?h(t):"string"==typeof t?Ya(t):t}function Bn(t,e){var n,r={};if(e)for(var o=t;o.componentInstance;)o=o.componentInstance._vnode,o.data&&(n=Fn(o.data))&&p(r,n);(n=Fn(t.data))&&p(r,n);for(var i=t;i=i.parent;)i.data&&(n=Fn(i.data))&&p(r,n);return r}function qn(t,e){var n=e.data,r=t.data;if(n.staticStyle||n.style||r.staticStyle||r.style){var o,i,a=e.elm,s=t.data.staticStyle,u=t.data.style||{},c=s||u,l=Hn(e.data.style)||{};e.data.style=l.__ob__?p({},l):l;var f=Bn(e,!0);for(i in c)null==f[i]&&es(a,i,"");for(i in f)(o=f[i])!==c[i]&&es(a,i,null==o?"":o)}}function Jn(t,e){if(e&&(e=e.trim()))if(t.classList)e.indexOf(" ")>-1?e.split(/\s+/).forEach(function(e){return t.classList.add(e)}):t.classList.add(e);else{var n=" "+(t.getAttribute("class")||"")+" ";n.indexOf(" "+e+" ")<0&&t.setAttribute("class",(n+e).trim())}}function zn(t,e){if(e&&(e=e.trim()))if(t.classList)e.indexOf(" ")>-1?e.split(/\s+/).forEach(function(e){return t.classList.remove(e)}):t.classList.remove(e);else{for(var n=" "+(t.getAttribute("class")||"")+" ",r=" "+e+" ";n.indexOf(r)>=0;)n=n.replace(r," ");t.setAttribute("class",n.trim())}}function Gn(t){if(t){if("object"==typeof t){var e={};return t.css!==!1&&p(e,is(t.name||"v")),p(e,t),e}return"string"==typeof t?is(t):void 0}}function Kn(t){ds(function(){ds(t)})}function Wn(t,e){(t._transitionClasses||(t._transitionClasses=[])).push(e),Jn(t,e)}function Zn(t,e){t._transitionClasses&&a(t._transitionClasses,e),zn(t,e)}function Xn(t,e,n){var r=Yn(t,e),o=r.type,i=r.timeout,a=r.propCount;if(!o)return n();var s=o===ss?ls:ps,u=0,c=function(){t.removeEventListener(s,l),n()},l=function(e){e.target===t&&++u>=a&&c()};setTimeout(function(){u<a&&c()},i+1),t.addEventListener(s,l)}function Yn(t,e){var n,r=window.getComputedStyle(t),o=r[cs+"Delay"].split(", "),i=r[cs+"Duration"].split(", "),a=Qn(o,i),s=r[fs+"Delay"].split(", "),u=r[fs+"Duration"].split(", "),c=Qn(s,u),l=0,f=0;return e===ss?a>0&&(n=ss,l=a,f=i.length):e===us?c>0&&(n=us,l=c,f=u.length):(l=Math.max(a,c),n=l>0?a>c?ss:us:null,f=n?n===ss?i.length:u.length:0),{type:n,timeout:l,propCount:f,hasTransform:n===ss&&vs.test(r[cs+"Property"])}}function Qn(t,e){for(;t.length<e.length;)t=t.concat(t);return Math.max.apply(null,e.map(function(e,n){return tr(e)+tr(t[n])}))}function tr(t){return 1e3*Number(t.slice(0,-1))}function er(t,n){var r=t.elm;r._leaveCb&&(r._leaveCb.cancelled=!0,r._leaveCb());var i=Gn(t.data.transition);if(i&&!r._enterCb&&1===r.nodeType){for(var a=i.css,s=i.type,u=i.enterClass,c=i.enterToClass,l=i.enterActiveClass,f=i.appearClass,p=i.appearToClass,v=i.appearActiveClass,h=i.beforeEnter,m=i.enter,y=i.afterEnter,g=i.enterCancelled,_=i.beforeAppear,w=i.appear,E=i.afterAppear,$=i.appearCancelled,x=i.duration,O=Ki,N=Ki.$vnode;N&&N.parent;)N=N.parent,O=N.context;var k=!O._isMounted||!t.isRootInsert;if(!k||w||""===w){var C=k&&f?f:u,A=k&&v?v:l,T=k&&p?p:c,D=k?_||h:h,S=k&&"function"==typeof w?w:m,j=k?E||y:y,V=k?$||g:g,M=o(d(x)?x.enter:x);"production"!==e.env.NODE_ENV&&null!=M&&rr(M,"enter",t);var P=a!==!1&&!ui,I=ir(S),L=r._enterCb=b(function(){P&&(Zn(r,T),Zn(r,A)),L.cancelled?(P&&Zn(r,C),V&&V(r)):j&&j(r),r._enterCb=null});t.data.show||tt(t.data.hook||(t.data.hook={}),"insert",function(){var e=r.parentNode,n=e&&e._pending&&e._pending[t.key];n&&n.tag===t.tag&&n.elm._leaveCb&&n.elm._leaveCb(),S&&S(r,L)}),D&&D(r),P&&(Wn(r,C),Wn(r,A),Kn(function(){Wn(r,T),Zn(r,C),L.cancelled||I||(or(M)?setTimeout(L,M):Xn(r,s,L))})),t.data.show&&(n&&n(),S&&S(r,L)),P||I||L()}}}function nr(t,n){function r(){$.cancelled||(t.data.show||((i.parentNode._pending||(i.parentNode._pending={}))[t.key]=t),p&&p(i),_&&(Wn(i,c),Wn(i,f),Kn(function(){Wn(i,l),Zn(i,c),$.cancelled||w||(or(E)?setTimeout($,E):Xn(i,u,$))})),v&&v(i,$),_||w||$())}var i=t.elm;i._enterCb&&(i._enterCb.cancelled=!0,i._enterCb());var a=Gn(t.data.transition);if(!a)return n();if(!i._leaveCb&&1===i.nodeType){var s=a.css,u=a.type,c=a.leaveClass,l=a.leaveToClass,f=a.leaveActiveClass,p=a.beforeLeave,v=a.leave,h=a.afterLeave,m=a.leaveCancelled,y=a.delayLeave,g=a.duration,_=s!==!1&&!ui,w=ir(v),E=o(d(g)?g.leave:g);"production"!==e.env.NODE_ENV&&null!=E&&rr(E,"leave",t);var $=i._leaveCb=b(function(){i.parentNode&&i.parentNode._pending&&(i.parentNode._pending[t.key]=null),_&&(Zn(i,l),Zn(i,f)),$.cancelled?(_&&Zn(i,c),m&&m(i)):(n(),h&&h(i)),i._leaveCb=null});y?y(r):r()}}function rr(t,e,n){"number"!=typeof t?wi("<transition> explicit "+e+" duration is not a valid number - got "+JSON.stringify(t)+".",n.context):isNaN(t)&&wi("<transition> explicit "+e+" duration is NaN - the duration expression might be incorrect.",n.context)}function or(t){return"number"==typeof t&&!isNaN(t)}function ir(t){if(!t)return!1;var e=t.fns;return e?ir(Array.isArray(e)?e[0]:e):(t._length||t.length)>1}function ar(t,e){e.data.show||er(e)}function sr(t,n,r){var o=n.value,i=t.multiple;if(i&&!Array.isArray(o))return void("production"!==e.env.NODE_ENV&&wi('<select multiple v-model="'+n.expression+'"> expects an Array value for its binding, but got '+Object.prototype.toString.call(o).slice(8,-1),r));for(var a,s,u=0,c=t.options.length;u<c;u++)if(s=t.options[u],i)a=_(o,cr(s))>-1,s.selected!==a&&(s.selected=a);else if(g(cr(s),o))return void(t.selectedIndex!==u&&(t.selectedIndex=u));i||(t.selectedIndex=-1)}function ur(t,e){for(var n=0,r=e.length;n<r;n++)if(g(cr(e[n]),t))return!1;return!0}function cr(t){return"_value"in t?t._value:t.value}function lr(t){t.target.composing=!0}function fr(t){t.target.composing=!1,pr(t.target,"input")}function pr(t,e){var n=document.createEvent("HTMLEvents");n.initEvent(e,!0,!0),t.dispatchEvent(n)}function dr(t){return!t.componentInstance||t.data&&t.data.transition?t:dr(t.componentInstance._vnode)}function vr(t){var e=t&&t.componentOptions;return e&&e.Ctor.options.abstract?vr(ot(e.children)):t}function hr(t){var e={},n=t.$options;for(var r in n.propsData)e[r]=t[r];var o=n._parentListeners;for(var i in o)e[Wo(i)]=o[i];return e}function mr(t,e){return/\d-keep-alive$/.test(e.tag)?t("keep-alive"):null}function yr(t){for(;t=t.parent;)if(t.data.transition)return!0}function gr(t,e){return e.key===t.key&&e.tag===t.tag}function _r(t){t.elm._moveCb&&t.elm._moveCb(),t.elm._enterCb&&t.elm._enterCb()}function br(t){t.data.newPos=t.elm.getBoundingClientRect()}function wr(t){var e=t.data.pos,n=t.data.newPos,r=e.left-n.left,o=e.top-n.top;if(r||o){t.data.moved=!0;var i=t.elm.style;i.transform=i.WebkitTransform="translate("+r+"px,"+o+"px)",i.transitionDuration="0s"}}function Er(t,e){var n=document.createElement("div");return n.innerHTML='<div a="'+t+'">',n.innerHTML.indexOf(e)>0}function $r(t){return ks=ks||document.createElement("div"),ks.innerHTML=t,ks.textContent}function xr(t,e){var n=e?du:pu;return t.replace(n,function(t){return fu[t]})}function Or(t,n){function r(e){p+=e,t=t.substring(e)}function o(){var e=t.match(Ls);if(e){var n={tagName:e[1],attrs:[],start:p};r(e[0].length);for(var o,i;!(o=t.match(Rs))&&(i=t.match(Ms));)r(i[0].length),n.attrs.push(i);if(o)return n.unarySlash=o[1],r(o[0].length),n.end=p,n}}function i(t){var e=t.tagName,r=t.unarySlash;l&&("p"===u&&Ds(e)&&a(u),Ts(e)&&u===e&&a(e));for(var o=f(e)||"html"===e&&"head"===u||!!r,i=t.attrs.length,s=new Array(i),p=0;p<i;p++){var d=t.attrs[p];qs&&d[0].indexOf('""')===-1&&(""===d[3]&&delete d[3],""===d[4]&&delete d[4],""===d[5]&&delete d[5]);var v=d[3]||d[4]||d[5]||"";s[p]={name:d[1],value:xr(v,n.shouldDecodeNewlines)}}o||(c.push({tag:e,lowerCasedTag:e.toLowerCase(),attrs:s}),u=e),n.start&&n.start(e,s,o,t.start,t.end)}function a(t,r,o){var i,a;if(null==r&&(r=p),null==o&&(o=p),t&&(a=t.toLowerCase()),t)for(i=c.length-1;i>=0&&c[i].lowerCasedTag!==a;i--);else i=0;if(i>=0){for(var s=c.length-1;s>=i;s--)"production"!==e.env.NODE_ENV&&(s>i||!t)&&n.warn&&n.warn("tag <"+c[s].tag+"> has no matching end tag."),n.end&&n.end(c[s].tag,r,o);c.length=i,u=i&&c[i-1].tag}else"br"===a?n.start&&n.start(t,[],!0,r,o):"p"===a&&(n.start&&n.start(t,[],!1,r,o),n.end&&n.end(t,r,o))}for(var s,u,c=[],l=n.expectHTML,f=n.isUnaryTag||ei,p=0;t;){if(s=t,u&&cu(u)){var d=u.toLowerCase(),v=lu[d]||(lu[d]=new RegExp("([\\s\\S]*?)(</"+d+"[^>]*>)","i")),h=0,m=t.replace(v,function(t,e,r){return h=r.length,"script"!==d&&"style"!==d&&"noscript"!==d&&(e=e.replace(/<!--([\s\S]*?)-->/g,"$1").replace(/<!\[CDATA\[([\s\S]*?)]]>/g,"$1")),n.chars&&n.chars(e),""});p+=t.length-m.length,t=m,a(d,p-h,p)}else{var y=t.indexOf("<");if(0===y){if(Hs.test(t)){var g=t.indexOf("-->");if(g>=0){r(g+3);continue}}if(Bs.test(t)){var _=t.indexOf("]>");if(_>=0){r(_+2);continue}}var b=t.match(Fs);if(b){r(b[0].length);continue}var w=t.match(Us);if(w){var E=p;r(w[0].length),a(w[1],E,p);continue}var $=o();if($){i($);continue}}var x=void 0,O=void 0,N=void 0;if(y>=0){for(O=t.slice(y);!(Us.test(O)||Ls.test(O)||Hs.test(O)||Bs.test(O))&&!((N=O.indexOf("<",1))<0);)y+=N,O=t.slice(y);x=t.substring(0,y),r(y)}y<0&&(x=t,t=""),n.chars&&x&&n.chars(x)}if(t===s){n.chars&&n.chars(t),"production"!==e.env.NODE_ENV&&!c.length&&n.warn&&n.warn('Mal-formatted tag at end of template: "'+t+'"');break}}a()}function Nr(t,e){var n=e?mu(e):vu;if(n.test(t)){for(var r,o,i=[],a=n.lastIndex=0;r=n.exec(t);){o=r.index,o>a&&i.push(JSON.stringify(t.slice(a,o)));var s=ln(r[1].trim());i.push("_s("+s+")"),a=o+r[0].length}return a<t.length&&i.push(JSON.stringify(t.slice(a))),i.join("+")}}function kr(t,n){function r(t){t.pre&&(u=!1),Ks(t.tag)&&(c=!1)}Js=n.warn||pn,zs=n.getTagNamespace||ei,Gs=n.mustUseProp||ei,Ks=n.isPreTag||ei,Ws=dn(n.modules,"preTransformNode"),Zs=dn(n.modules,"transformNode"),Xs=dn(n.modules,"postTransformNode"),Ys=n.delimiters;var o,i,a=[],s=n.preserveWhitespace!==!1,u=!1,c=!1,l=!1;return Or(t,{warn:Js,expectHTML:n.expectHTML,isUnaryTag:n.isUnaryTag,shouldDecodeNewlines:n.shouldDecodeNewlines,start:function(t,s,f){function p(t){"production"===e.env.NODE_ENV||l||("slot"!==t.tag&&"template"!==t.tag||(l=!0,Js("Cannot use <"+t.tag+"> as component root element because it may contain multiple nodes.")),t.attrsMap.hasOwnProperty("v-for")&&(l=!0,Js("Cannot use v-for on stateful component root element because it renders multiple elements.")))}var d=i&&i.ns||zs(t);si&&"svg"===d&&(s=Jr(s));var v={type:1,tag:t,attrsList:s,attrsMap:Br(s),parent:i,children:[]};d&&(v.ns=d),qr(v)&&!di()&&(v.forbidden=!0,"production"!==e.env.NODE_ENV&&Js("Templates should only be responsible for mapping the state to the UI. Avoid placing tags with side-effects in your templates, such as <"+t+">, as they will not be parsed."));for(var h=0;h<Ws.length;h++)Ws[h](v,n);if(u||(Cr(v),v.pre&&(u=!0)),Ks(v.tag)&&(c=!0),u)Ar(v);else{Sr(v),jr(v),Ir(v),Tr(v),v.plain=!v.key&&!s.length,Dr(v),Lr(v),Rr(v);for(var m=0;m<Zs.length;m++)Zs[m](v,n);Ur(v)}if(o?a.length||(o.if&&(v.elseif||v.else)?(p(v),Pr(o,{exp:v.elseif,block:v})):"production"===e.env.NODE_ENV||l||(l=!0,Js("Component template should contain exactly one root element. If you are using v-if on multiple elements, use v-else-if to chain them instead."))):(o=v,p(o)),i&&!v.forbidden)if(v.elseif||v.else)Vr(v,i);else if(v.slotScope){i.plain=!1;var y=v.slotTarget||'"default"';(i.scopedSlots||(i.scopedSlots={}))[y]=v}else i.children.push(v),v.parent=i;f?r(v):(i=v,a.push(v));for(var g=0;g<Xs.length;g++)Xs[g](v,n)},end:function(){var t=a[a.length-1],e=t.children[t.children.length-1];e&&3===e.type&&" "===e.text&&!c&&t.children.pop(),a.length-=1,i=a[a.length-1],r(t)},chars:function(n){if(!i)return void("production"===e.env.NODE_ENV||l||n!==t||(l=!0,Js("Component template requires a root element, rather than just text.")));if(!si||"textarea"!==i.tag||i.attrsMap.placeholder!==n){var r=i.children;if(n=c||n.trim()?xu(n):s&&r.length?" ":""){var o;!u&&" "!==n&&(o=Nr(n,Ys))?r.push({type:2,expression:o,text:n}):" "===n&&r.length&&" "===r[r.length-1].text||r.push({type:3,text:n})}}}}),o}function Cr(t){null!=_n(t,"v-pre")&&(t.pre=!0)}function Ar(t){var e=t.attrsList.length;if(e)for(var n=t.attrs=new Array(e),r=0;r<e;r++)n[r]={name:t.attrsList[r].name,value:JSON.stringify(t.attrsList[r].value)};else t.pre||(t.plain=!0)}function Tr(t){var n=gn(t,"key");n&&("production"!==e.env.NODE_ENV&&"template"===t.tag&&Js("<template> cannot be keyed. Place the key on real elements instead."),t.key=n)}function Dr(t){var e=gn(t,"ref");e&&(t.ref=e,t.refInFor=Fr(t))}function Sr(t){var n;if(n=_n(t,"v-for")){var r=n.match(_u);if(!r)return void("production"!==e.env.NODE_ENV&&Js("Invalid v-for expression: "+n));t.for=r[2].trim();var o=r[1].trim(),i=o.match(bu);i?(t.alias=i[1].trim(),t.iterator1=i[2].trim(),i[3]&&(t.iterator2=i[3].trim())):t.alias=o}}function jr(t){var e=_n(t,"v-if");if(e)t.if=e,Pr(t,{exp:e,block:t});else{null!=_n(t,"v-else")&&(t.else=!0);var n=_n(t,"v-else-if");n&&(t.elseif=n)}}function Vr(t,n){var r=Mr(n.children);r&&r.if?Pr(r,{exp:t.elseif,block:t}):"production"!==e.env.NODE_ENV&&Js("v-"+(t.elseif?'else-if="'+t.elseif+'"':"else")+" used on element <"+t.tag+"> without corresponding v-if.")}function Mr(t){for(var n=t.length;n--;){if(1===t[n].type)return t[n];"production"!==e.env.NODE_ENV&&" "!==t[n].text&&Js('text "'+t[n].text.trim()+'" between v-if and v-else(-if) will be ignored.'),t.pop()}}function Pr(t,e){t.ifConditions||(t.ifConditions=[]),t.ifConditions.push(e)}function Ir(t){null!=_n(t,"v-once")&&(t.once=!0)}function Lr(t){if("slot"===t.tag)t.slotName=gn(t,"name"),"production"!==e.env.NODE_ENV&&t.key&&Js("`key` does not work on <slot> because slots are abstract outlets and can possibly expand into multiple elements. Use the key on a wrapping element instead.");else{var n=gn(t,"slot");n&&(t.slotTarget='""'===n?'"default"':n),"template"===t.tag&&(t.slotScope=_n(t,"scope"))}}function Rr(t){var e;(e=gn(t,"is"))&&(t.component=e),null!=_n(t,"inline-template")&&(t.inlineTemplate=!0)}function Ur(t){var n,r,o,i,a,s,u,c,l=t.attrsList;for(n=0,r=l.length;n<r;n++)if(o=i=l[n].name,a=l[n].value,yu.test(o))if(t.hasBindings=!0,u=Hr(o),u&&(o=o.replace($u,"")),wu.test(o))o=o.replace(wu,""),a=ln(a),c=!1,u&&(u.prop&&(c=!0,"innerHtml"===(o=Wo(o))&&(o="innerHTML")),u.camel&&(o=Wo(o))),c||Gs(t.tag,t.attrsMap.type,o)?vn(t,o,a):hn(t,o,a);else if(gu.test(o))o=o.replace(gu,""),yn(t,o,a,u);else{o=o.replace(yu,"");var f=o.match(Eu);f&&(s=f[1])&&(o=o.slice(0,-(s.length+1))),mn(t,o,i,a,s,u),"production"!==e.env.NODE_ENV&&"model"===o&&zr(t,a)}else{if("production"!==e.env.NODE_ENV){var p=Nr(a,Ys);p&&Js(o+'="'+a+'": Interpolation inside attributes has been removed. Use v-bind or the colon shorthand instead. For example, instead of <div id="{{ val }}">, use <div :id="val">.')}hn(t,o,JSON.stringify(a))}}function Fr(t){for(var e=t;e;){if(void 0!==e.for)return!0;e=e.parent}return!1}function Hr(t){var e=t.match($u);if(e){var n={};return e.forEach(function(t){n[t.slice(1)]=!0}),n}}function Br(t){for(var n={},r=0,o=t.length;r<o;r++)"production"!==e.env.NODE_ENV&&n[t[r].name]&&!si&&Js("duplicate attribute: "+t[r].name),n[t[r].name]=t[r].value;return n}function qr(t){return"style"===t.tag||"script"===t.tag&&(!t.attrsMap.type||"text/javascript"===t.attrsMap.type)}function Jr(t){for(var e=[],n=0;n<t.length;n++){var r=t[n];Ou.test(r.name)||(r.name=r.name.replace(Nu,""),e.push(r))}return e}function zr(t,e){for(var n=t;n;)n.for&&n.alias===e&&Js("<"+t.tag+' v-model="'+e+'">: You are binding v-model directly to a v-for iteration alias. This will not be able to modify the v-for source array because writing to the alias is like modifying a function local variable. Consider using an array of objects and use v-model on an object property instead.'),n=n.parent}function Gr(t,e){t&&(Qs=ku(e.staticKeys||""),tu=e.isReservedTag||ei,Wr(t),Zr(t,!1))}function Kr(t){return i("type,tag,attrsList,attrsMap,plain,parent,children,attrs"+(t?","+t:""))}function Wr(t){if(t.static=Yr(t),1===t.type){if(!tu(t.tag)&&"slot"!==t.tag&&null==t.attrsMap["inline-template"])return;for(var e=0,n=t.children.length;e<n;e++){var r=t.children[e];Wr(r),r.static||(t.static=!1)}}}function Zr(t,e){if(1===t.type){if((t.static||t.once)&&(t.staticInFor=e),t.static&&t.children.length&&(1!==t.children.length||3!==t.children[0].type))return void(t.staticRoot=!0);if(t.staticRoot=!1,t.children)for(var n=0,r=t.children.length;n<r;n++)Zr(t.children[n],e||!!t.for);t.ifConditions&&Xr(t.ifConditions,e)}}function Xr(t,e){for(var n=1,r=t.length;n<r;n++)Zr(t[n].block,e)}function Yr(t){return 2!==t.type&&(3===t.type||!(!t.pre&&(t.hasBindings||t.if||t.for||zo(t.tag)||!tu(t.tag)||Qr(t)||!Object.keys(t).every(Qs))))}function Qr(t){for(;t.parent;){if(t=t.parent,"template"!==t.tag)return!1;if(t.for)return!0}return!1}function to(t,e){var n=e?"nativeOn:{":"on:{";for(var r in t)n+='"'+r+'":'+eo(r,t[r])+",";return n.slice(0,-1)+"}"}function eo(t,e){if(!e)return"function(){}";if(Array.isArray(e))return"["+e.map(function(e){return eo(t,e)}).join(",")+"]";var n=Au.test(e.value),r=Cu.test(e.value);if(e.modifiers){var o="",i=[];for(var a in e.modifiers)Su[a]?(o+=Su[a],Tu[a]&&i.push(a)):i.push(a);i.length&&(o+=no(i));return"function($event){"+o+(n?e.value+"($event)":r?"("+e.value+")($event)":e.value)+"}"}return n||r?e.value:"function($event){"+e.value+"}"}function no(t){return"if(!('button' in $event)&&"+t.map(ro).join("&&")+")return null;"}function ro(t){var e=parseInt(t,10);if(e)return"$event.keyCode!=="+e;var n=Tu[t];return"_k($event.keyCode,"+JSON.stringify(t)+(n?","+JSON.stringify(n):"")+")"}function oo(t,e){t.wrapData=function(n){return"_b("+n+",'"+t.tag+"',"+e.value+(e.modifiers&&e.modifiers.prop?",true":"")+")"}}function io(t,e){var n=au,r=au=[],o=su;su=0,uu=e,eu=e.warn||pn,nu=dn(e.modules,"transformCode"),ru=dn(e.modules,"genData"),ou=e.directives||{},iu=e.isReservedTag||ei;var i=t?ao(t):'_c("div")';return au=n,su=o,{render:"with(this){return "+i+"}",staticRenderFns:r}}function ao(t){if(t.staticRoot&&!t.staticProcessed)return so(t);if(t.once&&!t.onceProcessed)return uo(t);if(t.for&&!t.forProcessed)return fo(t);if(t.if&&!t.ifProcessed)return co(t);if("template"!==t.tag||t.slotTarget){if("slot"===t.tag)return xo(t);var e;if(t.component)e=Oo(t.component,t);else{var n=t.plain?void 0:po(t),r=t.inlineTemplate?null:go(t,!0);e="_c('"+t.tag+"'"+(n?","+n:"")+(r?","+r:"")+")"}for(var o=0;o<nu.length;o++)e=nu[o](t,e);return e}return go(t)||"void 0"}function so(t){return t.staticProcessed=!0,au.push("with(this){return "+ao(t)+"}"),"_m("+(au.length-1)+(t.staticInFor?",true":"")+")"}function uo(t){if(t.onceProcessed=!0,t.if&&!t.ifProcessed)return co(t);if(t.staticInFor){for(var n="",r=t.parent;r;){if(r.for){n=r.key;break}r=r.parent}return n?"_o("+ao(t)+","+su+++(n?","+n:"")+")":("production"!==e.env.NODE_ENV&&eu("v-once can only be used inside v-for that is keyed. "),ao(t))}return so(t)}function co(t){return t.ifProcessed=!0,lo(t.ifConditions.slice())}function lo(t){function e(t){return t.once?uo(t):ao(t)}if(!t.length)return"_e()";var n=t.shift();return n.exp?"("+n.exp+")?"+e(n.block)+":"+lo(t):""+e(n.block)}function fo(t){var n=t.for,r=t.alias,o=t.iterator1?","+t.iterator1:"",i=t.iterator2?","+t.iterator2:"";return"production"!==e.env.NODE_ENV&&wo(t)&&"slot"!==t.tag&&"template"!==t.tag&&!t.key&&eu("<"+t.tag+' v-for="'+r+" in "+n+'">: component lists rendered with v-for should have explicit keys. See https://vuejs.org/guide/list.html#key for more info.',!0),t.forProcessed=!0,"_l(("+n+"),function("+r+o+i+"){return "+ao(t)+"})"}function po(t){var e="{",n=vo(t);n&&(e+=n+","),t.key&&(e+="key:"+t.key+","),t.ref&&(e+="ref:"+t.ref+","),t.refInFor&&(e+="refInFor:true,"),t.pre&&(e+="pre:true,"),t.component&&(e+='tag:"'+t.tag+'",');for(var r=0;r<ru.length;r++)e+=ru[r](t);if(t.attrs&&(e+="attrs:{"+No(t.attrs)+"},"),t.props&&(e+="domProps:{"+No(t.props)+"},"),t.events&&(e+=to(t.events)+","),t.nativeEvents&&(e+=to(t.nativeEvents,!0)+","),t.slotTarget&&(e+="slot:"+t.slotTarget+","),t.scopedSlots&&(e+=mo(t.scopedSlots)+","),t.model&&(e+="model:{value:"+t.model.value+",callback:"+t.model.callback+",expression:"+t.model.expression+"},"),t.inlineTemplate){var o=ho(t);o&&(e+=o+",")}return e=e.replace(/,$/,"")+"}",t.wrapData&&(e=t.wrapData(e)),e}function vo(t){var e=t.directives;if(e){var n,r,o,i,a="directives:[",s=!1;for(n=0,r=e.length;n<r;n++){o=e[n],i=!0;var u=ou[o.name]||ju[o.name];u&&(i=!!u(t,o,eu)),i&&(s=!0,a+='{name:"'+o.name+'",rawName:"'+o.rawName+'"'+(o.value?",value:("+o.value+"),expression:"+JSON.stringify(o.value):"")+(o.arg?',arg:"'+o.arg+'"':"")+(o.modifiers?",modifiers:"+JSON.stringify(o.modifiers):"")+"},")}return s?a.slice(0,-1)+"]":void 0}}function ho(t){var n=t.children[0];if("production"!==e.env.NODE_ENV&&(t.children.length>1||1!==n.type)&&eu("Inline-template components must have exactly one child element."),1===n.type){var r=io(n,uu);return"inlineTemplate:{render:function(){"+r.render+"},staticRenderFns:["+r.staticRenderFns.map(function(t){return"function(){"+t+"}"}).join(",")+"]}"}}function mo(t){return"scopedSlots:_u(["+Object.keys(t).map(function(e){return yo(e,t[e])}).join(",")+"])"}function yo(t,e){return"["+t+",function("+String(e.attrsMap.scope)+"){return "+("template"===e.tag?go(e)||"void 0":ao(e))+"}]"}function go(t,e){var n=t.children;if(n.length){var r=n[0];if(1===n.length&&r.for&&"template"!==r.tag&&"slot"!==r.tag)return ao(r);var o=e?_o(n):0;return"["+n.map(Eo).join(",")+"]"+(o?","+o:"")}}function _o(t){for(var e=0,n=0;n<t.length;n++){var r=t[n];if(1===r.type){if(bo(r)||r.ifConditions&&r.ifConditions.some(function(t){return bo(t.block)})){e=2;break}(wo(r)||r.ifConditions&&r.ifConditions.some(function(t){return wo(t.block)}))&&(e=1)}}return e}function bo(t){return void 0!==t.for||"template"===t.tag||"slot"===t.tag}function wo(t){return!iu(t.tag)}function Eo(t){return 1===t.type?ao(t):$o(t)}function $o(t){return"_v("+(2===t.type?t.expression:ko(JSON.stringify(t.text)))+")"}function xo(t){var e=t.slotName||'"default"',n=go(t),r="_t("+e+(n?","+n:""),o=t.attrs&&"{"+t.attrs.map(function(t){return Wo(t.name)+":"+t.value}).join(",")+"}",i=t.attrsMap["v-bind"];return!o&&!i||n||(r+=",null"),o&&(r+=","+o),i&&(r+=(o?"":",null")+","+i),r+")"}function Oo(t,e){var n=e.inlineTemplate?null:go(e,!0);return"_c("+t+","+po(e)+(n?","+n:"")+")"}function No(t){for(var e="",n=0;n<t.length;n++){var r=t[n];e+='"'+r.name+'":'+ko(r.value)+","}return e.slice(0,-1)}function ko(t){return t.replace(/\u2028/g,"\\u2028").replace(/\u2029/g,"\\u2029")}function Co(t){var e=[];return t&&Ao(t,e),e}function Ao(t,e){if(1===t.type){for(var n in t.attrsMap)if(yu.test(n)){var r=t.attrsMap[n];r&&("v-for"===n?Do(t,'v-for="'+r+'"',e):gu.test(n)?To(r,n+'="'+r+'"',e):jo(r,n+'="'+r+'"',e))}if(t.children)for(var o=0;o<t.children.length;o++)Ao(t.children[o],e)}else 2===t.type&&jo(t.expression,t.text,e)}function To(t,e,n){var r=t.replace(Iu,"").match(Mu);r&&n.push('avoid using JavaScript unary operator as property name: "'+r[0]+'" in expression '+e.trim()),jo(t,e,n)}function Do(t,e,n){jo(t.for||"",e,n),So(t.alias,"v-for alias",e,n),So(t.iterator1,"v-for iterator",e,n),So(t.iterator2,"v-for iterator",e,n)}function So(t,e,n,r){"string"!=typeof t||Pu.test(t)||r.push("invalid "+e+' "'+t+'" in expression: '+n.trim())}function jo(t,e,n){try{new Function("return "+t)}catch(o){var r=t.replace(Iu,"").match(Vu);r?n.push('avoid using JavaScript keyword as property name: "'+r[0]+'" in expression '+e.trim()):n.push("invalid expression: "+e.trim())}}function Vo(t,e){var n=kr(t.trim(),e);Gr(n,e);var r=io(n,e);return{ast:n,render:r.render,staticRenderFns:r.staticRenderFns}}function Mo(t,e){try{return new Function(t)}catch(n){return e.push({err:n,code:t}),m}}function Po(t){function n(n,r){var o=Object.create(t),i=[],a=[];if(o.warn=function(t,e){(e?a:i).push(t)},r){r.modules&&(o.modules=(t.modules||[]).concat(r.modules)),r.directives&&(o.directives=p(Object.create(t.directives),r.directives));for(var s in r)"modules"!==s&&"directives"!==s&&(o[s]=r[s])}var u=Vo(n,o);return"production"!==e.env.NODE_ENV&&i.push.apply(i,Co(u.ast)),u.errors=i,u.tips=a,u}function r(t,r,i){if(r=r||{},"production"!==e.env.NODE_ENV)try{new Function("return 1")}catch(t){t.toString().match(/unsafe-eval|CSP/)&&wi("It seems you are using the standalone build of Vue.js in an environment with Content Security Policy that prohibits unsafe-eval. The template compiler cannot work in this environment. Consider relaxing the policy to allow unsafe-eval or pre-compiling your templates into render functions.")}var a=r.delimiters?String(r.delimiters)+t:t;if(o[a])return o[a];var s=n(t,r);"production"!==e.env.NODE_ENV&&(s.errors&&s.errors.length&&wi("Error compiling template:\n\n"+t+"\n\n"+s.errors.map(function(t){return"- "+t}).join("\n")+"\n",i),s.tips&&s.tips.length&&s.tips.forEach(function(t){return Ei(t,i)}));var u={},c=[];u.render=Mo(s.render,c);var l=s.staticRenderFns.length;u.staticRenderFns=new Array(l);for(var f=0;f<l;f++)u.staticRenderFns[f]=Mo(s.staticRenderFns[f],c);return"production"!==e.env.NODE_ENV&&(s.errors&&s.errors.length||!c.length||wi("Failed to generate render function:\n\n"+c.map(function(t){var e=t.err,n=t.code;return e.toString()+" in\n\n"+n+"\n"}).join("\n"),i)),o[a]=u}var o=Object.create(null);return{compile:n,compileToFunctions:r}}function Io(t,n){var r=n.warn||pn,o=_n(t,"class");if("production"!==e.env.NODE_ENV&&o){Nr(o,n.delimiters)&&r('class="'+o+'": Interpolation inside attributes has been removed. Use v-bind or the colon shorthand instead. For example, instead of <div class="{{ val }}">, use <div :class="val">.')}o&&(t.staticClass=JSON.stringify(o));var i=gn(t,"class",!1);i&&(t.classBinding=i)}function Lo(t){var e="";return t.staticClass&&(e+="staticClass:"+t.staticClass+","),t.classBinding&&(e+="class:"+t.classBinding+","),e}function Ro(t,n){var r=n.warn||pn,o=_n(t,"style");if(o){if("production"!==e.env.NODE_ENV){Nr(o,n.delimiters)&&r('style="'+o+'": Interpolation inside attributes has been removed. Use v-bind or the colon shorthand instead. For example, instead of <div style="{{ val }}">, use <div :style="val">.')}t.staticStyle=JSON.stringify(Ya(o))}var i=gn(t,"style",!1);i&&(t.styleBinding=i)}function Uo(t){var e="";return t.staticStyle&&(e+="staticStyle:"+t.staticStyle+","),t.styleBinding&&(e+="style:("+t.styleBinding+"),"),e}function Fo(t,e){e.value&&vn(t,"textContent","_s("+e.value+")")}function Ho(t,e){e.value&&vn(t,"innerHTML","_s("+e.value+")")}function Bo(t){if(t.outerHTML)return t.outerHTML;var e=document.createElement("div");return e.appendChild(t.cloneNode(!0)),e.innerHTML}var qo,Jo,zo=i("slot,component",!0),Go=Object.prototype.hasOwnProperty,Ko=/-(\w)/g,Wo=c(function(t){return t.replace(Ko,function(t,e){return e?e.toUpperCase():""})}),Zo=c(function(t){return t.charAt(0).toUpperCase()+t.slice(1)}),Xo=/([^-])([A-Z])/g,Yo=c(function(t){return t.replace(Xo,"$1-$2").replace(Xo,"$1-$2").toLowerCase()}),Qo=Object.prototype.toString,ti="[object Object]",ei=function(){return!1},ni=function(t){return t},ri={optionMergeStrategies:Object.create(null),silent:!1,productionTip:"production"!==e.env.NODE_ENV,devtools:"production"!==e.env.NODE_ENV,performance:"production"!==e.env.NODE_ENV,errorHandler:null,ignoredElements:[],keyCodes:Object.create(null),isReservedTag:ei,isUnknownElement:ei,getTagNamespace:m,parsePlatformTagName:ni,mustUseProp:ei,_assetTypes:["component","directive","filter"],_lifecycleHooks:["beforeCreate","created","beforeMount","mounted","beforeUpdate","updated","beforeDestroy","destroyed","activated","deactivated"],_maxUpdateCount:100},oi="__proto__"in{},ii="undefined"!=typeof window,ai=ii&&window.navigator.userAgent.toLowerCase(),si=ai&&/msie|trident/.test(ai),ui=ai&&ai.indexOf("msie 9.0")>0,ci=ai&&ai.indexOf("edge/")>0,li=ai&&ai.indexOf("android")>0,fi=ai&&/iphone|ipad|ipod|ios/.test(ai),pi=ai&&/chrome\/\d+/.test(ai)&&!ci,di=function(){return void 0===qo&&(qo=!ii&&void 0!==n&&"server"===n.process.env.VUE_ENV),qo},vi=ii&&window.__VUE_DEVTOOLS_GLOBAL_HOOK__,hi="undefined"!=typeof Symbol&&w(Symbol)&&"undefined"!=typeof Reflect&&w(Reflect.ownKeys),mi=function(){function t(){r=!1;var t=n.slice(0);n.length=0;for(var e=0;e<t.length;e++)t[e]()}var e,n=[],r=!1;if("undefined"!=typeof Promise&&w(Promise)){var o=Promise.resolve(),i=function(t){};e=function(){o.then(t).catch(i),fi&&setTimeout(m)}}else if("undefined"==typeof MutationObserver||!w(MutationObserver)&&"[object MutationObserverConstructor]"!==MutationObserver.toString())e=function(){setTimeout(t,0)};else{var a=1,s=new MutationObserver(t),u=document.createTextNode(String(a));s.observe(u,{characterData:!0}),e=function(){a=(a+1)%2,u.data=String(a)}}return function(t,o){var i;if(n.push(function(){t&&t.call(o),i&&i(o)}),r||(r=!0,e()),!t&&"undefined"!=typeof Promise)return new Promise(function(t){i=t})}}();Jo="undefined"!=typeof Set&&w(Set)?Set:function(){function t(){this.set=Object.create(null)}return t.prototype.has=function(t){return this.set[t]===!0},t.prototype.add=function(t){this.set[t]=!0},t.prototype.clear=function(){this.set=Object.create(null)},t}();var yi;"production"!==e.env.NODE_ENV&&(!(yi=ii&&window.performance)||yi.mark&&yi.measure||(yi=void 0));var gi,_i=Object.freeze({}),bi=/[^\w.$]/,wi=m,Ei=m;if("production"!==e.env.NODE_ENV){var $i="undefined"!=typeof console,xi=/(?:^|[-_])(\w)/g,Oi=function(t){return t.replace(xi,function(t){return t.toUpperCase()}).replace(/[-_]/g,"")};wi=function(t,e){$i&&ri.silent},Ei=function(t,e){$i&&ri.silent},gi=function(t,e){if(t.$root===t)return"<Root>";var n=t._isVue?t.$options.name||t.$options._componentTag:t.name,r=t._isVue&&t.$options.__file;if(!n&&r){var o=r.match(/([^\/\\]+)\.vue$/);n=o&&o[1]}return(n?"<"+Oi(n)+">":"<Anonymous>")+(r&&e!==!1?" at "+r:"")}}var Ni=0,ki=function(){this.id=Ni++,this.subs=[]};ki.prototype.addSub=function(t){this.subs.push(t)},ki.prototype.removeSub=function(t){a(this.subs,t)},ki.prototype.depend=function(){ki.target&&ki.target.addDep(this)},ki.prototype.notify=function(){for(var t=this.subs.slice(),e=0,n=t.length;e<n;e++)t[e].update()},ki.target=null;var Ci=[],Ai=Array.prototype,Ti=Object.create(Ai);["push","pop","shift","unshift","splice","sort","reverse"].forEach(function(t){var e=Ai[t];$(Ti,t,function(){for(var n=arguments,r=arguments.length,o=new Array(r);r--;)o[r]=n[r];var i,a=e.apply(this,o),s=this.__ob__;switch(t){case"push":i=o;break;case"unshift":i=o;break;case"splice":i=o.slice(2)}return i&&s.observeArray(i),s.dep.notify(),a})});var Di=Object.getOwnPropertyNames(Ti),Si={shouldConvert:!0,isSettingProps:!1},ji=function(t){if(this.value=t,this.dep=new ki,this.vmCount=0,$(t,"__ob__",this),Array.isArray(t)){(oi?k:C)(t,Ti,Di),this.observeArray(t)}else this.walk(t)};ji.prototype.walk=function(t){for(var e=Object.keys(t),n=0;n<e.length;n++)T(t,e[n],t[e[n]])},ji.prototype.observeArray=function(t){for(var e=0,n=t.length;e<n;e++)A(t[e])};var Vi=ri.optionMergeStrategies;"production"!==e.env.NODE_ENV&&(Vi.el=Vi.propsData=function(t,e,n,r){return n||wi('option "'+r+'" can only be used during instance creation with the `new` keyword.'),Pi(t,e)}),Vi.data=function(t,n,r){return r?t||n?function(){var e="function"==typeof n?n.call(r):n,o="function"==typeof t?t.call(r):void 0;return e?V(e,o):o}:void 0:n?"function"!=typeof n?("production"!==e.env.NODE_ENV&&wi('The "data" option should be a function that returns a per-instance value in component definitions.',r),t):t?function(){return V(n.call(this),t.call(this))}:n:t},ri._lifecycleHooks.forEach(function(t){Vi[t]=M}),ri._assetTypes.forEach(function(t){Vi[t+"s"]=P}),Vi.watch=function(t,e){if(!e)return Object.create(t||null);if(!t)return e;var n={};p(n,t);for(var r in e){var o=n[r],i=e[r];o&&!Array.isArray(o)&&(o=[o]),n[r]=o?o.concat(i):[i]}return n},Vi.props=Vi.methods=Vi.computed=function(t,e){if(!e)return Object.create(t||null);if(!t)return e;var n=Object.create(null);return p(n,t),p(n,e),n};var Mi,Pi=function(t,e){return void 0===e?t:e};if("production"!==e.env.NODE_ENV){var Ii=i("Infinity,undefined,NaN,isFinite,isNaN,parseFloat,parseInt,decodeURI,decodeURIComponent,encodeURI,encodeURIComponent,Math,Number,Date,Array,Object,Boolean,String,RegExp,Map,Set,JSON,Intl,require"),Li=function(t,e){wi('Property or method "'+e+'" is not defined on the instance but referenced during render. Make sure to declare reactive data properties in the data option.',t)},Ri="undefined"!=typeof Proxy&&Proxy.toString().match(/native code/);if(Ri){var Ui=i("stop,prevent,self,ctrl,shift,alt,meta");ri.keyCodes=new Proxy(ri.keyCodes,{set:function(t,e,n){return Ui(e)?(wi("Avoid overwriting built-in modifier in config.keyCodes: ."+e),!1):(t[e]=n,!0)}})}var Fi={has:function t(e,n){var t=n in e,r=Ii(n)||"_"===n.charAt(0);return t||r||Li(e,n),t||!r}},Hi={get:function(t,e){return"string"!=typeof e||e in t||Li(t,e),t[e]}};Mi=function(t){if(Ri){var e=t.$options,n=e.render&&e.render._withStripped?Hi:Fi;t._renderProxy=new Proxy(t,n)}else t._renderProxy=t}}var Bi=function(t,e,n,r,o,i,a){this.tag=t,this.data=e,this.children=n,this.text=r,this.elm=o,this.ns=void 0,this.context=i,this.functionalContext=void 0,this.key=e&&e.key,this.componentOptions=a,this.componentInstance=void 0,this.parent=void 0,this.raw=!1,this.isStatic=!1,this.isRootInsert=!0,this.isComment=!1,this.isCloned=!1,this.isOnce=!1},qi={child:{}};qi.child.get=function(){return this.componentInstance},Object.defineProperties(Bi.prototype,qi);var Ji,zi=function(){var t=new Bi;return t.text="",t.isComment=!0,t},Gi=c(function(t){var e="~"===t.charAt(0);t=e?t.slice(1):t;var n="!"===t.charAt(0);return t=n?t.slice(1):t,{name:t,once:e,capture:n}}),Ki=null,Wi=[],Zi={},Xi={},Yi=!1,Qi=!1,ta=0,ea=0,na=function(t,n,r,o){this.vm=t,t._watchers.push(this),o?(this.deep=!!o.deep,this.user=!!o.user,this.lazy=!!o.lazy,this.sync=!!o.sync):this.deep=this.user=this.lazy=this.sync=!1,this.cb=r,this.id=++ea,this.active=!0,this.dirty=this.lazy,this.deps=[],this.newDeps=[],this.depIds=new Jo,this.newDepIds=new Jo,this.expression="production"!==e.env.NODE_ENV?n.toString():"","function"==typeof n?this.getter=n:(this.getter=x(n),this.getter||(this.getter=function(){},"production"!==e.env.NODE_ENV&&wi('Failed watching path: "'+n+'" Watcher only accepts simple dot-delimited paths. For full control, use a function instead.',t))),this.value=this.lazy?void 0:this.get()};na.prototype.get=function(){O(this);var t,e=this.vm;if(this.user)try{t=this.getter.call(e,e)}catch(t){K(t,e,'getter for watcher "'+this.expression+'"')}else t=this.getter.call(e,e);return this.deep&&xt(t),N(),this.cleanupDeps(),t},na.prototype.addDep=function(t){var e=t.id;this.newDepIds.has(e)||(this.newDepIds.add(e),this.newDeps.push(t),this.depIds.has(e)||t.addSub(this))},na.prototype.cleanupDeps=function(){for(var t=this,e=this.deps.length;e--;){var n=t.deps[e];t.newDepIds.has(n.id)||n.removeSub(t)}var r=this.depIds;this.depIds=this.newDepIds,this.newDepIds=r,this.newDepIds.clear(),r=this.deps,this.deps=this.newDeps,this.newDeps=r,this.newDeps.length=0},na.prototype.update=function(){this.lazy?this.dirty=!0:this.sync?this.run():$t(this)},na.prototype.run=function(){if(this.active){var t=this.get();if(t!==this.value||d(t)||this.deep){var e=this.value;if(this.value=t,this.user)try{this.cb.call(this.vm,t,e)}catch(t){K(t,this.vm,'callback for watcher "'+this.expression+'"')}else this.cb.call(this.vm,t,e)}}},na.prototype.evaluate=function(){this.value=this.get(),this.dirty=!1},na.prototype.depend=function(){for(var t=this,e=this.deps.length;e--;)t.deps[e].depend()},na.prototype.teardown=function(){var t=this;if(this.active){this.vm._isBeingDestroyed||a(this.vm._watchers,this);for(var e=this.deps.length;e--;)t.deps[e].removeSub(t);this.active=!1}};var ra=new Jo,oa={enumerable:!0,configurable:!0,get:m,set:m},ia={key:1,ref:1,slot:1},aa={lazy:!0},sa={init:Ut,prepatch:Ft,insert:Ht,destroy:Bt},ua=Object.keys(sa),ca=1,la=2,fa=0;pe(ye),Pt(ye),ct(ye),vt(ye),ce(ye);var pa=[String,RegExp],da={name:"keep-alive",abstract:!0,props:{include:pa,exclude:pa},created:function(){this.cache=Object.create(null)},destroyed:function(){var t=this;for(var e in t.cache)ke(t.cache[e])},watch:{include:function(t){Ne(this.cache,function(e){return Oe(t,e)})},exclude:function(t){Ne(this.cache,function(e){return!Oe(t,e)})}},render:function(){var t=ot(this.$slots.default),e=t&&t.componentOptions;if(e){var n=xe(e);if(n&&(this.include&&!Oe(this.include,n)||this.exclude&&Oe(this.exclude,n)))return t;var r=null==t.key?e.Ctor.cid+(e.tag?"::"+e.tag:""):t.key;this.cache[r]?t.componentInstance=this.cache[r].componentInstance:this.cache[r]=t,t.data.keepAlive=!0}return t}},va={KeepAlive:da};Ce(ye),Object.defineProperty(ye.prototype,"$isServer",{get:di}),ye.version="2.2.2";var ha,ma,ya,ga,_a,ba,wa,Ea,$a,xa=i("input,textarea,option,select"),Oa=function(t,e,n){return"value"===n&&xa(t)&&"button"!==e||"selected"===n&&"option"===t||"checked"===n&&"input"===t||"muted"===n&&"video"===t},Na=i("contenteditable,draggable,spellcheck"),ka=i("allowfullscreen,async,autofocus,autoplay,checked,compact,controls,declare,default,defaultchecked,defaultmuted,defaultselected,defer,disabled,enabled,formnovalidate,hidden,indeterminate,inert,ismap,itemscope,loop,multiple,muted,nohref,noresize,noshade,novalidate,nowrap,open,pauseonexit,readonly,required,reversed,scoped,seamless,selected,sortable,translate,truespeed,typemustmatch,visible"),Ca="http://www.w3.org/1999/xlink",Aa=function(t){return":"===t.charAt(5)&&"xlink"===t.slice(0,5)},Ta=function(t){return Aa(t)?t.slice(6,t.length):""},Da=function(t){return null==t||t===!1},Sa={svg:"http://www.w3.org/2000/svg",math:"http://www.w3.org/1998/Math/MathML"},ja=i("html,body,base,head,link,meta,style,title,address,article,aside,footer,header,h1,h2,h3,h4,h5,h6,hgroup,nav,section,div,dd,dl,dt,figcaption,figure,hr,img,li,main,ol,p,pre,ul,a,b,abbr,bdi,bdo,br,cite,code,data,dfn,em,i,kbd,mark,q,rp,rt,rtc,ruby,s,samp,small,span,strong,sub,sup,time,u,var,wbr,area,audio,map,track,video,embed,object,param,source,canvas,script,noscript,del,ins,caption,col,colgroup,table,thead,tbody,td,th,tr,button,datalist,fieldset,form,input,label,legend,meter,optgroup,option,output,progress,select,textarea,details,dialog,menu,menuitem,summary,content,element,shadow,template"),Va=i("svg,animate,circle,clippath,cursor,defs,desc,ellipse,filter,font-face,foreignObject,g,glyph,image,line,marker,mask,missing-glyph,path,pattern,polygon,polyline,rect,switch,symbol,text,textpath,tspan,use,view",!0),Ma=function(t){return"pre"===t},Pa=function(t){return ja(t)||Va(t)},Ia=Object.create(null),La=Object.freeze({createElement:Ie,createElementNS:Le,createTextNode:Re,createComment:Ue,insertBefore:Fe,removeChild:He,appendChild:Be,parentNode:qe,nextSibling:Je,tagName:ze,setTextContent:Ge,setAttribute:Ke}),Ra={create:function(t,e){We(e)},update:function(t,e){t.data.ref!==e.data.ref&&(We(t,!0),We(e))},destroy:function(t){We(t,!0)}},Ua=new Bi("",{},[]),Fa=["create","activate","update","remove","destroy"],Ha={create:en,update:en,destroy:function(t){en(t,Ua)}},Ba=Object.create(null),qa=[Ra,Ha],Ja={create:sn,update:sn},za={create:cn,update:cn},Ga=/[\w).+\-_$\]]/,Ka="__r",Wa="__c",Za={create:Pn,update:Pn},Xa={create:In,update:In},Ya=c(function(t){var e={},n=/;(?![^(]*\))/g,r=/:(.+)/;return t.split(n).forEach(function(t){if(t){var n=t.split(r);n.length>1&&(e[n[0].trim()]=n[1].trim())}}),e}),Qa=/^--/,ts=/\s*!important$/,es=function(t,e,n){Qa.test(e)?t.style.setProperty(e,n):ts.test(n)?t.style.setProperty(e,n.replace(ts,""),"important"):t.style[rs(e)]=n},ns=["Webkit","Moz","ms"],rs=c(function(t){if($a=$a||document.createElement("div"),"filter"!==(t=Wo(t))&&t in $a.style)return t;for(var e=t.charAt(0).toUpperCase()+t.slice(1),n=0;n<ns.length;n++){var r=ns[n]+e;if(r in $a.style)return r}}),os={create:qn,update:qn},is=c(function(t){return{enterClass:t+"-enter",enterToClass:t+"-enter-to",enterActiveClass:t+"-enter-active",leaveClass:t+"-leave",leaveToClass:t+"-leave-to",leaveActiveClass:t+"-leave-active"}}),as=ii&&!ui,ss="transition",us="animation",cs="transition",ls="transitionend",fs="animation",ps="animationend";as&&(void 0===window.ontransitionend&&void 0!==window.onwebkittransitionend&&(cs="WebkitTransition",ls="webkitTransitionEnd"),void 0===window.onanimationend&&void 0!==window.onwebkitanimationend&&(fs="WebkitAnimation",ps="webkitAnimationEnd"));var ds=ii&&window.requestAnimationFrame?window.requestAnimationFrame.bind(window):setTimeout,vs=/\b(transform|all)(,|$)/,hs=ii?{create:ar,activate:ar,remove:function(t,e){t.data.show?e():nr(t,e)}}:{},ms=[Ja,za,Za,Xa,os,hs],ys=ms.concat(qa),gs=tn({nodeOps:La,modules:ys});ui&&document.addEventListener("selectionchange",function(){var t=document.activeElement;t&&t.vmodel&&pr(t,"input")});var _s={inserted:function(t,e,n){if("select"===n.tag){var r=function(){sr(t,e,n.context)};r(),(si||ci)&&setTimeout(r,0)}else"textarea"!==n.tag&&"text"!==t.type||(t._vModifiers=e.modifiers,e.modifiers.lazy||(li||(t.addEventListener("compositionstart",lr),t.addEventListener("compositionend",fr)),ui&&(t.vmodel=!0)))},componentUpdated:function(t,e,n){if("select"===n.tag){sr(t,e,n.context);(t.multiple?e.value.some(function(e){return ur(e,t.options)}):e.value!==e.oldValue&&ur(e.value,t.options))&&pr(t,"change")}}},bs={bind:function(t,e,n){var r=e.value;n=dr(n);var o=n.data&&n.data.transition,i=t.__vOriginalDisplay="none"===t.style.display?"":t.style.display;r&&o&&!ui?(n.data.show=!0,er(n,function(){t.style.display=i})):t.style.display=r?i:"none"},update:function(t,e,n){var r=e.value;r!==e.oldValue&&(n=dr(n),n.data&&n.data.transition&&!ui?(n.data.show=!0,r?er(n,function(){t.style.display=t.__vOriginalDisplay}):nr(n,function(){t.style.display="none"})):t.style.display=r?t.__vOriginalDisplay:"none")},unbind:function(t,e,n,r,o){o||(t.style.display=t.__vOriginalDisplay)}},ws={model:_s,show:bs},Es={name:String,appear:Boolean,css:Boolean,mode:String,type:String,enterClass:String,leaveClass:String,enterToClass:String,leaveToClass:String,enterActiveClass:String,leaveActiveClass:String,appearClass:String,appearActiveClass:String,appearToClass:String,duration:[Number,String,Object]},$s={name:"transition",props:Es,abstract:!0,render:function(t){var n=this,r=this.$slots.default;if(r&&(r=r.filter(function(t){return t.tag}),r.length)){"production"!==e.env.NODE_ENV&&r.length>1&&wi("<transition> can only be used on a single element. Use <transition-group> for lists.",this.$parent);var o=this.mode;"production"!==e.env.NODE_ENV&&o&&"in-out"!==o&&"out-in"!==o&&wi("invalid <transition> mode: "+o,this.$parent);var i=r[0];if(yr(this.$vnode))return i;var a=vr(i);if(!a)return i;if(this._leaving)return mr(t,i);var s="__transition-"+this._uid+"-";a.key=null==a.key?s+a.tag:u(a.key)?0===String(a.key).indexOf(s)?a.key:s+a.key:a.key;var c=(a.data||(a.data={})).transition=hr(this),l=this._vnode,f=vr(l);if(a.data.directives&&a.data.directives.some(function(t){return"show"===t.name})&&(a.data.show=!0),f&&f.data&&!gr(a,f)){var d=f&&(f.data.transition=p({},c));if("out-in"===o)return this._leaving=!0,tt(d,"afterLeave",function(){n._leaving=!1,n.$forceUpdate()}),mr(t,i);if("in-out"===o){var v,h=function(){v()};tt(c,"afterEnter",h),tt(c,"enterCancelled",h),tt(d,"delayLeave",function(t){v=t})}}return i}}},xs=p({tag:String,moveClass:String},Es);delete xs.mode;var Os={props:xs,render:function(t){for(var n=this.tag||this.$vnode.data.tag||"span",r=Object.create(null),o=this.prevChildren=this.children,i=this.$slots.default||[],a=this.children=[],s=hr(this),u=0;u<i.length;u++){var c=i[u];if(c.tag)if(null!=c.key&&0!==String(c.key).indexOf("__vlist"))a.push(c),r[c.key]=c,(c.data||(c.data={})).transition=s;else if("production"!==e.env.NODE_ENV){var l=c.componentOptions,f=l?l.Ctor.options.name||l.tag||"":c.tag;wi("<transition-group> children must be keyed: <"+f+">")}}if(o){for(var p=[],d=[],v=0;v<o.length;v++){var h=o[v];h.data.transition=s,h.data.pos=h.elm.getBoundingClientRect(),r[h.key]?p.push(h):d.push(h)}this.kept=t(n,null,p),this.removed=d}return t(n,null,a)},beforeUpdate:function(){this.__patch__(this._vnode,this.kept,!1,!0),this._vnode=this.kept},updated:function(){var t=this.prevChildren,e=this.moveClass||(this.name||"v")+"-move";if(t.length&&this.hasMove(t[0].elm,e)){t.forEach(_r),t.forEach(br),t.forEach(wr);var n=document.body;n.offsetHeight;t.forEach(function(t){if(t.data.moved){var n=t.elm,r=n.style;Wn(n,e),r.transform=r.WebkitTransform=r.transitionDuration="",n.addEventListener(ls,n._moveCb=function t(r){r&&!/transform$/.test(r.propertyName)||(n.removeEventListener(ls,t),n._moveCb=null,Zn(n,e))})}})}},methods:{hasMove:function(t,e){if(!as)return!1;if(null!=this._hasMove)return this._hasMove;var n=t.cloneNode();t._transitionClasses&&t._transitionClasses.forEach(function(t){zn(n,t)}),Jn(n,e),n.style.display="none",this.$el.appendChild(n);var r=Yn(n);return this.$el.removeChild(n),this._hasMove=r.hasTransform}}},Ns={Transition:$s,TransitionGroup:Os};ye.config.mustUseProp=Oa,ye.config.isReservedTag=Pa,ye.config.getTagNamespace=Ve,ye.config.isUnknownElement=Me,p(ye.options.directives,ws),p(ye.options.components,Ns),ye.prototype.__patch__=ii?gs:m,ye.prototype.$mount=function(t,e){return t=t&&ii?Pe(t):void 0,ht(this,t,e)},setTimeout(function(){ri.devtools&&(vi?vi.emit("init",ye):e.env.NODE_ENV),"production"!==e.env.NODE_ENV&&ri.productionTip},0);var ks,Cs=!!ii&&Er("\n","&#10;"),As=i("area,base,br,col,embed,frame,hr,img,input,isindex,keygen,link,meta,param,source,track,wbr"),Ts=i("colgroup,dd,dt,li,options,p,td,tfoot,th,thead,tr,source"),Ds=i("address,article,aside,base,blockquote,body,caption,col,colgroup,dd,details,dialog,div,dl,dt,fieldset,figcaption,figure,footer,form,h1,h2,h3,h4,h5,h6,head,header,hgroup,hr,html,legend,li,menuitem,meta,optgroup,option,param,rp,rt,source,style,summary,tbody,td,tfoot,th,thead,title,tr,track"),Ss=/([^\s"'<>\/=]+)/,js=/(?:=)/,Vs=[/"([^"]*)"+/.source,/'([^']*)'+/.source,/([^\s"'=<>`]+)/.source],Ms=new RegExp("^\\s*"+Ss.source+"(?:\\s*("+js.source+")\\s*(?:"+Vs.join("|")+"))?"),Ps="[a-zA-Z_][\\w\\-\\.]*",Is="((?:"+Ps+"\\:)?"+Ps+")",Ls=new RegExp("^<"+Is),Rs=/^\s*(\/?)>/,Us=new RegExp("^<\\/"+Is+"[^>]*>"),Fs=/^<!DOCTYPE [^>]+>/i,Hs=/^<!--/,Bs=/^<!\[/,qs=!1;"x".replace(/x(.)?/g,function(t,e){qs=""===e});var Js,zs,Gs,Ks,Ws,Zs,Xs,Ys,Qs,tu,eu,nu,ru,ou,iu,au,su,uu,cu=i("script,style",!0),lu={},fu={"&lt;":"<","&gt;":">","&quot;":'"',"&amp;":"&","&#10;":"\n"},pu=/&(?:lt|gt|quot|amp);/g,du=/&(?:lt|gt|quot|amp|#10);/g,vu=/\{\{((?:.|\n)+?)\}\}/g,hu=/[-.*+?^${}()|[\]\/\\]/g,mu=c(function(t){var e=t[0].replace(hu,"\\$&"),n=t[1].replace(hu,"\\$&");return new RegExp(e+"((?:.|\\n)+?)"+n,"g")}),yu=/^v-|^@|^:/,gu=/^@|^v-on:/,_u=/(.*?)\s+(?:in|of)\s+(.*)/,bu=/\((\{[^}]*\}|[^,]*),([^,]*)(?:,([^,]*))?\)/,wu=/^:|^v-bind:/,Eu=/:(.*)$/,$u=/\.[^.]+/g,xu=c($r),Ou=/^xmlns:NS\d+/,Nu=/^NS\d+:/,ku=c(Kr),Cu=/^\s*([\w$_]+|\([^)]*?\))\s*=>|^function\s*\(/,Au=/^\s*[A-Za-z_$][\w$]*(?:\.[A-Za-z_$][\w$]*|\['.*?']|\[".*?"]|\[\d+]|\[[A-Za-z_$][\w$]*])*\s*$/,Tu={esc:27,tab:9,enter:13,space:32,up:38,left:37,right:39,down:40,delete:[8,46]},Du=function(t){return"if("+t+")return null;"},Su={stop:"$event.stopPropagation();",prevent:"$event.preventDefault();",self:Du("$event.target !== $event.currentTarget"),ctrl:Du("!$event.ctrlKey"),shift:Du("!$event.shiftKey"),alt:Du("!$event.altKey"),meta:Du("!$event.metaKey"),left:Du("'button' in $event && $event.button !== 0"),middle:Du("'button' in $event && $event.button !== 1"),right:Du("'button' in $event && $event.button !== 2")},ju={bind:oo,cloak:m},Vu=new RegExp("\\b"+"do,if,for,let,new,try,var,case,else,with,await,break,catch,class,const,super,throw,while,yield,delete,export,import,return,switch,default,extends,finally,continue,debugger,function,arguments".split(",").join("\\b|\\b")+"\\b"),Mu=new RegExp("\\b"+"delete,typeof,void".split(",").join("\\s*\\([^\\)]*\\)|\\b")+"\\s*\\([^\\)]*\\)"),Pu=/[A-Za-z_$][\w$]*/,Iu=/'(?:[^'\\]|\\.)*'|"(?:[^"\\]|\\.)*"|`(?:[^`\\]|\\.)*\$\{|\}(?:[^`\\]|\\.)*`|`(?:[^`\\]|\\.)*`/g,Lu={staticKeys:["staticClass"],transformNode:Io,genData:Lo},Ru={staticKeys:["staticStyle"],transformNode:Ro,genData:Uo},Uu=[Lu,Ru],Fu={model:Cn,text:Fo,html:Ho},Hu={expectHTML:!0,modules:Uu,directives:Fu,isPreTag:Ma,isUnaryTag:As,mustUseProp:Oa,isReservedTag:Pa,getTagNamespace:Ve,staticKeys:y(Uu)},Bu=Po(Hu),qu=Bu.compileToFunctions,Ju=c(function(t){var e=Pe(t);return e&&e.innerHTML}),zu=ye.prototype.$mount;ye.prototype.$mount=function(t,n){if((t=t&&Pe(t))===document.body||t===document.documentElement)return"production"!==e.env.NODE_ENV&&wi("Do not mount Vue to <html> or <body> - mount to normal elements instead."),this;var r=this.$options;if(!r.render){var o=r.template;if(o)if("string"==typeof o)"#"===o.charAt(0)&&(o=Ju(o),"production"===e.env.NODE_ENV||o||wi("Template element not found or is empty: "+r.template,this));else{if(!o.nodeType)return"production"!==e.env.NODE_ENV&&wi("invalid template option:"+o,this),this;o=o.innerHTML}else t&&(o=Bo(t));if(o){"production"!==e.env.NODE_ENV&&ri.performance&&yi&&yi.mark("compile");var i=qu(o,{shouldDecodeNewlines:Cs,delimiters:r.delimiters},this),a=i.render,s=i.staticRenderFns;r.render=a,r.staticRenderFns=s,"production"!==e.env.NODE_ENV&&ri.performance&&yi&&(yi.mark("compile end"),yi.measure(this._name+" compile","compile","compile end"))}}return zu.call(this,t,n)},ye.compile=qu,t.exports=ye}).call(e,n(5),n(4))},84:function(t,e){t.exports=function(t,e,n,r){var o,i=t=t||{},a=typeof t.default;"object"!==a&&"function"!==a||(o=t,i=t.default);var s="function"==typeof i?i.options:i;if(e&&(s.render=e.render,s.staticRenderFns=e.staticRenderFns),n&&(s._scopeId=n),r){var u=Object.create(s.computed||null);Object.keys(r).forEach(function(t){var e=r[t];u[t]=function(){return e}}),s.computed=u}return{esModule:o,exports:i,options:s}}},85:function(t,e,n){t.exports={render:function(){var t=this,e=t.$createElement,n=t._self._c||e;return n("div",{attrs:{id:"app"}},[n("ul",{staticClass:"products-list"},t._l(t.products,function(e){return n("li",[t._v("\n     "+t._s(e.product_name)+"\n   ")])}))])},staticRenderFns:[]},t.exports.render._withStripped=!0},86:function(t,e,n){"use strict";function r(t){O&&(t._devtoolHook=O,O.emit("vuex:init",t),O.on("vuex:travel-to-state",function(e){t.replaceState(e)}),t.subscribe(function(t,e){O.emit("vuex:mutation",t,e)}))}function o(t,e){Object.keys(t).forEach(function(n){return e(t[n],n)})}function i(t){return null!==t&&"object"==typeof t}function a(t){return t&&"function"==typeof t.then}function s(t,e){if(!t)throw new Error("[vuex] "+e)}function u(t,e){if(t.update(e),e.modules)for(var n in e.modules){if(!t.getChild(n))return;u(t.getChild(n),e.modules[n])}}function c(t,e){t._actions=Object.create(null),t._mutations=Object.create(null),t._wrappedGetters=Object.create(null),t._modulesNamespaceMap=Object.create(null);var n=t.state;f(t,n,[],t._modules.root,!0),l(t,n,e)}function l(t,e,n){var r=t._vm;t.getters={};var i=t._wrappedGetters,a={};o(i,function(e,n){a[n]=function(){return e(t)},Object.defineProperty(t.getters,n,{get:function(){return t._vm[n]},enumerable:!0})});var s=A.config.silent;A.config.silent=!0,t._vm=new A({data:{$$state:e},computed:a}),A.config.silent=s,t.strict&&y(t),r&&(n&&t._withCommit(function(){r._data.$$state=null}),A.nextTick(function(){return r.$destroy()}))}function f(t,e,n,r,o){var i=!n.length,a=t._modules.getNamespace(n);if(a&&(t._modulesNamespaceMap[a]=r),!i&&!o){var s=g(e,n.slice(0,-1)),u=n[n.length-1];t._withCommit(function(){A.set(s,u,r.state)})}var c=r.context=p(t,a,n);r.forEachMutation(function(e,n){v(t,a+n,e,c)}),r.forEachAction(function(e,n){h(t,a+n,e,c)}),r.forEachGetter(function(e,n){m(t,a+n,e,c)}),r.forEachChild(function(r,i){f(t,e,n.concat(i),r,o)})}function p(t,e,n){var r=""===e,o={dispatch:r?t.dispatch:function(n,r,o){var i=_(n,r,o),a=i.payload,s=i.options,u=i.type;if(s&&s.root||(u=e+u,t._actions[u]))return t.dispatch(u,a)},commit:r?t.commit:function(n,r,o){var i=_(n,r,o),a=i.payload,s=i.options,u=i.type;(s&&s.root||(u=e+u,t._mutations[u]))&&t.commit(u,a,s)}};return Object.defineProperties(o,{getters:{get:r?function(){return t.getters}:function(){return d(t,e)}},state:{get:function(){return g(t.state,n)}}}),o}function d(t,e){var n={},r=e.length;return Object.keys(t.getters).forEach(function(o){if(o.slice(0,r)===e){var i=o.slice(r);Object.defineProperty(n,i,{get:function(){return t.getters[o]},enumerable:!0})}}),n}function v(t,e,n,r){(t._mutations[e]||(t._mutations[e]=[])).push(function(t){n(r.state,t)})}function h(t,e,n,r){(t._actions[e]||(t._actions[e]=[])).push(function(e,o){var i=n({dispatch:r.dispatch,commit:r.commit,getters:r.getters,state:r.state,rootGetters:t.getters,rootState:t.state},e,o);return a(i)||(i=Promise.resolve(i)),t._devtoolHook?i.catch(function(e){throw t._devtoolHook.emit("vuex:error",e),e}):i})}function m(t,e,n,r){t._wrappedGetters[e]||(t._wrappedGetters[e]=function(t){return n(r.state,r.getters,t.state,t.getters)})}function y(t){t._vm.$watch(function(){return this._data.$$state},function(){s(t._committing,"Do not mutate vuex store state outside mutation handlers.")},{deep:!0,sync:!0})}function g(t,e){return e.length?e.reduce(function(t,e){return t[e]},t):t}function _(t,e,n){return i(t)&&t.type&&(n=e,e=t,t=t.type),s("string"==typeof t,"Expects string as the type, but found "+typeof t+"."),{type:t,payload:e,options:n}}function b(t){A||(A=t,x(A))}function w(t){return Array.isArray(t)?t.map(function(t){return{key:t,val:t}}):Object.keys(t).map(function(e){return{key:e,val:t[e]}})}function E(t){return function(e,n){return"string"!=typeof e?(n=e,e=""):"/"!==e.charAt(e.length-1)&&(e+="/"),t(e,n)}}function $(t,e,n){var r=t._modulesNamespaceMap[n];return r}Object.defineProperty(e,"__esModule",{value:!0}),n.d(e,"Store",function(){return T}),n.d(e,"mapState",function(){return S}),n.d(e,"mapMutations",function(){return j}),n.d(e,"mapGetters",function(){return V}),n.d(e,"mapActions",function(){return M});var x=function(t){function e(){var t=this.$options;t.store?this.$store=t.store:t.parent&&t.parent.$store&&(this.$store=t.parent.$store)}if(Number(t.version.split(".")[0])>=2){var n=t.config._lifecycleHooks.indexOf("init")>-1;t.mixin(n?{init:e}:{beforeCreate:e})}else{var r=t.prototype._init;t.prototype._init=function(t){void 0===t&&(t={}),t.init=t.init?[e].concat(t.init):e,r.call(this,t)}}},O="undefined"!=typeof window&&window.__VUE_DEVTOOLS_GLOBAL_HOOK__,N=function(t,e){this.runtime=e,this._children=Object.create(null),this._rawModule=t},k={state:{},namespaced:{}};k.state.get=function(){return this._rawModule.state||{}},k.namespaced.get=function(){return!!this._rawModule.namespaced},N.prototype.addChild=function(t,e){this._children[t]=e},N.prototype.removeChild=function(t){delete this._children[t]},N.prototype.getChild=function(t){return this._children[t]},N.prototype.update=function(t){this._rawModule.namespaced=t.namespaced,t.actions&&(this._rawModule.actions=t.actions),t.mutations&&(this._rawModule.mutations=t.mutations),t.getters&&(this._rawModule.getters=t.getters)},N.prototype.forEachChild=function(t){o(this._children,t)},N.prototype.forEachGetter=function(t){this._rawModule.getters&&o(this._rawModule.getters,t)},N.prototype.forEachAction=function(t){this._rawModule.actions&&o(this._rawModule.actions,t)},N.prototype.forEachMutation=function(t){this._rawModule.mutations&&o(this._rawModule.mutations,t)},Object.defineProperties(N.prototype,k);var C=function(t){var e=this;this.root=new N(t,!1),t.modules&&o(t.modules,function(t,n){e.register([n],t,!1)})};C.prototype.get=function(t){return t.reduce(function(t,e){return t.getChild(e)},this.root)},C.prototype.getNamespace=function(t){var e=this.root;return t.reduce(function(t,n){return e=e.getChild(n),t+(e.namespaced?n+"/":"")},"")},C.prototype.update=function(t){u(this.root,t)},C.prototype.register=function(t,e,n){var r=this;void 0===n&&(n=!0);var i=this.get(t.slice(0,-1)),a=new N(e,n);i.addChild(t[t.length-1],a),e.modules&&o(e.modules,function(e,o){r.register(t.concat(o),e,n)})},C.prototype.unregister=function(t){var e=this.get(t.slice(0,-1)),n=t[t.length-1];e.getChild(n).runtime&&e.removeChild(n)};var A,T=function(t){var e=this;void 0===t&&(t={}),s(A,"must call Vue.use(Vuex) before creating a store instance."),s("undefined"!=typeof Promise,"vuex requires a Promise polyfill in this browser.");var n=t.state;void 0===n&&(n={});var o=t.plugins;void 0===o&&(o=[]);var i=t.strict;void 0===i&&(i=!1),this._committing=!1,this._actions=Object.create(null),this._mutations=Object.create(null),this._wrappedGetters=Object.create(null),this._modules=new C(t),this._modulesNamespaceMap=Object.create(null),this._subscribers=[],this._watcherVM=new A;var a=this,u=this,c=u.dispatch,p=u.commit;this.dispatch=function(t,e){return c.call(a,t,e)},this.commit=function(t,e,n){return p.call(a,t,e,n)},this.strict=i,f(this,n,[],this._modules.root),l(this,n),o.concat(r).forEach(function(t){return t(e)})},D={state:{}};D.state.get=function(){return this._vm._data.$$state},D.state.set=function(t){s(!1,"Use store.replaceState() to explicit replace store state.")},T.prototype.commit=function(t,e,n){var r=this,o=_(t,e,n),i=o.type,a=o.payload,s=o.options,u={type:i,payload:a},c=this._mutations[i];c&&(this._withCommit(function(){c.forEach(function(t){t(a)})}),this._subscribers.forEach(function(t){return t(u,r.state)}),s&&s.silent)},T.prototype.dispatch=function(t,e){var n=_(t,e),r=n.type,o=n.payload,i=this._actions[r];if(i)return i.length>1?Promise.all(i.map(function(t){return t(o)})):i[0](o)},T.prototype.subscribe=function(t){var e=this._subscribers;return e.indexOf(t)<0&&e.push(t),function(){var n=e.indexOf(t);n>-1&&e.splice(n,1)}},T.prototype.watch=function(t,e,n){var r=this;return s("function"==typeof t,"store.watch only accepts a function."),this._watcherVM.$watch(function(){return t(r.state,r.getters)},e,n)},T.prototype.replaceState=function(t){var e=this;this._withCommit(function(){e._vm._data.$$state=t})},T.prototype.registerModule=function(t,e){"string"==typeof t&&(t=[t]),s(Array.isArray(t),"module path must be a string or an Array."),this._modules.register(t,e),f(this,this.state,t,this._modules.get(t)),l(this,this.state)},T.prototype.unregisterModule=function(t){var e=this;"string"==typeof t&&(t=[t]),s(Array.isArray(t),"module path must be a string or an Array."),this._modules.unregister(t),this._withCommit(function(){var n=g(e.state,t.slice(0,-1));A.delete(n,t[t.length-1])}),c(this)},T.prototype.hotUpdate=function(t){this._modules.update(t),c(this,!0)},T.prototype._withCommit=function(t){var e=this._committing;this._committing=!0,t(),this._committing=e},Object.defineProperties(T.prototype,D),"undefined"!=typeof window&&window.Vue&&b(window.Vue);var S=E(function(t,e){var n={};return w(e).forEach(function(e){var r=e.key,o=e.val;n[r]=function(){var e=this.$store.state,n=this.$store.getters;if(t){var r=$(this.$store,"mapState",t);if(!r)return;e=r.context.state,n=r.context.getters}return"function"==typeof o?o.call(this,e,n):e[o]},n[r].vuex=!0}),n}),j=E(function(t,e){var n={};return w(e).forEach(function(e){var r=e.key,o=e.val;o=t+o,n[r]=function(){for(var e=[],n=arguments.length;n--;)e[n]=arguments[n];if(!t||$(this.$store,"mapMutations",t))return this.$store.commit.apply(this.$store,[o].concat(e))}}),n}),V=E(function(t,e){var n={};return w(e).forEach(function(e){var r=e.key,o=e.val;o=t+o,n[r]=function(){if((!t||$(this.$store,"mapGetters",t))&&o in this.$store.getters)return this.$store.getters[o]},n[r].vuex=!0}),n}),M=E(function(t,e){var n={};return w(e).forEach(function(e){var r=e.key,o=e.val;o=t+o,n[r]=function(){for(var e=[],n=arguments.length;n--;)e[n]=arguments[n];if(!t||$(this.$store,"mapActions",t))return this.$store.dispatch.apply(this.$store,[o].concat(e))}}),n}),P={Store:T,install:b,version:"2.2.1",mapState:S,mapMutations:j,mapGetters:V,mapActions:M};e.default=P},88:function(t,e){},89:function(t,e,n){"use strict";function r(t){return t&&t.__esModule?t:{default:t}}var o=n(6),i=r(o),a=n(27),s=r(a),u=n(16),c=r(u),l=n(14),f=r(l);i.default.use(s.default);new i.default({store:f.default,el:"#stock-app",template:"<app/>",components:{app:c.default},methods:{getStock:function(){this.$http.get(data.apiUrl).then(function(t){200==t.status&&this.$store.commit("addProducts",t.body)},function(t){})}},mounted:function(){this.getStock()}})}});
||||||| parent of 0e4097cdff... BO: Split stock-app in components
!function(t){function e(r){if(n[r])return n[r].exports;var o=n[r]={i:r,l:!1,exports:{}};return t[r].call(o.exports,o,o.exports,e),o.l=!0,o.exports}var n={};e.m=t,e.c=n,e.i=function(t){return t},e.d=function(t,n,r){e.o(t,n)||Object.defineProperty(t,n,{configurable:!1,enumerable:!0,get:r})},e.n=function(t){var n=t&&t.__esModule?function(){return t.default}:function(){return t};return e.d(n,"a",n),n},e.o=function(t,e){return Object.prototype.hasOwnProperty.call(t,e)},e.p="",e(e.s=87)}({15:function(t,e,n){"use strict";function r(t){return t&&t.__esModule?t:{default:t}}Object.defineProperty(e,"__esModule",{value:!0});var o=n(6),i=r(o),a=n(85),s=r(a);i.default.use(s.default);var u={products:[]},c={addProducts:function(t,e){t.products=e}},l={},f={};e.default=new s.default.Store({state:u,getters:f,actions:l,mutations:c})},17:function(t,e,n){"use strict";var r=n(83)(n(29),n(84),null,null);r.options.__file="/Users/nMartin/Documents/PrestaShop/admin-dev/themes/new-theme/js/stock-page/components/app.vue",r.esModule&&Object.keys(r.esModule).some(function(t){return"default"!==t&&"__esModule"!==t})&&console.error("named exports are not supported in *.vue files."),r.options.functional&&console.error("[vue-loader] app.vue: functional components are not supported with templates, they should use render functions."),t.exports=r.exports},28:function(t,e,n){"use strict";function r(t){this.state=z,this.value=void 0,this.deferred=[];var e=this;try{t(function(t){e.resolve(t)},function(t){e.reject(t)})}catch(t){e.reject(t)}}function o(t,e){t instanceof Promise?this.promise=t:this.promise=new Promise(t.bind(e)),this.context=e}function i(t){"undefined"!=typeof console&&tt&&console.warn("[VueResource warn]: "+t)}function a(t){"undefined"!=typeof console&&console.error(t)}function s(t,e){return W(t,e)}function u(t){return t?t.replace(/^\s*|\s*$/g,""):""}function c(t){return t?t.toLowerCase():""}function l(t){return t?t.toUpperCase():""}function f(t){return"string"==typeof t}function p(t){return"function"==typeof t}function d(t){return null!==t&&"object"==typeof t}function v(t){return d(t)&&Object.getPrototypeOf(t)==Object.prototype}function h(t){return"undefined"!=typeof Blob&&t instanceof Blob}function m(t){return"undefined"!=typeof FormData&&t instanceof FormData}function y(t,e,n){var r=o.resolve(t);return arguments.length<2?r:r.then(e,n)}function g(t,e,n){return n=n||{},p(n)&&(n=n.call(e)),b(t.bind({$vm:e,$options:n}),t,{$options:n})}function _(t,e){var n,r;if(rt(t))for(n=0;n<t.length;n++)e.call(t[n],t[n],n);else if(d(t))for(r in t)X.call(t,r)&&e.call(t[r],t[r],r);return t}function b(t){return Q.call(arguments,1).forEach(function(e){x(t,e,!0)}),t}function w(t){return Q.call(arguments,1).forEach(function(e){for(var n in e)void 0===t[n]&&(t[n]=e[n])}),t}function E(t){return Q.call(arguments,1).forEach(function(e){x(t,e)}),t}function x(t,e,n){for(var r in e)n&&(v(e[r])||rt(e[r]))?(v(e[r])&&!v(t[r])&&(t[r]={}),rt(e[r])&&!rt(t[r])&&(t[r]=[]),x(t[r],e[r],n)):void 0!==e[r]&&(t[r]=e[r])}function $(t,e,n){var r=O(t),o=r.expand(e);return n&&n.push.apply(n,r.vars),o}function O(t){var e=["+","#",".","/",";","?","&"],n=[];return{vars:n,expand:function(r){return t.replace(/\{([^\{\}]+)\}|([^\{\}]+)/g,function(t,o,i){if(o){var a=null,s=[];if(e.indexOf(o.charAt(0))!==-1&&(a=o.charAt(0),o=o.substr(1)),o.split(/,/g).forEach(function(t){var e=/([^:\*]*)(?::(\d+)|(\*))?/.exec(t);s.push.apply(s,N(r,a,e[1],e[2]||e[3])),n.push(e[1])}),a&&"+"!==a){var u=",";return"?"===a?u="&":"#"!==a&&(u=a),(0!==s.length?a:"")+s.join(u)}return s.join(",")}return T(i)})}}}function N(t,e,n,r){var o=t[n],i=[];if(k(o)&&""!==o)if("string"==typeof o||"number"==typeof o||"boolean"==typeof o)o=o.toString(),r&&"*"!==r&&(o=o.substring(0,parseInt(r,10))),i.push(A(e,o,C(e)?n:null));else if("*"===r)Array.isArray(o)?o.filter(k).forEach(function(t){i.push(A(e,t,C(e)?n:null))}):Object.keys(o).forEach(function(t){k(o[t])&&i.push(A(e,o[t],t))});else{var a=[];Array.isArray(o)?o.filter(k).forEach(function(t){a.push(A(e,t))}):Object.keys(o).forEach(function(t){k(o[t])&&(a.push(encodeURIComponent(t)),a.push(A(e,o[t].toString())))}),C(e)?i.push(encodeURIComponent(n)+"="+a.join(",")):0!==a.length&&i.push(a.join(","))}else";"===e?i.push(encodeURIComponent(n)):""!==o||"&"!==e&&"?"!==e?""===o&&i.push(""):i.push(encodeURIComponent(n)+"=");return i}function k(t){return void 0!==t&&null!==t}function C(t){return";"===t||"&"===t||"?"===t}function A(t,e,n){return e="+"===t||"#"===t?T(e):encodeURIComponent(e),n?encodeURIComponent(n)+"="+e:e}function T(t){return t.split(/(%[0-9A-Fa-f]{2})/g).map(function(t){return/%[0-9A-Fa-f]/.test(t)||(t=encodeURI(t)),t}).join("")}function D(t,e){var n,r=this||{},o=t;return f(t)&&(o={url:t,params:e}),o=b({},D.options,r.$options,o),D.transforms.forEach(function(t){n=S(t,n,r.$vm)}),n(o)}function S(t,e,n){return function(r){return t.call(n,r,e)}}function j(t,e,n){var r,o=rt(e),i=v(e);_(e,function(e,a){r=d(e)||rt(e),n&&(a=n+"["+(i||r?a:"")+"]"),!n&&o?t.add(e.name,e.value):r?j(t,e,a):t.add(a,e)})}function V(t){var e=t.match(/^\[|^\{(?!\{)/),n={"[":/]$/,"{":/}$/};return e&&n[e[0]].test(t)}function M(t,e){e((t.client||(et?gt:_t))(t))}function P(t,e){return Object.keys(t).reduce(function(t,n){return c(e)===c(n)?n:t},null)}function I(t){if(/[^a-z0-9\-#$%&'*+.\^_`|~]/i.test(t))throw new TypeError("Invalid character in header field name");return u(t)}function L(t){return new o(function(e){var n=new FileReader;n.readAsText(t),n.onload=function(){e(n.result)}})}function R(t){return 0===t.type.indexOf("text")||t.type.indexOf("json")!==-1}function U(t){var e=this||{},n=bt(e.$vm);return w(t||{},e.$options,U.options),U.interceptors.forEach(function(t){n.use(t)}),n(new xt(t)).then(function(t){return t.ok?t:o.reject(t)},function(t){return t instanceof Error&&a(t),o.reject(t)})}function F(t,e,n,r){var o=this||{},i={};return n=ot({},F.actions,n),_(n,function(n,a){n=b({url:t,params:ot({},e)},r,n),i[a]=function(){return(o.$http||U)(H(n,arguments))}}),i}function H(t,e){var n,r=ot({},t),o={};switch(e.length){case 2:o=e[0],n=e[1];break;case 1:/^(POST|PUT|PATCH)$/i.test(r.method)?n=e[0]:o=e[0];break;case 0:break;default:throw"Expected up to 2 arguments [params, body], got "+e.length+" arguments"}return r.body=n,r.params=ot({},r.params,o),r}function B(t){B.installed||(nt(t),t.url=D,t.http=U,t.resource=F,t.Promise=o,Object.defineProperties(t.prototype,{$url:{get:function(){return g(t.url,this,this.$options.url)}},$http:{get:function(){return g(t.http,this,this.$options.http)}},$resource:{get:function(){return t.resource.bind(this)}},$promise:{get:function(){var e=this;return function(n){return new t.Promise(n,e)}}}}))}var q=0,J=1,z=2;r.reject=function(t){return new r(function(e,n){n(t)})},r.resolve=function(t){return new r(function(e,n){e(t)})},r.all=function(t){return new r(function(e,n){function o(n){return function(r){a[n]=r,(i+=1)===t.length&&e(a)}}var i=0,a=[];0===t.length&&e(a);for(var s=0;s<t.length;s+=1)r.resolve(t[s]).then(o(s),n)})},r.race=function(t){return new r(function(e,n){for(var o=0;o<t.length;o+=1)r.resolve(t[o]).then(e,n)})};var G=r.prototype;G.resolve=function(t){var e=this;if(e.state===z){if(t===e)throw new TypeError("Promise settled with itself.");var n=!1;try{var r=t&&t.then;if(null!==t&&"object"==typeof t&&"function"==typeof r)return void r.call(t,function(t){n||e.resolve(t),n=!0},function(t){n||e.reject(t),n=!0})}catch(t){return void(n||e.reject(t))}e.state=q,e.value=t,e.notify()}},G.reject=function(t){var e=this;if(e.state===z){if(t===e)throw new TypeError("Promise settled with itself.");e.state=J,e.value=t,e.notify()}},G.notify=function(){var t=this;s(function(){if(t.state!==z)for(;t.deferred.length;){var e=t.deferred.shift(),n=e[0],r=e[1],o=e[2],i=e[3];try{t.state===q?o("function"==typeof n?n.call(void 0,t.value):t.value):t.state===J&&("function"==typeof r?o(r.call(void 0,t.value)):i(t.value))}catch(t){i(t)}}})},G.then=function(t,e){var n=this;return new r(function(r,o){n.deferred.push([t,e,r,o]),n.notify()})},G.catch=function(t){return this.then(void 0,t)},"undefined"==typeof Promise&&(window.Promise=r),o.all=function(t,e){return new o(Promise.all(t),e)},o.resolve=function(t,e){return new o(Promise.resolve(t),e)},o.reject=function(t,e){return new o(Promise.reject(t),e)},o.race=function(t,e){return new o(Promise.race(t),e)};var K=o.prototype;K.bind=function(t){return this.context=t,this},K.then=function(t,e){return t&&t.bind&&this.context&&(t=t.bind(this.context)),e&&e.bind&&this.context&&(e=e.bind(this.context)),new o(this.promise.then(t,e),this.context)},K.catch=function(t){return t&&t.bind&&this.context&&(t=t.bind(this.context)),new o(this.promise.catch(t),this.context)},K.finally=function(t){return this.then(function(e){return t.call(this),e},function(e){return t.call(this),Promise.reject(e)})};var W,Z={},X=Z.hasOwnProperty,Y=[],Q=Y.slice,tt=!1,et="undefined"!=typeof window,nt=function(t){var e=t.config;W=t.nextTick,tt=e.debug||!e.silent},rt=Array.isArray,ot=Object.assign||E,it=function(t,e){var n=e(t);return f(t.root)&&!n.match(/^(https?:)?\//)&&(n=t.root+"/"+n),n},at=function(t,e){var n=Object.keys(D.options.params),r={},o=e(t);return _(t.params,function(t,e){n.indexOf(e)===-1&&(r[e]=t)}),r=D.params(r),r&&(o+=(o.indexOf("?")==-1?"?":"&")+r),o},st=function(t){var e=[],n=$(t.url,t.params,e);return e.forEach(function(e){delete t.params[e]}),n};D.options={url:"",root:null,params:{}},D.transforms=[st,at,it],D.params=function(t){var e=[],n=encodeURIComponent;return e.add=function(t,e){p(e)&&(e=e()),null===e&&(e=""),this.push(n(t)+"="+n(e))},j(e,t),e.join("&").replace(/%20/g,"+")},D.parse=function(t){var e=document.createElement("a");return document.documentMode&&(e.href=t,t=e.href),e.href=t,{href:e.href,protocol:e.protocol?e.protocol.replace(/:$/,""):"",port:e.port,host:e.host,hostname:e.hostname,pathname:"/"===e.pathname.charAt(0)?e.pathname:"/"+e.pathname,search:e.search?e.search.replace(/^\?/,""):"",hash:e.hash?e.hash.replace(/^#/,""):""}};var ut=function(t){return new o(function(e){var n=new XDomainRequest,r=function(r){var o=r.type,i=0;"load"===o?i=200:"error"===o&&(i=500),e(t.respondWith(n.responseText,{status:i}))};t.abort=function(){return n.abort()},n.open(t.method,t.getUrl()),t.timeout&&(n.timeout=t.timeout),n.onload=r,n.onabort=r,n.onerror=r,n.ontimeout=r,n.onprogress=function(){},n.send(t.getBody())})},ct=et&&"withCredentials"in new XMLHttpRequest,lt=function(t,e){if(et){var n=D.parse(location.href),r=D.parse(t.getUrl());r.protocol===n.protocol&&r.host===n.host||(t.crossOrigin=!0,t.emulateHTTP=!1,ct||(t.client=ut))}e()},ft=function(t,e){m(t.body)?t.headers.delete("Content-Type"):(d(t.body)||rt(t.body))&&(t.emulateJSON?(t.body=D.params(t.body),t.headers.set("Content-Type","application/x-www-form-urlencoded")):t.body=JSON.stringify(t.body)),e(function(t){return Object.defineProperty(t,"data",{get:function(){return this.body},set:function(t){this.body=t}}),t.bodyText?y(t.text(),function(e){if(0===(t.headers.get("Content-Type")||"").indexOf("application/json")||V(e))try{t.body=JSON.parse(e)}catch(e){t.body=null}else t.body=e;return t}):t})},pt=function(t){return new o(function(e){var n,r,o=t.jsonp||"callback",i=t.jsonpCallback||"_jsonp"+Math.random().toString(36).substr(2),a=null;n=function(n){var o=n.type,s=0;"load"===o&&null!==a?s=200:"error"===o&&(s=500),s&&window[i]&&(delete window[i],document.body.removeChild(r)),e(t.respondWith(a,{status:s}))},window[i]=function(t){a=JSON.stringify(t)},t.abort=function(){n({type:"abort"})},t.params[o]=i,t.timeout&&setTimeout(t.abort,t.timeout),r=document.createElement("script"),r.src=t.getUrl(),r.type="text/javascript",r.async=!0,r.onload=n,r.onerror=n,document.body.appendChild(r)})},dt=function(t,e){"JSONP"==t.method&&(t.client=pt),e()},vt=function(t,e){p(t.before)&&t.before.call(this,t),e()},ht=function(t,e){t.emulateHTTP&&/^(PUT|PATCH|DELETE)$/i.test(t.method)&&(t.headers.set("X-HTTP-Method-Override",t.method),t.method="POST"),e()},mt=function(t,e){_(ot({},U.headers.common,t.crossOrigin?{}:U.headers.custom,U.headers[c(t.method)]),function(e,n){t.headers.has(n)||t.headers.set(n,e)}),e()},yt="undefined"!=typeof Blob&&"undefined"!=typeof FileReader,gt=function(t){return new o(function(e){var n=new XMLHttpRequest,r=function(r){var o=t.respondWith("response"in n?n.response:n.responseText,{status:1223===n.status?204:n.status,statusText:1223===n.status?"No Content":u(n.statusText)});_(u(n.getAllResponseHeaders()).split("\n"),function(t){o.headers.append(t.slice(0,t.indexOf(":")),t.slice(t.indexOf(":")+1))}),e(o)};t.abort=function(){return n.abort()},t.progress&&("GET"===t.method?n.addEventListener("progress",t.progress):/^(POST|PUT)$/i.test(t.method)&&n.upload.addEventListener("progress",t.progress)),n.open(t.method,t.getUrl(),!0),t.timeout&&(n.timeout=t.timeout),t.credentials===!0&&(n.withCredentials=!0),t.crossOrigin||t.headers.set("X-Requested-With","XMLHttpRequest"),"responseType"in n&&yt&&(n.responseType="blob"),t.headers.forEach(function(t,e){n.setRequestHeader(e,t)}),n.onload=r,n.onabort=r,n.onerror=r,n.ontimeout=r,n.send(t.getBody())})},_t=function(t){var e=n(86);return new o(function(n){var r,o=t.getUrl(),i=t.getBody(),a=t.method,s={};t.headers.forEach(function(t,e){s[e]=t}),e(o,{body:i,method:a,headers:s}).then(r=function(e){var r=t.respondWith(e.body,{status:e.statusCode,statusText:u(e.statusMessage)});_(e.headers,function(t,e){r.headers.set(e,t)}),n(r)},function(t){return r(t.response)})})},bt=function(t){function e(e){return new o(function(o){function s(){n=r.pop(),p(n)?n.call(t,e,u):(i("Invalid interceptor of type "+typeof n+", must be a function"),u())}function u(e){if(p(e))a.unshift(e);else if(d(e))return a.forEach(function(n){e=y(e,function(e){return n.call(t,e)||e})}),void y(e,o);s()}s()},t)}var n,r=[M],a=[];return d(t)||(t=null),e.use=function(t){r.push(t)},e},wt=function(t){var e=this;this.map={},_(t,function(t,n){return e.append(n,t)})};wt.prototype.has=function(t){return null!==P(this.map,t)},wt.prototype.get=function(t){var e=this.map[P(this.map,t)];return e?e.join():null},wt.prototype.getAll=function(t){return this.map[P(this.map,t)]||[]},wt.prototype.set=function(t,e){this.map[I(P(this.map,t)||t)]=[u(e)]},wt.prototype.append=function(t,e){var n=this.map[P(this.map,t)];n?n.push(u(e)):this.set(t,e)},wt.prototype.delete=function(t){delete this.map[P(this.map,t)]},wt.prototype.deleteAll=function(){this.map={}},wt.prototype.forEach=function(t,e){var n=this;_(this.map,function(r,o){_(r,function(r){return t.call(e,r,o,n)})})};var Et=function(t,e){var n=e.url,r=e.headers,o=e.status,i=e.statusText;this.url=n,this.ok=o>=200&&o<300,this.status=o||0,this.statusText=i||"",this.headers=new wt(r),this.body=t,f(t)?this.bodyText=t:h(t)&&(this.bodyBlob=t,R(t)&&(this.bodyText=L(t)))};Et.prototype.blob=function(){return y(this.bodyBlob)},Et.prototype.text=function(){return y(this.bodyText)},Et.prototype.json=function(){return y(this.text(),function(t){return JSON.parse(t)})};var xt=function(t){this.body=null,this.params={},ot(this,t,{method:l(t.method||"GET")}),this.headers instanceof wt||(this.headers=new wt(this.headers))};xt.prototype.getUrl=function(){return D(this)},xt.prototype.getBody=function(){return this.body},xt.prototype.respondWith=function(t,e){return new Et(t,ot(e||{},{url:this.getUrl()}))};var $t={Accept:"application/json, text/plain, */*"},Ot={"Content-Type":"application/json;charset=utf-8"};U.options={},U.headers={put:Ot,post:Ot,patch:Ot,delete:Ot,common:$t,custom:{}},U.interceptors=[vt,ht,ft,dt,mt,lt],["get","delete","head","jsonp"].forEach(function(t){U[t]=function(e,n){return this(ot(n||{},{url:e,method:t}))}}),["post","put","patch"].forEach(function(t){U[t]=function(e,n,r){return this(ot(r||{},{url:e,method:t,body:n}))}}),F.actions={get:{method:"GET"},save:{method:"POST"},query:{method:"GET"},update:{method:"PUT"},remove:{method:"DELETE"},delete:{method:"DELETE"}},"undefined"!=typeof window&&window.Vue&&window.Vue.use(B),t.exports=B},29:function(t,e,n){"use strict";Object.defineProperty(e,"__esModule",{value:!0}),e.default={name:"app",computed:{products:function(){return this.$store.state.products}}}},4:function(t,e){var n;n=function(){return this}();try{n=n||Function("return this")()||(0,eval)("this")}catch(t){"object"==typeof window&&(n=window)}t.exports=n},5:function(t,e){function n(){throw new Error("setTimeout has not been defined")}function r(){throw new Error("clearTimeout has not been defined")}function o(t){if(l===setTimeout)return setTimeout(t,0);if((l===n||!l)&&setTimeout)return l=setTimeout,setTimeout(t,0);try{return l(t,0)}catch(e){try{return l.call(null,t,0)}catch(e){return l.call(this,t,0)}}}function i(t){if(f===clearTimeout)return clearTimeout(t);if((f===r||!f)&&clearTimeout)return f=clearTimeout,clearTimeout(t);try{return f(t)}catch(e){try{return f.call(null,t)}catch(e){return f.call(this,t)}}}function a(){h&&d&&(h=!1,d.length?v=d.concat(v):m=-1,v.length&&s())}function s(){if(!h){var t=o(a);h=!0;for(var e=v.length;e;){for(d=v,v=[];++m<e;)d&&d[m].run();m=-1,e=v.length}d=null,h=!1,i(t)}}function u(t,e){this.fun=t,this.array=e}function c(){}var l,f,p=t.exports={};!function(){try{l="function"==typeof setTimeout?setTimeout:n}catch(t){l=n}try{f="function"==typeof clearTimeout?clearTimeout:r}catch(t){f=r}}();var d,v=[],h=!1,m=-1;p.nextTick=function(t){var e=new Array(arguments.length-1);if(arguments.length>1)for(var n=1;n<arguments.length;n++)e[n-1]=arguments[n];v.push(new u(t,e)),1!==v.length||h||o(s)},u.prototype.run=function(){this.fun.apply(null,this.array)},p.title="browser",p.browser=!0,p.env={},p.argv=[],p.version="",p.versions={},p.on=c,p.addListener=c,p.once=c,p.off=c,p.removeListener=c,p.removeAllListeners=c,p.emit=c,p.binding=function(t){throw new Error("process.binding is not supported")},p.cwd=function(){return"/"},p.chdir=function(t){throw new Error("process.chdir is not supported")},p.umask=function(){return 0}},6:function(t,e,n){"use strict";(function(e,n){function r(t){return null==t?"":"object"==typeof t?JSON.stringify(t,null,2):String(t)}function o(t){var e=parseFloat(t);return isNaN(e)?t:e}function i(t,e){for(var n=Object.create(null),r=t.split(","),o=0;o<r.length;o++)n[r[o]]=!0;return e?function(t){return n[t.toLowerCase()]}:function(t){return n[t]}}function a(t,e){if(t.length){var n=t.indexOf(e);if(n>-1)return t.splice(n,1)}}function s(t,e){return Jo.call(t,e)}function u(t){return"string"==typeof t||"number"==typeof t}function c(t){var e=Object.create(null);return function(n){return e[n]||(e[n]=t(n))}}function l(t,e){function n(n){var r=arguments.length;return r?r>1?t.apply(e,arguments):t.call(e,n):t.call(e)}return n._length=t.length,n}function f(t,e){e=e||0;for(var n=t.length-e,r=new Array(n);n--;)r[n]=t[n+e];return r}function p(t,e){for(var n in e)t[n]=e[n];return t}function d(t){return null!==t&&"object"==typeof t}function v(t){return Xo.call(t)===Yo}function h(t){for(var e={},n=0;n<t.length;n++)t[n]&&p(e,t[n]);return e}function m(){}function y(t){return t.reduce(function(t,e){return t.concat(e.staticKeys||[])},[]).join(",")}function g(t,e){var n=d(t),r=d(e);return n&&r?JSON.stringify(t)===JSON.stringify(e):!n&&!r&&String(t)===String(e)}function _(t,e){for(var n=0;n<t.length;n++)if(g(t[n],e))return n;return-1}function b(t){var e=!1;return function(){e||(e=!0,t())}}function w(t){return/native code/.test(t.toString())}function E(t){var e=(t+"").charCodeAt(0);return 36===e||95===e}function x(t,e,n,r){Object.defineProperty(t,e,{value:n,enumerable:!!r,writable:!0,configurable:!0})}function $(t){if(!gi.test(t)){var e=t.split(".");return function(t){for(var n=0;n<e.length;n++){if(!t)return;t=t[e[n]]}return t}}}function O(t){Ni.target&&ki.push(Ni.target),Ni.target=t}function N(){Ni.target=ki.pop()}function k(t,e){t.__proto__=e}function C(t,e,n){for(var r=0,o=n.length;r<o;r++){var i=n[r];x(t,i,e[i])}}function A(t,e){if(d(t)){var n;return s(t,"__ob__")&&t.__ob__ instanceof Si?n=t.__ob__:Di.shouldConvert&&!fi()&&(Array.isArray(t)||v(t))&&Object.isExtensible(t)&&!t._isVue&&(n=new Si(t)),e&&n&&n.vmCount++,n}}function T(t,n,r,o){var i=new Ni,a=Object.getOwnPropertyDescriptor(t,n);if(!a||a.configurable!==!1){var s=a&&a.get,u=a&&a.set,c=A(r);Object.defineProperty(t,n,{enumerable:!0,configurable:!0,get:function(){var e=s?s.call(t):r;return Ni.target&&(i.depend(),c&&c.dep.depend(),Array.isArray(e)&&j(e)),e},set:function(n){var a=s?s.call(t):r;n===a||n!==n&&a!==a||("production"!==e.env.NODE_ENV&&o&&o(),u?u.call(t,n):r=n,c=A(n),i.notify())}})}}function D(t,n,r){if(Array.isArray(t))return t.length=Math.max(t.length,n),t.splice(n,1,r),r;if(s(t,n))return void(t[n]=r);var o=t.__ob__;return t._isVue||o&&o.vmCount?void("production"!==e.env.NODE_ENV&&_i("Avoid adding reactive properties to a Vue instance or its root $data at runtime - declare it upfront in the data option.")):o?(T(o.value,n,r),o.dep.notify(),r):void(t[n]=r)}function S(t,n){if(Array.isArray(t))return void t.splice(n,1);var r=t.__ob__;if(t._isVue||r&&r.vmCount)return void("production"!==e.env.NODE_ENV&&_i("Avoid deleting properties on a Vue instance or its root $data - just set it to null."));s(t,n)&&(delete t[n],r&&r.dep.notify())}function j(t){for(var e=void 0,n=0,r=t.length;n<r;n++)e=t[n],e&&e.__ob__&&e.__ob__.dep.depend(),Array.isArray(e)&&j(e)}function V(t,e){if(!e)return t;for(var n,r,o,i=Object.keys(e),a=0;a<i.length;a++)n=i[a],r=t[n],o=e[n],s(t,n)?v(r)&&v(o)&&V(r,o):D(t,n,o);return t}function M(t,e){return e?t?t.concat(e):Array.isArray(e)?e:[e]:t}function P(t,e){var n=Object.create(t||null);return e?p(n,e):n}function I(t){for(var e in t.components){var n=e.toLowerCase();(qo(n)||ei.isReservedTag(n))&&_i("Do not use built-in or reserved HTML elements as component id: "+e)}}function L(t){var n=t.props;if(n){var r,o,i,a={};if(Array.isArray(n))for(r=n.length;r--;)o=n[r],"string"==typeof o?(i=Go(o),a[i]={type:null}):"production"!==e.env.NODE_ENV&&_i("props must be strings when using array syntax.");else if(v(n))for(var s in n)o=n[s],i=Go(s),a[i]=v(o)?o:{type:o};t.props=a}}function R(t){var e=t.directives;if(e)for(var n in e){var r=e[n];"function"==typeof r&&(e[n]={bind:r,update:r})}}function U(t,n,r){function o(e){var o=ji[e]||Mi;f[e]=o(t[e],n[e],r,e)}"production"!==e.env.NODE_ENV&&I(n),L(n),R(n);var i=n.extends;if(i&&(t="function"==typeof i?U(t,i.options,r):U(t,i,r)),n.mixins)for(var a=0,u=n.mixins.length;a<u;a++){var c=n.mixins[a];c.prototype instanceof he&&(c=c.options),t=U(t,c,r)}var l,f={};for(l in t)o(l);for(l in n)s(t,l)||o(l);return f}function F(t,n,r,o){if("string"==typeof r){var i=t[n];if(s(i,r))return i[r];var a=Go(r);if(s(i,a))return i[a];var u=Ko(a);if(s(i,u))return i[u];var c=i[r]||i[a]||i[u];return"production"!==e.env.NODE_ENV&&o&&!c&&_i("Failed to resolve "+n.slice(0,-1)+": "+r,t),c}}function H(t,n,r,o){var i=n[t],a=!s(r,t),u=r[t];if(G(Boolean,i.type)&&(a&&!s(i,"default")?u=!1:G(String,i.type)||""!==u&&u!==Zo(t)||(u=!0)),void 0===u){u=B(o,i,t);var c=Di.shouldConvert;Di.shouldConvert=!0,A(u),Di.shouldConvert=c}return"production"!==e.env.NODE_ENV&&q(i,t,u,o,a),u}function B(t,n,r){if(s(n,"default")){var o=n.default;return"production"!==e.env.NODE_ENV&&d(o)&&_i('Invalid default value for prop "'+r+'": Props with type Object/Array must use a factory function to return the default value.',t),t&&t.$options.propsData&&void 0===t.$options.propsData[r]&&void 0!==t._props[r]?t._props[r]:"function"==typeof o&&"Function"!==z(n.type)?o.call(t):o}}function q(t,e,n,r,o){if(t.required&&o)return void _i('Missing required prop: "'+e+'"',r);if(null!=n||t.required){var i=t.type,a=!i||i===!0,s=[];if(i){Array.isArray(i)||(i=[i]);for(var u=0;u<i.length&&!a;u++){var c=J(n,i[u]);s.push(c.expectedType||""),a=c.valid}}if(!a)return void _i('Invalid prop: type check failed for prop "'+e+'". Expected '+s.map(Ko).join(", ")+", got "+Object.prototype.toString.call(n).slice(8,-1)+".",r);var l=t.validator;l&&(l(n)||_i('Invalid prop: custom validator check failed for prop "'+e+'".',r))}}function J(t,e){var n,r=z(e);return n="String"===r?typeof t==(r="string"):"Number"===r?typeof t==(r="number"):"Boolean"===r?typeof t==(r="boolean"):"Function"===r?typeof t==(r="function"):"Object"===r?v(t):"Array"===r?Array.isArray(t):t instanceof e,{valid:n,expectedType:r}}function z(t){var e=t&&t.toString().match(/^\s*function (\w+)/);return e&&e[1]}function G(t,e){if(!Array.isArray(e))return z(e)===z(t);for(var n=0,r=e.length;n<r;n++)if(z(e[n])===z(t))return!0;return!1}function K(t,n,r){if(ei.errorHandler)ei.errorHandler.call(null,t,n,r);else{if("production"!==e.env.NODE_ENV&&_i("Error in "+r+":",n),!ri||"undefined"==typeof console)throw t;console.error(t)}}function W(t){return new Hi(void 0,void 0,void 0,String(t))}function Z(t){var e=new Hi(t.tag,t.data,t.children,t.text,t.elm,t.context,t.componentOptions);return e.ns=t.ns,e.isStatic=t.isStatic,e.key=t.key,e.isCloned=!0,e}function X(t){for(var e=new Array(t.length),n=0;n<t.length;n++)e[n]=Z(t[n]);return e}function Y(t){function e(){var t=arguments,n=e.fns;if(!Array.isArray(n))return n.apply(null,arguments);for(var r=0;r<n.length;r++)n[r].apply(null,t)}return e.fns=t,e}function Q(t,n,r,o,i){var a,s,u,c;for(a in t)s=t[a],u=n[a],c=zi(a),s?u?s!==u&&(u.fns=s,t[a]=u):(s.fns||(s=t[a]=Y(s)),r(c.name,s,c.once,c.capture)):"production"!==e.env.NODE_ENV&&_i('Invalid handler for event "'+c.name+'": got '+String(s),i);for(a in n)t[a]||(c=zi(a),o(c.name,n[a],c.capture))}function tt(t,e,n){function r(){n.apply(this,arguments),a(o.fns,r)}var o,i=t[e];i?i.fns&&i.merged?(o=i,o.fns.push(r)):o=Y([i,r]):o=Y([r]),o.merged=!0,t[e]=o}function et(t){for(var e=0;e<t.length;e++)if(Array.isArray(t[e]))return Array.prototype.concat.apply([],t);return t}function nt(t){return u(t)?[W(t)]:Array.isArray(t)?rt(t):void 0}function rt(t,e){var n,r,o,i=[];for(n=0;n<t.length;n++)null!=(r=t[n])&&"boolean"!=typeof r&&(o=i[i.length-1],Array.isArray(r)?i.push.apply(i,rt(r,(e||"")+"_"+n)):u(r)?o&&o.text?o.text+=String(r):""!==r&&i.push(W(r)):r.text&&o&&o.text?i[i.length-1]=W(o.text+r.text):(r.tag&&null==r.key&&null!=e&&(r.key="__vlist"+e+"_"+n+"__"),i.push(r)));return i}function ot(t){return t&&t.filter(function(t){return t&&t.componentOptions})[0]}function it(t){t._events=Object.create(null),t._hasHookEvent=!1;var e=t.$options._parentListeners;e&&ut(t,e)}function at(t,e,n){n?qi.$once(t,e):qi.$on(t,e)}function st(t,e){qi.$off(t,e)}function ut(t,e,n){qi=t,Q(e,n||{},at,st,t)}function ct(t){var e=/^hook:/;t.prototype.$on=function(t,n){var r=this,o=this;if(Array.isArray(t))for(var i=0,a=t.length;i<a;i++)r.$on(t[i],n);else(o._events[t]||(o._events[t]=[])).push(n),e.test(t)&&(o._hasHookEvent=!0);return o},t.prototype.$once=function(t,e){function n(){r.$off(t,n),e.apply(r,arguments)}var r=this;return n.fn=e,r.$on(t,n),r},t.prototype.$off=function(t,e){var n=this;if(!arguments.length)return n._events=Object.create(null),n;var r=n._events[t];if(!r)return n;if(1===arguments.length)return n._events[t]=null,n;for(var o,i=r.length;i--;)if((o=r[i])===e||o.fn===e){r.splice(i,1);break}return n},t.prototype.$emit=function(t){var e=this,n=e._events[t];if(n){n=n.length>1?f(n):n;for(var r=f(arguments,1),o=0,i=n.length;o<i;o++)n[o].apply(e,r)}return e}}function lt(t,e){var n={};if(!t)return n;for(var r,o,i=[],a=0,s=t.length;a<s;a++)if(o=t[a],(o.context===e||o.functionalContext===e)&&o.data&&(r=o.data.slot)){var u=n[r]||(n[r]=[]);"template"===o.tag?u.push.apply(u,o.children):u.push(o)}else i.push(o);return i.length&&(1!==i.length||" "!==i[0].text&&!i[0].isComment)&&(n.default=i),n}function ft(t){for(var e={},n=0;n<t.length;n++)e[t[n][0]]=t[n][1];return e}function pt(t){var e=t.$options,n=e.parent;if(n&&!e.abstract){for(;n.$options.abstract&&n.$parent;)n=n.$parent;n.$children.push(t)}t.$parent=n,t.$root=n?n.$root:t,t.$children=[],t.$refs={},t._watcher=null,t._inactive=null,t._directInactive=!1,t._isMounted=!1,t._isDestroyed=!1,t._isBeingDestroyed=!1}function dt(t){t.prototype._update=function(t,e){var n=this;n._isMounted&&_t(n,"beforeUpdate");var r=n.$el,o=n._vnode,i=Gi;Gi=n,n._vnode=t,n.$el=o?n.__patch__(o,t):n.__patch__(n.$el,t,e,!1,n.$options._parentElm,n.$options._refElm),Gi=i,r&&(r.__vue__=null),n.$el&&(n.$el.__vue__=n),n.$vnode&&n.$parent&&n.$vnode===n.$parent._vnode&&(n.$parent.$el=n.$el)},t.prototype.$forceUpdate=function(){var t=this;t._watcher&&t._watcher.update()},t.prototype.$destroy=function(){var t=this;if(!t._isBeingDestroyed){_t(t,"beforeDestroy"),t._isBeingDestroyed=!0;var e=t.$parent;!e||e._isBeingDestroyed||t.$options.abstract||a(e.$children,t),t._watcher&&t._watcher.teardown();for(var n=t._watchers.length;n--;)t._watchers[n].teardown();t._data.__ob__&&t._data.__ob__.vmCount--,t._isDestroyed=!0,_t(t,"destroyed"),t.$off(),t.$el&&(t.$el.__vue__=null),t.__patch__(t._vnode,null)}}}function vt(t,n,r){t.$el=n,t.$options.render||(t.$options.render=Ji,"production"!==e.env.NODE_ENV&&(t.$options.template&&"#"!==t.$options.template.charAt(0)?_i("You are using the runtime-only build of Vue where the template option is not available. Either pre-compile the templates into render functions, or use the compiler-included build.",t):_i("Failed to mount component: template or render function not defined.",t))),_t(t,"beforeMount");var o;return o="production"!==e.env.NODE_ENV&&ei.performance&&hi?function(){var e=t._name,n="start "+e,o="end "+e;hi.mark(n);var i=t._render();hi.mark(o),hi.measure(e+" render",n,o),hi.mark(n),t._update(i,r),hi.mark(o),hi.measure(e+" patch",n,o)}:function(){t._update(t._render(),r)},t._watcher=new ea(t,o,m),r=!1,null==t.$vnode&&(t._isMounted=!0,_t(t,"mounted")),t}function ht(t,n,r,o,i){var a=!!(i||t.$options._renderChildren||o.data.scopedSlots||t.$scopedSlots!==yi);if(t.$options._parentVnode=o,t.$vnode=o,t._vnode&&(t._vnode.parent=o),t.$options._renderChildren=i,n&&t.$options.props){Di.shouldConvert=!1,"production"!==e.env.NODE_ENV&&(Di.isSettingProps=!0);for(var s=t._props,u=t.$options._propKeys||[],c=0;c<u.length;c++){var l=u[c];s[l]=H(l,t.$options.props,n,t)}Di.shouldConvert=!0,"production"!==e.env.NODE_ENV&&(Di.isSettingProps=!1),t.$options.propsData=n}if(r){var f=t.$options._parentListeners;t.$options._parentListeners=r,ut(t,r,f)}a&&(t.$slots=lt(i,o.context),t.$forceUpdate())}function mt(t){for(;t&&(t=t.$parent);)if(t._inactive)return!0;return!1}function yt(t,e){if(e){if(t._directInactive=!1,mt(t))return}else if(t._directInactive)return;if(t._inactive||null==t._inactive){t._inactive=!1;for(var n=0;n<t.$children.length;n++)yt(t.$children[n]);_t(t,"activated")}}function gt(t,e){if(!(e&&(t._directInactive=!0,mt(t))||t._inactive)){t._inactive=!0;for(var n=0;n<t.$children.length;n++)gt(t.$children[n]);_t(t,"deactivated")}}function _t(t,e){var n=t.$options[e];if(n)for(var r=0,o=n.length;r<o;r++)try{n[r].call(t)}catch(n){K(n,t,e+" hook")}t._hasHookEvent&&t.$emit("hook:"+e)}function bt(){Ki.length=0,Wi={},"production"!==e.env.NODE_ENV&&(Zi={}),Xi=Yi=!1}function wt(){Yi=!0;var t,n,r;for(Ki.sort(function(t,e){return t.id-e.id}),Qi=0;Qi<Ki.length;Qi++)if(t=Ki[Qi],n=t.id,Wi[n]=null,t.run(),"production"!==e.env.NODE_ENV&&null!=Wi[n]&&(Zi[n]=(Zi[n]||0)+1,Zi[n]>ei._maxUpdateCount)){_i("You may have an infinite update loop "+(t.user?'in watcher with expression "'+t.expression+'"':"in a component render function."),t.vm);break}for(Qi=Ki.length;Qi--;)t=Ki[Qi],r=t.vm,r._watcher===t&&r._isMounted&&_t(r,"updated");pi&&ei.devtools&&pi.emit("flush"),bt()}function Et(t){var e=t.id;if(null==Wi[e]){if(Wi[e]=!0,Yi){for(var n=Ki.length-1;n>=0&&Ki[n].id>t.id;)n--;Ki.splice(Math.max(n,Qi)+1,0,t)}else Ki.push(t);Xi||(Xi=!0,vi(wt))}}function xt(t){na.clear(),$t(t,na)}function $t(t,e){var n,r,o=Array.isArray(t);if((o||d(t))&&Object.isExtensible(t)){if(t.__ob__){var i=t.__ob__.dep.id;if(e.has(i))return;e.add(i)}if(o)for(n=t.length;n--;)$t(t[n],e);else for(r=Object.keys(t),n=r.length;n--;)$t(t[r[n]],e)}}function Ot(t,e,n){ra.get=function(){return this[e][n]},ra.set=function(t){this[e][n]=t},Object.defineProperty(t,n,ra)}function Nt(t){t._watchers=[];var e=t.$options;e.props&&kt(t,e.props),e.methods&&St(t,e.methods),e.data?Ct(t):A(t._data={},!0),e.computed&&At(t,e.computed),e.watch&&jt(t,e.watch)}function kt(t,n){var r=t.$options.propsData||{},o=t._props={},i=t.$options._propKeys=[],a=!t.$parent;Di.shouldConvert=a;var s=function(a){i.push(a);var s=H(a,n,r,t);"production"!==e.env.NODE_ENV?(oa[a]&&_i('"'+a+'" is a reserved attribute and cannot be used as component prop.',t),T(o,a,s,function(){t.$parent&&!Di.isSettingProps&&_i("Avoid mutating a prop directly since the value will be overwritten whenever the parent component re-renders. Instead, use a data or computed property based on the prop's value. Prop being mutated: \""+a+'"',t)})):T(o,a,s),a in t||Ot(t,"_props",a)};for(var u in n)s(u);Di.shouldConvert=!0}function Ct(t){var n=t.$options.data;n=t._data="function"==typeof n?n.call(t):n||{},v(n)||(n={},"production"!==e.env.NODE_ENV&&_i("data functions should return an object:\nhttps://vuejs.org/v2/guide/components.html#data-Must-Be-a-Function",t));for(var r=Object.keys(n),o=t.$options.props,i=r.length;i--;)o&&s(o,r[i])?"production"!==e.env.NODE_ENV&&_i('The data property "'+r[i]+'" is already declared as a prop. Use prop default value instead.',t):E(r[i])||Ot(t,"_data",r[i]);A(n,!0)}function At(t,e){var n=t._computedWatchers=Object.create(null);for(var r in e){var o=e[r],i="function"==typeof o?o:o.get;n[r]=new ea(t,i,m,ia),r in t||Tt(t,r,o)}}function Tt(t,e,n){"function"==typeof n?(ra.get=Dt(e),ra.set=m):(ra.get=n.get?n.cache!==!1?Dt(e):n.get:m,ra.set=n.set?n.set:m),Object.defineProperty(t,e,ra)}function Dt(t){return function(){var e=this._computedWatchers&&this._computedWatchers[t];if(e)return e.dirty&&e.evaluate(),Ni.target&&e.depend(),e.value}}function St(t,n){var r=t.$options.props;for(var o in n)t[o]=null==n[o]?m:l(n[o],t),"production"!==e.env.NODE_ENV&&(null==n[o]&&_i('method "'+o+'" has an undefined value in the component definition. Did you reference the function correctly?',t),r&&s(r,o)&&_i('method "'+o+'" has already been defined as a prop.',t))}function jt(t,e){for(var n in e){var r=e[n];if(Array.isArray(r))for(var o=0;o<r.length;o++)Vt(t,n,r[o]);else Vt(t,n,r)}}function Vt(t,e,n){var r;v(n)&&(r=n,n=n.handler),"string"==typeof n&&(n=t[n]),t.$watch(e,n,r)}function Mt(t){var n={};n.get=function(){return this._data};var r={};r.get=function(){return this._props},"production"!==e.env.NODE_ENV&&(n.set=function(t){_i("Avoid replacing instance root $data. Use nested data properties instead.",this)},r.set=function(){_i("$props is readonly.",this)}),Object.defineProperty(t.prototype,"$data",n),Object.defineProperty(t.prototype,"$props",r),t.prototype.$set=D,t.prototype.$delete=S,t.prototype.$watch=function(t,e,n){var r=this;n=n||{},n.user=!0;var o=new ea(r,t,e,n);return n.immediate&&e.call(r,o.value),function(){o.teardown()}}}function Pt(t,n,r,o,i){if(t){var a=r.$options._base;if(d(t)&&(t=a.extend(t)),"function"!=typeof t)return void("production"!==e.env.NODE_ENV&&_i("Invalid Component definition: "+String(t),r));if(!t.cid)if(t.resolved)t=t.resolved;else if(!(t=Bt(t,a,function(){r.$forceUpdate()})))return;pe(t),n=n||{},n.model&&Kt(t.options,n);var s=qt(n,t);if(t.options.functional)return It(t,s,n,r,o);var u=n.on;n.on=n.nativeOn,t.options.abstract&&(n={}),zt(n);var c=t.options.name||i;return new Hi("vue-component-"+t.cid+(c?"-"+c:""),n,void 0,void 0,void 0,r,{Ctor:t,propsData:s,listeners:u,tag:i,children:o})}}function It(t,e,n,r,o){var i={},a=t.options.props;if(a)for(var s in a)i[s]=H(s,a,e);var u=Object.create(r),c=function(t,e,n,r){return Wt(u,t,e,n,r,!0)},l=t.options.render.call(null,c,{props:i,data:n,parent:r,children:o,slots:function(){return lt(o,r)}});return l instanceof Hi&&(l.functionalContext=r,n.slot&&((l.data||(l.data={})).slot=n.slot)),l}function Lt(t,e,n,r){var o=t.componentOptions,i={_isComponent:!0,parent:e,propsData:o.propsData,_componentTag:o.tag,_parentVnode:t,_parentListeners:o.listeners,_renderChildren:o.children,_parentElm:n||null,_refElm:r||null},a=t.data.inlineTemplate;return a&&(i.render=a.render,i.staticRenderFns=a.staticRenderFns),new o.Ctor(i)}function Rt(t,e,n,r){if(!t.componentInstance||t.componentInstance._isDestroyed){(t.componentInstance=Lt(t,Gi,n,r)).$mount(e?t.elm:void 0,e)}else if(t.data.keepAlive){var o=t;Ut(o,o)}}function Ut(t,e){var n=e.componentOptions;ht(e.componentInstance=t.componentInstance,n.propsData,n.listeners,e,n.children)}function Ft(t){t.componentInstance._isMounted||(t.componentInstance._isMounted=!0,_t(t.componentInstance,"mounted")),t.data.keepAlive&&yt(t.componentInstance,!0)}function Ht(t){t.componentInstance._isDestroyed||(t.data.keepAlive?gt(t.componentInstance,!0):t.componentInstance.$destroy())}function Bt(t,n,r){if(!t.requested){t.requested=!0;var o=t.pendingCallbacks=[r],i=!0,a=function(e){if(d(e)&&(e=n.extend(e)),t.resolved=e,!i)for(var r=0,a=o.length;r<a;r++)o[r](e)},s=function(n){"production"!==e.env.NODE_ENV&&_i("Failed to resolve async component: "+String(t)+(n?"\nReason: "+n:""))},u=t(a,s);return u&&"function"==typeof u.then&&!t.resolved&&u.then(a,s),i=!1,t.resolved}t.pendingCallbacks.push(r)}function qt(t,e){var n=e.options.props;if(n){var r={},o=t.attrs,i=t.props,a=t.domProps;if(o||i||a)for(var s in n){var u=Zo(s);Jt(r,i,s,u,!0)||Jt(r,o,s,u)||Jt(r,a,s,u)}return r}}function Jt(t,e,n,r,o){if(e){if(s(e,n))return t[n]=e[n],o||delete e[n],!0;if(s(e,r))return t[n]=e[r],o||delete e[r],!0}return!1}function zt(t){t.hook||(t.hook={});for(var e=0;e<sa.length;e++){var n=sa[e],r=t.hook[n],o=aa[n];t.hook[n]=r?Gt(o,r):o}}function Gt(t,e){return function(n,r,o,i){t(n,r,o,i),e(n,r,o,i)}}function Kt(t,e){var n=t.model&&t.model.prop||"value",r=t.model&&t.model.event||"input";(e.props||(e.props={}))[n]=e.model.value;var o=e.on||(e.on={});o[r]?o[r]=[e.model.callback].concat(o[r]):o[r]=e.model.callback}function Wt(t,e,n,r,o,i){return(Array.isArray(n)||u(n))&&(o=r,r=n,n=void 0),i&&(o=ca),Zt(t,e,n,r,o)}function Zt(t,n,r,o,i){if(r&&r.__ob__)return"production"!==e.env.NODE_ENV&&_i("Avoid using observed data object as vnode data: "+JSON.stringify(r)+"\nAlways create fresh vnode data objects in each render!",t),Ji();if(!n)return Ji();Array.isArray(o)&&"function"==typeof o[0]&&(r=r||{},r.scopedSlots={default:o[0]},o.length=0),i===ca?o=nt(o):i===ua&&(o=et(o));var a,s;if("string"==typeof n){var u;s=ei.getTagNamespace(n),a=ei.isReservedTag(n)?new Hi(ei.parsePlatformTagName(n),r,o,void 0,void 0,t):(u=F(t.$options,"components",n))?Pt(u,r,t,o,n):new Hi(n,r,o,void 0,void 0,t)}else a=Pt(n,r,t,o);return a?(s&&Xt(a,s),a):Ji()}function Xt(t,e){if(t.ns=e,"foreignObject"!==t.tag&&t.children)for(var n=0,r=t.children.length;n<r;n++){var o=t.children[n];o.tag&&!o.ns&&Xt(o,e)}}function Yt(t,e){var n,r,o,i,a;if(Array.isArray(t)||"string"==typeof t)for(n=new Array(t.length),r=0,o=t.length;r<o;r++)n[r]=e(t[r],r);else if("number"==typeof t)for(n=new Array(t),r=0;r<t;r++)n[r]=e(r+1,r);else if(d(t))for(i=Object.keys(t),n=new Array(i.length),r=0,o=i.length;r<o;r++)a=i[r],n[r]=e(t[a],a,r);return n}function Qt(t,n,r,o){var i=this.$scopedSlots[t];if(i)return r=r||{},o&&p(r,o),i(r)||n;var a=this.$slots[t];return a&&"production"!==e.env.NODE_ENV&&(a._rendered&&_i('Duplicate presence of slot "'+t+'" found in the same render tree - this will likely cause render errors.',this),a._rendered=!0),a||n}function te(t){return F(this.$options,"filters",t,!0)||ti}function ee(t,e,n){var r=ei.keyCodes[e]||n;return Array.isArray(r)?r.indexOf(t)===-1:r!==t}function ne(t,n,r,o){if(r)if(d(r)){Array.isArray(r)&&(r=h(r));for(var i in r)if("class"===i||"style"===i)t[i]=r[i];else{var a=t.attrs&&t.attrs.type,s=o||ei.mustUseProp(n,a,i)?t.domProps||(t.domProps={}):t.attrs||(t.attrs={});s[i]=r[i]}}else"production"!==e.env.NODE_ENV&&_i("v-bind without argument expects an Object or Array value",this);return t}function re(t,e){var n=this._staticTrees[t];return n&&!e?Array.isArray(n)?X(n):Z(n):(n=this._staticTrees[t]=this.$options.staticRenderFns[t].call(this._renderProxy),ie(n,"__static__"+t,!1),n)}function oe(t,e,n){return ie(t,"__once__"+e+(n?"_"+n:""),!0),t}function ie(t,e,n){if(Array.isArray(t))for(var r=0;r<t.length;r++)t[r]&&"string"!=typeof t[r]&&ae(t[r],e+"_"+r,n);else ae(t,e,n)}function ae(t,e,n){t.isStatic=!0,t.key=e,t.isOnce=n}function se(t){t.$vnode=null,t._vnode=null,t._staticTrees=null;var e=t.$options._parentVnode,n=e&&e.context;t.$slots=lt(t.$options._renderChildren,n),t.$scopedSlots=yi,t._c=function(e,n,r,o){return Wt(t,e,n,r,o,!1)},t.$createElement=function(e,n,r,o){return Wt(t,e,n,r,o,!0)}}function ue(t){t.prototype.$nextTick=function(t){return vi(t,this)},t.prototype._render=function(){var t=this,n=t.$options,r=n.render,o=n.staticRenderFns,i=n._parentVnode;if(t._isMounted)for(var a in t.$slots)t.$slots[a]=X(t.$slots[a]);t.$scopedSlots=i&&i.data.scopedSlots||yi,o&&!t._staticTrees&&(t._staticTrees=[]),t.$vnode=i;var s;try{s=r.call(t._renderProxy,t.$createElement)}catch(n){K(n,t,"render function"),s="production"!==e.env.NODE_ENV&&t.$options.renderError?t.$options.renderError.call(t._renderProxy,t.$createElement,n):t._vnode}return s instanceof Hi||("production"!==e.env.NODE_ENV&&Array.isArray(s)&&_i("Multiple root nodes returned from render function. Render function should return a single root node.",t),s=Ji()),s.parent=i,s},t.prototype._o=oe,t.prototype._n=o,t.prototype._s=r,t.prototype._l=Yt,t.prototype._t=Qt,t.prototype._q=g,t.prototype._i=_,t.prototype._m=re,t.prototype._f=te,t.prototype._k=ee,t.prototype._b=ne,t.prototype._v=W,t.prototype._e=Ji,t.prototype._u=ft}function ce(t){var e=t.$options.provide,n=t.$options.inject;if(e&&(t._provided="function"==typeof e?e.call(t):e),n)for(var r=Array.isArray(n),o=r?n:di?Reflect.ownKeys(n):Object.keys(n),i=0;i<o.length;i++)for(var a=o[i],s=r?a:n[a],u=t;u;){if(u._provided&&u._provided[s]){t[a]=u._provided[s];break}u=u.$parent}}function le(t){t.prototype._init=function(t){"production"!==e.env.NODE_ENV&&ei.performance&&hi&&hi.mark("init");var n=this;n._uid=la++,n._isVue=!0,t&&t._isComponent?fe(n,t):n.$options=U(pe(n.constructor),t||{},n),"production"!==e.env.NODE_ENV?Vi(n):n._renderProxy=n,n._self=n,pt(n),it(n),se(n),_t(n,"beforeCreate"),Nt(n),ce(n),_t(n,"created"),"production"!==e.env.NODE_ENV&&ei.performance&&hi&&(n._name=mi(n,!1),hi.mark("init end"),hi.measure(n._name+" init","init","init end")),n.$options.el&&n.$mount(n.$options.el)}}function fe(t,e){var n=t.$options=Object.create(t.constructor.options);n.parent=e.parent,n.propsData=e.propsData,n._parentVnode=e._parentVnode,n._parentListeners=e._parentListeners,n._renderChildren=e._renderChildren,n._componentTag=e._componentTag,n._parentElm=e._parentElm,n._refElm=e._refElm,e.render&&(n.render=e.render,n.staticRenderFns=e.staticRenderFns)}function pe(t){var e=t.options;if(t.super){var n=pe(t.super);if(n!==t.superOptions){t.superOptions=n;var r=de(t);r&&p(t.extendOptions,r),e=t.options=U(n,t.extendOptions),e.name&&(e.components[e.name]=t)}}return e}function de(t){var e,n=t.options,r=t.sealedOptions;for(var o in n)n[o]!==r[o]&&(e||(e={}),e[o]=ve(n[o],r[o]));return e}function ve(t,e){if(Array.isArray(t)){var n=[];e=Array.isArray(e)?e:[e];for(var r=0;r<t.length;r++)e.indexOf(t[r])<0&&n.push(t[r]);return n}return t}function he(t){"production"===e.env.NODE_ENV||this instanceof he||_i("Vue is a constructor and should be called with the `new` keyword"),this._init(t)}function me(t){t.use=function(t){if(!t.installed){var e=f(arguments,1);return e.unshift(this),"function"==typeof t.install?t.install.apply(t,e):"function"==typeof t&&t.apply(null,e),t.installed=!0,this}}}function ye(t){t.mixin=function(t){this.options=U(this.options,t)}}function ge(t){t.cid=0;var n=1;t.extend=function(t){t=t||{};var r=this,o=r.cid,i=t._Ctor||(t._Ctor={});if(i[o])return i[o];var a=t.name||r.options.name;"production"!==e.env.NODE_ENV&&(/^[a-zA-Z][\w-]*$/.test(a)||_i('Invalid component name: "'+a+'". Component names can only contain alphanumeric characters and the hyphen, and must start with a letter.'));var s=function(t){this._init(t)};return s.prototype=Object.create(r.prototype),s.prototype.constructor=s,s.cid=n++,s.options=U(r.options,t),s.super=r,s.options.props&&_e(s),s.options.computed&&be(s),s.extend=r.extend,s.mixin=r.mixin,s.use=r.use,ei._assetTypes.forEach(function(t){s[t]=r[t]}),a&&(s.options.components[a]=s),s.superOptions=r.options,s.extendOptions=t,s.sealedOptions=p({},s.options),i[o]=s,s}}function _e(t){var e=t.options.props;for(var n in e)Ot(t.prototype,"_props",n)}function be(t){var e=t.options.computed;for(var n in e)Tt(t.prototype,n,e[n])}function we(t){ei._assetTypes.forEach(function(n){t[n]=function(t,r){return r?("production"!==e.env.NODE_ENV&&"component"===n&&ei.isReservedTag(t)&&_i("Do not use built-in or reserved HTML elements as component id: "+t),"component"===n&&v(r)&&(r.name=r.name||t,r=this.options._base.extend(r)),"directive"===n&&"function"==typeof r&&(r={bind:r,update:r}),this.options[n+"s"][t]=r,r):this.options[n+"s"][t]}})}function Ee(t){return t&&(t.Ctor.options.name||t.tag)}function xe(t,e){return"string"==typeof t?t.split(",").indexOf(e)>-1:t instanceof RegExp&&t.test(e)}function $e(t,e){for(var n in t){var r=t[n];if(r){var o=Ee(r.componentOptions);o&&!e(o)&&(Oe(r),t[n]=null)}}}function Oe(t){t&&(t.componentInstance._inactive||_t(t.componentInstance,"deactivated"),t.componentInstance.$destroy())}function Ne(t){var n={};n.get=function(){return ei},"production"!==e.env.NODE_ENV&&(n.set=function(){_i("Do not replace the Vue.config object, set individual fields instead.")}),Object.defineProperty(t,"config",n),t.util={warn:_i,extend:p,mergeOptions:U,defineReactive:T},t.set=D,t.delete=S,t.nextTick=vi,t.options=Object.create(null),ei._assetTypes.forEach(function(e){t.options[e+"s"]=Object.create(null)}),t.options._base=t,p(t.options.components,da),me(t),ye(t),ge(t),we(t)}function ke(t){for(var e=t.data,n=t,r=t;r.componentInstance;)r=r.componentInstance._vnode,r.data&&(e=Ce(r.data,e));for(;n=n.parent;)n.data&&(e=Ce(e,n.data));return Ae(e)}function Ce(t,e){return{staticClass:Te(t.staticClass,e.staticClass),class:t.class?[t.class,e.class]:e.class}}function Ae(t){var e=t.class,n=t.staticClass;return n||e?Te(n,De(e)):""}function Te(t,e){return t?e?t+" "+e:t:e||""}function De(t){var e="";if(!t)return e;if("string"==typeof t)return t;if(Array.isArray(t)){for(var n,r=0,o=t.length;r<o;r++)t[r]&&(n=De(t[r]))&&(e+=n+" ");return e.slice(0,-1)}if(d(t)){for(var i in t)t[i]&&(e+=i+" ");return e.slice(0,-1)}return e}function Se(t){return ja(t)?"svg":"math"===t?"math":void 0}function je(t){if(!ri)return!0;if(Ma(t))return!1;if(t=t.toLowerCase(),null!=Pa[t])return Pa[t];var e=document.createElement(t);return t.indexOf("-")>-1?Pa[t]=e.constructor===window.HTMLUnknownElement||e.constructor===window.HTMLElement:Pa[t]=/HTMLUnknownElement/.test(e.toString())}function Ve(t){if("string"==typeof t){var n=document.querySelector(t);return n?n:("production"!==e.env.NODE_ENV&&_i("Cannot find element: "+t),document.createElement("div"))}return t}function Me(t,e){var n=document.createElement(t);return"select"!==t?n:(e.data&&e.data.attrs&&void 0!==e.data.attrs.multiple&&n.setAttribute("multiple","multiple"),n)}function Pe(t,e){return document.createElementNS(Da[t],e)}function Ie(t){return document.createTextNode(t)}function Le(t){return document.createComment(t)}function Re(t,e,n){t.insertBefore(e,n)}function Ue(t,e){t.removeChild(e)}function Fe(t,e){t.appendChild(e)}function He(t){return t.parentNode}function Be(t){return t.nextSibling}function qe(t){return t.tagName}function Je(t,e){t.textContent=e}function ze(t,e,n){t.setAttribute(e,n)}function Ge(t,e){var n=t.data.ref;if(n){var r=t.context,o=t.componentInstance||t.elm,i=r.$refs;e?Array.isArray(i[n])?a(i[n],o):i[n]===o&&(i[n]=void 0):t.data.refInFor?Array.isArray(i[n])&&i[n].indexOf(o)<0?i[n].push(o):i[n]=[o]:i[n]=o}}function Ke(t){return null==t}function We(t){return null!=t}function Ze(t,e){return t.key===e.key&&t.tag===e.tag&&t.isComment===e.isComment&&!t.data==!e.data}function Xe(t,e,n){var r,o,i={};for(r=e;r<=n;++r)o=t[r].key,We(o)&&(i[o]=r);return i}function Ye(t){function n(t){return new Hi(A.tagName(t).toLowerCase(),{},[],void 0,t)}function r(t,e){function n(){0==--n.listeners&&o(t)}return n.listeners=e,n}function o(t){var e=A.parentNode(t);e&&A.removeChild(e,t)}function a(t,n,r,o,i){if(t.isRootInsert=!i,!s(t,n,r,o)){var a=t.data,u=t.children,c=t.tag;We(c)?("production"!==e.env.NODE_ENV&&(a&&a.pre&&T++,T||t.ns||ei.ignoredElements.length&&ei.ignoredElements.indexOf(c)>-1||!ei.isUnknownElement(c)||_i("Unknown custom element: <"+c+'> - did you register the component correctly? For recursive components, make sure to provide the "name" option.',t.context)),t.elm=t.ns?A.createElementNS(t.ns,c):A.createElement(c,t),h(t),p(t,u,n),We(a)&&v(t,n),f(r,t.elm,o),"production"!==e.env.NODE_ENV&&a&&a.pre&&T--):t.isComment?(t.elm=A.createComment(t.text),f(r,t.elm,o)):(t.elm=A.createTextNode(t.text),f(r,t.elm,o))}}function s(t,e,n,r){var o=t.data;if(We(o)){var i=We(t.componentInstance)&&o.keepAlive;if(We(o=o.hook)&&We(o=o.init)&&o(t,!1,n,r),We(t.componentInstance))return c(t,e),i&&l(t,e,n,r),!0}}function c(t,e){t.data.pendingInsert&&e.push.apply(e,t.data.pendingInsert),t.elm=t.componentInstance.$el,d(t)?(v(t,e),h(t)):(Ge(t),e.push(t))}function l(t,e,n,r){for(var o,i=t;i.componentInstance;)if(i=i.componentInstance._vnode,We(o=i.data)&&We(o=o.transition)){for(o=0;o<k.activate.length;++o)k.activate[o](Ra,i);e.push(i);break}f(n,t.elm,r)}function f(t,e,n){t&&(n?A.insertBefore(t,e,n):A.appendChild(t,e))}function p(t,e,n){if(Array.isArray(e))for(var r=0;r<e.length;++r)a(e[r],n,t.elm,null,!0);else u(t.text)&&A.appendChild(t.elm,A.createTextNode(t.text))}function d(t){for(;t.componentInstance;)t=t.componentInstance._vnode;return We(t.tag)}function v(t,e){for(var n=0;n<k.create.length;++n)k.create[n](Ra,t);O=t.data.hook,We(O)&&(O.create&&O.create(Ra,t),O.insert&&e.push(t))}function h(t){for(var e,n=t;n;)We(e=n.context)&&We(e=e.$options._scopeId)&&A.setAttribute(t.elm,e,""),n=n.parent;We(e=Gi)&&e!==t.context&&We(e=e.$options._scopeId)&&A.setAttribute(t.elm,e,"")}function m(t,e,n,r,o,i){for(;r<=o;++r)a(n[r],i,t,e)}function y(t){var e,n,r=t.data;if(We(r))for(We(e=r.hook)&&We(e=e.destroy)&&e(t),e=0;e<k.destroy.length;++e)k.destroy[e](t);if(We(e=t.children))for(n=0;n<t.children.length;++n)y(t.children[n])}function g(t,e,n,r){for(;n<=r;++n){var i=e[n];We(i)&&(We(i.tag)?(_(i),y(i)):o(i.elm))}}function _(t,e){if(e||We(t.data)){var n=k.remove.length+1;for(e?e.listeners+=n:e=r(t.elm,n),We(O=t.componentInstance)&&We(O=O._vnode)&&We(O.data)&&_(O,e),O=0;O<k.remove.length;++O)k.remove[O](t,e);We(O=t.data.hook)&&We(O=O.remove)?O(t,e):e()}else o(t.elm)}function b(t,n,r,o,i){for(var s,u,c,l,f=0,p=0,d=n.length-1,v=n[0],h=n[d],y=r.length-1,_=r[0],b=r[y],E=!i;f<=d&&p<=y;)Ke(v)?v=n[++f]:Ke(h)?h=n[--d]:Ze(v,_)?(w(v,_,o),v=n[++f],_=r[++p]):Ze(h,b)?(w(h,b,o),h=n[--d],b=r[--y]):Ze(v,b)?(w(v,b,o),E&&A.insertBefore(t,v.elm,A.nextSibling(h.elm)),v=n[++f],b=r[--y]):Ze(h,_)?(w(h,_,o),E&&A.insertBefore(t,h.elm,v.elm),h=n[--d],_=r[++p]):(Ke(s)&&(s=Xe(n,f,d)),u=We(_.key)?s[_.key]:null,Ke(u)?(a(_,o,t,v.elm),_=r[++p]):(c=n[u],"production"===e.env.NODE_ENV||c||_i("It seems there are duplicate keys that is causing an update error. Make sure each v-for item has a unique key."),Ze(c,_)?(w(c,_,o),n[u]=void 0,E&&A.insertBefore(t,_.elm,v.elm),_=r[++p]):(a(_,o,t,v.elm),_=r[++p])));f>d?(l=Ke(r[y+1])?null:r[y+1].elm,m(t,l,r,p,y,o)):p>y&&g(t,n,f,d)}function w(t,e,n,r){if(t!==e){if(e.isStatic&&t.isStatic&&e.key===t.key&&(e.isCloned||e.isOnce))return e.elm=t.elm,void(e.componentInstance=t.componentInstance);var o,i=e.data,a=We(i);a&&We(o=i.hook)&&We(o=o.prepatch)&&o(t,e);var s=e.elm=t.elm,u=t.children,c=e.children;if(a&&d(e)){for(o=0;o<k.update.length;++o)k.update[o](t,e);We(o=i.hook)&&We(o=o.update)&&o(t,e)}Ke(e.text)?We(u)&&We(c)?u!==c&&b(s,u,c,n,r):We(c)?(We(t.text)&&A.setTextContent(s,""),m(s,null,c,0,c.length-1,n)):We(u)?g(s,u,0,u.length-1):We(t.text)&&A.setTextContent(s,""):t.text!==e.text&&A.setTextContent(s,e.text),a&&We(o=i.hook)&&We(o=o.postpatch)&&o(t,e)}}function E(t,e,n){if(n&&t.parent)t.parent.data.pendingInsert=e;else for(var r=0;r<e.length;++r)e[r].data.hook.insert(e[r])}function x(t,n,r){if("production"!==e.env.NODE_ENV&&!$(t,n))return!1;n.elm=t;var o=n.tag,i=n.data,a=n.children;if(We(i)&&(We(O=i.hook)&&We(O=O.init)&&O(n,!0),We(O=n.componentInstance)))return c(n,r),!0;if(We(o)){if(We(a))if(t.hasChildNodes()){for(var s=!0,u=t.firstChild,l=0;l<a.length;l++){if(!u||!x(u,a[l],r)){s=!1;break}u=u.nextSibling}if(!s||u)return"production"===e.env.NODE_ENV||"undefined"==typeof console||D||(D=!0,console.warn("Parent: ",t),console.warn("Mismatching childNodes vs. VNodes: ",t.childNodes,a)),!1}else p(n,a,r);if(We(i))for(var f in i)if(!S(f)){v(n,r);break}}else t.data!==n.text&&(t.data=n.text);return!0}function $(t,e){return e.tag?0===e.tag.indexOf("vue-component")||e.tag.toLowerCase()===(t.tagName&&t.tagName.toLowerCase()):t.nodeType===(e.isComment?8:3)}var O,N,k={},C=t.modules,A=t.nodeOps;for(O=0;O<Ua.length;++O)for(k[Ua[O]]=[],N=0;N<C.length;++N)void 0!==C[N][Ua[O]]&&k[Ua[O]].push(C[N][Ua[O]]);var T=0,D=!1,S=i("attrs,style,class,staticClass,staticStyle,key");return function(t,r,o,i,s,u){if(!r)return void(t&&y(t));var c=!1,l=[];if(t){var f=We(t.nodeType);if(!f&&Ze(t,r))w(t,r,l,i);else{if(f){if(1===t.nodeType&&t.hasAttribute("server-rendered")&&(t.removeAttribute("server-rendered"),o=!0),o){if(x(t,r,l))return E(r,l,!0),t;"production"!==e.env.NODE_ENV&&_i("The client-side rendered virtual DOM tree is not matching server-rendered content. This is likely caused by incorrect HTML markup, for example nesting block-level elements inside <p>, or missing <tbody>. Bailing hydration and performing full client-side render.")}t=n(t)}var p=t.elm,v=A.parentNode(p);if(a(r,l,p._leaveCb?null:v,A.nextSibling(p)),r.parent){for(var h=r.parent;h;)h.elm=r.elm,h=h.parent;if(d(r))for(var m=0;m<k.create.length;++m)k.create[m](Ra,r.parent)}null!==v?g(v,[t],0,0):We(t.tag)&&y(t)}}else c=!0,a(r,l,s,u);return E(r,l,c),r.elm}}function Qe(t,e){(t.data.directives||e.data.directives)&&tn(t,e)}function tn(t,e){var n,r,o,i=t===Ra,a=e===Ra,s=en(t.data.directives,t.context),u=en(e.data.directives,e.context),c=[],l=[];for(n in u)r=s[n],o=u[n],r?(o.oldValue=r.value,rn(o,"update",e,t),o.def&&o.def.componentUpdated&&l.push(o)):(rn(o,"bind",e,t),o.def&&o.def.inserted&&c.push(o));if(c.length){var f=function(){for(var n=0;n<c.length;n++)rn(c[n],"inserted",e,t)};i?tt(e.data.hook||(e.data.hook={}),"insert",f):f()}if(l.length&&tt(e.data.hook||(e.data.hook={}),"postpatch",function(){for(var n=0;n<l.length;n++)rn(l[n],"componentUpdated",e,t)}),!i)for(n in s)u[n]||rn(s[n],"unbind",t,t,a)}function en(t,e){var n=Object.create(null);if(!t)return n;var r,o;for(r=0;r<t.length;r++)o=t[r],o.modifiers||(o.modifiers=Ha),n[nn(o)]=o,o.def=F(e.$options,"directives",o.name,!0);return n}function nn(t){return t.rawName||t.name+"."+Object.keys(t.modifiers||{}).join(".")}function rn(t,e,n,r,o){var i=t.def&&t.def[e];i&&i(n.elm,t,n,r,o)}function on(t,e){if(t.data.attrs||e.data.attrs){var n,r,o=e.elm,i=t.data.attrs||{},a=e.data.attrs||{};a.__ob__&&(a=e.data.attrs=p({},a));for(n in a)r=a[n],i[n]!==r&&an(o,n,r);ai&&a.value!==i.value&&an(o,"value",a.value);for(n in i)null==a[n]&&(Ca(n)?o.removeAttributeNS(ka,Aa(n)):Oa(n)||o.removeAttribute(n))}}function an(t,e,n){Na(e)?Ta(n)?t.removeAttribute(e):t.setAttribute(e,e):Oa(e)?t.setAttribute(e,Ta(n)||"false"===n?"false":"true"):Ca(e)?Ta(n)?t.removeAttributeNS(ka,Aa(e)):t.setAttributeNS(ka,e,n):Ta(n)?t.removeAttribute(e):t.setAttribute(e,n)}function sn(t,e){var n=e.elm,r=e.data,o=t.data;if(r.staticClass||r.class||o&&(o.staticClass||o.class)){var i=ke(e),a=n._transitionClasses;a&&(i=Te(i,De(a))),i!==n._prevClass&&(n.setAttribute("class",i),n._prevClass=i)}}function un(t){function e(){(a||(a=[])).push(t.slice(v,o).trim()),v=o+1}var n,r,o,i,a,s=!1,u=!1,c=!1,l=!1,f=0,p=0,d=0,v=0;for(o=0;o<t.length;o++)if(r=n,n=t.charCodeAt(o),s)39===n&&92!==r&&(s=!1);else if(u)34===n&&92!==r&&(u=!1);else if(c)96===n&&92!==r&&(c=!1);else if(l)47===n&&92!==r&&(l=!1);else if(124!==n||124===t.charCodeAt(o+1)||124===t.charCodeAt(o-1)||f||p||d){switch(n){case 34:u=!0;break;case 39:s=!0;break;case 96:c=!0;break;case 40:d++;break;case 41:d--;break;case 91:p++;break;case 93:p--;break;case 123:f++;break;case 125:f--}if(47===n){for(var h=o-1,m=void 0;h>=0&&" "===(m=t.charAt(h));h--);m&&za.test(m)||(l=!0)}}else void 0===i?(v=o+1,i=t.slice(0,o).trim()):e();if(void 0===i?i=t.slice(0,o).trim():0!==v&&e(),a)for(o=0;o<a.length;o++)i=cn(i,a[o]);return i}function cn(t,e){var n=e.indexOf("(");return n<0?'_f("'+e+'")('+t+")":'_f("'+e.slice(0,n)+'")('+t+","+e.slice(n+1)}function ln(t){console.error("[Vue compiler]: "+t)}function fn(t,e){return t?t.map(function(t){return t[e]}).filter(function(t){return t}):[]}function pn(t,e,n){(t.props||(t.props=[])).push({name:e,value:n})}function dn(t,e,n){(t.attrs||(t.attrs=[])).push({name:e,value:n})}function vn(t,e,n,r,o,i){(t.directives||(t.directives=[])).push({name:e,rawName:n,value:r,arg:o,modifiers:i})}function hn(t,e,n,r,o){r&&r.capture&&(delete r.capture,e="!"+e),r&&r.once&&(delete r.once,e="~"+e);var i;r&&r.native?(delete r.native,i=t.nativeEvents||(t.nativeEvents={})):i=t.events||(t.events={});var a={value:n,modifiers:r},s=i[e];Array.isArray(s)?o?s.unshift(a):s.push(a):i[e]=s?o?[a,s]:[s,a]:a}function mn(t,e,n){var r=yn(t,":"+e)||yn(t,"v-bind:"+e);if(null!=r)return un(r);if(n!==!1){var o=yn(t,e);if(null!=o)return JSON.stringify(o)}}function yn(t,e){var n;if(null!=(n=t.attrsMap[e]))for(var r=t.attrsList,o=0,i=r.length;o<i;o++)if(r[o].name===e){r.splice(o,1);break}return n}function gn(t,e,n){var r=n||{},o=r.number,i=r.trim,a="$$v",s=a;i&&(s="(typeof "+a+" === 'string'? "+a+".trim(): "+a+")"),o&&(s="_n("+s+")");var u=_n(e,s);t.model={value:"("+e+")",callback:"function ("+a+") {"+u+"}"}}function _n(t,e){var n=bn(t);return null===n.idx?t+"="+e:"var $$exp = "+n.exp+", $$idx = "+n.idx+";if (!Array.isArray($$exp)){"+t+"="+e+"}else{$$exp.splice($$idx, 1, "+e+")}"}function bn(t){if(ha=t,va=ha.length,ya=ga=_a=0,t.indexOf("[")<0||t.lastIndexOf("]")<va-1)return{exp:t,idx:null};for(;!En();)ma=wn(),xn(ma)?On(ma):91===ma&&$n(ma);return{exp:t.substring(0,ga),idx:t.substring(ga+1,_a)}}function wn(){return ha.charCodeAt(++ya)}function En(){return ya>=va}function xn(t){return 34===t||39===t}function $n(t){var e=1;for(ga=ya;!En();)if(t=wn(),xn(t))On(t);else if(91===t&&e++,93===t&&e--,0===e){_a=ya;break}}function On(t){for(var e=t;!En()&&(t=wn())!==e;);}function Nn(t,n,r){ba=r;var o=n.value,i=n.modifiers,a=t.tag,s=t.attrsMap.type;if("production"!==e.env.NODE_ENV){var u=t.attrsMap["v-bind:type"]||t.attrsMap[":type"];"input"===a&&u&&ba('<input :type="'+u+'" v-model="'+o+'">:\nv-model does not support dynamic input types. Use v-if branches instead.'),"input"===a&&"file"===s&&ba("<"+t.tag+' v-model="'+o+'" type="file">:\nFile inputs are read only. Use a v-on:change listener instead.')}if("select"===a)An(t,o,i);else if("input"===a&&"checkbox"===s)kn(t,o,i);else if("input"===a&&"radio"===s)Cn(t,o,i);else if("input"===a||"textarea"===a)Dn(t,o,i);else{if(!ei.isReservedTag(a))return gn(t,o,i),!1;"production"!==e.env.NODE_ENV&&ba("<"+t.tag+' v-model="'+o+"\">: v-model is not supported on this element type. If you are working with contenteditable, it's recommended to wrap a library dedicated for that purpose inside a custom component.")}return!0}function kn(t,n,r){"production"!==e.env.NODE_ENV&&null!=t.attrsMap.checked&&ba("<"+t.tag+' v-model="'+n+"\" checked>:\ninline checked attributes will be ignored when using v-model. Declare initial values in the component's data option instead.");var o=r&&r.number,i=mn(t,"value")||"null",a=mn(t,"true-value")||"true",s=mn(t,"false-value")||"false";pn(t,"checked","Array.isArray("+n+")?_i("+n+","+i+")>-1"+("true"===a?":("+n+")":":_q("+n+","+a+")")),hn(t,Ka,"var $$a="+n+",$$el=$event.target,$$c=$$el.checked?("+a+"):("+s+");if(Array.isArray($$a)){var $$v="+(o?"_n("+i+")":i)+",$$i=_i($$a,$$v);if($$c){$$i<0&&("+n+"=$$a.concat($$v))}else{$$i>-1&&("+n+"=$$a.slice(0,$$i).concat($$a.slice($$i+1)))}}else{"+n+"=$$c}",null,!0)}function Cn(t,n,r){"production"!==e.env.NODE_ENV&&null!=t.attrsMap.checked&&ba("<"+t.tag+' v-model="'+n+"\" checked>:\ninline checked attributes will be ignored when using v-model. Declare initial values in the component's data option instead.");var o=r&&r.number,i=mn(t,"value")||"null";i=o?"_n("+i+")":i,pn(t,"checked","_q("+n+","+i+")"),hn(t,Ka,_n(n,i),null,!0)}function An(t,n,r){"production"!==e.env.NODE_ENV&&t.children.some(Tn);var o=r&&r.number,i='Array.prototype.filter.call($event.target.options,function(o){return o.selected}).map(function(o){var val = "_value" in o ? o._value : o.value;return '+(o?"_n(val)":"val")+"})",a="var $$selectedVal = "+i+";";a=a+" "+_n(n,"$event.target.multiple ? $$selectedVal : $$selectedVal[0]"),hn(t,"change",a,null,!0)}function Tn(t){return 1===t.type&&"option"===t.tag&&null!=t.attrsMap.selected&&(ba('<select v-model="'+t.parent.attrsMap["v-model"]+"\">:\ninline selected attributes on <option> will be ignored when using v-model. Declare initial values in the component's data option instead."),!0)}function Dn(t,e,n){var r=t.attrsMap.type,o=n||{},i=o.lazy,a=o.number,s=o.trim,u=!i&&"range"!==r,c=i?"change":"range"===r?Ga:"input",l="$event.target.value";s&&(l="$event.target.value.trim()"),a&&(l="_n("+l+")");var f=_n(e,l);u&&(f="if($event.target.composing)return;"+f),pn(t,"value","("+e+")"),hn(t,c,f,null,!0),(s||a||"number"===r)&&hn(t,"blur","$forceUpdate()")}function Sn(t){var e;t[Ga]&&(e=ii?"change":"input",t[e]=[].concat(t[Ga],t[e]||[]),delete t[Ga]),t[Ka]&&(e=li?"click":"change",t[e]=[].concat(t[Ka],t[e]||[]),delete t[Ka])}function jn(t,e,n,r){if(n){var o=e,i=wa;e=function(n){null!==(1===arguments.length?o(n):o.apply(null,arguments))&&Vn(t,e,r,i)}}wa.addEventListener(t,e,r)}function Vn(t,e,n,r){(r||wa).removeEventListener(t,e,n)}function Mn(t,e){if(t.data.on||e.data.on){var n=e.data.on||{},r=t.data.on||{};wa=e.elm,Sn(n),Q(n,r,jn,Vn,e.context)}}function Pn(t,e){if(t.data.domProps||e.data.domProps){var n,r,o=e.elm,i=t.data.domProps||{},a=e.data.domProps||{};a.__ob__&&(a=e.data.domProps=p({},a));for(n in i)null==a[n]&&(o[n]="");for(n in a)if(r=a[n],"textContent"!==n&&"innerHTML"!==n||(e.children&&(e.children.length=0),r!==i[n]))if("value"===n){o._value=r;var s=null==r?"":String(r);In(o,e,s)&&(o.value=s)}else o[n]=r}}function In(t,e,n){return!t.composing&&("option"===e.tag||Ln(t,n)||Rn(t,n))}function Ln(t,e){return document.activeElement!==t&&t.value!==e}function Rn(t,e){var n=t.value,r=t._vModifiers;return r&&r.number||"number"===t.type?o(n)!==o(e):r&&r.trim?n.trim()!==e.trim():n!==e}function Un(t){var e=Fn(t.style);return t.staticStyle?p(t.staticStyle,e):e}function Fn(t){return Array.isArray(t)?h(t):"string"==typeof t?Xa(t):t}function Hn(t,e){var n,r={};if(e)for(var o=t;o.componentInstance;)o=o.componentInstance._vnode,o.data&&(n=Un(o.data))&&p(r,n);(n=Un(t.data))&&p(r,n);for(var i=t;i=i.parent;)i.data&&(n=Un(i.data))&&p(r,n);return r}function Bn(t,e){var n=e.data,r=t.data;if(n.staticStyle||n.style||r.staticStyle||r.style){var o,i,a=e.elm,s=t.data.staticStyle,u=t.data.style||{},c=s||u,l=Fn(e.data.style)||{};e.data.style=l.__ob__?p({},l):l;var f=Hn(e,!0);for(i in c)null==f[i]&&ts(a,i,"");for(i in f)(o=f[i])!==c[i]&&ts(a,i,null==o?"":o)}}function qn(t,e){if(e&&(e=e.trim()))if(t.classList)e.indexOf(" ")>-1?e.split(/\s+/).forEach(function(e){return t.classList.add(e)}):t.classList.add(e);else{var n=" "+(t.getAttribute("class")||"")+" ";n.indexOf(" "+e+" ")<0&&t.setAttribute("class",(n+e).trim())}}function Jn(t,e){if(e&&(e=e.trim()))if(t.classList)e.indexOf(" ")>-1?e.split(/\s+/).forEach(function(e){return t.classList.remove(e)}):t.classList.remove(e);else{for(var n=" "+(t.getAttribute("class")||"")+" ",r=" "+e+" ";n.indexOf(r)>=0;)n=n.replace(r," ");t.setAttribute("class",n.trim())}}function zn(t){if(t){if("object"==typeof t){var e={};return t.css!==!1&&p(e,os(t.name||"v")),p(e,t),e}return"string"==typeof t?os(t):void 0}}function Gn(t){ps(function(){ps(t)})}function Kn(t,e){(t._transitionClasses||(t._transitionClasses=[])).push(e),qn(t,e)}function Wn(t,e){t._transitionClasses&&a(t._transitionClasses,e),Jn(t,e)}function Zn(t,e,n){var r=Xn(t,e),o=r.type,i=r.timeout,a=r.propCount;if(!o)return n();var s=o===as?cs:fs,u=0,c=function(){t.removeEventListener(s,l),n()},l=function(e){e.target===t&&++u>=a&&c()};setTimeout(function(){u<a&&c()},i+1),t.addEventListener(s,l)}function Xn(t,e){var n,r=window.getComputedStyle(t),o=r[us+"Delay"].split(", "),i=r[us+"Duration"].split(", "),a=Yn(o,i),s=r[ls+"Delay"].split(", "),u=r[ls+"Duration"].split(", "),c=Yn(s,u),l=0,f=0;return e===as?a>0&&(n=as,l=a,f=i.length):e===ss?c>0&&(n=ss,l=c,f=u.length):(l=Math.max(a,c),n=l>0?a>c?as:ss:null,f=n?n===as?i.length:u.length:0),{type:n,timeout:l,propCount:f,hasTransform:n===as&&ds.test(r[us+"Property"])}}function Yn(t,e){for(;t.length<e.length;)t=t.concat(t);return Math.max.apply(null,e.map(function(e,n){return Qn(e)+Qn(t[n])}))}function Qn(t){return 1e3*Number(t.slice(0,-1))}function tr(t,n){var r=t.elm;r._leaveCb&&(r._leaveCb.cancelled=!0,r._leaveCb());var i=zn(t.data.transition);if(i&&!r._enterCb&&1===r.nodeType){for(var a=i.css,s=i.type,u=i.enterClass,c=i.enterToClass,l=i.enterActiveClass,f=i.appearClass,p=i.appearToClass,v=i.appearActiveClass,h=i.beforeEnter,m=i.enter,y=i.afterEnter,g=i.enterCancelled,_=i.beforeAppear,w=i.appear,E=i.afterAppear,x=i.appearCancelled,$=i.duration,O=Gi,N=Gi.$vnode;N&&N.parent;)N=N.parent,O=N.context;var k=!O._isMounted||!t.isRootInsert;if(!k||w||""===w){var C=k&&f?f:u,A=k&&v?v:l,T=k&&p?p:c,D=k?_||h:h,S=k&&"function"==typeof w?w:m,j=k?E||y:y,V=k?x||g:g,M=o(d($)?$.enter:$);"production"!==e.env.NODE_ENV&&null!=M&&nr(M,"enter",t);var P=a!==!1&&!ai,I=or(S),L=r._enterCb=b(function(){P&&(Wn(r,T),Wn(r,A)),L.cancelled?(P&&Wn(r,C),V&&V(r)):j&&j(r),r._enterCb=null});t.data.show||tt(t.data.hook||(t.data.hook={}),"insert",function(){var e=r.parentNode,n=e&&e._pending&&e._pending[t.key];n&&n.tag===t.tag&&n.elm._leaveCb&&n.elm._leaveCb(),S&&S(r,L)}),D&&D(r),P&&(Kn(r,C),Kn(r,A),Gn(function(){Kn(r,T),Wn(r,C),L.cancelled||I||(rr(M)?setTimeout(L,M):Zn(r,s,L))})),t.data.show&&(n&&n(),S&&S(r,L)),P||I||L()}}}function er(t,n){function r(){x.cancelled||(t.data.show||((i.parentNode._pending||(i.parentNode._pending={}))[t.key]=t),p&&p(i),_&&(Kn(i,c),Kn(i,f),Gn(function(){Kn(i,l),Wn(i,c),x.cancelled||w||(rr(E)?setTimeout(x,E):Zn(i,u,x))})),v&&v(i,x),_||w||x())}var i=t.elm;i._enterCb&&(i._enterCb.cancelled=!0,i._enterCb());var a=zn(t.data.transition);if(!a)return n();if(!i._leaveCb&&1===i.nodeType){var s=a.css,u=a.type,c=a.leaveClass,l=a.leaveToClass,f=a.leaveActiveClass,p=a.beforeLeave,v=a.leave,h=a.afterLeave,m=a.leaveCancelled,y=a.delayLeave,g=a.duration,_=s!==!1&&!ai,w=or(v),E=o(d(g)?g.leave:g);"production"!==e.env.NODE_ENV&&null!=E&&nr(E,"leave",t);var x=i._leaveCb=b(function(){i.parentNode&&i.parentNode._pending&&(i.parentNode._pending[t.key]=null),_&&(Wn(i,l),Wn(i,f)),x.cancelled?(_&&Wn(i,c),m&&m(i)):(n(),h&&h(i)),i._leaveCb=null});y?y(r):r()}}function nr(t,e,n){"number"!=typeof t?_i("<transition> explicit "+e+" duration is not a valid number - got "+JSON.stringify(t)+".",n.context):isNaN(t)&&_i("<transition> explicit "+e+" duration is NaN - the duration expression might be incorrect.",n.context)}function rr(t){return"number"==typeof t&&!isNaN(t)}function or(t){if(!t)return!1;var e=t.fns;return e?or(Array.isArray(e)?e[0]:e):(t._length||t.length)>1}function ir(t,e){e.data.show||tr(e)}function ar(t,n,r){var o=n.value,i=t.multiple;if(i&&!Array.isArray(o))return void("production"!==e.env.NODE_ENV&&_i('<select multiple v-model="'+n.expression+'"> expects an Array value for its binding, but got '+Object.prototype.toString.call(o).slice(8,-1),r));for(var a,s,u=0,c=t.options.length;u<c;u++)if(s=t.options[u],i)a=_(o,ur(s))>-1,s.selected!==a&&(s.selected=a);else if(g(ur(s),o))return void(t.selectedIndex!==u&&(t.selectedIndex=u));i||(t.selectedIndex=-1)}function sr(t,e){for(var n=0,r=e.length;n<r;n++)if(g(ur(e[n]),t))return!1;return!0}function ur(t){return"_value"in t?t._value:t.value}function cr(t){t.target.composing=!0}function lr(t){t.target.composing=!1,fr(t.target,"input")}function fr(t,e){var n=document.createEvent("HTMLEvents");n.initEvent(e,!0,!0),t.dispatchEvent(n)}function pr(t){return!t.componentInstance||t.data&&t.data.transition?t:pr(t.componentInstance._vnode)}function dr(t){var e=t&&t.componentOptions;return e&&e.Ctor.options.abstract?dr(ot(e.children)):t}function vr(t){var e={},n=t.$options;for(var r in n.propsData)e[r]=t[r];var o=n._parentListeners;for(var i in o)e[Go(i)]=o[i];return e}function hr(t,e){return/\d-keep-alive$/.test(e.tag)?t("keep-alive"):null}function mr(t){for(;t=t.parent;)if(t.data.transition)return!0}function yr(t,e){return e.key===t.key&&e.tag===t.tag}function gr(t){t.elm._moveCb&&t.elm._moveCb(),t.elm._enterCb&&t.elm._enterCb()}function _r(t){t.data.newPos=t.elm.getBoundingClientRect()}function br(t){var e=t.data.pos,n=t.data.newPos,r=e.left-n.left,o=e.top-n.top;if(r||o){t.data.moved=!0;var i=t.elm.style;i.transform=i.WebkitTransform="translate("+r+"px,"+o+"px)",i.transitionDuration="0s"}}function wr(t,e){var n=document.createElement("div");return n.innerHTML='<div a="'+t+'">',n.innerHTML.indexOf(e)>0}function Er(t){return Ns=Ns||document.createElement("div"),Ns.innerHTML=t,Ns.textContent}function xr(t,e){var n=e?pu:fu;return t.replace(n,function(t){return lu[t]})}function $r(t,n){function r(e){p+=e,t=t.substring(e)}function o(){var e=t.match(Is);if(e){var n={tagName:e[1],attrs:[],start:p};r(e[0].length);for(var o,i;!(o=t.match(Ls))&&(i=t.match(Vs));)r(i[0].length),n.attrs.push(i);if(o)return n.unarySlash=o[1],r(o[0].length),n.end=p,n}}function i(t){var e=t.tagName,r=t.unarySlash;l&&("p"===u&&Ts(e)&&a(u),As(e)&&u===e&&a(e));for(var o=f(e)||"html"===e&&"head"===u||!!r,i=t.attrs.length,s=new Array(i),p=0;p<i;p++){var d=t.attrs[p];Bs&&d[0].indexOf('""')===-1&&(""===d[3]&&delete d[3],""===d[4]&&delete d[4],""===d[5]&&delete d[5]);var v=d[3]||d[4]||d[5]||"";s[p]={name:d[1],value:xr(v,n.shouldDecodeNewlines)}}o||(c.push({tag:e,lowerCasedTag:e.toLowerCase(),attrs:s}),u=e),n.start&&n.start(e,s,o,t.start,t.end)}function a(t,r,o){var i,a;if(null==r&&(r=p),null==o&&(o=p),t&&(a=t.toLowerCase()),t)for(i=c.length-1;i>=0&&c[i].lowerCasedTag!==a;i--);else i=0;if(i>=0){for(var s=c.length-1;s>=i;s--)"production"!==e.env.NODE_ENV&&(s>i||!t)&&n.warn&&n.warn("tag <"+c[s].tag+"> has no matching end tag."),n.end&&n.end(c[s].tag,r,o);c.length=i,u=i&&c[i-1].tag}else"br"===a?n.start&&n.start(t,[],!0,r,o):"p"===a&&(n.start&&n.start(t,[],!1,r,o),n.end&&n.end(t,r,o))}for(var s,u,c=[],l=n.expectHTML,f=n.isUnaryTag||Qo,p=0;t;){if(s=t,u&&uu(u)){var d=u.toLowerCase(),v=cu[d]||(cu[d]=new RegExp("([\\s\\S]*?)(</"+d+"[^>]*>)","i")),h=0,m=t.replace(v,function(t,e,r){return h=r.length,"script"!==d&&"style"!==d&&"noscript"!==d&&(e=e.replace(/<!--([\s\S]*?)-->/g,"$1").replace(/<!\[CDATA\[([\s\S]*?)]]>/g,"$1")),n.chars&&n.chars(e),""});p+=t.length-m.length,t=m,a(d,p-h,p)}else{var y=t.indexOf("<");if(0===y){if(Fs.test(t)){var g=t.indexOf("-->");if(g>=0){r(g+3);continue}}if(Hs.test(t)){var _=t.indexOf("]>");if(_>=0){r(_+2);continue}}var b=t.match(Us);if(b){r(b[0].length);continue}var w=t.match(Rs);if(w){var E=p;r(w[0].length),a(w[1],E,p);continue}var x=o();if(x){i(x);continue}}var $=void 0,O=void 0,N=void 0;if(y>=0){for(O=t.slice(y);!(Rs.test(O)||Is.test(O)||Fs.test(O)||Hs.test(O))&&!((N=O.indexOf("<",1))<0);)y+=N,O=t.slice(y);$=t.substring(0,y),r(y)}y<0&&($=t,t=""),n.chars&&$&&n.chars($)}if(t===s){n.chars&&n.chars(t),"production"!==e.env.NODE_ENV&&!c.length&&n.warn&&n.warn('Mal-formatted tag at end of template: "'+t+'"');break}}a()}function Or(t,e){var n=e?hu(e):du;if(n.test(t)){for(var r,o,i=[],a=n.lastIndex=0;r=n.exec(t);){o=r.index,o>a&&i.push(JSON.stringify(t.slice(a,o)));var s=un(r[1].trim());i.push("_s("+s+")"),a=o+r[0].length}return a<t.length&&i.push(JSON.stringify(t.slice(a))),i.join("+")}}function Nr(t,n){function r(t){t.pre&&(u=!1),Gs(t.tag)&&(c=!1)}qs=n.warn||ln,Js=n.getTagNamespace||Qo,zs=n.mustUseProp||Qo,Gs=n.isPreTag||Qo,Ks=fn(n.modules,"preTransformNode"),Ws=fn(n.modules,"transformNode"),Zs=fn(n.modules,"postTransformNode"),Xs=n.delimiters;var o,i,a=[],s=n.preserveWhitespace!==!1,u=!1,c=!1,l=!1;return $r(t,{warn:qs,expectHTML:n.expectHTML,isUnaryTag:n.isUnaryTag,shouldDecodeNewlines:n.shouldDecodeNewlines,start:function(t,s,f){function p(t){"production"===e.env.NODE_ENV||l||("slot"!==t.tag&&"template"!==t.tag||(l=!0,qs("Cannot use <"+t.tag+"> as component root element because it may contain multiple nodes.")),t.attrsMap.hasOwnProperty("v-for")&&(l=!0,qs("Cannot use v-for on stateful component root element because it renders multiple elements.")))}var d=i&&i.ns||Js(t);ii&&"svg"===d&&(s=qr(s));var v={type:1,tag:t,attrsList:s,attrsMap:Hr(s),parent:i,children:[]};d&&(v.ns=d),Br(v)&&!fi()&&(v.forbidden=!0,"production"!==e.env.NODE_ENV&&qs("Templates should only be responsible for mapping the state to the UI. Avoid placing tags with side-effects in your templates, such as <"+t+">, as they will not be parsed."));for(var h=0;h<Ks.length;h++)Ks[h](v,n);if(u||(kr(v),v.pre&&(u=!0)),Gs(v.tag)&&(c=!0),u)Cr(v);else{Dr(v),Sr(v),Pr(v),Ar(v),v.plain=!v.key&&!s.length,Tr(v),Ir(v),Lr(v);for(var m=0;m<Ws.length;m++)Ws[m](v,n);Rr(v)}if(o?a.length||(o.if&&(v.elseif||v.else)?(p(v),Mr(o,{exp:v.elseif,block:v})):"production"===e.env.NODE_ENV||l||(l=!0,qs("Component template should contain exactly one root element. If you are using v-if on multiple elements, use v-else-if to chain them instead."))):(o=v,p(o)),i&&!v.forbidden)if(v.elseif||v.else)jr(v,i);else if(v.slotScope){i.plain=!1;var y=v.slotTarget||'"default"';(i.scopedSlots||(i.scopedSlots={}))[y]=v}else i.children.push(v),v.parent=i;f?r(v):(i=v,a.push(v));for(var g=0;g<Zs.length;g++)Zs[g](v,n)},end:function(){var t=a[a.length-1],e=t.children[t.children.length-1];e&&3===e.type&&" "===e.text&&!c&&t.children.pop(),a.length-=1,i=a[a.length-1],r(t)},chars:function(n){if(!i)return void("production"===e.env.NODE_ENV||l||n!==t||(l=!0,qs("Component template requires a root element, rather than just text.")));if(!ii||"textarea"!==i.tag||i.attrsMap.placeholder!==n){var r=i.children;if(n=c||n.trim()?xu(n):s&&r.length?" ":""){var o;!u&&" "!==n&&(o=Or(n,Xs))?r.push({type:2,expression:o,text:n}):" "===n&&r.length&&" "===r[r.length-1].text||r.push({type:3,text:n})}}}}),o}function kr(t){null!=yn(t,"v-pre")&&(t.pre=!0)}function Cr(t){var e=t.attrsList.length;if(e)for(var n=t.attrs=new Array(e),r=0;r<e;r++)n[r]={name:t.attrsList[r].name,value:JSON.stringify(t.attrsList[r].value)};else t.pre||(t.plain=!0)}function Ar(t){var n=mn(t,"key");n&&("production"!==e.env.NODE_ENV&&"template"===t.tag&&qs("<template> cannot be keyed. Place the key on real elements instead."),t.key=n)}function Tr(t){var e=mn(t,"ref");e&&(t.ref=e,t.refInFor=Ur(t))}function Dr(t){var n;if(n=yn(t,"v-for")){var r=n.match(yu);if(!r)return void("production"!==e.env.NODE_ENV&&qs("Invalid v-for expression: "+n));t.for=r[2].trim();var o=r[1].trim(),i=o.match(gu);i?(t.alias=i[1].trim(),t.iterator1=i[2].trim(),i[3]&&(t.iterator2=i[3].trim())):t.alias=o}}function Sr(t){var e=yn(t,"v-if");if(e)t.if=e,Mr(t,{exp:e,block:t});else{null!=yn(t,"v-else")&&(t.else=!0);var n=yn(t,"v-else-if");n&&(t.elseif=n)}}function jr(t,n){var r=Vr(n.children);r&&r.if?Mr(r,{exp:t.elseif,block:t}):"production"!==e.env.NODE_ENV&&qs("v-"+(t.elseif?'else-if="'+t.elseif+'"':"else")+" used on element <"+t.tag+"> without corresponding v-if.")}function Vr(t){for(var n=t.length;n--;){if(1===t[n].type)return t[n];"production"!==e.env.NODE_ENV&&" "!==t[n].text&&qs('text "'+t[n].text.trim()+'" between v-if and v-else(-if) will be ignored.'),t.pop()}}function Mr(t,e){t.ifConditions||(t.ifConditions=[]),t.ifConditions.push(e)}function Pr(t){null!=yn(t,"v-once")&&(t.once=!0)}function Ir(t){if("slot"===t.tag)t.slotName=mn(t,"name"),"production"!==e.env.NODE_ENV&&t.key&&qs("`key` does not work on <slot> because slots are abstract outlets and can possibly expand into multiple elements. Use the key on a wrapping element instead.");else{var n=mn(t,"slot");n&&(t.slotTarget='""'===n?'"default"':n),"template"===t.tag&&(t.slotScope=yn(t,"scope"))}}function Lr(t){var e;(e=mn(t,"is"))&&(t.component=e),null!=yn(t,"inline-template")&&(t.inlineTemplate=!0)}function Rr(t){var n,r,o,i,a,s,u,c,l=t.attrsList;for(n=0,r=l.length;n<r;n++)if(o=i=l[n].name,a=l[n].value,mu.test(o))if(t.hasBindings=!0,u=Fr(o),u&&(o=o.replace(Eu,"")),_u.test(o))o=o.replace(_u,""),a=un(a),c=!1,u&&(u.prop&&(c=!0,"innerHtml"===(o=Go(o))&&(o="innerHTML")),u.camel&&(o=Go(o))),c||zs(t.tag,t.attrsMap.type,o)?pn(t,o,a):dn(t,o,a);else if(bu.test(o))o=o.replace(bu,""),hn(t,o,a,u);else{o=o.replace(mu,"");var f=o.match(wu);f&&(s=f[1])&&(o=o.slice(0,-(s.length+1))),vn(t,o,i,a,s,u),"production"!==e.env.NODE_ENV&&"model"===o&&Jr(t,a)}else{if("production"!==e.env.NODE_ENV){var p=Or(a,Xs);p&&qs(o+'="'+a+'": Interpolation inside attributes has been removed. Use v-bind or the colon shorthand instead. For example, instead of <div id="{{ val }}">, use <div :id="val">.')}dn(t,o,JSON.stringify(a))}}function Ur(t){for(var e=t;e;){if(void 0!==e.for)return!0;e=e.parent}return!1}function Fr(t){var e=t.match(Eu);if(e){var n={};return e.forEach(function(t){n[t.slice(1)]=!0}),n}}function Hr(t){for(var n={},r=0,o=t.length;r<o;r++)"production"!==e.env.NODE_ENV&&n[t[r].name]&&!ii&&qs("duplicate attribute: "+t[r].name),n[t[r].name]=t[r].value;return n}function Br(t){return"style"===t.tag||"script"===t.tag&&(!t.attrsMap.type||"text/javascript"===t.attrsMap.type)}function qr(t){for(var e=[],n=0;n<t.length;n++){var r=t[n];$u.test(r.name)||(r.name=r.name.replace(Ou,""),e.push(r))}return e}function Jr(t,e){for(var n=t;n;)n.for&&n.alias===e&&qs("<"+t.tag+' v-model="'+e+'">: You are binding v-model directly to a v-for iteration alias. This will not be able to modify the v-for source array because writing to the alias is like modifying a function local variable. Consider using an array of objects and use v-model on an object property instead.'),n=n.parent}function zr(t,e){t&&(Ys=Nu(e.staticKeys||""),Qs=e.isReservedTag||Qo,Kr(t),Wr(t,!1))}function Gr(t){return i("type,tag,attrsList,attrsMap,plain,parent,children,attrs"+(t?","+t:""))}function Kr(t){if(t.static=Xr(t),1===t.type){if(!Qs(t.tag)&&"slot"!==t.tag&&null==t.attrsMap["inline-template"])return;for(var e=0,n=t.children.length;e<n;e++){var r=t.children[e];Kr(r),r.static||(t.static=!1)}}}function Wr(t,e){if(1===t.type){if((t.static||t.once)&&(t.staticInFor=e),t.static&&t.children.length&&(1!==t.children.length||3!==t.children[0].type))return void(t.staticRoot=!0);if(t.staticRoot=!1,t.children)for(var n=0,r=t.children.length;n<r;n++)Wr(t.children[n],e||!!t.for);t.ifConditions&&Zr(t.ifConditions,e)}}function Zr(t,e){for(var n=1,r=t.length;n<r;n++)Wr(t[n].block,e)}function Xr(t){return 2!==t.type&&(3===t.type||!(!t.pre&&(t.hasBindings||t.if||t.for||qo(t.tag)||!Qs(t.tag)||Yr(t)||!Object.keys(t).every(Ys))))}function Yr(t){for(;t.parent;){if(t=t.parent,"template"!==t.tag)return!1;if(t.for)return!0}return!1}function Qr(t,e){var n=e?"nativeOn:{":"on:{";for(var r in t)n+='"'+r+'":'+to(r,t[r])+",";return n.slice(0,-1)+"}"}function to(t,e){if(e){if(Array.isArray(e))return"["+e.map(function(e){return to(t,e)}).join(",")+"]";if(e.modifiers){var n="",r=[];for(var o in e.modifiers)Du[o]?n+=Du[o]:r.push(o);r.length&&(n=eo(r)+n);return"function($event){"+n+(Cu.test(e.value)?e.value+"($event)":e.value)+"}"}return ku.test(e.value)||Cu.test(e.value)?e.value:"function($event){"+e.value+"}"}return"function(){}"}function eo(t){return"if("+t.map(no).join("&&")+")return null;"}function no(t){var e=parseInt(t,10);if(e)return"$event.keyCode!=="+e;var n=Au[t];return"_k($event.keyCode,"+JSON.stringify(t)+(n?","+JSON.stringify(n):"")+")"}function ro(t,e){t.wrapData=function(n){return"_b("+n+",'"+t.tag+"',"+e.value+(e.modifiers&&e.modifiers.prop?",true":"")+")"}}function oo(t,e){var n=iu,r=iu=[],o=au;au=0,su=e,tu=e.warn||ln,eu=fn(e.modules,"transformCode"),nu=fn(e.modules,"genData"),ru=e.directives||{},ou=e.isReservedTag||Qo;var i=t?io(t):'_c("div")';return iu=n,au=o,{render:"with(this){return "+i+"}",staticRenderFns:r}}function io(t){if(t.staticRoot&&!t.staticProcessed)return ao(t);if(t.once&&!t.onceProcessed)return so(t);if(t.for&&!t.forProcessed)return lo(t);if(t.if&&!t.ifProcessed)return uo(t);if("template"!==t.tag||t.slotTarget){if("slot"===t.tag)return xo(t);var e;if(t.component)e=$o(t.component,t);else{var n=t.plain?void 0:fo(t),r=t.inlineTemplate?null:yo(t,!0);e="_c('"+t.tag+"'"+(n?","+n:"")+(r?","+r:"")+")"}for(var o=0;o<eu.length;o++)e=eu[o](t,e);return e}return yo(t)||"void 0"}function ao(t){return t.staticProcessed=!0,iu.push("with(this){return "+io(t)+"}"),"_m("+(iu.length-1)+(t.staticInFor?",true":"")+")"}function so(t){if(t.onceProcessed=!0,t.if&&!t.ifProcessed)return uo(t);if(t.staticInFor){for(var n="",r=t.parent;r;){if(r.for){n=r.key;break}r=r.parent}return n?"_o("+io(t)+","+au+++(n?","+n:"")+")":("production"!==e.env.NODE_ENV&&tu("v-once can only be used inside v-for that is keyed. "),io(t))}return ao(t)}function uo(t){return t.ifProcessed=!0,co(t.ifConditions.slice())}function co(t){function e(t){return t.once?so(t):io(t)}if(!t.length)return"_e()";var n=t.shift();return n.exp?"("+n.exp+")?"+e(n.block)+":"+co(t):""+e(n.block)}function lo(t){var n=t.for,r=t.alias,o=t.iterator1?","+t.iterator1:"",i=t.iterator2?","+t.iterator2:"";return"production"!==e.env.NODE_ENV&&bo(t)&&"slot"!==t.tag&&"template"!==t.tag&&!t.key&&tu("<"+t.tag+' v-for="'+r+" in "+n+'">: component lists rendered with v-for should have explicit keys. See https://vuejs.org/guide/list.html#key for more info.',!0),t.forProcessed=!0,"_l(("+n+"),function("+r+o+i+"){return "+io(t)+"})"}function fo(t){var e="{",n=po(t);n&&(e+=n+","),t.key&&(e+="key:"+t.key+","),t.ref&&(e+="ref:"+t.ref+","),t.refInFor&&(e+="refInFor:true,"),t.pre&&(e+="pre:true,"),t.component&&(e+='tag:"'+t.tag+'",');for(var r=0;r<nu.length;r++)e+=nu[r](t);if(t.attrs&&(e+="attrs:{"+Oo(t.attrs)+"},"),t.props&&(e+="domProps:{"+Oo(t.props)+"},"),t.events&&(e+=Qr(t.events)+","),t.nativeEvents&&(e+=Qr(t.nativeEvents,!0)+","),t.slotTarget&&(e+="slot:"+t.slotTarget+","),t.scopedSlots&&(e+=ho(t.scopedSlots)+","),t.model&&(e+="model:{value:"+t.model.value+",callback:"+t.model.callback+"},"),t.inlineTemplate){var o=vo(t);o&&(e+=o+",")}return e=e.replace(/,$/,"")+"}",t.wrapData&&(e=t.wrapData(e)),e}function po(t){var e=t.directives;if(e){var n,r,o,i,a="directives:[",s=!1;for(n=0,r=e.length;n<r;n++){o=e[n],i=!0;var u=ru[o.name]||Su[o.name];u&&(i=!!u(t,o,tu)),i&&(s=!0,a+='{name:"'+o.name+'",rawName:"'+o.rawName+'"'+(o.value?",value:("+o.value+"),expression:"+JSON.stringify(o.value):"")+(o.arg?',arg:"'+o.arg+'"':"")+(o.modifiers?",modifiers:"+JSON.stringify(o.modifiers):"")+"},")}return s?a.slice(0,-1)+"]":void 0}}function vo(t){var n=t.children[0];if("production"!==e.env.NODE_ENV&&(t.children.length>1||1!==n.type)&&tu("Inline-template components must have exactly one child element."),1===n.type){var r=oo(n,su);return"inlineTemplate:{render:function(){"+r.render+"},staticRenderFns:["+r.staticRenderFns.map(function(t){return"function(){"+t+"}"}).join(",")+"]}"}}function ho(t){return"scopedSlots:_u(["+Object.keys(t).map(function(e){return mo(e,t[e])}).join(",")+"])"}function mo(t,e){return"["+t+",function("+String(e.attrsMap.scope)+"){return "+("template"===e.tag?yo(e)||"void 0":io(e))+"}]"}function yo(t,e){var n=t.children;if(n.length){var r=n[0];if(1===n.length&&r.for&&"template"!==r.tag&&"slot"!==r.tag)return io(r);var o=go(n);return"["+n.map(wo).join(",")+"]"+(e&&o?","+o:"")}}function go(t){for(var e=0,n=0;n<t.length;n++){var r=t[n];if(1===r.type){if(_o(r)||r.ifConditions&&r.ifConditions.some(function(t){return _o(t.block)})){e=2;break}(bo(r)||r.ifConditions&&r.ifConditions.some(function(t){return bo(t.block)}))&&(e=1)}}return e}function _o(t){return void 0!==t.for||"template"===t.tag||"slot"===t.tag}function bo(t){return!ou(t.tag)}function wo(t){return 1===t.type?io(t):Eo(t)}function Eo(t){return"_v("+(2===t.type?t.expression:No(JSON.stringify(t.text)))+")"}function xo(t){var e=t.slotName||'"default"',n=yo(t),r="_t("+e+(n?","+n:""),o=t.attrs&&"{"+t.attrs.map(function(t){return Go(t.name)+":"+t.value}).join(",")+"}",i=t.attrsMap["v-bind"];return!o&&!i||n||(r+=",null"),o&&(r+=","+o),i&&(r+=(o?"":",null")+","+i),r+")"}function $o(t,e){var n=e.inlineTemplate?null:yo(e,!0);return"_c("+t+","+fo(e)+(n?","+n:"")+")"}function Oo(t){for(var e="",n=0;n<t.length;n++){var r=t[n];e+='"'+r.name+'":'+No(r.value)+","}return e.slice(0,-1)}function No(t){return t.replace(/\u2028/g,"\\u2028").replace(/\u2029/g,"\\u2029")}function ko(t){var e=[];return t&&Co(t,e),e}function Co(t,e){if(1===t.type){for(var n in t.attrsMap)if(mu.test(n)){var r=t.attrsMap[n];r&&("v-for"===n?Ao(t,'v-for="'+r+'"',e):Do(r,n+'="'+r+'"',e))}if(t.children)for(var o=0;o<t.children.length;o++)Co(t.children[o],e)}else 2===t.type&&Do(t.expression,t.text,e)}function Ao(t,e,n){Do(t.for||"",e,n),To(t.alias,"v-for alias",e,n),To(t.iterator1,"v-for iterator",e,n),To(t.iterator2,"v-for iterator",e,n)}function To(t,e,n,r){"string"!=typeof t||Vu.test(t)||r.push("invalid "+e+' "'+t+'" in expression: '+n.trim())}function Do(t,e,n){try{new Function("return "+t)}catch(o){var r=t.replace(Mu,"").match(ju);r?n.push('avoid using JavaScript keyword as property name: "'+r[0]+'" in expression '+e.trim()):n.push("invalid expression: "+e.trim())}}function So(t,e){var n=Nr(t.trim(),e);zr(n,e);var r=oo(n,e);return{ast:n,render:r.render,staticRenderFns:r.staticRenderFns}}function jo(t,e){try{return new Function(t)}catch(n){return e.push({err:n,code:t}),m}}function Vo(t){function n(n,r){var o=Object.create(t),i=[],a=[];if(o.warn=function(t,e){(e?a:i).push(t)},r){r.modules&&(o.modules=(t.modules||[]).concat(r.modules)),r.directives&&(o.directives=p(Object.create(t.directives),r.directives));for(var s in r)"modules"!==s&&"directives"!==s&&(o[s]=r[s])}var u=So(n,o);return"production"!==e.env.NODE_ENV&&i.push.apply(i,ko(u.ast)),u.errors=i,u.tips=a,u}function r(t,r,i){if(r=r||{},"production"!==e.env.NODE_ENV)try{new Function("return 1")}catch(t){t.toString().match(/unsafe-eval|CSP/)&&_i("It seems you are using the standalone build of Vue.js in an environment with Content Security Policy that prohibits unsafe-eval. The template compiler cannot work in this environment. Consider relaxing the policy to allow unsafe-eval or pre-compiling your templates into render functions.")}var a=r.delimiters?String(r.delimiters)+t:t;if(o[a])return o[a];var s=n(t,r);"production"!==e.env.NODE_ENV&&(s.errors&&s.errors.length&&_i("Error compiling template:\n\n"+t+"\n\n"+s.errors.map(function(t){return"- "+t}).join("\n")+"\n",i),s.tips&&s.tips.length&&s.tips.forEach(function(t){return bi(t,i)}));var u={},c=[];u.render=jo(s.render,c);var l=s.staticRenderFns.length;u.staticRenderFns=new Array(l);for(var f=0;f<l;f++)u.staticRenderFns[f]=jo(s.staticRenderFns[f],c);return"production"!==e.env.NODE_ENV&&(s.errors&&s.errors.length||!c.length||_i("Failed to generate render function:\n\n"+c.map(function(t){var e=t.err,n=t.code;return e.toString()+" in\n\n"+n+"\n"}).join("\n"),i)),o[a]=u}var o=Object.create(null);return{compile:n,compileToFunctions:r}}function Mo(t,n){var r=n.warn||ln,o=yn(t,"class");if("production"!==e.env.NODE_ENV&&o){Or(o,n.delimiters)&&r('class="'+o+'": Interpolation inside attributes has been removed. Use v-bind or the colon shorthand instead. For example, instead of <div class="{{ val }}">, use <div :class="val">.')}o&&(t.staticClass=JSON.stringify(o));var i=mn(t,"class",!1);i&&(t.classBinding=i)}function Po(t){var e="";return t.staticClass&&(e+="staticClass:"+t.staticClass+","),t.classBinding&&(e+="class:"+t.classBinding+","),e}function Io(t,n){var r=n.warn||ln,o=yn(t,"style");if(o){if("production"!==e.env.NODE_ENV){Or(o,n.delimiters)&&r('style="'+o+'": Interpolation inside attributes has been removed. Use v-bind or the colon shorthand instead. For example, instead of <div style="{{ val }}">, use <div :style="val">.')}t.staticStyle=JSON.stringify(Xa(o))}var i=mn(t,"style",!1);i&&(t.styleBinding=i)}function Lo(t){var e="";return t.staticStyle&&(e+="staticStyle:"+t.staticStyle+","),t.styleBinding&&(e+="style:("+t.styleBinding+"),"),e}function Ro(t,e){e.value&&pn(t,"textContent","_s("+e.value+")")}function Uo(t,e){e.value&&pn(t,"innerHTML","_s("+e.value+")")}function Fo(t){if(t.outerHTML)return t.outerHTML;var e=document.createElement("div");return e.appendChild(t.cloneNode(!0)),e.innerHTML}var Ho,Bo,qo=i("slot,component",!0),Jo=Object.prototype.hasOwnProperty,zo=/-(\w)/g,Go=c(function(t){return t.replace(zo,function(t,e){return e?e.toUpperCase():""})}),Ko=c(function(t){return t.charAt(0).toUpperCase()+t.slice(1)}),Wo=/([^-])([A-Z])/g,Zo=c(function(t){return t.replace(Wo,"$1-$2").replace(Wo,"$1-$2").toLowerCase()}),Xo=Object.prototype.toString,Yo="[object Object]",Qo=function(){return!1},ti=function(t){return t},ei={optionMergeStrategies:Object.create(null),silent:!1,productionTip:"production"!==e.env.NODE_ENV,devtools:"production"!==e.env.NODE_ENV,performance:"production"!==e.env.NODE_ENV,errorHandler:null,ignoredElements:[],keyCodes:Object.create(null),isReservedTag:Qo,isUnknownElement:Qo,getTagNamespace:m,parsePlatformTagName:ti,mustUseProp:Qo,_assetTypes:["component","directive","filter"],_lifecycleHooks:["beforeCreate","created","beforeMount","mounted","beforeUpdate","updated","beforeDestroy","destroyed","activated","deactivated"],_maxUpdateCount:100},ni="__proto__"in{},ri="undefined"!=typeof window,oi=ri&&window.navigator.userAgent.toLowerCase(),ii=oi&&/msie|trident/.test(oi),ai=oi&&oi.indexOf("msie 9.0")>0,si=oi&&oi.indexOf("edge/")>0,ui=oi&&oi.indexOf("android")>0,ci=oi&&/iphone|ipad|ipod|ios/.test(oi),li=oi&&/chrome\/\d+/.test(oi)&&!si,fi=function(){return void 0===Ho&&(Ho=!ri&&void 0!==n&&"server"===n.process.env.VUE_ENV),Ho},pi=ri&&window.__VUE_DEVTOOLS_GLOBAL_HOOK__,di="undefined"!=typeof Symbol&&w(Symbol)&&"undefined"!=typeof Reflect&&w(Reflect.ownKeys),vi=function(){function t(){r=!1;var t=n.slice(0);n.length=0;for(var e=0;e<t.length;e++)t[e]()}var e,n=[],r=!1;if("undefined"!=typeof Promise&&w(Promise)){var o=Promise.resolve(),i=function(t){console.error(t)};e=function(){o.then(t).catch(i),ci&&setTimeout(m)}}else if("undefined"==typeof MutationObserver||!w(MutationObserver)&&"[object MutationObserverConstructor]"!==MutationObserver.toString())e=function(){setTimeout(t,0)};else{var a=1,s=new MutationObserver(t),u=document.createTextNode(String(a));s.observe(u,{characterData:!0}),e=function(){a=(a+1)%2,u.data=String(a)}}return function(t,o){var i;if(n.push(function(){t&&t.call(o),i&&i(o)}),r||(r=!0,e()),!t&&"undefined"!=typeof Promise)return new Promise(function(t){i=t})}}();Bo="undefined"!=typeof Set&&w(Set)?Set:function(){function t(){this.set=Object.create(null)}return t.prototype.has=function(t){return this.set[t]===!0},t.prototype.add=function(t){this.set[t]=!0},t.prototype.clear=function(){this.set=Object.create(null)},t}();var hi;"production"!==e.env.NODE_ENV&&(!(hi=ri&&window.performance)||hi.mark&&hi.measure||(hi=void 0));var mi,yi=Object.freeze({}),gi=/[^\w.$]/,_i=m,bi=m;if("production"!==e.env.NODE_ENV){var wi="undefined"!=typeof console,Ei=/(?:^|[-_])(\w)/g,xi=function(t){return t.replace(Ei,function(t){return t.toUpperCase()}).replace(/[-_]/g,"")};_i=function(t,e){wi&&!ei.silent&&console.error("[Vue warn]: "+t+" "+(e?$i(mi(e)):""))},bi=function(t,e){wi&&!ei.silent&&console.warn("[Vue tip]: "+t+" "+(e?$i(mi(e)):""))},mi=function(t,e){if(t.$root===t)return"<Root>";var n=t._isVue?t.$options.name||t.$options._componentTag:t.name,r=t._isVue&&t.$options.__file;if(!n&&r){var o=r.match(/([^\/\\]+)\.vue$/);n=o&&o[1]}return(n?"<"+xi(n)+">":"<Anonymous>")+(r&&e!==!1?" at "+r:"")};var $i=function(t){return"<Anonymous>"===t&&(t+=' - use the "name" option for better debugging messages.'),"\n(found in "+t+")"}}var Oi=0,Ni=function(){this.id=Oi++,this.subs=[]};Ni.prototype.addSub=function(t){this.subs.push(t)},Ni.prototype.removeSub=function(t){a(this.subs,t)},Ni.prototype.depend=function(){Ni.target&&Ni.target.addDep(this)},Ni.prototype.notify=function(){for(var t=this.subs.slice(),e=0,n=t.length;e<n;e++)t[e].update()},Ni.target=null;var ki=[],Ci=Array.prototype,Ai=Object.create(Ci);["push","pop","shift","unshift","splice","sort","reverse"].forEach(function(t){var e=Ci[t];x(Ai,t,function(){for(var n=arguments,r=arguments.length,o=new Array(r);r--;)o[r]=n[r];var i,a=e.apply(this,o),s=this.__ob__;switch(t){case"push":i=o;break;case"unshift":i=o;break;case"splice":i=o.slice(2)}return i&&s.observeArray(i),s.dep.notify(),a})});var Ti=Object.getOwnPropertyNames(Ai),Di={shouldConvert:!0,isSettingProps:!1},Si=function(t){if(this.value=t,this.dep=new Ni,this.vmCount=0,x(t,"__ob__",this),Array.isArray(t)){(ni?k:C)(t,Ai,Ti),this.observeArray(t)}else this.walk(t)};Si.prototype.walk=function(t){for(var e=Object.keys(t),n=0;n<e.length;n++)T(t,e[n],t[e[n]])},Si.prototype.observeArray=function(t){for(var e=0,n=t.length;e<n;e++)A(t[e])};var ji=ei.optionMergeStrategies;"production"!==e.env.NODE_ENV&&(ji.el=ji.propsData=function(t,e,n,r){return n||_i('option "'+r+'" can only be used during instance creation with the `new` keyword.'),Mi(t,e)}),ji.data=function(t,n,r){return r?t||n?function(){var e="function"==typeof n?n.call(r):n,o="function"==typeof t?t.call(r):void 0;return e?V(e,o):o}:void 0:n?"function"!=typeof n?("production"!==e.env.NODE_ENV&&_i('The "data" option should be a function that returns a per-instance value in component definitions.',r),t):t?function(){return V(n.call(this),t.call(this))}:n:t},ei._lifecycleHooks.forEach(function(t){ji[t]=M}),ei._assetTypes.forEach(function(t){ji[t+"s"]=P}),ji.watch=function(t,e){if(!e)return Object.create(t||null);if(!t)return e;var n={};p(n,t);for(var r in e){var o=n[r],i=e[r];o&&!Array.isArray(o)&&(o=[o]),n[r]=o?o.concat(i):[i]}return n},ji.props=ji.methods=ji.computed=function(t,e){if(!e)return Object.create(t||null);if(!t)return e;var n=Object.create(null);return p(n,t),p(n,e),n};var Vi,Mi=function(t,e){return void 0===e?t:e};if("production"!==e.env.NODE_ENV){var Pi=i("Infinity,undefined,NaN,isFinite,isNaN,parseFloat,parseInt,decodeURI,decodeURIComponent,encodeURI,encodeURIComponent,Math,Number,Date,Array,Object,Boolean,String,RegExp,Map,Set,JSON,Intl,require"),Ii=function(t,e){_i('Property or method "'+e+'" is not defined on the instance but referenced during render. Make sure to declare reactive data properties in the data option.',t)},Li="undefined"!=typeof Proxy&&Proxy.toString().match(/native code/);if(Li){var Ri=i("stop,prevent,self,ctrl,shift,alt,meta");ei.keyCodes=new Proxy(ei.keyCodes,{set:function(t,e,n){return Ri(e)?(_i("Avoid overwriting built-in modifier in config.keyCodes: ."+e),!1):(t[e]=n,!0)}})}var Ui={has:function t(e,n){var t=n in e,r=Pi(n)||"_"===n.charAt(0);return t||r||Ii(e,n),t||!r}},Fi={get:function(t,e){return"string"!=typeof e||e in t||Ii(t,e),t[e]}};Vi=function(t){if(Li){var e=t.$options,n=e.render&&e.render._withStripped?Fi:Ui;t._renderProxy=new Proxy(t,n)}else t._renderProxy=t}}var Hi=function(t,e,n,r,o,i,a){this.tag=t,this.data=e,this.children=n,this.text=r,this.elm=o,this.ns=void 0,this.context=i,this.functionalContext=void 0,this.key=e&&e.key,this.componentOptions=a,this.componentInstance=void 0,this.parent=void 0,this.raw=!1,this.isStatic=!1,this.isRootInsert=!0,this.isComment=!1,this.isCloned=!1,this.isOnce=!1},Bi={child:{}};Bi.child.get=function(){return this.componentInstance},Object.defineProperties(Hi.prototype,Bi);var qi,Ji=function(){var t=new Hi;return t.text="",t.isComment=!0,t},zi=c(function(t){var e="~"===t.charAt(0);t=e?t.slice(1):t;var n="!"===t.charAt(0);return t=n?t.slice(1):t,{name:t,once:e,capture:n}}),Gi=null,Ki=[],Wi={},Zi={},Xi=!1,Yi=!1,Qi=0,ta=0,ea=function(t,n,r,o){this.vm=t,t._watchers.push(this),o?(this.deep=!!o.deep,this.user=!!o.user,this.lazy=!!o.lazy,this.sync=!!o.sync):this.deep=this.user=this.lazy=this.sync=!1,this.cb=r,this.id=++ta,this.active=!0,this.dirty=this.lazy,this.deps=[],this.newDeps=[],this.depIds=new Bo,this.newDepIds=new Bo,this.expression="production"!==e.env.NODE_ENV?n.toString():"","function"==typeof n?this.getter=n:(this.getter=$(n),this.getter||(this.getter=function(){},"production"!==e.env.NODE_ENV&&_i('Failed watching path: "'+n+'" Watcher only accepts simple dot-delimited paths. For full control, use a function instead.',t))),this.value=this.lazy?void 0:this.get()};ea.prototype.get=function(){O(this);var t,e=this.vm;if(this.user)try{t=this.getter.call(e,e)}catch(t){K(t,e,'getter for watcher "'+this.expression+'"')}else t=this.getter.call(e,e);return this.deep&&xt(t),N(),this.cleanupDeps(),t},ea.prototype.addDep=function(t){var e=t.id;this.newDepIds.has(e)||(this.newDepIds.add(e),this.newDeps.push(t),this.depIds.has(e)||t.addSub(this))},ea.prototype.cleanupDeps=function(){for(var t=this,e=this.deps.length;e--;){var n=t.deps[e];t.newDepIds.has(n.id)||n.removeSub(t)}var r=this.depIds;this.depIds=this.newDepIds,this.newDepIds=r,this.newDepIds.clear(),r=this.deps,this.deps=this.newDeps,this.newDeps=r,this.newDeps.length=0},ea.prototype.update=function(){this.lazy?this.dirty=!0:this.sync?this.run():Et(this)},ea.prototype.run=function(){if(this.active){var t=this.get();if(t!==this.value||d(t)||this.deep){var e=this.value;if(this.value=t,this.user)try{this.cb.call(this.vm,t,e)}catch(t){K(t,this.vm,'callback for watcher "'+this.expression+'"')}else this.cb.call(this.vm,t,e)}}},ea.prototype.evaluate=function(){this.value=this.get(),this.dirty=!1},ea.prototype.depend=function(){for(var t=this,e=this.deps.length;e--;)t.deps[e].depend()},ea.prototype.teardown=function(){var t=this;if(this.active){this.vm._isBeingDestroyed||a(this.vm._watchers,this);for(var e=this.deps.length;e--;)t.deps[e].removeSub(t);this.active=!1}};var na=new Bo,ra={enumerable:!0,configurable:!0,get:m,set:m},oa={key:1,ref:1,slot:1},ia={lazy:!0},aa={init:Rt,prepatch:Ut,insert:Ft,destroy:Ht},sa=Object.keys(aa),ua=1,ca=2,la=0;le(he),Mt(he),ct(he),dt(he),ue(he);var fa=[String,RegExp],pa={name:"keep-alive",abstract:!0,props:{include:fa,exclude:fa},created:function(){this.cache=Object.create(null)},destroyed:function(){var t=this;for(var e in t.cache)Oe(t.cache[e])},watch:{include:function(t){$e(this.cache,function(e){return xe(t,e)})},exclude:function(t){$e(this.cache,function(e){return!xe(t,e)})}},render:function(){var t=ot(this.$slots.default),e=t&&t.componentOptions;if(e){var n=Ee(e);if(n&&(this.include&&!xe(this.include,n)||this.exclude&&xe(this.exclude,n)))return t;var r=null==t.key?e.Ctor.cid+(e.tag?"::"+e.tag:""):t.key;this.cache[r]?t.componentInstance=this.cache[r].componentInstance:this.cache[r]=t,t.data.keepAlive=!0}return t}},da={KeepAlive:pa};Ne(he),Object.defineProperty(he.prototype,"$isServer",{get:fi}),he.version="2.2.1";var va,ha,ma,ya,ga,_a,ba,wa,Ea,xa=i("input,textarea,option,select"),$a=function(t,e,n){return"value"===n&&xa(t)&&"button"!==e||"selected"===n&&"option"===t||"checked"===n&&"input"===t||"muted"===n&&"video"===t},Oa=i("contenteditable,draggable,spellcheck"),Na=i("allowfullscreen,async,autofocus,autoplay,checked,compact,controls,declare,default,defaultchecked,defaultmuted,defaultselected,defer,disabled,enabled,formnovalidate,hidden,indeterminate,inert,ismap,itemscope,loop,multiple,muted,nohref,noresize,noshade,novalidate,nowrap,open,pauseonexit,readonly,required,reversed,scoped,seamless,selected,sortable,translate,truespeed,typemustmatch,visible"),ka="http://www.w3.org/1999/xlink",Ca=function(t){return":"===t.charAt(5)&&"xlink"===t.slice(0,5)},Aa=function(t){return Ca(t)?t.slice(6,t.length):""},Ta=function(t){return null==t||t===!1},Da={svg:"http://www.w3.org/2000/svg",math:"http://www.w3.org/1998/Math/MathML"},Sa=i("html,body,base,head,link,meta,style,title,address,article,aside,footer,header,h1,h2,h3,h4,h5,h6,hgroup,nav,section,div,dd,dl,dt,figcaption,figure,hr,img,li,main,ol,p,pre,ul,a,b,abbr,bdi,bdo,br,cite,code,data,dfn,em,i,kbd,mark,q,rp,rt,rtc,ruby,s,samp,small,span,strong,sub,sup,time,u,var,wbr,area,audio,map,track,video,embed,object,param,source,canvas,script,noscript,del,ins,caption,col,colgroup,table,thead,tbody,td,th,tr,button,datalist,fieldset,form,input,label,legend,meter,optgroup,option,output,progress,select,textarea,details,dialog,menu,menuitem,summary,content,element,shadow,template"),ja=i("svg,animate,circle,clippath,cursor,defs,desc,ellipse,filter,font-face,foreignObject,g,glyph,image,line,marker,mask,missing-glyph,path,pattern,polygon,polyline,rect,switch,symbol,text,textpath,tspan,use,view",!0),Va=function(t){return"pre"===t},Ma=function(t){return Sa(t)||ja(t)},Pa=Object.create(null),Ia=Object.freeze({createElement:Me,createElementNS:Pe,createTextNode:Ie,createComment:Le,insertBefore:Re,removeChild:Ue,appendChild:Fe,parentNode:He,nextSibling:Be,tagName:qe,setTextContent:Je,setAttribute:ze}),La={create:function(t,e){Ge(e)},update:function(t,e){t.data.ref!==e.data.ref&&(Ge(t,!0),Ge(e))},destroy:function(t){Ge(t,!0)}},Ra=new Hi("",{},[]),Ua=["create","activate","update","remove","destroy"],Fa={create:Qe,update:Qe,destroy:function(t){Qe(t,Ra)}},Ha=Object.create(null),Ba=[La,Fa],qa={create:on,update:on},Ja={create:sn,update:sn},za=/[\w).+\-_$\]]/,Ga="__r",Ka="__c",Wa={create:Mn,update:Mn},Za={create:Pn,update:Pn},Xa=c(function(t){var e={},n=/;(?![^(]*\))/g,r=/:(.+)/;return t.split(n).forEach(function(t){if(t){var n=t.split(r);n.length>1&&(e[n[0].trim()]=n[1].trim())}}),e}),Ya=/^--/,Qa=/\s*!important$/,ts=function(t,e,n){Ya.test(e)?t.style.setProperty(e,n):Qa.test(n)?t.style.setProperty(e,n.replace(Qa,""),"important"):t.style[ns(e)]=n},es=["Webkit","Moz","ms"],ns=c(function(t){if(Ea=Ea||document.createElement("div"),"filter"!==(t=Go(t))&&t in Ea.style)return t;for(var e=t.charAt(0).toUpperCase()+t.slice(1),n=0;n<es.length;n++){var r=es[n]+e;if(r in Ea.style)return r}}),rs={create:Bn,update:Bn},os=c(function(t){return{enterClass:t+"-enter",enterToClass:t+"-enter-to",enterActiveClass:t+"-enter-active",leaveClass:t+"-leave",leaveToClass:t+"-leave-to",leaveActiveClass:t+"-leave-active"}}),is=ri&&!ai,as="transition",ss="animation",us="transition",cs="transitionend",ls="animation",fs="animationend";is&&(void 0===window.ontransitionend&&void 0!==window.onwebkittransitionend&&(us="WebkitTransition",cs="webkitTransitionEnd"),void 0===window.onanimationend&&void 0!==window.onwebkitanimationend&&(ls="WebkitAnimation",fs="webkitAnimationEnd"));var ps=ri&&window.requestAnimationFrame?window.requestAnimationFrame.bind(window):setTimeout,ds=/\b(transform|all)(,|$)/,vs=ri?{create:ir,activate:ir,remove:function(t,e){t.data.show?e():er(t,e)}}:{},hs=[qa,Ja,Wa,Za,rs,vs],ms=hs.concat(Ba),ys=Ye({nodeOps:Ia,modules:ms});ai&&document.addEventListener("selectionchange",function(){var t=document.activeElement;t&&t.vmodel&&fr(t,"input")});var gs={inserted:function(t,e,n){if("select"===n.tag){var r=function(){ar(t,e,n.context)};r(),(ii||si)&&setTimeout(r,0)}else"textarea"!==n.tag&&"text"!==t.type||(t._vModifiers=e.modifiers,e.modifiers.lazy||(ui||(t.addEventListener("compositionstart",cr),t.addEventListener("compositionend",lr)),ai&&(t.vmodel=!0)))},componentUpdated:function(t,e,n){if("select"===n.tag){ar(t,e,n.context);(t.multiple?e.value.some(function(e){return sr(e,t.options)}):e.value!==e.oldValue&&sr(e.value,t.options))&&fr(t,"change")}}},_s={bind:function(t,e,n){var r=e.value;n=pr(n);var o=n.data&&n.data.transition,i=t.__vOriginalDisplay="none"===t.style.display?"":t.style.display;r&&o&&!ai?(n.data.show=!0,tr(n,function(){t.style.display=i})):t.style.display=r?i:"none"},update:function(t,e,n){var r=e.value;r!==e.oldValue&&(n=pr(n),n.data&&n.data.transition&&!ai?(n.data.show=!0,r?tr(n,function(){t.style.display=t.__vOriginalDisplay}):er(n,function(){t.style.display="none"})):t.style.display=r?t.__vOriginalDisplay:"none")},unbind:function(t,e,n,r,o){o||(t.style.display=t.__vOriginalDisplay)}},bs={model:gs,show:_s},ws={name:String,appear:Boolean,css:Boolean,mode:String,type:String,enterClass:String,leaveClass:String,enterToClass:String,leaveToClass:String,enterActiveClass:String,leaveActiveClass:String,appearClass:String,appearActiveClass:String,appearToClass:String,duration:[Number,String,Object]},Es={name:"transition",props:ws,abstract:!0,render:function(t){var n=this,r=this.$slots.default;if(r&&(r=r.filter(function(t){return t.tag}),r.length)){"production"!==e.env.NODE_ENV&&r.length>1&&_i("<transition> can only be used on a single element. Use <transition-group> for lists.",this.$parent);var o=this.mode;"production"!==e.env.NODE_ENV&&o&&"in-out"!==o&&"out-in"!==o&&_i("invalid <transition> mode: "+o,this.$parent);var i=r[0];if(mr(this.$vnode))return i;var a=dr(i);if(!a)return i;if(this._leaving)return hr(t,i);var s="__transition-"+this._uid+"-";a.key=null==a.key?s+a.tag:u(a.key)?0===String(a.key).indexOf(s)?a.key:s+a.key:a.key;var c=(a.data||(a.data={})).transition=vr(this),l=this._vnode,f=dr(l);if(a.data.directives&&a.data.directives.some(function(t){return"show"===t.name})&&(a.data.show=!0),f&&f.data&&!yr(a,f)){var d=f&&(f.data.transition=p({},c));if("out-in"===o)return this._leaving=!0,tt(d,"afterLeave",function(){n._leaving=!1,n.$forceUpdate()}),hr(t,i);if("in-out"===o){var v,h=function(){v()};tt(c,"afterEnter",h),tt(c,"enterCancelled",h),tt(d,"delayLeave",function(t){v=t})}}return i}}},xs=p({tag:String,moveClass:String},ws);delete xs.mode;var $s={props:xs,render:function(t){for(var n=this.tag||this.$vnode.data.tag||"span",r=Object.create(null),o=this.prevChildren=this.children,i=this.$slots.default||[],a=this.children=[],s=vr(this),u=0;u<i.length;u++){var c=i[u];if(c.tag)if(null!=c.key&&0!==String(c.key).indexOf("__vlist"))a.push(c),r[c.key]=c,(c.data||(c.data={})).transition=s;else if("production"!==e.env.NODE_ENV){var l=c.componentOptions,f=l?l.Ctor.options.name||l.tag||"":c.tag;_i("<transition-group> children must be keyed: <"+f+">")}}if(o){for(var p=[],d=[],v=0;v<o.length;v++){var h=o[v];h.data.transition=s,h.data.pos=h.elm.getBoundingClientRect(),r[h.key]?p.push(h):d.push(h)}this.kept=t(n,null,p),this.removed=d}return t(n,null,a)},beforeUpdate:function(){this.__patch__(this._vnode,this.kept,!1,!0),this._vnode=this.kept},updated:function(){var t=this.prevChildren,e=this.moveClass||(this.name||"v")+"-move";if(t.length&&this.hasMove(t[0].elm,e)){t.forEach(gr),t.forEach(_r),t.forEach(br);var n=document.body;n.offsetHeight;t.forEach(function(t){if(t.data.moved){var n=t.elm,r=n.style;Kn(n,e),r.transform=r.WebkitTransform=r.transitionDuration="",n.addEventListener(cs,n._moveCb=function t(r){r&&!/transform$/.test(r.propertyName)||(n.removeEventListener(cs,t),n._moveCb=null,Wn(n,e))})}})}},methods:{hasMove:function(t,e){if(!is)return!1;if(null!=this._hasMove)return this._hasMove;var n=t.cloneNode();t._transitionClasses&&t._transitionClasses.forEach(function(t){Jn(n,t)}),qn(n,e),n.style.display="none",this.$el.appendChild(n);var r=Xn(n);return this.$el.removeChild(n),this._hasMove=r.hasTransform}}},Os={Transition:Es,TransitionGroup:$s};he.config.mustUseProp=$a,he.config.isReservedTag=Ma,he.config.getTagNamespace=Se,he.config.isUnknownElement=je,p(he.options.directives,bs),p(he.options.components,Os),he.prototype.__patch__=ri?ys:m,he.prototype.$mount=function(t,e){return t=t&&ri?Ve(t):void 0,vt(this,t,e)},setTimeout(function(){ei.devtools&&(pi?pi.emit("init",he):"production"!==e.env.NODE_ENV&&li&&console[console.info?"info":"log"]("Download the Vue Devtools extension for a better development experience:\nhttps://github.com/vuejs/vue-devtools")),"production"!==e.env.NODE_ENV&&ei.productionTip!==!1&&ri&&"undefined"!=typeof console&&console[console.info?"info":"log"]("You are running Vue in development mode.\nMake sure to turn on production mode when deploying for production.\nSee more tips at https://vuejs.org/guide/deployment.html")},0);var Ns,ks=!!ri&&wr("\n","&#10;"),Cs=i("area,base,br,col,embed,frame,hr,img,input,isindex,keygen,link,meta,param,source,track,wbr",!0),As=i("colgroup,dd,dt,li,options,p,td,tfoot,th,thead,tr,source",!0),Ts=i("address,article,aside,base,blockquote,body,caption,col,colgroup,dd,details,dialog,div,dl,dt,fieldset,figcaption,figure,footer,form,h1,h2,h3,h4,h5,h6,head,header,hgroup,hr,html,legend,li,menuitem,meta,optgroup,option,param,rp,rt,source,style,summary,tbody,td,tfoot,th,thead,title,tr,track",!0),Ds=/([^\s"'<>\/=]+)/,Ss=/(?:=)/,js=[/"([^"]*)"+/.source,/'([^']*)'+/.source,/([^\s"'=<>`]+)/.source],Vs=new RegExp("^\\s*"+Ds.source+"(?:\\s*("+Ss.source+")\\s*(?:"+js.join("|")+"))?"),Ms="[a-zA-Z_][\\w\\-\\.]*",Ps="((?:"+Ms+"\\:)?"+Ms+")",Is=new RegExp("^<"+Ps),Ls=/^\s*(\/?)>/,Rs=new RegExp("^<\\/"+Ps+"[^>]*>"),Us=/^<!DOCTYPE [^>]+>/i,Fs=/^<!--/,Hs=/^<!\[/,Bs=!1;"x".replace(/x(.)?/g,function(t,e){Bs=""===e});var qs,Js,zs,Gs,Ks,Ws,Zs,Xs,Ys,Qs,tu,eu,nu,ru,ou,iu,au,su,uu=i("script,style",!0),cu={},lu={"&lt;":"<","&gt;":">","&quot;":'"',"&amp;":"&","&#10;":"\n"},fu=/&(?:lt|gt|quot|amp);/g,pu=/&(?:lt|gt|quot|amp|#10);/g,du=/\{\{((?:.|\n)+?)\}\}/g,vu=/[-.*+?^${}()|[\]\/\\]/g,hu=c(function(t){var e=t[0].replace(vu,"\\$&"),n=t[1].replace(vu,"\\$&");return new RegExp(e+"((?:.|\\n)+?)"+n,"g")}),mu=/^v-|^@|^:/,yu=/(.*?)\s+(?:in|of)\s+(.*)/,gu=/\((\{[^}]*\}|[^,]*),([^,]*)(?:,([^,]*))?\)/,_u=/^:|^v-bind:/,bu=/^@|^v-on:/,wu=/:(.*)$/,Eu=/\.[^.]+/g,xu=c(Er),$u=/^xmlns:NS\d+/,Ou=/^NS\d+:/,Nu=c(Gr),ku=/^\s*([\w$_]+|\([^)]*?\))\s*=>|^function\s*\(/,Cu=/^\s*[A-Za-z_$][\w$]*(?:\.[A-Za-z_$][\w$]*|\['.*?']|\[".*?"]|\[\d+]|\[[A-Za-z_$][\w$]*])*\s*$/,Au={esc:27,tab:9,enter:13,space:32,up:38,left:37,right:39,down:40,delete:[8,46]},Tu=function(t){return"if("+t+")return null;"},Du={stop:"$event.stopPropagation();",prevent:"$event.preventDefault();",self:Tu("$event.target !== $event.currentTarget"),ctrl:Tu("!$event.ctrlKey"),shift:Tu("!$event.shiftKey"),alt:Tu("!$event.altKey"),meta:Tu("!$event.metaKey"),left:Tu("$event.button !== 0"),middle:Tu("$event.button !== 1"),right:Tu("$event.button !== 2")},Su={bind:ro,cloak:m},ju=new RegExp("\\b"+"do,if,for,let,new,try,var,case,else,with,await,break,catch,class,const,super,throw,while,yield,delete,export,import,return,switch,default,extends,finally,continue,debugger,function,arguments".split(",").join("\\b|\\b")+"\\b"),Vu=/[A-Za-z_$][\w$]*/,Mu=/'(?:[^'\\]|\\.)*'|"(?:[^"\\]|\\.)*"|`(?:[^`\\]|\\.)*\$\{|\}(?:[^`\\]|\\.)*`|`(?:[^`\\]|\\.)*`/g,Pu={staticKeys:["staticClass"],transformNode:Mo,genData:Po},Iu={staticKeys:["staticStyle"],transformNode:Io,genData:Lo},Lu=[Pu,Iu],Ru={model:Nn,text:Ro,html:Uo},Uu={expectHTML:!0,modules:Lu,directives:Ru,isPreTag:Va,isUnaryTag:Cs,mustUseProp:$a,isReservedTag:Ma,getTagNamespace:Se,staticKeys:y(Lu)},Fu=Vo(Uu),Hu=Fu.compileToFunctions,Bu=c(function(t){var e=Ve(t);return e&&e.innerHTML}),qu=he.prototype.$mount;he.prototype.$mount=function(t,n){if((t=t&&Ve(t))===document.body||t===document.documentElement)return"production"!==e.env.NODE_ENV&&_i("Do not mount Vue to <html> or <body> - mount to normal elements instead."),this;var r=this.$options;if(!r.render){var o=r.template;if(o)if("string"==typeof o)"#"===o.charAt(0)&&(o=Bu(o),"production"===e.env.NODE_ENV||o||_i("Template element not found or is empty: "+r.template,this));else{if(!o.nodeType)return"production"!==e.env.NODE_ENV&&_i("invalid template option:"+o,this),this;o=o.innerHTML}else t&&(o=Fo(t));if(o){"production"!==e.env.NODE_ENV&&ei.performance&&hi&&hi.mark("compile");var i=Hu(o,{shouldDecodeNewlines:ks,delimiters:r.delimiters},this),a=i.render,s=i.staticRenderFns;r.render=a,r.staticRenderFns=s,"production"!==e.env.NODE_ENV&&ei.performance&&hi&&(hi.mark("compile end"),hi.measure(this._name+" compile","compile","compile end"))}}return qu.call(this,t,n)},he.compile=Hu,t.exports=he}).call(e,n(5),n(4))},83:function(t,e){t.exports=function(t,e,n,r){var o,i=t=t||{},a=typeof t.default;"object"!==a&&"function"!==a||(o=t,i=t.default);var s="function"==typeof i?i.options:i;if(e&&(s.render=e.render,s.staticRenderFns=e.staticRenderFns),n&&(s._scopeId=n),r){var u=Object.create(s.computed||null);Object.keys(r).forEach(function(t){var e=r[t];u[t]=function(){return e}}),s.computed=u}return{esModule:o,exports:i,options:s}}},84:function(t,e,n){t.exports={render:function(){var t=this,e=t.$createElement,n=t._self._c||e;return n("div",{attrs:{id:"app"}},[n("ul",{staticClass:"products-list"},t._l(t.products,function(e){return n("li",[t._v("\n     "+t._s(e.product_name)+"\n   ")])}))])},staticRenderFns:[]},t.exports.render._withStripped=!0},85:function(t,e,n){"use strict";function r(t){O&&(t._devtoolHook=O,O.emit("vuex:init",t),O.on("vuex:travel-to-state",function(e){t.replaceState(e)}),t.subscribe(function(t,e){O.emit("vuex:mutation",t,e)}))}function o(t,e){Object.keys(t).forEach(function(n){return e(t[n],n)})}function i(t){return null!==t&&"object"==typeof t}function a(t){return t&&"function"==typeof t.then}function s(t,e){if(!t)throw new Error("[vuex] "+e)}function u(t,e){if(t.update(e),e.modules)for(var n in e.modules){if(!t.getChild(n))return void console.warn("[vuex] trying to add a new module '"+n+"' on hot reloading, manual reload is needed");u(t.getChild(n),e.modules[n])}}function c(t,e){t._actions=Object.create(null),t._mutations=Object.create(null),t._wrappedGetters=Object.create(null),t._modulesNamespaceMap=Object.create(null);var n=t.state;f(t,n,[],t._modules.root,!0),l(t,n,e)}function l(t,e,n){var r=t._vm;t.getters={};var i=t._wrappedGetters,a={};o(i,function(e,n){a[n]=function(){return e(t)},Object.defineProperty(t.getters,n,{get:function(){return t._vm[n]},enumerable:!0})});var s=A.config.silent;A.config.silent=!0,t._vm=new A({data:{$$state:e},computed:a}),A.config.silent=s,t.strict&&y(t),r&&(n&&t._withCommit(function(){r._data.$$state=null}),A.nextTick(function(){return r.$destroy()}))}function f(t,e,n,r,o){var i=!n.length,a=t._modules.getNamespace(n);if(a&&(t._modulesNamespaceMap[a]=r),!i&&!o){var s=g(e,n.slice(0,-1)),u=n[n.length-1];t._withCommit(function(){A.set(s,u,r.state)})}var c=r.context=p(t,a,n);r.forEachMutation(function(e,n){v(t,a+n,e,c)}),r.forEachAction(function(e,n){h(t,a+n,e,c)}),r.forEachGetter(function(e,n){m(t,a+n,e,c)}),r.forEachChild(function(r,i){f(t,e,n.concat(i),r,o)})}function p(t,e,n){var r=""===e,o={dispatch:r?t.dispatch:function(n,r,o){var i=_(n,r,o),a=i.payload,s=i.options,u=i.type;return s&&s.root||(u=e+u,t._actions[u])?t.dispatch(u,a):void console.error("[vuex] unknown local action type: "+i.type+", global type: "+u)},commit:r?t.commit:function(n,r,o){var i=_(n,r,o),a=i.payload,s=i.options,u=i.type;if(!(s&&s.root||(u=e+u,t._mutations[u])))return void console.error("[vuex] unknown local mutation type: "+i.type+", global type: "+u);t.commit(u,a,s)}};return Object.defineProperties(o,{getters:{get:r?function(){return t.getters}:function(){return d(t,e)}},state:{get:function(){return g(t.state,n)}}}),o}function d(t,e){var n={},r=e.length;return Object.keys(t.getters).forEach(function(o){if(o.slice(0,r)===e){var i=o.slice(r);Object.defineProperty(n,i,{get:function(){return t.getters[o]},enumerable:!0})}}),n}function v(t,e,n,r){(t._mutations[e]||(t._mutations[e]=[])).push(function(t){n(r.state,t)})}function h(t,e,n,r){(t._actions[e]||(t._actions[e]=[])).push(function(e,o){var i=n({dispatch:r.dispatch,commit:r.commit,getters:r.getters,state:r.state,rootGetters:t.getters,rootState:t.state},e,o);return a(i)||(i=Promise.resolve(i)),t._devtoolHook?i.catch(function(e){throw t._devtoolHook.emit("vuex:error",e),e}):i})}function m(t,e,n,r){if(t._wrappedGetters[e])return void console.error("[vuex] duplicate getter key: "+e);t._wrappedGetters[e]=function(t){return n(r.state,r.getters,t.state,t.getters)}}function y(t){t._vm.$watch(function(){return this._data.$$state},function(){s(t._committing,"Do not mutate vuex store state outside mutation handlers.")},{deep:!0,sync:!0})}function g(t,e){return e.length?e.reduce(function(t,e){return t[e]},t):t}function _(t,e,n){return i(t)&&t.type&&(n=e,e=t,t=t.type),s("string"==typeof t,"Expects string as the type, but found "+typeof t+"."),{type:t,payload:e,options:n}}function b(t){if(A)return void console.error("[vuex] already installed. Vue.use(Vuex) should be called only once.");A=t,$(A)}function w(t){return Array.isArray(t)?t.map(function(t){return{key:t,val:t}}):Object.keys(t).map(function(e){return{key:e,val:t[e]}})}function E(t){return function(e,n){return"string"!=typeof e?(n=e,e=""):"/"!==e.charAt(e.length-1)&&(e+="/"),t(e,n)}}function x(t,e,n){var r=t._modulesNamespaceMap[n];return r||console.error("[vuex] module namespace not found in "+e+"(): "+n),r}Object.defineProperty(e,"__esModule",{value:!0}),n.d(e,"Store",function(){return T}),n.d(e,"mapState",function(){return S}),n.d(e,"mapMutations",function(){return j}),n.d(e,"mapGetters",function(){return V}),n.d(e,"mapActions",function(){return M});var $=function(t){function e(){var t=this.$options;t.store?this.$store=t.store:t.parent&&t.parent.$store&&(this.$store=t.parent.$store)}if(Number(t.version.split(".")[0])>=2){var n=t.config._lifecycleHooks.indexOf("init")>-1;t.mixin(n?{init:e}:{beforeCreate:e})}else{var r=t.prototype._init;t.prototype._init=function(t){void 0===t&&(t={}),t.init=t.init?[e].concat(t.init):e,r.call(this,t)}}},O="undefined"!=typeof window&&window.__VUE_DEVTOOLS_GLOBAL_HOOK__,N=function(t,e){this.runtime=e,this._children=Object.create(null),this._rawModule=t},k={state:{},namespaced:{}};k.state.get=function(){return this._rawModule.state||{}},k.namespaced.get=function(){return!!this._rawModule.namespaced},N.prototype.addChild=function(t,e){this._children[t]=e},N.prototype.removeChild=function(t){delete this._children[t]},N.prototype.getChild=function(t){return this._children[t]},N.prototype.update=function(t){this._rawModule.namespaced=t.namespaced,t.actions&&(this._rawModule.actions=t.actions),t.mutations&&(this._rawModule.mutations=t.mutations),t.getters&&(this._rawModule.getters=t.getters)},N.prototype.forEachChild=function(t){o(this._children,t)},N.prototype.forEachGetter=function(t){this._rawModule.getters&&o(this._rawModule.getters,t)},N.prototype.forEachAction=function(t){this._rawModule.actions&&o(this._rawModule.actions,t)},N.prototype.forEachMutation=function(t){this._rawModule.mutations&&o(this._rawModule.mutations,t)},Object.defineProperties(N.prototype,k);var C=function(t){var e=this;this.root=new N(t,!1),t.modules&&o(t.modules,function(t,n){e.register([n],t,!1)})};C.prototype.get=function(t){return t.reduce(function(t,e){return t.getChild(e)},this.root)},C.prototype.getNamespace=function(t){var e=this.root;return t.reduce(function(t,n){return e=e.getChild(n),t+(e.namespaced?n+"/":"")},"")},C.prototype.update=function(t){u(this.root,t)},C.prototype.register=function(t,e,n){var r=this;void 0===n&&(n=!0);var i=this.get(t.slice(0,-1)),a=new N(e,n);i.addChild(t[t.length-1],a),e.modules&&o(e.modules,function(e,o){r.register(t.concat(o),e,n)})},C.prototype.unregister=function(t){var e=this.get(t.slice(0,-1)),n=t[t.length-1];e.getChild(n).runtime&&e.removeChild(n)};var A,T=function(t){var e=this;void 0===t&&(t={}),s(A,"must call Vue.use(Vuex) before creating a store instance."),s("undefined"!=typeof Promise,"vuex requires a Promise polyfill in this browser.");var n=t.state;void 0===n&&(n={});var o=t.plugins;void 0===o&&(o=[]);var i=t.strict;void 0===i&&(i=!1),this._committing=!1,this._actions=Object.create(null),this._mutations=Object.create(null),this._wrappedGetters=Object.create(null),this._modules=new C(t),this._modulesNamespaceMap=Object.create(null),this._subscribers=[],this._watcherVM=new A;var a=this,u=this,c=u.dispatch,p=u.commit;this.dispatch=function(t,e){return c.call(a,t,e)},this.commit=function(t,e,n){return p.call(a,t,e,n)},this.strict=i,f(this,n,[],this._modules.root),l(this,n),o.concat(r).forEach(function(t){return t(e)})},D={state:{}};D.state.get=function(){return this._vm._data.$$state},D.state.set=function(t){s(!1,"Use store.replaceState() to explicit replace store state.")},T.prototype.commit=function(t,e,n){var r=this,o=_(t,e,n),i=o.type,a=o.payload,s=o.options,u={type:i,payload:a},c=this._mutations[i];if(!c)return void console.error("[vuex] unknown mutation type: "+i);this._withCommit(function(){c.forEach(function(t){t(a)})}),this._subscribers.forEach(function(t){return t(u,r.state)}),s&&s.silent&&console.warn("[vuex] mutation type: "+i+". Silent option has been removed. Use the filter functionality in the vue-devtools")},T.prototype.dispatch=function(t,e){var n=_(t,e),r=n.type,o=n.payload,i=this._actions[r];return i?i.length>1?Promise.all(i.map(function(t){return t(o)})):i[0](o):void console.error("[vuex] unknown action type: "+r)},T.prototype.subscribe=function(t){var e=this._subscribers;return e.indexOf(t)<0&&e.push(t),function(){var n=e.indexOf(t);n>-1&&e.splice(n,1)}},T.prototype.watch=function(t,e,n){var r=this;return s("function"==typeof t,"store.watch only accepts a function."),this._watcherVM.$watch(function(){return t(r.state,r.getters)},e,n)},T.prototype.replaceState=function(t){var e=this;this._withCommit(function(){e._vm._data.$$state=t})},T.prototype.registerModule=function(t,e){"string"==typeof t&&(t=[t]),s(Array.isArray(t),"module path must be a string or an Array."),this._modules.register(t,e),f(this,this.state,t,this._modules.get(t)),l(this,this.state)},T.prototype.unregisterModule=function(t){var e=this;"string"==typeof t&&(t=[t]),s(Array.isArray(t),"module path must be a string or an Array."),this._modules.unregister(t),this._withCommit(function(){var n=g(e.state,t.slice(0,-1));A.delete(n,t[t.length-1])}),c(this)},T.prototype.hotUpdate=function(t){this._modules.update(t),c(this,!0)},T.prototype._withCommit=function(t){var e=this._committing;this._committing=!0,t(),this._committing=e},Object.defineProperties(T.prototype,D),"undefined"!=typeof window&&window.Vue&&b(window.Vue);var S=E(function(t,e){var n={};return w(e).forEach(function(e){var r=e.key,o=e.val;n[r]=function(){var e=this.$store.state,n=this.$store.getters;if(t){var r=x(this.$store,"mapState",t);if(!r)return;e=r.context.state,n=r.context.getters}return"function"==typeof o?o.call(this,e,n):e[o]},n[r].vuex=!0}),n}),j=E(function(t,e){var n={};return w(e).forEach(function(e){var r=e.key,o=e.val;o=t+o,n[r]=function(){for(var e=[],n=arguments.length;n--;)e[n]=arguments[n];if(!t||x(this.$store,"mapMutations",t))return this.$store.commit.apply(this.$store,[o].concat(e))}}),n}),V=E(function(t,e){var n={};return w(e).forEach(function(e){var r=e.key,o=e.val;o=t+o,n[r]=function(){if(!t||x(this.$store,"mapGetters",t))return o in this.$store.getters?this.$store.getters[o]:void console.error("[vuex] unknown getter: "+o)},n[r].vuex=!0}),n}),M=E(function(t,e){var n={};return w(e).forEach(function(e){var r=e.key,o=e.val;o=t+o,n[r]=function(){for(var e=[],n=arguments.length;n--;)e[n]=arguments[n];if(!t||x(this.$store,"mapActions",t))return this.$store.dispatch.apply(this.$store,[o].concat(e))}}),n}),P={Store:T,install:b,version:"2.2.1",mapState:S,mapMutations:j,mapGetters:V,mapActions:M};e.default=P},86:function(t,e){},87:function(t,e,n){"use strict";function r(t){return t&&t.__esModule?t:{default:t}}var o=n(6),i=r(o),a=n(28),s=r(a),u=n(17),c=r(u),l=n(15),f=r(l);i.default.use(s.default);new i.default({store:f.default,el:"#stock-app",template:"<app/>",components:{app:c.default},methods:{getStock:function(){this.$http.get(data.apiUrl).then(function(t){200==t.status&&this.$store.commit("addProducts",t.body)},function(t){console.log(t.statusText)})}},mounted:function(){this.getStock()}})}});
=======
/******/ (function(modules) { // webpackBootstrap
/******/ 	// The module cache
/******/ 	var installedModules = {};

/******/ 	// The require function
/******/ 	function __webpack_require__(moduleId) {

/******/ 		// Check if module is in cache
/******/ 		if(installedModules[moduleId])
/******/ 			return installedModules[moduleId].exports;

/******/ 		// Create a new module (and put it into the cache)
/******/ 		var module = installedModules[moduleId] = {
/******/ 			i: moduleId,
/******/ 			l: false,
/******/ 			exports: {}
/******/ 		};

/******/ 		// Execute the module function
/******/ 		modules[moduleId].call(module.exports, module, module.exports, __webpack_require__);

/******/ 		// Flag the module as loaded
/******/ 		module.l = true;

/******/ 		// Return the exports of the module
/******/ 		return module.exports;
/******/ 	}


/******/ 	// expose the modules object (__webpack_modules__)
/******/ 	__webpack_require__.m = modules;

/******/ 	// expose the module cache
/******/ 	__webpack_require__.c = installedModules;

/******/ 	// identity function for calling harmony imports with the correct context
/******/ 	__webpack_require__.i = function(value) { return value; };

/******/ 	// define getter function for harmony exports
/******/ 	__webpack_require__.d = function(exports, name, getter) {
/******/ 		if(!__webpack_require__.o(exports, name)) {
/******/ 			Object.defineProperty(exports, name, {
/******/ 				configurable: false,
/******/ 				enumerable: true,
/******/ 				get: getter
/******/ 			});
/******/ 		}
/******/ 	};

/******/ 	// getDefaultExport function for compatibility with non-harmony modules
/******/ 	__webpack_require__.n = function(module) {
/******/ 		var getter = module && module.__esModule ?
/******/ 			function getDefault() { return module['default']; } :
/******/ 			function getModuleExports() { return module; };
/******/ 		__webpack_require__.d(getter, 'a', getter);
/******/ 		return getter;
/******/ 	};

/******/ 	// Object.prototype.hasOwnProperty.call
/******/ 	__webpack_require__.o = function(object, property) { return Object.prototype.hasOwnProperty.call(object, property); };

/******/ 	// __webpack_public_path__
/******/ 	__webpack_require__.p = "";

/******/ 	// Load entry module and return exports
/******/ 	return __webpack_require__(__webpack_require__.s = 112);
/******/ })
/************************************************************************/
/******/ ([
/* 0 */,
/* 1 */
/***/ (function(module, exports) {

module.exports = function normalizeComponent (
  rawScriptExports,
  compiledTemplate,
  scopeId,
  cssModules
) {
  var esModule
  var scriptExports = rawScriptExports = rawScriptExports || {}

  // ES6 modules interop
  var type = typeof rawScriptExports.default
  if (type === 'object' || type === 'function') {
    esModule = rawScriptExports
    scriptExports = rawScriptExports.default
  }

  // Vue.extend constructor export interop
  var options = typeof scriptExports === 'function'
    ? scriptExports.options
    : scriptExports

  // render functions
  if (compiledTemplate) {
    options.render = compiledTemplate.render
    options.staticRenderFns = compiledTemplate.staticRenderFns
  }

  // scopedId
  if (scopeId) {
    options._scopeId = scopeId
  }

  // inject cssModules
  if (cssModules) {
    var computed = Object.create(options.computed || null)
    Object.keys(cssModules).forEach(function (key) {
      var module = cssModules[key]
      computed[key] = function () { return module }
    })
    options.computed = computed
  }

  return {
    esModule: esModule,
    exports: scriptExports,
    options: options
  }
}


/***/ }),
/* 2 */,
/* 3 */,
/* 4 */,
/* 5 */
/***/ (function(module, exports) {

var g;

// This works in non-strict mode
g = (function() {
	return this;
})();

try {
	// This works if eval is allowed (see CSP)
	g = g || Function("return this")() || (1,eval)("this");
} catch(e) {
	// This works if the window reference is available
	if(typeof window === "object")
		g = window;
}

// g can still be undefined, but nothing to do about it...
// We return undefined, instead of nothing here, so it's
// easier to handle this case. if(!global) { ...}

module.exports = g;


/***/ }),
/* 6 */
/***/ (function(module, exports) {

// shim for using process in browser
var process = module.exports = {};

// cached from whatever global is present so that test runners that stub it
// don't break things.  But we need to wrap it in a try catch in case it is
// wrapped in strict mode code which doesn't define any globals.  It's inside a
// function because try/catches deoptimize in certain engines.

var cachedSetTimeout;
var cachedClearTimeout;

function defaultSetTimout() {
    throw new Error('setTimeout has not been defined');
}
function defaultClearTimeout () {
    throw new Error('clearTimeout has not been defined');
}
(function () {
    try {
        if (typeof setTimeout === 'function') {
            cachedSetTimeout = setTimeout;
        } else {
            cachedSetTimeout = defaultSetTimout;
        }
    } catch (e) {
        cachedSetTimeout = defaultSetTimout;
    }
    try {
        if (typeof clearTimeout === 'function') {
            cachedClearTimeout = clearTimeout;
        } else {
            cachedClearTimeout = defaultClearTimeout;
        }
    } catch (e) {
        cachedClearTimeout = defaultClearTimeout;
    }
} ())
function runTimeout(fun) {
    if (cachedSetTimeout === setTimeout) {
        //normal enviroments in sane situations
        return setTimeout(fun, 0);
    }
    // if setTimeout wasn't available but was latter defined
    if ((cachedSetTimeout === defaultSetTimout || !cachedSetTimeout) && setTimeout) {
        cachedSetTimeout = setTimeout;
        return setTimeout(fun, 0);
    }
    try {
        // when when somebody has screwed with setTimeout but no I.E. maddness
        return cachedSetTimeout(fun, 0);
    } catch(e){
        try {
            // When we are in I.E. but the script has been evaled so I.E. doesn't trust the global object when called normally
            return cachedSetTimeout.call(null, fun, 0);
        } catch(e){
            // same as above but when it's a version of I.E. that must have the global object for 'this', hopfully our context correct otherwise it will throw a global error
            return cachedSetTimeout.call(this, fun, 0);
        }
    }


}
function runClearTimeout(marker) {
    if (cachedClearTimeout === clearTimeout) {
        //normal enviroments in sane situations
        return clearTimeout(marker);
    }
    // if clearTimeout wasn't available but was latter defined
    if ((cachedClearTimeout === defaultClearTimeout || !cachedClearTimeout) && clearTimeout) {
        cachedClearTimeout = clearTimeout;
        return clearTimeout(marker);
    }
    try {
        // when when somebody has screwed with setTimeout but no I.E. maddness
        return cachedClearTimeout(marker);
    } catch (e){
        try {
            // When we are in I.E. but the script has been evaled so I.E. doesn't  trust the global object when called normally
            return cachedClearTimeout.call(null, marker);
        } catch (e){
            // same as above but when it's a version of I.E. that must have the global object for 'this', hopfully our context correct otherwise it will throw a global error.
            // Some versions of I.E. have different rules for clearTimeout vs setTimeout
            return cachedClearTimeout.call(this, marker);
        }
    }



}
var queue = [];
var draining = false;
var currentQueue;
var queueIndex = -1;

function cleanUpNextTick() {
    if (!draining || !currentQueue) {
        return;
    }
    draining = false;
    if (currentQueue.length) {
        queue = currentQueue.concat(queue);
    } else {
        queueIndex = -1;
    }
    if (queue.length) {
        drainQueue();
    }
}

function drainQueue() {
    if (draining) {
        return;
    }
    var timeout = runTimeout(cleanUpNextTick);
    draining = true;

    var len = queue.length;
    while(len) {
        currentQueue = queue;
        queue = [];
        while (++queueIndex < len) {
            if (currentQueue) {
                currentQueue[queueIndex].run();
            }
        }
        queueIndex = -1;
        len = queue.length;
    }
    currentQueue = null;
    draining = false;
    runClearTimeout(timeout);
}

process.nextTick = function (fun) {
    var args = new Array(arguments.length - 1);
    if (arguments.length > 1) {
        for (var i = 1; i < arguments.length; i++) {
            args[i - 1] = arguments[i];
        }
    }
    queue.push(new Item(fun, args));
    if (queue.length === 1 && !draining) {
        runTimeout(drainQueue);
    }
};

// v8 likes predictible objects
function Item(fun, array) {
    this.fun = fun;
    this.array = array;
}
Item.prototype.run = function () {
    this.fun.apply(null, this.array);
};
process.title = 'browser';
process.browser = true;
process.env = {};
process.argv = [];
process.version = ''; // empty string to avoid regexp issues
process.versions = {};

function noop() {}

process.on = noop;
process.addListener = noop;
process.once = noop;
process.off = noop;
process.removeListener = noop;
process.removeAllListeners = noop;
process.emit = noop;

process.binding = function (name) {
    throw new Error('process.binding is not supported');
};

process.cwd = function () { return '/' };
process.chdir = function (dir) {
    throw new Error('process.chdir is not supported');
};
process.umask = function() { return 0; };


/***/ }),
/* 7 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";
/* WEBPACK VAR INJECTION */(function(process, global) {/*!
 * Vue.js v2.2.1
 * (c) 2014-2017 Evan You
 * Released under the MIT License.
 */


/*  */

/**
 * Convert a value to a string that is actually rendered.
 */
function _toString (val) {
  return val == null
    ? ''
    : typeof val === 'object'
      ? JSON.stringify(val, null, 2)
      : String(val)
}

/**
 * Convert a input value to a number for persistence.
 * If the conversion fails, return original string.
 */
function toNumber (val) {
  var n = parseFloat(val);
  return isNaN(n) ? val : n
}

/**
 * Make a map and return a function for checking if a key
 * is in that map.
 */
function makeMap (
  str,
  expectsLowerCase
) {
  var map = Object.create(null);
  var list = str.split(',');
  for (var i = 0; i < list.length; i++) {
    map[list[i]] = true;
  }
  return expectsLowerCase
    ? function (val) { return map[val.toLowerCase()]; }
    : function (val) { return map[val]; }
}

/**
 * Check if a tag is a built-in tag.
 */
var isBuiltInTag = makeMap('slot,component', true);

/**
 * Remove an item from an array
 */
function remove (arr, item) {
  if (arr.length) {
    var index = arr.indexOf(item);
    if (index > -1) {
      return arr.splice(index, 1)
    }
  }
}

/**
 * Check whether the object has the property.
 */
var hasOwnProperty = Object.prototype.hasOwnProperty;
function hasOwn (obj, key) {
  return hasOwnProperty.call(obj, key)
}

/**
 * Check if value is primitive
 */
function isPrimitive (value) {
  return typeof value === 'string' || typeof value === 'number'
}

/**
 * Create a cached version of a pure function.
 */
function cached (fn) {
  var cache = Object.create(null);
  return (function cachedFn (str) {
    var hit = cache[str];
    return hit || (cache[str] = fn(str))
  })
}

/**
 * Camelize a hyphen-delimited string.
 */
var camelizeRE = /-(\w)/g;
var camelize = cached(function (str) {
  return str.replace(camelizeRE, function (_, c) { return c ? c.toUpperCase() : ''; })
});

/**
 * Capitalize a string.
 */
var capitalize = cached(function (str) {
  return str.charAt(0).toUpperCase() + str.slice(1)
});

/**
 * Hyphenate a camelCase string.
 */
var hyphenateRE = /([^-])([A-Z])/g;
var hyphenate = cached(function (str) {
  return str
    .replace(hyphenateRE, '$1-$2')
    .replace(hyphenateRE, '$1-$2')
    .toLowerCase()
});

/**
 * Simple bind, faster than native
 */
function bind (fn, ctx) {
  function boundFn (a) {
    var l = arguments.length;
    return l
      ? l > 1
        ? fn.apply(ctx, arguments)
        : fn.call(ctx, a)
      : fn.call(ctx)
  }
  // record original fn length
  boundFn._length = fn.length;
  return boundFn
}

/**
 * Convert an Array-like object to a real Array.
 */
function toArray (list, start) {
  start = start || 0;
  var i = list.length - start;
  var ret = new Array(i);
  while (i--) {
    ret[i] = list[i + start];
  }
  return ret
}

/**
 * Mix properties into target object.
 */
function extend (to, _from) {
  for (var key in _from) {
    to[key] = _from[key];
  }
  return to
}

/**
 * Quick object check - this is primarily used to tell
 * Objects from primitive values when we know the value
 * is a JSON-compliant type.
 */
function isObject (obj) {
  return obj !== null && typeof obj === 'object'
}

/**
 * Strict object type check. Only returns true
 * for plain JavaScript objects.
 */
var toString = Object.prototype.toString;
var OBJECT_STRING = '[object Object]';
function isPlainObject (obj) {
  return toString.call(obj) === OBJECT_STRING
}

/**
 * Merge an Array of Objects into a single Object.
 */
function toObject (arr) {
  var res = {};
  for (var i = 0; i < arr.length; i++) {
    if (arr[i]) {
      extend(res, arr[i]);
    }
  }
  return res
}

/**
 * Perform no operation.
 */
function noop () {}

/**
 * Always return false.
 */
var no = function () { return false; };

/**
 * Return same value
 */
var identity = function (_) { return _; };

/**
 * Generate a static keys string from compiler modules.
 */
function genStaticKeys (modules) {
  return modules.reduce(function (keys, m) {
    return keys.concat(m.staticKeys || [])
  }, []).join(',')
}

/**
 * Check if two values are loosely equal - that is,
 * if they are plain objects, do they have the same shape?
 */
function looseEqual (a, b) {
  var isObjectA = isObject(a);
  var isObjectB = isObject(b);
  if (isObjectA && isObjectB) {
    return JSON.stringify(a) === JSON.stringify(b)
  } else if (!isObjectA && !isObjectB) {
    return String(a) === String(b)
  } else {
    return false
  }
}

function looseIndexOf (arr, val) {
  for (var i = 0; i < arr.length; i++) {
    if (looseEqual(arr[i], val)) { return i }
  }
  return -1
}

/**
 * Ensure a function is called only once.
 */
function once (fn) {
  var called = false;
  return function () {
    if (!called) {
      called = true;
      fn();
    }
  }
}

/*  */

var config = {
  /**
   * Option merge strategies (used in core/util/options)
   */
  optionMergeStrategies: Object.create(null),

  /**
   * Whether to suppress warnings.
   */
  silent: false,

  /**
   * Show production mode tip message on boot?
   */
  productionTip: process.env.NODE_ENV !== 'production',

  /**
   * Whether to enable devtools
   */
  devtools: process.env.NODE_ENV !== 'production',

  /**
   * Whether to record perf
   */
  performance: process.env.NODE_ENV !== 'production',

  /**
   * Error handler for watcher errors
   */
  errorHandler: null,

  /**
   * Ignore certain custom elements
   */
  ignoredElements: [],

  /**
   * Custom user key aliases for v-on
   */
  keyCodes: Object.create(null),

  /**
   * Check if a tag is reserved so that it cannot be registered as a
   * component. This is platform-dependent and may be overwritten.
   */
  isReservedTag: no,

  /**
   * Check if a tag is an unknown element.
   * Platform-dependent.
   */
  isUnknownElement: no,

  /**
   * Get the namespace of an element
   */
  getTagNamespace: noop,

  /**
   * Parse the real tag name for the specific platform.
   */
  parsePlatformTagName: identity,

  /**
   * Check if an attribute must be bound using property, e.g. value
   * Platform-dependent.
   */
  mustUseProp: no,

  /**
   * List of asset types that a component can own.
   */
  _assetTypes: [
    'component',
    'directive',
    'filter'
  ],

  /**
   * List of lifecycle hooks.
   */
  _lifecycleHooks: [
    'beforeCreate',
    'created',
    'beforeMount',
    'mounted',
    'beforeUpdate',
    'updated',
    'beforeDestroy',
    'destroyed',
    'activated',
    'deactivated'
  ],

  /**
   * Max circular updates allowed in a scheduler flush cycle.
   */
  _maxUpdateCount: 100
};

/*  */
/* globals MutationObserver */

// can we use __proto__?
var hasProto = '__proto__' in {};

// Browser environment sniffing
var inBrowser = typeof window !== 'undefined';
var UA = inBrowser && window.navigator.userAgent.toLowerCase();
var isIE = UA && /msie|trident/.test(UA);
var isIE9 = UA && UA.indexOf('msie 9.0') > 0;
var isEdge = UA && UA.indexOf('edge/') > 0;
var isAndroid = UA && UA.indexOf('android') > 0;
var isIOS = UA && /iphone|ipad|ipod|ios/.test(UA);
var isChrome = UA && /chrome\/\d+/.test(UA) && !isEdge;

// this needs to be lazy-evaled because vue may be required before
// vue-server-renderer can set VUE_ENV
var _isServer;
var isServerRendering = function () {
  if (_isServer === undefined) {
    /* istanbul ignore if */
    if (!inBrowser && typeof global !== 'undefined') {
      // detect presence of vue-server-renderer and avoid
      // Webpack shimming the process
      _isServer = global['process'].env.VUE_ENV === 'server';
    } else {
      _isServer = false;
    }
  }
  return _isServer
};

// detect devtools
var devtools = inBrowser && window.__VUE_DEVTOOLS_GLOBAL_HOOK__;

/* istanbul ignore next */
function isNative (Ctor) {
  return /native code/.test(Ctor.toString())
}

var hasSymbol =
  typeof Symbol !== 'undefined' && isNative(Symbol) &&
  typeof Reflect !== 'undefined' && isNative(Reflect.ownKeys);

/**
 * Defer a task to execute it asynchronously.
 */
var nextTick = (function () {
  var callbacks = [];
  var pending = false;
  var timerFunc;

  function nextTickHandler () {
    pending = false;
    var copies = callbacks.slice(0);
    callbacks.length = 0;
    for (var i = 0; i < copies.length; i++) {
      copies[i]();
    }
  }

  // the nextTick behavior leverages the microtask queue, which can be accessed
  // via either native Promise.then or MutationObserver.
  // MutationObserver has wider support, however it is seriously bugged in
  // UIWebView in iOS >= 9.3.3 when triggered in touch event handlers. It
  // completely stops working after triggering a few times... so, if native
  // Promise is available, we will use it:
  /* istanbul ignore if */
  if (typeof Promise !== 'undefined' && isNative(Promise)) {
    var p = Promise.resolve();
    var logError = function (err) { console.error(err); };
    timerFunc = function () {
      p.then(nextTickHandler).catch(logError);
      // in problematic UIWebViews, Promise.then doesn't completely break, but
      // it can get stuck in a weird state where callbacks are pushed into the
      // microtask queue but the queue isn't being flushed, until the browser
      // needs to do some other work, e.g. handle a timer. Therefore we can
      // "force" the microtask queue to be flushed by adding an empty timer.
      if (isIOS) { setTimeout(noop); }
    };
  } else if (typeof MutationObserver !== 'undefined' && (
    isNative(MutationObserver) ||
    // PhantomJS and iOS 7.x
    MutationObserver.toString() === '[object MutationObserverConstructor]'
  )) {
    // use MutationObserver where native Promise is not available,
    // e.g. PhantomJS IE11, iOS7, Android 4.4
    var counter = 1;
    var observer = new MutationObserver(nextTickHandler);
    var textNode = document.createTextNode(String(counter));
    observer.observe(textNode, {
      characterData: true
    });
    timerFunc = function () {
      counter = (counter + 1) % 2;
      textNode.data = String(counter);
    };
  } else {
    // fallback to setTimeout
    /* istanbul ignore next */
    timerFunc = function () {
      setTimeout(nextTickHandler, 0);
    };
  }

  return function queueNextTick (cb, ctx) {
    var _resolve;
    callbacks.push(function () {
      if (cb) { cb.call(ctx); }
      if (_resolve) { _resolve(ctx); }
    });
    if (!pending) {
      pending = true;
      timerFunc();
    }
    if (!cb && typeof Promise !== 'undefined') {
      return new Promise(function (resolve) {
        _resolve = resolve;
      })
    }
  }
})();

var _Set;
/* istanbul ignore if */
if (typeof Set !== 'undefined' && isNative(Set)) {
  // use native Set when available.
  _Set = Set;
} else {
  // a non-standard Set polyfill that only works with primitive keys.
  _Set = (function () {
    function Set () {
      this.set = Object.create(null);
    }
    Set.prototype.has = function has (key) {
      return this.set[key] === true
    };
    Set.prototype.add = function add (key) {
      this.set[key] = true;
    };
    Set.prototype.clear = function clear () {
      this.set = Object.create(null);
    };

    return Set;
  }());
}

var perf;

if (process.env.NODE_ENV !== 'production') {
  perf = inBrowser && window.performance;
  if (perf && (!perf.mark || !perf.measure)) {
    perf = undefined;
  }
}

/*  */

var emptyObject = Object.freeze({});

/**
 * Check if a string starts with $ or _
 */
function isReserved (str) {
  var c = (str + '').charCodeAt(0);
  return c === 0x24 || c === 0x5F
}

/**
 * Define a property.
 */
function def (obj, key, val, enumerable) {
  Object.defineProperty(obj, key, {
    value: val,
    enumerable: !!enumerable,
    writable: true,
    configurable: true
  });
}

/**
 * Parse simple path.
 */
var bailRE = /[^\w.$]/;
function parsePath (path) {
  if (bailRE.test(path)) {
    return
  } else {
    var segments = path.split('.');
    return function (obj) {
      for (var i = 0; i < segments.length; i++) {
        if (!obj) { return }
        obj = obj[segments[i]];
      }
      return obj
    }
  }
}

var warn = noop;
var tip = noop;
var formatComponentName;

if (process.env.NODE_ENV !== 'production') {
  var hasConsole = typeof console !== 'undefined';
  var classifyRE = /(?:^|[-_])(\w)/g;
  var classify = function (str) { return str
    .replace(classifyRE, function (c) { return c.toUpperCase(); })
    .replace(/[-_]/g, ''); };

  warn = function (msg, vm) {
    if (hasConsole && (!config.silent)) {
      console.error("[Vue warn]: " + msg + " " + (
        vm ? formatLocation(formatComponentName(vm)) : ''
      ));
    }
  };

  tip = function (msg, vm) {
    if (hasConsole && (!config.silent)) {
      console.warn("[Vue tip]: " + msg + " " + (
        vm ? formatLocation(formatComponentName(vm)) : ''
      ));
    }
  };

  formatComponentName = function (vm, includeFile) {
    if (vm.$root === vm) {
      return '<Root>'
    }
    var name = vm._isVue
      ? vm.$options.name || vm.$options._componentTag
      : vm.name;

    var file = vm._isVue && vm.$options.__file;
    if (!name && file) {
      var match = file.match(/([^/\\]+)\.vue$/);
      name = match && match[1];
    }

    return (
      (name ? ("<" + (classify(name)) + ">") : "<Anonymous>") +
      (file && includeFile !== false ? (" at " + file) : '')
    )
  };

  var formatLocation = function (str) {
    if (str === "<Anonymous>") {
      str += " - use the \"name\" option for better debugging messages.";
    }
    return ("\n(found in " + str + ")")
  };
}

/*  */


var uid$1 = 0;

/**
 * A dep is an observable that can have multiple
 * directives subscribing to it.
 */
var Dep = function Dep () {
  this.id = uid$1++;
  this.subs = [];
};

Dep.prototype.addSub = function addSub (sub) {
  this.subs.push(sub);
};

Dep.prototype.removeSub = function removeSub (sub) {
  remove(this.subs, sub);
};

Dep.prototype.depend = function depend () {
  if (Dep.target) {
    Dep.target.addDep(this);
  }
};

Dep.prototype.notify = function notify () {
  // stablize the subscriber list first
  var subs = this.subs.slice();
  for (var i = 0, l = subs.length; i < l; i++) {
    subs[i].update();
  }
};

// the current target watcher being evaluated.
// this is globally unique because there could be only one
// watcher being evaluated at any time.
Dep.target = null;
var targetStack = [];

function pushTarget (_target) {
  if (Dep.target) { targetStack.push(Dep.target); }
  Dep.target = _target;
}

function popTarget () {
  Dep.target = targetStack.pop();
}

/*
 * not type checking this file because flow doesn't play well with
 * dynamically accessing methods on Array prototype
 */

var arrayProto = Array.prototype;
var arrayMethods = Object.create(arrayProto);[
  'push',
  'pop',
  'shift',
  'unshift',
  'splice',
  'sort',
  'reverse'
]
.forEach(function (method) {
  // cache original method
  var original = arrayProto[method];
  def(arrayMethods, method, function mutator () {
    var arguments$1 = arguments;

    // avoid leaking arguments:
    // http://jsperf.com/closure-with-arguments
    var i = arguments.length;
    var args = new Array(i);
    while (i--) {
      args[i] = arguments$1[i];
    }
    var result = original.apply(this, args);
    var ob = this.__ob__;
    var inserted;
    switch (method) {
      case 'push':
        inserted = args;
        break
      case 'unshift':
        inserted = args;
        break
      case 'splice':
        inserted = args.slice(2);
        break
    }
    if (inserted) { ob.observeArray(inserted); }
    // notify change
    ob.dep.notify();
    return result
  });
});

/*  */

var arrayKeys = Object.getOwnPropertyNames(arrayMethods);

/**
 * By default, when a reactive property is set, the new value is
 * also converted to become reactive. However when passing down props,
 * we don't want to force conversion because the value may be a nested value
 * under a frozen data structure. Converting it would defeat the optimization.
 */
var observerState = {
  shouldConvert: true,
  isSettingProps: false
};

/**
 * Observer class that are attached to each observed
 * object. Once attached, the observer converts target
 * object's property keys into getter/setters that
 * collect dependencies and dispatches updates.
 */
var Observer = function Observer (value) {
  this.value = value;
  this.dep = new Dep();
  this.vmCount = 0;
  def(value, '__ob__', this);
  if (Array.isArray(value)) {
    var augment = hasProto
      ? protoAugment
      : copyAugment;
    augment(value, arrayMethods, arrayKeys);
    this.observeArray(value);
  } else {
    this.walk(value);
  }
};

/**
 * Walk through each property and convert them into
 * getter/setters. This method should only be called when
 * value type is Object.
 */
Observer.prototype.walk = function walk (obj) {
  var keys = Object.keys(obj);
  for (var i = 0; i < keys.length; i++) {
    defineReactive$$1(obj, keys[i], obj[keys[i]]);
  }
};

/**
 * Observe a list of Array items.
 */
Observer.prototype.observeArray = function observeArray (items) {
  for (var i = 0, l = items.length; i < l; i++) {
    observe(items[i]);
  }
};

// helpers

/**
 * Augment an target Object or Array by intercepting
 * the prototype chain using __proto__
 */
function protoAugment (target, src) {
  /* eslint-disable no-proto */
  target.__proto__ = src;
  /* eslint-enable no-proto */
}

/**
 * Augment an target Object or Array by defining
 * hidden properties.
 */
/* istanbul ignore next */
function copyAugment (target, src, keys) {
  for (var i = 0, l = keys.length; i < l; i++) {
    var key = keys[i];
    def(target, key, src[key]);
  }
}

/**
 * Attempt to create an observer instance for a value,
 * returns the new observer if successfully observed,
 * or the existing observer if the value already has one.
 */
function observe (value, asRootData) {
  if (!isObject(value)) {
    return
  }
  var ob;
  if (hasOwn(value, '__ob__') && value.__ob__ instanceof Observer) {
    ob = value.__ob__;
  } else if (
    observerState.shouldConvert &&
    !isServerRendering() &&
    (Array.isArray(value) || isPlainObject(value)) &&
    Object.isExtensible(value) &&
    !value._isVue
  ) {
    ob = new Observer(value);
  }
  if (asRootData && ob) {
    ob.vmCount++;
  }
  return ob
}

/**
 * Define a reactive property on an Object.
 */
function defineReactive$$1 (
  obj,
  key,
  val,
  customSetter
) {
  var dep = new Dep();

  var property = Object.getOwnPropertyDescriptor(obj, key);
  if (property && property.configurable === false) {
    return
  }

  // cater for pre-defined getter/setters
  var getter = property && property.get;
  var setter = property && property.set;

  var childOb = observe(val);
  Object.defineProperty(obj, key, {
    enumerable: true,
    configurable: true,
    get: function reactiveGetter () {
      var value = getter ? getter.call(obj) : val;
      if (Dep.target) {
        dep.depend();
        if (childOb) {
          childOb.dep.depend();
        }
        if (Array.isArray(value)) {
          dependArray(value);
        }
      }
      return value
    },
    set: function reactiveSetter (newVal) {
      var value = getter ? getter.call(obj) : val;
      /* eslint-disable no-self-compare */
      if (newVal === value || (newVal !== newVal && value !== value)) {
        return
      }
      /* eslint-enable no-self-compare */
      if (process.env.NODE_ENV !== 'production' && customSetter) {
        customSetter();
      }
      if (setter) {
        setter.call(obj, newVal);
      } else {
        val = newVal;
      }
      childOb = observe(newVal);
      dep.notify();
    }
  });
}

/**
 * Set a property on an object. Adds the new property and
 * triggers change notification if the property doesn't
 * already exist.
 */
function set (obj, key, val) {
  if (Array.isArray(obj)) {
    obj.length = Math.max(obj.length, key);
    obj.splice(key, 1, val);
    return val
  }
  if (hasOwn(obj, key)) {
    obj[key] = val;
    return
  }
  var ob = obj.__ob__;
  if (obj._isVue || (ob && ob.vmCount)) {
    process.env.NODE_ENV !== 'production' && warn(
      'Avoid adding reactive properties to a Vue instance or its root $data ' +
      'at runtime - declare it upfront in the data option.'
    );
    return
  }
  if (!ob) {
    obj[key] = val;
    return
  }
  defineReactive$$1(ob.value, key, val);
  ob.dep.notify();
  return val
}

/**
 * Delete a property and trigger change if necessary.
 */
function del (obj, key) {
  if (Array.isArray(obj)) {
    obj.splice(key, 1);
    return
  }
  var ob = obj.__ob__;
  if (obj._isVue || (ob && ob.vmCount)) {
    process.env.NODE_ENV !== 'production' && warn(
      'Avoid deleting properties on a Vue instance or its root $data ' +
      '- just set it to null.'
    );
    return
  }
  if (!hasOwn(obj, key)) {
    return
  }
  delete obj[key];
  if (!ob) {
    return
  }
  ob.dep.notify();
}

/**
 * Collect dependencies on array elements when the array is touched, since
 * we cannot intercept array element access like property getters.
 */
function dependArray (value) {
  for (var e = (void 0), i = 0, l = value.length; i < l; i++) {
    e = value[i];
    e && e.__ob__ && e.__ob__.dep.depend();
    if (Array.isArray(e)) {
      dependArray(e);
    }
  }
}

/*  */

/**
 * Option overwriting strategies are functions that handle
 * how to merge a parent option value and a child option
 * value into the final value.
 */
var strats = config.optionMergeStrategies;

/**
 * Options with restrictions
 */
if (process.env.NODE_ENV !== 'production') {
  strats.el = strats.propsData = function (parent, child, vm, key) {
    if (!vm) {
      warn(
        "option \"" + key + "\" can only be used during instance " +
        'creation with the `new` keyword.'
      );
    }
    return defaultStrat(parent, child)
  };
}

/**
 * Helper that recursively merges two data objects together.
 */
function mergeData (to, from) {
  if (!from) { return to }
  var key, toVal, fromVal;
  var keys = Object.keys(from);
  for (var i = 0; i < keys.length; i++) {
    key = keys[i];
    toVal = to[key];
    fromVal = from[key];
    if (!hasOwn(to, key)) {
      set(to, key, fromVal);
    } else if (isPlainObject(toVal) && isPlainObject(fromVal)) {
      mergeData(toVal, fromVal);
    }
  }
  return to
}

/**
 * Data
 */
strats.data = function (
  parentVal,
  childVal,
  vm
) {
  if (!vm) {
    // in a Vue.extend merge, both should be functions
    if (!childVal) {
      return parentVal
    }
    if (typeof childVal !== 'function') {
      process.env.NODE_ENV !== 'production' && warn(
        'The "data" option should be a function ' +
        'that returns a per-instance value in component ' +
        'definitions.',
        vm
      );
      return parentVal
    }
    if (!parentVal) {
      return childVal
    }
    // when parentVal & childVal are both present,
    // we need to return a function that returns the
    // merged result of both functions... no need to
    // check if parentVal is a function here because
    // it has to be a function to pass previous merges.
    return function mergedDataFn () {
      return mergeData(
        childVal.call(this),
        parentVal.call(this)
      )
    }
  } else if (parentVal || childVal) {
    return function mergedInstanceDataFn () {
      // instance merge
      var instanceData = typeof childVal === 'function'
        ? childVal.call(vm)
        : childVal;
      var defaultData = typeof parentVal === 'function'
        ? parentVal.call(vm)
        : undefined;
      if (instanceData) {
        return mergeData(instanceData, defaultData)
      } else {
        return defaultData
      }
    }
  }
};

/**
 * Hooks and props are merged as arrays.
 */
function mergeHook (
  parentVal,
  childVal
) {
  return childVal
    ? parentVal
      ? parentVal.concat(childVal)
      : Array.isArray(childVal)
        ? childVal
        : [childVal]
    : parentVal
}

config._lifecycleHooks.forEach(function (hook) {
  strats[hook] = mergeHook;
});

/**
 * Assets
 *
 * When a vm is present (instance creation), we need to do
 * a three-way merge between constructor options, instance
 * options and parent options.
 */
function mergeAssets (parentVal, childVal) {
  var res = Object.create(parentVal || null);
  return childVal
    ? extend(res, childVal)
    : res
}

config._assetTypes.forEach(function (type) {
  strats[type + 's'] = mergeAssets;
});

/**
 * Watchers.
 *
 * Watchers hashes should not overwrite one
 * another, so we merge them as arrays.
 */
strats.watch = function (parentVal, childVal) {
  /* istanbul ignore if */
  if (!childVal) { return Object.create(parentVal || null) }
  if (!parentVal) { return childVal }
  var ret = {};
  extend(ret, parentVal);
  for (var key in childVal) {
    var parent = ret[key];
    var child = childVal[key];
    if (parent && !Array.isArray(parent)) {
      parent = [parent];
    }
    ret[key] = parent
      ? parent.concat(child)
      : [child];
  }
  return ret
};

/**
 * Other object hashes.
 */
strats.props =
strats.methods =
strats.computed = function (parentVal, childVal) {
  if (!childVal) { return Object.create(parentVal || null) }
  if (!parentVal) { return childVal }
  var ret = Object.create(null);
  extend(ret, parentVal);
  extend(ret, childVal);
  return ret
};

/**
 * Default strategy.
 */
var defaultStrat = function (parentVal, childVal) {
  return childVal === undefined
    ? parentVal
    : childVal
};

/**
 * Validate component names
 */
function checkComponents (options) {
  for (var key in options.components) {
    var lower = key.toLowerCase();
    if (isBuiltInTag(lower) || config.isReservedTag(lower)) {
      warn(
        'Do not use built-in or reserved HTML elements as component ' +
        'id: ' + key
      );
    }
  }
}

/**
 * Ensure all props option syntax are normalized into the
 * Object-based format.
 */
function normalizeProps (options) {
  var props = options.props;
  if (!props) { return }
  var res = {};
  var i, val, name;
  if (Array.isArray(props)) {
    i = props.length;
    while (i--) {
      val = props[i];
      if (typeof val === 'string') {
        name = camelize(val);
        res[name] = { type: null };
      } else if (process.env.NODE_ENV !== 'production') {
        warn('props must be strings when using array syntax.');
      }
    }
  } else if (isPlainObject(props)) {
    for (var key in props) {
      val = props[key];
      name = camelize(key);
      res[name] = isPlainObject(val)
        ? val
        : { type: val };
    }
  }
  options.props = res;
}

/**
 * Normalize raw function directives into object format.
 */
function normalizeDirectives (options) {
  var dirs = options.directives;
  if (dirs) {
    for (var key in dirs) {
      var def = dirs[key];
      if (typeof def === 'function') {
        dirs[key] = { bind: def, update: def };
      }
    }
  }
}

/**
 * Merge two option objects into a new one.
 * Core utility used in both instantiation and inheritance.
 */
function mergeOptions (
  parent,
  child,
  vm
) {
  if (process.env.NODE_ENV !== 'production') {
    checkComponents(child);
  }
  normalizeProps(child);
  normalizeDirectives(child);
  var extendsFrom = child.extends;
  if (extendsFrom) {
    parent = typeof extendsFrom === 'function'
      ? mergeOptions(parent, extendsFrom.options, vm)
      : mergeOptions(parent, extendsFrom, vm);
  }
  if (child.mixins) {
    for (var i = 0, l = child.mixins.length; i < l; i++) {
      var mixin = child.mixins[i];
      if (mixin.prototype instanceof Vue$3) {
        mixin = mixin.options;
      }
      parent = mergeOptions(parent, mixin, vm);
    }
  }
  var options = {};
  var key;
  for (key in parent) {
    mergeField(key);
  }
  for (key in child) {
    if (!hasOwn(parent, key)) {
      mergeField(key);
    }
  }
  function mergeField (key) {
    var strat = strats[key] || defaultStrat;
    options[key] = strat(parent[key], child[key], vm, key);
  }
  return options
}

/**
 * Resolve an asset.
 * This function is used because child instances need access
 * to assets defined in its ancestor chain.
 */
function resolveAsset (
  options,
  type,
  id,
  warnMissing
) {
  /* istanbul ignore if */
  if (typeof id !== 'string') {
    return
  }
  var assets = options[type];
  // check local registration variations first
  if (hasOwn(assets, id)) { return assets[id] }
  var camelizedId = camelize(id);
  if (hasOwn(assets, camelizedId)) { return assets[camelizedId] }
  var PascalCaseId = capitalize(camelizedId);
  if (hasOwn(assets, PascalCaseId)) { return assets[PascalCaseId] }
  // fallback to prototype chain
  var res = assets[id] || assets[camelizedId] || assets[PascalCaseId];
  if (process.env.NODE_ENV !== 'production' && warnMissing && !res) {
    warn(
      'Failed to resolve ' + type.slice(0, -1) + ': ' + id,
      options
    );
  }
  return res
}

/*  */

function validateProp (
  key,
  propOptions,
  propsData,
  vm
) {
  var prop = propOptions[key];
  var absent = !hasOwn(propsData, key);
  var value = propsData[key];
  // handle boolean props
  if (isType(Boolean, prop.type)) {
    if (absent && !hasOwn(prop, 'default')) {
      value = false;
    } else if (!isType(String, prop.type) && (value === '' || value === hyphenate(key))) {
      value = true;
    }
  }
  // check default value
  if (value === undefined) {
    value = getPropDefaultValue(vm, prop, key);
    // since the default value is a fresh copy,
    // make sure to observe it.
    var prevShouldConvert = observerState.shouldConvert;
    observerState.shouldConvert = true;
    observe(value);
    observerState.shouldConvert = prevShouldConvert;
  }
  if (process.env.NODE_ENV !== 'production') {
    assertProp(prop, key, value, vm, absent);
  }
  return value
}

/**
 * Get the default value of a prop.
 */
function getPropDefaultValue (vm, prop, key) {
  // no default, return undefined
  if (!hasOwn(prop, 'default')) {
    return undefined
  }
  var def = prop.default;
  // warn against non-factory defaults for Object & Array
  if (process.env.NODE_ENV !== 'production' && isObject(def)) {
    warn(
      'Invalid default value for prop "' + key + '": ' +
      'Props with type Object/Array must use a factory function ' +
      'to return the default value.',
      vm
    );
  }
  // the raw prop value was also undefined from previous render,
  // return previous default value to avoid unnecessary watcher trigger
  if (vm && vm.$options.propsData &&
    vm.$options.propsData[key] === undefined &&
    vm._props[key] !== undefined) {
    return vm._props[key]
  }
  // call factory function for non-Function types
  // a value is Function if its prototype is function even across different execution context
  return typeof def === 'function' && getType(prop.type) !== 'Function'
    ? def.call(vm)
    : def
}

/**
 * Assert whether a prop is valid.
 */
function assertProp (
  prop,
  name,
  value,
  vm,
  absent
) {
  if (prop.required && absent) {
    warn(
      'Missing required prop: "' + name + '"',
      vm
    );
    return
  }
  if (value == null && !prop.required) {
    return
  }
  var type = prop.type;
  var valid = !type || type === true;
  var expectedTypes = [];
  if (type) {
    if (!Array.isArray(type)) {
      type = [type];
    }
    for (var i = 0; i < type.length && !valid; i++) {
      var assertedType = assertType(value, type[i]);
      expectedTypes.push(assertedType.expectedType || '');
      valid = assertedType.valid;
    }
  }
  if (!valid) {
    warn(
      'Invalid prop: type check failed for prop "' + name + '".' +
      ' Expected ' + expectedTypes.map(capitalize).join(', ') +
      ', got ' + Object.prototype.toString.call(value).slice(8, -1) + '.',
      vm
    );
    return
  }
  var validator = prop.validator;
  if (validator) {
    if (!validator(value)) {
      warn(
        'Invalid prop: custom validator check failed for prop "' + name + '".',
        vm
      );
    }
  }
}

/**
 * Assert the type of a value
 */
function assertType (value, type) {
  var valid;
  var expectedType = getType(type);
  if (expectedType === 'String') {
    valid = typeof value === (expectedType = 'string');
  } else if (expectedType === 'Number') {
    valid = typeof value === (expectedType = 'number');
  } else if (expectedType === 'Boolean') {
    valid = typeof value === (expectedType = 'boolean');
  } else if (expectedType === 'Function') {
    valid = typeof value === (expectedType = 'function');
  } else if (expectedType === 'Object') {
    valid = isPlainObject(value);
  } else if (expectedType === 'Array') {
    valid = Array.isArray(value);
  } else {
    valid = value instanceof type;
  }
  return {
    valid: valid,
    expectedType: expectedType
  }
}

/**
 * Use function string name to check built-in types,
 * because a simple equality check will fail when running
 * across different vms / iframes.
 */
function getType (fn) {
  var match = fn && fn.toString().match(/^\s*function (\w+)/);
  return match && match[1]
}

function isType (type, fn) {
  if (!Array.isArray(fn)) {
    return getType(fn) === getType(type)
  }
  for (var i = 0, len = fn.length; i < len; i++) {
    if (getType(fn[i]) === getType(type)) {
      return true
    }
  }
  /* istanbul ignore next */
  return false
}

function handleError (err, vm, type) {
  if (config.errorHandler) {
    config.errorHandler.call(null, err, vm, type);
  } else {
    if (process.env.NODE_ENV !== 'production') {
      warn(("Error in " + type + ":"), vm);
    }
    /* istanbul ignore else */
    if (inBrowser && typeof console !== 'undefined') {
      console.error(err);
    } else {
      throw err
    }
  }
}

/* not type checking this file because flow doesn't play well with Proxy */

var initProxy;

if (process.env.NODE_ENV !== 'production') {
  var allowedGlobals = makeMap(
    'Infinity,undefined,NaN,isFinite,isNaN,' +
    'parseFloat,parseInt,decodeURI,decodeURIComponent,encodeURI,encodeURIComponent,' +
    'Math,Number,Date,Array,Object,Boolean,String,RegExp,Map,Set,JSON,Intl,' +
    'require' // for Webpack/Browserify
  );

  var warnNonPresent = function (target, key) {
    warn(
      "Property or method \"" + key + "\" is not defined on the instance but " +
      "referenced during render. Make sure to declare reactive data " +
      "properties in the data option.",
      target
    );
  };

  var hasProxy =
    typeof Proxy !== 'undefined' &&
    Proxy.toString().match(/native code/);

  if (hasProxy) {
    var isBuiltInModifier = makeMap('stop,prevent,self,ctrl,shift,alt,meta');
    config.keyCodes = new Proxy(config.keyCodes, {
      set: function set (target, key, value) {
        if (isBuiltInModifier(key)) {
          warn(("Avoid overwriting built-in modifier in config.keyCodes: ." + key));
          return false
        } else {
          target[key] = value;
          return true
        }
      }
    });
  }

  var hasHandler = {
    has: function has (target, key) {
      var has = key in target;
      var isAllowed = allowedGlobals(key) || key.charAt(0) === '_';
      if (!has && !isAllowed) {
        warnNonPresent(target, key);
      }
      return has || !isAllowed
    }
  };

  var getHandler = {
    get: function get (target, key) {
      if (typeof key === 'string' && !(key in target)) {
        warnNonPresent(target, key);
      }
      return target[key]
    }
  };

  initProxy = function initProxy (vm) {
    if (hasProxy) {
      // determine which proxy handler to use
      var options = vm.$options;
      var handlers = options.render && options.render._withStripped
        ? getHandler
        : hasHandler;
      vm._renderProxy = new Proxy(vm, handlers);
    } else {
      vm._renderProxy = vm;
    }
  };
}

/*  */

var VNode = function VNode (
  tag,
  data,
  children,
  text,
  elm,
  context,
  componentOptions
) {
  this.tag = tag;
  this.data = data;
  this.children = children;
  this.text = text;
  this.elm = elm;
  this.ns = undefined;
  this.context = context;
  this.functionalContext = undefined;
  this.key = data && data.key;
  this.componentOptions = componentOptions;
  this.componentInstance = undefined;
  this.parent = undefined;
  this.raw = false;
  this.isStatic = false;
  this.isRootInsert = true;
  this.isComment = false;
  this.isCloned = false;
  this.isOnce = false;
};

var prototypeAccessors = { child: {} };

// DEPRECATED: alias for componentInstance for backwards compat.
/* istanbul ignore next */
prototypeAccessors.child.get = function () {
  return this.componentInstance
};

Object.defineProperties( VNode.prototype, prototypeAccessors );

var createEmptyVNode = function () {
  var node = new VNode();
  node.text = '';
  node.isComment = true;
  return node
};

function createTextVNode (val) {
  return new VNode(undefined, undefined, undefined, String(val))
}

// optimized shallow clone
// used for static nodes and slot nodes because they may be reused across
// multiple renders, cloning them avoids errors when DOM manipulations rely
// on their elm reference.
function cloneVNode (vnode) {
  var cloned = new VNode(
    vnode.tag,
    vnode.data,
    vnode.children,
    vnode.text,
    vnode.elm,
    vnode.context,
    vnode.componentOptions
  );
  cloned.ns = vnode.ns;
  cloned.isStatic = vnode.isStatic;
  cloned.key = vnode.key;
  cloned.isCloned = true;
  return cloned
}

function cloneVNodes (vnodes) {
  var res = new Array(vnodes.length);
  for (var i = 0; i < vnodes.length; i++) {
    res[i] = cloneVNode(vnodes[i]);
  }
  return res
}

/*  */

var normalizeEvent = cached(function (name) {
  var once$$1 = name.charAt(0) === '~'; // Prefixed last, checked first
  name = once$$1 ? name.slice(1) : name;
  var capture = name.charAt(0) === '!';
  name = capture ? name.slice(1) : name;
  return {
    name: name,
    once: once$$1,
    capture: capture
  }
});

function createFnInvoker (fns) {
  function invoker () {
    var arguments$1 = arguments;

    var fns = invoker.fns;
    if (Array.isArray(fns)) {
      for (var i = 0; i < fns.length; i++) {
        fns[i].apply(null, arguments$1);
      }
    } else {
      // return handler return value for single handlers
      return fns.apply(null, arguments)
    }
  }
  invoker.fns = fns;
  return invoker
}

function updateListeners (
  on,
  oldOn,
  add,
  remove$$1,
  vm
) {
  var name, cur, old, event;
  for (name in on) {
    cur = on[name];
    old = oldOn[name];
    event = normalizeEvent(name);
    if (!cur) {
      process.env.NODE_ENV !== 'production' && warn(
        "Invalid handler for event \"" + (event.name) + "\": got " + String(cur),
        vm
      );
    } else if (!old) {
      if (!cur.fns) {
        cur = on[name] = createFnInvoker(cur);
      }
      add(event.name, cur, event.once, event.capture);
    } else if (cur !== old) {
      old.fns = cur;
      on[name] = old;
    }
  }
  for (name in oldOn) {
    if (!on[name]) {
      event = normalizeEvent(name);
      remove$$1(event.name, oldOn[name], event.capture);
    }
  }
}

/*  */

function mergeVNodeHook (def, hookKey, hook) {
  var invoker;
  var oldHook = def[hookKey];

  function wrappedHook () {
    hook.apply(this, arguments);
    // important: remove merged hook to ensure it's called only once
    // and prevent memory leak
    remove(invoker.fns, wrappedHook);
  }

  if (!oldHook) {
    // no existing hook
    invoker = createFnInvoker([wrappedHook]);
  } else {
    /* istanbul ignore if */
    if (oldHook.fns && oldHook.merged) {
      // already a merged invoker
      invoker = oldHook;
      invoker.fns.push(wrappedHook);
    } else {
      // existing plain hook
      invoker = createFnInvoker([oldHook, wrappedHook]);
    }
  }

  invoker.merged = true;
  def[hookKey] = invoker;
}

/*  */

// The template compiler attempts to minimize the need for normalization by
// statically analyzing the template at compile time.
//
// For plain HTML markup, normalization can be completely skipped because the
// generated render function is guaranteed to return Array<VNode>. There are
// two cases where extra normalization is needed:

// 1. When the children contains components - because a functional component
// may return an Array instead of a single root. In this case, just a simple
// normalization is needed - if any child is an Array, we flatten the whole
// thing with Array.prototype.concat. It is guaranteed to be only 1-level deep
// because functional components already normalize their own children.
function simpleNormalizeChildren (children) {
  for (var i = 0; i < children.length; i++) {
    if (Array.isArray(children[i])) {
      return Array.prototype.concat.apply([], children)
    }
  }
  return children
}

// 2. When the children contains constrcuts that always generated nested Arrays,
// e.g. <template>, <slot>, v-for, or when the children is provided by user
// with hand-written render functions / JSX. In such cases a full normalization
// is needed to cater to all possible types of children values.
function normalizeChildren (children) {
  return isPrimitive(children)
    ? [createTextVNode(children)]
    : Array.isArray(children)
      ? normalizeArrayChildren(children)
      : undefined
}

function normalizeArrayChildren (children, nestedIndex) {
  var res = [];
  var i, c, last;
  for (i = 0; i < children.length; i++) {
    c = children[i];
    if (c == null || typeof c === 'boolean') { continue }
    last = res[res.length - 1];
    //  nested
    if (Array.isArray(c)) {
      res.push.apply(res, normalizeArrayChildren(c, ((nestedIndex || '') + "_" + i)));
    } else if (isPrimitive(c)) {
      if (last && last.text) {
        last.text += String(c);
      } else if (c !== '') {
        // convert primitive to vnode
        res.push(createTextVNode(c));
      }
    } else {
      if (c.text && last && last.text) {
        res[res.length - 1] = createTextVNode(last.text + c.text);
      } else {
        // default key for nested array children (likely generated by v-for)
        if (c.tag && c.key == null && nestedIndex != null) {
          c.key = "__vlist" + nestedIndex + "_" + i + "__";
        }
        res.push(c);
      }
    }
  }
  return res
}

/*  */

function getFirstComponentChild (children) {
  return children && children.filter(function (c) { return c && c.componentOptions; })[0]
}

/*  */

function initEvents (vm) {
  vm._events = Object.create(null);
  vm._hasHookEvent = false;
  // init parent attached events
  var listeners = vm.$options._parentListeners;
  if (listeners) {
    updateComponentListeners(vm, listeners);
  }
}

var target;

function add (event, fn, once$$1) {
  if (once$$1) {
    target.$once(event, fn);
  } else {
    target.$on(event, fn);
  }
}

function remove$1 (event, fn) {
  target.$off(event, fn);
}

function updateComponentListeners (
  vm,
  listeners,
  oldListeners
) {
  target = vm;
  updateListeners(listeners, oldListeners || {}, add, remove$1, vm);
}

function eventsMixin (Vue) {
  var hookRE = /^hook:/;
  Vue.prototype.$on = function (event, fn) {
    var this$1 = this;

    var vm = this;
    if (Array.isArray(event)) {
      for (var i = 0, l = event.length; i < l; i++) {
        this$1.$on(event[i], fn);
      }
    } else {
      (vm._events[event] || (vm._events[event] = [])).push(fn);
      // optimize hook:event cost by using a boolean flag marked at registration
      // instead of a hash lookup
      if (hookRE.test(event)) {
        vm._hasHookEvent = true;
      }
    }
    return vm
  };

  Vue.prototype.$once = function (event, fn) {
    var vm = this;
    function on () {
      vm.$off(event, on);
      fn.apply(vm, arguments);
    }
    on.fn = fn;
    vm.$on(event, on);
    return vm
  };

  Vue.prototype.$off = function (event, fn) {
    var vm = this;
    // all
    if (!arguments.length) {
      vm._events = Object.create(null);
      return vm
    }
    // specific event
    var cbs = vm._events[event];
    if (!cbs) {
      return vm
    }
    if (arguments.length === 1) {
      vm._events[event] = null;
      return vm
    }
    // specific handler
    var cb;
    var i = cbs.length;
    while (i--) {
      cb = cbs[i];
      if (cb === fn || cb.fn === fn) {
        cbs.splice(i, 1);
        break
      }
    }
    return vm
  };

  Vue.prototype.$emit = function (event) {
    var vm = this;
    var cbs = vm._events[event];
    if (cbs) {
      cbs = cbs.length > 1 ? toArray(cbs) : cbs;
      var args = toArray(arguments, 1);
      for (var i = 0, l = cbs.length; i < l; i++) {
        cbs[i].apply(vm, args);
      }
    }
    return vm
  };
}

/*  */

/**
 * Runtime helper for resolving raw children VNodes into a slot object.
 */
function resolveSlots (
  children,
  context
) {
  var slots = {};
  if (!children) {
    return slots
  }
  var defaultSlot = [];
  var name, child;
  for (var i = 0, l = children.length; i < l; i++) {
    child = children[i];
    // named slots should only be respected if the vnode was rendered in the
    // same context.
    if ((child.context === context || child.functionalContext === context) &&
        child.data && (name = child.data.slot)) {
      var slot = (slots[name] || (slots[name] = []));
      if (child.tag === 'template') {
        slot.push.apply(slot, child.children);
      } else {
        slot.push(child);
      }
    } else {
      defaultSlot.push(child);
    }
  }
  // ignore single whitespace
  if (defaultSlot.length && !(
    defaultSlot.length === 1 &&
    (defaultSlot[0].text === ' ' || defaultSlot[0].isComment)
  )) {
    slots.default = defaultSlot;
  }
  return slots
}

function resolveScopedSlots (
  fns
) {
  var res = {};
  for (var i = 0; i < fns.length; i++) {
    res[fns[i][0]] = fns[i][1];
  }
  return res
}

/*  */

var activeInstance = null;

function initLifecycle (vm) {
  var options = vm.$options;

  // locate first non-abstract parent
  var parent = options.parent;
  if (parent && !options.abstract) {
    while (parent.$options.abstract && parent.$parent) {
      parent = parent.$parent;
    }
    parent.$children.push(vm);
  }

  vm.$parent = parent;
  vm.$root = parent ? parent.$root : vm;

  vm.$children = [];
  vm.$refs = {};

  vm._watcher = null;
  vm._inactive = null;
  vm._directInactive = false;
  vm._isMounted = false;
  vm._isDestroyed = false;
  vm._isBeingDestroyed = false;
}

function lifecycleMixin (Vue) {
  Vue.prototype._update = function (vnode, hydrating) {
    var vm = this;
    if (vm._isMounted) {
      callHook(vm, 'beforeUpdate');
    }
    var prevEl = vm.$el;
    var prevVnode = vm._vnode;
    var prevActiveInstance = activeInstance;
    activeInstance = vm;
    vm._vnode = vnode;
    // Vue.prototype.__patch__ is injected in entry points
    // based on the rendering backend used.
    if (!prevVnode) {
      // initial render
      vm.$el = vm.__patch__(
        vm.$el, vnode, hydrating, false /* removeOnly */,
        vm.$options._parentElm,
        vm.$options._refElm
      );
    } else {
      // updates
      vm.$el = vm.__patch__(prevVnode, vnode);
    }
    activeInstance = prevActiveInstance;
    // update __vue__ reference
    if (prevEl) {
      prevEl.__vue__ = null;
    }
    if (vm.$el) {
      vm.$el.__vue__ = vm;
    }
    // if parent is an HOC, update its $el as well
    if (vm.$vnode && vm.$parent && vm.$vnode === vm.$parent._vnode) {
      vm.$parent.$el = vm.$el;
    }
    // updated hook is called by the scheduler to ensure that children are
    // updated in a parent's updated hook.
  };

  Vue.prototype.$forceUpdate = function () {
    var vm = this;
    if (vm._watcher) {
      vm._watcher.update();
    }
  };

  Vue.prototype.$destroy = function () {
    var vm = this;
    if (vm._isBeingDestroyed) {
      return
    }
    callHook(vm, 'beforeDestroy');
    vm._isBeingDestroyed = true;
    // remove self from parent
    var parent = vm.$parent;
    if (parent && !parent._isBeingDestroyed && !vm.$options.abstract) {
      remove(parent.$children, vm);
    }
    // teardown watchers
    if (vm._watcher) {
      vm._watcher.teardown();
    }
    var i = vm._watchers.length;
    while (i--) {
      vm._watchers[i].teardown();
    }
    // remove reference from data ob
    // frozen object may not have observer.
    if (vm._data.__ob__) {
      vm._data.__ob__.vmCount--;
    }
    // call the last hook...
    vm._isDestroyed = true;
    callHook(vm, 'destroyed');
    // turn off all instance listeners.
    vm.$off();
    // remove __vue__ reference
    if (vm.$el) {
      vm.$el.__vue__ = null;
    }
    // invoke destroy hooks on current rendered tree
    vm.__patch__(vm._vnode, null);
  };
}

function mountComponent (
  vm,
  el,
  hydrating
) {
  vm.$el = el;
  if (!vm.$options.render) {
    vm.$options.render = createEmptyVNode;
    if (process.env.NODE_ENV !== 'production') {
      /* istanbul ignore if */
      if (vm.$options.template && vm.$options.template.charAt(0) !== '#') {
        warn(
          'You are using the runtime-only build of Vue where the template ' +
          'option is not available. Either pre-compile the templates into ' +
          'render functions, or use the compiler-included build.',
          vm
        );
      } else {
        warn(
          'Failed to mount component: template or render function not defined.',
          vm
        );
      }
    }
  }
  callHook(vm, 'beforeMount');

  var updateComponent;
  /* istanbul ignore if */
  if (process.env.NODE_ENV !== 'production' && config.performance && perf) {
    updateComponent = function () {
      var name = vm._name;
      var startTag = "start " + name;
      var endTag = "end " + name;
      perf.mark(startTag);
      var vnode = vm._render();
      perf.mark(endTag);
      perf.measure((name + " render"), startTag, endTag);
      perf.mark(startTag);
      vm._update(vnode, hydrating);
      perf.mark(endTag);
      perf.measure((name + " patch"), startTag, endTag);
    };
  } else {
    updateComponent = function () {
      vm._update(vm._render(), hydrating);
    };
  }

  vm._watcher = new Watcher(vm, updateComponent, noop);
  hydrating = false;

  // manually mounted instance, call mounted on self
  // mounted is called for render-created child components in its inserted hook
  if (vm.$vnode == null) {
    vm._isMounted = true;
    callHook(vm, 'mounted');
  }
  return vm
}

function updateChildComponent (
  vm,
  propsData,
  listeners,
  parentVnode,
  renderChildren
) {
  // determine whether component has slot children
  // we need to do this before overwriting $options._renderChildren
  var hasChildren = !!(
    renderChildren ||               // has new static slots
    vm.$options._renderChildren ||  // has old static slots
    parentVnode.data.scopedSlots || // has new scoped slots
    vm.$scopedSlots !== emptyObject // has old scoped slots
  );

  vm.$options._parentVnode = parentVnode;
  vm.$vnode = parentVnode; // update vm's placeholder node without re-render
  if (vm._vnode) { // update child tree's parent
    vm._vnode.parent = parentVnode;
  }
  vm.$options._renderChildren = renderChildren;

  // update props
  if (propsData && vm.$options.props) {
    observerState.shouldConvert = false;
    if (process.env.NODE_ENV !== 'production') {
      observerState.isSettingProps = true;
    }
    var props = vm._props;
    var propKeys = vm.$options._propKeys || [];
    for (var i = 0; i < propKeys.length; i++) {
      var key = propKeys[i];
      props[key] = validateProp(key, vm.$options.props, propsData, vm);
    }
    observerState.shouldConvert = true;
    if (process.env.NODE_ENV !== 'production') {
      observerState.isSettingProps = false;
    }
    // keep a copy of raw propsData
    vm.$options.propsData = propsData;
  }
  // update listeners
  if (listeners) {
    var oldListeners = vm.$options._parentListeners;
    vm.$options._parentListeners = listeners;
    updateComponentListeners(vm, listeners, oldListeners);
  }
  // resolve slots + force update if has children
  if (hasChildren) {
    vm.$slots = resolveSlots(renderChildren, parentVnode.context);
    vm.$forceUpdate();
  }
}

function isInInactiveTree (vm) {
  while (vm && (vm = vm.$parent)) {
    if (vm._inactive) { return true }
  }
  return false
}

function activateChildComponent (vm, direct) {
  if (direct) {
    vm._directInactive = false;
    if (isInInactiveTree(vm)) {
      return
    }
  } else if (vm._directInactive) {
    return
  }
  if (vm._inactive || vm._inactive == null) {
    vm._inactive = false;
    for (var i = 0; i < vm.$children.length; i++) {
      activateChildComponent(vm.$children[i]);
    }
    callHook(vm, 'activated');
  }
}

function deactivateChildComponent (vm, direct) {
  if (direct) {
    vm._directInactive = true;
    if (isInInactiveTree(vm)) {
      return
    }
  }
  if (!vm._inactive) {
    vm._inactive = true;
    for (var i = 0; i < vm.$children.length; i++) {
      deactivateChildComponent(vm.$children[i]);
    }
    callHook(vm, 'deactivated');
  }
}

function callHook (vm, hook) {
  var handlers = vm.$options[hook];
  if (handlers) {
    for (var i = 0, j = handlers.length; i < j; i++) {
      try {
        handlers[i].call(vm);
      } catch (e) {
        handleError(e, vm, (hook + " hook"));
      }
    }
  }
  if (vm._hasHookEvent) {
    vm.$emit('hook:' + hook);
  }
}

/*  */


var queue = [];
var has = {};
var circular = {};
var waiting = false;
var flushing = false;
var index = 0;

/**
 * Reset the scheduler's state.
 */
function resetSchedulerState () {
  queue.length = 0;
  has = {};
  if (process.env.NODE_ENV !== 'production') {
    circular = {};
  }
  waiting = flushing = false;
}

/**
 * Flush both queues and run the watchers.
 */
function flushSchedulerQueue () {
  flushing = true;
  var watcher, id, vm;

  // Sort queue before flush.
  // This ensures that:
  // 1. Components are updated from parent to child. (because parent is always
  //    created before the child)
  // 2. A component's user watchers are run before its render watcher (because
  //    user watchers are created before the render watcher)
  // 3. If a component is destroyed during a parent component's watcher run,
  //    its watchers can be skipped.
  queue.sort(function (a, b) { return a.id - b.id; });

  // do not cache length because more watchers might be pushed
  // as we run existing watchers
  for (index = 0; index < queue.length; index++) {
    watcher = queue[index];
    id = watcher.id;
    has[id] = null;
    watcher.run();
    // in dev build, check and stop circular updates.
    if (process.env.NODE_ENV !== 'production' && has[id] != null) {
      circular[id] = (circular[id] || 0) + 1;
      if (circular[id] > config._maxUpdateCount) {
        warn(
          'You may have an infinite update loop ' + (
            watcher.user
              ? ("in watcher with expression \"" + (watcher.expression) + "\"")
              : "in a component render function."
          ),
          watcher.vm
        );
        break
      }
    }
  }

  // call updated hooks
  index = queue.length;
  while (index--) {
    watcher = queue[index];
    vm = watcher.vm;
    if (vm._watcher === watcher && vm._isMounted) {
      callHook(vm, 'updated');
    }
  }

  // devtool hook
  /* istanbul ignore if */
  if (devtools && config.devtools) {
    devtools.emit('flush');
  }

  resetSchedulerState();
}

/**
 * Push a watcher into the watcher queue.
 * Jobs with duplicate IDs will be skipped unless it's
 * pushed when the queue is being flushed.
 */
function queueWatcher (watcher) {
  var id = watcher.id;
  if (has[id] == null) {
    has[id] = true;
    if (!flushing) {
      queue.push(watcher);
    } else {
      // if already flushing, splice the watcher based on its id
      // if already past its id, it will be run next immediately.
      var i = queue.length - 1;
      while (i >= 0 && queue[i].id > watcher.id) {
        i--;
      }
      queue.splice(Math.max(i, index) + 1, 0, watcher);
    }
    // queue the flush
    if (!waiting) {
      waiting = true;
      nextTick(flushSchedulerQueue);
    }
  }
}

/*  */

var uid$2 = 0;

/**
 * A watcher parses an expression, collects dependencies,
 * and fires callback when the expression value changes.
 * This is used for both the $watch() api and directives.
 */
var Watcher = function Watcher (
  vm,
  expOrFn,
  cb,
  options
) {
  this.vm = vm;
  vm._watchers.push(this);
  // options
  if (options) {
    this.deep = !!options.deep;
    this.user = !!options.user;
    this.lazy = !!options.lazy;
    this.sync = !!options.sync;
  } else {
    this.deep = this.user = this.lazy = this.sync = false;
  }
  this.cb = cb;
  this.id = ++uid$2; // uid for batching
  this.active = true;
  this.dirty = this.lazy; // for lazy watchers
  this.deps = [];
  this.newDeps = [];
  this.depIds = new _Set();
  this.newDepIds = new _Set();
  this.expression = process.env.NODE_ENV !== 'production'
    ? expOrFn.toString()
    : '';
  // parse expression for getter
  if (typeof expOrFn === 'function') {
    this.getter = expOrFn;
  } else {
    this.getter = parsePath(expOrFn);
    if (!this.getter) {
      this.getter = function () {};
      process.env.NODE_ENV !== 'production' && warn(
        "Failed watching path: \"" + expOrFn + "\" " +
        'Watcher only accepts simple dot-delimited paths. ' +
        'For full control, use a function instead.',
        vm
      );
    }
  }
  this.value = this.lazy
    ? undefined
    : this.get();
};

/**
 * Evaluate the getter, and re-collect dependencies.
 */
Watcher.prototype.get = function get () {
  pushTarget(this);
  var value;
  var vm = this.vm;
  if (this.user) {
    try {
      value = this.getter.call(vm, vm);
    } catch (e) {
      handleError(e, vm, ("getter for watcher \"" + (this.expression) + "\""));
    }
  } else {
    value = this.getter.call(vm, vm);
  }
  // "touch" every property so they are all tracked as
  // dependencies for deep watching
  if (this.deep) {
    traverse(value);
  }
  popTarget();
  this.cleanupDeps();
  return value
};

/**
 * Add a dependency to this directive.
 */
Watcher.prototype.addDep = function addDep (dep) {
  var id = dep.id;
  if (!this.newDepIds.has(id)) {
    this.newDepIds.add(id);
    this.newDeps.push(dep);
    if (!this.depIds.has(id)) {
      dep.addSub(this);
    }
  }
};

/**
 * Clean up for dependency collection.
 */
Watcher.prototype.cleanupDeps = function cleanupDeps () {
    var this$1 = this;

  var i = this.deps.length;
  while (i--) {
    var dep = this$1.deps[i];
    if (!this$1.newDepIds.has(dep.id)) {
      dep.removeSub(this$1);
    }
  }
  var tmp = this.depIds;
  this.depIds = this.newDepIds;
  this.newDepIds = tmp;
  this.newDepIds.clear();
  tmp = this.deps;
  this.deps = this.newDeps;
  this.newDeps = tmp;
  this.newDeps.length = 0;
};

/**
 * Subscriber interface.
 * Will be called when a dependency changes.
 */
Watcher.prototype.update = function update () {
  /* istanbul ignore else */
  if (this.lazy) {
    this.dirty = true;
  } else if (this.sync) {
    this.run();
  } else {
    queueWatcher(this);
  }
};

/**
 * Scheduler job interface.
 * Will be called by the scheduler.
 */
Watcher.prototype.run = function run () {
  if (this.active) {
    var value = this.get();
    if (
      value !== this.value ||
      // Deep watchers and watchers on Object/Arrays should fire even
      // when the value is the same, because the value may
      // have mutated.
      isObject(value) ||
      this.deep
    ) {
      // set new value
      var oldValue = this.value;
      this.value = value;
      if (this.user) {
        try {
          this.cb.call(this.vm, value, oldValue);
        } catch (e) {
          handleError(e, this.vm, ("callback for watcher \"" + (this.expression) + "\""));
        }
      } else {
        this.cb.call(this.vm, value, oldValue);
      }
    }
  }
};

/**
 * Evaluate the value of the watcher.
 * This only gets called for lazy watchers.
 */
Watcher.prototype.evaluate = function evaluate () {
  this.value = this.get();
  this.dirty = false;
};

/**
 * Depend on all deps collected by this watcher.
 */
Watcher.prototype.depend = function depend () {
    var this$1 = this;

  var i = this.deps.length;
  while (i--) {
    this$1.deps[i].depend();
  }
};

/**
 * Remove self from all dependencies' subscriber list.
 */
Watcher.prototype.teardown = function teardown () {
    var this$1 = this;

  if (this.active) {
    // remove self from vm's watcher list
    // this is a somewhat expensive operation so we skip it
    // if the vm is being destroyed.
    if (!this.vm._isBeingDestroyed) {
      remove(this.vm._watchers, this);
    }
    var i = this.deps.length;
    while (i--) {
      this$1.deps[i].removeSub(this$1);
    }
    this.active = false;
  }
};

/**
 * Recursively traverse an object to evoke all converted
 * getters, so that every nested property inside the object
 * is collected as a "deep" dependency.
 */
var seenObjects = new _Set();
function traverse (val) {
  seenObjects.clear();
  _traverse(val, seenObjects);
}

function _traverse (val, seen) {
  var i, keys;
  var isA = Array.isArray(val);
  if ((!isA && !isObject(val)) || !Object.isExtensible(val)) {
    return
  }
  if (val.__ob__) {
    var depId = val.__ob__.dep.id;
    if (seen.has(depId)) {
      return
    }
    seen.add(depId);
  }
  if (isA) {
    i = val.length;
    while (i--) { _traverse(val[i], seen); }
  } else {
    keys = Object.keys(val);
    i = keys.length;
    while (i--) { _traverse(val[keys[i]], seen); }
  }
}

/*  */

var sharedPropertyDefinition = {
  enumerable: true,
  configurable: true,
  get: noop,
  set: noop
};

function proxy (target, sourceKey, key) {
  sharedPropertyDefinition.get = function proxyGetter () {
    return this[sourceKey][key]
  };
  sharedPropertyDefinition.set = function proxySetter (val) {
    this[sourceKey][key] = val;
  };
  Object.defineProperty(target, key, sharedPropertyDefinition);
}

function initState (vm) {
  vm._watchers = [];
  var opts = vm.$options;
  if (opts.props) { initProps(vm, opts.props); }
  if (opts.methods) { initMethods(vm, opts.methods); }
  if (opts.data) {
    initData(vm);
  } else {
    observe(vm._data = {}, true /* asRootData */);
  }
  if (opts.computed) { initComputed(vm, opts.computed); }
  if (opts.watch) { initWatch(vm, opts.watch); }
}

var isReservedProp = { key: 1, ref: 1, slot: 1 };

function initProps (vm, propsOptions) {
  var propsData = vm.$options.propsData || {};
  var props = vm._props = {};
  // cache prop keys so that future props updates can iterate using Array
  // instead of dynamic object key enumeration.
  var keys = vm.$options._propKeys = [];
  var isRoot = !vm.$parent;
  // root instance props should be converted
  observerState.shouldConvert = isRoot;
  var loop = function ( key ) {
    keys.push(key);
    var value = validateProp(key, propsOptions, propsData, vm);
    /* istanbul ignore else */
    if (process.env.NODE_ENV !== 'production') {
      if (isReservedProp[key]) {
        warn(
          ("\"" + key + "\" is a reserved attribute and cannot be used as component prop."),
          vm
        );
      }
      defineReactive$$1(props, key, value, function () {
        if (vm.$parent && !observerState.isSettingProps) {
          warn(
            "Avoid mutating a prop directly since the value will be " +
            "overwritten whenever the parent component re-renders. " +
            "Instead, use a data or computed property based on the prop's " +
            "value. Prop being mutated: \"" + key + "\"",
            vm
          );
        }
      });
    } else {
      defineReactive$$1(props, key, value);
    }
    // static props are already proxied on the component's prototype
    // during Vue.extend(). We only need to proxy props defined at
    // instantiation here.
    if (!(key in vm)) {
      proxy(vm, "_props", key);
    }
  };

  for (var key in propsOptions) loop( key );
  observerState.shouldConvert = true;
}

function initData (vm) {
  var data = vm.$options.data;
  data = vm._data = typeof data === 'function'
    ? data.call(vm)
    : data || {};
  if (!isPlainObject(data)) {
    data = {};
    process.env.NODE_ENV !== 'production' && warn(
      'data functions should return an object:\n' +
      'https://vuejs.org/v2/guide/components.html#data-Must-Be-a-Function',
      vm
    );
  }
  // proxy data on instance
  var keys = Object.keys(data);
  var props = vm.$options.props;
  var i = keys.length;
  while (i--) {
    if (props && hasOwn(props, keys[i])) {
      process.env.NODE_ENV !== 'production' && warn(
        "The data property \"" + (keys[i]) + "\" is already declared as a prop. " +
        "Use prop default value instead.",
        vm
      );
    } else if (!isReserved(keys[i])) {
      proxy(vm, "_data", keys[i]);
    }
  }
  // observe data
  observe(data, true /* asRootData */);
}

var computedWatcherOptions = { lazy: true };

function initComputed (vm, computed) {
  var watchers = vm._computedWatchers = Object.create(null);

  for (var key in computed) {
    var userDef = computed[key];
    var getter = typeof userDef === 'function' ? userDef : userDef.get;
    // create internal watcher for the computed property.
    watchers[key] = new Watcher(vm, getter, noop, computedWatcherOptions);

    // component-defined computed properties are already defined on the
    // component prototype. We only need to define computed properties defined
    // at instantiation here.
    if (!(key in vm)) {
      defineComputed(vm, key, userDef);
    }
  }
}

function defineComputed (target, key, userDef) {
  if (typeof userDef === 'function') {
    sharedPropertyDefinition.get = createComputedGetter(key);
    sharedPropertyDefinition.set = noop;
  } else {
    sharedPropertyDefinition.get = userDef.get
      ? userDef.cache !== false
        ? createComputedGetter(key)
        : userDef.get
      : noop;
    sharedPropertyDefinition.set = userDef.set
      ? userDef.set
      : noop;
  }
  Object.defineProperty(target, key, sharedPropertyDefinition);
}

function createComputedGetter (key) {
  return function computedGetter () {
    var watcher = this._computedWatchers && this._computedWatchers[key];
    if (watcher) {
      if (watcher.dirty) {
        watcher.evaluate();
      }
      if (Dep.target) {
        watcher.depend();
      }
      return watcher.value
    }
  }
}

function initMethods (vm, methods) {
  var props = vm.$options.props;
  for (var key in methods) {
    vm[key] = methods[key] == null ? noop : bind(methods[key], vm);
    if (process.env.NODE_ENV !== 'production') {
      if (methods[key] == null) {
        warn(
          "method \"" + key + "\" has an undefined value in the component definition. " +
          "Did you reference the function correctly?",
          vm
        );
      }
      if (props && hasOwn(props, key)) {
        warn(
          ("method \"" + key + "\" has already been defined as a prop."),
          vm
        );
      }
    }
  }
}

function initWatch (vm, watch) {
  for (var key in watch) {
    var handler = watch[key];
    if (Array.isArray(handler)) {
      for (var i = 0; i < handler.length; i++) {
        createWatcher(vm, key, handler[i]);
      }
    } else {
      createWatcher(vm, key, handler);
    }
  }
}

function createWatcher (vm, key, handler) {
  var options;
  if (isPlainObject(handler)) {
    options = handler;
    handler = handler.handler;
  }
  if (typeof handler === 'string') {
    handler = vm[handler];
  }
  vm.$watch(key, handler, options);
}

function stateMixin (Vue) {
  // flow somehow has problems with directly declared definition object
  // when using Object.defineProperty, so we have to procedurally build up
  // the object here.
  var dataDef = {};
  dataDef.get = function () { return this._data };
  var propsDef = {};
  propsDef.get = function () { return this._props };
  if (process.env.NODE_ENV !== 'production') {
    dataDef.set = function (newData) {
      warn(
        'Avoid replacing instance root $data. ' +
        'Use nested data properties instead.',
        this
      );
    };
    propsDef.set = function () {
      warn("$props is readonly.", this);
    };
  }
  Object.defineProperty(Vue.prototype, '$data', dataDef);
  Object.defineProperty(Vue.prototype, '$props', propsDef);

  Vue.prototype.$set = set;
  Vue.prototype.$delete = del;

  Vue.prototype.$watch = function (
    expOrFn,
    cb,
    options
  ) {
    var vm = this;
    options = options || {};
    options.user = true;
    var watcher = new Watcher(vm, expOrFn, cb, options);
    if (options.immediate) {
      cb.call(vm, watcher.value);
    }
    return function unwatchFn () {
      watcher.teardown();
    }
  };
}

/*  */

var hooks = { init: init, prepatch: prepatch, insert: insert, destroy: destroy };
var hooksToMerge = Object.keys(hooks);

function createComponent (
  Ctor,
  data,
  context,
  children,
  tag
) {
  if (!Ctor) {
    return
  }

  var baseCtor = context.$options._base;
  if (isObject(Ctor)) {
    Ctor = baseCtor.extend(Ctor);
  }

  if (typeof Ctor !== 'function') {
    if (process.env.NODE_ENV !== 'production') {
      warn(("Invalid Component definition: " + (String(Ctor))), context);
    }
    return
  }

  // async component
  if (!Ctor.cid) {
    if (Ctor.resolved) {
      Ctor = Ctor.resolved;
    } else {
      Ctor = resolveAsyncComponent(Ctor, baseCtor, function () {
        // it's ok to queue this on every render because
        // $forceUpdate is buffered by the scheduler.
        context.$forceUpdate();
      });
      if (!Ctor) {
        // return nothing if this is indeed an async component
        // wait for the callback to trigger parent update.
        return
      }
    }
  }

  // resolve constructor options in case global mixins are applied after
  // component constructor creation
  resolveConstructorOptions(Ctor);

  data = data || {};

  // transform component v-model data into props & events
  if (data.model) {
    transformModel(Ctor.options, data);
  }

  // extract props
  var propsData = extractProps(data, Ctor);

  // functional component
  if (Ctor.options.functional) {
    return createFunctionalComponent(Ctor, propsData, data, context, children)
  }

  // extract listeners, since these needs to be treated as
  // child component listeners instead of DOM listeners
  var listeners = data.on;
  // replace with listeners with .native modifier
  data.on = data.nativeOn;

  if (Ctor.options.abstract) {
    // abstract components do not keep anything
    // other than props & listeners
    data = {};
  }

  // merge component management hooks onto the placeholder node
  mergeHooks(data);

  // return a placeholder vnode
  var name = Ctor.options.name || tag;
  var vnode = new VNode(
    ("vue-component-" + (Ctor.cid) + (name ? ("-" + name) : '')),
    data, undefined, undefined, undefined, context,
    { Ctor: Ctor, propsData: propsData, listeners: listeners, tag: tag, children: children }
  );
  return vnode
}

function createFunctionalComponent (
  Ctor,
  propsData,
  data,
  context,
  children
) {
  var props = {};
  var propOptions = Ctor.options.props;
  if (propOptions) {
    for (var key in propOptions) {
      props[key] = validateProp(key, propOptions, propsData);
    }
  }
  // ensure the createElement function in functional components
  // gets a unique context - this is necessary for correct named slot check
  var _context = Object.create(context);
  var h = function (a, b, c, d) { return createElement(_context, a, b, c, d, true); };
  var vnode = Ctor.options.render.call(null, h, {
    props: props,
    data: data,
    parent: context,
    children: children,
    slots: function () { return resolveSlots(children, context); }
  });
  if (vnode instanceof VNode) {
    vnode.functionalContext = context;
    if (data.slot) {
      (vnode.data || (vnode.data = {})).slot = data.slot;
    }
  }
  return vnode
}

function createComponentInstanceForVnode (
  vnode, // we know it's MountedComponentVNode but flow doesn't
  parent, // activeInstance in lifecycle state
  parentElm,
  refElm
) {
  var vnodeComponentOptions = vnode.componentOptions;
  var options = {
    _isComponent: true,
    parent: parent,
    propsData: vnodeComponentOptions.propsData,
    _componentTag: vnodeComponentOptions.tag,
    _parentVnode: vnode,
    _parentListeners: vnodeComponentOptions.listeners,
    _renderChildren: vnodeComponentOptions.children,
    _parentElm: parentElm || null,
    _refElm: refElm || null
  };
  // check inline-template render functions
  var inlineTemplate = vnode.data.inlineTemplate;
  if (inlineTemplate) {
    options.render = inlineTemplate.render;
    options.staticRenderFns = inlineTemplate.staticRenderFns;
  }
  return new vnodeComponentOptions.Ctor(options)
}

function init (
  vnode,
  hydrating,
  parentElm,
  refElm
) {
  if (!vnode.componentInstance || vnode.componentInstance._isDestroyed) {
    var child = vnode.componentInstance = createComponentInstanceForVnode(
      vnode,
      activeInstance,
      parentElm,
      refElm
    );
    child.$mount(hydrating ? vnode.elm : undefined, hydrating);
  } else if (vnode.data.keepAlive) {
    // kept-alive components, treat as a patch
    var mountedNode = vnode; // work around flow
    prepatch(mountedNode, mountedNode);
  }
}

function prepatch (
  oldVnode,
  vnode
) {
  var options = vnode.componentOptions;
  var child = vnode.componentInstance = oldVnode.componentInstance;
  updateChildComponent(
    child,
    options.propsData, // updated props
    options.listeners, // updated listeners
    vnode, // new parent vnode
    options.children // new children
  );
}

function insert (vnode) {
  if (!vnode.componentInstance._isMounted) {
    vnode.componentInstance._isMounted = true;
    callHook(vnode.componentInstance, 'mounted');
  }
  if (vnode.data.keepAlive) {
    activateChildComponent(vnode.componentInstance, true /* direct */);
  }
}

function destroy (vnode) {
  if (!vnode.componentInstance._isDestroyed) {
    if (!vnode.data.keepAlive) {
      vnode.componentInstance.$destroy();
    } else {
      deactivateChildComponent(vnode.componentInstance, true /* direct */);
    }
  }
}

function resolveAsyncComponent (
  factory,
  baseCtor,
  cb
) {
  if (factory.requested) {
    // pool callbacks
    factory.pendingCallbacks.push(cb);
  } else {
    factory.requested = true;
    var cbs = factory.pendingCallbacks = [cb];
    var sync = true;

    var resolve = function (res) {
      if (isObject(res)) {
        res = baseCtor.extend(res);
      }
      // cache resolved
      factory.resolved = res;
      // invoke callbacks only if this is not a synchronous resolve
      // (async resolves are shimmed as synchronous during SSR)
      if (!sync) {
        for (var i = 0, l = cbs.length; i < l; i++) {
          cbs[i](res);
        }
      }
    };

    var reject = function (reason) {
      process.env.NODE_ENV !== 'production' && warn(
        "Failed to resolve async component: " + (String(factory)) +
        (reason ? ("\nReason: " + reason) : '')
      );
    };

    var res = factory(resolve, reject);

    // handle promise
    if (res && typeof res.then === 'function' && !factory.resolved) {
      res.then(resolve, reject);
    }

    sync = false;
    // return in case resolved synchronously
    return factory.resolved
  }
}

function extractProps (data, Ctor) {
  // we are only extracting raw values here.
  // validation and default values are handled in the child
  // component itself.
  var propOptions = Ctor.options.props;
  if (!propOptions) {
    return
  }
  var res = {};
  var attrs = data.attrs;
  var props = data.props;
  var domProps = data.domProps;
  if (attrs || props || domProps) {
    for (var key in propOptions) {
      var altKey = hyphenate(key);
      checkProp(res, props, key, altKey, true) ||
      checkProp(res, attrs, key, altKey) ||
      checkProp(res, domProps, key, altKey);
    }
  }
  return res
}

function checkProp (
  res,
  hash,
  key,
  altKey,
  preserve
) {
  if (hash) {
    if (hasOwn(hash, key)) {
      res[key] = hash[key];
      if (!preserve) {
        delete hash[key];
      }
      return true
    } else if (hasOwn(hash, altKey)) {
      res[key] = hash[altKey];
      if (!preserve) {
        delete hash[altKey];
      }
      return true
    }
  }
  return false
}

function mergeHooks (data) {
  if (!data.hook) {
    data.hook = {};
  }
  for (var i = 0; i < hooksToMerge.length; i++) {
    var key = hooksToMerge[i];
    var fromParent = data.hook[key];
    var ours = hooks[key];
    data.hook[key] = fromParent ? mergeHook$1(ours, fromParent) : ours;
  }
}

function mergeHook$1 (one, two) {
  return function (a, b, c, d) {
    one(a, b, c, d);
    two(a, b, c, d);
  }
}

// transform component v-model info (value and callback) into
// prop and event handler respectively.
function transformModel (options, data) {
  var prop = (options.model && options.model.prop) || 'value';
  var event = (options.model && options.model.event) || 'input';(data.props || (data.props = {}))[prop] = data.model.value;
  var on = data.on || (data.on = {});
  if (on[event]) {
    on[event] = [data.model.callback].concat(on[event]);
  } else {
    on[event] = data.model.callback;
  }
}

/*  */

var SIMPLE_NORMALIZE = 1;
var ALWAYS_NORMALIZE = 2;

// wrapper function for providing a more flexible interface
// without getting yelled at by flow
function createElement (
  context,
  tag,
  data,
  children,
  normalizationType,
  alwaysNormalize
) {
  if (Array.isArray(data) || isPrimitive(data)) {
    normalizationType = children;
    children = data;
    data = undefined;
  }
  if (alwaysNormalize) { normalizationType = ALWAYS_NORMALIZE; }
  return _createElement(context, tag, data, children, normalizationType)
}

function _createElement (
  context,
  tag,
  data,
  children,
  normalizationType
) {
  if (data && data.__ob__) {
    process.env.NODE_ENV !== 'production' && warn(
      "Avoid using observed data object as vnode data: " + (JSON.stringify(data)) + "\n" +
      'Always create fresh vnode data objects in each render!',
      context
    );
    return createEmptyVNode()
  }
  if (!tag) {
    // in case of component :is set to falsy value
    return createEmptyVNode()
  }
  // support single function children as default scoped slot
  if (Array.isArray(children) &&
      typeof children[0] === 'function') {
    data = data || {};
    data.scopedSlots = { default: children[0] };
    children.length = 0;
  }
  if (normalizationType === ALWAYS_NORMALIZE) {
    children = normalizeChildren(children);
  } else if (normalizationType === SIMPLE_NORMALIZE) {
    children = simpleNormalizeChildren(children);
  }
  var vnode, ns;
  if (typeof tag === 'string') {
    var Ctor;
    ns = config.getTagNamespace(tag);
    if (config.isReservedTag(tag)) {
      // platform built-in elements
      vnode = new VNode(
        config.parsePlatformTagName(tag), data, children,
        undefined, undefined, context
      );
    } else if ((Ctor = resolveAsset(context.$options, 'components', tag))) {
      // component
      vnode = createComponent(Ctor, data, context, children, tag);
    } else {
      // unknown or unlisted namespaced elements
      // check at runtime because it may get assigned a namespace when its
      // parent normalizes children
      vnode = new VNode(
        tag, data, children,
        undefined, undefined, context
      );
    }
  } else {
    // direct component options / constructor
    vnode = createComponent(tag, data, context, children);
  }
  if (vnode) {
    if (ns) { applyNS(vnode, ns); }
    return vnode
  } else {
    return createEmptyVNode()
  }
}

function applyNS (vnode, ns) {
  vnode.ns = ns;
  if (vnode.tag === 'foreignObject') {
    // use default namespace inside foreignObject
    return
  }
  if (vnode.children) {
    for (var i = 0, l = vnode.children.length; i < l; i++) {
      var child = vnode.children[i];
      if (child.tag && !child.ns) {
        applyNS(child, ns);
      }
    }
  }
}

/*  */

/**
 * Runtime helper for rendering v-for lists.
 */
function renderList (
  val,
  render
) {
  var ret, i, l, keys, key;
  if (Array.isArray(val) || typeof val === 'string') {
    ret = new Array(val.length);
    for (i = 0, l = val.length; i < l; i++) {
      ret[i] = render(val[i], i);
    }
  } else if (typeof val === 'number') {
    ret = new Array(val);
    for (i = 0; i < val; i++) {
      ret[i] = render(i + 1, i);
    }
  } else if (isObject(val)) {
    keys = Object.keys(val);
    ret = new Array(keys.length);
    for (i = 0, l = keys.length; i < l; i++) {
      key = keys[i];
      ret[i] = render(val[key], key, i);
    }
  }
  return ret
}

/*  */

/**
 * Runtime helper for rendering <slot>
 */
function renderSlot (
  name,
  fallback,
  props,
  bindObject
) {
  var scopedSlotFn = this.$scopedSlots[name];
  if (scopedSlotFn) { // scoped slot
    props = props || {};
    if (bindObject) {
      extend(props, bindObject);
    }
    return scopedSlotFn(props) || fallback
  } else {
    var slotNodes = this.$slots[name];
    // warn duplicate slot usage
    if (slotNodes && process.env.NODE_ENV !== 'production') {
      slotNodes._rendered && warn(
        "Duplicate presence of slot \"" + name + "\" found in the same render tree " +
        "- this will likely cause render errors.",
        this
      );
      slotNodes._rendered = true;
    }
    return slotNodes || fallback
  }
}

/*  */

/**
 * Runtime helper for resolving filters
 */
function resolveFilter (id) {
  return resolveAsset(this.$options, 'filters', id, true) || identity
}

/*  */

/**
 * Runtime helper for checking keyCodes from config.
 */
function checkKeyCodes (
  eventKeyCode,
  key,
  builtInAlias
) {
  var keyCodes = config.keyCodes[key] || builtInAlias;
  if (Array.isArray(keyCodes)) {
    return keyCodes.indexOf(eventKeyCode) === -1
  } else {
    return keyCodes !== eventKeyCode
  }
}

/*  */

/**
 * Runtime helper for merging v-bind="object" into a VNode's data.
 */
function bindObjectProps (
  data,
  tag,
  value,
  asProp
) {
  if (value) {
    if (!isObject(value)) {
      process.env.NODE_ENV !== 'production' && warn(
        'v-bind without argument expects an Object or Array value',
        this
      );
    } else {
      if (Array.isArray(value)) {
        value = toObject(value);
      }
      for (var key in value) {
        if (key === 'class' || key === 'style') {
          data[key] = value[key];
        } else {
          var type = data.attrs && data.attrs.type;
          var hash = asProp || config.mustUseProp(tag, type, key)
            ? data.domProps || (data.domProps = {})
            : data.attrs || (data.attrs = {});
          hash[key] = value[key];
        }
      }
    }
  }
  return data
}

/*  */

/**
 * Runtime helper for rendering static trees.
 */
function renderStatic (
  index,
  isInFor
) {
  var tree = this._staticTrees[index];
  // if has already-rendered static tree and not inside v-for,
  // we can reuse the same tree by doing a shallow clone.
  if (tree && !isInFor) {
    return Array.isArray(tree)
      ? cloneVNodes(tree)
      : cloneVNode(tree)
  }
  // otherwise, render a fresh tree.
  tree = this._staticTrees[index] =
    this.$options.staticRenderFns[index].call(this._renderProxy);
  markStatic(tree, ("__static__" + index), false);
  return tree
}

/**
 * Runtime helper for v-once.
 * Effectively it means marking the node as static with a unique key.
 */
function markOnce (
  tree,
  index,
  key
) {
  markStatic(tree, ("__once__" + index + (key ? ("_" + key) : "")), true);
  return tree
}

function markStatic (
  tree,
  key,
  isOnce
) {
  if (Array.isArray(tree)) {
    for (var i = 0; i < tree.length; i++) {
      if (tree[i] && typeof tree[i] !== 'string') {
        markStaticNode(tree[i], (key + "_" + i), isOnce);
      }
    }
  } else {
    markStaticNode(tree, key, isOnce);
  }
}

function markStaticNode (node, key, isOnce) {
  node.isStatic = true;
  node.key = key;
  node.isOnce = isOnce;
}

/*  */

function initRender (vm) {
  vm.$vnode = null; // the placeholder node in parent tree
  vm._vnode = null; // the root of the child tree
  vm._staticTrees = null;
  var parentVnode = vm.$options._parentVnode;
  var renderContext = parentVnode && parentVnode.context;
  vm.$slots = resolveSlots(vm.$options._renderChildren, renderContext);
  vm.$scopedSlots = emptyObject;
  // bind the createElement fn to this instance
  // so that we get proper render context inside it.
  // args order: tag, data, children, normalizationType, alwaysNormalize
  // internal version is used by render functions compiled from templates
  vm._c = function (a, b, c, d) { return createElement(vm, a, b, c, d, false); };
  // normalization is always applied for the public version, used in
  // user-written render functions.
  vm.$createElement = function (a, b, c, d) { return createElement(vm, a, b, c, d, true); };
}

function renderMixin (Vue) {
  Vue.prototype.$nextTick = function (fn) {
    return nextTick(fn, this)
  };

  Vue.prototype._render = function () {
    var vm = this;
    var ref = vm.$options;
    var render = ref.render;
    var staticRenderFns = ref.staticRenderFns;
    var _parentVnode = ref._parentVnode;

    if (vm._isMounted) {
      // clone slot nodes on re-renders
      for (var key in vm.$slots) {
        vm.$slots[key] = cloneVNodes(vm.$slots[key]);
      }
    }

    vm.$scopedSlots = (_parentVnode && _parentVnode.data.scopedSlots) || emptyObject;

    if (staticRenderFns && !vm._staticTrees) {
      vm._staticTrees = [];
    }
    // set parent vnode. this allows render functions to have access
    // to the data on the placeholder node.
    vm.$vnode = _parentVnode;
    // render self
    var vnode;
    try {
      vnode = render.call(vm._renderProxy, vm.$createElement);
    } catch (e) {
      handleError(e, vm, "render function");
      // return error render result,
      // or previous vnode to prevent render error causing blank component
      /* istanbul ignore else */
      if (process.env.NODE_ENV !== 'production') {
        vnode = vm.$options.renderError
          ? vm.$options.renderError.call(vm._renderProxy, vm.$createElement, e)
          : vm._vnode;
      } else {
        vnode = vm._vnode;
      }
    }
    // return empty vnode in case the render function errored out
    if (!(vnode instanceof VNode)) {
      if (process.env.NODE_ENV !== 'production' && Array.isArray(vnode)) {
        warn(
          'Multiple root nodes returned from render function. Render function ' +
          'should return a single root node.',
          vm
        );
      }
      vnode = createEmptyVNode();
    }
    // set parent
    vnode.parent = _parentVnode;
    return vnode
  };

  // internal render helpers.
  // these are exposed on the instance prototype to reduce generated render
  // code size.
  Vue.prototype._o = markOnce;
  Vue.prototype._n = toNumber;
  Vue.prototype._s = _toString;
  Vue.prototype._l = renderList;
  Vue.prototype._t = renderSlot;
  Vue.prototype._q = looseEqual;
  Vue.prototype._i = looseIndexOf;
  Vue.prototype._m = renderStatic;
  Vue.prototype._f = resolveFilter;
  Vue.prototype._k = checkKeyCodes;
  Vue.prototype._b = bindObjectProps;
  Vue.prototype._v = createTextVNode;
  Vue.prototype._e = createEmptyVNode;
  Vue.prototype._u = resolveScopedSlots;
}

/*  */

function initInjections (vm) {
  var provide = vm.$options.provide;
  var inject = vm.$options.inject;
  if (provide) {
    vm._provided = typeof provide === 'function'
      ? provide.call(vm)
      : provide;
  }
  if (inject) {
    // inject is :any because flow is not smart enough to figure out cached
    // isArray here
    var isArray = Array.isArray(inject);
    var keys = isArray
      ? inject
      : hasSymbol
        ? Reflect.ownKeys(inject)
        : Object.keys(inject);

    for (var i = 0; i < keys.length; i++) {
      var key = keys[i];
      var provideKey = isArray ? key : inject[key];
      var source = vm;
      while (source) {
        if (source._provided && source._provided[provideKey]) {
          vm[key] = source._provided[provideKey];
          break
        }
        source = source.$parent;
      }
    }
  }
}

/*  */

var uid = 0;

function initMixin (Vue) {
  Vue.prototype._init = function (options) {
    /* istanbul ignore if */
    if (process.env.NODE_ENV !== 'production' && config.performance && perf) {
      perf.mark('init');
    }

    var vm = this;
    // a uid
    vm._uid = uid++;
    // a flag to avoid this being observed
    vm._isVue = true;
    // merge options
    if (options && options._isComponent) {
      // optimize internal component instantiation
      // since dynamic options merging is pretty slow, and none of the
      // internal component options needs special treatment.
      initInternalComponent(vm, options);
    } else {
      vm.$options = mergeOptions(
        resolveConstructorOptions(vm.constructor),
        options || {},
        vm
      );
    }
    /* istanbul ignore else */
    if (process.env.NODE_ENV !== 'production') {
      initProxy(vm);
    } else {
      vm._renderProxy = vm;
    }
    // expose real self
    vm._self = vm;
    initLifecycle(vm);
    initEvents(vm);
    initRender(vm);
    callHook(vm, 'beforeCreate');
    initState(vm);
    initInjections(vm);
    callHook(vm, 'created');

    /* istanbul ignore if */
    if (process.env.NODE_ENV !== 'production' && config.performance && perf) {
      vm._name = formatComponentName(vm, false);
      perf.mark('init end');
      perf.measure(((vm._name) + " init"), 'init', 'init end');
    }

    if (vm.$options.el) {
      vm.$mount(vm.$options.el);
    }
  };
}

function initInternalComponent (vm, options) {
  var opts = vm.$options = Object.create(vm.constructor.options);
  // doing this because it's faster than dynamic enumeration.
  opts.parent = options.parent;
  opts.propsData = options.propsData;
  opts._parentVnode = options._parentVnode;
  opts._parentListeners = options._parentListeners;
  opts._renderChildren = options._renderChildren;
  opts._componentTag = options._componentTag;
  opts._parentElm = options._parentElm;
  opts._refElm = options._refElm;
  if (options.render) {
    opts.render = options.render;
    opts.staticRenderFns = options.staticRenderFns;
  }
}

function resolveConstructorOptions (Ctor) {
  var options = Ctor.options;
  if (Ctor.super) {
    var superOptions = resolveConstructorOptions(Ctor.super);
    var cachedSuperOptions = Ctor.superOptions;
    if (superOptions !== cachedSuperOptions) {
      // super option changed,
      // need to resolve new options.
      Ctor.superOptions = superOptions;
      // check if there are any late-modified/attached options (#4976)
      var modifiedOptions = resolveModifiedOptions(Ctor);
      // update base extend options
      if (modifiedOptions) {
        extend(Ctor.extendOptions, modifiedOptions);
      }
      options = Ctor.options = mergeOptions(superOptions, Ctor.extendOptions);
      if (options.name) {
        options.components[options.name] = Ctor;
      }
    }
  }
  return options
}

function resolveModifiedOptions (Ctor) {
  var modified;
  var latest = Ctor.options;
  var sealed = Ctor.sealedOptions;
  for (var key in latest) {
    if (latest[key] !== sealed[key]) {
      if (!modified) { modified = {}; }
      modified[key] = dedupe(latest[key], sealed[key]);
    }
  }
  return modified
}

function dedupe (latest, sealed) {
  // compare latest and sealed to ensure lifecycle hooks won't be duplicated
  // between merges
  if (Array.isArray(latest)) {
    var res = [];
    sealed = Array.isArray(sealed) ? sealed : [sealed];
    for (var i = 0; i < latest.length; i++) {
      if (sealed.indexOf(latest[i]) < 0) {
        res.push(latest[i]);
      }
    }
    return res
  } else {
    return latest
  }
}

function Vue$3 (options) {
  if (process.env.NODE_ENV !== 'production' &&
    !(this instanceof Vue$3)) {
    warn('Vue is a constructor and should be called with the `new` keyword');
  }
  this._init(options);
}

initMixin(Vue$3);
stateMixin(Vue$3);
eventsMixin(Vue$3);
lifecycleMixin(Vue$3);
renderMixin(Vue$3);

/*  */

function initUse (Vue) {
  Vue.use = function (plugin) {
    /* istanbul ignore if */
    if (plugin.installed) {
      return
    }
    // additional parameters
    var args = toArray(arguments, 1);
    args.unshift(this);
    if (typeof plugin.install === 'function') {
      plugin.install.apply(plugin, args);
    } else if (typeof plugin === 'function') {
      plugin.apply(null, args);
    }
    plugin.installed = true;
    return this
  };
}

/*  */

function initMixin$1 (Vue) {
  Vue.mixin = function (mixin) {
    this.options = mergeOptions(this.options, mixin);
  };
}

/*  */

function initExtend (Vue) {
  /**
   * Each instance constructor, including Vue, has a unique
   * cid. This enables us to create wrapped "child
   * constructors" for prototypal inheritance and cache them.
   */
  Vue.cid = 0;
  var cid = 1;

  /**
   * Class inheritance
   */
  Vue.extend = function (extendOptions) {
    extendOptions = extendOptions || {};
    var Super = this;
    var SuperId = Super.cid;
    var cachedCtors = extendOptions._Ctor || (extendOptions._Ctor = {});
    if (cachedCtors[SuperId]) {
      return cachedCtors[SuperId]
    }

    var name = extendOptions.name || Super.options.name;
    if (process.env.NODE_ENV !== 'production') {
      if (!/^[a-zA-Z][\w-]*$/.test(name)) {
        warn(
          'Invalid component name: "' + name + '". Component names ' +
          'can only contain alphanumeric characters and the hyphen, ' +
          'and must start with a letter.'
        );
      }
    }

    var Sub = function VueComponent (options) {
      this._init(options);
    };
    Sub.prototype = Object.create(Super.prototype);
    Sub.prototype.constructor = Sub;
    Sub.cid = cid++;
    Sub.options = mergeOptions(
      Super.options,
      extendOptions
    );
    Sub['super'] = Super;

    // For props and computed properties, we define the proxy getters on
    // the Vue instances at extension time, on the extended prototype. This
    // avoids Object.defineProperty calls for each instance created.
    if (Sub.options.props) {
      initProps$1(Sub);
    }
    if (Sub.options.computed) {
      initComputed$1(Sub);
    }

    // allow further extension/mixin/plugin usage
    Sub.extend = Super.extend;
    Sub.mixin = Super.mixin;
    Sub.use = Super.use;

    // create asset registers, so extended classes
    // can have their private assets too.
    config._assetTypes.forEach(function (type) {
      Sub[type] = Super[type];
    });
    // enable recursive self-lookup
    if (name) {
      Sub.options.components[name] = Sub;
    }

    // keep a reference to the super options at extension time.
    // later at instantiation we can check if Super's options have
    // been updated.
    Sub.superOptions = Super.options;
    Sub.extendOptions = extendOptions;
    Sub.sealedOptions = extend({}, Sub.options);

    // cache constructor
    cachedCtors[SuperId] = Sub;
    return Sub
  };
}

function initProps$1 (Comp) {
  var props = Comp.options.props;
  for (var key in props) {
    proxy(Comp.prototype, "_props", key);
  }
}

function initComputed$1 (Comp) {
  var computed = Comp.options.computed;
  for (var key in computed) {
    defineComputed(Comp.prototype, key, computed[key]);
  }
}

/*  */

function initAssetRegisters (Vue) {
  /**
   * Create asset registration methods.
   */
  config._assetTypes.forEach(function (type) {
    Vue[type] = function (
      id,
      definition
    ) {
      if (!definition) {
        return this.options[type + 's'][id]
      } else {
        /* istanbul ignore if */
        if (process.env.NODE_ENV !== 'production') {
          if (type === 'component' && config.isReservedTag(id)) {
            warn(
              'Do not use built-in or reserved HTML elements as component ' +
              'id: ' + id
            );
          }
        }
        if (type === 'component' && isPlainObject(definition)) {
          definition.name = definition.name || id;
          definition = this.options._base.extend(definition);
        }
        if (type === 'directive' && typeof definition === 'function') {
          definition = { bind: definition, update: definition };
        }
        this.options[type + 's'][id] = definition;
        return definition
      }
    };
  });
}

/*  */

var patternTypes = [String, RegExp];

function getComponentName (opts) {
  return opts && (opts.Ctor.options.name || opts.tag)
}

function matches (pattern, name) {
  if (typeof pattern === 'string') {
    return pattern.split(',').indexOf(name) > -1
  } else if (pattern instanceof RegExp) {
    return pattern.test(name)
  }
  /* istanbul ignore next */
  return false
}

function pruneCache (cache, filter) {
  for (var key in cache) {
    var cachedNode = cache[key];
    if (cachedNode) {
      var name = getComponentName(cachedNode.componentOptions);
      if (name && !filter(name)) {
        pruneCacheEntry(cachedNode);
        cache[key] = null;
      }
    }
  }
}

function pruneCacheEntry (vnode) {
  if (vnode) {
    if (!vnode.componentInstance._inactive) {
      callHook(vnode.componentInstance, 'deactivated');
    }
    vnode.componentInstance.$destroy();
  }
}

var KeepAlive = {
  name: 'keep-alive',
  abstract: true,

  props: {
    include: patternTypes,
    exclude: patternTypes
  },

  created: function created () {
    this.cache = Object.create(null);
  },

  destroyed: function destroyed () {
    var this$1 = this;

    for (var key in this$1.cache) {
      pruneCacheEntry(this$1.cache[key]);
    }
  },

  watch: {
    include: function include (val) {
      pruneCache(this.cache, function (name) { return matches(val, name); });
    },
    exclude: function exclude (val) {
      pruneCache(this.cache, function (name) { return !matches(val, name); });
    }
  },

  render: function render () {
    var vnode = getFirstComponentChild(this.$slots.default);
    var componentOptions = vnode && vnode.componentOptions;
    if (componentOptions) {
      // check pattern
      var name = getComponentName(componentOptions);
      if (name && (
        (this.include && !matches(this.include, name)) ||
        (this.exclude && matches(this.exclude, name))
      )) {
        return vnode
      }
      var key = vnode.key == null
        // same constructor may get registered as different local components
        // so cid alone is not enough (#3269)
        ? componentOptions.Ctor.cid + (componentOptions.tag ? ("::" + (componentOptions.tag)) : '')
        : vnode.key;
      if (this.cache[key]) {
        vnode.componentInstance = this.cache[key].componentInstance;
      } else {
        this.cache[key] = vnode;
      }
      vnode.data.keepAlive = true;
    }
    return vnode
  }
};

var builtInComponents = {
  KeepAlive: KeepAlive
};

/*  */

function initGlobalAPI (Vue) {
  // config
  var configDef = {};
  configDef.get = function () { return config; };
  if (process.env.NODE_ENV !== 'production') {
    configDef.set = function () {
      warn(
        'Do not replace the Vue.config object, set individual fields instead.'
      );
    };
  }
  Object.defineProperty(Vue, 'config', configDef);

  // exposed util methods.
  // NOTE: these are not considered part of the public API - avoid relying on
  // them unless you are aware of the risk.
  Vue.util = {
    warn: warn,
    extend: extend,
    mergeOptions: mergeOptions,
    defineReactive: defineReactive$$1
  };

  Vue.set = set;
  Vue.delete = del;
  Vue.nextTick = nextTick;

  Vue.options = Object.create(null);
  config._assetTypes.forEach(function (type) {
    Vue.options[type + 's'] = Object.create(null);
  });

  // this is used to identify the "base" constructor to extend all plain-object
  // components with in Weex's multi-instance scenarios.
  Vue.options._base = Vue;

  extend(Vue.options.components, builtInComponents);

  initUse(Vue);
  initMixin$1(Vue);
  initExtend(Vue);
  initAssetRegisters(Vue);
}

initGlobalAPI(Vue$3);

Object.defineProperty(Vue$3.prototype, '$isServer', {
  get: isServerRendering
});

Vue$3.version = '2.2.1';

/*  */

// attributes that should be using props for binding
var acceptValue = makeMap('input,textarea,option,select');
var mustUseProp = function (tag, type, attr) {
  return (
    (attr === 'value' && acceptValue(tag)) && type !== 'button' ||
    (attr === 'selected' && tag === 'option') ||
    (attr === 'checked' && tag === 'input') ||
    (attr === 'muted' && tag === 'video')
  )
};

var isEnumeratedAttr = makeMap('contenteditable,draggable,spellcheck');

var isBooleanAttr = makeMap(
  'allowfullscreen,async,autofocus,autoplay,checked,compact,controls,declare,' +
  'default,defaultchecked,defaultmuted,defaultselected,defer,disabled,' +
  'enabled,formnovalidate,hidden,indeterminate,inert,ismap,itemscope,loop,multiple,' +
  'muted,nohref,noresize,noshade,novalidate,nowrap,open,pauseonexit,readonly,' +
  'required,reversed,scoped,seamless,selected,sortable,translate,' +
  'truespeed,typemustmatch,visible'
);

var xlinkNS = 'http://www.w3.org/1999/xlink';

var isXlink = function (name) {
  return name.charAt(5) === ':' && name.slice(0, 5) === 'xlink'
};

var getXlinkProp = function (name) {
  return isXlink(name) ? name.slice(6, name.length) : ''
};

var isFalsyAttrValue = function (val) {
  return val == null || val === false
};

/*  */

function genClassForVnode (vnode) {
  var data = vnode.data;
  var parentNode = vnode;
  var childNode = vnode;
  while (childNode.componentInstance) {
    childNode = childNode.componentInstance._vnode;
    if (childNode.data) {
      data = mergeClassData(childNode.data, data);
    }
  }
  while ((parentNode = parentNode.parent)) {
    if (parentNode.data) {
      data = mergeClassData(data, parentNode.data);
    }
  }
  return genClassFromData(data)
}

function mergeClassData (child, parent) {
  return {
    staticClass: concat(child.staticClass, parent.staticClass),
    class: child.class
      ? [child.class, parent.class]
      : parent.class
  }
}

function genClassFromData (data) {
  var dynamicClass = data.class;
  var staticClass = data.staticClass;
  if (staticClass || dynamicClass) {
    return concat(staticClass, stringifyClass(dynamicClass))
  }
  /* istanbul ignore next */
  return ''
}

function concat (a, b) {
  return a ? b ? (a + ' ' + b) : a : (b || '')
}

function stringifyClass (value) {
  var res = '';
  if (!value) {
    return res
  }
  if (typeof value === 'string') {
    return value
  }
  if (Array.isArray(value)) {
    var stringified;
    for (var i = 0, l = value.length; i < l; i++) {
      if (value[i]) {
        if ((stringified = stringifyClass(value[i]))) {
          res += stringified + ' ';
        }
      }
    }
    return res.slice(0, -1)
  }
  if (isObject(value)) {
    for (var key in value) {
      if (value[key]) { res += key + ' '; }
    }
    return res.slice(0, -1)
  }
  /* istanbul ignore next */
  return res
}

/*  */

var namespaceMap = {
  svg: 'http://www.w3.org/2000/svg',
  math: 'http://www.w3.org/1998/Math/MathML'
};

var isHTMLTag = makeMap(
  'html,body,base,head,link,meta,style,title,' +
  'address,article,aside,footer,header,h1,h2,h3,h4,h5,h6,hgroup,nav,section,' +
  'div,dd,dl,dt,figcaption,figure,hr,img,li,main,ol,p,pre,ul,' +
  'a,b,abbr,bdi,bdo,br,cite,code,data,dfn,em,i,kbd,mark,q,rp,rt,rtc,ruby,' +
  's,samp,small,span,strong,sub,sup,time,u,var,wbr,area,audio,map,track,video,' +
  'embed,object,param,source,canvas,script,noscript,del,ins,' +
  'caption,col,colgroup,table,thead,tbody,td,th,tr,' +
  'button,datalist,fieldset,form,input,label,legend,meter,optgroup,option,' +
  'output,progress,select,textarea,' +
  'details,dialog,menu,menuitem,summary,' +
  'content,element,shadow,template'
);

// this map is intentionally selective, only covering SVG elements that may
// contain child elements.
var isSVG = makeMap(
  'svg,animate,circle,clippath,cursor,defs,desc,ellipse,filter,font-face,' +
  'foreignObject,g,glyph,image,line,marker,mask,missing-glyph,path,pattern,' +
  'polygon,polyline,rect,switch,symbol,text,textpath,tspan,use,view',
  true
);

var isPreTag = function (tag) { return tag === 'pre'; };

var isReservedTag = function (tag) {
  return isHTMLTag(tag) || isSVG(tag)
};

function getTagNamespace (tag) {
  if (isSVG(tag)) {
    return 'svg'
  }
  // basic support for MathML
  // note it doesn't support other MathML elements being component roots
  if (tag === 'math') {
    return 'math'
  }
}

var unknownElementCache = Object.create(null);
function isUnknownElement (tag) {
  /* istanbul ignore if */
  if (!inBrowser) {
    return true
  }
  if (isReservedTag(tag)) {
    return false
  }
  tag = tag.toLowerCase();
  /* istanbul ignore if */
  if (unknownElementCache[tag] != null) {
    return unknownElementCache[tag]
  }
  var el = document.createElement(tag);
  if (tag.indexOf('-') > -1) {
    // http://stackoverflow.com/a/28210364/1070244
    return (unknownElementCache[tag] = (
      el.constructor === window.HTMLUnknownElement ||
      el.constructor === window.HTMLElement
    ))
  } else {
    return (unknownElementCache[tag] = /HTMLUnknownElement/.test(el.toString()))
  }
}

/*  */

/**
 * Query an element selector if it's not an element already.
 */
function query (el) {
  if (typeof el === 'string') {
    var selected = document.querySelector(el);
    if (!selected) {
      process.env.NODE_ENV !== 'production' && warn(
        'Cannot find element: ' + el
      );
      return document.createElement('div')
    }
    return selected
  } else {
    return el
  }
}

/*  */

function createElement$1 (tagName, vnode) {
  var elm = document.createElement(tagName);
  if (tagName !== 'select') {
    return elm
  }
  // false or null will remove the attribute but undefined will not
  if (vnode.data && vnode.data.attrs && vnode.data.attrs.multiple !== undefined) {
    elm.setAttribute('multiple', 'multiple');
  }
  return elm
}

function createElementNS (namespace, tagName) {
  return document.createElementNS(namespaceMap[namespace], tagName)
}

function createTextNode (text) {
  return document.createTextNode(text)
}

function createComment (text) {
  return document.createComment(text)
}

function insertBefore (parentNode, newNode, referenceNode) {
  parentNode.insertBefore(newNode, referenceNode);
}

function removeChild (node, child) {
  node.removeChild(child);
}

function appendChild (node, child) {
  node.appendChild(child);
}

function parentNode (node) {
  return node.parentNode
}

function nextSibling (node) {
  return node.nextSibling
}

function tagName (node) {
  return node.tagName
}

function setTextContent (node, text) {
  node.textContent = text;
}

function setAttribute (node, key, val) {
  node.setAttribute(key, val);
}


var nodeOps = Object.freeze({
	createElement: createElement$1,
	createElementNS: createElementNS,
	createTextNode: createTextNode,
	createComment: createComment,
	insertBefore: insertBefore,
	removeChild: removeChild,
	appendChild: appendChild,
	parentNode: parentNode,
	nextSibling: nextSibling,
	tagName: tagName,
	setTextContent: setTextContent,
	setAttribute: setAttribute
});

/*  */

var ref = {
  create: function create (_, vnode) {
    registerRef(vnode);
  },
  update: function update (oldVnode, vnode) {
    if (oldVnode.data.ref !== vnode.data.ref) {
      registerRef(oldVnode, true);
      registerRef(vnode);
    }
  },
  destroy: function destroy (vnode) {
    registerRef(vnode, true);
  }
};

function registerRef (vnode, isRemoval) {
  var key = vnode.data.ref;
  if (!key) { return }

  var vm = vnode.context;
  var ref = vnode.componentInstance || vnode.elm;
  var refs = vm.$refs;
  if (isRemoval) {
    if (Array.isArray(refs[key])) {
      remove(refs[key], ref);
    } else if (refs[key] === ref) {
      refs[key] = undefined;
    }
  } else {
    if (vnode.data.refInFor) {
      if (Array.isArray(refs[key]) && refs[key].indexOf(ref) < 0) {
        refs[key].push(ref);
      } else {
        refs[key] = [ref];
      }
    } else {
      refs[key] = ref;
    }
  }
}

/**
 * Virtual DOM patching algorithm based on Snabbdom by
 * Simon Friis Vindum (@paldepind)
 * Licensed under the MIT License
 * https://github.com/paldepind/snabbdom/blob/master/LICENSE
 *
 * modified by Evan You (@yyx990803)
 *

/*
 * Not type-checking this because this file is perf-critical and the cost
 * of making flow understand it is not worth it.
 */

var emptyNode = new VNode('', {}, []);

var hooks$1 = ['create', 'activate', 'update', 'remove', 'destroy'];

function isUndef (s) {
  return s == null
}

function isDef (s) {
  return s != null
}

function sameVnode (vnode1, vnode2) {
  return (
    vnode1.key === vnode2.key &&
    vnode1.tag === vnode2.tag &&
    vnode1.isComment === vnode2.isComment &&
    !vnode1.data === !vnode2.data
  )
}

function createKeyToOldIdx (children, beginIdx, endIdx) {
  var i, key;
  var map = {};
  for (i = beginIdx; i <= endIdx; ++i) {
    key = children[i].key;
    if (isDef(key)) { map[key] = i; }
  }
  return map
}

function createPatchFunction (backend) {
  var i, j;
  var cbs = {};

  var modules = backend.modules;
  var nodeOps = backend.nodeOps;

  for (i = 0; i < hooks$1.length; ++i) {
    cbs[hooks$1[i]] = [];
    for (j = 0; j < modules.length; ++j) {
      if (modules[j][hooks$1[i]] !== undefined) { cbs[hooks$1[i]].push(modules[j][hooks$1[i]]); }
    }
  }

  function emptyNodeAt (elm) {
    return new VNode(nodeOps.tagName(elm).toLowerCase(), {}, [], undefined, elm)
  }

  function createRmCb (childElm, listeners) {
    function remove$$1 () {
      if (--remove$$1.listeners === 0) {
        removeNode(childElm);
      }
    }
    remove$$1.listeners = listeners;
    return remove$$1
  }

  function removeNode (el) {
    var parent = nodeOps.parentNode(el);
    // element may have already been removed due to v-html / v-text
    if (parent) {
      nodeOps.removeChild(parent, el);
    }
  }

  var inPre = 0;
  function createElm (vnode, insertedVnodeQueue, parentElm, refElm, nested) {
    vnode.isRootInsert = !nested; // for transition enter check
    if (createComponent(vnode, insertedVnodeQueue, parentElm, refElm)) {
      return
    }

    var data = vnode.data;
    var children = vnode.children;
    var tag = vnode.tag;
    if (isDef(tag)) {
      if (process.env.NODE_ENV !== 'production') {
        if (data && data.pre) {
          inPre++;
        }
        if (
          !inPre &&
          !vnode.ns &&
          !(config.ignoredElements.length && config.ignoredElements.indexOf(tag) > -1) &&
          config.isUnknownElement(tag)
        ) {
          warn(
            'Unknown custom element: <' + tag + '> - did you ' +
            'register the component correctly? For recursive components, ' +
            'make sure to provide the "name" option.',
            vnode.context
          );
        }
      }
      vnode.elm = vnode.ns
        ? nodeOps.createElementNS(vnode.ns, tag)
        : nodeOps.createElement(tag, vnode);
      setScope(vnode);

      /* istanbul ignore if */
      {
        createChildren(vnode, children, insertedVnodeQueue);
        if (isDef(data)) {
          invokeCreateHooks(vnode, insertedVnodeQueue);
        }
        insert(parentElm, vnode.elm, refElm);
      }

      if (process.env.NODE_ENV !== 'production' && data && data.pre) {
        inPre--;
      }
    } else if (vnode.isComment) {
      vnode.elm = nodeOps.createComment(vnode.text);
      insert(parentElm, vnode.elm, refElm);
    } else {
      vnode.elm = nodeOps.createTextNode(vnode.text);
      insert(parentElm, vnode.elm, refElm);
    }
  }

  function createComponent (vnode, insertedVnodeQueue, parentElm, refElm) {
    var i = vnode.data;
    if (isDef(i)) {
      var isReactivated = isDef(vnode.componentInstance) && i.keepAlive;
      if (isDef(i = i.hook) && isDef(i = i.init)) {
        i(vnode, false /* hydrating */, parentElm, refElm);
      }
      // after calling the init hook, if the vnode is a child component
      // it should've created a child instance and mounted it. the child
      // component also has set the placeholder vnode's elm.
      // in that case we can just return the element and be done.
      if (isDef(vnode.componentInstance)) {
        initComponent(vnode, insertedVnodeQueue);
        if (isReactivated) {
          reactivateComponent(vnode, insertedVnodeQueue, parentElm, refElm);
        }
        return true
      }
    }
  }

  function initComponent (vnode, insertedVnodeQueue) {
    if (vnode.data.pendingInsert) {
      insertedVnodeQueue.push.apply(insertedVnodeQueue, vnode.data.pendingInsert);
    }
    vnode.elm = vnode.componentInstance.$el;
    if (isPatchable(vnode)) {
      invokeCreateHooks(vnode, insertedVnodeQueue);
      setScope(vnode);
    } else {
      // empty component root.
      // skip all element-related modules except for ref (#3455)
      registerRef(vnode);
      // make sure to invoke the insert hook
      insertedVnodeQueue.push(vnode);
    }
  }

  function reactivateComponent (vnode, insertedVnodeQueue, parentElm, refElm) {
    var i;
    // hack for #4339: a reactivated component with inner transition
    // does not trigger because the inner node's created hooks are not called
    // again. It's not ideal to involve module-specific logic in here but
    // there doesn't seem to be a better way to do it.
    var innerNode = vnode;
    while (innerNode.componentInstance) {
      innerNode = innerNode.componentInstance._vnode;
      if (isDef(i = innerNode.data) && isDef(i = i.transition)) {
        for (i = 0; i < cbs.activate.length; ++i) {
          cbs.activate[i](emptyNode, innerNode);
        }
        insertedVnodeQueue.push(innerNode);
        break
      }
    }
    // unlike a newly created component,
    // a reactivated keep-alive component doesn't insert itself
    insert(parentElm, vnode.elm, refElm);
  }

  function insert (parent, elm, ref) {
    if (parent) {
      if (ref) {
        nodeOps.insertBefore(parent, elm, ref);
      } else {
        nodeOps.appendChild(parent, elm);
      }
    }
  }

  function createChildren (vnode, children, insertedVnodeQueue) {
    if (Array.isArray(children)) {
      for (var i = 0; i < children.length; ++i) {
        createElm(children[i], insertedVnodeQueue, vnode.elm, null, true);
      }
    } else if (isPrimitive(vnode.text)) {
      nodeOps.appendChild(vnode.elm, nodeOps.createTextNode(vnode.text));
    }
  }

  function isPatchable (vnode) {
    while (vnode.componentInstance) {
      vnode = vnode.componentInstance._vnode;
    }
    return isDef(vnode.tag)
  }

  function invokeCreateHooks (vnode, insertedVnodeQueue) {
    for (var i$1 = 0; i$1 < cbs.create.length; ++i$1) {
      cbs.create[i$1](emptyNode, vnode);
    }
    i = vnode.data.hook; // Reuse variable
    if (isDef(i)) {
      if (i.create) { i.create(emptyNode, vnode); }
      if (i.insert) { insertedVnodeQueue.push(vnode); }
    }
  }

  // set scope id attribute for scoped CSS.
  // this is implemented as a special case to avoid the overhead
  // of going through the normal attribute patching process.
  function setScope (vnode) {
    var i;
    var ancestor = vnode;
    while (ancestor) {
      if (isDef(i = ancestor.context) && isDef(i = i.$options._scopeId)) {
        nodeOps.setAttribute(vnode.elm, i, '');
      }
      ancestor = ancestor.parent;
    }
    // for slot content they should also get the scopeId from the host instance.
    if (isDef(i = activeInstance) &&
        i !== vnode.context &&
        isDef(i = i.$options._scopeId)) {
      nodeOps.setAttribute(vnode.elm, i, '');
    }
  }

  function addVnodes (parentElm, refElm, vnodes, startIdx, endIdx, insertedVnodeQueue) {
    for (; startIdx <= endIdx; ++startIdx) {
      createElm(vnodes[startIdx], insertedVnodeQueue, parentElm, refElm);
    }
  }

  function invokeDestroyHook (vnode) {
    var i, j;
    var data = vnode.data;
    if (isDef(data)) {
      if (isDef(i = data.hook) && isDef(i = i.destroy)) { i(vnode); }
      for (i = 0; i < cbs.destroy.length; ++i) { cbs.destroy[i](vnode); }
    }
    if (isDef(i = vnode.children)) {
      for (j = 0; j < vnode.children.length; ++j) {
        invokeDestroyHook(vnode.children[j]);
      }
    }
  }

  function removeVnodes (parentElm, vnodes, startIdx, endIdx) {
    for (; startIdx <= endIdx; ++startIdx) {
      var ch = vnodes[startIdx];
      if (isDef(ch)) {
        if (isDef(ch.tag)) {
          removeAndInvokeRemoveHook(ch);
          invokeDestroyHook(ch);
        } else { // Text node
          removeNode(ch.elm);
        }
      }
    }
  }

  function removeAndInvokeRemoveHook (vnode, rm) {
    if (rm || isDef(vnode.data)) {
      var listeners = cbs.remove.length + 1;
      if (!rm) {
        // directly removing
        rm = createRmCb(vnode.elm, listeners);
      } else {
        // we have a recursively passed down rm callback
        // increase the listeners count
        rm.listeners += listeners;
      }
      // recursively invoke hooks on child component root node
      if (isDef(i = vnode.componentInstance) && isDef(i = i._vnode) && isDef(i.data)) {
        removeAndInvokeRemoveHook(i, rm);
      }
      for (i = 0; i < cbs.remove.length; ++i) {
        cbs.remove[i](vnode, rm);
      }
      if (isDef(i = vnode.data.hook) && isDef(i = i.remove)) {
        i(vnode, rm);
      } else {
        rm();
      }
    } else {
      removeNode(vnode.elm);
    }
  }

  function updateChildren (parentElm, oldCh, newCh, insertedVnodeQueue, removeOnly) {
    var oldStartIdx = 0;
    var newStartIdx = 0;
    var oldEndIdx = oldCh.length - 1;
    var oldStartVnode = oldCh[0];
    var oldEndVnode = oldCh[oldEndIdx];
    var newEndIdx = newCh.length - 1;
    var newStartVnode = newCh[0];
    var newEndVnode = newCh[newEndIdx];
    var oldKeyToIdx, idxInOld, elmToMove, refElm;

    // removeOnly is a special flag used only by <transition-group>
    // to ensure removed elements stay in correct relative positions
    // during leaving transitions
    var canMove = !removeOnly;

    while (oldStartIdx <= oldEndIdx && newStartIdx <= newEndIdx) {
      if (isUndef(oldStartVnode)) {
        oldStartVnode = oldCh[++oldStartIdx]; // Vnode has been moved left
      } else if (isUndef(oldEndVnode)) {
        oldEndVnode = oldCh[--oldEndIdx];
      } else if (sameVnode(oldStartVnode, newStartVnode)) {
        patchVnode(oldStartVnode, newStartVnode, insertedVnodeQueue);
        oldStartVnode = oldCh[++oldStartIdx];
        newStartVnode = newCh[++newStartIdx];
      } else if (sameVnode(oldEndVnode, newEndVnode)) {
        patchVnode(oldEndVnode, newEndVnode, insertedVnodeQueue);
        oldEndVnode = oldCh[--oldEndIdx];
        newEndVnode = newCh[--newEndIdx];
      } else if (sameVnode(oldStartVnode, newEndVnode)) { // Vnode moved right
        patchVnode(oldStartVnode, newEndVnode, insertedVnodeQueue);
        canMove && nodeOps.insertBefore(parentElm, oldStartVnode.elm, nodeOps.nextSibling(oldEndVnode.elm));
        oldStartVnode = oldCh[++oldStartIdx];
        newEndVnode = newCh[--newEndIdx];
      } else if (sameVnode(oldEndVnode, newStartVnode)) { // Vnode moved left
        patchVnode(oldEndVnode, newStartVnode, insertedVnodeQueue);
        canMove && nodeOps.insertBefore(parentElm, oldEndVnode.elm, oldStartVnode.elm);
        oldEndVnode = oldCh[--oldEndIdx];
        newStartVnode = newCh[++newStartIdx];
      } else {
        if (isUndef(oldKeyToIdx)) { oldKeyToIdx = createKeyToOldIdx(oldCh, oldStartIdx, oldEndIdx); }
        idxInOld = isDef(newStartVnode.key) ? oldKeyToIdx[newStartVnode.key] : null;
        if (isUndef(idxInOld)) { // New element
          createElm(newStartVnode, insertedVnodeQueue, parentElm, oldStartVnode.elm);
          newStartVnode = newCh[++newStartIdx];
        } else {
          elmToMove = oldCh[idxInOld];
          /* istanbul ignore if */
          if (process.env.NODE_ENV !== 'production' && !elmToMove) {
            warn(
              'It seems there are duplicate keys that is causing an update error. ' +
              'Make sure each v-for item has a unique key.'
            );
          }
          if (sameVnode(elmToMove, newStartVnode)) {
            patchVnode(elmToMove, newStartVnode, insertedVnodeQueue);
            oldCh[idxInOld] = undefined;
            canMove && nodeOps.insertBefore(parentElm, newStartVnode.elm, oldStartVnode.elm);
            newStartVnode = newCh[++newStartIdx];
          } else {
            // same key but different element. treat as new element
            createElm(newStartVnode, insertedVnodeQueue, parentElm, oldStartVnode.elm);
            newStartVnode = newCh[++newStartIdx];
          }
        }
      }
    }
    if (oldStartIdx > oldEndIdx) {
      refElm = isUndef(newCh[newEndIdx + 1]) ? null : newCh[newEndIdx + 1].elm;
      addVnodes(parentElm, refElm, newCh, newStartIdx, newEndIdx, insertedVnodeQueue);
    } else if (newStartIdx > newEndIdx) {
      removeVnodes(parentElm, oldCh, oldStartIdx, oldEndIdx);
    }
  }

  function patchVnode (oldVnode, vnode, insertedVnodeQueue, removeOnly) {
    if (oldVnode === vnode) {
      return
    }
    // reuse element for static trees.
    // note we only do this if the vnode is cloned -
    // if the new node is not cloned it means the render functions have been
    // reset by the hot-reload-api and we need to do a proper re-render.
    if (vnode.isStatic &&
        oldVnode.isStatic &&
        vnode.key === oldVnode.key &&
        (vnode.isCloned || vnode.isOnce)) {
      vnode.elm = oldVnode.elm;
      vnode.componentInstance = oldVnode.componentInstance;
      return
    }
    var i;
    var data = vnode.data;
    var hasData = isDef(data);
    if (hasData && isDef(i = data.hook) && isDef(i = i.prepatch)) {
      i(oldVnode, vnode);
    }
    var elm = vnode.elm = oldVnode.elm;
    var oldCh = oldVnode.children;
    var ch = vnode.children;
    if (hasData && isPatchable(vnode)) {
      for (i = 0; i < cbs.update.length; ++i) { cbs.update[i](oldVnode, vnode); }
      if (isDef(i = data.hook) && isDef(i = i.update)) { i(oldVnode, vnode); }
    }
    if (isUndef(vnode.text)) {
      if (isDef(oldCh) && isDef(ch)) {
        if (oldCh !== ch) { updateChildren(elm, oldCh, ch, insertedVnodeQueue, removeOnly); }
      } else if (isDef(ch)) {
        if (isDef(oldVnode.text)) { nodeOps.setTextContent(elm, ''); }
        addVnodes(elm, null, ch, 0, ch.length - 1, insertedVnodeQueue);
      } else if (isDef(oldCh)) {
        removeVnodes(elm, oldCh, 0, oldCh.length - 1);
      } else if (isDef(oldVnode.text)) {
        nodeOps.setTextContent(elm, '');
      }
    } else if (oldVnode.text !== vnode.text) {
      nodeOps.setTextContent(elm, vnode.text);
    }
    if (hasData) {
      if (isDef(i = data.hook) && isDef(i = i.postpatch)) { i(oldVnode, vnode); }
    }
  }

  function invokeInsertHook (vnode, queue, initial) {
    // delay insert hooks for component root nodes, invoke them after the
    // element is really inserted
    if (initial && vnode.parent) {
      vnode.parent.data.pendingInsert = queue;
    } else {
      for (var i = 0; i < queue.length; ++i) {
        queue[i].data.hook.insert(queue[i]);
      }
    }
  }

  var bailed = false;
  // list of modules that can skip create hook during hydration because they
  // are already rendered on the client or has no need for initialization
  var isRenderedModule = makeMap('attrs,style,class,staticClass,staticStyle,key');

  // Note: this is a browser-only function so we can assume elms are DOM nodes.
  function hydrate (elm, vnode, insertedVnodeQueue) {
    if (process.env.NODE_ENV !== 'production') {
      if (!assertNodeMatch(elm, vnode)) {
        return false
      }
    }
    vnode.elm = elm;
    var tag = vnode.tag;
    var data = vnode.data;
    var children = vnode.children;
    if (isDef(data)) {
      if (isDef(i = data.hook) && isDef(i = i.init)) { i(vnode, true /* hydrating */); }
      if (isDef(i = vnode.componentInstance)) {
        // child component. it should have hydrated its own tree.
        initComponent(vnode, insertedVnodeQueue);
        return true
      }
    }
    if (isDef(tag)) {
      if (isDef(children)) {
        // empty element, allow client to pick up and populate children
        if (!elm.hasChildNodes()) {
          createChildren(vnode, children, insertedVnodeQueue);
        } else {
          var childrenMatch = true;
          var childNode = elm.firstChild;
          for (var i$1 = 0; i$1 < children.length; i$1++) {
            if (!childNode || !hydrate(childNode, children[i$1], insertedVnodeQueue)) {
              childrenMatch = false;
              break
            }
            childNode = childNode.nextSibling;
          }
          // if childNode is not null, it means the actual childNodes list is
          // longer than the virtual children list.
          if (!childrenMatch || childNode) {
            if (process.env.NODE_ENV !== 'production' &&
                typeof console !== 'undefined' &&
                !bailed) {
              bailed = true;
              console.warn('Parent: ', elm);
              console.warn('Mismatching childNodes vs. VNodes: ', elm.childNodes, children);
            }
            return false
          }
        }
      }
      if (isDef(data)) {
        for (var key in data) {
          if (!isRenderedModule(key)) {
            invokeCreateHooks(vnode, insertedVnodeQueue);
            break
          }
        }
      }
    } else if (elm.data !== vnode.text) {
      elm.data = vnode.text;
    }
    return true
  }

  function assertNodeMatch (node, vnode) {
    if (vnode.tag) {
      return (
        vnode.tag.indexOf('vue-component') === 0 ||
        vnode.tag.toLowerCase() === (node.tagName && node.tagName.toLowerCase())
      )
    } else {
      return node.nodeType === (vnode.isComment ? 8 : 3)
    }
  }

  return function patch (oldVnode, vnode, hydrating, removeOnly, parentElm, refElm) {
    if (!vnode) {
      if (oldVnode) { invokeDestroyHook(oldVnode); }
      return
    }

    var isInitialPatch = false;
    var insertedVnodeQueue = [];

    if (!oldVnode) {
      // empty mount (likely as component), create new root element
      isInitialPatch = true;
      createElm(vnode, insertedVnodeQueue, parentElm, refElm);
    } else {
      var isRealElement = isDef(oldVnode.nodeType);
      if (!isRealElement && sameVnode(oldVnode, vnode)) {
        // patch existing root node
        patchVnode(oldVnode, vnode, insertedVnodeQueue, removeOnly);
      } else {
        if (isRealElement) {
          // mounting to a real element
          // check if this is server-rendered content and if we can perform
          // a successful hydration.
          if (oldVnode.nodeType === 1 && oldVnode.hasAttribute('server-rendered')) {
            oldVnode.removeAttribute('server-rendered');
            hydrating = true;
          }
          if (hydrating) {
            if (hydrate(oldVnode, vnode, insertedVnodeQueue)) {
              invokeInsertHook(vnode, insertedVnodeQueue, true);
              return oldVnode
            } else if (process.env.NODE_ENV !== 'production') {
              warn(
                'The client-side rendered virtual DOM tree is not matching ' +
                'server-rendered content. This is likely caused by incorrect ' +
                'HTML markup, for example nesting block-level elements inside ' +
                '<p>, or missing <tbody>. Bailing hydration and performing ' +
                'full client-side render.'
              );
            }
          }
          // either not server-rendered, or hydration failed.
          // create an empty node and replace it
          oldVnode = emptyNodeAt(oldVnode);
        }
        // replacing existing element
        var oldElm = oldVnode.elm;
        var parentElm$1 = nodeOps.parentNode(oldElm);
        createElm(
          vnode,
          insertedVnodeQueue,
          // extremely rare edge case: do not insert if old element is in a
          // leaving transition. Only happens when combining transition +
          // keep-alive + HOCs. (#4590)
          oldElm._leaveCb ? null : parentElm$1,
          nodeOps.nextSibling(oldElm)
        );

        if (vnode.parent) {
          // component root element replaced.
          // update parent placeholder node element, recursively
          var ancestor = vnode.parent;
          while (ancestor) {
            ancestor.elm = vnode.elm;
            ancestor = ancestor.parent;
          }
          if (isPatchable(vnode)) {
            for (var i = 0; i < cbs.create.length; ++i) {
              cbs.create[i](emptyNode, vnode.parent);
            }
          }
        }

        if (parentElm$1 !== null) {
          removeVnodes(parentElm$1, [oldVnode], 0, 0);
        } else if (isDef(oldVnode.tag)) {
          invokeDestroyHook(oldVnode);
        }
      }
    }

    invokeInsertHook(vnode, insertedVnodeQueue, isInitialPatch);
    return vnode.elm
  }
}

/*  */

var directives = {
  create: updateDirectives,
  update: updateDirectives,
  destroy: function unbindDirectives (vnode) {
    updateDirectives(vnode, emptyNode);
  }
};

function updateDirectives (oldVnode, vnode) {
  if (oldVnode.data.directives || vnode.data.directives) {
    _update(oldVnode, vnode);
  }
}

function _update (oldVnode, vnode) {
  var isCreate = oldVnode === emptyNode;
  var isDestroy = vnode === emptyNode;
  var oldDirs = normalizeDirectives$1(oldVnode.data.directives, oldVnode.context);
  var newDirs = normalizeDirectives$1(vnode.data.directives, vnode.context);

  var dirsWithInsert = [];
  var dirsWithPostpatch = [];

  var key, oldDir, dir;
  for (key in newDirs) {
    oldDir = oldDirs[key];
    dir = newDirs[key];
    if (!oldDir) {
      // new directive, bind
      callHook$1(dir, 'bind', vnode, oldVnode);
      if (dir.def && dir.def.inserted) {
        dirsWithInsert.push(dir);
      }
    } else {
      // existing directive, update
      dir.oldValue = oldDir.value;
      callHook$1(dir, 'update', vnode, oldVnode);
      if (dir.def && dir.def.componentUpdated) {
        dirsWithPostpatch.push(dir);
      }
    }
  }

  if (dirsWithInsert.length) {
    var callInsert = function () {
      for (var i = 0; i < dirsWithInsert.length; i++) {
        callHook$1(dirsWithInsert[i], 'inserted', vnode, oldVnode);
      }
    };
    if (isCreate) {
      mergeVNodeHook(vnode.data.hook || (vnode.data.hook = {}), 'insert', callInsert);
    } else {
      callInsert();
    }
  }

  if (dirsWithPostpatch.length) {
    mergeVNodeHook(vnode.data.hook || (vnode.data.hook = {}), 'postpatch', function () {
      for (var i = 0; i < dirsWithPostpatch.length; i++) {
        callHook$1(dirsWithPostpatch[i], 'componentUpdated', vnode, oldVnode);
      }
    });
  }

  if (!isCreate) {
    for (key in oldDirs) {
      if (!newDirs[key]) {
        // no longer present, unbind
        callHook$1(oldDirs[key], 'unbind', oldVnode, oldVnode, isDestroy);
      }
    }
  }
}

var emptyModifiers = Object.create(null);

function normalizeDirectives$1 (
  dirs,
  vm
) {
  var res = Object.create(null);
  if (!dirs) {
    return res
  }
  var i, dir;
  for (i = 0; i < dirs.length; i++) {
    dir = dirs[i];
    if (!dir.modifiers) {
      dir.modifiers = emptyModifiers;
    }
    res[getRawDirName(dir)] = dir;
    dir.def = resolveAsset(vm.$options, 'directives', dir.name, true);
  }
  return res
}

function getRawDirName (dir) {
  return dir.rawName || ((dir.name) + "." + (Object.keys(dir.modifiers || {}).join('.')))
}

function callHook$1 (dir, hook, vnode, oldVnode, isDestroy) {
  var fn = dir.def && dir.def[hook];
  if (fn) {
    fn(vnode.elm, dir, vnode, oldVnode, isDestroy);
  }
}

var baseModules = [
  ref,
  directives
];

/*  */

function updateAttrs (oldVnode, vnode) {
  if (!oldVnode.data.attrs && !vnode.data.attrs) {
    return
  }
  var key, cur, old;
  var elm = vnode.elm;
  var oldAttrs = oldVnode.data.attrs || {};
  var attrs = vnode.data.attrs || {};
  // clone observed objects, as the user probably wants to mutate it
  if (attrs.__ob__) {
    attrs = vnode.data.attrs = extend({}, attrs);
  }

  for (key in attrs) {
    cur = attrs[key];
    old = oldAttrs[key];
    if (old !== cur) {
      setAttr(elm, key, cur);
    }
  }
  // #4391: in IE9, setting type can reset value for input[type=radio]
  /* istanbul ignore if */
  if (isIE9 && attrs.value !== oldAttrs.value) {
    setAttr(elm, 'value', attrs.value);
  }
  for (key in oldAttrs) {
    if (attrs[key] == null) {
      if (isXlink(key)) {
        elm.removeAttributeNS(xlinkNS, getXlinkProp(key));
      } else if (!isEnumeratedAttr(key)) {
        elm.removeAttribute(key);
      }
    }
  }
}

function setAttr (el, key, value) {
  if (isBooleanAttr(key)) {
    // set attribute for blank value
    // e.g. <option disabled>Select one</option>
    if (isFalsyAttrValue(value)) {
      el.removeAttribute(key);
    } else {
      el.setAttribute(key, key);
    }
  } else if (isEnumeratedAttr(key)) {
    el.setAttribute(key, isFalsyAttrValue(value) || value === 'false' ? 'false' : 'true');
  } else if (isXlink(key)) {
    if (isFalsyAttrValue(value)) {
      el.removeAttributeNS(xlinkNS, getXlinkProp(key));
    } else {
      el.setAttributeNS(xlinkNS, key, value);
    }
  } else {
    if (isFalsyAttrValue(value)) {
      el.removeAttribute(key);
    } else {
      el.setAttribute(key, value);
    }
  }
}

var attrs = {
  create: updateAttrs,
  update: updateAttrs
};

/*  */

function updateClass (oldVnode, vnode) {
  var el = vnode.elm;
  var data = vnode.data;
  var oldData = oldVnode.data;
  if (!data.staticClass && !data.class &&
      (!oldData || (!oldData.staticClass && !oldData.class))) {
    return
  }

  var cls = genClassForVnode(vnode);

  // handle transition classes
  var transitionClass = el._transitionClasses;
  if (transitionClass) {
    cls = concat(cls, stringifyClass(transitionClass));
  }

  // set the class
  if (cls !== el._prevClass) {
    el.setAttribute('class', cls);
    el._prevClass = cls;
  }
}

var klass = {
  create: updateClass,
  update: updateClass
};

/*  */

var validDivisionCharRE = /[\w).+\-_$\]]/;

function parseFilters (exp) {
  var inSingle = false;
  var inDouble = false;
  var inTemplateString = false;
  var inRegex = false;
  var curly = 0;
  var square = 0;
  var paren = 0;
  var lastFilterIndex = 0;
  var c, prev, i, expression, filters;

  for (i = 0; i < exp.length; i++) {
    prev = c;
    c = exp.charCodeAt(i);
    if (inSingle) {
      if (c === 0x27 && prev !== 0x5C) { inSingle = false; }
    } else if (inDouble) {
      if (c === 0x22 && prev !== 0x5C) { inDouble = false; }
    } else if (inTemplateString) {
      if (c === 0x60 && prev !== 0x5C) { inTemplateString = false; }
    } else if (inRegex) {
      if (c === 0x2f && prev !== 0x5C) { inRegex = false; }
    } else if (
      c === 0x7C && // pipe
      exp.charCodeAt(i + 1) !== 0x7C &&
      exp.charCodeAt(i - 1) !== 0x7C &&
      !curly && !square && !paren
    ) {
      if (expression === undefined) {
        // first filter, end of expression
        lastFilterIndex = i + 1;
        expression = exp.slice(0, i).trim();
      } else {
        pushFilter();
      }
    } else {
      switch (c) {
        case 0x22: inDouble = true; break         // "
        case 0x27: inSingle = true; break         // '
        case 0x60: inTemplateString = true; break // `
        case 0x28: paren++; break                 // (
        case 0x29: paren--; break                 // )
        case 0x5B: square++; break                // [
        case 0x5D: square--; break                // ]
        case 0x7B: curly++; break                 // {
        case 0x7D: curly--; break                 // }
      }
      if (c === 0x2f) { // /
        var j = i - 1;
        var p = (void 0);
        // find first non-whitespace prev char
        for (; j >= 0; j--) {
          p = exp.charAt(j);
          if (p !== ' ') { break }
        }
        if (!p || !validDivisionCharRE.test(p)) {
          inRegex = true;
        }
      }
    }
  }

  if (expression === undefined) {
    expression = exp.slice(0, i).trim();
  } else if (lastFilterIndex !== 0) {
    pushFilter();
  }

  function pushFilter () {
    (filters || (filters = [])).push(exp.slice(lastFilterIndex, i).trim());
    lastFilterIndex = i + 1;
  }

  if (filters) {
    for (i = 0; i < filters.length; i++) {
      expression = wrapFilter(expression, filters[i]);
    }
  }

  return expression
}

function wrapFilter (exp, filter) {
  var i = filter.indexOf('(');
  if (i < 0) {
    // _f: resolveFilter
    return ("_f(\"" + filter + "\")(" + exp + ")")
  } else {
    var name = filter.slice(0, i);
    var args = filter.slice(i + 1);
    return ("_f(\"" + name + "\")(" + exp + "," + args)
  }
}

/*  */

function baseWarn (msg) {
  console.error(("[Vue compiler]: " + msg));
}

function pluckModuleFunction (
  modules,
  key
) {
  return modules
    ? modules.map(function (m) { return m[key]; }).filter(function (_) { return _; })
    : []
}

function addProp (el, name, value) {
  (el.props || (el.props = [])).push({ name: name, value: value });
}

function addAttr (el, name, value) {
  (el.attrs || (el.attrs = [])).push({ name: name, value: value });
}

function addDirective (
  el,
  name,
  rawName,
  value,
  arg,
  modifiers
) {
  (el.directives || (el.directives = [])).push({ name: name, rawName: rawName, value: value, arg: arg, modifiers: modifiers });
}

function addHandler (
  el,
  name,
  value,
  modifiers,
  important
) {
  // check capture modifier
  if (modifiers && modifiers.capture) {
    delete modifiers.capture;
    name = '!' + name; // mark the event as captured
  }
  if (modifiers && modifiers.once) {
    delete modifiers.once;
    name = '~' + name; // mark the event as once
  }
  var events;
  if (modifiers && modifiers.native) {
    delete modifiers.native;
    events = el.nativeEvents || (el.nativeEvents = {});
  } else {
    events = el.events || (el.events = {});
  }
  var newHandler = { value: value, modifiers: modifiers };
  var handlers = events[name];
  /* istanbul ignore if */
  if (Array.isArray(handlers)) {
    important ? handlers.unshift(newHandler) : handlers.push(newHandler);
  } else if (handlers) {
    events[name] = important ? [newHandler, handlers] : [handlers, newHandler];
  } else {
    events[name] = newHandler;
  }
}

function getBindingAttr (
  el,
  name,
  getStatic
) {
  var dynamicValue =
    getAndRemoveAttr(el, ':' + name) ||
    getAndRemoveAttr(el, 'v-bind:' + name);
  if (dynamicValue != null) {
    return parseFilters(dynamicValue)
  } else if (getStatic !== false) {
    var staticValue = getAndRemoveAttr(el, name);
    if (staticValue != null) {
      return JSON.stringify(staticValue)
    }
  }
}

function getAndRemoveAttr (el, name) {
  var val;
  if ((val = el.attrsMap[name]) != null) {
    var list = el.attrsList;
    for (var i = 0, l = list.length; i < l; i++) {
      if (list[i].name === name) {
        list.splice(i, 1);
        break
      }
    }
  }
  return val
}

/*  */

/**
 * Cross-platform code generation for component v-model
 */
function genComponentModel (
  el,
  value,
  modifiers
) {
  var ref = modifiers || {};
  var number = ref.number;
  var trim = ref.trim;

  var baseValueExpression = '$$v';
  var valueExpression = baseValueExpression;
  if (trim) {
    valueExpression =
      "(typeof " + baseValueExpression + " === 'string'" +
        "? " + baseValueExpression + ".trim()" +
        ": " + baseValueExpression + ")";
  }
  if (number) {
    valueExpression = "_n(" + valueExpression + ")";
  }
  var assignment = genAssignmentCode(value, valueExpression);

  el.model = {
    value: ("(" + value + ")"),
    callback: ("function (" + baseValueExpression + ") {" + assignment + "}")
  };
}

/**
 * Cross-platform codegen helper for generating v-model value assignment code.
 */
function genAssignmentCode (
  value,
  assignment
) {
  var modelRs = parseModel(value);
  if (modelRs.idx === null) {
    return (value + "=" + assignment)
  } else {
    return "var $$exp = " + (modelRs.exp) + ", $$idx = " + (modelRs.idx) + ";" +
      "if (!Array.isArray($$exp)){" +
        value + "=" + assignment + "}" +
      "else{$$exp.splice($$idx, 1, " + assignment + ")}"
  }
}

/**
 * parse directive model to do the array update transform. a[idx] = val => $$a.splice($$idx, 1, val)
 *
 * for loop possible cases:
 *
 * - test
 * - test[idx]
 * - test[test1[idx]]
 * - test["a"][idx]
 * - xxx.test[a[a].test1[idx]]
 * - test.xxx.a["asa"][test1[idx]]
 *
 */

var len;
var str;
var chr;
var index$1;
var expressionPos;
var expressionEndPos;

function parseModel (val) {
  str = val;
  len = str.length;
  index$1 = expressionPos = expressionEndPos = 0;

  if (val.indexOf('[') < 0 || val.lastIndexOf(']') < len - 1) {
    return {
      exp: val,
      idx: null
    }
  }

  while (!eof()) {
    chr = next();
    /* istanbul ignore if */
    if (isStringStart(chr)) {
      parseString(chr);
    } else if (chr === 0x5B) {
      parseBracket(chr);
    }
  }

  return {
    exp: val.substring(0, expressionPos),
    idx: val.substring(expressionPos + 1, expressionEndPos)
  }
}

function next () {
  return str.charCodeAt(++index$1)
}

function eof () {
  return index$1 >= len
}

function isStringStart (chr) {
  return chr === 0x22 || chr === 0x27
}

function parseBracket (chr) {
  var inBracket = 1;
  expressionPos = index$1;
  while (!eof()) {
    chr = next();
    if (isStringStart(chr)) {
      parseString(chr);
      continue
    }
    if (chr === 0x5B) { inBracket++; }
    if (chr === 0x5D) { inBracket--; }
    if (inBracket === 0) {
      expressionEndPos = index$1;
      break
    }
  }
}

function parseString (chr) {
  var stringQuote = chr;
  while (!eof()) {
    chr = next();
    if (chr === stringQuote) {
      break
    }
  }
}

/*  */

var warn$1;

// in some cases, the event used has to be determined at runtime
// so we used some reserved tokens during compile.
var RANGE_TOKEN = '__r';
var CHECKBOX_RADIO_TOKEN = '__c';

function model (
  el,
  dir,
  _warn
) {
  warn$1 = _warn;
  var value = dir.value;
  var modifiers = dir.modifiers;
  var tag = el.tag;
  var type = el.attrsMap.type;

  if (process.env.NODE_ENV !== 'production') {
    var dynamicType = el.attrsMap['v-bind:type'] || el.attrsMap[':type'];
    if (tag === 'input' && dynamicType) {
      warn$1(
        "<input :type=\"" + dynamicType + "\" v-model=\"" + value + "\">:\n" +
        "v-model does not support dynamic input types. Use v-if branches instead."
      );
    }
    // inputs with type="file" are read only and setting the input's
    // value will throw an error.
    if (tag === 'input' && type === 'file') {
      warn$1(
        "<" + (el.tag) + " v-model=\"" + value + "\" type=\"file\">:\n" +
        "File inputs are read only. Use a v-on:change listener instead."
      );
    }
  }

  if (tag === 'select') {
    genSelect(el, value, modifiers);
  } else if (tag === 'input' && type === 'checkbox') {
    genCheckboxModel(el, value, modifiers);
  } else if (tag === 'input' && type === 'radio') {
    genRadioModel(el, value, modifiers);
  } else if (tag === 'input' || tag === 'textarea') {
    genDefaultModel(el, value, modifiers);
  } else if (!config.isReservedTag(tag)) {
    genComponentModel(el, value, modifiers);
    // component v-model doesn't need extra runtime
    return false
  } else if (process.env.NODE_ENV !== 'production') {
    warn$1(
      "<" + (el.tag) + " v-model=\"" + value + "\">: " +
      "v-model is not supported on this element type. " +
      'If you are working with contenteditable, it\'s recommended to ' +
      'wrap a library dedicated for that purpose inside a custom component.'
    );
  }

  // ensure runtime directive metadata
  return true
}

function genCheckboxModel (
  el,
  value,
  modifiers
) {
  if (process.env.NODE_ENV !== 'production' &&
    el.attrsMap.checked != null) {
    warn$1(
      "<" + (el.tag) + " v-model=\"" + value + "\" checked>:\n" +
      "inline checked attributes will be ignored when using v-model. " +
      'Declare initial values in the component\'s data option instead.'
    );
  }
  var number = modifiers && modifiers.number;
  var valueBinding = getBindingAttr(el, 'value') || 'null';
  var trueValueBinding = getBindingAttr(el, 'true-value') || 'true';
  var falseValueBinding = getBindingAttr(el, 'false-value') || 'false';
  addProp(el, 'checked',
    "Array.isArray(" + value + ")" +
      "?_i(" + value + "," + valueBinding + ")>-1" + (
        trueValueBinding === 'true'
          ? (":(" + value + ")")
          : (":_q(" + value + "," + trueValueBinding + ")")
      )
  );
  addHandler(el, CHECKBOX_RADIO_TOKEN,
    "var $$a=" + value + "," +
        '$$el=$event.target,' +
        "$$c=$$el.checked?(" + trueValueBinding + "):(" + falseValueBinding + ");" +
    'if(Array.isArray($$a)){' +
      "var $$v=" + (number ? '_n(' + valueBinding + ')' : valueBinding) + "," +
          '$$i=_i($$a,$$v);' +
      "if($$c){$$i<0&&(" + value + "=$$a.concat($$v))}" +
      "else{$$i>-1&&(" + value + "=$$a.slice(0,$$i).concat($$a.slice($$i+1)))}" +
    "}else{" + value + "=$$c}",
    null, true
  );
}

function genRadioModel (
    el,
    value,
    modifiers
) {
  if (process.env.NODE_ENV !== 'production' &&
    el.attrsMap.checked != null) {
    warn$1(
      "<" + (el.tag) + " v-model=\"" + value + "\" checked>:\n" +
      "inline checked attributes will be ignored when using v-model. " +
      'Declare initial values in the component\'s data option instead.'
    );
  }
  var number = modifiers && modifiers.number;
  var valueBinding = getBindingAttr(el, 'value') || 'null';
  valueBinding = number ? ("_n(" + valueBinding + ")") : valueBinding;
  addProp(el, 'checked', ("_q(" + value + "," + valueBinding + ")"));
  addHandler(el, CHECKBOX_RADIO_TOKEN, genAssignmentCode(value, valueBinding), null, true);
}

function genSelect (
    el,
    value,
    modifiers
) {
  if (process.env.NODE_ENV !== 'production') {
    el.children.some(checkOptionWarning);
  }

  var number = modifiers && modifiers.number;
  var selectedVal = "Array.prototype.filter" +
    ".call($event.target.options,function(o){return o.selected})" +
    ".map(function(o){var val = \"_value\" in o ? o._value : o.value;" +
    "return " + (number ? '_n(val)' : 'val') + "})";

  var assignment = '$event.target.multiple ? $$selectedVal : $$selectedVal[0]';
  var code = "var $$selectedVal = " + selectedVal + ";";
  code = code + " " + (genAssignmentCode(value, assignment));
  addHandler(el, 'change', code, null, true);
}

function checkOptionWarning (option) {
  if (option.type === 1 &&
    option.tag === 'option' &&
    option.attrsMap.selected != null) {
    warn$1(
      "<select v-model=\"" + (option.parent.attrsMap['v-model']) + "\">:\n" +
      'inline selected attributes on <option> will be ignored when using v-model. ' +
      'Declare initial values in the component\'s data option instead.'
    );
    return true
  }
  return false
}

function genDefaultModel (
  el,
  value,
  modifiers
) {
  var type = el.attrsMap.type;
  var ref = modifiers || {};
  var lazy = ref.lazy;
  var number = ref.number;
  var trim = ref.trim;
  var needCompositionGuard = !lazy && type !== 'range';
  var event = lazy
    ? 'change'
    : type === 'range'
      ? RANGE_TOKEN
      : 'input';

  var valueExpression = '$event.target.value';
  if (trim) {
    valueExpression = "$event.target.value.trim()";
  }
  if (number) {
    valueExpression = "_n(" + valueExpression + ")";
  }

  var code = genAssignmentCode(value, valueExpression);
  if (needCompositionGuard) {
    code = "if($event.target.composing)return;" + code;
  }

  addProp(el, 'value', ("(" + value + ")"));
  addHandler(el, event, code, null, true);
  if (trim || number || type === 'number') {
    addHandler(el, 'blur', '$forceUpdate()');
  }
}

/*  */

// normalize v-model event tokens that can only be determined at runtime.
// it's important to place the event as the first in the array because
// the whole point is ensuring the v-model callback gets called before
// user-attached handlers.
function normalizeEvents (on) {
  var event;
  /* istanbul ignore if */
  if (on[RANGE_TOKEN]) {
    // IE input[type=range] only supports `change` event
    event = isIE ? 'change' : 'input';
    on[event] = [].concat(on[RANGE_TOKEN], on[event] || []);
    delete on[RANGE_TOKEN];
  }
  if (on[CHECKBOX_RADIO_TOKEN]) {
    // Chrome fires microtasks in between click/change, leads to #4521
    event = isChrome ? 'click' : 'change';
    on[event] = [].concat(on[CHECKBOX_RADIO_TOKEN], on[event] || []);
    delete on[CHECKBOX_RADIO_TOKEN];
  }
}

var target$1;

function add$1 (
  event,
  handler,
  once,
  capture
) {
  if (once) {
    var oldHandler = handler;
    var _target = target$1; // save current target element in closure
    handler = function (ev) {
      var res = arguments.length === 1
        ? oldHandler(ev)
        : oldHandler.apply(null, arguments);
      if (res !== null) {
        remove$2(event, handler, capture, _target);
      }
    };
  }
  target$1.addEventListener(event, handler, capture);
}

function remove$2 (
  event,
  handler,
  capture,
  _target
) {
  (_target || target$1).removeEventListener(event, handler, capture);
}

function updateDOMListeners (oldVnode, vnode) {
  if (!oldVnode.data.on && !vnode.data.on) {
    return
  }
  var on = vnode.data.on || {};
  var oldOn = oldVnode.data.on || {};
  target$1 = vnode.elm;
  normalizeEvents(on);
  updateListeners(on, oldOn, add$1, remove$2, vnode.context);
}

var events = {
  create: updateDOMListeners,
  update: updateDOMListeners
};

/*  */

function updateDOMProps (oldVnode, vnode) {
  if (!oldVnode.data.domProps && !vnode.data.domProps) {
    return
  }
  var key, cur;
  var elm = vnode.elm;
  var oldProps = oldVnode.data.domProps || {};
  var props = vnode.data.domProps || {};
  // clone observed objects, as the user probably wants to mutate it
  if (props.__ob__) {
    props = vnode.data.domProps = extend({}, props);
  }

  for (key in oldProps) {
    if (props[key] == null) {
      elm[key] = '';
    }
  }
  for (key in props) {
    cur = props[key];
    // ignore children if the node has textContent or innerHTML,
    // as these will throw away existing DOM nodes and cause removal errors
    // on subsequent patches (#3360)
    if (key === 'textContent' || key === 'innerHTML') {
      if (vnode.children) { vnode.children.length = 0; }
      if (cur === oldProps[key]) { continue }
    }

    if (key === 'value') {
      // store value as _value as well since
      // non-string values will be stringified
      elm._value = cur;
      // avoid resetting cursor position when value is the same
      var strCur = cur == null ? '' : String(cur);
      if (shouldUpdateValue(elm, vnode, strCur)) {
        elm.value = strCur;
      }
    } else {
      elm[key] = cur;
    }
  }
}

// check platforms/web/util/attrs.js acceptValue


function shouldUpdateValue (
  elm,
  vnode,
  checkVal
) {
  return (!elm.composing && (
    vnode.tag === 'option' ||
    isDirty(elm, checkVal) ||
    isInputChanged(elm, checkVal)
  ))
}

function isDirty (elm, checkVal) {
  // return true when textbox (.number and .trim) loses focus and its value is not equal to the updated value
  return document.activeElement !== elm && elm.value !== checkVal
}

function isInputChanged (elm, newVal) {
  var value = elm.value;
  var modifiers = elm._vModifiers; // injected by v-model runtime
  if ((modifiers && modifiers.number) || elm.type === 'number') {
    return toNumber(value) !== toNumber(newVal)
  }
  if (modifiers && modifiers.trim) {
    return value.trim() !== newVal.trim()
  }
  return value !== newVal
}

var domProps = {
  create: updateDOMProps,
  update: updateDOMProps
};

/*  */

var parseStyleText = cached(function (cssText) {
  var res = {};
  var listDelimiter = /;(?![^(]*\))/g;
  var propertyDelimiter = /:(.+)/;
  cssText.split(listDelimiter).forEach(function (item) {
    if (item) {
      var tmp = item.split(propertyDelimiter);
      tmp.length > 1 && (res[tmp[0].trim()] = tmp[1].trim());
    }
  });
  return res
});

// merge static and dynamic style data on the same vnode
function normalizeStyleData (data) {
  var style = normalizeStyleBinding(data.style);
  // static style is pre-processed into an object during compilation
  // and is always a fresh object, so it's safe to merge into it
  return data.staticStyle
    ? extend(data.staticStyle, style)
    : style
}

// normalize possible array / string values into Object
function normalizeStyleBinding (bindingStyle) {
  if (Array.isArray(bindingStyle)) {
    return toObject(bindingStyle)
  }
  if (typeof bindingStyle === 'string') {
    return parseStyleText(bindingStyle)
  }
  return bindingStyle
}

/**
 * parent component style should be after child's
 * so that parent component's style could override it
 */
function getStyle (vnode, checkChild) {
  var res = {};
  var styleData;

  if (checkChild) {
    var childNode = vnode;
    while (childNode.componentInstance) {
      childNode = childNode.componentInstance._vnode;
      if (childNode.data && (styleData = normalizeStyleData(childNode.data))) {
        extend(res, styleData);
      }
    }
  }

  if ((styleData = normalizeStyleData(vnode.data))) {
    extend(res, styleData);
  }

  var parentNode = vnode;
  while ((parentNode = parentNode.parent)) {
    if (parentNode.data && (styleData = normalizeStyleData(parentNode.data))) {
      extend(res, styleData);
    }
  }
  return res
}

/*  */

var cssVarRE = /^--/;
var importantRE = /\s*!important$/;
var setProp = function (el, name, val) {
  /* istanbul ignore if */
  if (cssVarRE.test(name)) {
    el.style.setProperty(name, val);
  } else if (importantRE.test(val)) {
    el.style.setProperty(name, val.replace(importantRE, ''), 'important');
  } else {
    el.style[normalize(name)] = val;
  }
};

var prefixes = ['Webkit', 'Moz', 'ms'];

var testEl;
var normalize = cached(function (prop) {
  testEl = testEl || document.createElement('div');
  prop = camelize(prop);
  if (prop !== 'filter' && (prop in testEl.style)) {
    return prop
  }
  var upper = prop.charAt(0).toUpperCase() + prop.slice(1);
  for (var i = 0; i < prefixes.length; i++) {
    var prefixed = prefixes[i] + upper;
    if (prefixed in testEl.style) {
      return prefixed
    }
  }
});

function updateStyle (oldVnode, vnode) {
  var data = vnode.data;
  var oldData = oldVnode.data;

  if (!data.staticStyle && !data.style &&
      !oldData.staticStyle && !oldData.style) {
    return
  }

  var cur, name;
  var el = vnode.elm;
  var oldStaticStyle = oldVnode.data.staticStyle;
  var oldStyleBinding = oldVnode.data.style || {};

  // if static style exists, stylebinding already merged into it when doing normalizeStyleData
  var oldStyle = oldStaticStyle || oldStyleBinding;

  var style = normalizeStyleBinding(vnode.data.style) || {};

  vnode.data.style = style.__ob__ ? extend({}, style) : style;

  var newStyle = getStyle(vnode, true);

  for (name in oldStyle) {
    if (newStyle[name] == null) {
      setProp(el, name, '');
    }
  }
  for (name in newStyle) {
    cur = newStyle[name];
    if (cur !== oldStyle[name]) {
      // ie9 setting to null has no effect, must use empty string
      setProp(el, name, cur == null ? '' : cur);
    }
  }
}

var style = {
  create: updateStyle,
  update: updateStyle
};

/*  */

/**
 * Add class with compatibility for SVG since classList is not supported on
 * SVG elements in IE
 */
function addClass (el, cls) {
  /* istanbul ignore if */
  if (!cls || !(cls = cls.trim())) {
    return
  }

  /* istanbul ignore else */
  if (el.classList) {
    if (cls.indexOf(' ') > -1) {
      cls.split(/\s+/).forEach(function (c) { return el.classList.add(c); });
    } else {
      el.classList.add(cls);
    }
  } else {
    var cur = " " + (el.getAttribute('class') || '') + " ";
    if (cur.indexOf(' ' + cls + ' ') < 0) {
      el.setAttribute('class', (cur + cls).trim());
    }
  }
}

/**
 * Remove class with compatibility for SVG since classList is not supported on
 * SVG elements in IE
 */
function removeClass (el, cls) {
  /* istanbul ignore if */
  if (!cls || !(cls = cls.trim())) {
    return
  }

  /* istanbul ignore else */
  if (el.classList) {
    if (cls.indexOf(' ') > -1) {
      cls.split(/\s+/).forEach(function (c) { return el.classList.remove(c); });
    } else {
      el.classList.remove(cls);
    }
  } else {
    var cur = " " + (el.getAttribute('class') || '') + " ";
    var tar = ' ' + cls + ' ';
    while (cur.indexOf(tar) >= 0) {
      cur = cur.replace(tar, ' ');
    }
    el.setAttribute('class', cur.trim());
  }
}

/*  */

function resolveTransition (def$$1) {
  if (!def$$1) {
    return
  }
  /* istanbul ignore else */
  if (typeof def$$1 === 'object') {
    var res = {};
    if (def$$1.css !== false) {
      extend(res, autoCssTransition(def$$1.name || 'v'));
    }
    extend(res, def$$1);
    return res
  } else if (typeof def$$1 === 'string') {
    return autoCssTransition(def$$1)
  }
}

var autoCssTransition = cached(function (name) {
  return {
    enterClass: (name + "-enter"),
    enterToClass: (name + "-enter-to"),
    enterActiveClass: (name + "-enter-active"),
    leaveClass: (name + "-leave"),
    leaveToClass: (name + "-leave-to"),
    leaveActiveClass: (name + "-leave-active")
  }
});

var hasTransition = inBrowser && !isIE9;
var TRANSITION = 'transition';
var ANIMATION = 'animation';

// Transition property/event sniffing
var transitionProp = 'transition';
var transitionEndEvent = 'transitionend';
var animationProp = 'animation';
var animationEndEvent = 'animationend';
if (hasTransition) {
  /* istanbul ignore if */
  if (window.ontransitionend === undefined &&
    window.onwebkittransitionend !== undefined) {
    transitionProp = 'WebkitTransition';
    transitionEndEvent = 'webkitTransitionEnd';
  }
  if (window.onanimationend === undefined &&
    window.onwebkitanimationend !== undefined) {
    animationProp = 'WebkitAnimation';
    animationEndEvent = 'webkitAnimationEnd';
  }
}

// binding to window is necessary to make hot reload work in IE in strict mode
var raf = inBrowser && window.requestAnimationFrame
  ? window.requestAnimationFrame.bind(window)
  : setTimeout;

function nextFrame (fn) {
  raf(function () {
    raf(fn);
  });
}

function addTransitionClass (el, cls) {
  (el._transitionClasses || (el._transitionClasses = [])).push(cls);
  addClass(el, cls);
}

function removeTransitionClass (el, cls) {
  if (el._transitionClasses) {
    remove(el._transitionClasses, cls);
  }
  removeClass(el, cls);
}

function whenTransitionEnds (
  el,
  expectedType,
  cb
) {
  var ref = getTransitionInfo(el, expectedType);
  var type = ref.type;
  var timeout = ref.timeout;
  var propCount = ref.propCount;
  if (!type) { return cb() }
  var event = type === TRANSITION ? transitionEndEvent : animationEndEvent;
  var ended = 0;
  var end = function () {
    el.removeEventListener(event, onEnd);
    cb();
  };
  var onEnd = function (e) {
    if (e.target === el) {
      if (++ended >= propCount) {
        end();
      }
    }
  };
  setTimeout(function () {
    if (ended < propCount) {
      end();
    }
  }, timeout + 1);
  el.addEventListener(event, onEnd);
}

var transformRE = /\b(transform|all)(,|$)/;

function getTransitionInfo (el, expectedType) {
  var styles = window.getComputedStyle(el);
  var transitioneDelays = styles[transitionProp + 'Delay'].split(', ');
  var transitionDurations = styles[transitionProp + 'Duration'].split(', ');
  var transitionTimeout = getTimeout(transitioneDelays, transitionDurations);
  var animationDelays = styles[animationProp + 'Delay'].split(', ');
  var animationDurations = styles[animationProp + 'Duration'].split(', ');
  var animationTimeout = getTimeout(animationDelays, animationDurations);

  var type;
  var timeout = 0;
  var propCount = 0;
  /* istanbul ignore if */
  if (expectedType === TRANSITION) {
    if (transitionTimeout > 0) {
      type = TRANSITION;
      timeout = transitionTimeout;
      propCount = transitionDurations.length;
    }
  } else if (expectedType === ANIMATION) {
    if (animationTimeout > 0) {
      type = ANIMATION;
      timeout = animationTimeout;
      propCount = animationDurations.length;
    }
  } else {
    timeout = Math.max(transitionTimeout, animationTimeout);
    type = timeout > 0
      ? transitionTimeout > animationTimeout
        ? TRANSITION
        : ANIMATION
      : null;
    propCount = type
      ? type === TRANSITION
        ? transitionDurations.length
        : animationDurations.length
      : 0;
  }
  var hasTransform =
    type === TRANSITION &&
    transformRE.test(styles[transitionProp + 'Property']);
  return {
    type: type,
    timeout: timeout,
    propCount: propCount,
    hasTransform: hasTransform
  }
}

function getTimeout (delays, durations) {
  /* istanbul ignore next */
  while (delays.length < durations.length) {
    delays = delays.concat(delays);
  }

  return Math.max.apply(null, durations.map(function (d, i) {
    return toMs(d) + toMs(delays[i])
  }))
}

function toMs (s) {
  return Number(s.slice(0, -1)) * 1000
}

/*  */

function enter (vnode, toggleDisplay) {
  var el = vnode.elm;

  // call leave callback now
  if (el._leaveCb) {
    el._leaveCb.cancelled = true;
    el._leaveCb();
  }

  var data = resolveTransition(vnode.data.transition);
  if (!data) {
    return
  }

  /* istanbul ignore if */
  if (el._enterCb || el.nodeType !== 1) {
    return
  }

  var css = data.css;
  var type = data.type;
  var enterClass = data.enterClass;
  var enterToClass = data.enterToClass;
  var enterActiveClass = data.enterActiveClass;
  var appearClass = data.appearClass;
  var appearToClass = data.appearToClass;
  var appearActiveClass = data.appearActiveClass;
  var beforeEnter = data.beforeEnter;
  var enter = data.enter;
  var afterEnter = data.afterEnter;
  var enterCancelled = data.enterCancelled;
  var beforeAppear = data.beforeAppear;
  var appear = data.appear;
  var afterAppear = data.afterAppear;
  var appearCancelled = data.appearCancelled;
  var duration = data.duration;

  // activeInstance will always be the <transition> component managing this
  // transition. One edge case to check is when the <transition> is placed
  // as the root node of a child component. In that case we need to check
  // <transition>'s parent for appear check.
  var context = activeInstance;
  var transitionNode = activeInstance.$vnode;
  while (transitionNode && transitionNode.parent) {
    transitionNode = transitionNode.parent;
    context = transitionNode.context;
  }

  var isAppear = !context._isMounted || !vnode.isRootInsert;

  if (isAppear && !appear && appear !== '') {
    return
  }

  var startClass = isAppear && appearClass
    ? appearClass
    : enterClass;
  var activeClass = isAppear && appearActiveClass
    ? appearActiveClass
    : enterActiveClass;
  var toClass = isAppear && appearToClass
    ? appearToClass
    : enterToClass;

  var beforeEnterHook = isAppear
    ? (beforeAppear || beforeEnter)
    : beforeEnter;
  var enterHook = isAppear
    ? (typeof appear === 'function' ? appear : enter)
    : enter;
  var afterEnterHook = isAppear
    ? (afterAppear || afterEnter)
    : afterEnter;
  var enterCancelledHook = isAppear
    ? (appearCancelled || enterCancelled)
    : enterCancelled;

  var explicitEnterDuration = toNumber(
    isObject(duration)
      ? duration.enter
      : duration
  );

  if (process.env.NODE_ENV !== 'production' && explicitEnterDuration != null) {
    checkDuration(explicitEnterDuration, 'enter', vnode);
  }

  var expectsCSS = css !== false && !isIE9;
  var userWantsControl = getHookAgumentsLength(enterHook);

  var cb = el._enterCb = once(function () {
    if (expectsCSS) {
      removeTransitionClass(el, toClass);
      removeTransitionClass(el, activeClass);
    }
    if (cb.cancelled) {
      if (expectsCSS) {
        removeTransitionClass(el, startClass);
      }
      enterCancelledHook && enterCancelledHook(el);
    } else {
      afterEnterHook && afterEnterHook(el);
    }
    el._enterCb = null;
  });

  if (!vnode.data.show) {
    // remove pending leave element on enter by injecting an insert hook
    mergeVNodeHook(vnode.data.hook || (vnode.data.hook = {}), 'insert', function () {
      var parent = el.parentNode;
      var pendingNode = parent && parent._pending && parent._pending[vnode.key];
      if (pendingNode &&
          pendingNode.tag === vnode.tag &&
          pendingNode.elm._leaveCb) {
        pendingNode.elm._leaveCb();
      }
      enterHook && enterHook(el, cb);
    });
  }

  // start enter transition
  beforeEnterHook && beforeEnterHook(el);
  if (expectsCSS) {
    addTransitionClass(el, startClass);
    addTransitionClass(el, activeClass);
    nextFrame(function () {
      addTransitionClass(el, toClass);
      removeTransitionClass(el, startClass);
      if (!cb.cancelled && !userWantsControl) {
        if (isValidDuration(explicitEnterDuration)) {
          setTimeout(cb, explicitEnterDuration);
        } else {
          whenTransitionEnds(el, type, cb);
        }
      }
    });
  }

  if (vnode.data.show) {
    toggleDisplay && toggleDisplay();
    enterHook && enterHook(el, cb);
  }

  if (!expectsCSS && !userWantsControl) {
    cb();
  }
}

function leave (vnode, rm) {
  var el = vnode.elm;

  // call enter callback now
  if (el._enterCb) {
    el._enterCb.cancelled = true;
    el._enterCb();
  }

  var data = resolveTransition(vnode.data.transition);
  if (!data) {
    return rm()
  }

  /* istanbul ignore if */
  if (el._leaveCb || el.nodeType !== 1) {
    return
  }

  var css = data.css;
  var type = data.type;
  var leaveClass = data.leaveClass;
  var leaveToClass = data.leaveToClass;
  var leaveActiveClass = data.leaveActiveClass;
  var beforeLeave = data.beforeLeave;
  var leave = data.leave;
  var afterLeave = data.afterLeave;
  var leaveCancelled = data.leaveCancelled;
  var delayLeave = data.delayLeave;
  var duration = data.duration;

  var expectsCSS = css !== false && !isIE9;
  var userWantsControl = getHookAgumentsLength(leave);

  var explicitLeaveDuration = toNumber(
    isObject(duration)
      ? duration.leave
      : duration
  );

  if (process.env.NODE_ENV !== 'production' && explicitLeaveDuration != null) {
    checkDuration(explicitLeaveDuration, 'leave', vnode);
  }

  var cb = el._leaveCb = once(function () {
    if (el.parentNode && el.parentNode._pending) {
      el.parentNode._pending[vnode.key] = null;
    }
    if (expectsCSS) {
      removeTransitionClass(el, leaveToClass);
      removeTransitionClass(el, leaveActiveClass);
    }
    if (cb.cancelled) {
      if (expectsCSS) {
        removeTransitionClass(el, leaveClass);
      }
      leaveCancelled && leaveCancelled(el);
    } else {
      rm();
      afterLeave && afterLeave(el);
    }
    el._leaveCb = null;
  });

  if (delayLeave) {
    delayLeave(performLeave);
  } else {
    performLeave();
  }

  function performLeave () {
    // the delayed leave may have already been cancelled
    if (cb.cancelled) {
      return
    }
    // record leaving element
    if (!vnode.data.show) {
      (el.parentNode._pending || (el.parentNode._pending = {}))[vnode.key] = vnode;
    }
    beforeLeave && beforeLeave(el);
    if (expectsCSS) {
      addTransitionClass(el, leaveClass);
      addTransitionClass(el, leaveActiveClass);
      nextFrame(function () {
        addTransitionClass(el, leaveToClass);
        removeTransitionClass(el, leaveClass);
        if (!cb.cancelled && !userWantsControl) {
          if (isValidDuration(explicitLeaveDuration)) {
            setTimeout(cb, explicitLeaveDuration);
          } else {
            whenTransitionEnds(el, type, cb);
          }
        }
      });
    }
    leave && leave(el, cb);
    if (!expectsCSS && !userWantsControl) {
      cb();
    }
  }
}

// only used in dev mode
function checkDuration (val, name, vnode) {
  if (typeof val !== 'number') {
    warn(
      "<transition> explicit " + name + " duration is not a valid number - " +
      "got " + (JSON.stringify(val)) + ".",
      vnode.context
    );
  } else if (isNaN(val)) {
    warn(
      "<transition> explicit " + name + " duration is NaN - " +
      'the duration expression might be incorrect.',
      vnode.context
    );
  }
}

function isValidDuration (val) {
  return typeof val === 'number' && !isNaN(val)
}

/**
 * Normalize a transition hook's argument length. The hook may be:
 * - a merged hook (invoker) with the original in .fns
 * - a wrapped component method (check ._length)
 * - a plain function (.length)
 */
function getHookAgumentsLength (fn) {
  if (!fn) { return false }
  var invokerFns = fn.fns;
  if (invokerFns) {
    // invoker
    return getHookAgumentsLength(
      Array.isArray(invokerFns)
        ? invokerFns[0]
        : invokerFns
    )
  } else {
    return (fn._length || fn.length) > 1
  }
}

function _enter (_, vnode) {
  if (!vnode.data.show) {
    enter(vnode);
  }
}

var transition = inBrowser ? {
  create: _enter,
  activate: _enter,
  remove: function remove$$1 (vnode, rm) {
    /* istanbul ignore else */
    if (!vnode.data.show) {
      leave(vnode, rm);
    } else {
      rm();
    }
  }
} : {};

var platformModules = [
  attrs,
  klass,
  events,
  domProps,
  style,
  transition
];

/*  */

// the directive module should be applied last, after all
// built-in modules have been applied.
var modules = platformModules.concat(baseModules);

var patch = createPatchFunction({ nodeOps: nodeOps, modules: modules });

/**
 * Not type checking this file because flow doesn't like attaching
 * properties to Elements.
 */

/* istanbul ignore if */
if (isIE9) {
  // http://www.matts411.com/post/internet-explorer-9-oninput/
  document.addEventListener('selectionchange', function () {
    var el = document.activeElement;
    if (el && el.vmodel) {
      trigger(el, 'input');
    }
  });
}

var model$1 = {
  inserted: function inserted (el, binding, vnode) {
    if (vnode.tag === 'select') {
      var cb = function () {
        setSelected(el, binding, vnode.context);
      };
      cb();
      /* istanbul ignore if */
      if (isIE || isEdge) {
        setTimeout(cb, 0);
      }
    } else if (vnode.tag === 'textarea' || el.type === 'text') {
      el._vModifiers = binding.modifiers;
      if (!binding.modifiers.lazy) {
        if (!isAndroid) {
          el.addEventListener('compositionstart', onCompositionStart);
          el.addEventListener('compositionend', onCompositionEnd);
        }
        /* istanbul ignore if */
        if (isIE9) {
          el.vmodel = true;
        }
      }
    }
  },
  componentUpdated: function componentUpdated (el, binding, vnode) {
    if (vnode.tag === 'select') {
      setSelected(el, binding, vnode.context);
      // in case the options rendered by v-for have changed,
      // it's possible that the value is out-of-sync with the rendered options.
      // detect such cases and filter out values that no longer has a matching
      // option in the DOM.
      var needReset = el.multiple
        ? binding.value.some(function (v) { return hasNoMatchingOption(v, el.options); })
        : binding.value !== binding.oldValue && hasNoMatchingOption(binding.value, el.options);
      if (needReset) {
        trigger(el, 'change');
      }
    }
  }
};

function setSelected (el, binding, vm) {
  var value = binding.value;
  var isMultiple = el.multiple;
  if (isMultiple && !Array.isArray(value)) {
    process.env.NODE_ENV !== 'production' && warn(
      "<select multiple v-model=\"" + (binding.expression) + "\"> " +
      "expects an Array value for its binding, but got " + (Object.prototype.toString.call(value).slice(8, -1)),
      vm
    );
    return
  }
  var selected, option;
  for (var i = 0, l = el.options.length; i < l; i++) {
    option = el.options[i];
    if (isMultiple) {
      selected = looseIndexOf(value, getValue(option)) > -1;
      if (option.selected !== selected) {
        option.selected = selected;
      }
    } else {
      if (looseEqual(getValue(option), value)) {
        if (el.selectedIndex !== i) {
          el.selectedIndex = i;
        }
        return
      }
    }
  }
  if (!isMultiple) {
    el.selectedIndex = -1;
  }
}

function hasNoMatchingOption (value, options) {
  for (var i = 0, l = options.length; i < l; i++) {
    if (looseEqual(getValue(options[i]), value)) {
      return false
    }
  }
  return true
}

function getValue (option) {
  return '_value' in option
    ? option._value
    : option.value
}

function onCompositionStart (e) {
  e.target.composing = true;
}

function onCompositionEnd (e) {
  e.target.composing = false;
  trigger(e.target, 'input');
}

function trigger (el, type) {
  var e = document.createEvent('HTMLEvents');
  e.initEvent(type, true, true);
  el.dispatchEvent(e);
}

/*  */

// recursively search for possible transition defined inside the component root
function locateNode (vnode) {
  return vnode.componentInstance && (!vnode.data || !vnode.data.transition)
    ? locateNode(vnode.componentInstance._vnode)
    : vnode
}

var show = {
  bind: function bind (el, ref, vnode) {
    var value = ref.value;

    vnode = locateNode(vnode);
    var transition = vnode.data && vnode.data.transition;
    var originalDisplay = el.__vOriginalDisplay =
      el.style.display === 'none' ? '' : el.style.display;
    if (value && transition && !isIE9) {
      vnode.data.show = true;
      enter(vnode, function () {
        el.style.display = originalDisplay;
      });
    } else {
      el.style.display = value ? originalDisplay : 'none';
    }
  },

  update: function update (el, ref, vnode) {
    var value = ref.value;
    var oldValue = ref.oldValue;

    /* istanbul ignore if */
    if (value === oldValue) { return }
    vnode = locateNode(vnode);
    var transition = vnode.data && vnode.data.transition;
    if (transition && !isIE9) {
      vnode.data.show = true;
      if (value) {
        enter(vnode, function () {
          el.style.display = el.__vOriginalDisplay;
        });
      } else {
        leave(vnode, function () {
          el.style.display = 'none';
        });
      }
    } else {
      el.style.display = value ? el.__vOriginalDisplay : 'none';
    }
  },

  unbind: function unbind (
    el,
    binding,
    vnode,
    oldVnode,
    isDestroy
  ) {
    if (!isDestroy) {
      el.style.display = el.__vOriginalDisplay;
    }
  }
};

var platformDirectives = {
  model: model$1,
  show: show
};

/*  */

// Provides transition support for a single element/component.
// supports transition mode (out-in / in-out)

var transitionProps = {
  name: String,
  appear: Boolean,
  css: Boolean,
  mode: String,
  type: String,
  enterClass: String,
  leaveClass: String,
  enterToClass: String,
  leaveToClass: String,
  enterActiveClass: String,
  leaveActiveClass: String,
  appearClass: String,
  appearActiveClass: String,
  appearToClass: String,
  duration: [Number, String, Object]
};

// in case the child is also an abstract component, e.g. <keep-alive>
// we want to recursively retrieve the real component to be rendered
function getRealChild (vnode) {
  var compOptions = vnode && vnode.componentOptions;
  if (compOptions && compOptions.Ctor.options.abstract) {
    return getRealChild(getFirstComponentChild(compOptions.children))
  } else {
    return vnode
  }
}

function extractTransitionData (comp) {
  var data = {};
  var options = comp.$options;
  // props
  for (var key in options.propsData) {
    data[key] = comp[key];
  }
  // events.
  // extract listeners and pass them directly to the transition methods
  var listeners = options._parentListeners;
  for (var key$1 in listeners) {
    data[camelize(key$1)] = listeners[key$1];
  }
  return data
}

function placeholder (h, rawChild) {
  return /\d-keep-alive$/.test(rawChild.tag)
    ? h('keep-alive')
    : null
}

function hasParentTransition (vnode) {
  while ((vnode = vnode.parent)) {
    if (vnode.data.transition) {
      return true
    }
  }
}

function isSameChild (child, oldChild) {
  return oldChild.key === child.key && oldChild.tag === child.tag
}

var Transition = {
  name: 'transition',
  props: transitionProps,
  abstract: true,

  render: function render (h) {
    var this$1 = this;

    var children = this.$slots.default;
    if (!children) {
      return
    }

    // filter out text nodes (possible whitespaces)
    children = children.filter(function (c) { return c.tag; });
    /* istanbul ignore if */
    if (!children.length) {
      return
    }

    // warn multiple elements
    if (process.env.NODE_ENV !== 'production' && children.length > 1) {
      warn(
        '<transition> can only be used on a single element. Use ' +
        '<transition-group> for lists.',
        this.$parent
      );
    }

    var mode = this.mode;

    // warn invalid mode
    if (process.env.NODE_ENV !== 'production' &&
        mode && mode !== 'in-out' && mode !== 'out-in') {
      warn(
        'invalid <transition> mode: ' + mode,
        this.$parent
      );
    }

    var rawChild = children[0];

    // if this is a component root node and the component's
    // parent container node also has transition, skip.
    if (hasParentTransition(this.$vnode)) {
      return rawChild
    }

    // apply transition data to child
    // use getRealChild() to ignore abstract components e.g. keep-alive
    var child = getRealChild(rawChild);
    /* istanbul ignore if */
    if (!child) {
      return rawChild
    }

    if (this._leaving) {
      return placeholder(h, rawChild)
    }

    // ensure a key that is unique to the vnode type and to this transition
    // component instance. This key will be used to remove pending leaving nodes
    // during entering.
    var id = "__transition-" + (this._uid) + "-";
    child.key = child.key == null
      ? id + child.tag
      : isPrimitive(child.key)
        ? (String(child.key).indexOf(id) === 0 ? child.key : id + child.key)
        : child.key;

    var data = (child.data || (child.data = {})).transition = extractTransitionData(this);
    var oldRawChild = this._vnode;
    var oldChild = getRealChild(oldRawChild);

    // mark v-show
    // so that the transition module can hand over the control to the directive
    if (child.data.directives && child.data.directives.some(function (d) { return d.name === 'show'; })) {
      child.data.show = true;
    }

    if (oldChild && oldChild.data && !isSameChild(child, oldChild)) {
      // replace old child transition data with fresh one
      // important for dynamic transitions!
      var oldData = oldChild && (oldChild.data.transition = extend({}, data));
      // handle transition mode
      if (mode === 'out-in') {
        // return placeholder node and queue update when leave finishes
        this._leaving = true;
        mergeVNodeHook(oldData, 'afterLeave', function () {
          this$1._leaving = false;
          this$1.$forceUpdate();
        });
        return placeholder(h, rawChild)
      } else if (mode === 'in-out') {
        var delayedLeave;
        var performLeave = function () { delayedLeave(); };
        mergeVNodeHook(data, 'afterEnter', performLeave);
        mergeVNodeHook(data, 'enterCancelled', performLeave);
        mergeVNodeHook(oldData, 'delayLeave', function (leave) { delayedLeave = leave; });
      }
    }

    return rawChild
  }
};

/*  */

// Provides transition support for list items.
// supports move transitions using the FLIP technique.

// Because the vdom's children update algorithm is "unstable" - i.e.
// it doesn't guarantee the relative positioning of removed elements,
// we force transition-group to update its children into two passes:
// in the first pass, we remove all nodes that need to be removed,
// triggering their leaving transition; in the second pass, we insert/move
// into the final disired state. This way in the second pass removed
// nodes will remain where they should be.

var props = extend({
  tag: String,
  moveClass: String
}, transitionProps);

delete props.mode;

var TransitionGroup = {
  props: props,

  render: function render (h) {
    var tag = this.tag || this.$vnode.data.tag || 'span';
    var map = Object.create(null);
    var prevChildren = this.prevChildren = this.children;
    var rawChildren = this.$slots.default || [];
    var children = this.children = [];
    var transitionData = extractTransitionData(this);

    for (var i = 0; i < rawChildren.length; i++) {
      var c = rawChildren[i];
      if (c.tag) {
        if (c.key != null && String(c.key).indexOf('__vlist') !== 0) {
          children.push(c);
          map[c.key] = c
          ;(c.data || (c.data = {})).transition = transitionData;
        } else if (process.env.NODE_ENV !== 'production') {
          var opts = c.componentOptions;
          var name = opts ? (opts.Ctor.options.name || opts.tag || '') : c.tag;
          warn(("<transition-group> children must be keyed: <" + name + ">"));
        }
      }
    }

    if (prevChildren) {
      var kept = [];
      var removed = [];
      for (var i$1 = 0; i$1 < prevChildren.length; i$1++) {
        var c$1 = prevChildren[i$1];
        c$1.data.transition = transitionData;
        c$1.data.pos = c$1.elm.getBoundingClientRect();
        if (map[c$1.key]) {
          kept.push(c$1);
        } else {
          removed.push(c$1);
        }
      }
      this.kept = h(tag, null, kept);
      this.removed = removed;
    }

    return h(tag, null, children)
  },

  beforeUpdate: function beforeUpdate () {
    // force removing pass
    this.__patch__(
      this._vnode,
      this.kept,
      false, // hydrating
      true // removeOnly (!important, avoids unnecessary moves)
    );
    this._vnode = this.kept;
  },

  updated: function updated () {
    var children = this.prevChildren;
    var moveClass = this.moveClass || ((this.name || 'v') + '-move');
    if (!children.length || !this.hasMove(children[0].elm, moveClass)) {
      return
    }

    // we divide the work into three loops to avoid mixing DOM reads and writes
    // in each iteration - which helps prevent layout thrashing.
    children.forEach(callPendingCbs);
    children.forEach(recordPosition);
    children.forEach(applyTranslation);

    // force reflow to put everything in position
    var body = document.body;
    var f = body.offsetHeight; // eslint-disable-line

    children.forEach(function (c) {
      if (c.data.moved) {
        var el = c.elm;
        var s = el.style;
        addTransitionClass(el, moveClass);
        s.transform = s.WebkitTransform = s.transitionDuration = '';
        el.addEventListener(transitionEndEvent, el._moveCb = function cb (e) {
          if (!e || /transform$/.test(e.propertyName)) {
            el.removeEventListener(transitionEndEvent, cb);
            el._moveCb = null;
            removeTransitionClass(el, moveClass);
          }
        });
      }
    });
  },

  methods: {
    hasMove: function hasMove (el, moveClass) {
      /* istanbul ignore if */
      if (!hasTransition) {
        return false
      }
      if (this._hasMove != null) {
        return this._hasMove
      }
      // Detect whether an element with the move class applied has
      // CSS transitions. Since the element may be inside an entering
      // transition at this very moment, we make a clone of it and remove
      // all other transition classes applied to ensure only the move class
      // is applied.
      var clone = el.cloneNode();
      if (el._transitionClasses) {
        el._transitionClasses.forEach(function (cls) { removeClass(clone, cls); });
      }
      addClass(clone, moveClass);
      clone.style.display = 'none';
      this.$el.appendChild(clone);
      var info = getTransitionInfo(clone);
      this.$el.removeChild(clone);
      return (this._hasMove = info.hasTransform)
    }
  }
};

function callPendingCbs (c) {
  /* istanbul ignore if */
  if (c.elm._moveCb) {
    c.elm._moveCb();
  }
  /* istanbul ignore if */
  if (c.elm._enterCb) {
    c.elm._enterCb();
  }
}

function recordPosition (c) {
  c.data.newPos = c.elm.getBoundingClientRect();
}

function applyTranslation (c) {
  var oldPos = c.data.pos;
  var newPos = c.data.newPos;
  var dx = oldPos.left - newPos.left;
  var dy = oldPos.top - newPos.top;
  if (dx || dy) {
    c.data.moved = true;
    var s = c.elm.style;
    s.transform = s.WebkitTransform = "translate(" + dx + "px," + dy + "px)";
    s.transitionDuration = '0s';
  }
}

var platformComponents = {
  Transition: Transition,
  TransitionGroup: TransitionGroup
};

/*  */

// install platform specific utils
Vue$3.config.mustUseProp = mustUseProp;
Vue$3.config.isReservedTag = isReservedTag;
Vue$3.config.getTagNamespace = getTagNamespace;
Vue$3.config.isUnknownElement = isUnknownElement;

// install platform runtime directives & components
extend(Vue$3.options.directives, platformDirectives);
extend(Vue$3.options.components, platformComponents);

// install platform patch function
Vue$3.prototype.__patch__ = inBrowser ? patch : noop;

// public mount method
Vue$3.prototype.$mount = function (
  el,
  hydrating
) {
  el = el && inBrowser ? query(el) : undefined;
  return mountComponent(this, el, hydrating)
};

// devtools global hook
/* istanbul ignore next */
setTimeout(function () {
  if (config.devtools) {
    if (devtools) {
      devtools.emit('init', Vue$3);
    } else if (process.env.NODE_ENV !== 'production' && isChrome) {
      console[console.info ? 'info' : 'log'](
        'Download the Vue Devtools extension for a better development experience:\n' +
        'https://github.com/vuejs/vue-devtools'
      );
    }
  }
  if (process.env.NODE_ENV !== 'production' &&
      config.productionTip !== false &&
      inBrowser && typeof console !== 'undefined') {
    console[console.info ? 'info' : 'log'](
      "You are running Vue in development mode.\n" +
      "Make sure to turn on production mode when deploying for production.\n" +
      "See more tips at https://vuejs.org/guide/deployment.html"
    );
  }
}, 0);

/*  */

// check whether current browser encodes a char inside attribute values
function shouldDecode (content, encoded) {
  var div = document.createElement('div');
  div.innerHTML = "<div a=\"" + content + "\">";
  return div.innerHTML.indexOf(encoded) > 0
}

// #3663
// IE encodes newlines inside attribute values while other browsers don't
var shouldDecodeNewlines = inBrowser ? shouldDecode('\n', '&#10;') : false;

/*  */

var isUnaryTag = makeMap(
  'area,base,br,col,embed,frame,hr,img,input,isindex,keygen,' +
  'link,meta,param,source,track,wbr',
  true
);

// Elements that you can, intentionally, leave open
// (and which close themselves)
var canBeLeftOpenTag = makeMap(
  'colgroup,dd,dt,li,options,p,td,tfoot,th,thead,tr,source',
  true
);

// HTML5 tags https://html.spec.whatwg.org/multipage/indices.html#elements-3
// Phrasing Content https://html.spec.whatwg.org/multipage/dom.html#phrasing-content
var isNonPhrasingTag = makeMap(
  'address,article,aside,base,blockquote,body,caption,col,colgroup,dd,' +
  'details,dialog,div,dl,dt,fieldset,figcaption,figure,footer,form,' +
  'h1,h2,h3,h4,h5,h6,head,header,hgroup,hr,html,legend,li,menuitem,meta,' +
  'optgroup,option,param,rp,rt,source,style,summary,tbody,td,tfoot,th,thead,' +
  'title,tr,track',
  true
);

/*  */

var decoder;

function decode (html) {
  decoder = decoder || document.createElement('div');
  decoder.innerHTML = html;
  return decoder.textContent
}

/**
 * Not type-checking this file because it's mostly vendor code.
 */

/*!
 * HTML Parser By John Resig (ejohn.org)
 * Modified by Juriy "kangax" Zaytsev
 * Original code by Erik Arvidsson, Mozilla Public License
 * http://erik.eae.net/simplehtmlparser/simplehtmlparser.js
 */

// Regular Expressions for parsing tags and attributes
var singleAttrIdentifier = /([^\s"'<>/=]+)/;
var singleAttrAssign = /(?:=)/;
var singleAttrValues = [
  // attr value double quotes
  /"([^"]*)"+/.source,
  // attr value, single quotes
  /'([^']*)'+/.source,
  // attr value, no quotes
  /([^\s"'=<>`]+)/.source
];
var attribute = new RegExp(
  '^\\s*' + singleAttrIdentifier.source +
  '(?:\\s*(' + singleAttrAssign.source + ')' +
  '\\s*(?:' + singleAttrValues.join('|') + '))?'
);

// could use https://www.w3.org/TR/1999/REC-xml-names-19990114/#NT-QName
// but for Vue templates we can enforce a simple charset
var ncname = '[a-zA-Z_][\\w\\-\\.]*';
var qnameCapture = '((?:' + ncname + '\\:)?' + ncname + ')';
var startTagOpen = new RegExp('^<' + qnameCapture);
var startTagClose = /^\s*(\/?)>/;
var endTag = new RegExp('^<\\/' + qnameCapture + '[^>]*>');
var doctype = /^<!DOCTYPE [^>]+>/i;
var comment = /^<!--/;
var conditionalComment = /^<!\[/;

var IS_REGEX_CAPTURING_BROKEN = false;
'x'.replace(/x(.)?/g, function (m, g) {
  IS_REGEX_CAPTURING_BROKEN = g === '';
});

// Special Elements (can contain anything)
var isScriptOrStyle = makeMap('script,style', true);
var reCache = {};

var decodingMap = {
  '&lt;': '<',
  '&gt;': '>',
  '&quot;': '"',
  '&amp;': '&',
  '&#10;': '\n'
};
var encodedAttr = /&(?:lt|gt|quot|amp);/g;
var encodedAttrWithNewLines = /&(?:lt|gt|quot|amp|#10);/g;

function decodeAttr (value, shouldDecodeNewlines) {
  var re = shouldDecodeNewlines ? encodedAttrWithNewLines : encodedAttr;
  return value.replace(re, function (match) { return decodingMap[match]; })
}

function parseHTML (html, options) {
  var stack = [];
  var expectHTML = options.expectHTML;
  var isUnaryTag$$1 = options.isUnaryTag || no;
  var index = 0;
  var last, lastTag;
  while (html) {
    last = html;
    // Make sure we're not in a script or style element
    if (!lastTag || !isScriptOrStyle(lastTag)) {
      var textEnd = html.indexOf('<');
      if (textEnd === 0) {
        // Comment:
        if (comment.test(html)) {
          var commentEnd = html.indexOf('-->');

          if (commentEnd >= 0) {
            advance(commentEnd + 3);
            continue
          }
        }

        // http://en.wikipedia.org/wiki/Conditional_comment#Downlevel-revealed_conditional_comment
        if (conditionalComment.test(html)) {
          var conditionalEnd = html.indexOf(']>');

          if (conditionalEnd >= 0) {
            advance(conditionalEnd + 2);
            continue
          }
        }

        // Doctype:
        var doctypeMatch = html.match(doctype);
        if (doctypeMatch) {
          advance(doctypeMatch[0].length);
          continue
        }

        // End tag:
        var endTagMatch = html.match(endTag);
        if (endTagMatch) {
          var curIndex = index;
          advance(endTagMatch[0].length);
          parseEndTag(endTagMatch[1], curIndex, index);
          continue
        }

        // Start tag:
        var startTagMatch = parseStartTag();
        if (startTagMatch) {
          handleStartTag(startTagMatch);
          continue
        }
      }

      var text = (void 0), rest$1 = (void 0), next = (void 0);
      if (textEnd >= 0) {
        rest$1 = html.slice(textEnd);
        while (
          !endTag.test(rest$1) &&
          !startTagOpen.test(rest$1) &&
          !comment.test(rest$1) &&
          !conditionalComment.test(rest$1)
        ) {
          // < in plain text, be forgiving and treat it as text
          next = rest$1.indexOf('<', 1);
          if (next < 0) { break }
          textEnd += next;
          rest$1 = html.slice(textEnd);
        }
        text = html.substring(0, textEnd);
        advance(textEnd);
      }

      if (textEnd < 0) {
        text = html;
        html = '';
      }

      if (options.chars && text) {
        options.chars(text);
      }
    } else {
      var stackedTag = lastTag.toLowerCase();
      var reStackedTag = reCache[stackedTag] || (reCache[stackedTag] = new RegExp('([\\s\\S]*?)(</' + stackedTag + '[^>]*>)', 'i'));
      var endTagLength = 0;
      var rest = html.replace(reStackedTag, function (all, text, endTag) {
        endTagLength = endTag.length;
        if (stackedTag !== 'script' && stackedTag !== 'style' && stackedTag !== 'noscript') {
          text = text
            .replace(/<!--([\s\S]*?)-->/g, '$1')
            .replace(/<!\[CDATA\[([\s\S]*?)]]>/g, '$1');
        }
        if (options.chars) {
          options.chars(text);
        }
        return ''
      });
      index += html.length - rest.length;
      html = rest;
      parseEndTag(stackedTag, index - endTagLength, index);
    }

    if (html === last) {
      options.chars && options.chars(html);
      if (process.env.NODE_ENV !== 'production' && !stack.length && options.warn) {
        options.warn(("Mal-formatted tag at end of template: \"" + html + "\""));
      }
      break
    }
  }

  // Clean up any remaining tags
  parseEndTag();

  function advance (n) {
    index += n;
    html = html.substring(n);
  }

  function parseStartTag () {
    var start = html.match(startTagOpen);
    if (start) {
      var match = {
        tagName: start[1],
        attrs: [],
        start: index
      };
      advance(start[0].length);
      var end, attr;
      while (!(end = html.match(startTagClose)) && (attr = html.match(attribute))) {
        advance(attr[0].length);
        match.attrs.push(attr);
      }
      if (end) {
        match.unarySlash = end[1];
        advance(end[0].length);
        match.end = index;
        return match
      }
    }
  }

  function handleStartTag (match) {
    var tagName = match.tagName;
    var unarySlash = match.unarySlash;

    if (expectHTML) {
      if (lastTag === 'p' && isNonPhrasingTag(tagName)) {
        parseEndTag(lastTag);
      }
      if (canBeLeftOpenTag(tagName) && lastTag === tagName) {
        parseEndTag(tagName);
      }
    }

    var unary = isUnaryTag$$1(tagName) || tagName === 'html' && lastTag === 'head' || !!unarySlash;

    var l = match.attrs.length;
    var attrs = new Array(l);
    for (var i = 0; i < l; i++) {
      var args = match.attrs[i];
      // hackish work around FF bug https://bugzilla.mozilla.org/show_bug.cgi?id=369778
      if (IS_REGEX_CAPTURING_BROKEN && args[0].indexOf('""') === -1) {
        if (args[3] === '') { delete args[3]; }
        if (args[4] === '') { delete args[4]; }
        if (args[5] === '') { delete args[5]; }
      }
      var value = args[3] || args[4] || args[5] || '';
      attrs[i] = {
        name: args[1],
        value: decodeAttr(
          value,
          options.shouldDecodeNewlines
        )
      };
    }

    if (!unary) {
      stack.push({ tag: tagName, lowerCasedTag: tagName.toLowerCase(), attrs: attrs });
      lastTag = tagName;
    }

    if (options.start) {
      options.start(tagName, attrs, unary, match.start, match.end);
    }
  }

  function parseEndTag (tagName, start, end) {
    var pos, lowerCasedTagName;
    if (start == null) { start = index; }
    if (end == null) { end = index; }

    if (tagName) {
      lowerCasedTagName = tagName.toLowerCase();
    }

    // Find the closest opened tag of the same type
    if (tagName) {
      for (pos = stack.length - 1; pos >= 0; pos--) {
        if (stack[pos].lowerCasedTag === lowerCasedTagName) {
          break
        }
      }
    } else {
      // If no tag name is provided, clean shop
      pos = 0;
    }

    if (pos >= 0) {
      // Close all the open elements, up the stack
      for (var i = stack.length - 1; i >= pos; i--) {
        if (process.env.NODE_ENV !== 'production' &&
            (i > pos || !tagName) &&
            options.warn) {
          options.warn(
            ("tag <" + (stack[i].tag) + "> has no matching end tag.")
          );
        }
        if (options.end) {
          options.end(stack[i].tag, start, end);
        }
      }

      // Remove the open elements from the stack
      stack.length = pos;
      lastTag = pos && stack[pos - 1].tag;
    } else if (lowerCasedTagName === 'br') {
      if (options.start) {
        options.start(tagName, [], true, start, end);
      }
    } else if (lowerCasedTagName === 'p') {
      if (options.start) {
        options.start(tagName, [], false, start, end);
      }
      if (options.end) {
        options.end(tagName, start, end);
      }
    }
  }
}

/*  */

var defaultTagRE = /\{\{((?:.|\n)+?)\}\}/g;
var regexEscapeRE = /[-.*+?^${}()|[\]\/\\]/g;

var buildRegex = cached(function (delimiters) {
  var open = delimiters[0].replace(regexEscapeRE, '\\$&');
  var close = delimiters[1].replace(regexEscapeRE, '\\$&');
  return new RegExp(open + '((?:.|\\n)+?)' + close, 'g')
});

function parseText (
  text,
  delimiters
) {
  var tagRE = delimiters ? buildRegex(delimiters) : defaultTagRE;
  if (!tagRE.test(text)) {
    return
  }
  var tokens = [];
  var lastIndex = tagRE.lastIndex = 0;
  var match, index;
  while ((match = tagRE.exec(text))) {
    index = match.index;
    // push text token
    if (index > lastIndex) {
      tokens.push(JSON.stringify(text.slice(lastIndex, index)));
    }
    // tag token
    var exp = parseFilters(match[1].trim());
    tokens.push(("_s(" + exp + ")"));
    lastIndex = index + match[0].length;
  }
  if (lastIndex < text.length) {
    tokens.push(JSON.stringify(text.slice(lastIndex)));
  }
  return tokens.join('+')
}

/*  */

var dirRE = /^v-|^@|^:/;
var forAliasRE = /(.*?)\s+(?:in|of)\s+(.*)/;
var forIteratorRE = /\((\{[^}]*\}|[^,]*),([^,]*)(?:,([^,]*))?\)/;
var bindRE = /^:|^v-bind:/;
var onRE = /^@|^v-on:/;
var argRE = /:(.*)$/;
var modifierRE = /\.[^.]+/g;

var decodeHTMLCached = cached(decode);

// configurable state
var warn$2;
var platformGetTagNamespace;
var platformMustUseProp;
var platformIsPreTag;
var preTransforms;
var transforms;
var postTransforms;
var delimiters;

/**
 * Convert HTML string to AST.
 */
function parse (
  template,
  options
) {
  warn$2 = options.warn || baseWarn;
  platformGetTagNamespace = options.getTagNamespace || no;
  platformMustUseProp = options.mustUseProp || no;
  platformIsPreTag = options.isPreTag || no;
  preTransforms = pluckModuleFunction(options.modules, 'preTransformNode');
  transforms = pluckModuleFunction(options.modules, 'transformNode');
  postTransforms = pluckModuleFunction(options.modules, 'postTransformNode');
  delimiters = options.delimiters;

  var stack = [];
  var preserveWhitespace = options.preserveWhitespace !== false;
  var root;
  var currentParent;
  var inVPre = false;
  var inPre = false;
  var warned = false;

  function endPre (element) {
    // check pre state
    if (element.pre) {
      inVPre = false;
    }
    if (platformIsPreTag(element.tag)) {
      inPre = false;
    }
  }

  parseHTML(template, {
    warn: warn$2,
    expectHTML: options.expectHTML,
    isUnaryTag: options.isUnaryTag,
    shouldDecodeNewlines: options.shouldDecodeNewlines,
    start: function start (tag, attrs, unary) {
      // check namespace.
      // inherit parent ns if there is one
      var ns = (currentParent && currentParent.ns) || platformGetTagNamespace(tag);

      // handle IE svg bug
      /* istanbul ignore if */
      if (isIE && ns === 'svg') {
        attrs = guardIESVGBug(attrs);
      }

      var element = {
        type: 1,
        tag: tag,
        attrsList: attrs,
        attrsMap: makeAttrsMap(attrs),
        parent: currentParent,
        children: []
      };
      if (ns) {
        element.ns = ns;
      }

      if (isForbiddenTag(element) && !isServerRendering()) {
        element.forbidden = true;
        process.env.NODE_ENV !== 'production' && warn$2(
          'Templates should only be responsible for mapping the state to the ' +
          'UI. Avoid placing tags with side-effects in your templates, such as ' +
          "<" + tag + ">" + ', as they will not be parsed.'
        );
      }

      // apply pre-transforms
      for (var i = 0; i < preTransforms.length; i++) {
        preTransforms[i](element, options);
      }

      if (!inVPre) {
        processPre(element);
        if (element.pre) {
          inVPre = true;
        }
      }
      if (platformIsPreTag(element.tag)) {
        inPre = true;
      }
      if (inVPre) {
        processRawAttrs(element);
      } else {
        processFor(element);
        processIf(element);
        processOnce(element);
        processKey(element);

        // determine whether this is a plain element after
        // removing structural attributes
        element.plain = !element.key && !attrs.length;

        processRef(element);
        processSlot(element);
        processComponent(element);
        for (var i$1 = 0; i$1 < transforms.length; i$1++) {
          transforms[i$1](element, options);
        }
        processAttrs(element);
      }

      function checkRootConstraints (el) {
        if (process.env.NODE_ENV !== 'production' && !warned) {
          if (el.tag === 'slot' || el.tag === 'template') {
            warned = true;
            warn$2(
              "Cannot use <" + (el.tag) + "> as component root element because it may " +
              'contain multiple nodes.'
            );
          }
          if (el.attrsMap.hasOwnProperty('v-for')) {
            warned = true;
            warn$2(
              'Cannot use v-for on stateful component root element because ' +
              'it renders multiple elements.'
            );
          }
        }
      }

      // tree management
      if (!root) {
        root = element;
        checkRootConstraints(root);
      } else if (!stack.length) {
        // allow root elements with v-if, v-else-if and v-else
        if (root.if && (element.elseif || element.else)) {
          checkRootConstraints(element);
          addIfCondition(root, {
            exp: element.elseif,
            block: element
          });
        } else if (process.env.NODE_ENV !== 'production' && !warned) {
          warned = true;
          warn$2(
            "Component template should contain exactly one root element. " +
            "If you are using v-if on multiple elements, " +
            "use v-else-if to chain them instead."
          );
        }
      }
      if (currentParent && !element.forbidden) {
        if (element.elseif || element.else) {
          processIfConditions(element, currentParent);
        } else if (element.slotScope) { // scoped slot
          currentParent.plain = false;
          var name = element.slotTarget || '"default"';(currentParent.scopedSlots || (currentParent.scopedSlots = {}))[name] = element;
        } else {
          currentParent.children.push(element);
          element.parent = currentParent;
        }
      }
      if (!unary) {
        currentParent = element;
        stack.push(element);
      } else {
        endPre(element);
      }
      // apply post-transforms
      for (var i$2 = 0; i$2 < postTransforms.length; i$2++) {
        postTransforms[i$2](element, options);
      }
    },

    end: function end () {
      // remove trailing whitespace
      var element = stack[stack.length - 1];
      var lastNode = element.children[element.children.length - 1];
      if (lastNode && lastNode.type === 3 && lastNode.text === ' ' && !inPre) {
        element.children.pop();
      }
      // pop stack
      stack.length -= 1;
      currentParent = stack[stack.length - 1];
      endPre(element);
    },

    chars: function chars (text) {
      if (!currentParent) {
        if (process.env.NODE_ENV !== 'production' && !warned && text === template) {
          warned = true;
          warn$2(
            'Component template requires a root element, rather than just text.'
          );
        }
        return
      }
      // IE textarea placeholder bug
      /* istanbul ignore if */
      if (isIE &&
          currentParent.tag === 'textarea' &&
          currentParent.attrsMap.placeholder === text) {
        return
      }
      var children = currentParent.children;
      text = inPre || text.trim()
        ? decodeHTMLCached(text)
        // only preserve whitespace if its not right after a starting tag
        : preserveWhitespace && children.length ? ' ' : '';
      if (text) {
        var expression;
        if (!inVPre && text !== ' ' && (expression = parseText(text, delimiters))) {
          children.push({
            type: 2,
            expression: expression,
            text: text
          });
        } else if (text !== ' ' || !children.length || children[children.length - 1].text !== ' ') {
          children.push({
            type: 3,
            text: text
          });
        }
      }
    }
  });
  return root
}

function processPre (el) {
  if (getAndRemoveAttr(el, 'v-pre') != null) {
    el.pre = true;
  }
}

function processRawAttrs (el) {
  var l = el.attrsList.length;
  if (l) {
    var attrs = el.attrs = new Array(l);
    for (var i = 0; i < l; i++) {
      attrs[i] = {
        name: el.attrsList[i].name,
        value: JSON.stringify(el.attrsList[i].value)
      };
    }
  } else if (!el.pre) {
    // non root node in pre blocks with no attributes
    el.plain = true;
  }
}

function processKey (el) {
  var exp = getBindingAttr(el, 'key');
  if (exp) {
    if (process.env.NODE_ENV !== 'production' && el.tag === 'template') {
      warn$2("<template> cannot be keyed. Place the key on real elements instead.");
    }
    el.key = exp;
  }
}

function processRef (el) {
  var ref = getBindingAttr(el, 'ref');
  if (ref) {
    el.ref = ref;
    el.refInFor = checkInFor(el);
  }
}

function processFor (el) {
  var exp;
  if ((exp = getAndRemoveAttr(el, 'v-for'))) {
    var inMatch = exp.match(forAliasRE);
    if (!inMatch) {
      process.env.NODE_ENV !== 'production' && warn$2(
        ("Invalid v-for expression: " + exp)
      );
      return
    }
    el.for = inMatch[2].trim();
    var alias = inMatch[1].trim();
    var iteratorMatch = alias.match(forIteratorRE);
    if (iteratorMatch) {
      el.alias = iteratorMatch[1].trim();
      el.iterator1 = iteratorMatch[2].trim();
      if (iteratorMatch[3]) {
        el.iterator2 = iteratorMatch[3].trim();
      }
    } else {
      el.alias = alias;
    }
  }
}

function processIf (el) {
  var exp = getAndRemoveAttr(el, 'v-if');
  if (exp) {
    el.if = exp;
    addIfCondition(el, {
      exp: exp,
      block: el
    });
  } else {
    if (getAndRemoveAttr(el, 'v-else') != null) {
      el.else = true;
    }
    var elseif = getAndRemoveAttr(el, 'v-else-if');
    if (elseif) {
      el.elseif = elseif;
    }
  }
}

function processIfConditions (el, parent) {
  var prev = findPrevElement(parent.children);
  if (prev && prev.if) {
    addIfCondition(prev, {
      exp: el.elseif,
      block: el
    });
  } else if (process.env.NODE_ENV !== 'production') {
    warn$2(
      "v-" + (el.elseif ? ('else-if="' + el.elseif + '"') : 'else') + " " +
      "used on element <" + (el.tag) + "> without corresponding v-if."
    );
  }
}

function findPrevElement (children) {
  var i = children.length;
  while (i--) {
    if (children[i].type === 1) {
      return children[i]
    } else {
      if (process.env.NODE_ENV !== 'production' && children[i].text !== ' ') {
        warn$2(
          "text \"" + (children[i].text.trim()) + "\" between v-if and v-else(-if) " +
          "will be ignored."
        );
      }
      children.pop();
    }
  }
}

function addIfCondition (el, condition) {
  if (!el.ifConditions) {
    el.ifConditions = [];
  }
  el.ifConditions.push(condition);
}

function processOnce (el) {
  var once$$1 = getAndRemoveAttr(el, 'v-once');
  if (once$$1 != null) {
    el.once = true;
  }
}

function processSlot (el) {
  if (el.tag === 'slot') {
    el.slotName = getBindingAttr(el, 'name');
    if (process.env.NODE_ENV !== 'production' && el.key) {
      warn$2(
        "`key` does not work on <slot> because slots are abstract outlets " +
        "and can possibly expand into multiple elements. " +
        "Use the key on a wrapping element instead."
      );
    }
  } else {
    var slotTarget = getBindingAttr(el, 'slot');
    if (slotTarget) {
      el.slotTarget = slotTarget === '""' ? '"default"' : slotTarget;
    }
    if (el.tag === 'template') {
      el.slotScope = getAndRemoveAttr(el, 'scope');
    }
  }
}

function processComponent (el) {
  var binding;
  if ((binding = getBindingAttr(el, 'is'))) {
    el.component = binding;
  }
  if (getAndRemoveAttr(el, 'inline-template') != null) {
    el.inlineTemplate = true;
  }
}

function processAttrs (el) {
  var list = el.attrsList;
  var i, l, name, rawName, value, arg, modifiers, isProp;
  for (i = 0, l = list.length; i < l; i++) {
    name = rawName = list[i].name;
    value = list[i].value;
    if (dirRE.test(name)) {
      // mark element as dynamic
      el.hasBindings = true;
      // modifiers
      modifiers = parseModifiers(name);
      if (modifiers) {
        name = name.replace(modifierRE, '');
      }
      if (bindRE.test(name)) { // v-bind
        name = name.replace(bindRE, '');
        value = parseFilters(value);
        isProp = false;
        if (modifiers) {
          if (modifiers.prop) {
            isProp = true;
            name = camelize(name);
            if (name === 'innerHtml') { name = 'innerHTML'; }
          }
          if (modifiers.camel) {
            name = camelize(name);
          }
        }
        if (isProp || platformMustUseProp(el.tag, el.attrsMap.type, name)) {
          addProp(el, name, value);
        } else {
          addAttr(el, name, value);
        }
      } else if (onRE.test(name)) { // v-on
        name = name.replace(onRE, '');
        addHandler(el, name, value, modifiers);
      } else { // normal directives
        name = name.replace(dirRE, '');
        // parse arg
        var argMatch = name.match(argRE);
        if (argMatch && (arg = argMatch[1])) {
          name = name.slice(0, -(arg.length + 1));
        }
        addDirective(el, name, rawName, value, arg, modifiers);
        if (process.env.NODE_ENV !== 'production' && name === 'model') {
          checkForAliasModel(el, value);
        }
      }
    } else {
      // literal attribute
      if (process.env.NODE_ENV !== 'production') {
        var expression = parseText(value, delimiters);
        if (expression) {
          warn$2(
            name + "=\"" + value + "\": " +
            'Interpolation inside attributes has been removed. ' +
            'Use v-bind or the colon shorthand instead. For example, ' +
            'instead of <div id="{{ val }}">, use <div :id="val">.'
          );
        }
      }
      addAttr(el, name, JSON.stringify(value));
    }
  }
}

function checkInFor (el) {
  var parent = el;
  while (parent) {
    if (parent.for !== undefined) {
      return true
    }
    parent = parent.parent;
  }
  return false
}

function parseModifiers (name) {
  var match = name.match(modifierRE);
  if (match) {
    var ret = {};
    match.forEach(function (m) { ret[m.slice(1)] = true; });
    return ret
  }
}

function makeAttrsMap (attrs) {
  var map = {};
  for (var i = 0, l = attrs.length; i < l; i++) {
    if (process.env.NODE_ENV !== 'production' && map[attrs[i].name] && !isIE) {
      warn$2('duplicate attribute: ' + attrs[i].name);
    }
    map[attrs[i].name] = attrs[i].value;
  }
  return map
}

function isForbiddenTag (el) {
  return (
    el.tag === 'style' ||
    (el.tag === 'script' && (
      !el.attrsMap.type ||
      el.attrsMap.type === 'text/javascript'
    ))
  )
}

var ieNSBug = /^xmlns:NS\d+/;
var ieNSPrefix = /^NS\d+:/;

/* istanbul ignore next */
function guardIESVGBug (attrs) {
  var res = [];
  for (var i = 0; i < attrs.length; i++) {
    var attr = attrs[i];
    if (!ieNSBug.test(attr.name)) {
      attr.name = attr.name.replace(ieNSPrefix, '');
      res.push(attr);
    }
  }
  return res
}

function checkForAliasModel (el, value) {
  var _el = el;
  while (_el) {
    if (_el.for && _el.alias === value) {
      warn$2(
        "<" + (el.tag) + " v-model=\"" + value + "\">: " +
        "You are binding v-model directly to a v-for iteration alias. " +
        "This will not be able to modify the v-for source array because " +
        "writing to the alias is like modifying a function local variable. " +
        "Consider using an array of objects and use v-model on an object property instead."
      );
    }
    _el = _el.parent;
  }
}

/*  */

var isStaticKey;
var isPlatformReservedTag;

var genStaticKeysCached = cached(genStaticKeys$1);

/**
 * Goal of the optimizer: walk the generated template AST tree
 * and detect sub-trees that are purely static, i.e. parts of
 * the DOM that never needs to change.
 *
 * Once we detect these sub-trees, we can:
 *
 * 1. Hoist them into constants, so that we no longer need to
 *    create fresh nodes for them on each re-render;
 * 2. Completely skip them in the patching process.
 */
function optimize (root, options) {
  if (!root) { return }
  isStaticKey = genStaticKeysCached(options.staticKeys || '');
  isPlatformReservedTag = options.isReservedTag || no;
  // first pass: mark all non-static nodes.
  markStatic$1(root);
  // second pass: mark static roots.
  markStaticRoots(root, false);
}

function genStaticKeys$1 (keys) {
  return makeMap(
    'type,tag,attrsList,attrsMap,plain,parent,children,attrs' +
    (keys ? ',' + keys : '')
  )
}

function markStatic$1 (node) {
  node.static = isStatic(node);
  if (node.type === 1) {
    // do not make component slot content static. this avoids
    // 1. components not able to mutate slot nodes
    // 2. static slot content fails for hot-reloading
    if (
      !isPlatformReservedTag(node.tag) &&
      node.tag !== 'slot' &&
      node.attrsMap['inline-template'] == null
    ) {
      return
    }
    for (var i = 0, l = node.children.length; i < l; i++) {
      var child = node.children[i];
      markStatic$1(child);
      if (!child.static) {
        node.static = false;
      }
    }
  }
}

function markStaticRoots (node, isInFor) {
  if (node.type === 1) {
    if (node.static || node.once) {
      node.staticInFor = isInFor;
    }
    // For a node to qualify as a static root, it should have children that
    // are not just static text. Otherwise the cost of hoisting out will
    // outweigh the benefits and it's better off to just always render it fresh.
    if (node.static && node.children.length && !(
      node.children.length === 1 &&
      node.children[0].type === 3
    )) {
      node.staticRoot = true;
      return
    } else {
      node.staticRoot = false;
    }
    if (node.children) {
      for (var i = 0, l = node.children.length; i < l; i++) {
        markStaticRoots(node.children[i], isInFor || !!node.for);
      }
    }
    if (node.ifConditions) {
      walkThroughConditionsBlocks(node.ifConditions, isInFor);
    }
  }
}

function walkThroughConditionsBlocks (conditionBlocks, isInFor) {
  for (var i = 1, len = conditionBlocks.length; i < len; i++) {
    markStaticRoots(conditionBlocks[i].block, isInFor);
  }
}

function isStatic (node) {
  if (node.type === 2) { // expression
    return false
  }
  if (node.type === 3) { // text
    return true
  }
  return !!(node.pre || (
    !node.hasBindings && // no dynamic bindings
    !node.if && !node.for && // not v-if or v-for or v-else
    !isBuiltInTag(node.tag) && // not a built-in
    isPlatformReservedTag(node.tag) && // not a component
    !isDirectChildOfTemplateFor(node) &&
    Object.keys(node).every(isStaticKey)
  ))
}

function isDirectChildOfTemplateFor (node) {
  while (node.parent) {
    node = node.parent;
    if (node.tag !== 'template') {
      return false
    }
    if (node.for) {
      return true
    }
  }
  return false
}

/*  */

var fnExpRE = /^\s*([\w$_]+|\([^)]*?\))\s*=>|^function\s*\(/;
var simplePathRE = /^\s*[A-Za-z_$][\w$]*(?:\.[A-Za-z_$][\w$]*|\['.*?']|\[".*?"]|\[\d+]|\[[A-Za-z_$][\w$]*])*\s*$/;

// keyCode aliases
var keyCodes = {
  esc: 27,
  tab: 9,
  enter: 13,
  space: 32,
  up: 38,
  left: 37,
  right: 39,
  down: 40,
  'delete': [8, 46]
};

// #4868: modifiers that prevent the execution of the listener
// need to explicitly return null so that we can determine whether to remove
// the listener for .once
var genGuard = function (condition) { return ("if(" + condition + ")return null;"); };

var modifierCode = {
  stop: '$event.stopPropagation();',
  prevent: '$event.preventDefault();',
  self: genGuard("$event.target !== $event.currentTarget"),
  ctrl: genGuard("!$event.ctrlKey"),
  shift: genGuard("!$event.shiftKey"),
  alt: genGuard("!$event.altKey"),
  meta: genGuard("!$event.metaKey"),
  left: genGuard("$event.button !== 0"),
  middle: genGuard("$event.button !== 1"),
  right: genGuard("$event.button !== 2")
};

function genHandlers (events, native) {
  var res = native ? 'nativeOn:{' : 'on:{';
  for (var name in events) {
    res += "\"" + name + "\":" + (genHandler(name, events[name])) + ",";
  }
  return res.slice(0, -1) + '}'
}

function genHandler (
  name,
  handler
) {
  if (!handler) {
    return 'function(){}'
  } else if (Array.isArray(handler)) {
    return ("[" + (handler.map(function (handler) { return genHandler(name, handler); }).join(',')) + "]")
  } else if (!handler.modifiers) {
    return fnExpRE.test(handler.value) || simplePathRE.test(handler.value)
      ? handler.value
      : ("function($event){" + (handler.value) + "}")
  } else {
    var code = '';
    var keys = [];
    for (var key in handler.modifiers) {
      if (modifierCode[key]) {
        code += modifierCode[key];
      } else {
        keys.push(key);
      }
    }
    if (keys.length) {
      code = genKeyFilter(keys) + code;
    }
    var handlerCode = simplePathRE.test(handler.value)
      ? handler.value + '($event)'
      : handler.value;
    return ("function($event){" + code + handlerCode + "}")
  }
}

function genKeyFilter (keys) {
  return ("if(" + (keys.map(genFilterCode).join('&&')) + ")return null;")
}

function genFilterCode (key) {
  var keyVal = parseInt(key, 10);
  if (keyVal) {
    return ("$event.keyCode!==" + keyVal)
  }
  var alias = keyCodes[key];
  return ("_k($event.keyCode," + (JSON.stringify(key)) + (alias ? ',' + JSON.stringify(alias) : '') + ")")
}

/*  */

function bind$1 (el, dir) {
  el.wrapData = function (code) {
    return ("_b(" + code + ",'" + (el.tag) + "'," + (dir.value) + (dir.modifiers && dir.modifiers.prop ? ',true' : '') + ")")
  };
}

/*  */

var baseDirectives = {
  bind: bind$1,
  cloak: noop
};

/*  */

// configurable state
var warn$3;
var transforms$1;
var dataGenFns;
var platformDirectives$1;
var isPlatformReservedTag$1;
var staticRenderFns;
var onceCount;
var currentOptions;

function generate (
  ast,
  options
) {
  // save previous staticRenderFns so generate calls can be nested
  var prevStaticRenderFns = staticRenderFns;
  var currentStaticRenderFns = staticRenderFns = [];
  var prevOnceCount = onceCount;
  onceCount = 0;
  currentOptions = options;
  warn$3 = options.warn || baseWarn;
  transforms$1 = pluckModuleFunction(options.modules, 'transformCode');
  dataGenFns = pluckModuleFunction(options.modules, 'genData');
  platformDirectives$1 = options.directives || {};
  isPlatformReservedTag$1 = options.isReservedTag || no;
  var code = ast ? genElement(ast) : '_c("div")';
  staticRenderFns = prevStaticRenderFns;
  onceCount = prevOnceCount;
  return {
    render: ("with(this){return " + code + "}"),
    staticRenderFns: currentStaticRenderFns
  }
}

function genElement (el) {
  if (el.staticRoot && !el.staticProcessed) {
    return genStatic(el)
  } else if (el.once && !el.onceProcessed) {
    return genOnce(el)
  } else if (el.for && !el.forProcessed) {
    return genFor(el)
  } else if (el.if && !el.ifProcessed) {
    return genIf(el)
  } else if (el.tag === 'template' && !el.slotTarget) {
    return genChildren(el) || 'void 0'
  } else if (el.tag === 'slot') {
    return genSlot(el)
  } else {
    // component or element
    var code;
    if (el.component) {
      code = genComponent(el.component, el);
    } else {
      var data = el.plain ? undefined : genData(el);

      var children = el.inlineTemplate ? null : genChildren(el, true);
      code = "_c('" + (el.tag) + "'" + (data ? ("," + data) : '') + (children ? ("," + children) : '') + ")";
    }
    // module transforms
    for (var i = 0; i < transforms$1.length; i++) {
      code = transforms$1[i](el, code);
    }
    return code
  }
}

// hoist static sub-trees out
function genStatic (el) {
  el.staticProcessed = true;
  staticRenderFns.push(("with(this){return " + (genElement(el)) + "}"));
  return ("_m(" + (staticRenderFns.length - 1) + (el.staticInFor ? ',true' : '') + ")")
}

// v-once
function genOnce (el) {
  el.onceProcessed = true;
  if (el.if && !el.ifProcessed) {
    return genIf(el)
  } else if (el.staticInFor) {
    var key = '';
    var parent = el.parent;
    while (parent) {
      if (parent.for) {
        key = parent.key;
        break
      }
      parent = parent.parent;
    }
    if (!key) {
      process.env.NODE_ENV !== 'production' && warn$3(
        "v-once can only be used inside v-for that is keyed. "
      );
      return genElement(el)
    }
    return ("_o(" + (genElement(el)) + "," + (onceCount++) + (key ? ("," + key) : "") + ")")
  } else {
    return genStatic(el)
  }
}

function genIf (el) {
  el.ifProcessed = true; // avoid recursion
  return genIfConditions(el.ifConditions.slice())
}

function genIfConditions (conditions) {
  if (!conditions.length) {
    return '_e()'
  }

  var condition = conditions.shift();
  if (condition.exp) {
    return ("(" + (condition.exp) + ")?" + (genTernaryExp(condition.block)) + ":" + (genIfConditions(conditions)))
  } else {
    return ("" + (genTernaryExp(condition.block)))
  }

  // v-if with v-once should generate code like (a)?_m(0):_m(1)
  function genTernaryExp (el) {
    return el.once ? genOnce(el) : genElement(el)
  }
}

function genFor (el) {
  var exp = el.for;
  var alias = el.alias;
  var iterator1 = el.iterator1 ? ("," + (el.iterator1)) : '';
  var iterator2 = el.iterator2 ? ("," + (el.iterator2)) : '';

  if (
    process.env.NODE_ENV !== 'production' &&
    maybeComponent(el) && el.tag !== 'slot' && el.tag !== 'template' && !el.key
  ) {
    warn$3(
      "<" + (el.tag) + " v-for=\"" + alias + " in " + exp + "\">: component lists rendered with " +
      "v-for should have explicit keys. " +
      "See https://vuejs.org/guide/list.html#key for more info.",
      true /* tip */
    );
  }

  el.forProcessed = true; // avoid recursion
  return "_l((" + exp + ")," +
    "function(" + alias + iterator1 + iterator2 + "){" +
      "return " + (genElement(el)) +
    '})'
}

function genData (el) {
  var data = '{';

  // directives first.
  // directives may mutate the el's other properties before they are generated.
  var dirs = genDirectives(el);
  if (dirs) { data += dirs + ','; }

  // key
  if (el.key) {
    data += "key:" + (el.key) + ",";
  }
  // ref
  if (el.ref) {
    data += "ref:" + (el.ref) + ",";
  }
  if (el.refInFor) {
    data += "refInFor:true,";
  }
  // pre
  if (el.pre) {
    data += "pre:true,";
  }
  // record original tag name for components using "is" attribute
  if (el.component) {
    data += "tag:\"" + (el.tag) + "\",";
  }
  // module data generation functions
  for (var i = 0; i < dataGenFns.length; i++) {
    data += dataGenFns[i](el);
  }
  // attributes
  if (el.attrs) {
    data += "attrs:{" + (genProps(el.attrs)) + "},";
  }
  // DOM props
  if (el.props) {
    data += "domProps:{" + (genProps(el.props)) + "},";
  }
  // event handlers
  if (el.events) {
    data += (genHandlers(el.events)) + ",";
  }
  if (el.nativeEvents) {
    data += (genHandlers(el.nativeEvents, true)) + ",";
  }
  // slot target
  if (el.slotTarget) {
    data += "slot:" + (el.slotTarget) + ",";
  }
  // scoped slots
  if (el.scopedSlots) {
    data += (genScopedSlots(el.scopedSlots)) + ",";
  }
  // component v-model
  if (el.model) {
    data += "model:{value:" + (el.model.value) + ",callback:" + (el.model.callback) + "},";
  }
  // inline-template
  if (el.inlineTemplate) {
    var inlineTemplate = genInlineTemplate(el);
    if (inlineTemplate) {
      data += inlineTemplate + ",";
    }
  }
  data = data.replace(/,$/, '') + '}';
  // v-bind data wrap
  if (el.wrapData) {
    data = el.wrapData(data);
  }
  return data
}

function genDirectives (el) {
  var dirs = el.directives;
  if (!dirs) { return }
  var res = 'directives:[';
  var hasRuntime = false;
  var i, l, dir, needRuntime;
  for (i = 0, l = dirs.length; i < l; i++) {
    dir = dirs[i];
    needRuntime = true;
    var gen = platformDirectives$1[dir.name] || baseDirectives[dir.name];
    if (gen) {
      // compile-time directive that manipulates AST.
      // returns true if it also needs a runtime counterpart.
      needRuntime = !!gen(el, dir, warn$3);
    }
    if (needRuntime) {
      hasRuntime = true;
      res += "{name:\"" + (dir.name) + "\",rawName:\"" + (dir.rawName) + "\"" + (dir.value ? (",value:(" + (dir.value) + "),expression:" + (JSON.stringify(dir.value))) : '') + (dir.arg ? (",arg:\"" + (dir.arg) + "\"") : '') + (dir.modifiers ? (",modifiers:" + (JSON.stringify(dir.modifiers))) : '') + "},";
    }
  }
  if (hasRuntime) {
    return res.slice(0, -1) + ']'
  }
}

function genInlineTemplate (el) {
  var ast = el.children[0];
  if (process.env.NODE_ENV !== 'production' && (
    el.children.length > 1 || ast.type !== 1
  )) {
    warn$3('Inline-template components must have exactly one child element.');
  }
  if (ast.type === 1) {
    var inlineRenderFns = generate(ast, currentOptions);
    return ("inlineTemplate:{render:function(){" + (inlineRenderFns.render) + "},staticRenderFns:[" + (inlineRenderFns.staticRenderFns.map(function (code) { return ("function(){" + code + "}"); }).join(',')) + "]}")
  }
}

function genScopedSlots (slots) {
  return ("scopedSlots:_u([" + (Object.keys(slots).map(function (key) { return genScopedSlot(key, slots[key]); }).join(',')) + "])")
}

function genScopedSlot (key, el) {
  return "[" + key + ",function(" + (String(el.attrsMap.scope)) + "){" +
    "return " + (el.tag === 'template'
      ? genChildren(el) || 'void 0'
      : genElement(el)) + "}]"
}

function genChildren (el, checkSkip) {
  var children = el.children;
  if (children.length) {
    var el$1 = children[0];
    // optimize single v-for
    if (children.length === 1 &&
        el$1.for &&
        el$1.tag !== 'template' &&
        el$1.tag !== 'slot') {
      return genElement(el$1)
    }
    var normalizationType = getNormalizationType(children);
    return ("[" + (children.map(genNode).join(',')) + "]" + (checkSkip
        ? normalizationType ? ("," + normalizationType) : ''
        : ''))
  }
}

// determine the normalization needed for the children array.
// 0: no normalization needed
// 1: simple normalization needed (possible 1-level deep nested array)
// 2: full normalization needed
function getNormalizationType (children) {
  var res = 0;
  for (var i = 0; i < children.length; i++) {
    var el = children[i];
    if (el.type !== 1) {
      continue
    }
    if (needsNormalization(el) ||
        (el.ifConditions && el.ifConditions.some(function (c) { return needsNormalization(c.block); }))) {
      res = 2;
      break
    }
    if (maybeComponent(el) ||
        (el.ifConditions && el.ifConditions.some(function (c) { return maybeComponent(c.block); }))) {
      res = 1;
    }
  }
  return res
}

function needsNormalization (el) {
  return el.for !== undefined || el.tag === 'template' || el.tag === 'slot'
}

function maybeComponent (el) {
  return !isPlatformReservedTag$1(el.tag)
}

function genNode (node) {
  if (node.type === 1) {
    return genElement(node)
  } else {
    return genText(node)
  }
}

function genText (text) {
  return ("_v(" + (text.type === 2
    ? text.expression // no need for () because already wrapped in _s()
    : transformSpecialNewlines(JSON.stringify(text.text))) + ")")
}

function genSlot (el) {
  var slotName = el.slotName || '"default"';
  var children = genChildren(el);
  var res = "_t(" + slotName + (children ? ("," + children) : '');
  var attrs = el.attrs && ("{" + (el.attrs.map(function (a) { return ((camelize(a.name)) + ":" + (a.value)); }).join(',')) + "}");
  var bind$$1 = el.attrsMap['v-bind'];
  if ((attrs || bind$$1) && !children) {
    res += ",null";
  }
  if (attrs) {
    res += "," + attrs;
  }
  if (bind$$1) {
    res += (attrs ? '' : ',null') + "," + bind$$1;
  }
  return res + ')'
}

// componentName is el.component, take it as argument to shun flow's pessimistic refinement
function genComponent (componentName, el) {
  var children = el.inlineTemplate ? null : genChildren(el, true);
  return ("_c(" + componentName + "," + (genData(el)) + (children ? ("," + children) : '') + ")")
}

function genProps (props) {
  var res = '';
  for (var i = 0; i < props.length; i++) {
    var prop = props[i];
    res += "\"" + (prop.name) + "\":" + (transformSpecialNewlines(prop.value)) + ",";
  }
  return res.slice(0, -1)
}

// #3895, #4268
function transformSpecialNewlines (text) {
  return text
    .replace(/\u2028/g, '\\u2028')
    .replace(/\u2029/g, '\\u2029')
}

/*  */

// operators like typeof, instanceof and in are allowed
var prohibitedKeywordRE = new RegExp('\\b' + (
  'do,if,for,let,new,try,var,case,else,with,await,break,catch,class,const,' +
  'super,throw,while,yield,delete,export,import,return,switch,default,' +
  'extends,finally,continue,debugger,function,arguments'
).split(',').join('\\b|\\b') + '\\b');
// check valid identifier for v-for
var identRE = /[A-Za-z_$][\w$]*/;
// strip strings in expressions
var stripStringRE = /'(?:[^'\\]|\\.)*'|"(?:[^"\\]|\\.)*"|`(?:[^`\\]|\\.)*\$\{|\}(?:[^`\\]|\\.)*`|`(?:[^`\\]|\\.)*`/g;

// detect problematic expressions in a template
function detectErrors (ast) {
  var errors = [];
  if (ast) {
    checkNode(ast, errors);
  }
  return errors
}

function checkNode (node, errors) {
  if (node.type === 1) {
    for (var name in node.attrsMap) {
      if (dirRE.test(name)) {
        var value = node.attrsMap[name];
        if (value) {
          if (name === 'v-for') {
            checkFor(node, ("v-for=\"" + value + "\""), errors);
          } else {
            checkExpression(value, (name + "=\"" + value + "\""), errors);
          }
        }
      }
    }
    if (node.children) {
      for (var i = 0; i < node.children.length; i++) {
        checkNode(node.children[i], errors);
      }
    }
  } else if (node.type === 2) {
    checkExpression(node.expression, node.text, errors);
  }
}

function checkFor (node, text, errors) {
  checkExpression(node.for || '', text, errors);
  checkIdentifier(node.alias, 'v-for alias', text, errors);
  checkIdentifier(node.iterator1, 'v-for iterator', text, errors);
  checkIdentifier(node.iterator2, 'v-for iterator', text, errors);
}

function checkIdentifier (ident, type, text, errors) {
  if (typeof ident === 'string' && !identRE.test(ident)) {
    errors.push(("invalid " + type + " \"" + ident + "\" in expression: " + (text.trim())));
  }
}

function checkExpression (exp, text, errors) {
  try {
    new Function(("return " + exp));
  } catch (e) {
    var keywordMatch = exp.replace(stripStringRE, '').match(prohibitedKeywordRE);
    if (keywordMatch) {
      errors.push(
        "avoid using JavaScript keyword as property name: " +
        "\"" + (keywordMatch[0]) + "\" in expression " + (text.trim())
      );
    } else {
      errors.push(("invalid expression: " + (text.trim())));
    }
  }
}

/*  */

function baseCompile (
  template,
  options
) {
  var ast = parse(template.trim(), options);
  optimize(ast, options);
  var code = generate(ast, options);
  return {
    ast: ast,
    render: code.render,
    staticRenderFns: code.staticRenderFns
  }
}

function makeFunction (code, errors) {
  try {
    return new Function(code)
  } catch (err) {
    errors.push({ err: err, code: code });
    return noop
  }
}

function createCompiler (baseOptions) {
  var functionCompileCache = Object.create(null);

  function compile (
    template,
    options
  ) {
    var finalOptions = Object.create(baseOptions);
    var errors = [];
    var tips = [];
    finalOptions.warn = function (msg, tip$$1) {
      (tip$$1 ? tips : errors).push(msg);
    };

    if (options) {
      // merge custom modules
      if (options.modules) {
        finalOptions.modules = (baseOptions.modules || []).concat(options.modules);
      }
      // merge custom directives
      if (options.directives) {
        finalOptions.directives = extend(
          Object.create(baseOptions.directives),
          options.directives
        );
      }
      // copy other options
      for (var key in options) {
        if (key !== 'modules' && key !== 'directives') {
          finalOptions[key] = options[key];
        }
      }
    }

    var compiled = baseCompile(template, finalOptions);
    if (process.env.NODE_ENV !== 'production') {
      errors.push.apply(errors, detectErrors(compiled.ast));
    }
    compiled.errors = errors;
    compiled.tips = tips;
    return compiled
  }

  function compileToFunctions (
    template,
    options,
    vm
  ) {
    options = options || {};

    /* istanbul ignore if */
    if (process.env.NODE_ENV !== 'production') {
      // detect possible CSP restriction
      try {
        new Function('return 1');
      } catch (e) {
        if (e.toString().match(/unsafe-eval|CSP/)) {
          warn(
            'It seems you are using the standalone build of Vue.js in an ' +
            'environment with Content Security Policy that prohibits unsafe-eval. ' +
            'The template compiler cannot work in this environment. Consider ' +
            'relaxing the policy to allow unsafe-eval or pre-compiling your ' +
            'templates into render functions.'
          );
        }
      }
    }

    // check cache
    var key = options.delimiters
      ? String(options.delimiters) + template
      : template;
    if (functionCompileCache[key]) {
      return functionCompileCache[key]
    }

    // compile
    var compiled = compile(template, options);

    // check compilation errors/tips
    if (process.env.NODE_ENV !== 'production') {
      if (compiled.errors && compiled.errors.length) {
        warn(
          "Error compiling template:\n\n" + template + "\n\n" +
          compiled.errors.map(function (e) { return ("- " + e); }).join('\n') + '\n',
          vm
        );
      }
      if (compiled.tips && compiled.tips.length) {
        compiled.tips.forEach(function (msg) { return tip(msg, vm); });
      }
    }

    // turn code into functions
    var res = {};
    var fnGenErrors = [];
    res.render = makeFunction(compiled.render, fnGenErrors);
    var l = compiled.staticRenderFns.length;
    res.staticRenderFns = new Array(l);
    for (var i = 0; i < l; i++) {
      res.staticRenderFns[i] = makeFunction(compiled.staticRenderFns[i], fnGenErrors);
    }

    // check function generation errors.
    // this should only happen if there is a bug in the compiler itself.
    // mostly for codegen development use
    /* istanbul ignore if */
    if (process.env.NODE_ENV !== 'production') {
      if ((!compiled.errors || !compiled.errors.length) && fnGenErrors.length) {
        warn(
          "Failed to generate render function:\n\n" +
          fnGenErrors.map(function (ref) {
            var err = ref.err;
            var code = ref.code;

            return ((err.toString()) + " in\n\n" + code + "\n");
        }).join('\n'),
          vm
        );
      }
    }

    return (functionCompileCache[key] = res)
  }

  return {
    compile: compile,
    compileToFunctions: compileToFunctions
  }
}

/*  */

function transformNode (el, options) {
  var warn = options.warn || baseWarn;
  var staticClass = getAndRemoveAttr(el, 'class');
  if (process.env.NODE_ENV !== 'production' && staticClass) {
    var expression = parseText(staticClass, options.delimiters);
    if (expression) {
      warn(
        "class=\"" + staticClass + "\": " +
        'Interpolation inside attributes has been removed. ' +
        'Use v-bind or the colon shorthand instead. For example, ' +
        'instead of <div class="{{ val }}">, use <div :class="val">.'
      );
    }
  }
  if (staticClass) {
    el.staticClass = JSON.stringify(staticClass);
  }
  var classBinding = getBindingAttr(el, 'class', false /* getStatic */);
  if (classBinding) {
    el.classBinding = classBinding;
  }
}

function genData$1 (el) {
  var data = '';
  if (el.staticClass) {
    data += "staticClass:" + (el.staticClass) + ",";
  }
  if (el.classBinding) {
    data += "class:" + (el.classBinding) + ",";
  }
  return data
}

var klass$1 = {
  staticKeys: ['staticClass'],
  transformNode: transformNode,
  genData: genData$1
};

/*  */

function transformNode$1 (el, options) {
  var warn = options.warn || baseWarn;
  var staticStyle = getAndRemoveAttr(el, 'style');
  if (staticStyle) {
    /* istanbul ignore if */
    if (process.env.NODE_ENV !== 'production') {
      var expression = parseText(staticStyle, options.delimiters);
      if (expression) {
        warn(
          "style=\"" + staticStyle + "\": " +
          'Interpolation inside attributes has been removed. ' +
          'Use v-bind or the colon shorthand instead. For example, ' +
          'instead of <div style="{{ val }}">, use <div :style="val">.'
        );
      }
    }
    el.staticStyle = JSON.stringify(parseStyleText(staticStyle));
  }

  var styleBinding = getBindingAttr(el, 'style', false /* getStatic */);
  if (styleBinding) {
    el.styleBinding = styleBinding;
  }
}

function genData$2 (el) {
  var data = '';
  if (el.staticStyle) {
    data += "staticStyle:" + (el.staticStyle) + ",";
  }
  if (el.styleBinding) {
    data += "style:(" + (el.styleBinding) + "),";
  }
  return data
}

var style$1 = {
  staticKeys: ['staticStyle'],
  transformNode: transformNode$1,
  genData: genData$2
};

var modules$1 = [
  klass$1,
  style$1
];

/*  */

function text (el, dir) {
  if (dir.value) {
    addProp(el, 'textContent', ("_s(" + (dir.value) + ")"));
  }
}

/*  */

function html (el, dir) {
  if (dir.value) {
    addProp(el, 'innerHTML', ("_s(" + (dir.value) + ")"));
  }
}

var directives$1 = {
  model: model,
  text: text,
  html: html
};

/*  */

var baseOptions = {
  expectHTML: true,
  modules: modules$1,
  directives: directives$1,
  isPreTag: isPreTag,
  isUnaryTag: isUnaryTag,
  mustUseProp: mustUseProp,
  isReservedTag: isReservedTag,
  getTagNamespace: getTagNamespace,
  staticKeys: genStaticKeys(modules$1)
};

var ref$1 = createCompiler(baseOptions);
var compileToFunctions = ref$1.compileToFunctions;

/*  */

var idToTemplate = cached(function (id) {
  var el = query(id);
  return el && el.innerHTML
});

var mount = Vue$3.prototype.$mount;
Vue$3.prototype.$mount = function (
  el,
  hydrating
) {
  el = el && query(el);

  /* istanbul ignore if */
  if (el === document.body || el === document.documentElement) {
    process.env.NODE_ENV !== 'production' && warn(
      "Do not mount Vue to <html> or <body> - mount to normal elements instead."
    );
    return this
  }

  var options = this.$options;
  // resolve template/el and convert to render function
  if (!options.render) {
    var template = options.template;
    if (template) {
      if (typeof template === 'string') {
        if (template.charAt(0) === '#') {
          template = idToTemplate(template);
          /* istanbul ignore if */
          if (process.env.NODE_ENV !== 'production' && !template) {
            warn(
              ("Template element not found or is empty: " + (options.template)),
              this
            );
          }
        }
      } else if (template.nodeType) {
        template = template.innerHTML;
      } else {
        if (process.env.NODE_ENV !== 'production') {
          warn('invalid template option:' + template, this);
        }
        return this
      }
    } else if (el) {
      template = getOuterHTML(el);
    }
    if (template) {
      /* istanbul ignore if */
      if (process.env.NODE_ENV !== 'production' && config.performance && perf) {
        perf.mark('compile');
      }

      var ref = compileToFunctions(template, {
        shouldDecodeNewlines: shouldDecodeNewlines,
        delimiters: options.delimiters
      }, this);
      var render = ref.render;
      var staticRenderFns = ref.staticRenderFns;
      options.render = render;
      options.staticRenderFns = staticRenderFns;

      /* istanbul ignore if */
      if (process.env.NODE_ENV !== 'production' && config.performance && perf) {
        perf.mark('compile end');
        perf.measure(((this._name) + " compile"), 'compile', 'compile end');
      }
    }
  }
  return mount.call(this, el, hydrating)
};

/**
 * Get outerHTML of elements, taking care
 * of SVG elements in IE as well.
 */
function getOuterHTML (el) {
  if (el.outerHTML) {
    return el.outerHTML
  } else {
    var container = document.createElement('div');
    container.appendChild(el.cloneNode(true));
    return container.innerHTML
  }
}

Vue$3.compile = compileToFunctions;

module.exports = Vue$3;

/* WEBPACK VAR INJECTION */}.call(exports, __webpack_require__(6), __webpack_require__(5)))

/***/ }),
/* 8 */,
/* 9 */,
/* 10 */,
/* 11 */,
/* 12 */,
/* 13 */,
/* 14 */,
/* 15 */,
/* 16 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";


Object.defineProperty(exports, "__esModule", {
  value: true
});

var _vue = __webpack_require__(7);

var _vue2 = _interopRequireDefault(_vue);

var _vuex = __webpack_require__(110);

var _vuex2 = _interopRequireDefault(_vuex);

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

_vue2.default.use(_vuex2.default);

// root state object.
// each Vuex instance is just a single state tree.
var state = {
  products: []
};

// mutations are operations that actually mutates the state.
// each mutation handler gets the entire state tree as the
// first argument, followed by additional payload arguments.
// mutations must be synchronous and can be recorded by plugins
// for debugging purposes.
var mutations = {
  addProducts: function addProducts(state, products) {
    state.products = products;
  }
};

// actions are functions that causes side effects and can involve
// asynchronous operations.
var actions = {};

// getters are functions
var getters = {};

// A Vuex instance is created by combining the state, mutations, actions,
// and getters.
exports.default = new _vuex2.default.Store({
  state: state,
  getters: getters,
  actions: actions,
  mutations: mutations
});

/***/ }),
/* 17 */,
/* 18 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";


var Component = __webpack_require__(1)(
/* script */
__webpack_require__(30),
/* template */
__webpack_require__(104),
/* scopeId */
null,
/* cssModules */
null);
Component.options.__file = "/Users/nMartin/Documents/PrestaShop/admin-dev/themes/new-theme/js/stock-page/components/app.vue";
if (Component.esModule && Object.keys(Component.esModule).some(function (key) {
  return key !== "default" && key !== "__esModule";
})) {
  console.error("named exports are not supported in *.vue files.");
}
if (Component.options.functional) {
  console.error("[vue-loader] app.vue: functional components are not supported with templates, they should use render functions.");
}

/* hot reload */
if (false) {
  (function () {
    var hotAPI = require("vue-hot-reload-api");
    hotAPI.install(require("vue"), false);
    if (!hotAPI.compatible) return;
    module.hot.accept();
    if (!module.hot.data) {
      hotAPI.createRecord("data-v-616d468c", Component.options);
    } else {
      hotAPI.reload("data-v-616d468c", Component.options);
    }
  })();
}

module.exports = Component.exports;

/***/ }),
/* 19 */,
/* 20 */,
/* 21 */,
/* 22 */,
/* 23 */,
/* 24 */,
/* 25 */,
/* 26 */,
/* 27 */,
/* 28 */,
/* 29 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";
/*!
 * vue-resource v1.2.1
 * https://github.com/pagekit/vue-resource
 * Released under the MIT License.
 */



/**
 * Promises/A+ polyfill v1.1.4 (https://github.com/bramstein/promis)
 */

var RESOLVED = 0;
var REJECTED = 1;
var PENDING  = 2;

function Promise$1(executor) {

    this.state = PENDING;
    this.value = undefined;
    this.deferred = [];

    var promise = this;

    try {
        executor(function (x) {
            promise.resolve(x);
        }, function (r) {
            promise.reject(r);
        });
    } catch (e) {
        promise.reject(e);
    }
}

Promise$1.reject = function (r) {
    return new Promise$1(function (resolve, reject) {
        reject(r);
    });
};

Promise$1.resolve = function (x) {
    return new Promise$1(function (resolve, reject) {
        resolve(x);
    });
};

Promise$1.all = function all(iterable) {
    return new Promise$1(function (resolve, reject) {
        var count = 0, result = [];

        if (iterable.length === 0) {
            resolve(result);
        }

        function resolver(i) {
            return function (x) {
                result[i] = x;
                count += 1;

                if (count === iterable.length) {
                    resolve(result);
                }
            };
        }

        for (var i = 0; i < iterable.length; i += 1) {
            Promise$1.resolve(iterable[i]).then(resolver(i), reject);
        }
    });
};

Promise$1.race = function race(iterable) {
    return new Promise$1(function (resolve, reject) {
        for (var i = 0; i < iterable.length; i += 1) {
            Promise$1.resolve(iterable[i]).then(resolve, reject);
        }
    });
};

var p$1 = Promise$1.prototype;

p$1.resolve = function resolve(x) {
    var promise = this;

    if (promise.state === PENDING) {
        if (x === promise) {
            throw new TypeError('Promise settled with itself.');
        }

        var called = false;

        try {
            var then = x && x['then'];

            if (x !== null && typeof x === 'object' && typeof then === 'function') {
                then.call(x, function (x) {
                    if (!called) {
                        promise.resolve(x);
                    }
                    called = true;

                }, function (r) {
                    if (!called) {
                        promise.reject(r);
                    }
                    called = true;
                });
                return;
            }
        } catch (e) {
            if (!called) {
                promise.reject(e);
            }
            return;
        }

        promise.state = RESOLVED;
        promise.value = x;
        promise.notify();
    }
};

p$1.reject = function reject(reason) {
    var promise = this;

    if (promise.state === PENDING) {
        if (reason === promise) {
            throw new TypeError('Promise settled with itself.');
        }

        promise.state = REJECTED;
        promise.value = reason;
        promise.notify();
    }
};

p$1.notify = function notify() {
    var promise = this;

    nextTick(function () {
        if (promise.state !== PENDING) {
            while (promise.deferred.length) {
                var deferred = promise.deferred.shift(),
                    onResolved = deferred[0],
                    onRejected = deferred[1],
                    resolve = deferred[2],
                    reject = deferred[3];

                try {
                    if (promise.state === RESOLVED) {
                        if (typeof onResolved === 'function') {
                            resolve(onResolved.call(undefined, promise.value));
                        } else {
                            resolve(promise.value);
                        }
                    } else if (promise.state === REJECTED) {
                        if (typeof onRejected === 'function') {
                            resolve(onRejected.call(undefined, promise.value));
                        } else {
                            reject(promise.value);
                        }
                    }
                } catch (e) {
                    reject(e);
                }
            }
        }
    });
};

p$1.then = function then(onResolved, onRejected) {
    var promise = this;

    return new Promise$1(function (resolve, reject) {
        promise.deferred.push([onResolved, onRejected, resolve, reject]);
        promise.notify();
    });
};

p$1.catch = function (onRejected) {
    return this.then(undefined, onRejected);
};

/**
 * Promise adapter.
 */

if (typeof Promise === 'undefined') {
    window.Promise = Promise$1;
}

function PromiseObj(executor, context) {

    if (executor instanceof Promise) {
        this.promise = executor;
    } else {
        this.promise = new Promise(executor.bind(context));
    }

    this.context = context;
}

PromiseObj.all = function (iterable, context) {
    return new PromiseObj(Promise.all(iterable), context);
};

PromiseObj.resolve = function (value, context) {
    return new PromiseObj(Promise.resolve(value), context);
};

PromiseObj.reject = function (reason, context) {
    return new PromiseObj(Promise.reject(reason), context);
};

PromiseObj.race = function (iterable, context) {
    return new PromiseObj(Promise.race(iterable), context);
};

var p = PromiseObj.prototype;

p.bind = function (context) {
    this.context = context;
    return this;
};

p.then = function (fulfilled, rejected) {

    if (fulfilled && fulfilled.bind && this.context) {
        fulfilled = fulfilled.bind(this.context);
    }

    if (rejected && rejected.bind && this.context) {
        rejected = rejected.bind(this.context);
    }

    return new PromiseObj(this.promise.then(fulfilled, rejected), this.context);
};

p.catch = function (rejected) {

    if (rejected && rejected.bind && this.context) {
        rejected = rejected.bind(this.context);
    }

    return new PromiseObj(this.promise.catch(rejected), this.context);
};

p.finally = function (callback) {

    return this.then(function (value) {
            callback.call(this);
            return value;
        }, function (reason) {
            callback.call(this);
            return Promise.reject(reason);
        }
    );
};

/**
 * Utility functions.
 */

var ref = {};
var hasOwnProperty = ref.hasOwnProperty;

var ref$1 = [];
var slice = ref$1.slice;
var debug = false;
var ntick;

var inBrowser = typeof window !== 'undefined';

var Util = function (ref) {
    var config = ref.config;
    var nextTick = ref.nextTick;

    ntick = nextTick;
    debug = config.debug || !config.silent;
};

function warn(msg) {
    if (typeof console !== 'undefined' && debug) {
        console.warn('[VueResource warn]: ' + msg);
    }
}

function error(msg) {
    if (typeof console !== 'undefined') {
        console.error(msg);
    }
}

function nextTick(cb, ctx) {
    return ntick(cb, ctx);
}

function trim(str) {
    return str ? str.replace(/^\s*|\s*$/g, '') : '';
}

function toLower(str) {
    return str ? str.toLowerCase() : '';
}

function toUpper(str) {
    return str ? str.toUpperCase() : '';
}

var isArray = Array.isArray;

function isString(val) {
    return typeof val === 'string';
}



function isFunction(val) {
    return typeof val === 'function';
}

function isObject(obj) {
    return obj !== null && typeof obj === 'object';
}

function isPlainObject(obj) {
    return isObject(obj) && Object.getPrototypeOf(obj) == Object.prototype;
}

function isBlob(obj) {
    return typeof Blob !== 'undefined' && obj instanceof Blob;
}

function isFormData(obj) {
    return typeof FormData !== 'undefined' && obj instanceof FormData;
}

function when(value, fulfilled, rejected) {

    var promise = PromiseObj.resolve(value);

    if (arguments.length < 2) {
        return promise;
    }

    return promise.then(fulfilled, rejected);
}

function options(fn, obj, opts) {

    opts = opts || {};

    if (isFunction(opts)) {
        opts = opts.call(obj);
    }

    return merge(fn.bind({$vm: obj, $options: opts}), fn, {$options: opts});
}

function each(obj, iterator) {

    var i, key;

    if (isArray(obj)) {
        for (i = 0; i < obj.length; i++) {
            iterator.call(obj[i], obj[i], i);
        }
    } else if (isObject(obj)) {
        for (key in obj) {
            if (hasOwnProperty.call(obj, key)) {
                iterator.call(obj[key], obj[key], key);
            }
        }
    }

    return obj;
}

var assign = Object.assign || _assign;

function merge(target) {

    var args = slice.call(arguments, 1);

    args.forEach(function (source) {
        _merge(target, source, true);
    });

    return target;
}

function defaults(target) {

    var args = slice.call(arguments, 1);

    args.forEach(function (source) {

        for (var key in source) {
            if (target[key] === undefined) {
                target[key] = source[key];
            }
        }

    });

    return target;
}

function _assign(target) {

    var args = slice.call(arguments, 1);

    args.forEach(function (source) {
        _merge(target, source);
    });

    return target;
}

function _merge(target, source, deep) {
    for (var key in source) {
        if (deep && (isPlainObject(source[key]) || isArray(source[key]))) {
            if (isPlainObject(source[key]) && !isPlainObject(target[key])) {
                target[key] = {};
            }
            if (isArray(source[key]) && !isArray(target[key])) {
                target[key] = [];
            }
            _merge(target[key], source[key], deep);
        } else if (source[key] !== undefined) {
            target[key] = source[key];
        }
    }
}

/**
 * Root Prefix Transform.
 */

var root = function (options$$1, next) {

    var url = next(options$$1);

    if (isString(options$$1.root) && !url.match(/^(https?:)?\//)) {
        url = options$$1.root + '/' + url;
    }

    return url;
};

/**
 * Query Parameter Transform.
 */

var query = function (options$$1, next) {

    var urlParams = Object.keys(Url.options.params), query = {}, url = next(options$$1);

    each(options$$1.params, function (value, key) {
        if (urlParams.indexOf(key) === -1) {
            query[key] = value;
        }
    });

    query = Url.params(query);

    if (query) {
        url += (url.indexOf('?') == -1 ? '?' : '&') + query;
    }

    return url;
};

/**
 * URL Template v2.0.6 (https://github.com/bramstein/url-template)
 */

function expand(url, params, variables) {

    var tmpl = parse(url), expanded = tmpl.expand(params);

    if (variables) {
        variables.push.apply(variables, tmpl.vars);
    }

    return expanded;
}

function parse(template) {

    var operators = ['+', '#', '.', '/', ';', '?', '&'], variables = [];

    return {
        vars: variables,
        expand: function expand(context) {
            return template.replace(/\{([^\{\}]+)\}|([^\{\}]+)/g, function (_, expression, literal) {
                if (expression) {

                    var operator = null, values = [];

                    if (operators.indexOf(expression.charAt(0)) !== -1) {
                        operator = expression.charAt(0);
                        expression = expression.substr(1);
                    }

                    expression.split(/,/g).forEach(function (variable) {
                        var tmp = /([^:\*]*)(?::(\d+)|(\*))?/.exec(variable);
                        values.push.apply(values, getValues(context, operator, tmp[1], tmp[2] || tmp[3]));
                        variables.push(tmp[1]);
                    });

                    if (operator && operator !== '+') {

                        var separator = ',';

                        if (operator === '?') {
                            separator = '&';
                        } else if (operator !== '#') {
                            separator = operator;
                        }

                        return (values.length !== 0 ? operator : '') + values.join(separator);
                    } else {
                        return values.join(',');
                    }

                } else {
                    return encodeReserved(literal);
                }
            });
        }
    };
}

function getValues(context, operator, key, modifier) {

    var value = context[key], result = [];

    if (isDefined(value) && value !== '') {
        if (typeof value === 'string' || typeof value === 'number' || typeof value === 'boolean') {
            value = value.toString();

            if (modifier && modifier !== '*') {
                value = value.substring(0, parseInt(modifier, 10));
            }

            result.push(encodeValue(operator, value, isKeyOperator(operator) ? key : null));
        } else {
            if (modifier === '*') {
                if (Array.isArray(value)) {
                    value.filter(isDefined).forEach(function (value) {
                        result.push(encodeValue(operator, value, isKeyOperator(operator) ? key : null));
                    });
                } else {
                    Object.keys(value).forEach(function (k) {
                        if (isDefined(value[k])) {
                            result.push(encodeValue(operator, value[k], k));
                        }
                    });
                }
            } else {
                var tmp = [];

                if (Array.isArray(value)) {
                    value.filter(isDefined).forEach(function (value) {
                        tmp.push(encodeValue(operator, value));
                    });
                } else {
                    Object.keys(value).forEach(function (k) {
                        if (isDefined(value[k])) {
                            tmp.push(encodeURIComponent(k));
                            tmp.push(encodeValue(operator, value[k].toString()));
                        }
                    });
                }

                if (isKeyOperator(operator)) {
                    result.push(encodeURIComponent(key) + '=' + tmp.join(','));
                } else if (tmp.length !== 0) {
                    result.push(tmp.join(','));
                }
            }
        }
    } else {
        if (operator === ';') {
            result.push(encodeURIComponent(key));
        } else if (value === '' && (operator === '&' || operator === '?')) {
            result.push(encodeURIComponent(key) + '=');
        } else if (value === '') {
            result.push('');
        }
    }

    return result;
}

function isDefined(value) {
    return value !== undefined && value !== null;
}

function isKeyOperator(operator) {
    return operator === ';' || operator === '&' || operator === '?';
}

function encodeValue(operator, value, key) {

    value = (operator === '+' || operator === '#') ? encodeReserved(value) : encodeURIComponent(value);

    if (key) {
        return encodeURIComponent(key) + '=' + value;
    } else {
        return value;
    }
}

function encodeReserved(str) {
    return str.split(/(%[0-9A-Fa-f]{2})/g).map(function (part) {
        if (!/%[0-9A-Fa-f]/.test(part)) {
            part = encodeURI(part);
        }
        return part;
    }).join('');
}

/**
 * URL Template (RFC 6570) Transform.
 */

var template = function (options) {

    var variables = [], url = expand(options.url, options.params, variables);

    variables.forEach(function (key) {
        delete options.params[key];
    });

    return url;
};

/**
 * Service for URL templating.
 */

function Url(url, params) {

    var self = this || {}, options$$1 = url, transform;

    if (isString(url)) {
        options$$1 = {url: url, params: params};
    }

    options$$1 = merge({}, Url.options, self.$options, options$$1);

    Url.transforms.forEach(function (handler) {
        transform = factory(handler, transform, self.$vm);
    });

    return transform(options$$1);
}

/**
 * Url options.
 */

Url.options = {
    url: '',
    root: null,
    params: {}
};

/**
 * Url transforms.
 */

Url.transforms = [template, query, root];

/**
 * Encodes a Url parameter string.
 *
 * @param {Object} obj
 */

Url.params = function (obj) {

    var params = [], escape = encodeURIComponent;

    params.add = function (key, value) {

        if (isFunction(value)) {
            value = value();
        }

        if (value === null) {
            value = '';
        }

        this.push(escape(key) + '=' + escape(value));
    };

    serialize(params, obj);

    return params.join('&').replace(/%20/g, '+');
};

/**
 * Parse a URL and return its components.
 *
 * @param {String} url
 */

Url.parse = function (url) {

    var el = document.createElement('a');

    if (document.documentMode) {
        el.href = url;
        url = el.href;
    }

    el.href = url;

    return {
        href: el.href,
        protocol: el.protocol ? el.protocol.replace(/:$/, '') : '',
        port: el.port,
        host: el.host,
        hostname: el.hostname,
        pathname: el.pathname.charAt(0) === '/' ? el.pathname : '/' + el.pathname,
        search: el.search ? el.search.replace(/^\?/, '') : '',
        hash: el.hash ? el.hash.replace(/^#/, '') : ''
    };
};

function factory(handler, next, vm) {
    return function (options$$1) {
        return handler.call(vm, options$$1, next);
    };
}

function serialize(params, obj, scope) {

    var array = isArray(obj), plain = isPlainObject(obj), hash;

    each(obj, function (value, key) {

        hash = isObject(value) || isArray(value);

        if (scope) {
            key = scope + '[' + (plain || hash ? key : '') + ']';
        }

        if (!scope && array) {
            params.add(value.name, value.value);
        } else if (hash) {
            serialize(params, value, key);
        } else {
            params.add(key, value);
        }
    });
}

/**
 * XDomain client (Internet Explorer).
 */

var xdrClient = function (request) {
    return new PromiseObj(function (resolve) {

        var xdr = new XDomainRequest(), handler = function (ref) {
            var type = ref.type;


            var status = 0;

            if (type === 'load') {
                status = 200;
            } else if (type === 'error') {
                status = 500;
            }

            resolve(request.respondWith(xdr.responseText, {status: status}));
        };

        request.abort = function () { return xdr.abort(); };

        xdr.open(request.method, request.getUrl());

        if (request.timeout) {
            xdr.timeout = request.timeout;
        }

        xdr.onload = handler;
        xdr.onabort = handler;
        xdr.onerror = handler;
        xdr.ontimeout = handler;
        xdr.onprogress = function () {};
        xdr.send(request.getBody());
    });
};

/**
 * CORS Interceptor.
 */

var SUPPORTS_CORS = inBrowser && 'withCredentials' in new XMLHttpRequest();

var cors = function (request, next) {

    if (inBrowser) {

        var orgUrl = Url.parse(location.href);
        var reqUrl = Url.parse(request.getUrl());

        if (reqUrl.protocol !== orgUrl.protocol || reqUrl.host !== orgUrl.host) {

            request.crossOrigin = true;
            request.emulateHTTP = false;

            if (!SUPPORTS_CORS) {
                request.client = xdrClient;
            }
        }
    }

    next();
};

/**
 * Body Interceptor.
 */

var body = function (request, next) {

    if (isFormData(request.body)) {

        request.headers.delete('Content-Type');

    } else if (isObject(request.body) || isArray(request.body)) {

        if (request.emulateJSON) {
            request.body = Url.params(request.body);
            request.headers.set('Content-Type', 'application/x-www-form-urlencoded');
        } else {
            request.body = JSON.stringify(request.body);
        }
    }

    next(function (response) {

        Object.defineProperty(response, 'data', {

            get: function get() {
                return this.body;
            },

            set: function set(body) {
                this.body = body;
            }

        });

        return response.bodyText ? when(response.text(), function (text) {

            var type = response.headers.get('Content-Type') || '';

            if (type.indexOf('application/json') === 0 || isJson(text)) {

                try {
                    response.body = JSON.parse(text);
                } catch (e) {
                    response.body = null;
                }

            } else {
                response.body = text;
            }

            return response;

        }) : response;

    });
};

function isJson(str) {

    var start = str.match(/^\[|^\{(?!\{)/), end = {'[': /]$/, '{': /}$/};

    return start && end[start[0]].test(str);
}

/**
 * JSONP client (Browser).
 */

var jsonpClient = function (request) {
    return new PromiseObj(function (resolve) {

        var name = request.jsonp || 'callback', callback = request.jsonpCallback || '_jsonp' + Math.random().toString(36).substr(2), body = null, handler, script;

        handler = function (ref) {
            var type = ref.type;


            var status = 0;

            if (type === 'load' && body !== null) {
                status = 200;
            } else if (type === 'error') {
                status = 500;
            }

            if (status && window[callback]) {
                delete window[callback];
                document.body.removeChild(script);
            }

            resolve(request.respondWith(body, {status: status}));
        };

        window[callback] = function (result) {
            body = JSON.stringify(result);
        };

        request.abort = function () {
            handler({type: 'abort'});
        };

        request.params[name] = callback;

        if (request.timeout) {
            setTimeout(request.abort, request.timeout);
        }

        script = document.createElement('script');
        script.src = request.getUrl();
        script.type = 'text/javascript';
        script.async = true;
        script.onload = handler;
        script.onerror = handler;

        document.body.appendChild(script);
    });
};

/**
 * JSONP Interceptor.
 */

var jsonp = function (request, next) {

    if (request.method == 'JSONP') {
        request.client = jsonpClient;
    }

    next();
};

/**
 * Before Interceptor.
 */

var before = function (request, next) {

    if (isFunction(request.before)) {
        request.before.call(this, request);
    }

    next();
};

/**
 * HTTP method override Interceptor.
 */

var method = function (request, next) {

    if (request.emulateHTTP && /^(PUT|PATCH|DELETE)$/i.test(request.method)) {
        request.headers.set('X-HTTP-Method-Override', request.method);
        request.method = 'POST';
    }

    next();
};

/**
 * Header Interceptor.
 */

var header = function (request, next) {

    var headers = assign({}, Http.headers.common,
        !request.crossOrigin ? Http.headers.custom : {},
        Http.headers[toLower(request.method)]
    );

    each(headers, function (value, name) {
        if (!request.headers.has(name)) {
            request.headers.set(name, value);
        }
    });

    next();
};

/**
 * XMLHttp client (Browser).
 */

var SUPPORTS_BLOB = typeof Blob !== 'undefined' && typeof FileReader !== 'undefined';

var xhrClient = function (request) {
    return new PromiseObj(function (resolve) {

        var xhr = new XMLHttpRequest(), handler = function (event) {

            var response = request.respondWith(
                'response' in xhr ? xhr.response : xhr.responseText, {
                    status: xhr.status === 1223 ? 204 : xhr.status, // IE9 status bug
                    statusText: xhr.status === 1223 ? 'No Content' : trim(xhr.statusText)
                }
            );

            each(trim(xhr.getAllResponseHeaders()).split('\n'), function (row) {
                response.headers.append(row.slice(0, row.indexOf(':')), row.slice(row.indexOf(':') + 1));
            });

            resolve(response);
        };

        request.abort = function () { return xhr.abort(); };

        if (request.progress) {
            if (request.method === 'GET') {
                xhr.addEventListener('progress', request.progress);
            } else if (/^(POST|PUT)$/i.test(request.method)) {
                xhr.upload.addEventListener('progress', request.progress);
            }
        }

        xhr.open(request.method, request.getUrl(), true);

        if (request.timeout) {
            xhr.timeout = request.timeout;
        }

        if (request.credentials === true) {
            xhr.withCredentials = true;
        }

        if (!request.crossOrigin) {
            request.headers.set('X-Requested-With', 'XMLHttpRequest');
        }

        if ('responseType' in xhr && SUPPORTS_BLOB) {
            xhr.responseType = 'blob';
        }

        request.headers.forEach(function (value, name) {
            xhr.setRequestHeader(name, value);
        });

        xhr.onload = handler;
        xhr.onabort = handler;
        xhr.onerror = handler;
        xhr.ontimeout = handler;
        xhr.send(request.getBody());
    });
};

/**
 * Http client (Node).
 */

var nodeClient = function (request) {

    var client = __webpack_require__(111);

    return new PromiseObj(function (resolve) {

        var url = request.getUrl();
        var body = request.getBody();
        var method = request.method;
        var headers = {}, handler;

        request.headers.forEach(function (value, name) {
            headers[name] = value;
        });

        client(url, {body: body, method: method, headers: headers}).then(handler = function (resp) {

            var response = request.respondWith(resp.body, {
                    status: resp.statusCode,
                    statusText: trim(resp.statusMessage)
                }
            );

            each(resp.headers, function (value, name) {
                response.headers.set(name, value);
            });

            resolve(response);

        }, function (error$$1) { return handler(error$$1.response); });
    });
};

/**
 * Base client.
 */

var Client = function (context) {

    var reqHandlers = [sendRequest], resHandlers = [], handler;

    if (!isObject(context)) {
        context = null;
    }

    function Client(request) {
        return new PromiseObj(function (resolve) {

            function exec() {

                handler = reqHandlers.pop();

                if (isFunction(handler)) {
                    handler.call(context, request, next);
                } else {
                    warn(("Invalid interceptor of type " + (typeof handler) + ", must be a function"));
                    next();
                }
            }

            function next(response) {

                if (isFunction(response)) {

                    resHandlers.unshift(response);

                } else if (isObject(response)) {

                    resHandlers.forEach(function (handler) {
                        response = when(response, function (response) {
                            return handler.call(context, response) || response;
                        });
                    });

                    when(response, resolve);

                    return;
                }

                exec();
            }

            exec();

        }, context);
    }

    Client.use = function (handler) {
        reqHandlers.push(handler);
    };

    return Client;
};

function sendRequest(request, resolve) {

    var client = request.client || (inBrowser ? xhrClient : nodeClient);

    resolve(client(request));
}

/**
 * HTTP Headers.
 */

var Headers = function Headers(headers) {
    var this$1 = this;


    this.map = {};

    each(headers, function (value, name) { return this$1.append(name, value); });
};

Headers.prototype.has = function has (name) {
    return getName(this.map, name) !== null;
};

Headers.prototype.get = function get (name) {

    var list = this.map[getName(this.map, name)];

    return list ? list.join() : null;
};

Headers.prototype.getAll = function getAll (name) {
    return this.map[getName(this.map, name)] || [];
};

Headers.prototype.set = function set (name, value) {
    this.map[normalizeName(getName(this.map, name) || name)] = [trim(value)];
};

Headers.prototype.append = function append (name, value){

    var list = this.map[getName(this.map, name)];

    if (list) {
        list.push(trim(value));
    } else {
        this.set(name, value);
    }
};

Headers.prototype.delete = function delete$1 (name){
    delete this.map[getName(this.map, name)];
};

Headers.prototype.deleteAll = function deleteAll (){
    this.map = {};
};

Headers.prototype.forEach = function forEach (callback, thisArg) {
        var this$1 = this;

    each(this.map, function (list, name) {
        each(list, function (value) { return callback.call(thisArg, value, name, this$1); });
    });
};

function getName(map, name) {
    return Object.keys(map).reduce(function (prev, curr) {
        return toLower(name) === toLower(curr) ? curr : prev;
    }, null);
}

function normalizeName(name) {

    if (/[^a-z0-9\-#$%&'*+.\^_`|~]/i.test(name)) {
        throw new TypeError('Invalid character in header field name');
    }

    return trim(name);
}

/**
 * HTTP Response.
 */

var Response = function Response(body, ref) {
    var url = ref.url;
    var headers = ref.headers;
    var status = ref.status;
    var statusText = ref.statusText;


    this.url = url;
    this.ok = status >= 200 && status < 300;
    this.status = status || 0;
    this.statusText = statusText || '';
    this.headers = new Headers(headers);
    this.body = body;

    if (isString(body)) {

        this.bodyText = body;

    } else if (isBlob(body)) {

        this.bodyBlob = body;

        if (isBlobText(body)) {
            this.bodyText = blobText(body);
        }
    }
};

Response.prototype.blob = function blob () {
    return when(this.bodyBlob);
};

Response.prototype.text = function text () {
    return when(this.bodyText);
};

Response.prototype.json = function json () {
    return when(this.text(), function (text) { return JSON.parse(text); });
};

function blobText(body) {
    return new PromiseObj(function (resolve) {

        var reader = new FileReader();

        reader.readAsText(body);
        reader.onload = function () {
            resolve(reader.result);
        };

    });
}

function isBlobText(body) {
    return body.type.indexOf('text') === 0 || body.type.indexOf('json') !== -1;
}

/**
 * HTTP Request.
 */

var Request = function Request(options$$1) {

    this.body = null;
    this.params = {};

    assign(this, options$$1, {
        method: toUpper(options$$1.method || 'GET')
    });

    if (!(this.headers instanceof Headers)) {
        this.headers = new Headers(this.headers);
    }
};

Request.prototype.getUrl = function getUrl (){
    return Url(this);
};

Request.prototype.getBody = function getBody (){
    return this.body;
};

Request.prototype.respondWith = function respondWith (body, options$$1) {
    return new Response(body, assign(options$$1 || {}, {url: this.getUrl()}));
};

/**
 * Service for sending network requests.
 */

var COMMON_HEADERS = {'Accept': 'application/json, text/plain, */*'};
var JSON_CONTENT_TYPE = {'Content-Type': 'application/json;charset=utf-8'};

function Http(options$$1) {

    var self = this || {}, client = Client(self.$vm);

    defaults(options$$1 || {}, self.$options, Http.options);

    Http.interceptors.forEach(function (handler) {
        client.use(handler);
    });

    return client(new Request(options$$1)).then(function (response) {

        return response.ok ? response : PromiseObj.reject(response);

    }, function (response) {

        if (response instanceof Error) {
            error(response);
        }

        return PromiseObj.reject(response);
    });
}

Http.options = {};

Http.headers = {
    put: JSON_CONTENT_TYPE,
    post: JSON_CONTENT_TYPE,
    patch: JSON_CONTENT_TYPE,
    delete: JSON_CONTENT_TYPE,
    common: COMMON_HEADERS,
    custom: {}
};

Http.interceptors = [before, method, body, jsonp, header, cors];

['get', 'delete', 'head', 'jsonp'].forEach(function (method$$1) {

    Http[method$$1] = function (url, options$$1) {
        return this(assign(options$$1 || {}, {url: url, method: method$$1}));
    };

});

['post', 'put', 'patch'].forEach(function (method$$1) {

    Http[method$$1] = function (url, body$$1, options$$1) {
        return this(assign(options$$1 || {}, {url: url, method: method$$1, body: body$$1}));
    };

});

/**
 * Service for interacting with RESTful services.
 */

function Resource(url, params, actions, options$$1) {

    var self = this || {}, resource = {};

    actions = assign({},
        Resource.actions,
        actions
    );

    each(actions, function (action, name) {

        action = merge({url: url, params: assign({}, params)}, options$$1, action);

        resource[name] = function () {
            return (self.$http || Http)(opts(action, arguments));
        };
    });

    return resource;
}

function opts(action, args) {

    var options$$1 = assign({}, action), params = {}, body;

    switch (args.length) {

        case 2:

            params = args[0];
            body = args[1];

            break;

        case 1:

            if (/^(POST|PUT|PATCH)$/i.test(options$$1.method)) {
                body = args[0];
            } else {
                params = args[0];
            }

            break;

        case 0:

            break;

        default:

            throw 'Expected up to 2 arguments [params, body], got ' + args.length + ' arguments';
    }

    options$$1.body = body;
    options$$1.params = assign({}, options$$1.params, params);

    return options$$1;
}

Resource.actions = {

    get: {method: 'GET'},
    save: {method: 'POST'},
    query: {method: 'GET'},
    update: {method: 'PUT'},
    remove: {method: 'DELETE'},
    delete: {method: 'DELETE'}

};

/**
 * Install plugin.
 */

function plugin(Vue) {

    if (plugin.installed) {
        return;
    }

    Util(Vue);

    Vue.url = Url;
    Vue.http = Http;
    Vue.resource = Resource;
    Vue.Promise = PromiseObj;

    Object.defineProperties(Vue.prototype, {

        $url: {
            get: function get() {
                return options(Vue.url, this, this.$options.url);
            }
        },

        $http: {
            get: function get() {
                return options(Vue.http, this, this.$options.http);
            }
        },

        $resource: {
            get: function get() {
                return Vue.resource.bind(this);
            }
        },

        $promise: {
            get: function get() {
                var this$1 = this;

                return function (executor) { return new Vue.Promise(executor, this$1); };
            }
        }

    });
}

if (typeof window !== 'undefined' && window.Vue) {
    window.Vue.use(plugin);
}

module.exports = plugin;


/***/ }),
/* 30 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
Object.defineProperty(__webpack_exports__, "__esModule", { value: true });
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0__stock_header__ = __webpack_require__(61);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0__stock_header___default = __webpack_require__.n(__WEBPACK_IMPORTED_MODULE_0__stock_header__);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1__products_actions__ = __webpack_require__(57);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1__products_actions___default = __webpack_require__.n(__WEBPACK_IMPORTED_MODULE_1__products_actions__);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_2__products_table__ = __webpack_require__(58);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_2__products_table___default = __webpack_require__.n(__WEBPACK_IMPORTED_MODULE_2__products_table__);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_3__pagination__ = __webpack_require__(55);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_3__pagination___default = __webpack_require__.n(__WEBPACK_IMPORTED_MODULE_3__pagination__);
//
//
//
//
//
//
//
//
//





/* harmony default export */ __webpack_exports__["default"] = {
  name: 'app',
  components: {
    StockHeader: __WEBPACK_IMPORTED_MODULE_0__stock_header___default.a,
    ProductsActions: __WEBPACK_IMPORTED_MODULE_1__products_actions___default.a,
    Products: __WEBPACK_IMPORTED_MODULE_2__products_table___default.a,
    Pagination: __WEBPACK_IMPORTED_MODULE_3__pagination___default.a
  }
};

/***/ }),
/* 31 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
Object.defineProperty(__webpack_exports__, "__esModule", { value: true });
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//

/* harmony default export */ __webpack_exports__["default"] = {
  props: ['product'],
  computed: {
    imagePath: function () {
      return `${data.baseUrl}/${this.product.image_thumbnail_path}`;
    }
  }
};

/***/ }),
/* 32 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
Object.defineProperty(__webpack_exports__, "__esModule", { value: true });
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0__movement_type__ = __webpack_require__(54);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0__movement_type___default = __webpack_require__.n(__WEBPACK_IMPORTED_MODULE_0__movement_type__);
//
//
//
//
//
//
//
//



/* harmony default export */ __webpack_exports__["default"] = {
  components: {
    MovementType: __WEBPACK_IMPORTED_MODULE_0__movement_type___default.a
  }
};

/***/ }),
/* 33 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
Object.defineProperty(__webpack_exports__, "__esModule", { value: true });
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0__product_line__ = __webpack_require__(56);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0__product_line___default = __webpack_require__.n(__WEBPACK_IMPORTED_MODULE_0__product_line__);
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//



/* harmony default export */ __webpack_exports__["default"] = {
  components: {
    ProductLine: __WEBPACK_IMPORTED_MODULE_0__product_line___default.a
  },
  computed: {
    products: function () {
      return this.$store.state.products;
    }
  }
};

/***/ }),
/* 34 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
Object.defineProperty(__webpack_exports__, "__esModule", { value: true });
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0__search_filter__ = __webpack_require__(59);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0__search_filter___default = __webpack_require__.n(__WEBPACK_IMPORTED_MODULE_0__search_filter__);
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//



/* harmony default export */ __webpack_exports__["default"] = {
  components: {
    SearchFilter: __WEBPACK_IMPORTED_MODULE_0__search_filter___default.a
  }
};

/***/ }),
/* 35 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
Object.defineProperty(__webpack_exports__, "__esModule", { value: true });
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0__breadcrumb__ = __webpack_require__(53);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0__breadcrumb___default = __webpack_require__.n(__WEBPACK_IMPORTED_MODULE_0__breadcrumb__);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1__tabs__ = __webpack_require__(62);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1__tabs___default = __webpack_require__.n(__WEBPACK_IMPORTED_MODULE_1__tabs__);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_2__search__ = __webpack_require__(60);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_2__search___default = __webpack_require__.n(__WEBPACK_IMPORTED_MODULE_2__search__);
//
//
//
//
//
//
//
//
//





/* harmony default export */ __webpack_exports__["default"] = {
  components: {
    Breadcrumb: __WEBPACK_IMPORTED_MODULE_0__breadcrumb___default.a,
    Tabs: __WEBPACK_IMPORTED_MODULE_1__tabs___default.a,
    Search: __WEBPACK_IMPORTED_MODULE_2__search___default.a
  }
};

/***/ }),
/* 36 */,
/* 37 */,
/* 38 */,
/* 39 */,
/* 40 */,
/* 41 */,
/* 42 */,
/* 43 */,
/* 44 */,
/* 45 */,
/* 46 */,
/* 47 */,
/* 48 */,
/* 49 */,
/* 50 */,
/* 51 */,
/* 52 */,
/* 53 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";


var Component = __webpack_require__(1)(
/* script */
null,
/* template */
__webpack_require__(106),
/* scopeId */
null,
/* cssModules */
null);
Component.options.__file = "/Users/nMartin/Documents/PrestaShop/admin-dev/themes/new-theme/js/stock-page/components/breadcrumb.vue";
if (Component.esModule && Object.keys(Component.esModule).some(function (key) {
  return key !== "default" && key !== "__esModule";
})) {
  console.error("named exports are not supported in *.vue files.");
}
if (Component.options.functional) {
  console.error("[vue-loader] breadcrumb.vue: functional components are not supported with templates, they should use render functions.");
}

/* hot reload */
if (false) {
  (function () {
    var hotAPI = require("vue-hot-reload-api");
    hotAPI.install(require("vue"), false);
    if (!hotAPI.compatible) return;
    module.hot.accept();
    if (!module.hot.data) {
      hotAPI.createRecord("data-v-af4f3930", Component.options);
    } else {
      hotAPI.reload("data-v-af4f3930", Component.options);
    }
  })();
}

module.exports = Component.exports;

/***/ }),
/* 54 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";


var Component = __webpack_require__(1)(
/* script */
null,
/* template */
__webpack_require__(105),
/* scopeId */
null,
/* cssModules */
null);
Component.options.__file = "/Users/nMartin/Documents/PrestaShop/admin-dev/themes/new-theme/js/stock-page/components/movement-type.vue";
if (Component.esModule && Object.keys(Component.esModule).some(function (key) {
  return key !== "default" && key !== "__esModule";
})) {
  console.error("named exports are not supported in *.vue files.");
}
if (Component.options.functional) {
  console.error("[vue-loader] movement-type.vue: functional components are not supported with templates, they should use render functions.");
}

/* hot reload */
if (false) {
  (function () {
    var hotAPI = require("vue-hot-reload-api");
    hotAPI.install(require("vue"), false);
    if (!hotAPI.compatible) return;
    module.hot.accept();
    if (!module.hot.data) {
      hotAPI.createRecord("data-v-9add1a7a", Component.options);
    } else {
      hotAPI.reload("data-v-9add1a7a", Component.options);
    }
  })();
}

module.exports = Component.exports;

/***/ }),
/* 55 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";


var Component = __webpack_require__(1)(
/* script */
null,
/* template */
__webpack_require__(107),
/* scopeId */
null,
/* cssModules */
null);
Component.options.__file = "/Users/nMartin/Documents/PrestaShop/admin-dev/themes/new-theme/js/stock-page/components/pagination.vue";
if (Component.esModule && Object.keys(Component.esModule).some(function (key) {
  return key !== "default" && key !== "__esModule";
})) {
  console.error("named exports are not supported in *.vue files.");
}
if (Component.options.functional) {
  console.error("[vue-loader] pagination.vue: functional components are not supported with templates, they should use render functions.");
}

/* hot reload */
if (false) {
  (function () {
    var hotAPI = require("vue-hot-reload-api");
    hotAPI.install(require("vue"), false);
    if (!hotAPI.compatible) return;
    module.hot.accept();
    if (!module.hot.data) {
      hotAPI.createRecord("data-v-c4a27102", Component.options);
    } else {
      hotAPI.reload("data-v-c4a27102", Component.options);
    }
  })();
}

module.exports = Component.exports;

/***/ }),
/* 56 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";


var Component = __webpack_require__(1)(
/* script */
__webpack_require__(31),
/* template */
__webpack_require__(99),
/* scopeId */
null,
/* cssModules */
null);
Component.options.__file = "/Users/nMartin/Documents/PrestaShop/admin-dev/themes/new-theme/js/stock-page/components/product-line.vue";
if (Component.esModule && Object.keys(Component.esModule).some(function (key) {
  return key !== "default" && key !== "__esModule";
})) {
  console.error("named exports are not supported in *.vue files.");
}
if (Component.options.functional) {
  console.error("[vue-loader] product-line.vue: functional components are not supported with templates, they should use render functions.");
}

/* hot reload */
if (false) {
  (function () {
    var hotAPI = require("vue-hot-reload-api");
    hotAPI.install(require("vue"), false);
    if (!hotAPI.compatible) return;
    module.hot.accept();
    if (!module.hot.data) {
      hotAPI.createRecord("data-v-16aaf6b7", Component.options);
    } else {
      hotAPI.reload("data-v-16aaf6b7", Component.options);
    }
  })();
}

module.exports = Component.exports;

/***/ }),
/* 57 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";


var Component = __webpack_require__(1)(
/* script */
__webpack_require__(32),
/* template */
__webpack_require__(108),
/* scopeId */
null,
/* cssModules */
null);
Component.options.__file = "/Users/nMartin/Documents/PrestaShop/admin-dev/themes/new-theme/js/stock-page/components/products-actions.vue";
if (Component.esModule && Object.keys(Component.esModule).some(function (key) {
  return key !== "default" && key !== "__esModule";
})) {
  console.error("named exports are not supported in *.vue files.");
}
if (Component.options.functional) {
  console.error("[vue-loader] products-actions.vue: functional components are not supported with templates, they should use render functions.");
}

/* hot reload */
if (false) {
  (function () {
    var hotAPI = require("vue-hot-reload-api");
    hotAPI.install(require("vue"), false);
    if (!hotAPI.compatible) return;
    module.hot.accept();
    if (!module.hot.data) {
      hotAPI.createRecord("data-v-d2b0a28e", Component.options);
    } else {
      hotAPI.reload("data-v-d2b0a28e", Component.options);
    }
  })();
}

module.exports = Component.exports;

/***/ }),
/* 58 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";


var Component = __webpack_require__(1)(
/* script */
__webpack_require__(33),
/* template */
__webpack_require__(109),
/* scopeId */
null,
/* cssModules */
null);
Component.options.__file = "/Users/nMartin/Documents/PrestaShop/admin-dev/themes/new-theme/js/stock-page/components/products-table.vue";
if (Component.esModule && Object.keys(Component.esModule).some(function (key) {
  return key !== "default" && key !== "__esModule";
})) {
  console.error("named exports are not supported in *.vue files.");
}
if (Component.options.functional) {
  console.error("[vue-loader] products-table.vue: functional components are not supported with templates, they should use render functions.");
}

/* hot reload */
if (false) {
  (function () {
    var hotAPI = require("vue-hot-reload-api");
    hotAPI.install(require("vue"), false);
    if (!hotAPI.compatible) return;
    module.hot.accept();
    if (!module.hot.data) {
      hotAPI.createRecord("data-v-da6c2bec", Component.options);
    } else {
      hotAPI.reload("data-v-da6c2bec", Component.options);
    }
  })();
}

module.exports = Component.exports;

/***/ }),
/* 59 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";


var Component = __webpack_require__(1)(
/* script */
null,
/* template */
__webpack_require__(101),
/* scopeId */
null,
/* cssModules */
null);
Component.options.__file = "/Users/nMartin/Documents/PrestaShop/admin-dev/themes/new-theme/js/stock-page/components/search-filter.vue";
if (Component.esModule && Object.keys(Component.esModule).some(function (key) {
  return key !== "default" && key !== "__esModule";
})) {
  console.error("named exports are not supported in *.vue files.");
}
if (Component.options.functional) {
  console.error("[vue-loader] search-filter.vue: functional components are not supported with templates, they should use render functions.");
}

/* hot reload */
if (false) {
  (function () {
    var hotAPI = require("vue-hot-reload-api");
    hotAPI.install(require("vue"), false);
    if (!hotAPI.compatible) return;
    module.hot.accept();
    if (!module.hot.data) {
      hotAPI.createRecord("data-v-3b1c1430", Component.options);
    } else {
      hotAPI.reload("data-v-3b1c1430", Component.options);
    }
  })();
}

module.exports = Component.exports;

/***/ }),
/* 60 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";


var Component = __webpack_require__(1)(
/* script */
__webpack_require__(34),
/* template */
__webpack_require__(102),
/* scopeId */
null,
/* cssModules */
null);
Component.options.__file = "/Users/nMartin/Documents/PrestaShop/admin-dev/themes/new-theme/js/stock-page/components/search.vue";
if (Component.esModule && Object.keys(Component.esModule).some(function (key) {
  return key !== "default" && key !== "__esModule";
})) {
  console.error("named exports are not supported in *.vue files.");
}
if (Component.options.functional) {
  console.error("[vue-loader] search.vue: functional components are not supported with templates, they should use render functions.");
}

/* hot reload */
if (false) {
  (function () {
    var hotAPI = require("vue-hot-reload-api");
    hotAPI.install(require("vue"), false);
    if (!hotAPI.compatible) return;
    module.hot.accept();
    if (!module.hot.data) {
      hotAPI.createRecord("data-v-4f796d0d", Component.options);
    } else {
      hotAPI.reload("data-v-4f796d0d", Component.options);
    }
  })();
}

module.exports = Component.exports;

/***/ }),
/* 61 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";


var Component = __webpack_require__(1)(
/* script */
__webpack_require__(35),
/* template */
__webpack_require__(100),
/* scopeId */
null,
/* cssModules */
null);
Component.options.__file = "/Users/nMartin/Documents/PrestaShop/admin-dev/themes/new-theme/js/stock-page/components/stock-header.vue";
if (Component.esModule && Object.keys(Component.esModule).some(function (key) {
  return key !== "default" && key !== "__esModule";
})) {
  console.error("named exports are not supported in *.vue files.");
}
if (Component.options.functional) {
  console.error("[vue-loader] stock-header.vue: functional components are not supported with templates, they should use render functions.");
}

/* hot reload */
if (false) {
  (function () {
    var hotAPI = require("vue-hot-reload-api");
    hotAPI.install(require("vue"), false);
    if (!hotAPI.compatible) return;
    module.hot.accept();
    if (!module.hot.data) {
      hotAPI.createRecord("data-v-391cacc9", Component.options);
    } else {
      hotAPI.reload("data-v-391cacc9", Component.options);
    }
  })();
}

module.exports = Component.exports;

/***/ }),
/* 62 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";


var Component = __webpack_require__(1)(
/* script */
null,
/* template */
__webpack_require__(103),
/* scopeId */
null,
/* cssModules */
null);
Component.options.__file = "/Users/nMartin/Documents/PrestaShop/admin-dev/themes/new-theme/js/stock-page/components/tabs.vue";
if (Component.esModule && Object.keys(Component.esModule).some(function (key) {
  return key !== "default" && key !== "__esModule";
})) {
  console.error("named exports are not supported in *.vue files.");
}
if (Component.options.functional) {
  console.error("[vue-loader] tabs.vue: functional components are not supported with templates, they should use render functions.");
}

/* hot reload */
if (false) {
  (function () {
    var hotAPI = require("vue-hot-reload-api");
    hotAPI.install(require("vue"), false);
    if (!hotAPI.compatible) return;
    module.hot.accept();
    if (!module.hot.data) {
      hotAPI.createRecord("data-v-543c2f23", Component.options);
    } else {
      hotAPI.reload("data-v-543c2f23", Component.options);
    }
  })();
}

module.exports = Component.exports;

/***/ }),
/* 63 */,
/* 64 */,
/* 65 */,
/* 66 */,
/* 67 */,
/* 68 */,
/* 69 */,
/* 70 */,
/* 71 */,
/* 72 */,
/* 73 */,
/* 74 */,
/* 75 */,
/* 76 */,
/* 77 */,
/* 78 */,
/* 79 */,
/* 80 */,
/* 81 */,
/* 82 */,
/* 83 */,
/* 84 */,
/* 85 */,
/* 86 */,
/* 87 */,
/* 88 */,
/* 89 */,
/* 90 */,
/* 91 */,
/* 92 */,
/* 93 */,
/* 94 */,
/* 95 */,
/* 96 */,
/* 97 */,
/* 98 */,
/* 99 */
/***/ (function(module, exports, __webpack_require__) {

module.exports={render:function (){var _vm=this;var _h=_vm.$createElement;var _c=_vm._self._c||_h;
  return _c('tr', [_c('td', [_c('input', {
    attrs: {
      "type": "checkbox"
    },
    domProps: {
      "value": _vm.product.product_id
    }
  }), _vm._v(" "), _c('img', {
    attrs: {
      "src": _vm.imagePath
    }
  }), _vm._v(" "), _c('span', [_vm._v(_vm._s(_vm.product.product_name))])]), _vm._v(" "), _c('td', [_vm._v("\n    " + _vm._s(_vm.product.product_reference) + "\n  ")]), _vm._v(" "), _c('td', [_vm._v("\n    " + _vm._s(_vm.product.supplier_name) + "\n  ")]), _vm._v(" "), _c('td', [_vm._v("\n    " + _vm._s(_vm.product.product_available_quantity + _vm.product.product_reserved_quantity) + "\n  ")]), _vm._v(" "), _c('td', [_vm._v("\n    " + _vm._s(_vm.product.product_reserved_quantity) + "\n  ")]), _vm._v(" "), _c('td', [_vm._v("\n    " + _vm._s(_vm.product.product_available_quantity) + "\n  ")]), _vm._v(" "), _vm._m(0)])
},staticRenderFns: [function (){var _vm=this;var _h=_vm.$createElement;var _c=_vm._self._c||_h;
  return _c('td', [_c('input')])
}]}
module.exports.render._withStripped = true
if (false) {
  module.hot.accept()
  if (module.hot.data) {
     require("vue-hot-reload-api").rerender("data-v-16aaf6b7", module.exports)
  }
}

/***/ }),
/* 100 */
/***/ (function(module, exports, __webpack_require__) {

module.exports={render:function (){var _vm=this;var _h=_vm.$createElement;var _c=_vm._self._c||_h;
  return _c('div', [_c('Breadcrumb'), _vm._v(" "), _c('h1', [_vm._v("Stock Management")]), _vm._v(" "), _c('Tabs'), _vm._v(" "), _c('Search')], 1)
},staticRenderFns: []}
module.exports.render._withStripped = true
if (false) {
  module.hot.accept()
  if (module.hot.data) {
     require("vue-hot-reload-api").rerender("data-v-391cacc9", module.exports)
  }
}

/***/ }),
/* 101 */
/***/ (function(module, exports, __webpack_require__) {

module.exports={render:function (){var _vm=this;var _h=_vm.$createElement;var _c=_vm._self._c||_h;
  return _vm._m(0)
},staticRenderFns: [function (){var _vm=this;var _h=_vm.$createElement;var _c=_vm._self._c||_h;
  return _c('select', {
    attrs: {
      "data-toggle": "select2"
    }
  }, [_c('option', {
    attrs: {
      "selected": ""
    }
  }, [_vm._v("Open this select menu")]), _vm._v(" "), _c('option', {
    attrs: {
      "value": "1"
    }
  }, [_vm._v("One")]), _vm._v(" "), _c('option', {
    attrs: {
      "value": "2"
    }
  }, [_vm._v("Two")]), _vm._v(" "), _c('option', {
    attrs: {
      "value": "3"
    }
  }, [_vm._v("Three")])])
}]}
module.exports.render._withStripped = true
if (false) {
  module.hot.accept()
  if (module.hot.data) {
     require("vue-hot-reload-api").rerender("data-v-3b1c1430", module.exports)
  }
}

/***/ }),
/* 102 */
/***/ (function(module, exports, __webpack_require__) {

module.exports={render:function (){var _vm=this;var _h=_vm.$createElement;var _c=_vm._self._c||_h;
  return _c('div', [_vm._m(0), _vm._v(" "), _c('SearchFilter')], 1)
},staticRenderFns: [function (){var _vm=this;var _h=_vm.$createElement;var _c=_vm._self._c||_h;
  return _c('div', {
    staticClass: "autocomplete"
  }, [_c('input', {
    staticClass: "form-control search",
    attrs: {
      "type": "text",
      "placeholder": "Search and add a related product",
      "autocomplete": "off"
    }
  }), _vm._v(" "), _c('ul', {
    staticClass: "product-list"
  }, [_c('li', {
    staticClass: "media"
  }, [_c('div', {
    staticClass: "media-left"
  }, [_c('img', {
    staticClass: "media-object image"
  })]), _vm._v(" "), _c('div', {
    staticClass: "media-body media-middle"
  }, [_c('span', {
    staticClass: "label"
  }, [_vm._v("Faded Short Sleeves T-shirt (ref: demo_1)")]), _vm._v(" "), _c('i', {
    staticClass: "material-icons delete"
  }, [_vm._v("clear")])])])])])
}]}
module.exports.render._withStripped = true
if (false) {
  module.hot.accept()
  if (module.hot.data) {
     require("vue-hot-reload-api").rerender("data-v-4f796d0d", module.exports)
  }
}

/***/ }),
/* 103 */
/***/ (function(module, exports, __webpack_require__) {

module.exports={render:function (){var _vm=this;var _h=_vm.$createElement;var _c=_vm._self._c||_h;
  return _vm._m(0)
},staticRenderFns: [function (){var _vm=this;var _h=_vm.$createElement;var _c=_vm._self._c||_h;
  return _c('ul', {
    staticClass: "nav nav-tabs",
    attrs: {
      "id": "tab",
      "role": "tablist"
    }
  }, [_c('li', {
    staticClass: "nav-item"
  }, [_c('a', {
    staticClass: "nav-link active",
    attrs: {
      "data-toggle": "tab",
      "href": "#stock",
      "role": "tab"
    }
  }, [_vm._v("Stock")])]), _vm._v(" "), _c('li', {
    staticClass: "nav-item"
  }, [_c('a', {
    staticClass: "nav-link",
    attrs: {
      "data-toggle": "tab",
      "href": "#movement",
      "role": "tab"
    }
  }, [_vm._v("Movements")])])])
}]}
module.exports.render._withStripped = true
if (false) {
  module.hot.accept()
  if (module.hot.data) {
     require("vue-hot-reload-api").rerender("data-v-543c2f23", module.exports)
  }
}

/***/ }),
/* 104 */
/***/ (function(module, exports, __webpack_require__) {

module.exports={render:function (){var _vm=this;var _h=_vm.$createElement;var _c=_vm._self._c||_h;
  return _c('div', {
    attrs: {
      "id": "app"
    }
  }, [_c('StockHeader'), _vm._v(" "), _c('ProductsActions'), _vm._v(" "), _c('Products'), _vm._v(" "), _c('Pagination')], 1)
},staticRenderFns: []}
module.exports.render._withStripped = true
if (false) {
  module.hot.accept()
  if (module.hot.data) {
     require("vue-hot-reload-api").rerender("data-v-616d468c", module.exports)
  }
}

/***/ }),
/* 105 */
/***/ (function(module, exports, __webpack_require__) {

module.exports={render:function (){var _vm=this;var _h=_vm.$createElement;var _c=_vm._self._c||_h;
  return _vm._m(0)
},staticRenderFns: [function (){var _vm=this;var _h=_vm.$createElement;var _c=_vm._self._c||_h;
  return _c('div', {
    staticClass: "pull-xs-right"
  }, [_c('select', {
    attrs: {
      "data-toggle": "select2"
    }
  }, [_c('option', {
    attrs: {
      "selected": ""
    }
  }, [_vm._v("Open this select menu")]), _vm._v(" "), _c('option', {
    attrs: {
      "value": "1"
    }
  }, [_vm._v("One")]), _vm._v(" "), _c('option', {
    attrs: {
      "value": "2"
    }
  }, [_vm._v("Two")]), _vm._v(" "), _c('option', {
    attrs: {
      "value": "3"
    }
  }, [_vm._v("Three")])]), _vm._v(" "), _c('button', {
    staticClass: "btn",
    attrs: {
      "type": "button",
      "disabled": ""
    }
  }, [_vm._v("APPLY NEW QUANTITY")])])
}]}
module.exports.render._withStripped = true
if (false) {
  module.hot.accept()
  if (module.hot.data) {
     require("vue-hot-reload-api").rerender("data-v-9add1a7a", module.exports)
  }
}

/***/ }),
/* 106 */
/***/ (function(module, exports, __webpack_require__) {

module.exports={render:function (){var _vm=this;var _h=_vm.$createElement;var _c=_vm._self._c||_h;
  return _c('small', [_vm._v("Catalog / Stock Management / Stock")])
},staticRenderFns: []}
module.exports.render._withStripped = true
if (false) {
  module.hot.accept()
  if (module.hot.data) {
     require("vue-hot-reload-api").rerender("data-v-af4f3930", module.exports)
  }
}

/***/ }),
/* 107 */
/***/ (function(module, exports, __webpack_require__) {

module.exports={render:function (){var _vm=this;var _h=_vm.$createElement;var _c=_vm._self._c||_h;
  return _vm._m(0)
},staticRenderFns: [function (){var _vm=this;var _h=_vm.$createElement;var _c=_vm._self._c||_h;
  return _c('nav', {
    staticClass: "pull-xs-right"
  }, [_c('ul', {
    staticClass: "multi pagination"
  }, [_c('li', {
    staticClass: "page-item"
  }, [_c('a', {
    staticClass: "pull-left arrow",
    attrs: {
      "href": "#",
      "aria-label": "Previous"
    }
  }, [_c('i', {
    staticClass: "material-icons"
  }, [_vm._v("keyboard_arrow_left")]), _vm._v(" "), _c('span', {
    staticClass: "sr-only"
  }, [_vm._v("Previous")])])]), _vm._v(" "), _c('li', {
    staticClass: "page-item active"
  }, [_c('a', {
    staticClass: "page-link",
    attrs: {
      "href": "#"
    }
  }, [_vm._v("1 "), _c('span', {
    staticClass: "sr-only"
  }, [_vm._v("(current)")])])]), _vm._v(" "), _c('li', {
    staticClass: "page-item"
  }, [_c('a', {
    staticClass: "page-link",
    attrs: {
      "href": "#"
    }
  }, [_vm._v("2")])]), _vm._v(" "), _c('li', {
    staticClass: "page-item"
  }, [_c('a', {
    staticClass: "page-link",
    attrs: {
      "href": "#"
    }
  }, [_vm._v("3")])]), _vm._v(" "), _c('li', {
    staticClass: "page-item"
  }, [_c('a', {
    staticClass: "page-link",
    attrs: {
      "href": "#"
    }
  }, [_vm._v("4")])]), _vm._v(" "), _c('li', {
    staticClass: "page-item"
  }, [_c('a', {
    staticClass: "pull-left arrow",
    attrs: {
      "href": "#",
      "aria-label": "Next"
    }
  }, [_c('i', {
    staticClass: "material-icons"
  }, [_vm._v("keyboard_arrow_right")]), _vm._v(" "), _c('span', {
    staticClass: "sr-only"
  }, [_vm._v("Next")])])])])])
}]}
module.exports.render._withStripped = true
if (false) {
  module.hot.accept()
  if (module.hot.data) {
     require("vue-hot-reload-api").rerender("data-v-c4a27102", module.exports)
  }
}

/***/ }),
/* 108 */
/***/ (function(module, exports, __webpack_require__) {

module.exports={render:function (){var _vm=this;var _h=_vm.$createElement;var _c=_vm._self._c||_h;
  return _c('div', [_c('input', {
    attrs: {
      "type": "checkbox"
    }
  }), _vm._v(" "), _c('button', {
    staticClass: "btn btn-tertiary-outline",
    attrs: {
      "type": "button"
    }
  }, [_vm._v("BULK EDIT")]), _vm._v(" "), _c('MovementType')], 1)
},staticRenderFns: []}
module.exports.render._withStripped = true
if (false) {
  module.hot.accept()
  if (module.hot.data) {
     require("vue-hot-reload-api").rerender("data-v-d2b0a28e", module.exports)
  }
}

/***/ }),
/* 109 */
/***/ (function(module, exports, __webpack_require__) {

module.exports={render:function (){var _vm=this;var _h=_vm.$createElement;var _c=_vm._self._c||_h;
  return _c('table', {
    staticClass: "table"
  }, [_vm._m(0), _vm._v(" "), _c('tbody', _vm._l((_vm.products), function(product) {
    return _c('ProductLine', {
      key: product.product_id,
      attrs: {
        "product": product
      }
    })
  }))])
},staticRenderFns: [function (){var _vm=this;var _h=_vm.$createElement;var _c=_vm._self._c||_h;
  return _c('thead', [_c('tr', [_c('th', [_vm._v("Product")]), _vm._v(" "), _c('th', [_vm._v("Reference")]), _vm._v(" "), _c('th', [_vm._v("Supplier")]), _vm._v(" "), _c('th', [_vm._v("Physical")]), _vm._v(" "), _c('th', [_vm._v("Reserved")]), _vm._v(" "), _c('th', [_vm._v("Available")]), _vm._v(" "), _c('th', [_vm._v("Edit Quantity")])])])
}]}
module.exports.render._withStripped = true
if (false) {
  module.hot.accept()
  if (module.hot.data) {
     require("vue-hot-reload-api").rerender("data-v-da6c2bec", module.exports)
  }
}

/***/ }),
/* 110 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
Object.defineProperty(__webpack_exports__, "__esModule", { value: true });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "Store", function() { return Store; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "mapState", function() { return mapState; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "mapMutations", function() { return mapMutations; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "mapGetters", function() { return mapGetters; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "mapActions", function() { return mapActions; });
/**
 * vuex v2.2.1
 * (c) 2017 Evan You
 * @license MIT
 */
var applyMixin = function (Vue) {
  var version = Number(Vue.version.split('.')[0]);

  if (version >= 2) {
    var usesInit = Vue.config._lifecycleHooks.indexOf('init') > -1;
    Vue.mixin(usesInit ? { init: vuexInit } : { beforeCreate: vuexInit });
  } else {
    // override init and inject vuex init procedure
    // for 1.x backwards compatibility.
    var _init = Vue.prototype._init;
    Vue.prototype._init = function (options) {
      if ( options === void 0 ) options = {};

      options.init = options.init
        ? [vuexInit].concat(options.init)
        : vuexInit;
      _init.call(this, options);
    };
  }

  /**
   * Vuex init hook, injected into each instances init hooks list.
   */

  function vuexInit () {
    var options = this.$options;
    // store injection
    if (options.store) {
      this.$store = options.store;
    } else if (options.parent && options.parent.$store) {
      this.$store = options.parent.$store;
    }
  }
};

var devtoolHook =
  typeof window !== 'undefined' &&
  window.__VUE_DEVTOOLS_GLOBAL_HOOK__;

function devtoolPlugin (store) {
  if (!devtoolHook) { return }

  store._devtoolHook = devtoolHook;

  devtoolHook.emit('vuex:init', store);

  devtoolHook.on('vuex:travel-to-state', function (targetState) {
    store.replaceState(targetState);
  });

  store.subscribe(function (mutation, state) {
    devtoolHook.emit('vuex:mutation', mutation, state);
  });
}

/**
 * Get the first item that pass the test
 * by second argument function
 *
 * @param {Array} list
 * @param {Function} f
 * @return {*}
 */
/**
 * Deep copy the given object considering circular structure.
 * This function caches all nested objects and its copies.
 * If it detects circular structure, use cached copy to avoid infinite loop.
 *
 * @param {*} obj
 * @param {Array<Object>} cache
 * @return {*}
 */


/**
 * forEach for object
 */
function forEachValue (obj, fn) {
  Object.keys(obj).forEach(function (key) { return fn(obj[key], key); });
}

function isObject (obj) {
  return obj !== null && typeof obj === 'object'
}

function isPromise (val) {
  return val && typeof val.then === 'function'
}

function assert (condition, msg) {
  if (!condition) { throw new Error(("[vuex] " + msg)) }
}

var Module = function Module (rawModule, runtime) {
  this.runtime = runtime;
  this._children = Object.create(null);
  this._rawModule = rawModule;
};

var prototypeAccessors$1 = { state: {},namespaced: {} };

prototypeAccessors$1.state.get = function () {
  return this._rawModule.state || {}
};

prototypeAccessors$1.namespaced.get = function () {
  return !!this._rawModule.namespaced
};

Module.prototype.addChild = function addChild (key, module) {
  this._children[key] = module;
};

Module.prototype.removeChild = function removeChild (key) {
  delete this._children[key];
};

Module.prototype.getChild = function getChild (key) {
  return this._children[key]
};

Module.prototype.update = function update (rawModule) {
  this._rawModule.namespaced = rawModule.namespaced;
  if (rawModule.actions) {
    this._rawModule.actions = rawModule.actions;
  }
  if (rawModule.mutations) {
    this._rawModule.mutations = rawModule.mutations;
  }
  if (rawModule.getters) {
    this._rawModule.getters = rawModule.getters;
  }
};

Module.prototype.forEachChild = function forEachChild (fn) {
  forEachValue(this._children, fn);
};

Module.prototype.forEachGetter = function forEachGetter (fn) {
  if (this._rawModule.getters) {
    forEachValue(this._rawModule.getters, fn);
  }
};

Module.prototype.forEachAction = function forEachAction (fn) {
  if (this._rawModule.actions) {
    forEachValue(this._rawModule.actions, fn);
  }
};

Module.prototype.forEachMutation = function forEachMutation (fn) {
  if (this._rawModule.mutations) {
    forEachValue(this._rawModule.mutations, fn);
  }
};

Object.defineProperties( Module.prototype, prototypeAccessors$1 );

var ModuleCollection = function ModuleCollection (rawRootModule) {
  var this$1 = this;

  // register root module (Vuex.Store options)
  this.root = new Module(rawRootModule, false);

  // register all nested modules
  if (rawRootModule.modules) {
    forEachValue(rawRootModule.modules, function (rawModule, key) {
      this$1.register([key], rawModule, false);
    });
  }
};

ModuleCollection.prototype.get = function get (path) {
  return path.reduce(function (module, key) {
    return module.getChild(key)
  }, this.root)
};

ModuleCollection.prototype.getNamespace = function getNamespace (path) {
  var module = this.root;
  return path.reduce(function (namespace, key) {
    module = module.getChild(key);
    return namespace + (module.namespaced ? key + '/' : '')
  }, '')
};

ModuleCollection.prototype.update = function update$1 (rawRootModule) {
  update(this.root, rawRootModule);
};

ModuleCollection.prototype.register = function register (path, rawModule, runtime) {
    var this$1 = this;
    if ( runtime === void 0 ) runtime = true;

  var parent = this.get(path.slice(0, -1));
  var newModule = new Module(rawModule, runtime);
  parent.addChild(path[path.length - 1], newModule);

  // register nested modules
  if (rawModule.modules) {
    forEachValue(rawModule.modules, function (rawChildModule, key) {
      this$1.register(path.concat(key), rawChildModule, runtime);
    });
  }
};

ModuleCollection.prototype.unregister = function unregister (path) {
  var parent = this.get(path.slice(0, -1));
  var key = path[path.length - 1];
  if (!parent.getChild(key).runtime) { return }

  parent.removeChild(key);
};

function update (targetModule, newModule) {
  // update target module
  targetModule.update(newModule);

  // update nested modules
  if (newModule.modules) {
    for (var key in newModule.modules) {
      if (!targetModule.getChild(key)) {
        console.warn(
          "[vuex] trying to add a new module '" + key + "' on hot reloading, " +
          'manual reload is needed'
        );
        return
      }
      update(targetModule.getChild(key), newModule.modules[key]);
    }
  }
}

var Vue; // bind on install

var Store = function Store (options) {
  var this$1 = this;
  if ( options === void 0 ) options = {};

  assert(Vue, "must call Vue.use(Vuex) before creating a store instance.");
  assert(typeof Promise !== 'undefined', "vuex requires a Promise polyfill in this browser.");

  var state = options.state; if ( state === void 0 ) state = {};
  var plugins = options.plugins; if ( plugins === void 0 ) plugins = [];
  var strict = options.strict; if ( strict === void 0 ) strict = false;

  // store internal state
  this._committing = false;
  this._actions = Object.create(null);
  this._mutations = Object.create(null);
  this._wrappedGetters = Object.create(null);
  this._modules = new ModuleCollection(options);
  this._modulesNamespaceMap = Object.create(null);
  this._subscribers = [];
  this._watcherVM = new Vue();

  // bind commit and dispatch to self
  var store = this;
  var ref = this;
  var dispatch = ref.dispatch;
  var commit = ref.commit;
  this.dispatch = function boundDispatch (type, payload) {
    return dispatch.call(store, type, payload)
  };
  this.commit = function boundCommit (type, payload, options) {
    return commit.call(store, type, payload, options)
  };

  // strict mode
  this.strict = strict;

  // init root module.
  // this also recursively registers all sub-modules
  // and collects all module getters inside this._wrappedGetters
  installModule(this, state, [], this._modules.root);

  // initialize the store vm, which is responsible for the reactivity
  // (also registers _wrappedGetters as computed properties)
  resetStoreVM(this, state);

  // apply plugins
  plugins.concat(devtoolPlugin).forEach(function (plugin) { return plugin(this$1); });
};

var prototypeAccessors = { state: {} };

prototypeAccessors.state.get = function () {
  return this._vm._data.$$state
};

prototypeAccessors.state.set = function (v) {
  assert(false, "Use store.replaceState() to explicit replace store state.");
};

Store.prototype.commit = function commit (_type, _payload, _options) {
    var this$1 = this;

  // check object-style commit
  var ref = unifyObjectStyle(_type, _payload, _options);
    var type = ref.type;
    var payload = ref.payload;
    var options = ref.options;

  var mutation = { type: type, payload: payload };
  var entry = this._mutations[type];
  if (!entry) {
    console.error(("[vuex] unknown mutation type: " + type));
    return
  }
  this._withCommit(function () {
    entry.forEach(function commitIterator (handler) {
      handler(payload);
    });
  });
  this._subscribers.forEach(function (sub) { return sub(mutation, this$1.state); });

  if (options && options.silent) {
    console.warn(
      "[vuex] mutation type: " + type + ". Silent option has been removed. " +
      'Use the filter functionality in the vue-devtools'
    );
  }
};

Store.prototype.dispatch = function dispatch (_type, _payload) {
  // check object-style dispatch
  var ref = unifyObjectStyle(_type, _payload);
    var type = ref.type;
    var payload = ref.payload;

  var entry = this._actions[type];
  if (!entry) {
    console.error(("[vuex] unknown action type: " + type));
    return
  }
  return entry.length > 1
    ? Promise.all(entry.map(function (handler) { return handler(payload); }))
    : entry[0](payload)
};

Store.prototype.subscribe = function subscribe (fn) {
  var subs = this._subscribers;
  if (subs.indexOf(fn) < 0) {
    subs.push(fn);
  }
  return function () {
    var i = subs.indexOf(fn);
    if (i > -1) {
      subs.splice(i, 1);
    }
  }
};

Store.prototype.watch = function watch (getter, cb, options) {
    var this$1 = this;

  assert(typeof getter === 'function', "store.watch only accepts a function.");
  return this._watcherVM.$watch(function () { return getter(this$1.state, this$1.getters); }, cb, options)
};

Store.prototype.replaceState = function replaceState (state) {
    var this$1 = this;

  this._withCommit(function () {
    this$1._vm._data.$$state = state;
  });
};

Store.prototype.registerModule = function registerModule (path, rawModule) {
  if (typeof path === 'string') { path = [path]; }
  assert(Array.isArray(path), "module path must be a string or an Array.");
  this._modules.register(path, rawModule);
  installModule(this, this.state, path, this._modules.get(path));
  // reset store to update getters...
  resetStoreVM(this, this.state);
};

Store.prototype.unregisterModule = function unregisterModule (path) {
    var this$1 = this;

  if (typeof path === 'string') { path = [path]; }
  assert(Array.isArray(path), "module path must be a string or an Array.");
  this._modules.unregister(path);
  this._withCommit(function () {
    var parentState = getNestedState(this$1.state, path.slice(0, -1));
    Vue.delete(parentState, path[path.length - 1]);
  });
  resetStore(this);
};

Store.prototype.hotUpdate = function hotUpdate (newOptions) {
  this._modules.update(newOptions);
  resetStore(this, true);
};

Store.prototype._withCommit = function _withCommit (fn) {
  var committing = this._committing;
  this._committing = true;
  fn();
  this._committing = committing;
};

Object.defineProperties( Store.prototype, prototypeAccessors );

function resetStore (store, hot) {
  store._actions = Object.create(null);
  store._mutations = Object.create(null);
  store._wrappedGetters = Object.create(null);
  store._modulesNamespaceMap = Object.create(null);
  var state = store.state;
  // init all modules
  installModule(store, state, [], store._modules.root, true);
  // reset vm
  resetStoreVM(store, state, hot);
}

function resetStoreVM (store, state, hot) {
  var oldVm = store._vm;

  // bind store public getters
  store.getters = {};
  var wrappedGetters = store._wrappedGetters;
  var computed = {};
  forEachValue(wrappedGetters, function (fn, key) {
    // use computed to leverage its lazy-caching mechanism
    computed[key] = function () { return fn(store); };
    Object.defineProperty(store.getters, key, {
      get: function () { return store._vm[key]; },
      enumerable: true // for local getters
    });
  });

  // use a Vue instance to store the state tree
  // suppress warnings just in case the user has added
  // some funky global mixins
  var silent = Vue.config.silent;
  Vue.config.silent = true;
  store._vm = new Vue({
    data: {
      $$state: state
    },
    computed: computed
  });
  Vue.config.silent = silent;

  // enable strict mode for new vm
  if (store.strict) {
    enableStrictMode(store);
  }

  if (oldVm) {
    if (hot) {
      // dispatch changes in all subscribed watchers
      // to force getter re-evaluation for hot reloading.
      store._withCommit(function () {
        oldVm._data.$$state = null;
      });
    }
    Vue.nextTick(function () { return oldVm.$destroy(); });
  }
}

function installModule (store, rootState, path, module, hot) {
  var isRoot = !path.length;
  var namespace = store._modules.getNamespace(path);

  // register in namespace map
  if (namespace) {
    store._modulesNamespaceMap[namespace] = module;
  }

  // set state
  if (!isRoot && !hot) {
    var parentState = getNestedState(rootState, path.slice(0, -1));
    var moduleName = path[path.length - 1];
    store._withCommit(function () {
      Vue.set(parentState, moduleName, module.state);
    });
  }

  var local = module.context = makeLocalContext(store, namespace, path);

  module.forEachMutation(function (mutation, key) {
    var namespacedType = namespace + key;
    registerMutation(store, namespacedType, mutation, local);
  });

  module.forEachAction(function (action, key) {
    var namespacedType = namespace + key;
    registerAction(store, namespacedType, action, local);
  });

  module.forEachGetter(function (getter, key) {
    var namespacedType = namespace + key;
    registerGetter(store, namespacedType, getter, local);
  });

  module.forEachChild(function (child, key) {
    installModule(store, rootState, path.concat(key), child, hot);
  });
}

/**
 * make localized dispatch, commit, getters and state
 * if there is no namespace, just use root ones
 */
function makeLocalContext (store, namespace, path) {
  var noNamespace = namespace === '';

  var local = {
    dispatch: noNamespace ? store.dispatch : function (_type, _payload, _options) {
      var args = unifyObjectStyle(_type, _payload, _options);
      var payload = args.payload;
      var options = args.options;
      var type = args.type;

      if (!options || !options.root) {
        type = namespace + type;
        if (!store._actions[type]) {
          console.error(("[vuex] unknown local action type: " + (args.type) + ", global type: " + type));
          return
        }
      }

      return store.dispatch(type, payload)
    },

    commit: noNamespace ? store.commit : function (_type, _payload, _options) {
      var args = unifyObjectStyle(_type, _payload, _options);
      var payload = args.payload;
      var options = args.options;
      var type = args.type;

      if (!options || !options.root) {
        type = namespace + type;
        if (!store._mutations[type]) {
          console.error(("[vuex] unknown local mutation type: " + (args.type) + ", global type: " + type));
          return
        }
      }

      store.commit(type, payload, options);
    }
  };

  // getters and state object must be gotten lazily
  // because they will be changed by vm update
  Object.defineProperties(local, {
    getters: {
      get: noNamespace
        ? function () { return store.getters; }
        : function () { return makeLocalGetters(store, namespace); }
    },
    state: {
      get: function () { return getNestedState(store.state, path); }
    }
  });

  return local
}

function makeLocalGetters (store, namespace) {
  var gettersProxy = {};

  var splitPos = namespace.length;
  Object.keys(store.getters).forEach(function (type) {
    // skip if the target getter is not match this namespace
    if (type.slice(0, splitPos) !== namespace) { return }

    // extract local getter type
    var localType = type.slice(splitPos);

    // Add a port to the getters proxy.
    // Define as getter property because
    // we do not want to evaluate the getters in this time.
    Object.defineProperty(gettersProxy, localType, {
      get: function () { return store.getters[type]; },
      enumerable: true
    });
  });

  return gettersProxy
}

function registerMutation (store, type, handler, local) {
  var entry = store._mutations[type] || (store._mutations[type] = []);
  entry.push(function wrappedMutationHandler (payload) {
    handler(local.state, payload);
  });
}

function registerAction (store, type, handler, local) {
  var entry = store._actions[type] || (store._actions[type] = []);
  entry.push(function wrappedActionHandler (payload, cb) {
    var res = handler({
      dispatch: local.dispatch,
      commit: local.commit,
      getters: local.getters,
      state: local.state,
      rootGetters: store.getters,
      rootState: store.state
    }, payload, cb);
    if (!isPromise(res)) {
      res = Promise.resolve(res);
    }
    if (store._devtoolHook) {
      return res.catch(function (err) {
        store._devtoolHook.emit('vuex:error', err);
        throw err
      })
    } else {
      return res
    }
  });
}

function registerGetter (store, type, rawGetter, local) {
  if (store._wrappedGetters[type]) {
    console.error(("[vuex] duplicate getter key: " + type));
    return
  }
  store._wrappedGetters[type] = function wrappedGetter (store) {
    return rawGetter(
      local.state, // local state
      local.getters, // local getters
      store.state, // root state
      store.getters // root getters
    )
  };
}

function enableStrictMode (store) {
  store._vm.$watch(function () { return this._data.$$state }, function () {
    assert(store._committing, "Do not mutate vuex store state outside mutation handlers.");
  }, { deep: true, sync: true });
}

function getNestedState (state, path) {
  return path.length
    ? path.reduce(function (state, key) { return state[key]; }, state)
    : state
}

function unifyObjectStyle (type, payload, options) {
  if (isObject(type) && type.type) {
    options = payload;
    payload = type;
    type = type.type;
  }

  assert(typeof type === 'string', ("Expects string as the type, but found " + (typeof type) + "."));

  return { type: type, payload: payload, options: options }
}

function install (_Vue) {
  if (Vue) {
    console.error(
      '[vuex] already installed. Vue.use(Vuex) should be called only once.'
    );
    return
  }
  Vue = _Vue;
  applyMixin(Vue);
}

// auto install in dist mode
if (typeof window !== 'undefined' && window.Vue) {
  install(window.Vue);
}

var mapState = normalizeNamespace(function (namespace, states) {
  var res = {};
  normalizeMap(states).forEach(function (ref) {
    var key = ref.key;
    var val = ref.val;

    res[key] = function mappedState () {
      var state = this.$store.state;
      var getters = this.$store.getters;
      if (namespace) {
        var module = getModuleByNamespace(this.$store, 'mapState', namespace);
        if (!module) {
          return
        }
        state = module.context.state;
        getters = module.context.getters;
      }
      return typeof val === 'function'
        ? val.call(this, state, getters)
        : state[val]
    };
    // mark vuex getter for devtools
    res[key].vuex = true;
  });
  return res
});

var mapMutations = normalizeNamespace(function (namespace, mutations) {
  var res = {};
  normalizeMap(mutations).forEach(function (ref) {
    var key = ref.key;
    var val = ref.val;

    val = namespace + val;
    res[key] = function mappedMutation () {
      var args = [], len = arguments.length;
      while ( len-- ) args[ len ] = arguments[ len ];

      if (namespace && !getModuleByNamespace(this.$store, 'mapMutations', namespace)) {
        return
      }
      return this.$store.commit.apply(this.$store, [val].concat(args))
    };
  });
  return res
});

var mapGetters = normalizeNamespace(function (namespace, getters) {
  var res = {};
  normalizeMap(getters).forEach(function (ref) {
    var key = ref.key;
    var val = ref.val;

    val = namespace + val;
    res[key] = function mappedGetter () {
      if (namespace && !getModuleByNamespace(this.$store, 'mapGetters', namespace)) {
        return
      }
      if (!(val in this.$store.getters)) {
        console.error(("[vuex] unknown getter: " + val));
        return
      }
      return this.$store.getters[val]
    };
    // mark vuex getter for devtools
    res[key].vuex = true;
  });
  return res
});

var mapActions = normalizeNamespace(function (namespace, actions) {
  var res = {};
  normalizeMap(actions).forEach(function (ref) {
    var key = ref.key;
    var val = ref.val;

    val = namespace + val;
    res[key] = function mappedAction () {
      var args = [], len = arguments.length;
      while ( len-- ) args[ len ] = arguments[ len ];

      if (namespace && !getModuleByNamespace(this.$store, 'mapActions', namespace)) {
        return
      }
      return this.$store.dispatch.apply(this.$store, [val].concat(args))
    };
  });
  return res
});

function normalizeMap (map) {
  return Array.isArray(map)
    ? map.map(function (key) { return ({ key: key, val: key }); })
    : Object.keys(map).map(function (key) { return ({ key: key, val: map[key] }); })
}

function normalizeNamespace (fn) {
  return function (namespace, map) {
    if (typeof namespace !== 'string') {
      map = namespace;
      namespace = '';
    } else if (namespace.charAt(namespace.length - 1) !== '/') {
      namespace += '/';
    }
    return fn(namespace, map)
  }
}

function getModuleByNamespace (store, helper, namespace) {
  var module = store._modulesNamespaceMap[namespace];
  if (!module) {
    console.error(("[vuex] module namespace not found in " + helper + "(): " + namespace));
  }
  return module
}

var index_esm = {
  Store: Store,
  install: install,
  version: '2.2.1',
  mapState: mapState,
  mapMutations: mapMutations,
  mapGetters: mapGetters,
  mapActions: mapActions
};

/* harmony default export */ __webpack_exports__["default"] = index_esm;


/***/ }),
/* 111 */
/***/ (function(module, exports) {

/* (ignored) */

/***/ }),
/* 112 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";


var _vue = __webpack_require__(7);

var _vue2 = _interopRequireDefault(_vue);

var _vueResource = __webpack_require__(29);

var _vueResource2 = _interopRequireDefault(_vueResource);

var _app = __webpack_require__(18);

var _app2 = _interopRequireDefault(_app);

var _store = __webpack_require__(16);

var _store2 = _interopRequireDefault(_store);

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

_vue2.default.use(_vueResource2.default);

var stockApp = new _vue2.default({
  store: _store2.default,
  el: '#stock-app',
  template: '<app/>',
  components: { app: _app2.default },
  methods: {
    getStock: function getStock() {
      this.$http.get(data.apiUrl).then(function (response) {
        if (response.status == 200) {
          this.$store.commit('addProducts', response.body);
        }
      }, function (error) {
        console.log(error.statusText);
      });
    }
  },
  mounted: function mounted() {
    this.getStock();
  }
});

/***/ })
/******/ ]);
>>>>>>> 0e4097cdff... BO: Split stock-app in components
