<?php
define("CON_number",4);
define("CON_image_width",70);
define("CON_image_height",17);
define("CON_image_string","0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz");
header("Content-type:image/png");
session_start();
if ($_SESSION['num'])
{
unset($_SESSION['num']);
}
$im=imagecreate(CON_image_width,CON_image_height);
$black=imagecolorallocate($im,0,0,0);
$white=imagecolorallocate($im,255,255,255);
$gray=imagecolorallocate($im,200,200,200);
imagefill($im,80,30,$black);
for($i=0;$i<10;$i++)
{
$randcolor=imagecolorallocate($im,rand(100,255),rand(100,255),rand(100,255));
imagesetpixel($im, rand(0,CON_image_width),rand(0,CON_image_height),$randcolor);
}
for($i=0;$i<4;$i++)
{
imageline($im,rand(0,CON_image_width),rand(0,CON_image_height),rand(0,CON_image_width),rand(0,CON_image_height),$gray);//以随机坐标画线
}
for($i=0;$i<CON_number;$i++)
{
$authnum.=substr(CON_image_string,rand(0,61),1);
}
$_SESSION['num']=$authnum;
imagestring($im, 10,15, 0,$authnum,$white);
imagepng($im);
imagedestroy($im);
exit();
?>