<?php

require "/path/to/slide.inc";

class Carousel {

	public $slides = [];

	public function new_slide($label, $content, $image, $alt = null) {
	
		$slide = new CarouselSlide($label, $content, $image, $alt);

		$this->tmp = $slide;
		return $slide;
	
	}

	public function save_slide($target = null) {
	
		if (!is_null($target)): $this->slides[$target] = $this->tmp;
		else: array_push($this->slides, $this->tmp); endif;
	
	}

}

?>