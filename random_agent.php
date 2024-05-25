<?php
$google301_txt=file("../google301.txt");
$image_link_txt=file("../image_link.txt");
$state_txt=file("../state.txt");

$url = 'https://'.$_SERVER['SERVER_NAME'];
$uri = $_SERVER['SCRIPT_URI'];
//echo $uri;
$subfolder = $_SERVER['REQUEST_URI'];
$subfolder = trim(str_replace('/','',$subfolder));
$subfolder = ucwords(str_replace('-',' ',$subfolder));
$subfolder = trim($subfolder);
$image_link_array = explode('<br>',$image_link_txt[0]);
$relate_array = array(24,28,32,36,40,44,48);
$related_loop = mt_rand(0, 6);
shuffle($state_txt);
//print_r ($image_link_array);
//print_r ($state_txt);

for ($a=0;$a<$relate_array[$related_loop];$a++)
		{
    $state_txt[$a] = trim($state_txt[$a]);
    $link_image = $url.'/images/'.$image_link_array[$a];
    $rand_link_google301 = rand(0,count($google301_txt)-1);
    $redirect_google301 = trim($google301_txt[$rand_link_google301]);
    $subfolder_kw = ucwords(str_replace(' ','-',$subfolder));
    //$redirect_google301_url = $redirect_google301.$url;
    $state_kw = ucwords(str_replace(' ','-',$state_txt[$a]));
    //$redirect_google301_url = $url.'/'.$subfolder_kw.'/#'.$subfolder_kw.'-in-'.$state_kw;
    $redirect_google301_url = '#'.$subfolder_kw.'-in-'.$state_kw;
    //echo $link_image.'<br>'.$redirect_google301_url.'<br>'.$subfolder.' in '.$state_txt[$a].'<br>';
    
    
$related_content.='					<div class="col mb-5">'. "\n";
$related_content.='                        <div class="card h-100">'. "\n";
$related_content.='                            <!-- Product image-->'. "\n";
$related_content.='                            <a href="'.$redirect_google301_url.'" alt="'.$subfolder.' in '.$state_txt[$a].'"><img class="card-img-top" src="'.$link_image.'" alt="'.$subfolder.' in '.$state_txt[$a].'" /></a>'. "\n";
$related_content.='                           <!-- Product details-->'. "\n";
$related_content.='                           <div class="card-body p-4">'. "\n";
$related_content.='                                <div class="text-center">'. "\n";
$related_content.='                                    <!-- Product name-->'. "\n";
$related_content.='                                    <a href="'.$redirect_google301_url.'" alt="'.$subfolder.' in '.$state_txt[$a].'"><h5 class="fw-bolder">'.$subfolder.' in '.$state_txt[$a].'</h5></a>'. "\n";
$related_content.='                                </div>'. "\n";
$related_content.='                            </div>'. "\n";
$related_content.='                            <!-- Product actions-->'. "\n";
$related_content.='                            <div class="card-footer p-4 pt-0 border-top-0 bg-transparent">'. "\n";
$related_content.='                                <div class="text-center"><a class="btn btn-outline-dark mt-auto" href="'.$redirect_google301_url.'">'.$subfolder.' in '.$state_txt[$a].'</a></div>'. "\n";
$related_content.='                            </div>'. "\n";
$related_content.='                        </div>'. "\n";
$related_content.='                    </div>'. "\n";


}
$related_content_header.='<div class="container px-4 px-lg-5 mt-5">
                <div class="row gx-4 gx-lg-5 row-cols-2 row-cols-md-3 row-cols-xl-4 justify-content-center">'. "\n";

$related_content_footer.='</div>
                                </div>'. "\n";


$related_content_full = $related_content_header.$related_content.$related_content_footer;
echo $related_content_full;

?>
