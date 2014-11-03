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

	// First, get the Hubs page and extract the list of most populat hubs
	if (!file_exists('habr.txt')) { 
		$habr_hubs_page = file_get_contents('http://habrahabr.ru/hubs/');
		$f = fopen('habr.txt', 'w');
		fwrite($f, $habr_hubs_page);
		fclose($f);
	} else {
		$habr_hubs_page = file_get_contents('habr.txt'); // the page is cashed locally to avoid repeated calls 
	}; 	

	$pieces = explode("hub ", $habr_hubs_page);
	unset($pieces[0]);
	$hub_nodes = array();
	
	foreach ($pieces as $piece) {
		$pos1 = strpos($piece, 'ru/hub')+7; 
		$len = strpos($piece, '/', $pos1) - $pos1;
		array_push($hub_nodes, substr($piece, $pos1, $len));
	};

	// finally the list of hubs is extracted
	if (sizeof($hub_nodes) == 40){
		$hub_nodes = array_slice($hub_nodes, 0, 20); // take top most hubs
	};
	
	
	// Now for each hub create sub-folder and retrieve the history into the data.txt file
	foreach ($hub_nodes as $hub_name) {
	
		// First, create the folder 
		if (!file_exists($dirname.'/'.$hub_name)) { 
			mkdir($dirname.'/'.$hub_name, 0777, true);

			// Then open the file to store the hub pages 
			$f = fopen($dirname.'/'.$hub_name.'/data.txt', 'a');
			$page = 1;
			
			do {
				// Construct the URL for the page
				if($page > 1){
					$url = 'http://habrahabr.ru/hub/'.$hub_name.'/page'.$page.'/';
					echo $url.'<br />';
					
				} else {
					$url = 'http://habrahabr.ru/hub/'.$hub_name.'/';
				};
				
				// Retrieve the data and save it to the file
				$hub_page =  file_get_contents($url);
				fwrite($f, $hub_page);
				
				sleep(2); // wait for some time before retrieving the next page
				$page = $page + 1;
				
			} while ((!$hub_page )or ($page<50));
			
			fclose($f);
			
			$update_url = "http://rost.1gb.ru/update.php?hub=".$hub_name;
			$tmp = file_get_contents($update_url);
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
				$link = 'List of posts <a href="hubs/'.$value.'/list.html"> '.$value.'</a>.';
				$link = $link.' Update the list for <a href="http://rost.1gb.ru/update.php?hub='.$value.'"> '.$value.'</a> hub.<br />';
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
