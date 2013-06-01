function AjaxResponder(params) {
	this.xhttp = null;
	this.controller = (params.controller) ? params.controller : '';
	this.method = (params.method) ? params.method : '';
	this.status = false;
	this.callback = (params.callback) ? params.callback : null;
	
	this.init = function() {
		if (window.XMLHttpRequest) {
			this.xhttp = new XMLHttpRequest();
		} else if (window.ActiveXObject) {
			try {
				this.xhttp = new ActiveXObject("Msxml2.XMLHTTP");
			} 
			catch (e) {
				try {
					this.xhttp = new ActiveXObject("Microsoft.XMLHTTP");
				} 
				catch (e) {}
			}
		}

		if (!this.xhttp) {
			alert('Giving up :( Cannot create an XMLHTTP instance');
		}
		return this;
	};
	
	this.dispatch = function(params) {
		if (params.xhttp.readyState === 4) {
			if (params.xhttp.status === 200) {
				params.callback(JSON.parse(params.xhttp.responseText));
			} else {
				alert('There was a problem with the request.');
			}
		}
	};
	
	this.service = function(args) {
		var self = this;
		if(self.xhttp) {
			var arglist = encodeURIComponent(args);
			
			self.xhttp.onreadystatechange = function() {
				self.dispatch({
					xhttp: self.xhttp,
					callback: self.callback
				});
			};
			self.xhttp.open('POST', BASE_PATH + self.controller + '/' + self.method + '/');
			self.xhttp.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
			self.xhttp.send('args=' + arglist);
		}
	};
}