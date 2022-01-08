<?php

require "/path/to/carousel.inc";

$carousel = new Carousel;
	$carousel->target = "albumCarousel";

for ($i = 1; $i <= 3; $i++):

	$image = new Image("//placehold.it/", "800x300", "[Photo]");
	$carousel->new_slide("Slide Label", "Slide Content", $image); unset($image);
	$carousel->save_slide();

endfor; unset($i);

?>