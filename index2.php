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
	        marker = new google.maps.Marker({
	        	position: event.latLng, 
	        	map: map
	        });
	        markers.push(marker);
	        
	        // Store node's lat and lng
	        nodes.push(event.latLng);
	        
	        // displayRoute(nodes[0], nodes[1], directionsService, directionsDisplay);

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
	</div>

	<!-- Load google map -->
	<div id="map" style="z-index: -9999; width: 100%; height: 1024px; float: right;" />

</body>
</html>