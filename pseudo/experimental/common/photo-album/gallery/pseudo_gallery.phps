<?php

require "/path/to/gallery.inc";

$gallery = new Gallery;

for ($i = 1; $i <= 6; $i++):

	$image = new Image("//placehold.it/", "300x250", "[Photo]");

	$gallery->new_canvas("Title", "Description", $image); unset($image);
	$gallery->save_canvas("photo_{$i}");

endfor; unset($i);

?>