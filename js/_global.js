window.addEvent ('domready', function () {
	// Extend EJS with some ACTUALLY useful functions...
	EjsView.prototype.toClassName = function (str) {
		return str.toLowerCase ().replace (' ', '_');
	};

	// Launch the app
	var app = new SchoolBrowser ();
});