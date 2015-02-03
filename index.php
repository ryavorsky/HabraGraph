<html><head>
    <title>Habragraph analytics</title>

	<meta http-equiv="Content-Type" content="text/html; charset=windows-1251">

    <link href="style.css" rel="stylesheet" type="text/css">

    <script type="text/javascript" src="grapher2D.js"></script>
    <script type="text/javascript" src="Graph.js"></script>
    <script type="text/javascript" src="Parametrs.js"></script> 
    <script type="text/javascript" src="svgGraph.js"></script>
	
<?php

// Get the list of hubs
	$dirname = '/home/virtwww/w_rost_8baa4f5f/http/';
	$dirList = scandir($dirname.'hubs');
	$hub_list = "";
	foreach ($dirList as $value) {
		if (($value !== '.') and ($value !== '..')){
			$link = '<a href="http://rost.1gb.ru/community.php?hub='.$value.'"> '.$value.'</a>, ';
			$hub_list = $hub_list.$link."\n";
		};
	};

// Get the list of users
	$users_file_name = $dirname.'/users.txt';
	$users = file($users_file_name, FILE_IGNORE_NEW_LINES);
	$users_list = "";
	foreach ($users as $value) {
		$link = '<a href="http://rost.1gb.ru/profile.php?user='.$value.'"> '.$value.'</a>, ';
		$users_list = $users_list.$link."\n";
	};


?>	
</head>

<body onload="Start()">

	<!--- ======================================================== --->
	<h2 class="fig">Visual analytics of 
	<a href="http://habrahabr.ru" target="blank">Habrahabr</a> IT blogging portal </h2>

	<h3 class="fig">Three types of analytics are provided: per hub, per post, and per user. </h3>

	<!--- ======================================================== --->
	<hr />
	<h3 class="fig">Hubs</h3>
	<?=$hub_list?>

	<!--- ======================================================== --->
	<hr />
	<h3 class="fig">Users</h3>
	<?=$users_list?>

	<!--- ======================================================== --->
	<hr />
	<p class="ugolkrug">
		<svg id="svg2" height="500px" width="1000px" >
		</svg>
		<figcaption class="tex">
			Based on Coment Graph Analysis.
		</figcaption>
	</p>


</body></html>
