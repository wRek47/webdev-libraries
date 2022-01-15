<?php

$carousel = new Carousel;
	$carousel->target = "albumCarousel";

for ($i = 1; $i <= 3; $i++):

	$image = new Image("//placehold.it/", "800x300", "[Photo]");
	$carousel->new_slide("Slide Label", "Slide Content", $image); unset($image);
	$carousel->save_slide();

endfor; unset($i);

$gallery = new Gallery;

for ($i = 1; $i <= 6; $i++):

	$image = new Image("//placehold.it/", "300x250", "[Photo]");

	$gallery->new_canvas("Title", "Description", $image); unset($image);
	$gallery->save_canvas("photo_{$i}");

endfor; unset($i);

?>
<div class="container-fluid">
	<section id="<?= $carousel->target; ?>" class="carousel slide" data-bs-ride="carousel">
		<div class="carousel-indicators">
<? for ($i = 0; $i < count($carousel->slides); $i++): ?>
<? $class = ($i == 0) ? ' class="active"' : ""; ?>
			<button type="button" data-bs-target="#<?= $carousel->target; ?>" data-bs-slide-to="<?= $i; ?>"<?= $class; ?>></button>
<? endfor; unset($i); ?>
		</div>
		
		<div class="carousel-inner">
<? foreach ($carousel->slides as $id => $slide): ?>
<? $active = ($id == 0) ? " active" : ""; ?>
			<figure class="carousel-item<?= $active; ?>">
				<img class="d-block w-100" src="<?= $slide->image->src; ?>" alt="<?= $slide->image->alt; ?>" />

				<figcaption class="carousel-caption d-none d-md-block">
					<h5><?= $slide->label; ?></h5>
					<p><?= $slide->content; ?></p>
				</figcaption>
			</figure>
<? endforeach; unset($id, $photo); ?>
		</div>
		
		<button class="carousel-control-prev" type="button" data-bs-target="#<?= $carousel->target; ?>" data-bs-slide="prev">
			<span class="carousel-control-prev-icon"></span>
		</button>
		
		<button class="carousel-control-next" type="button" data-bs-target="#<?= $carousel->target; ?>" data-bs-slide="next">
			<span class="carousel-control-next-icon"></span>
		</button>
	</section>
	
	<section class="pb-3">
		<div class="row row-cols-1 row-cols-md-3 g-3">
<? foreach ($gallery->wall as $id => $canvas): ?>
			<aside class="col">
	<article class="card">
		<figure class="mb-0">
			<a href="#card_<?= $id; ?>" data-bs-toggle="modal" data-bs-target="#card_<?= $id; ?>">
				<img class="w-100" src="<?= $canvas->image->src; ?>" alt="<?= $canvas->image->alt; ?>" />
			</a>
		</figure>
		
		<div class="card-body">
			<p class="mb-0"><?= $canvas->title; ?></p>
			
			<nav class="mt-3 small text-muted">
				<span class="pull-right">Added <? // $canvas->elapsed_time; ?> ago</span>
				<a class="btn btn-pill border btn-sm" href="#" data-bs-toggle="modal" data-bs-target="#card_<?= $id; ?>">View</a>
			</nav>
		</div>
	</article>
	
	<div id="card_<?= $id; ?>" class="modal">
		<div class="modal-dialog modal-xl">
			<div class="modal-content">
				<header class="modal-header bg-light">
					<h5 class="modal-title"><?= $canvas->title; ?></h5>
					<button type="button" class="btn-close" data-bs-dismiss="modal"></button>
				</header>
				
				<div class="modal-body">
				</div>
			</div>
		</div>
	</div>
			</aside>
<? endforeach; unset($canvas); ?>
		</div>
	</section>
</div><?php

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

class Image {

	public function __construct($folder, $file, $alt = "") {
	
		$this->folder = $folder;
		$this->file = $file;
		$this->alt = $alt;
		$this->src = $folder . $file;
	
	}

}

?>