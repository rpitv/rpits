<?

$gravities = array("west" => imagick::GRAVITY_WEST, "center" => imagick::GRAVITY_CENTER, "east" => imagick::GRAVITY_EAST);
$fonts = array("fontN" => "fonts/GothamNarrow-Bold.otf", "font" => "fonts/Gotham-Bold.ttf", "fontX" => "fonts/GothamXNarrow-Bold.otf");

function slantRectangle(&$canvas, $o) {
	$background = "#FFFFFF";
	//$ltr = '#000000';
	$ltr = '#888';
	$bd = '#A6A6A6';
	$iglow = '#404040';

	try {

		$gradient1 = new Imagick();
		$gradient1->newPseudoImage($o['h'], $o['w'] * 3 / 8, "gradient:white-$ltr");
		$gradient1->rotateImage(new ImagickPixel(), 270);
		$gradient2 = new Imagick();
		$gradient2->newPseudoImage($o['h'], $o['w'] * 3 / 8, "gradient:$ltr-white");
		$gradient2->rotateImage(new ImagickPixel(), 270);

		$lefttoright = new Imagick();
		$lefttoright->newPseudoImage($o['w'], $o['h'], "xc:$ltr");
		$lefttoright->compositeImage($gradient1, imagick::COMPOSITE_OVER, 0, 0);
		$lefttoright->compositeImage($gradient2, imagick::COMPOSITE_OVER, $o['w'] * 5 / 8, 0);

		$whiteup = new Imagick();
		$whiteup->newPseudoImage($o['w'], $o['h'] / 2, "gradient:black-#888");

		$gradient1 = new Imagick();
		$gradient1->newPseudoImage($o['w'], $o['h'] / 7, "gradient:$bd-white");
		$gradient2 = new Imagick();
		$gradient2->newPseudoImage($o['w'], $o['h'] / 7, "gradient:white-$bd");

		$bottomdark = new Imagick();
		$bottomdark->newPseudoImage($o['w'], $o['h'], "xc:white");
		$bottomdark->compositeImage($gradient1, imagick::COMPOSITE_OVER, 0, 0);
		$bottomdark->compositeImage($gradient2, imagick::COMPOSITE_OVER, 0, ($o['h'] / 2) - ($o['h'] / 7));

		$background = fillRectangle($o['w'], $o['h'], $o['color']);

		$background->compositeImage($lefttoright, imagick::COMPOSITE_MULTIPLY, 0, 0);

		// experimental
		if ($o['image']) {
			$logo = new Imagick();
			$logo->readImage(realpath($o['image']));
			$logo->resizeImage($o['h'], $o['h'], imagick::FILTER_TRIANGLE, 1);
			$background->compositeImage($logo, imagick::COMPOSITE_OVER, $o['w'] - $o['h'] - ($o['h'] / 3), 0);
		}
		$background->compositeImage($whiteup, imagick::COMPOSITE_SCREEN, 0, 0);
		$background->compositeImage($bottomdark, imagick::COMPOSITE_MULTIPLY, 0, $o['h'] / 2);

		$slantleft = new Imagick();
		$slantleft->newPseudoImage($o['h'] * sqrt(5) / 2, 8, "gradient:$iglow-white");
		$slantleft->rotateImage("none", 296.6);
		$slantright = new Imagick();
		$slantright->newPseudoImage($o['h'] * sqrt(5) / 2, 8, "gradient:$iglow-white");
		$slantright->rotateImage("none", 117.2);

		$top = new Imagick();
		$top->newPseudoImage($o['w'], 8, "gradient:$iglow-white");
		$bottom = new Imagick();
		$bottom->newPseudoImage($o['w'], 8, "gradient:white-$iglow");

		$slants = new Imagick();
		$slants->newPseudoImage($o['w'], $o['h'], "xc:white");
		$slants->compositeImage($slantleft, imagick::COMPOSITE_OVER, -1, 0);
		$slants->compositeImage($slantright, imagick::COMPOSITE_OVER, $o['w'] - ($o['h'] / 2) - 9, 0);
		$slants->compositeImage($top, imagick::COMPOSITE_MULTIPLY, 0, 0);
		$slants->compositeImage($bottom, imagick::COMPOSITE_MULTIPLY, 0, $o['h'] - 8);

		$background->compositeImage($slants, imagick::COMPOSITE_MULTIPLY, 0, 00);

		$draw1 = new ImagickDraw();
		$draw1->pushPattern('gradient', 0, 0, $o['w'], $o['h']);
		$draw1->composite(Imagick::COMPOSITE_OVER, 0, 0, $o['w'], $o['h'], $background);
		$draw1->popPattern();
		$draw1->setFillPatternURL('#gradient');
		$draw1->polygon(array(array('x' => 00, 'y' => $o['h'] - 1), array('x' => ($o['h'] / 2) - 1, 'y' => 00), array('x' => $o['w'] - 1, 'y' => 00), array('x' => $o['w'] - ($o['h'] / 2) - 1, 'y' => $o['h'] - 1)));

		$points = array(array('x' => 0, 'y' => $o['h'] - 1), array('x' => ($o['h'] / 2) - 1, 'y' => 00), array('x' => $o['w'] - 1, 'y' => 00), array('x' => $o['w'] - ($o['h'] / 2) - 1, 'y' => $o['h'] - 1));

		for ($i = 0; $i < 4; $i++) {
			$points[$i]['x']+=10;
			$points[$i]['y']+=10;
		}

		$shadow = new Imagick();
		$shadow->newPseudoImage($o['w'] + 20, $o['h'] + 20, "xc:none");
		$draws = new ImagickDraw();
		$draws->setFillColor("black");
		$draws->polygon($points);
		$shadow->drawImage($draws);
		$shadow->blurImage(0, 4, imagick::CHANNEL_ALPHA);

		$im = new Imagick();
		$im->newPseudoImage($o['w'], $o['h'], "xc:none");

		$im->drawImage($draw1);

		$im2 = new Imagick();
		$im2->newPseudoImage($o['w'] + 50, $o['h'] + 50, "xc:none");
		$im2->compositeImage($shadow, imagick::COMPOSITE_OVER, 5, 5);
		$draw1 = new ImagickDraw();
		$draw1->setStrokeWidth(6);
		$draw1->setStrokeColor("black");

		$draw1->polygon($points);
		$draw1->setStrokeWidth(2);
		$draw1->setStrokeColor("white");
		$draw1->polygon($points);
		$im2->drawImage($draw1);

		$im2->compositeImage($im, imagick::COMPOSITE_OVER, 10, 10);



		$canvas->compositeImage($im2, imagick::COMPOSITE_OVER, $o['x'] - 10, $o['y'] - 10);
	} catch (Exception $e) {
		echo 'Error: ', $e->getMessage(), "";
	}
}

