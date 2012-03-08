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

	// Launch the app
	var app = new SchoolBrowser ();

	document.body.addEvent ('DOMUpdated', function () {
		$$('.related_disciplines:not([data-rigged~="discipline_accordion"])').each (function (element, index) {
			new DisciplineAccordion (element, {
				'handles': '> li'
			});
			element.appendProperty ('data-rigged', 'discipline_accordion');
		});
	}).fireEvent ('DOMUpdated');
});