<?

include_once('Geo.php');
include_once('Primitives.php');
//include_once('Generator.php');

// Auto load geos/generators as they are needed
function __autoload($className) {
	$directories = ['','generators/'];
	foreach($directories as $dir) {
		echo 'path: ' . $dir . $className . '.php';
		if(file_exists($dir . $className . '.php')) {
			include_once($dir . $className . '.php');
			return true;
		}
	}
	die('Could not load class file named ' . $className . '.php');
}

function fillRectangle($w,$h,$color) {
	if (preg_match('/linear-gradient\((.+)\)/', $color,$matches)) {
		$commaRemoved = preg_replace("/rgb\(\s?([0-9]+),\s?([0-9]+),\s?([0-9]+)\)/", "rgb($1|$2|$3)", $matches[1]);
		$groups = explode(',',$commaRemoved);
		$direction = explode(' ',trim($groups[0]));
		$stops = [];

		$startIndex = 1;

		$firstGroup = explode(' ',trim($groups[0]));
		if ($firstGroup[0] != 'to') {
			$startIndex = 0;
			$direction = ['to','bottom'];
		}

		for ($i = $startIndex; $i < count($groups); $i++) {
			$stop = explode(' ',trim($groups[$i]));
			$dist = $stop[1];
			if (!$dist) {
				if ($i == $startIndex) {
					$dist = '0%';
				} else if ($i+1 == count($groups)) {
					$dist = '100%';
				} else {
					die('FATAL ERROR: middle stops must have a %');
				}
			}
			if (strpos($stop[0],'texture') !== false) {
				// the technology isn't there yet
				$stop[0] = 'grey';
			}
			$stops[] = array('color'=> str_replace('|',',',$stop[0]),'stop'=>$dist);
		}
		assert(count($stops) >= 2);
		assert(count($direction) == 2);

		if ($stops[0]['stop'] != '0px' && $stops[0]['stop'] != '0%') {
			array_unshift($stops,array('color'=>$stops[0]['color'],'stop'=>'0%'));
		}
		if ($stops[count($stops)-1]['stop'] != '100%') {
			array_push($stops,array('color'=>$stops[count($stops)-1]['color'],'stop'=>'100%'));
		}
		$x = $w;
		$y = $h;
		if ($direction[1] == 'left' || $direction[1] == 'right') {
			$x = $h;
			$y = $w;
		}
		$result = new Imagick();
		$result->newPseudoImage($x, $y, "xc:none");
		for ($i = 0; $i < count($stops)-1; $i++) {
			$pctHeight = intval($stops[$i+1]['stop']) - intval($stops[$i]['stop']);
			$height = ceil($y*($pctHeight)/100);
			assert ($height > 0);
			$step = new Imagick();
			$step->newPseudoImage($x,$height,"gradient:".$stops[$i]['color'].'-'.$stops[$i+1]['color']);
			$result->compositeImage($step, Imagick::COMPOSITE_OVER, 0,ceil($y*intval($stops[$i]['stop'])/100));
		}
		$rotationList = array('left'=>90,'right'=>270,'top'=>180,'bottom'=>0);
		$result->rotateImage(new ImagickPixel(), $rotationList[$direction[1]]);
		return $result;
	} else if (preg_match('/texture\((.+)\)/', $color,$matches)) {
		$result = new Imagick();
		$result->readImage(realpath($matches[1]));
		$result->cropimage($w,$h, 0, 0);
		return $result;
	} else {
		$result = new Imagick();
		$result->newPseudoImage($w, $h, "xc:$color");
		return $result;
	}
	
}

function defaultText($o) {

	$gravities = [
		"west" => imagick::GRAVITY_WEST,
		"center" => imagick::GRAVITY_CENTER,
		"east" => imagick::GRAVITY_EAST
	];
	$fonts = [
		"fontN" => "fonts/GothamNarrow-Bold.otf",
		"font" => "fonts/Gotham-Bold.ttf",
		"fontX" => "fonts/GothamXNarrow-Bold.otf"
	];

	if ($o['case'] == 'upper') {
		$o['text'] = strtoupper($o['text']);
	}

	if ($o['text'] == '') {
		$o['text'] = ' ';
	}
	
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

function getTextWidth($o) {
	$text = defaultText($o);
	$text->trimImage(0);
	$geo = $text->getImageGeometry();
	return $geo["width"];
}

?>
