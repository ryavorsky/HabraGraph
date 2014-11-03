<html>

<head>
	<title>
		HabraGraph analyst
	</title>

<?php

$dirname = '/home/virtwww/w_rost_8baa4f5f/http/hubs';
$hub_list = '';
$out = '';

function initialize($dirname) {

	if (!file_exists('habr.txt')) { 
		$habr_hubs_page = file_get_contents('http://habrahabr.ru/hubs/');
		$f = fopen('habr.txt', 'w');
		fwrite($f, $habr_hubs_page);
		fclose($f);
	} else {
		$habr_hubs_page = file_get_contents('habr.txt');
	}; 	

	$pieces = explode("hub ", $habr_hubs_page);
	unset($pieces[0]);
	$hub_nodes = array();
	
	foreach ($pieces as $piece) {
		$pos1 = strpos($piece, 'ru/hub')+7;
		$len = strpos($piece, '/', $pos1) - $pos1;
		array_push($hub_nodes, substr($piece, $pos1, $len));
	};

	if (sizeof($hub_nodes) == 40){
		$hub_nodes = array_slice($hub_nodes, 0, 5);
	};
	
	foreach ($hub_nodes as $hub_name) {
		if (!file_exists($dirname.'/'.$hub_name)) { 
			mkdir($dirname.'/'.$hub_name, 0777, true);
			$hub_page = file_get_contents('http://habrahabr.ru/hub/'.$hub_name.'/');
			$f = fopen($dirname.'/'.$hub_name.'/data.html', 'w');
			fwrite($f, $hub_page);
			fclose($f);
		}; 	
	};

	return implode('<br />', $hub_nodes);

};

function main(){
	global $hub_list, $out, $dirname;
	
	if (!file_exists($dirname)) { 
		mkdir($dirname, 0777, true);
	}; 	
	
	$dirList = scandir($dirname);
	
	if (sizeof($dirList) == 2){
		$out = initialize($dirname);
		$hub_list = 'The list is empty.<br /><br /> Initializing... <br />'.$out;
	} else {
		$hub_list = "<hr />";
		foreach ($dirList as $value) {
			if (($value !== '.') and ($value !== '..')){
				$link = 'Data for <a href="hubs/'.$value.'/data.html"> '.$value.'</a><br />';
				$hub_list = $hub_list.$link."\n";
			};
		};
	};
};

main();
?>
</head>

<body>

<h2>Welcome to the Habr Analyst Tool</h2>

<h3>The hub list</h3>

<?=$hub_list?>

</body>

</html>