function fillRectangle($w,$h,$color) {
	if(preg_match('/linear-gradient\((.+)\)/', $color,$matches)) {
		$groups = explode(',',$matches[1]);
		$direction = explode(' ',$groups[0]);
		$stops = [];

		$startIndex = 1;

		$firstGroup = explode(' ',$groups[0]);
		if($firstGroup[0] != 'to') {
			$startIndex = 0;
			$direction = ['to','bottom'];
		}

		for($i = $startIndex; $i < count($groups); $i++) {
			$stop = explode(' ',$groups[$i]);
			$dist = $stop[1];
			if(!$dist) {
				if($i == $startIndex) {
					$dist = '0%';
				} else if ($i+1 == count($groups)) {
					$dist = '100%';
				} else {
					die('FATAL ERROR: middle stops must have a %');
				}
			}
			$stops[] = array('color'=>$stop[0],'stop'=>$dist);
		}
		assert(count($stops) > 2);
		assert(count($direction) == 2);

		if($stops[0]['stop'] != '0px' && $stops[0]['stop'] != '0%') {
			array_unshift($stops,array('color'=>$stops[0]['color'],'stop'=>'0%'));
		}
		if($stops[count($stops)-1]['stop'] != '100%') {
			array_push($stops,array('color'=>$stops[count($stops)-1]['color'],'stop'=>'100%'));
		}
		$x = $w;
		$y = $h;
		if($direction[1] == 'left' || $direction[1] == 'right') {
			$x = $h;
			$y = $w;
		}
		$result = new Imagick();
		$result->newPseudoImage($x, $y, "xc:none");
		for($i = 0; $i < count($stops)-1; $i++) {
			$pctHeight = intval($stops[$i+1]['stop']) - intval($stops[$i]['stop']);
			$height = ceil($y*($pctHeight)/100);
			assert ($height > 0);
			$step = new Imagick();
			//echo $width . ' ' . "gradient:".$stops[$i]['color'].'-'.$stops[$i+1]['color'] . ' ' . ceil($x*intval($stops[$i]['stop'])/100) . "<br>";
			$step->newPseudoImage($x,$height,"gradient:".$stops[$i]['color'].'-'.$stops[$i+1]['color']);
			$result->compositeImage($step, Imagick::COMPOSITE_OVER, 0,ceil($y*intval($stops[$i]['stop'])/100));
		}
		$rotationList = array('left'=>90,'right'=>270,'top'=>180,'bottom'=>0);
		$result->rotateImage(new ImagickPixel(), $rotationList[$direction[1]]);
		return $result;
	} else {
		$result = new Imagick();
		$result->newPseudoImage($w, $h, "xc:$color");
		return $result;
	}
	
}

