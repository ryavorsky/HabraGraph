<html>
<head>
	<title>
		HabraGraph user profile analytics
	</title>
    <script type="text/javascript" src="grapher2D.js"></script>
    <script type="text/javascript" src="Graph.js"></script>
    <script type="text/javascript" src="Parametrs.js"></script> 
    <script type="text/javascript" src="svgGraph.js"></script>
    <link href="style.css" rel="stylesheet" type="text/css">
	
</head>
<body onload="Start()">

<?php
	$dirname = '/home/virtwww/w_rost_8baa4f5f/http/hubs/';
	$user_name = $_GET["user"];
	$user_link = '<a href="http://habrahabr.ru/users/'.$user_name.'" target="blank">'.$user_name.'</a>';
?>
	<h2 class="fig">Visual analytics of 
	<a href="http://habrahabr.ru" target="blank">Habrahabr</a> IT blogging portal </h2>

	<!--- ======================================================== --->
	<hr />
	<h3 class="fig">User profile analytics for user <?=$user_link?></h3>

	<p class="ugolkrug">
		<svg id="svg2" height="500px" width="1000px" >
		</svg>
		<figcaption class="tex">
			Based on Coment Graph Analysis.
		</figcaption>
	</p>

</body>
</html>