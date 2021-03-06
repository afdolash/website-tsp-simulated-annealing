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
	<script src="https://code.jquery.com/jquery-1.11.3.min.js"></script>
	<script src="js/menu_toggle.js"></script>
    <script src="js/bootstrap.min.js"></script>

	<!-- Title -->
	<title>Simulated Annealing</title>

	<!-- Javascript for load google map -->
	<script>
	  var map;
	  var directionsService;
	  var directionsDisplay;

	  var nodes = [];
	  var markers = [];
	  var waypoints = [];
	  var arrMarkers = [];
	  var marker;
      var bounds;

	  function myMap() {
	  	// Make new map
        map = new google.maps.Map(document.getElementById('map'), {
          zoom: 12,
          center: {lat: -7.257931, lng: 112.757346}  // Australia.
        });

        directionsService = new google.maps.DirectionsService;
        directionsDisplay = new google.maps.DirectionsRenderer({
          draggable: false,
          map: map
        });

        // Calculate direction distance
        directionsDisplay.addListener('directions_changed', function() {
          computeTotalDistance(directionsDisplay.getDirections());
        });

        // Create map click event
	    google.maps.event.addListener(map, 'click', function(event) {      
	        // Add a node to map
	        marker = new google.maps.Marker({
	        	position: event.latLng, 
	        	map: map,
	        	draggable: false
	        });
	        markers.push(marker);
	        
	        // Store node's lat and lng
	        nodes.push(event.latLng);
	    });
      }

      // Set waypoints by node, after filtering in Simulated Annealing
      function configWaypoints(requestCode) {
      	waypoints = [];

      	for (var i = 0; i < result.length - 1; i++) {
      		waypoints.push({
      			location: new google.maps.LatLng(result[i+1][0], result[i+1][1]),
      			stopover: true
      		});
      	}

      	var from = new google.maps.LatLng(result[0][0], result[0][1]);
      	var destination = new google.maps.LatLng(result[0][0], result[0][1]);

	    if (directionsDisplay != null) {
	    	displayRoute(from, destination, directionsService, directionsDisplay, requestCode);
	    } else {
	    	directionsDisplay = new google.maps.DirectionsRenderer({
	          draggable: false,
	          map: map
	        });

	        directionsDisplay.addListener('directions_changed', function() {
	          computeTotalDistance(directionsDisplay.getDirections());
	        });

	    	displayRoute(from, destination, directionsService, directionsDisplay, requestCode);
	    }

	    clearMapMarkers();
      }

      // Display the route by nodes
	  function displayRoute(origin, destination, service, display, requestCode) {	
        service.route({
          origin: origin,
          destination: destination,
          travelMode: 'DRIVING',
          waypoints: waypoints,
          avoidTolls: true
        }, function(response, status) {
          if (status === 'OK') {
            display.setDirections(response);
            if (requestCode == 1) {
            	driveSim(response);
            }
          } else {
            // alert('Could not display directions due to: ' + status);
          }
        });
      }

      // Make animate marker on direction
      function driveSim (response){
    	var path = response.routes[0].overview_path;
	    var maxIter = path.length;

	    // Make new marker
	    taxiCab = new google.maps.Marker({
	       position: path[0],
	       map: map, 
	    });

	    // Delay animate
	    var delay = 150, count = 0;
	    function delayed () {
	      taxiCab.setPosition({lat:path[count].lat(),lng:path[count].lng()});
	      if (count < maxIter-1) {
	        setTimeout(delayed, delay);
	      } else {
	      	taxiCab.setMap(null);
	      }

	      count += 1;
	    }

	    delayed();	    
	  }  

	  // Removes markers and temporary paths
	  function clearMapMarkers() {
	    for (index in markers) {
	        markers[index].setMap(null);
	    }

	    prevNodes = nodes;
	    nodes = [];
	    markers = [];
	  }

	  function clearDirection() {
	  	// If there are directions being shown, clear them
	    if (directionsDisplay != null) {
	        directionsDisplay.setMap(null);
	        directionsDisplay = null;
	    }
	  }

	  // Calculate direction distance
      function computeTotalDistance(result) {
        var total = 0;
        var myroute = result.routes[0];
        for (var i = 0; i < myroute.legs.length; i++) {
          total += myroute.legs[i].distance.value;
        }
        total = total / 1000;
        document.getElementById('distance').innerHTML = total + ' km';
      }

	  // Initialize of random node
      function initialize() {
      	var latlng = new google.maps.LatLng(-7.257931, 112.757346);
      	var myOptions = {
      		zoom:12,
      		center:latlng,
      		mapTypeId:google.maps.MapTypeId.HYBRID,
      		mapTypeControlOptions:
      		{
      			style:google.maps.MapTypeControlStyle.DROPDOWN_MENU
      		}
      	};

      	map = new google.maps.Map(document.getElementById("map_canvas"), myOptions);
      }

      // Event if button random cliked
      function ftn_button_clicked() {
      	// Clear arrMaker value
      	if (arrMarkers) {
      		for (i in arrMarkers) {
      			arrMarkers[i].setMap(null);
      		}
      	}

      	arrMarkers = new Array(0);
      	var num = document.getElementById("nm").value;

      	if (num < 1000) {
      		plotrandom(num);
      	}
      }

      // Plant the random marker
      function plotrandom(number) {
      	// Bounds random marker on the map
      	bounds = map.getBounds();

      	var southWest = new google.maps.LatLng(-7.237931, 112.757346);
      	var northEast = new google.maps.LatLng(-7.357931, 112.687346);
      	var lngSpan = northEast.lng() - southWest.lng();
      	var latSpan = northEast.lat() - southWest.lat();

      	nodes = [];
      	pointsrand = [];

      	// Push the LatLng of random marker to nodes[]
      	for(var i=0;i<number;++i) {
      		var point = new google.maps.LatLng(southWest.lat() + latSpan * Math.random(),southWest.lng() + lngSpan * Math.random());
      		pointsrand.push(point);

      		// Store node's lat and lng
      		nodes.push(pointsrand[i]);
      	}

      	// Place random marker on the map
      	for(var i=0;i<number;++i) {
      		marker = placeMarker(pointsrand[i]);

      		arrMarkers.push(marker);
      		marker.setMap(map);
      	}
      }

      // Marker option of random marker
      function placeMarker(location) { 
      	var marker = new google.maps.Marker({
      		position:location,
      		map:map,
      		draggable:false,
      		optimized: false,
      		zIndex:-99999999
      	});

      	return marker;
      }

      // View distance detail
      function viewLog() {
      	var result = directionsDisplay.getDirections();
        var total = 0;
        var myroute = result.routes[0];
        var text = "";

        for (var i = 0; i < myroute.legs.length; i++) {
          total += myroute.legs[i].distance.value;
          text = text + " Node "+ (i+1) +" - Node "+ (i+2) +" : "+ myroute.legs[i].distance.value / 1000 +"<br>";
        }

        total = total / 1000;
        text = text + " <br><b>Total distance of nodes : "+ total +"<b>";
        document.getElementById('viewlog-body').innerHTML = ""+ text;
      }

	</script>

	<script async defer src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCK6wBEKMl4FJYDQPLS0zKL_GPoRpEPEJs&callback=myMap"></script>
	<script src="js/close_menu.js"></script>