function blackBox(&$canvas, $o) {

	$rectangle = new Imagick();
	$rectangle->newPseudoImage($o['w'], $o['h'], "xc:none");
	$draw1 = new ImagickDraw();
	$draw1->pushPattern('gradient', 0, 0, 5, 5);
	$tile = new Imagick();
	$tile->readImage(realpath("assets/diag_tile.png"));
	$draw1->composite(Imagick::COMPOSITE_OVER, 0, 0, 5, 5, $tile);
	$draw1->popPattern();
	$draw1->setFillPatternURL('#gradient');
	$draw1->rectangle(0, 0, $o['w'], $o['h']);
	$rectangle->drawImage($draw1);

	$gradient = new Imagick();
	$gradient->newPseudoImage($o['w'], $o['h'], "gradient:#DDD-#666");

	$rectangle->compositeImage($gradient, Imagick::COMPOSITE_COPYOPACITY, 0, 0);

	$black = new Imagick();
	$black->newPseudoImage($o['w'], $o['h'], "xc:black");

	$layered = new Imagick();
	$layered->newPseudoImage($o['w'] + 20, $o['h'] + 20, "xc:none");
	$layered->compositeImage($black, Imagick::COMPOSITE_OVER, 5, 0);
	$layered->compositeImage($black, Imagick::COMPOSITE_OVER, 5, 5);
	$layered->compositeImage($gradient, Imagick::COMPOSITE_COPYOPACITY, 5, 5);
	$layered->blurImage(4, 5, imagick::CHANNEL_ALPHA);
	$layered->compositeImage($black, Imagick::COMPOSITE_DSTOUT, 0, 0);

	$canvas->compositeImage($layered, Imagick::COMPOSITE_OVER, $o['x'], $o['y']);
	$canvas->compositeImage($rectangle, Imagick::COMPOSITE_OVER, $o['x'], $o['y']);
}

function defaultText($o) {

	if($o['case'] == 'upper') {
		$o['text'] = strtoupper($o['text']);
	}

	if($o['text'] == '') {
		$o['text'] = ' ';
	}

	global $gravities, $fonts;
	$text = new Imagick();
	$text->setFont($fonts[$o['font']]);
	$text->setBackgroundColor("none");
	$text->setGravity($gravities[$o['gravity']]);
	if ($o['wordWrap']) {
		$text->newPseudoImage($o['w'], $o['h'], "caption:" . $o['text']);
	} else {
		$text->newPseudoImage($o['w'], $o['h'], "label:" . $o['text']);
	}
	//$metrics = $text->queryFontMetrics( $annotate, $text );

	return $text;
}

function plainText(&$canvas, $o) {
	$text = defaultText($o);
	$shadow = $text->clone();
	$shadow->blurImage(4, 2, imagick::CHANNEL_ALPHA);
	$text->colorizeImage($o['color'], 1);

	$canvas->compositeImage($shadow, imagick::COMPOSITE_OVER, $o['x'], $o['y']);
	$canvas->compositeImage($text, imagick::COMPOSITE_OVER, $o['x'], $o['y']);
}

function getTextWidth($o) {
	$text = defaultText($o);
	$text->trimImage(0);
	$geo = $text->getImageGeometry();
	return $geo["width"];
}

function shadowText(&$canvas, $o) {
	$text = defaultText($o);
	$shadow = $text->clone();
	$stroke = $text->clone();
	$shadow->blurImage(4, 5, imagick::CHANNEL_ALPHA);
	$text->colorizeImage($o['color'], 1);

	$canvas->compositeImage($shadow, imagick::COMPOSITE_OVER, $o['x'] + 5, $o['y'] + 5);
	$canvas->compositeImage($shadow, imagick::COMPOSITE_OVER, $o['x'], $o['y']);
	$canvas->compositeImage($text, imagick::COMPOSITE_OVER, $o['x'], $o['y']);
}

function placeImage(&$canvas, $o) {
	if(!file_exists($o['path'])) {
		return;
	}
	try {
		$logo = new Imagick();
		$logo->readImage(realpath($o['path']));

		// This might cause problems with non-Player Portraits if aspect ratios are off.
		$size = @getimagesize($o['path']);
		$logo->cropImage($size[0], $size[0] * 1.2, 0, 0);

		$logo->resizeImage($o['w'], $o['h'], imagick::FILTER_TRIANGLE, 1);
		$canvas->compositeImage($logo, imagick::COMPOSITE_OVER, $o['x'], $o['y']);
	} catch (Exception $e) {
		echo 'Error: ', $e->getMessage(), "";
	}
}

?>
