<?php

class Image {

	public function __construct($folder, $file, $alt = "") {
	
		$this->folder = $folder;
		$this->file = $file;
		$this->alt = $alt;
		$this->src = $folder . $file;
	
	}

}

?>