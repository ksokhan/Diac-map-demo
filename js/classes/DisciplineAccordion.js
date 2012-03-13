/*--------------------------------------------------------------------------
Horizontal School Accordion Thing
--------------------------------------------------------------------------*/
var DisciplineAccordion = new Class ({
	'Implements': [Options, Events],

	'options': {
		/*
		'handles': '> li',
		*/
	},

	'initialize': function (element, options) {
		this.setOptions (options);
		this.element = element;
		this.attach ();
	},

	'attach': function () {
		// Measure the container
		this.element.size = this.element.getSize ();

		// Select the handles
		this.handles = this.element.getElements (this.options.handles);

		// Create the Fx.Elements object
		this.fx = new Fx.Elements (this.handles, {
			// Fx.options go here
		});

		// Widths
		this.default_width = this.element.size.x / this.handles.length;
		this.expanded_width = this.element.size.x * 0.7;
		this.pinched_width = (this.element.size.x - this.expanded_width) / (this.handles.length - 1);

		// Transitions
		this.transitions = {};
		this.transitions.pinched = {};
		this.transitions.relaxed = {};
		this.handles.each (function (element, index) {
			this.transitions.pinched[index] = {
				'width': this.pinched_width,
				'z-index': 10
			};
			this.transitions.relaxed[index] = {
				'width': this.default_width,
				'z-index': 1
			};
		}, this);

		// Tell each handle to display inline-block
		this.handles.setStyles ({
			'display': 'inline-block',
			'width': this.default_width,
		});

		if (this.handles.length > 1)
		{
			// Add events to each handle
			this.handles.each (function (element, index) {
				element.addEvent ('mouseenter', this.expandDiscipline.pass (index, this));
			}, this);

			// Add the mouseleave event to the whole container
			this.element.addEvent ('mouseleave', this.collapseDisciplines.bind (this));
		}
	},

	'expandDiscipline': function (index) {
		var transitions = Object.clone (this.transitions);
		transitions.pinched[index] = {
			'width': this.expanded_width,
			'z-index': 1
		};
		this.fx.start (transitions.pinched);
	},

	'collapseDisciplines': function () {
		this.fx.start (this.transitions.relaxed);
	},
});
