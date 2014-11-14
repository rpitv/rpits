<?php

class Geo {

	function __construct($attributes) {
		$this->attributes = array_merge($this->attributes, $this->defaults, $attributes);
	}

	protected $attributes = [
			'x' => 0,
			'y' => 0,
			'w' => 100,
			'h' => 100,
			'name' => ''
	];

	protected $defaults = [];

	protected $nonEditable = ['name','x','y','w','h'];

	public function draw(&$canvas, $o, $bustCache) {
		// overriden
	}

	public function addToCanvas(&$canvas,$bustCache = false) {
		$img = $this->getFromCache();
		if(!$img || $bustCache) {
			$img = $this->render($bustCache);
			$this->saveToCache($img);
		}
		$canvas->compositeImage($img, imagick::COMPOSITE_OVER, $this->attributes['x']-10, $this->attributes['y']-10);
	}

	protected function getFromCache() {
		$path = realpath('cache') . "/" . $this->getHash() . ".tga";
		if (file_exists($path)) {
			$img = new Imagick($path);
			$img->setImageDepth(8);
			$img->setimagecolorspace(imagick::COLORSPACE_SRGB);
			return $img;
		} else {
			return false;
		}
	}

	protected function saveToCache($img) {
		$img->writeImage(realpath("cache") . "/" . $this->getHash() . '.tga') or die ('Error writing Geo to cache');
	}

	protected function render($bustCache = false) {
		$x = 10; $y = 10; $w = 20; $h = 20;
		$canvas = new Imagick();
		$canvas->newImage($this->attributes["w"] + $x + $w, $this->attributes["h"] + $y + $h, "none", 'tga');
		$canvas->setImageDepth(8);
		$canvas->setimagecolorspace(imagick::COLORSPACE_SRGB);

		$args = $this->attributes;
		$args['x'] = $x;
		$args['y'] = $y;
		$this->draw($canvas, $args, $bustCache);

		return $canvas;
	}

	public function getHash() {
		$attributes = $this->attributes;
		$ignore = ['x','y','name','order'];
		foreach($ignore as $i) {
			unset($attributes[$i]);
		}
		return $this->attributes['type'] . '_' . hash('md4',json_encode($attributes));
	}

	public function toJSON() {
		return json_encode($this->attributes);
	}

	public function getEditableAttributes() {
		return array_diff($this->attrobites,$this->nonEditable);
	}

}
