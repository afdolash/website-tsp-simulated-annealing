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
</head>
<body>

	<!-- Load google map -->
	<div id="map-canvas" style="width: 100%; height: 1024px; float: right;" />

	<!-- Javascript for load google map -->
	<script>
	var map;
	var directionsDisplay = null;
	var directionsService;
	var polylinePath;

	var nodes = [];
	var prevNodes = [];
	var markers = [];
	var durations = [];

	// Initialize google maps
	function myMap() {
		var mapProp= {
		    center:new google.maps.LatLng(-7.255868, 112.750815),
		    zoom:13,
		    streetViewControl: false,
		    mapTypeControl: false,
		};
		map = new google.maps.Map(document.getElementById("map-canvas"),mapProp);

		// Create map click event
	    google.maps.event.addListener(map, 'click', function(event) {
	        // Add destination (max 9)
	        if (nodes.length >= 9) {
	            alert('Max destinations added');
	            return;
	        }

	        // If there are directions being shown, clear them
	        clearDirections();
	        
	        // Add a node to map
	        marker = new google.maps.Marker({position: event.latLng, map: map});
	        markers.push(marker);
	        
	        // Store node's lat and lng
	        nodes.push(event.latLng);
	        
	        // Update destination count
	        $('#destinations-count').html(nodes.length);
	    });
	}

	// Get all durations depending on travel type
	function getDurations(callback) {
	    var service = new google.maps.DistanceMatrixService();
	    service.getDistanceMatrix({
	        origins: nodes,
	        destinations: nodes,
	        travelMode: google.maps.TravelMode[$('#travel-type').val()],
	        avoidHighways: parseInt($('#avoid-highways').val()) > 0 ? true : false,
	        avoidTolls: false,
	    }, function(distanceData) {
	        // Create duration data array
	        var nodeDistanceData;
	        for (originNodeIndex in distanceData.rows) {
	            nodeDistanceData = distanceData.rows[originNodeIndex].elements;
	            durations[originNodeIndex] = [];
	            for (destinationNodeIndex in nodeDistanceData) {
	                if (durations[originNodeIndex][destinationNodeIndex] = nodeDistanceData[destinationNodeIndex].duration == undefined) {
	                    alert('Error: couldn\'t get a trip duration from API');
	                    return;
	                }
	                durations[originNodeIndex][destinationNodeIndex] = nodeDistanceData[destinationNodeIndex].duration.value;
	            }
	        }

	        if (callback != undefined) {
	            callback();
	        }
	    });
	}

	// Removes markers and temporary paths
	function clearMapMarkers() {
	    for (index in markers) {
	        markers[index].setMap(null);
	    }

	    prevNodes = nodes;
	    nodes = [];

	    if (polylinePath != undefined) {
	        polylinePath.setMap(null);
	    }
	    
	    markers = [];
	    
	    $('#ga-buttons').show();
	}

	// Removes map directions
	function clearDirections() {
	    // If there are directions being shown, clear them
	    if (directionsDisplay != null) {
	        directionsDisplay.setMap(null);
	        directionsDisplay = null;
	    }
	}

	// Completely clears map
	function clearMap() {
	    clearMapMarkers();
	    clearDirections();
	    
	    $('#destinations-count').html('0');
	}

	// Initial Google Maps
	google.maps.event.addDomListener(window, 'load', myMap);

	// Create listeners
	$(document).ready(function() {
	    $('#clear-map').click(clearMap);

	    // Start GA
	    $('#find-route').click(function() {    
	        if (nodes.length < 2) {
	            if (prevNodes.length >= 2) {
	                nodes = prevNodes;
	            } else {
	                alert('Click on the map to select destination points');
	                return;
	            }
	        }

	        if (directionsDisplay != null) {
	            directionsDisplay.setMap(null);
	            directionsDisplay = null;
	        }
	        
	        $('#ga-buttons').hide();

	        // Get route durations
	        getDurations(function(){
	            $('.ga-info').show();

	            // Get config and create initial GA population
	            ga.getConfig();
	            var pop = new ga.population();
	            pop.initialize(nodes.length);
	            var route = pop.getFittest().chromosome;

	            ga.evolvePopulation(pop, function(update) {
	                $('#generations-passed').html(update.generation);
	                $('#best-time').html((update.population.getFittest().getDistance() / 60).toFixed(2) + ' Mins');
	            
	                // Get route coordinates
	                var route = update.population.getFittest().chromosome;
	                var routeCoordinates = [];
	                for (index in route) {
	                    routeCoordinates[index] = nodes[route[index]];
	                }
	                routeCoordinates[route.length] = nodes[route[0]];

	                // Display temp. route
	                if (polylinePath != undefined) {
	                    polylinePath.setMap(null);
	                }
	                polylinePath = new google.maps.Polyline({
	                    path: routeCoordinates,
	                    strokeColor: "#0066ff",
	                    strokeOpacity: 0.75,
	                    strokeWeight: 2,
	                });
	                polylinePath.setMap(map);
	            }, function(result) {
	                // Get route
	                route = result.population.getFittest().chromosome;

	                // Add route to map
	                directionsService = new google.maps.DirectionsService();
	                directionsDisplay = new google.maps.DirectionsRenderer();
	                directionsDisplay.setMap(map);
	                var waypts = [];
	                for (var i = 1; i < route.length; i++) {
	                    waypts.push({
	                        location: nodes[route[i]],
	                        stopover: true
	                    });
	                }
	                
	                // Add final route to map
	                var request = {
	                    origin: nodes[route[0]],
	                    destination: nodes[route[0]],
	                    waypoints: waypts,
	                    travelMode: google.maps.TravelMode[$('#travel-type').val()],
	                    avoidHighways: parseInt($('#avoid-highways').val()) > 0 ? true : false,
	                    avoidTolls: false
	                };
	                directionsService.route(request, function(response, status) {
	                    if (status == google.maps.DirectionsStatus.OK) {
	                        directionsDisplay.setDirections(response);
	                    }
	                    clearMapMarkers();
	                });
	            });
	        });
	    });
	});
	</script>

	<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCK6wBEKMl4FJYDQPLS0zKL_GPoRpEPEJs&callback=myMap"></script>

	<script src="js/close_menu.js"></script>
	
	<!--* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
		*															*
		*															*
		*		  	      T H E    B E G I N N I N G 				*
		*															*
		*															*
		* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * -->

	<!-- Toogle menu -->
	<i class="fa fa-bars toggle_menu"></i>

	<!-- Sidebar -->
	<div class="sidebar_menu">
		<i class="fa fa-times"></i>
		<center>
			<h1 class="boxed_item">TRAVELLING<span class="logo_bold"> SALESMAN</span></h1>
			<h2 class="logo_title">Simulated Annealing</h2>
		</center>

		<ul class="navigation_section" style="width: 85%;">
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
			<!-- <li class="navigation_item">
				<p>TRAVEL MODE</p>
				<div class="form-group" style="margin-bottom: 0;">
				  <select class="form-control" id="sel-trav-mode">
				    <option>Car</option>
				    <option>Bicycle</option>
				    <option>Walking</option>
				  </select>
				</div>
			</li> -->
			<!-- <li class="navigation_item">
				<p>AVOID HIGHWAYS</p>
				<div class="form-group" style="margin-bottom: 0;">
				  <label class="radio-inline"><input type="radio" name="rad-avd-hw">Disable</label>
				  <label class="radio-inline"><input type="radio" name="rad-avd-hw">Enable</label>
				</div>
			</li> -->
			<!-- <li class="navigation_item">
				<p>SALESMAN</p>
				<input type="text" placeholder="Number of salesman" class="form-control" id="salesman" style="margin-bottom: 6px">
			</li> -->
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
				  		  <th>
			  				<a href="#">
							  <h1 class="boxed_item boxed_item_smaller" style="width: 98.5%; margin-left: 3px;">
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

</body>
</html>