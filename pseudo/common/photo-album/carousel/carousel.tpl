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
</div>