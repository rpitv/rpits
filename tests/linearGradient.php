<style>
	div {
		margin: 2px;
		display: inline-block;
	}
	p {
		margin-bottom: 0px;
	}
</style>
<pre>
<?

$includePath = '../';
include($includePath . 'include.php');

$w = $h = 200;

$gradients = ['linear-gradient(red,white)',
'linear-gradient(to right, red, white)',
'linear-gradient(to right, #FF0000, rgb(255,255,255))',
'linear-gradient(to right, red, black 50%, white)',
'linear-gradient(to right, red 10%, black 50%, white)',
'linear-gradient(to right, red 10%, black 50%, white 90%)'];

foreach($gradients as $gradient) {
	echo '<p>' . $gradient . '</p>';
	echo '<div style="background:' . $gradient . ';width:'.$w.'px;height:'.$h.'px;">CSS</div>';

	$canvas = new Imagick();
	$canvas->newImage($w, $h, "none", 'png');
	$canvas->setImageDepth(8);
	$canvas->setimagecolorspace(imagick::COLORSPACE_SRGB);

	$result = fillRectangle($w,$h,$gradient);
	$canvas->compositeImage($result,Imagick::COMPOSITE_OVER,0,0);

	echo '<div style="background:url(data:image/gif;base64,' . base64_encode($canvas) . ');width:'.$w.'px;height:'.$h.'px;">Imagick</div>';
}