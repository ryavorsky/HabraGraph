<html>
<head>
	<title>
		HabraGraph update
	</title>
</head>
<body>

<?php
	$dirname = '/home/virtwww/w_rost_8baa4f5f/http/hubs/';
	$hub_name = $_GET["hub"];
	$res_file_name = $dirname.$hub_name.'/posts_url_list.txt';
	$f = fopen($res_file_name, 'w');
	
	$data_file_name = $dirname.$hub_name.'/data.txt';
	$data = file_get_contents($data_file_name);
	
	preg_match_all ("/\"(\S+)\" class=\"post_title\"/", $data, $matches, PREG_SET_ORDER);
	
	$res = "<br />";
	foreach ($matches as $item) {
		$url = $item[1];
		fwrite($f, $url.PHP_EOL);
		$res = $res.$url.'<br />';
	};
	
	fclose($f);
?>

Result: <?=$res?>

</body>
</html>