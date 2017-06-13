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
	  var directionsService;
	  var directionsDisplay;

	  var nodes = [];
	  var markers = [];
	  var waypoints = [];

	  function myMap() {
        var map = new google.maps.Map(document.getElementById('map'), {
          zoom: 4,
          center: {lat: -24.345, lng: 134.46}  // Australia.
        });

        directionsService = new google.maps.DirectionsService;
        directionsDisplay = new google.maps.DirectionsRenderer({
          draggable: true,
          map: map,
        });

        directionsDisplay.addListener('directions_changed', function() {
          computeTotalDistance(directionsDisplay.getDirections());
        });

        // Create map click event
	    google.maps.event.addListener(map, 'click', function(event) {      
	        // Add a node to map
	        marker = new google.maps.Marker({
	        	position: event.latLng, 
	        	map: map
	        });
	        markers.push(marker);
	        
	        // Store node's lat and lng
	        nodes.push(event.latLng);
	        
	        // displayRoute(result[0], result[1], directionsService, directionsDisplay);

	     //    for (index in markers) {
		    //     markers[index].setMap(null);
		    // }

		    // prevNodes = nodes;
		    // nodes = [];
		    // markers = [];
		    document.getElementById('lat').innerHTML = nodes[0].lat();
		    document.getElementById('lng').innerHTML = nodes[0].lng();
	    });
      }

      function configRoute() {
      	waypoints = [];

      	for (var i = 0; i < result.length - 2; i++) {
      		waypoints.push({
      			location: new google.maps.LatLng(result[i+1][0], result[i+1][1]),
      			stopover: true
      		});
      	}

      	var from = new google.maps.LatLng(result[0][0], result[0][1]);
      	var destination = new google.maps.LatLng(result[result.length-1][0], result[result.length-1][1]);

	    displayRoute(from, destination, directionsService, directionsDisplay);

      	document.getElementById('waypoints').innerHTML = waypoints;
      	document.getElementById('from').innerHTML = result[0];
      	document.getElementById('to').innerHTML = result[result.length - 1];
      }

	  function displayRoute(origin, destination, service, display) {
        service.route({
          origin: origin,
          destination: destination,
          travelMode: 'DRIVING',
          waypoints: waypoints,
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

      /**
      	* Simulated Annealing
      	**/

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

	<div>
		<p>LATITUDE</p>
		<span id="lat" style="margin-left: 12px; margin-bottom: 6px" >0.00</span>
		<p>LONGITUDE</p>
		<span id="lng" style="margin-left: 12px; margin-bottom: 6px" >0.00</span>
		<p>RESULT</p>
		<span id="result" style="margin-left: 12px; margin-bottom: 6px" >0.00</span>
		<p>DISTANCE</p>
		<span id="distance" style="margin-left: 12px; margin-bottom: 6px" >0.00</span>
		<p>WAYPOINTS</p>
		<span id="waypoints" style="margin-left: 12px; margin-bottom: 6px" >0.00</span>
		<p>FROM</p>
		<span id="from" style="margin-left: 12px; margin-bottom: 6px" >0.00</span>
		<p>TO</p>
		<span id="to" style="margin-left: 12px; margin-bottom: 6px" >0.00</span>
	</div>
	<div>
		<div id="settings">
			<h2><i class="fa fa-cog"></i> SETTINGS</h2>
			<table>
				<tbody>
					<tr>
						<td>Cities</td>
						<td>
						<input type="text" id="cities" value="10"></input>
						</td>
					</tr>
					<tr>
						<td>Initial Temperature</td>
						<td>
						<input type="text" id="temperature" value="0.1"></input>
						</td>
					</tr>
					<tr>
						<td>Absolute Zero</td>
						<td>
						<input type="text" id="abszero" value=".0001"></input>
						</td>
					</tr>
					<tr>
						<td>Cooling Rate</td>
						<td>
						<input type="text" id="coolrate" value="0.99999"></input>
						</td>
					</tr>
				</tbody>
			</table>
			<div style="margin: 15px auto; width: 100px; border: 1px solid black;">
				<button id="solve">Solve</button>
			</div>
		</div>
		<canvas id="tsp-canvas" width="700" height="600">
			HTML5 Unsupported.
		</canvas>
	</div>

	<!-- Load google map -->
	<div id="map" style="z-index: -9999; width: 100%; height: 1024px; float: right;" />

	<script type="text/javascript">
		var temperature = 0.1;
		var ABSOLUTE_ZERO = 1e-4;
		var COOLING_RATE = 0.999999;
		var CITIES = nodes.length;
		var current = [];
		var best = [];
		var best_cost = 0;
		var result = [];

		$(document).ready(function()
			{
				$("#solve").click(function()
					{
						temperature = parseFloat($("#temperature").val());
						ABSOLUTE_ZERO = parseFloat($("#abszero").val());
						COOLING_RATE = parseFloat($("#coolrate").val());
						CITIES = nodes.length;
						init();
					});
			});

		var tsp_canvas = document.getElementById('tsp-canvas');
		var tsp_ctx = tsp_canvas.getContext("2d");

		//init();

		function randomFloat(n)
		{
			return (Math.random()*n);
		}

		function randomInt(n)
		{
			return Math.floor(Math.random()*(n));
		}

		function randomInteger(a,b)
		{
			return Math.floor(Math.random()*(b-a)+a);
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
			setInterval(solve, 10);
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
					configRoute();
				}
				temperature *= COOLING_RATE;
			}
		}

		function paint()
		{
			tsp_ctx.clearRect(0,0, tsp_canvas.width, tsp_canvas.height);
			// Cities
			for(var i=0; i<CITIES; i++)
			{
				tsp_ctx.beginPath();
				tsp_ctx.arc(best[i][0], best[i][1], 4, 0, 2*Math.PI);
				tsp_ctx.fillStyle = "#0000ff";
				tsp_ctx.strokeStyle = "#000";
				tsp_ctx.closePath();
				tsp_ctx.fill();
				tsp_ctx.lineWidth=1;
				tsp_ctx.stroke();
			}
			// Links
			tsp_ctx.strokeStyle = "#ff0000";
			tsp_ctx.lineWidth=2;
			tsp_ctx.moveTo(best[0][0], best[0][1]);
			result[0] = [best[0][0], best[0][1]];
			for(var i=0; i<CITIES-1; i++)
			{
				tsp_ctx.lineTo(best[i+1][0], best[i+1][1]);
				result[i+1] = [best[i+1][0], best[i+1][1]];
			}
			document.getElementById('result').innerHTML = result;
			tsp_ctx.lineTo(best[0][0], best[0][1]);
			tsp_ctx.stroke();
			tsp_ctx.closePath();
		}
	</script>
</body>
</html>