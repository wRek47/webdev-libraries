<?php

class CarouselSlide {

	public function __construct($label, $content, $image, $alt = null) {
	
		if (!is_null($alt)): $image->alt = $alt; endif;

		$this->set_slide_label($label);
		$this->set_slide_content($content);

		$this->set_slide_image($image);
	
	}

	public function set_slide_label($text) { $this->label = $text; }
	public function set_slide_content($content) { $this->content = $content; }
	public function set_slide_image($image) { $this->image = $image; }

}

?>