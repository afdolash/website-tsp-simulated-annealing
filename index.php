<!DOCTYPE html>
<html>
<head>
	<!-- Stylesheets -->
	<link rel="stylesheet" type="text/css" href="css/font-awesome.min.css">
	<link rel="stylesheet" type="text/css" href="css/style.css">
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
	<link rel="stylesheet" type="text/css" href="css/bootstrap.css">
	
	<!-- Fonts -->
	<link href='https://fonts.googleapis.com/css?family=Open+Sans:400,600,700,800,300' rel='stylesheet' type='text/css'>
	
	<!-- Scripts -->
	<script src="http://code.jquery.com/jquery-1.10.1.min.js"></script>
	<script src="js/menu_toggle.js"></script>
    <script src="js/bootstrap.min.js"></script>

	<!-- Title -->
	<title>Simulated Annealing</title>

	<!-- Javascript for load google map -->
	<script>
	  var nodes = [];
	  var markers = [];

	  function myMap() {
        var map = new google.maps.Map(document.getElementById('map'), {
          zoom: 4,
          center: {lat: -24.345, lng: 134.46}  // Australia.
        });

        var directionsService = new google.maps.DirectionsService;
        var directionsDisplay = new google.maps.DirectionsRenderer({
          draggable: true,
          map: map,
        });

        directionsDisplay.addListener('directions_changed', function() {
          computeTotalDistance(directionsDisplay.getDirections());
        });

        // Create map click event
	    google.maps.event.addListener(map, 'click', function(event) {      
	        // Add a node to map
	        marker = new google.maps.Marker({position: event.latLng, map: map});
	        markers.push(marker);
	        
	        // Store node's lat and lng
	        nodes.push(event.latLng);
	        
	        displayRoute(nodes[0], nodes[1], directionsService, directionsDisplay);

	        for (index in markers) {
		        markers[index].setMap(null);
		    }

		    prevNodes = nodes;
		    nodes = [];
		    markers = [];
	    });
      }

	  function displayRoute(origin, destination, service, display) {
        service.route({
          origin: origin,
          destination: destination,
          travelMode: google.maps.TravelMode[$('#travmode').val()],
          // waypoints: [{location: 'Broken Hill, NSW'}, {location: 'Adelaide, SA'}],
          avoidTolls: true
        }, function(response, status) {
          if (status === 'OK') {
            display.setDirections(response);
          } else {
            alert('Could not display directions due to: ' + status);
          }
        });
      }

      function computeTotalDistance(result) {
        var total = 0;
        var myroute = result.routes[0];
        for (var i = 0; i < myroute.legs.length; i++) {
          total += myroute.legs[i].distance.value;
        }
        total = total / 1000;
        document.getElementById('distance').innerHTML = total + ' km';
      }
	</script>

	<script async defer src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCK6wBEKMl4FJYDQPLS0zKL_GPoRpEPEJs&callback=myMap"></script>

	<script src="js/close_menu.js"></script>
</head>
<body>
	<!-- * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
		 *															 *
		 *															 *
		 *		  	      T H E    B E G I N N I N G 				 *
		 *															 *
		 *															 *
		 * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * -->

	<!-- Toogle menu -->
	<i class="fa fa-bars toggle_menu"></i>

	<!-- Sidebar -->
	<div class="sidebar_menu" style="float: left;">
		<i class="fa fa-times"></i>
		<center>
			<h1 class="boxed_item">TRAVELLING<span class="logo_bold"> SALESMAN</span></h1>
			<h2 class="logo_title">Simulated Annealing</h2>
		</center>

		<ul class="navigation_section" style="width: 85%;">
			<li class="navigation_item">
				<p>TRAVEL MODE</p>
				<div class="form-group" style="margin-bottom: 0;">
				  <select class="form-control" id="travmode">
				    <option value="DRIVING">Driving</option>
				  	<option value="WALKING">Walking</option>
				  	<option value="BICYCLING">Bicycling</option>
				  </select>
				</div>
			</li>
			<li class="navigation_item">
				<p>DISTANCE</p>
				<span id="distance" style="margin-left: 12px; margin-bottom: 6px" >0.00 km</span>
			</li>
			<li class="navigation_item">
				<p>INITIAL TEMPERATURE</p>
				<input type="text" placeholder="Initial temperature" class="form-control" id="temperature" style="margin-bottom: 6px">
			</li>
			<li class="navigation_item">
				<p>ABSOLUTE ZERO</p>
				<input type="text" placeholder="Absolute zero" class="form-control" id="abszero" style="margin-bottom: 6px">
			</li>
			<li class="navigation_item">
				<p>COOLING RATE</p>
				<input type="text" placeholder="Cooling rate" class="form-control" id="coolrate" style="margin-bottom: 6px">
			</li>
			<li class="navigation_item">
				<p>RANDOM NODE</p>
				<input type="text" placeholder="Number of node" class="form-control" id="cities" style="margin-bottom: 6px">
				<center>
					<a href="#">
					  <h1 class="boxed_item boxed_item_smaller" style="width: 100%;">
					    RANDOM
					  </h1>
				  	</a>
				</center>
			</li>
			<li>
				<br><br><br>
				<center>
					<a href="#">
					  <h1 class="boxed_item boxed_item_smaller" style="width: 100%; margin-bottom: 6px">
					    VIEW LOG
					  </h1>
				  	</a>
				  	<table style="width: 100%;">
				  		<tr>
				  		  <th>
			  				<a href="#">
							  <h1 class="boxed_item boxed_item_smaller" style="width: 98.5%; margin-right: 3px;">
							    CLEAR
							  </h1>
						  	</a>
				  		  </th>
				  		  <th id="find-route">
			  				<a href="#" id="find-route">
							  <h1 id="find-route" class="boxed_item boxed_item_smaller" style="width: 98.5%; margin-left: 3px;">
							    START
							  </h1>
						  	</a>
				  		  </th>
				  		</tr>
				  	</table>
				</center>
			</li>
		</ul>
	</div>
	<!-- End of sidebar -->

	<!-- Load google map -->
	<div id="map" style="z-index: -9999; width: 100%; height: 1024px; float: right;" />
</body>
</html>