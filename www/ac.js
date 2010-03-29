/* vim:ts=4:sts=4:sw=2:noai:noexpandtab
 *
 * Auto-complete client side javascript.
 * Copyright (c) 2005 Steven McCoy <fnjordy@gmail.com>
 *
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 2.1 of the License, or (at your option) any later version.
 * 
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 * Lesser General Public License for more details.
 * 
 * You should have received a copy of the GNU Lesser General Public
 * License along with this library; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 */

/* format of constructor is overloaded:
 * AC(<type>, <id>, <submit callback>)
 * AC(<type>, <id>)
 * AC(<id>)
 */
function AC(id) {
/* find search type */
	if (arguments.length > 1) {
		this.type = arguments[0];
		id = arguments[1];
	} else {
		this.type = id;
	}

/* input element we are autocompleting on */
	this.obj = document.getElementById(id);
	this.obj.value = '';

/* base url to send request too */
	this.url = '/ac.php';

/* function to call when option selected */
	this.submit_callback = (arguments.length > 2) ? arguments[2] : null;

/* popup layer we will display results in */
	this.div = document.createElement('DIV');
	this.div.className = 'ac_menu';
	this.div.style.visibility = 'hidden';
	this.div.style.position = 'absolute';
	this.div.style.zIndex = 1;
	this.div.style.width = this.obj.offsetWidth - 2 + "px";

	this.div.style.left = this.total_offset(this.obj,'offsetLeft') + "px";
	this.div.style.top = this.total_offset(this.obj,'offsetTop') + this.obj.offsetHeight - 1 + "px";

/* tie to input element */
	this.obj.parentNode.insertBefore(this.div, this.obj.nextSibling);

/* iframe for non-XmlHttpRequest() browsers */
	this.iframe = null;

/* install event handlers */
	this.obj.onkeydown = this.onkeydown;
	this.obj.onkeyup = this.onkeyup;
	this.obj.onkeypress = this.onkeypress;
	this.obj.onblur = function() { this.AC.close_popup(); }

	this.obj.AC = this;		/* self reference */
	this.selected_option = null;	/* the currently selected option */

	this.request = null;		/* http request object */
	this.cache = new Array();	/* cache of results from server */
	this.typing = false;		/* whether user is still typing */
	this.typing_timeout = 10;
	this.sending_timeout = 10;

	this.search_term = null;	/* current search term  */
	this.previous_term = null;	/* previous search term */
	this.searched_term = null;	/* search from keyboard */

	this.last_input = null;		/* previous typed entry */

/* Unicode inputs require polling of the input control for updates */
	this.poll_input = false;

/* update extern mapping array for rpc reply */
	_ac_map_add(this);
}

AC.prototype.enable_unicode = function() {
	this.poll_input = true;
	_ac_key_check(this,this.typing_timeout);
}

AC.prototype.total_offset = function(element, property) {
	var total = 0;
	while (element) {
		total += element[property];
		element = element.offsetParent;
	}
	return total;
}

/* hide popup and cleanup */
AC.prototype.close_popup = function() { 
	this.div.style.visibility = 'hidden'; 

/* no selected item, no typing, and close any pending request */
	this.selected_option = null;
	this.typing = false;
	this.search_term = null;
	this.previous_term = null;
}

/* create object for rpc call */
AC.prototype.XMLHttpRequest = function() {
	var request = null;
	if (typeof XMLHttpRequest != 'undefined') {
		request = new XMLHttpRequest();
	} else {
		try {
			request = new ActiveXObject('Msxml2.XMLHTTP')
		} catch(e) {
			try { 
				request = new ActiveXObject('Microsoft.XMLHTTP')
			} catch(e) { 
				request = null 
			}
		}
	}
	return request;
}

