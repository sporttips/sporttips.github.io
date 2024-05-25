<?php
include 'langen.php';

$langGen = new LangGen();
$langGen->learn('data/1.txt');
$article1=$langGen->generate();
//echo str_replace(".",".<br />",$article1);
//echo "<br />###################################################<br />";
$kwstyle= array (0 => '<u>##KEYWORD##</u>', 1 => '<strong>##KEYWORD##</strong>', 2 => '<b>##KEYWORD##</b>', 3 => '<i>##KEYWORD##</i>', 4 => '<em>##KEYWORD##</em>');
$sentences=explode(".",$article1);
$numsentences=count($sentences);
for($h=0;$h<$numsentences;$h++)
{
	$numwords=count(explode(" ",$sentences[$h]));
	if($numwords<7)
		{
		$sentences[$h]="";
		//echo "$numwords replace $h | ";
		}
	else $sentences[$h]=ucfirst(trim($sentences[$h]));
}
//echo "<br />";
$article1=implode(".<br />",array_filter($sentences));
//echo $article1;
//echo "<br />###################################################<br />";

$sentences=explode(".",$article1);
$keys=array_rand($sentences,5);
$replace=count($keys);
$content="";
for($i=0;$i<$replace;$i++) {
	$sentence=$sentences[$keys[$i]];
	$words=explode(" ",$sentence);
	$numwords=count($words);

	for($j=0;$j<$numwords;$j++) {
		if(strtolower($words[$j])=='is'||strtolower($words[$j])=='are'||strtolower($words[$j])=='an'||strtolower($words[$j])=='a'||strtolower($words[$j])=='or'||strtolower($words[$j])=='for'||strtolower($words[$j])=='from'||strtolower($words[$j])=='the'||strtolower($words[$j])=='but'||strtolower($words[$j])=='this'||strtolower($words[$j])=='that'||strtolower($words[$j])=='in'||strtolower($words[$j])=='on')
		{
		$myverb=$words[$j];
		$words[$j]=$myverb." ".$kwstyle[$j];
		break;
		}
		else {
		}
	}
	$sentences[$keys[$i]]=ucfirst(implode(" ",$words));		
}
//echo "<br />";
echo $content="<h4>##BUYING##</h4>".implode(". ",$sentences).".";
