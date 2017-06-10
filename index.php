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
    
    
	<title>Gisku</title>
</head>
<body>

	<div id="googleMap" style="width:100%;height:815px;float: right;"></div>

	<script>
	function myMap() {
	var mapProp= {
	    center:new google.maps.LatLng(-7.2817154,112.7866759),
	    zoom:5,
	};
	var map=new google.maps.Map(document.getElementById("googleMap"),mapProp);
	}
	</script>

	<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCK6wBEKMl4FJYDQPLS0zKL_GPoRpEPEJs&callback=myMap"></script>

	<script src="js/close_menu.js"></script>
	
	<i class="fa fa-bars toggle_menu"></i>


	<div class="sidebar_menu">
		<i class="fa fa-times"></i>
		<center>
			<h1 class="boxed_item">TRAVELLING<span class="logo_bold"> SALESMAN</span></h1>
			<h2 class="logo_title"> Simulated Annealing</h2>
		</center>

		<!-- <ul class="navigation_section">
			<li class="navigation_item" id="profile">
				PROFILE
			</li>
			<li class="navigation_item" id="education">
				EDUCATION
			</li>
			<li class="navigation_item" id="work_experience">
				WORK EXPERIENCE
			</li>
			<li class="navigation_item" id="skills">
				SKILLS
			</li>
			<li class="navigation_item" id="interests">
				INTERESTS
			</li>
			<li class="navigation_item" id="portfolio">
				PORTFOLIO
			</li>
			<li class="navigation_item" id="contact">
				CONTACT
			</li>
		</ul> -->
		<form style="padding-left: 10px;padding-right: 10px">
		  <div class="form-group">
			  <div class="input-group">
			    <span class="input-group-addon"><i class="glyphicon glyphicon-user"></i></span>
			    <input id="muatan" type="text" class="form-control" name="muatan" placeholder="Muatan">
			  </div>
		  </div>
		  <div class="form-group">
			  <div class="input-group">
			    <span class="input-group-addon"><i class="glyphicon glyphicon-lock"></i></span>
			    <input id="truk" type="truk" class="form-control" name="truk" placeholder="Truk">
			  </div>
		  </div>
		</form>

		<center>
			<a href="#"><h1 class="boxed_item boxed_item_smaller signup">
			<i class="fa fa-user"></i>
				RUN
			</h1></a>
		</center>
	</div><!-- End of sidebar -->

	

</body>
</html>