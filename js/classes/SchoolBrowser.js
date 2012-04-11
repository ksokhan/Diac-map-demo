/*--------------------------------------------------------------------------
The App
--------------------------------------------------------------------------*/
var SchoolBrowser = new Class ({
	'Implements': [Options, Events],

	'options': {
		'view_mode': 'list',
		'map_zoom_level': 6
	},

	'criteria': {
		'disciplines': [],
		'certifications': [],
		'program_durations': [],
		'location': {
			'city': '',
			'radius': 0
		}
	},

	'initialize': function (options) {
		var _self = this;

		_self.setOptions (options);

		// Load the school data, then start doing shit
		new Request.JSON ({
			'url': 'data/schools.json',
			'method': 'get',
			'onSuccess': function (schools) {
				// Save them
				_self.schools = schools;

				// Rig interface elements with their events
				_self.attach ();

				// Set the view mode
				_self.setViewMode (_self.options.view_mode);

				// Read the interface elements to build the criteria object
				_self.updateView ();
			}
		}).send ();
	},

	/*--------------------------------------------------------------------------
	Hook into page elements
	--------------------------------------------------------------------------*/
	'attach': function () {
		var _self = this;

		/*--------------------------------------------------------------------------
		Rig Executables
		--------------------------------------------------------------------------*/
		$$('[data-exec]').each (function (element) {
			var func_name = element.get ('data-exec');
			var event_name = element.get ('data-on-event') || 'click';
			var options = JSON.decode (element.get ('data-exec-options'));
			var args = [];

			// We don't really need the property name; just send the values as
			// the args to the filter function
			Object.each (options, function (value, key) {
				args.push(value);
			});

			// Bind the filter function to the click event
			element.addEvent (event_name, _self[func_name].pass (args, _self));
		});

		/*--------------------------------------------------------------------------
		Get the fields as properties of the class so we can access them everywhere
		--------------------------------------------------------------------------*/
		_self.fields = {};
		_self.fields.disciplines       = $('disciplines');
		_self.fields.certifications    = $('certifications');
		_self.fields.program_durations = $('program_durations');
		_self.fields.location_radius   = $('location_radius');
		_self.fields.location_city     = $('location_city');

		/*--------------------------------------------------------------------------
		Pre-Load EJS Templates
		--------------------------------------------------------------------------*/
		_self.templates = {
			'map_info_bubble': new EJS({ 'url': 'templates/map_info_bubble.ejs' }),
			'list_view_item': new EJS({ 'url': 'templates/list_view_item.ejs' })
		};

		/*--------------------------------------------------------------------------
		Set up the map
		--------------------------------------------------------------------------*/

		_self.google_map = new google.maps.Map ($('map_canvas'), {
			'center': new google.maps.LatLng(-34.397, 150.644),
			'zoom': _self.options.map_zoom_level,
			'mapTypeId': google.maps.MapTypeId.ROADMAP,
			'mapTypeControl': false,
			'panControl': false,
			'streetViewControl': false,
			'styles': [
				{ stylers: [ { visibility: "off" } ] },
				{ featureType: "administrative.province", elementType: "geometry", stylers: [ { visibility: "on" }, { hue: "#505A5F" }, { saturation: 0 }, { lightness: 95 }, { gamma: 9.99 } ] },
				{ featureType: "landscape", stylers: [ { visibility: "simplified" }, { hue: "#505A5F" }, { lightness: -60 }, { gamma: 0.90}, { saturation: -60 } ] },
				{ featureType: "water", stylers: [ { visibility: "simplified" }, { lightness: -60 }, { saturation: -74 }, { gamma: 1.0 } , { hue: "#505A5F" } ] },
				{ featureType: "administrative.country", stylers: [ { visibility: "simplified" }, { gamma: 0.08 }, { hue: "#505A5F" }, { saturation: 0 }, { lightness: 95 } ] }
			]/*
			[
			  {
			    stylers: [
			      { visibility: "off" }
			    ]
			  },{
			    featureType: "water",
			    stylers: [
			      { lightness: -60 },
			      { saturation: -60 },
			      { hue: '#505A5F'},
			      { visibility: "on" }
			    ]
			  },
			  {
			    featureType: "administrative.province",
			    elementType: "geometry",
			    stylers: [
			      { visibility: "on" }
			    ]
			  }
			]*/
		});

		// Create an info window(we'll only allow one to be shown at once)
		_self.google_map.info_window = new google.maps.InfoWindow({
			'content': '',
			'minHeight': 200,
		});

		// Center the map on Ontario
		_self.google_map.geocoder = new google.maps.Geocoder();
		_self.google_map.geocoder.geocode({ 'address': 'Toronto, Ontario, Canada' }, function (results, status) {
			_self.google_map.setCenter (results[0].geometry.location);
		});

		// Plot the markers for *all* schools
		_self.markers = {};
		_self.schools.each (function (school, index) {
			var new_marker = new google.maps.Marker ({
				'map': _self.google_map,
				'position': new google.maps.LatLng (school.geocoded_location.x, school.geocoded_location.y),
				'title': school.name,
				'icon': './images/pin.png'
			});

			// The marker needs to how who its school is
			new_marker.school = school;

			// Rig it for the info window
			google.maps.event.addListener (new_marker, 'click', function () {
				// Set the contents of the window to the description of the point
				_self.google_map.info_window.setContent(_self.templates.map_info_bubble.render (school));

				// Show the window!
				_self.google_map.info_window.open(_self.google_map, new_marker);
			});

			// Store the marker, indexed on the school name
			_self.markers[school.name] = new_marker;
		});
	},

	/*--------------------------------------------------------------------------
	Run a search with the current criteria and draw the results to the views
	--------------------------------------------------------------------------*/
	'updateView': function () {
		var _self = this;

		// Gather criteria
		_self.buildCriteria ();

		// Search
		var results = _self.searchSchools ();

		/*--------------------------------------------------------------------------
		List View
		--------------------------------------------------------------------------*/
		$$('#list_view #results').set ('html', _self.templates.list_view_item.render ({ 'schools': results }));

		// Re-parse the document for accordions and such
		document.body.fireEvent ('DOMUpdated');

		/*--------------------------------------------------------------------------
		Map View
		--------------------------------------------------------------------------*/
		// Hide all markers
		Object.each (_self.markers, function (marker, index) {
			marker.setMap (null);
		});
		// Now show the ones that had results
		results.each (function (school, index) {
			_self.markers[school.name].setMap (_self.google_map);
		});

		// Update the sidebar boxes
		$$('#disciplines ! .box, #certifications ! .box, #program_durations ! .box').addClass('inactive');
		$$('input:checked ! .box').removeClass ('inactive');
		$$('input:not(:checked) ! li').removeClass ('active');
		$$('input:checked ! li').addClass ('active');
		$$('.handle').removeClass ('showing_all');
		$$('.inactive.box !+ .handle').addClass ('showing_all');
	},

	/*--------------------------------------------------------------------------
	Goes through the list of checkboxes and fields and builds the criteria object
	--------------------------------------------------------------------------*/
	'buildCriteria': function () {
		var _self = this;

		// Disciplines
		_self.criteria.disciplines = [];
		_self.fields.disciplines.getElements ('input:checked').each (function (checkbox, index) {
			_self.criteria.disciplines.push (checkbox.get ('value'));
		});

		// Certifications
		_self.criteria.certifications = [];
		_self.fields.certifications.getElements ('input:checked').each (function (checkbox, index) {
			_self.criteria.certifications.push (checkbox.get ('value'));
		});

		// Program Durations
		_self.criteria.program_durations = [];
		_self.fields.program_durations.getElements ('input:checked').each (function (checkbox, index) {
			_self.criteria.program_durations.push (checkbox.get ('value'));
		});

		// Location
		_self.criteria.location.city = _self.fields.location_city.get ('value');
		_self.criteria.location.radius = _self.fields.location_radius.get ('value');
	},

	/*--------------------------------------------------------------------------
	Search schools with the current criteria
	--------------------------------------------------------------------------*/
	'searchSchools': function () {
		var _self = this;

		// Go through each school...
		var results = [];
		_self.schools.each (function (school, school_index) {
			// Then through each of its programs...
			var matching_programs = [];
			school.programs.each (function (program, program_index) {
				// Disciplines
				var matches_disciplines = program.disciplines.some (function (item, index) {
					return _self.criteria.disciplines.length == 0 || _self.criteria.disciplines.contains (item);
				});

				// Certifications
				var matches_certifications = program.certifications.some (function (item, index) {
					return _self.criteria.certifications.length == 0 ||  _self.criteria.certifications.contains (item);
				});

				// Program Durations
				var matches_program_durations = program.durations.some (function (item, index) {
					return _self.criteria.program_durations.length == 0 || _self.criteria.program_durations.contains (item);
				});

				// Locations
				/* This logic takes too much thinking. I am le tired. */

				// If it matches all of the above, add it to the results
				if (matches_disciplines && matches_certifications && matches_program_durations) {
					matching_programs.push (program);
				}
			});

			// Found matching programs?
			if (matching_programs.length) {
				// Clone the school, in a way that doesn't crash...
				/*var school_clone = {};
				for (var i in school) {
					school_clone[i] = school[i];
				}*/
				var school_clone = Object.clone (school);
				// Replace its programs with the ones that matched the search
				school_clone.programs = matching_programs;
				// Add it as a result
				results.push (school_clone);
			}
		});

		return results;
	},

	/*--------------------------------------------------------------------------
	Toggles between map and list view of results
	--------------------------------------------------------------------------*/
	'setViewMode': function (view_mode) {
		var _self = this;

		// Don't set the view_mode to the same view_mode we're already in...
		if (view_mode != _self.view_mode)
		{
			// The opposite mode is...
			var other_mode = view_mode == 'map' ? 'list' : 'map';

			$('app').removeClass(other_mode).addClass(view_mode);

			// Increment the z-index
			_self.viewModeZIndex = _self.viewModeZIndex ? _self.viewModeZIndex + 1 : 1;

			// Get our elements
			var active_element           = $(view_mode + '_view');
			var active_trigger_element   = $(view_mode + '_view_mode_trigger');
			var inactive_element         = $(other_mode + '_view');
			var inactive_trigger_element = $(other_mode + '_view_mode_trigger');

			// Fade the views out and in
			inactive_element.fade('out');
			active_element.fade('hide').setStyle ('z-index', _self.viewModeZIndex).fade('in');

			// Update the triggers
			inactive_trigger_element.removeClass ('active').addClass ('inactive');
			active_trigger_element.removeClass ('inactive').addClass ('active');

			// Check the radio
			active_trigger_element.getElement ('input[type=radio]').set ('checked', 'checked');

			// Update ourselves
			_self.view_mode = view_mode;
		}
	},
});
