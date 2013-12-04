webshims.register("track",function(a,b,c,d){"use strict";var e=b.mediaelement,f=((new Date).getTime(),a.fn.addBack?"addBack":"andSelf",{subtitles:1,captions:1,descriptions:1}),g=a("<track />"),h=Modernizr.ES5&&Modernizr.objectAccessor,i=function(a){var c={};return a.addEventListener=function(a,d){c[a]&&b.error("always use $.on to the shimed event: "+a+" already bound fn was: "+c[a]+" your fn was: "+d),c[a]=d},a.removeEventListener=function(a,d){c[a]&&c[a]!=d&&b.error("always use $.on/$.off to the shimed event: "+a+" already bound fn was: "+c[a]+" your fn was: "+d),c[a]&&delete c[a]},a},j={getCueById:function(a){for(var b=null,c=0,d=this.length;d>c;c++)if(this[c].id===a){b=this[c];break}return b}},k={0:"disabled",1:"hidden",2:"showing"},l={shimActiveCues:null,_shimActiveCues:null,activeCues:null,cues:null,kind:"subtitles",label:"",language:"",id:"",mode:"disabled",oncuechange:null,toString:function(){return"[object TextTrack]"},addCue:function(a){if(this.cues){var c=this.cues[this.cues.length-1];c&&c.startTime>a.startTime&&b.error("cue startTime higher than previous cue's startTime")}else this.cues=e.createCueList();a.track&&a.track.removeCue&&a.track.removeCue(a),a.track=this,this.cues.push(a)},removeCue:function(a){var c=this.cues||[],d=0,e=c.length;if(a.track!=this)return b.error("cue not part of track"),void 0;for(;e>d;d++)if(c[d]===a){c.splice(d,1),a.track=null;break}return a.track?(b.error("cue not part of track"),void 0):void 0}},m=["kind","label","srclang"],n={srclang:"language"},o=Function.prototype.call.bind(Object.prototype.hasOwnProperty),p=function(c,d){var e,f,g=[],h=[],i=[];if(c||(c=b.data(this,"mediaelementBase")||b.data(this,"mediaelementBase",{})),d||(c.blockTrackListUpdate=!0,d=a.prop(this,"textTracks"),c.blockTrackListUpdate=!1),clearTimeout(c.updateTrackListTimer),a("track",this).each(function(){var b=a.prop(this,"track");i.push(b),-1==d.indexOf(b)&&h.push(b)}),c.scriptedTextTracks)for(e=0,f=c.scriptedTextTracks.length;f>e;e++)i.push(c.scriptedTextTracks[e]),-1==d.indexOf(c.scriptedTextTracks[e])&&h.push(c.scriptedTextTracks[e]);for(e=0,f=d.length;f>e;e++)-1==i.indexOf(d[e])&&g.push(d[e]);if(g.length||h.length){for(d.splice(0),e=0,f=i.length;f>e;e++)d.push(i[e]);for(e=0,f=g.length;f>e;e++)a([d]).triggerHandler(a.Event({type:"removetrack",track:g[e]}));for(e=0,f=h.length;f>e;e++)a([d]).triggerHandler(a.Event({type:"addtrack",track:h[e]}));(c.scriptedTextTracks||g.length)&&a(this).triggerHandler("updatetrackdisplay")}},q=function(c,d){d||(d=b.data(c,"trackData")),d&&!d.isTriggering&&(d.isTriggering=!0,setTimeout(function(){a(c).closest("audio, video").triggerHandler("updatetrackdisplay"),d.isTriggering=!1},1))},r=function(){var c={subtitles:{subtitles:1,captions:1},descriptions:{descriptions:1},chapters:{chapters:1}};return c.captions=c.subtitles,function(d){var e,f,g=a.prop(d,"default");return g&&"metadata"!=(e=a.prop(d,"kind"))&&(f=a(d).parent().find("track[default]").filter(function(){return!!c[e][a.prop(this,"kind")]})[0],f!=d&&(g=!1,b.error("more than one default track of a specific kind detected. Fall back to default = false"))),g}}(),s=a("<div />")[0],t=function(a,c,d){3!=arguments.length&&b.error("wrong arguments.length for TextTrackCue.constructor"),this.startTime=a,this.endTime=c,this.text=d,i(this)};t.prototype={onenter:null,onexit:null,pauseOnExit:!1,getCueAsHTML:function(){var a,b="",c="",f=d.createDocumentFragment();return o(this,"getCueAsHTML")||(a=this.getCueAsHTML=function(){var a,d;if(b!=this.text)for(b=this.text,c=e.parseCueTextToHTML(b),s.innerHTML=c,a=0,d=s.childNodes.length;d>a;a++)f.appendChild(s.childNodes[a].cloneNode(!0));return f.cloneNode(!0)}),a?a.apply(this,arguments):f.cloneNode(!0)},track:null,id:""},c.TextTrackCue=t,e.createCueList=function(){return a.extend([],j)},e.parseCueTextToHTML=function(){var a=/(<\/?[^>]+>)/gi,b=/^(?:c|v|ruby|rt|b|i|u)/,c=/\<\s*\//,d=function(a,b,d,e){var f;return c.test(e)?f="</"+a+">":(d.splice(0,1),f="<"+a+" "+b+'="'+d.join(" ").replace(/\"/g,"&#34;")+'">'),f},e=function(a){var c=a.replace(/[<\/>]+/gi,"").split(/[\s\.]+/);return c[0]&&(c[0]=c[0].toLowerCase(),b.test(c[0])?"c"==c[0]?a=d("span","class",c,a):"v"==c[0]&&(a=d("q","title",c,a)):a=""),a};return function(b){return b.replace(a,e)}}(),e.loadTextTrack=function(c,d,g,h){var i="play playing updatetrackdisplay",j=g.track,k=function(){var f,h,l,m;if("disabled"!=j.mode&&a.attr(d,"src")&&(l=a.prop(d,"src"))&&(a(c).off(i,k),!g.readyState)){f=function(){g.readyState=3,j.cues=null,j.activeCues=j.shimActiveCues=j._shimActiveCues=null,a(d).triggerHandler("error")},g.readyState=1;try{j.cues=e.createCueList(),j.activeCues=j.shimActiveCues=j._shimActiveCues=e.createCueList(),m=function(){h=a.ajax({dataType:"text",url:l,success:function(i){"text/vtt"!=h.getResponseHeader("content-type")&&b.error("set the mime-type of your WebVTT files to text/vtt. see: http://dev.w3.org/html5/webvtt/#text/vtt"),e.parseCaptions(i,j,function(b){b&&"length"in b?(g.readyState=2,a(d).triggerHandler("load"),a(c).triggerHandler("updatetrackdisplay")):f()})},error:f})},a.ajax?m():(b.ready("$ajax",m),b.loader.loadList(["$ajax"]))}catch(n){f(),b.error(n)}}};g.readyState=0,j.shimActiveCues=null,j._shimActiveCues=null,j.activeCues=null,j.cues=null,a(c).off(i,k),a(c).on(i,k),h&&(j.mode=f[j.kind]?"showing":"hidden",k())},e.createTextTrack=function(c,d){var f,g;return d.nodeName&&(g=b.data(d,"trackData"),g&&(q(d,g),f=g.track)),f||(f=i(b.objectCreate(l)),h||m.forEach(function(b){var c=a.prop(d,b);c&&(f[n[b]||b]=c)}),d.nodeName?(h&&m.forEach(function(c){b.defineProperty(f,n[c]||c,{get:function(){return a.prop(d,c)}})}),f.id=a(d).prop("id"),g=b.data(d,"trackData",{track:f}),e.loadTextTrack(c,d,g,r(d))):(h&&m.forEach(function(a){b.defineProperty(f,n[a]||a,{value:d[a],writeable:!1})}),f.cues=e.createCueList(),f.activeCues=f._shimActiveCues=f.shimActiveCues=e.createCueList(),f.mode="hidden",f.readyState=2),"subtitles"!=f.kind||f.language||b.error("you must provide a language for track in subtitles state"),f.__wsmode=f.mode),f},e.parseCaptionChunk=function(){var a=/^(\d{2})?:?(\d{2}):(\d{2})\.(\d+)\s+\-\-\>\s+(\d{2})?:?(\d{2}):(\d{2})\.(\d+)\s*(.*)/,c=/^(DEFAULTS|DEFAULT)\s+\-\-\>\s+(.*)/g,d=/^(STYLE|STYLES)\s+\-\-\>\s*\n([\s\S]*)/g,e=/^(COMMENT|COMMENTS)\s+\-\-\>\s+(.*)/g;return function(f){var g,h,i,j,k,l,m,n,o,p;if(n=c.exec(f))return null;if(n=d.exec(f))return null;if(n=e.exec(f))return null;for(g=f.split(/\n/g);!g[0].replace(/\s+/gi,"").length&&g.length>0;)g.shift();for(g[0].match(/^\s*[a-z0-9-\_]+\s*$/gi)&&(m=String(g.shift().replace(/\s*/gi,""))),l=0;l<g.length;l++){var q=g[l];(o=a.exec(q))&&(k=o.slice(1),h=parseInt(60*(k[0]||0)*60,10)+parseInt(60*(k[1]||0),10)+parseInt(k[2]||0,10)+parseFloat("0."+(k[3]||0)),i=parseInt(60*(k[4]||0)*60,10)+parseInt(60*(k[5]||0),10)+parseInt(k[6]||0,10)+parseFloat("0."+(k[7]||0))),g=g.slice(0,l).concat(g.slice(l+1));break}return h||i?(j=g.join("\n"),p=new t(h,i,j),m&&(p.id=m),p):(b.warn("couldn't extract time information: "+[h,i,g.join("\n"),m].join(" ; ")),null)}}(),e.parseCaptions=function(a,c,d){{var f,g,h,i,j;e.createCueList()}a?(h=/^WEBVTT(\s*FILE)?/gi,g=function(k,l){for(;l>k;k++){if(f=a[k],h.test(f))j=!0;else if(f.replace(/\s*/gi,"").length){if(!j){b.error("please use WebVTT format. This is the standard"),d(null);break}f=e.parseCaptionChunk(f,k),f&&c.addCue(f)}if(i<(new Date).getTime()-30){k++,setTimeout(function(){i=(new Date).getTime(),g(k,l)},90);break}}k>=l&&(j||b.error("please use WebVTT format. This is the standard"),d(c.cues))},a=a.replace(/\r\n/g,"\n"),setTimeout(function(){a=a.replace(/\r/g,"\n"),setTimeout(function(){i=(new Date).getTime(),a=a.split(/\n\n+/g),g(0,a.length)},9)},9)):b.error("Required parameter captionData not supplied.")},e.createTrackList=function(c,d){return d=d||b.data(c,"mediaelementBase")||b.data(c,"mediaelementBase",{}),d.textTracks||(d.textTracks=[],b.defineProperties(d.textTracks,{onaddtrack:{value:null},onremovetrack:{value:null},onchange:{value:null},getTrackById:{value:function(a){for(var b=null,c=0;c<d.textTracks.length;c++)if(a==d.textTracks[c].id){b=d.textTracks[c];break}return b}}}),i(d.textTracks),a(c).on("updatetrackdisplay",function(){for(var b,c=0;c<d.textTracks.length;c++)b=d.textTracks[c],b.__wsmode!=b.mode&&(b.__wsmode=b.mode,a([d.textTracks]).triggerHandler("change"))})),d.textTracks},Modernizr.track||(b.defineNodeNamesBooleanProperty(["track"],"default"),b.reflectProperties(["track"],["srclang","label"]),b.defineNodeNameProperties("track",{src:{reflect:!0,propType:"src"}})),b.defineNodeNameProperties("track",{kind:{attr:Modernizr.track?{set:function(a){var c=b.data(this,"trackData");this.setAttribute("data-kind",a),c&&(c.attrKind=a)},get:function(){var a=b.data(this,"trackData");return a&&"attrKind"in a?a.attrKind:this.getAttribute("kind")}}:{},reflect:!0,propType:"enumarated",defaultValue:"subtitles",limitedTo:["subtitles","captions","descriptions","chapters","metadata"]}}),a.each(m,function(c,d){var e=n[d]||d;b.onNodeNamesPropertyModify("track",d,function(){var c=b.data(this,"trackData");c&&("kind"==d&&q(this,c),h||(c.track[e]=a.prop(this,d)))})}),b.onNodeNamesPropertyModify("track","src",function(c){if(c){var d,f=b.data(this,"trackData");f&&(d=a(this).closest("video, audio"),d[0]&&e.loadTextTrack(d,this,f))}}),b.defineNodeNamesProperties(["track"],{ERROR:{value:3},LOADED:{value:2},LOADING:{value:1},NONE:{value:0},readyState:{get:function(){return(b.data(this,"trackData")||{readyState:0}).readyState},writeable:!1},track:{get:function(){return e.createTextTrack(a(this).closest("audio, video")[0],this)},writeable:!1}},"prop"),b.defineNodeNamesProperties(["audio","video"],{textTracks:{get:function(){var a=this,c=b.data(a,"mediaelementBase")||b.data(a,"mediaelementBase",{}),d=e.createTrackList(a,c);return c.blockTrackListUpdate||p.call(a,c,d),d},writeable:!1},addTextTrack:{value:function(a,c,d){var f=e.createTextTrack(this,{kind:g.prop("kind",a||"").prop("kind"),label:c||"",srclang:d||""}),h=b.data(this,"mediaelementBase")||b.data(this,"mediaelementBase",{});return h.scriptedTextTracks||(h.scriptedTextTracks=[]),h.scriptedTextTracks.push(f),p.call(this),f}}},"prop"),a(d).on("emptied ended updatetracklist",function(c){if(a(c.target).is("audio, video")){var d=b.data(c.target,"mediaelementBase");d&&(clearTimeout(d.updateTrackListTimer),d.updateTrackListTimer=setTimeout(function(){p.call(c.target,d)},0))}});var u=function(a,b){return b.readyState||a.readyState},v=function(a){a.originalEvent&&a.stopImmediatePropagation()},w=function(){if(b.implement(this,"track")){var c,d,e=a.prop(this,"track"),f=this.track;f&&(c=a.prop(this,"kind"),d=u(this,f),(f.mode||d)&&(e.mode=k[f.mode]||f.mode),"descriptions"!=c&&(f.mode="string"==typeof f.mode?"disabled":0,this.kind="metadata",a(this).attr({kind:c}))),a(this).on("load error",v)}};b.addReady(function(c,d){var e=d.filter("video, audio, track").closest("audio, video");a("video, audio",c).add(e).each(function(){p.call(this)}).each(function(){if(Modernizr.track){var c=a.prop(this,"textTracks"),d=this.textTracks;c.length!=d.length&&b.error("textTracks couldn't be copied"),a("track",this).each(w)}}),e.each(function(){var a=this,c=b.data(a,"mediaelementBase");c&&(clearTimeout(c.updateTrackListTimer),c.updateTrackListTimer=setTimeout(function(){p.call(a,c)},9))})}),Modernizr.texttrackapi&&a("video, audio").trigger("trackapichange")});