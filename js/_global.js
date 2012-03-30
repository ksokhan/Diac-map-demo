Element.implement ({
	// $('myElement').appendProperty ('data-rigged', ' autosave');
	'appendProperty': function (property, value) {
		this.set (property, ((this.get (property) || '') + value).trim ());
		return this;
	}
});

window.addEvent ('domready', function () {
	// Extend EJS with some ACTUALLY useful functions...
	EjsView.prototype.toClassName = function (str) {
		return str.toLowerCase ().replace (' ', '_');
	};
	EjsView.prototype.truncateAt = function (str, chars) {
		return str.slice(0, chars) + ' ...';
	};

	// Launch the app
	var app = new SchoolBrowser ();
});
