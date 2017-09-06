<?php
	//Include router file
	include "core/Router.php";
	//BaseUrl
	$baseUrl = "";
	//some handles
	function home() {
		echo "Home page";
	}
	function get($id) {
		echo "Get: ".$id;
	}
	function news($name) {
		echo "Name: ".$name;
	}
	function short($int) {
		echo $int;
	}
	$routs = [
		[
			"uri" => "/",
			"handle" => "home"
		],
		[
			"uri" => "/get/{{temp}}",
			"handle" => function($temp)
			{
				echo $temp;
			}
		],
		[
			"uri" => "/nice",
			"handle" => function()
						{
                            echo 'Hi';
						}
		],
		[
			"uri" => "/{{temp}}",
			"handle" => function($temp)
						{
                            echo $temp;
						}
		]
	];
	$router = new Router($routs, $baseUrl);
?>