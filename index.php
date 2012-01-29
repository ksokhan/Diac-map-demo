<!DOCTYPE html>
<html lang="en">

	<head>
		<meta charset="utf-8" />
		<title>DIAC MAP SANDBOX</title>

		<!--HTML5 RESET-->
		<link rel="stylesheet" type="text/css" href="/css/html5reset.css" />

		<!--MooTools Core-->
		<script type="text/javascript" src="/js/mootools-core-1.4.1.js"></script>
		<!--<script type="text/javascript" src="/js/mootools-more-1.4.0.1.js"></script>-->



		<!--HTML5 Shiv for IE < 9-->
		<!--[if lt IE 9]>
		<script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script>
		<![endif]-->

		<meta name="viewport" content="initial-scale=1.0, user-scalable=no" />
		<script type="text/javascript" src="http://maps.googleapis.com/maps/api/js?key=AIzaSyDHXocBCL0RRUGklPm1A6_X_pwy2yJ1NL8&sensor=false"></script>

		<script type="text/javascript">
			window.addEvent('load', function () {
				var myOptions = {
				  "center": new google.maps.LatLng(-34.397, 150.644),
				  "zoom": 10,
				  "mapTypeId": google.maps.MapTypeId.ROADMAP
				};
				var map = new google.maps.Map(document.getElementById("map_canvas"), myOptions);

				var geocoder = new google.maps.Geocoder();
				var disciplines = [
					'graphic design',
					'interior design',
					'architecture',
					'landscape architecture',
					'industrial design',
					'fashion'
				];
				var points = [
					{
						"address": "York University, Toronto, Ontario, Canada",
						"label": "York University",
						"description": "This is some info about York University!",
						"disciplines": [ "graphic design", "interior design", "architecture", "fashion" ],
					},
					{
						"address": "Sheridan College, Oakville, Ontario, Canada",
						"label": "Sheridan College",
						"description": "This is some info about Sheridan College!",
						"disciplines": [ "graphic design", "interior design", "fashion" ],
					},
					{
						"address": "OCADU, Toronto, Ontario, Canada",
						"label": "OCADU",
						"description": "This is some info about OCADU!",
						"disciplines": [ "graphic design", "industrial design" ],
					},
					{
						"address": "Ryerson University, Toronto, Ontario, Canada",
						"label": "Ryerson University",
						"description": "This is some info about Ryerson University!",
						"disciplines": [ "architecture", "graphic design" ],
					},
					{
						"address": "Unversity of Toronto, Toronto, Ontario, Canada",
						"label": "Unversity of Toronto",
						"description": "This is some info about Unversity of Toronto!",
						"disciplines": [ "fasion", "graphic design" ],
					},
					{
						"address": "George Brown College, Toronto, Ontario, Canada",
						"label": "George Brown College",
						"description": "This is some info about George Brown College!",
						"disciplines": [ "graphic design", "landscape architecture" ],
					},
				];

				// Create an info window(we'll only allow one to be shown at once)
				var info_window = new google.maps.InfoWindow({
					content: "",
					minHeight: 200,
				});

				// Center the map on Ontario
				geocoder.geocode({ address: 'Toronto, Ontario, Canada' }, function (results, status) {
					map.setCenter(results[0].geometry.location);
				});

				// Plot the markers
				points.each(function (point, index) {
					// GeoCode the address, and when it's done, move the map to that location and plot a marker
					geocoder.geocode({ address: point.address }, function (results, status) {
						// Got the address!
						if (status == google.maps.GeocoderStatus.OK) {
							// Create a marker
							point.marker = new google.maps.Marker({
								"map": map,
								"position": results[0].geometry.location,
								"title": point.label
							});

							// Rig it for the info window
							google.maps.event.addListener(point.marker, 'click', function () {
								// Set the contents of the window to the description of the point
								info_window.setContent('<div class="bubble_description">' + point.description + '</div>');

								// Show the window!
								info_window.open(map, point.marker);
							});

						} else {
							alert("Geocode was not successful for the following reason: " + status);
						}
					});
				});

				// Rig checkboxes
				var toggle_discipline = function () {
					// Get the checked boxes
					var show_disciplines = [];
					$$('#filter_panel input[type="checkbox"]:checked').each (function (box, index) {
						show_disciplines.push( box.get('value') );
					});

					// For each school...
					points.each( function( point, index ) {
						// Loop through each requested discipline
						var matches_a_discipline = false;
						show_disciplines.each( function (discipline, index) {
							// Does it match this discipline?
							if (point.disciplines.indexOf (discipline) >= 0)
							{
								matches_a_discipline = true;
							}
						});

						// Show the school on the map if it matched even one discpline,
						// otherwise take it off the map
						point.marker.setMap( (matches_a_discipline) ? map : null );
					});
				};
				$$('#filter_panel input[type="checkbox"]').addEvent('click', toggle_discipline);

				// Add counts
				disciplines.each (function ($discipline, $index) {
					var $count = 0;
					points.each (function ($point, $point_index) {
						if ($point.disciplines.indexOf ($discipline) >= 0)
						{
							$count++;
						}
					});
					$$('#discipline_' + $index + ' var').set ('html', '(' + $count + ')');
				});
			});
		</script>

		<!--LessPHP-->
		<?php
			require 'libraries/lessphp/lessc.inc.php';

			if (file_exists('css/_global.less'))
			{
				try {
					lessc::ccompile('css/_global.less', 'css/_compiled_css/_global.css');
					echo '<link rel="stylesheet" type="text/css" href="/css/_compiled_css/_global.css" />';
				} catch (exception $ex) {
					exit('lessc fatal error:<br />'.$ex->getMessage());
				}
			}
		?>
	</head>

	<body>
		<h1>Map Thing</h1>
		<div id="map_canvas"></div>
		<div id="filter_panel">
			<h2>Discipline</h2>
			<div class="contents">
				<ul>
					<li><label id="discipline_0"><input type="checkbox" name="disciplines[]" value="graphic design" checked="checked" /> Graphic Design <var></var></label></li>
					<li><label id="discipline_1"><input type="checkbox" name="disciplines[]" value="interior design" checked="checked" /> Interior Design <var></var></label></li>
					<li><label id="discipline_2"><input type="checkbox" name="disciplines[]" value="architecture" checked="checked" /> Architecture <var></var></label></li>
					<li><label id="discipline_3"><input type="checkbox" name="disciplines[]" value="landscape architecture" checked="checked" /> Landscape Architecture <var></var></label></li>
					<li><label id="discipline_4"><input type="checkbox" name="disciplines[]" value="industrial design" checked="checked" /> Industrial Design <var></var></label></li>
					<li><label id="discipline_5"><input type="checkbox" name="disciplines[]" value="fashion" checked="checked" /> Fashion <var></var></label></li>
				</ul>
			</div>
		</div>
	</body>

</html>