/* helper functions to process typing timer */
var _ac_key_thunk = new Array();
function _ac_key_thunk_call(id) {
	if (_ac_key_thunk[id]) {
		var ac = _ac_key_thunk[id][1];

/* now check as if onkeyup() was called */
/* first find unselected text */
		var unselected = ac.obj.value;
		if (document.selection) {
			var range = document.selection.createRange();
			if (range) {
/* to limit the execution this would be nice, but parentElement() not supported in Opera */
//			if (range && range.parentElement && range.parentElement() == ac.obj) {
				var length = unselected.length - range.text.length;
				if (length > 0) {
					unselected = unselected.substring(0, length);
				}
			}
		} else if (ac.obj.setSelectionRange) {
			var length = ac.obj.selectionEnd - ac.obj.selectionStart;
			if (length > 0)
				unselected = unselected.substring(0,ac.obj.selectionStart);
		}

		if (unselected != ac.last_input) {
			if (unselected.length > 0) {
				ac.searched_term = unselected;
				ac.suggest(ac.searched_term);
			} else {
				_ac_cancel(ac);
				ac.close_popup();
			}
			ac.last_input = unselected;
		}

/* re-install timer for polling */
		if (ac.poll_input) {
			_ac_key_thunk[id][2] = setTimeout("_ac_key_thunk_call("+id+")",ac.typing_timeout);
		} else {
/* remove from list and cleanup list */
			_ac_key_thunk[id] = null;
			for (i = _ac_key_thunk.length; i > 0; i--)
				if (_ac_key_thunk[i] == null)
					_ac_key_thunk.length--;
		}
	}
}

function _ac_key_check(ac,timeout) {
/* first remove any pending key check */
	for (i = _ac_key_thunk.length-1; i >= 0; i--)
		if (_ac_key_thunk[i] != null && _ac_key_thunk[i][0] == ac.obj.id) {
			clearTimeout(_ac_key_thunk[i][2]);
			_ac_key_thunk[i] = null;
		}

/* now setup a new one */
	var i = _ac_key_thunk.length;
	var handle = setTimeout("_ac_key_thunk_call("+i+")",timeout);
	_ac_key_thunk[i] = new Array(ac.obj.id,ac,handle);
}

/* helper functions to process sending timer */
var _ac_thunk = new Array();
function _ac_thunk_call(id) {
	if (_ac_thunk[id]) {
		var ac = _ac_thunk[id][1];
		ac.typing = false;
		ac.send(_ac_thunk[id][2]);
		_ac_thunk[id] = null;
		for (i = _ac_thunk.length; i > 0; i--)
			if (_ac_thunk[i] == null)
				_ac_thunk.length--;
	}
}

/* cancel a pending request */
function _ac_cancel(ac) {
	for (i = _ac_thunk.length-1; i >= 0; i--)
		if (_ac_thunk[i] != null && _ac_thunk[i][0] == ac.obj.id) {
			clearTimeout(_ac_thunk[i][3]);
			_ac_thunk[i] = null;
		}
}

function _ac_add(ac,query,timeout) {
	var i = _ac_thunk.length;
	var handle = setTimeout("_ac_thunk_call("+i+")",timeout);
	_ac_thunk[i] = new Array(ac.obj.id,ac,query,handle);
}

/* helper functions for webserver rpc processing */
var _ac_map = new Array();
function _ac_map_add(ac) {
	_ac_map[ac.obj.id] = ac;
}

/* called to initiation suggestion process */
AC.prototype.suggest = function(query) {
/* remove redundant searches */
	if (query == this.search_term)
		return;

/* cancel any existing http call */
	_ac_cancel(this);
	if (this.request && this.request.readyState != 0) {
		this.request.abort();
	}

/* check cache */
	var lc = query.toLowerCase();
	for (i = 0; i < this.cache.length; i++)
		if (this.cache[i][0] == lc) {
			var results = this.cache[i][1];
			this.search_term = query;
			this.update_popup(results);
			return;
		}

/* send call to server */
	this.typing = true;
	this.send(query);
}

