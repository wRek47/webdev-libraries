<div class="container-fluid">
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
</div>