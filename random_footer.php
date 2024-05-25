<?php 

		
$urllists = file_get_contents('url-list-random.txt');
		$urllists_exp = explode("<br>", $urllists);


$count_urllists = count($urllists_exp);
		unset($urllists_exp[$count_urllists-1]);
		//print_r($urllists_exp);
		shuffle($urllists_exp);
		//echo $count_urllists;
		//print_r($urllists_exp);
		$rand_num = rand(20,50);
		$get_url = '';
		//echo $rand_num;
		for ($a=0;$a<$rand_num;$a++){
			$get_url.= $urllists_exp[$a].' | ';
			
		}
echo $get_url;



?>
