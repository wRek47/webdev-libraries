<?php

class GalleryCanvas {

	public function __construct($title, $description, $image, $alt = null) {
	
		if (!is_null($alt)): $image->alt = $alt; endif;

		$this->set_canvas_title($title);
		$this->set_canvas_description($description);

		$this->set_canvas_image($image);
	
	}

	public function set_canvas_title($text) { $this->title = $text; }
	public function set_canvas_description($text) { $this->description = $text; }
	public function set_canvas_image($image) { $this->image = $image; }

}

?>