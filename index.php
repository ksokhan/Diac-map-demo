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

		<!--LessPHP-->
		<?php
			require 'libraries/lessphp/lessc.inc.php';

			if (file_exists ('css/_global.less'))
			{
				try {
					lessc::ccompile('css/_global.less', 'css/_compiled_css/_global.css');
					echo '<link rel="stylesheet" type="text/css" href="/css/_compiled_css/_global.css" />';
				} catch (exception $ex) {
					exit ('lessc fatal error:<br />'.$ex->getMessage());
				}
			}
		?>

		<!--HTML5 Shiv for IE < 9-->
		<!--[if lt IE 9]>
		<script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script>
		<![endif]-->

		<meta name="viewport" content="initial-scale=1.0, user-scalable=no" />
		<style type="text/css">
		  html { height: 100% }
		  body { height: 100%; margin: 0; padding: 0 }
		  #map_canvas { height: 100% }
		</style>
		<script type="text/javascript"
		  src="http://maps.googleapis.com/maps/api/js?key=AIzaSyDHXocBCL0RRUGklPm1A6_X_pwy2yJ1NL8&sensor=true">
		</script>

		<script type="text/javascript">
			window.addEvent ('load', function () {
				var myOptions = {
				  center: new google.maps.LatLng(-34.397, 150.644),
				  zoom: 5,
				  mapTypeId: google.maps.MapTypeId.ROADMAP
				};
				var map = new google.maps.Map(document.getElementById("map_canvas"), myOptions);

				var geocoder = new google.maps.Geocoder ();
				var points = [
					'York University, Toronto, Ontario, Canada'
				];
				points.each (function (point, index) {
					geocoder.geocode ({ address: point }, function (results, status) {
						if (status == google.maps.GeocoderStatus.OK) {
							map.setCenter(results[0].geometry.location);
							var marker = new google.maps.Marker({
								map: map,
								position: results[0].geometry.location
							});
						} else {
							alert("Geocode was not successful for the following reason: " + status);
						}
					});
				});
			});
		</script>
	</head>

	<body>
		<div id="map_canvas" style="width:100%; height:100%"></div>
	</body>

</html>