<?php

require "/path/to/canvas.inc";

class Gallery {

	public $wall = [];

	public function new_canvas($title, $description, $image, $alt = null) {
	
		$canvas = new GalleryCanvas($title, $description, $image, $alt);

		$this->tmp = $canvas;
		return $canvas;
	
	}

	public function save_canvas($target = null) {
	
		if (!is_null($target)):
		
			$this->wall[$target] = $this->tmp;
		
		else:
		
			array_push($this->wall, $this->tmp);
		
		endif;
	
	}

}

?>