/* called to send message to a server */
AC.prototype.send = function(query) {
/* check throttle timer */
	if (this.typing) {
		_ac_add(this,query,this.sending_timeout);
		return;
	}

/* initiate new call */
	this.search_term = query;
	if (this.iframe == null) {
		this.request = this.XMLHttpRequest();
		if (this.request == null) {
			var iframe = document.createElement('IFRAME');
			iframe.src = this.url+'?i=1&id='+encodeURI(this.obj.id)+'&t='+encodeURI(this.type)+'&s='+encodeURI(query);
/* opera 7.54 doesn't like iframe styles */
			iframe.style.width = '0px';
			iframe.style.height = '0px';
			this.iframe = this.obj.appendChild(iframe);
		 	this.obj.focus();
		} else {
/* send XmlHttpRequest */
			var AC = this;
			this.request.onreadystatechange = function() {
				if (AC.request.readyState == 4) {
					try {
						if (AC.request.status != 200 || AC.request.responseText.charAt(0) == '<') {
							/* some error */
						} else {
							eval(AC.request.responseText);
						}
					} catch (e) {}
				}
			}
			this.request.open("GET", this.url+"?id="+encodeURI(this.obj.id)+"&t="+encodeURI(this.type)+"&s="+encodeURI(query));
			this.request.send(null);
		}
	} else {
/* re-submit iframe */
		this.iframe.src = this.url+'?i=1&id='+encodeURI(this.obj.id)+'&t='+encodeURI(this.type)+'&s='+encodeURI(query);
		this.obj.focus();
	}
}

/* called with array of search results */
AC.prototype.update_popup = function(results) {
	if (this.search_term != null && results != null && results.length > 0) {
/* remove currently listed options */
		while (this.div.hasChildNodes())
			this.div.removeChild(this.div.firstChild);

/* default to first result when adding characters */
		if (this.previous_term == null || this.search_term.length >= this.previous_term.length) {
			this.selected_option = 0;
		} else {
/* remove selection when deleteing */
			this.selected_option = null;
		}
		this.previous_term = this.search_term;

		for (i = 0; i < results.length; i++) {
			var div = document.createElement('DIV');
			div.divid = results[i][2];
			div.AC = this;
			if (this.selected_option == div.divid)
				div.className = 'ac_highlight';
			else
				div.className = 'ac_normal';
			div.name = results[i][0];
			div.value = results[i][1];
			div.innerHTML = results[i][3];
			div.onmousedown = function() { this.AC.onselected(); }
			div.onmouseover = function() { 
if (this.AC.selected_option != null)
	this.AC.div.childNodes[this.AC.selected_option].className = 'ac_normal';
this.AC.selected_option = this.divid;
this.AC.cabbage = this.AC.selected_option;
this.className = 'ac_highlight';
}
			div.onmouseout = function() { this.className = 'ac_normal'; }
			this.div.appendChild(div);
		}
		this.div.style.visibility = 'visible';

/* complete text box with selected text */

		if (this.selected_option == 0 && 
			(this.obj.createTextRange || this.obj.setSelectionRange) &&
			this.obj.value != results[0][1] &&
			results[0][1].substring(0,this.search_term.length).toLowerCase() == this.search_term.toLowerCase())
		{
			this.obj.value = results[0][1];
			if (this.obj.createTextRange) {
				var range = this.obj.createTextRange();
				range.moveStart('character',this.search_term.length);
				range.select();
			} else {
//				var range = document.createRange();
//				range.setStart(this.obj,this.search_term.length);
				this.obj.setSelectionRange(this.search_term.length,this.obj.value.length);
			}
		}
	} else {
		this.close_popup();
	}

/* update cache */
	var found = false;
	var lc = this.search_term.toLowerCase();
	for (i = 0; i < this.cache.length; i++)
		if (this.cache[i][0] == lc) {
			found = true;
			break;
		}

	if (!found) {
		this.cache[this.cache.length] = new Array(lc, results);
	}
}

/* update auto-compete input element with selected option */
AC.prototype.update_input = function() {
	this.obj.value = this.div.childNodes[this.selected_option].name;
}

/* when option is clicked with mouse, or entered with keyboard */
AC.prototype.onselected = function() {
	if (this.selected_option == null)
		if (this.cabbage == null)
			return;
		else
			this.selected_option = this.cabbage;	/* opera funky */

	this.update_input();

/* hide popup */
	this.close_popup();
/* submit form */
	if (this.submit_callback)
		this.submit_callback();
}

