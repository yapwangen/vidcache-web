/* Extension for parsing XML on IE browsers */
Request.XML = new Class({
	Extends: Request,

	success: function(text, xml) {
		if (xml) {
			if(Browser.Features.xpath) {
				xml = xml.documentElement;
			} else {
				xml = this.createXML(xml.documentElement); // ie browsers
			}
		}
		//window.xml = xml;
		this.onSuccess(xml, text);
	},

	createXML : function(xml, parent, level) {
		if(!parent) {
			parent = new Element(xml.nodeName);
			level  = 'root';
		}
		level += '>>'+xml.nodeName;
		//console.log(level);
		if(xml.childNodes.length) {
			for(var i = 0; i < xml.childNodes.length; i++) {
				var son = xml.childNodes[i];
				if(son.nodeType == 1) { // Element type
					// NodeName
					var el = new Element(son.nodeName.replace(':', ''));
					// Attributes
					if(son.attributes.length) {
						for(var j = 0; j < son.attributes.length; j++) {
							var property = son.attributes[j].nodeName;
							var value    = son.attributes[j].nodeValue;
							el.setProperty(property, value);
						}
					}
					// Value
					if (son.firstChild && son.firstChild.nodeType==3) {
						if (son.firstChild.nodeValue) {
							el.set({
								'text' : son.firstChild.nodeValue
								});
						}
					}
					if (son.nodeName == 'fileentryid') console.log('docId:'+el.get('text'));
					parent.grab(el);
					this.createXML(son, el, level);
				}
			}
		}
		return parent;
	}
});