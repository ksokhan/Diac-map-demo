<!DOCTYPE html>
<html lang="en">

	<head>
		<meta charset="utf-8" />
		<title>Diac Map</title>

		<!--HTML5 RESET-->
		<link rel="stylesheet" type="text/css" href="css/html5reset.css" />
		<link href='http://fonts.googleapis.com/css?family=Open+Sans:400,600,700,400italic' rel='stylesheet' type='text/css'>

		<!-- Styles -->
		<link rel="stylesheet" type="text/css" href="css/_global.css" />

		<!--MooTools Core-->
		<script type="text/javascript" src="libraries/mootools/moo.core.1.4.5.dev.js"></script>
		<script type="text/javascript" src="libraries/mootools/moo.more.1.4.0.1.js"></script>

		<!-- EJS -->
		<script type="text/javascript" src="libraries/ejs/ejs.js"></script>

		<!-- Scripts -->
		<script type="text/javascript" src="js/classes/SchoolBrowser.js"></script>
		<script type="text/javascript" src="js/_global.js"></script>

		<!-- Google Map API -->
		<script type="text/javascript" src="http://maps.googleapis.com/maps/api/js?key=AIzaSyDHXocBCL0RRUGklPm1A6_X_pwy2yJ1NL8&sensor=false"></script>

		<!-- Custom Script -->
		<script type="text/javascript">
			window.addEvent ('domready', function () {
			});
		</script>

		<!--HTML5 Shiv for IE < 9-->
		<!--[if lt IE 9]>
		<script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script>
		<![endif]-->
	</head>

	<body>
		<header>
			<h1><a href="#">DIAC</a></h1>
			<nav>
				<ul>
					<li><a href="#">About</a></li>
					<li><a href="#">Design Pulse</a></li>
					<li><a href="#">Connect with Designers</a></li>
					<li><a href="#">Get a Design Education</a></li>
				</ul>
			</nav>
		</header>
		<section id="content_container">
			<nav id="bread_crumbs">
				<ul class="breadcrumb">
					<li><a href="#">Home</a> <span class="divider">/</span></li>
					<li><a href="#">Get a Design Education</a> <span class="divider">/</span></li>
					<li class="active"><a href="#">School Browser</a></li>
				</ul>
			</nav>

			<h1>School Browser</h1>

			<div id="app">
				<!-- This is where the map or list views are shown -->
				<div id="results_area">
					<div class="view" id="map_view">
						<div id="map_canvas"></div>
					</div>
					<div class="view" id="list_view">
						LIST VIEW
						<div id="results">
							<!-- Results get added here by the JS -->
						</div>
					</div>
				</div>

				<!-- The filter panel / sidebar -->
				<div id="sidebar">
				<form class="form-horizontal" action="#">
					<div class="handle">View Mode</div>
					<div class="box">
						<ul id="view_mode_triggers">
							<li id="list_view_mode_trigger" data-exec="setViewMode" data-exec-options="{ 'view_mode': 'list' }"><a href="#">List View</a></li>
							<li id="map_view_mode_trigger" data-exec="setViewMode" data-exec-options="{ 'view_mode': 'map' }"><a href="#">Map View</a></li>
						</ul>
					</div> <div class="handle">Design Discipline</div>
					<div class="box">
						<ul id="disciplines">
							<li><label><input type="checkbox" data-exec="updateView" value="architecture"> Architecture</label></li>
							<li><label><input type="checkbox" data-exec="updateView" value="fashion design"> Fashion Design</label></li>
							<li><label><input type="checkbox" data-exec="updateView" value="graphic design"> Graphic Design</label></li>
							<li><label><input type="checkbox" data-exec="updateView" value="industrial design"> Industrial Design</label></li>
							<li><label><input type="checkbox" data-exec="updateView" value="interior design"> Interior Design</label></li>
							<li><label><input type="checkbox" data-exec="updateView" value="landscape architecture"> Landscape Architecture</label></li>
						</ul>
					</div>
					<div class="handle">Certification</div>
					<div class="box">
						<ul id="certifications">
							<li><label><input type="checkbox" data-exec="updateView" value="degree"> Degree</label></li>
							<li><label><input type="checkbox" data-exec="updateView" value="diploma"> Diploma</label></li>
							<li><label><input type="checkbox" data-exec="updateView" value="honours degree"> Honours Degree</label></li>
							<li><label><input type="checkbox" data-exec="updateView" value="a pat on the back"> A Pat on the Back</label></li>
						</ul>
					</div>
					<div class="handle">Program Duration</div>
					<div class="box">
						<ul id="program_durations">
							<li><label><input type="checkbox" data-exec="updateView" value="1 year"> 1 year</label></li>
							<li><label><input type="checkbox" data-exec="updateView" value="2 years"> 2 years</label></li>
							<li><label><input type="checkbox" data-exec="updateView" value="3 years"> 3 years</label></li>
							<li><label><input type="checkbox" data-exec="updateView" value="4 years"> 4 years</label></li>
						</ul>
					</div>
					<div class="handle">Location</div>
					<div class="box">
						<ul>
							<li>
							    <input id="location_radius" class="span1" type="text" data-exec="updateView" data-on-event="change">
							    km from
							    <select id="location_city" class="span1" data-exec="updateView" data-on-event="change">
								    <option>Anywhere</option>
								    <option value="kingston">Kingston</option>
								    <option value="ottawa">Ottawa</option>
								    <option value="sudbury">Sudbury</option>
								    <option value="toronto">Toronto</option>
								    <option value="waterloo">Waterloo</option>
							    </select>
							</li>
						</ul>
				</form>
				</div>
			</div>
		</section>
	</body>

</html>
