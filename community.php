<html>
<head>
	<title>
		HabraGraph community analytics
	</title>
    <link href="style.css" rel="stylesheet" type="text/css">
	
    <script type="text/javascript" src="grapher2D.js"></script>
    <script type="text/javascript" src="Graph.js"></script>
    <script type="text/javascript" src="Parametrs.js"></script> 
    <script type="text/javascript" src="svgGraph.js"></script>
</head>
<body onload="Start()">

<?php
	$hub_name = $_GET["hub"];
	$dirname = '/home/virtwww/w_rost_8baa4f5f/http/hubs/'.$hub_name.'/';
	$hub_link = '<a href="http://habrahabr.ru/hub/'.$hub_name.'" target="blank">'.$hub_name.'</a>';
	$file_name = $dirname.'list.html';
	$posts_list = file_get_contents($file_name);
?>
	<h2 class="fig">Visual analytics of 
	<a href="http://habrahabr.ru" target="blank">Habrahabr</a> IT blogging portal </h2>

	<!--- ======================================================== --->
	<hr />
	<h3 class="fig">Community analytics for <?=$hub_link?> hub</h3>
	<table>
	<tr>
		<td width = 200>
			<?=$posts_list?>
		</td>
		<td  valign="top">
			<p class="ugolkrug">
				<svg id="svg2" height="500px" width="1000px" >
				</svg>
				<figcaption class="tex">
					Based on Coment Graph Analysis.
				</figcaption>
			</p>
		</td>
	</tr>
	</table>


</body>
</html>