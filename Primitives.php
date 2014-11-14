<?php

class SlantRectangle extends Geo {

	protected $defaults = [
			// "required"
			"color" => "#FF0000", // fill color

			// "optional"
			"image" => false, // Image to embed behind the glass surface
	];

	public function draw(&$canvas, $o, $bustCache) {
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
}