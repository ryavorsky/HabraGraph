<html>
<head>
	<title>
		HabraGraph - the graph
	</title>
</head>
<body>

<?php
	
	$data_file_name = '/home/virtwww/w_rost_8baa4f5f/http/post.txt';
	$data = file_get_contents($data_file_name);
	
	// First, get the post author name
	$pos1 = strpos($data, '<div class="author">');
	$line = substr($data, $pos1+20, 200);
	preg_match("/http:\/\/habrahabr.ru\/users\/(\S+)\/\"/", $line, $match);
	$author = $match[1];
	$res = '<br />Author: '.$author;

	// Extract the comments tree
	$tree = "<br />Tree:<hr />digraph G {<br/>";
	preg_match_all("/show_tree\" data\-id=\"(\w+)\" data\-parent_id=\"(\w+)\"/", $data, $matches, PREG_SET_ORDER);
	foreach ($matches as $item) {
		$com_author = $item[1].' -> '.$item[2].';';
		$tree = $tree.$com_author.' ';
	};
	$tree = $tree.'}<br /><br /><br />';
	
	$graph = str_replace('Tree', 'Graph', $tree);

	// Get comments authors
	$res = $res."<br />Users:<hr />";
	preg_match_all("/class=\"username\">(\w+)<([^#]+)#(\w+)/", $data, $matches, PREG_SET_ORDER);
	foreach ($matches as $item) {
		$com_id = str_replace('comment_','',$item[3]);
		$com_author = $item[1].' - '.$com_id;
		$res = $res.$com_author.' <br />';
		$graph = str_replace($com_id, $item[1], $graph);
	};
	
	$res = $res.$tree.'<br/> '.$graph;
	
?>

<h2>The post graph</h2> 
<hr/> <?=$res?>

</body>
</html>