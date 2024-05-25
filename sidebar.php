<?php
//$uri = trim($_SERVER['SCRIPT_URI']);
//echo $uri."\n";
$uri2 =  explode('public_html/',realpath('.'));
$uri3 = '/'.$uri2[1].'/';
$urllist_txt=file('../url-list-random.txt');
//print_r ($urllist_txt);

//echo $urllist_txt[0];
$sidebar_list = explode($uri3,$urllist_txt[0]);
//echo $sidebar_list[1];
$sidebar_list2 = explode('<br>',$sidebar_list[1]);
//print_r ($sidebar_list2);
echo '<h2>Related Posts</h2>';
echo "<ul>"."\n"."<li>".$sidebar_list2[1]."</li>"."\n"."<li>".$sidebar_list2[2]."</li>"."\n"."<li>".$sidebar_list2[3]."</li>"."\n"."<li>".$sidebar_list2[4]."</li>"."\n"."<li>".$sidebar_list2[5]."</li>"."\n"."<li>".$sidebar_list2[6]."</li>"."\n"."<li>".$sidebar_list2[7]."</li>"."\n"."<li>".$sidebar_list2[8]."</li>"."\n"."<li>".$sidebar_list2[9]."</li>"."\n"."<li>".$sidebar_list2[10]."</li>"."\n"."</ul>";
?>