/* capture up & down actions to prevent moving cursor left or right */
/* input.onkeypress() */
AC.prototype.onkeypress = function(e) {
	if (!e) e = window.event;
	var c = e.keyCode;
	if (c == 0) c = e.charCode;
	if(e.charCode) {_ac_key_check(this.AC,this.AC.typing_timeout); return;}
	switch (c) {
	case 38:	/* up */
	case 40:	/* down */
		e.returnValue = false;
		if (e.preventDefault) e.preventDefault();
		break;

	default: break;
	}
}

/* move cursor on down to allow repeating */
/* input.onkeydown() */
AC.prototype.onkeydown = function(e) {
	if (!e) e = window.event;
	var c = e.keyCode;
	if (c == 0) c = e.charCode;
	var i = this.AC.selected_option == null ? -1 : this.AC.selected_option;
	if(e.charCode) {_ac_key_check(this.AC,this.AC.typing_timeout); return;}
	switch (c) {
	case 38:	/* up */
		i--;
		e.returnValue = false;
		if (e.preventDefault) e.preventDefault();
		break;

	case 40:	/* down */
		i++;
		e.returnValue = false;
		if (e.preventDefault) e.preventDefault();
		break;

	default:
		_ac_key_check(this.AC,this.AC.typing_timeout);
		break;
	}

	if (c == 38 || c == 40) {
		var length = this.AC.div.childNodes.length;
		if (i < 0) i = 0;
		if (i >= length) i = length-1;
		if (i != this.AC.selected_option) {
			for (j = 0; j < length; j++) {
				if (j == i) {
					this.AC.obj.value = this.AC.div.childNodes[j].value;
					this.AC.selected_option = this.AC.div.childNodes[j].divid;
					this.AC.div.childNodes[j].className = 'ac_highlight';
				} else {
					this.AC.div.childNodes[j].className = 'ac_normal';
				}
			}

/* update search term */
			this.AC.search_term = this.AC.div.childNodes[this.AC.selected_option].value;

/* popup if hidden */
			if (this.AC.div.style.visibility == 'hidden') {
				this.AC.suggest (this.AC.searched_term);
			}
		}
	}
}
	
/* input.onkeyup() */
AC.prototype.onkeyup = function(e) {
	if (!e) e = window.event;
	var c = e.keyCode;
	if (c == 0) c = e.charCode;
	switch (c) {
/* prevent strange selections at top of option list */
	case 38:	/* up */
	case 40:	/* down */
		e.returnValue = false;
		if (e.preventDefault) e.preventDefault();
		break;

/* select highlighted option */
	case 13:	/* enter */
		this.AC.onselected();
		e.returnValue = false;
		if (e.preventDefault) e.preventDefault();
		break;

/* hide popup window */
	case 27:	/* escape */
		this.AC.close_popup();
		e.returnValue = false;
		if (e.preventDefault) e.preventDefault();
		break;

/* get new suggestion for new data */
	default:

/* for latin this is ok: 
		if (this.value.length > 0) {
			this.AC.searched_term = this.value;
			this.AC.suggest(this.value);
		} else {
			_ac_cancel(this.AC);
			this.AC.close_popup();
		}
*/
		break;
	}
}

/* iframe or XmlHttpRequest() callback */
function _ac_rpc() {
	var id = arguments[0];
	if (_ac_map[id]) {
/* we cannot shift() arguments as it is an object :( */
		_ac_map[id].process_reply.apply(_ac_map[id],arguments);
	}
}

/* parse rpc results into html for the popup */
AC.prototype.process_reply = function() {
	var results = new Array();
	var c = 0;
	var re = new RegExp('('+this.searched_term+')', "gi");
	var nt = '<font color="red"><b>$1</b></font>';
	for (i = 1; i < arguments.length; i += 2) {
		var name = this.highlight ? arguments[i+1].replace(re, nt) : arguments[i+1];
		var value = this.highlight ? arguments[i].replace(re, nt) : arguments[i];
		var html = "<span class='d'>"+name+"</span><span class='a'>"+value+"</span>";
		results[c] = new Array(arguments[i+1], arguments[i], c, html);
		c++;
	}

	this.update_popup(results);
}

function escapeURI(La){
  if(encodeURIComponent) {
    return encodeURIComponent(La);
  }
  if(escape) {
    return escape(La)
  }
}
