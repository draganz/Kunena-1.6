Element.implement({getSelectedText:function(){if(Browser.Engine.trident)return document.selection.createRange().text;return this.get('value').substring(this.selectionStart,this.selectionEnd);},replaceSelectedText:function(newtext,isLast){var isLast=(isLast==null)?true:isLast;var scroll_top=this.scrollTop;if(Browser.Engine.trident){this.focus();var range=document.selection.createRange();range.text=newtext;if(isLast){range.select();this.scrollTop=scroll_top;}}
else{originalStart=this.selectionStart;originalEnd=this.selectionEnd;this.value=this.get('value').substring(0,originalStart)+newtext+this.get('value').substring(originalEnd);if(isLast==false){this.setSelectionRange(originalStart,originalStart+newtext.length);}
else{this.setSelectionRange(originalStart+newtext.length,originalStart+newtext.length);this.scrollTop=scroll_top;}
this.focus();}}});var nawte=new Class({Implements:Options,options:{displatchChangeEvent:false,changeEventDelay:200,interceptTabs:true},initialize:function(element,list,options){this.el=$(element);this.setOptions(options);if(this.options.dispatchChangeEvent){this.el.addEvents({'focus':function(){this.timer=this.watchChange.periodical(this.options.changeEventDelay,this);}.bind(this),'blur':function(){this.timer=$clear(this.timer);}.bind(this)});}
if(this.options.interceptTabs){this.el.addEvent('keypress',function(event){var event=new Event(event);if(event.key=="tab"){event.preventDefault();this.replaceSelection("\t");}}.bind(this));}
if(!$defined(list)||list==""){list=new Element('li');list.inject(this.el,'before');this.list=list;}
else{this.list=$(list);}
this.oldContent=this.el.get('value');},watchChange:function(){if(this.oldContent!=this.el.get('value')){this.oldContent=this.el.get('value');this.el.fireEvent('change');}},getSelection:function(){return this.el.getSelectedText();},wrapSelection:function(wrapper,isLast){var isLast=(isLast==null)?true:isLast;this.el.replaceSelectedText(wrapper+this.el.getSelectedText()+wrapper,isLast);},insert:function(insertText,where,isLast){var isLast=(isLast==null)?true:isLast;where=(where=="")?'after':where;var newText=(where=="before")?insertText+this.el.getSelectedText():this.el.getSelectedText()+insertText;this.el.replaceSelectedText(newText,isLast);},replaceSelection:function(newText,isLast){var isLast=(isLast==null)?true:isLast;this.el.replaceSelectedText(newText,isLast);},processEachLine:function(callback,isLast){var isLast=(isLast==null)?true:isLast;var lines=this.el.getSelectedText().split("\n");var newlines=[];lines.each(function(line){if(line!="")
newlines.push(callback.attempt(line,this));else
newlines.push("");}.bind(this));this.el.replaceSelectedText(newlines.join("\n"),isLast);},getValue:function(){return this.el.get('value');},setValue:function(text){this.el.set('value',text);this.el.focus();},addFunction:function(name,callback,args){var item=new Element('li');var itemlink=new Element('a',{'events':{'click':function(e){new Event(e).stop();callback.attempt(null,this);}.bind(this)},'href':'#'});itemlink.set('html','<span>'+name+'</span>');itemlink.setProperties(args||{});itemlink.inject(item,'bottom');item.injectInside(this.list);}});