</head>

<body>
	<!-- Toogle menu -->
	<i class="fa fa-bars toggle_menu"></i>

	<!-- Modal -->
    <div class="modal fade" id="myViewlog" role="dialog">
      <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal">&times;</button>
            <h4 class="modal-title">Detail Distance</h4>
          </div>
          <div id="viewlog-body" class="modal-body">
          	<!-- Text here -->
          </div>
        </div>  
      </div>
    </div>

	<!-- Sidebar -->
	<div class="sidebar_menu" style="float: left;">
		<i class="fa fa-times"></i>
		<center>
			<h1 class="boxed_item">TRAVELLING<span class="logo_bold"> SALESMAN</span></h1>
			<h2 class="logo_title">Simulated Annealing</h2>
		</center>

		<ul class="navigation_section" style="width: 85%;">
			<li class="navigation_item">
				<p>DISTANCE</p>
				<span id="distance" style="margin-left: 12px; margin-bottom: 6px" >0.00 km</span>
			</li>
			<li class="navigation_item">
				<p>INITIAL TEMPERATURE</p>
				<input type="text" placeholder="Initial temperature" class="form-control" id="temperature" value="0.1" style="margin-bottom: 6px">
			</li>
			<li class="navigation_item">
				<p>ABSOLUTE ZERO</p>
				<input type="text" placeholder="Absolute zero" class="form-control" id="abszero" value="0.01" style="margin-bottom: 6px">
			</li>
			<li class="navigation_item">
				<p>COOLING RATE</p>
				<input type="text" placeholder="Cooling rate" class="form-control" id="coolrate" value="0.99" style="margin-bottom: 6px">
			</li>
			<li class="navigation_item">
				<p>RANDOM NODE</p>
				<input type="text" placeholder="Number of node" class="form-control" id="nm" value="5" style="margin-bottom: 6px">
				<center>
					<a href="#" id="random" onclick="ftn_button_clicked()">
					  <h1 class="boxed_item boxed_item_smaller" style="width: 100%;">
					    RANDOM
					  </h1>
				  	</a>
				</center>
			</li>
			<li>
				<br><br><br>
				<center>
				  	<table style="width: 100%;">
				  		<tr>
				  			<a href="#" id="viewlog" data-toggle="modal" data-target="#myViewlog" onclick="viewLog()">
							  <h1 class="boxed_item boxed_item_smaller" style="width: 100%; margin-bottom: 6px;">
							    DETAIL DISTANCE
							  </h1>
						  	</a>
				  		</tr>
				  		<tr>
				  		  <th>
			  				<a href="#" id="reload" onClick="window.location.reload()">
							  <h1 class="boxed_item boxed_item_smaller" style="width: 98%; margin-right: 3px;">
							    RELOAD
							  </h1>
						  	</a>
				  		  </th>
				  		  <th>
				  		  	<a href="#" id="solve">
							  <h1 class="boxed_item boxed_item_smaller" style="width: 98%; margin-left: 3px;">
							    SOLVE
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

	<!-- Simulated Annealing -->
	<script type="text/javascript">
		var temperature = 0.1;
		var ABSOLUTE_ZERO = 0.01;
		var COOLING_RATE = 0.99;
		var CITIES = nodes.length;
		var current = [];
		var best = [];
		var best_cost = 0;
		var result = [];
		var status;

		$(document).ready(function()
			{
				$("#solve").click(function()
					{
						temperature = parseFloat($("#temperature").val());
						ABSOLUTE_ZERO = parseFloat($("#abszero").val());
						COOLING_RATE = parseFloat($("#coolrate").val());
						CITIES = nodes.length;
						status = 0;
						init();
					});
			});

		//init();

		function randomInt(n)
		{
			return Math.floor(Math.random()*(n));
		}

		function deep_copy(array, to)
		{
			var i = array.length;
			while(i--)
			{
				to[i] = [array[i][0],array[i][1]];
			}
		}

		function getCost(route)
		{
			var cost = 0;
			for(var i=0; i< CITIES-1;i++)
			{
				cost = cost + getDistance(route[i], route[i+1]);
			}
			cost = cost + getDistance(route[0],route[CITIES-1]);
			return cost;
		}

		function getDistance(p1, p2)
		{
			del_x = p1[0] - p2[0];
			del_y = p1[1] - p2[1];
			return Math.sqrt((del_x*del_x) + (del_y*del_y));
		}

		function mutate2Opt(route, i, j)
		{
			var neighbor = [];
			deep_copy(route, neighbor);
			while(i != j)
			{
				var t = neighbor[j];
				neighbor[j] = neighbor[i];
				neighbor[i] = t;

				i = (i+1) % CITIES;
				if (i == j)
					break;
				j = (j-1+CITIES) % CITIES;
			}
			return neighbor;
		}

		function acceptanceProbability(current_cost, neighbor_cost)
		{
			if(neighbor_cost < current_cost)
				return 1;
			return Math.exp((current_cost - neighbor_cost)/temperature);
		}

		function init()
		{
			for(var i=0;i<CITIES;i++)
			{
				current[i] = [nodes[i].lat(),nodes[i].lng()];
			}

			deep_copy(current, best);
			best_cost = getCost(best);
			setInterval(solve, 20);
		}

		function solve()
		{
			if(temperature>ABSOLUTE_ZERO)
			{
				var current_cost = getCost(current);
				var k = randomInt(CITIES);
				var l = (k+1+ randomInt(CITIES - 2)) % CITIES;
				if(k > l)
				{
					var tmp = k;
					k = l;
					l = tmp;
				}
				var neighbor = mutate2Opt(current, k, l);
				var neighbor_cost = getCost(neighbor);
				if(Math.random() < acceptanceProbability(current_cost, neighbor_cost))
				{
					deep_copy(neighbor, current);
					current_cost = getCost(current);
				}
				if(current_cost < best_cost)
				{
					deep_copy(current, best);
					best_cost = current_cost;
					paint();
				}
				temperature *= COOLING_RATE;
			} else if (status == 0) {
				configWaypoints(1);

				status = 1;
			}
		}

		function paint()
		{
			for(var i=0; i<CITIES; i++)
			{
				result[i] = [best[i][0], best[i][1]];
			}

			configWaypoints(0);
		}
	</script>
</body>
</html>