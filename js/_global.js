Slick.definePseudo ('has', function (value) {
    return Element.getElements (this, value).length > 0;
});
window.addEvent ('domready', function () {
	var app = new SchoolBrowser ();
});
