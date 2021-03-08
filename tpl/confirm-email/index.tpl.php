<?php

	/* @var Stoic\Web\PageHelper $page */

?>
<?php $this->layout('shared::auth-master', ['page' => $page]); ?>

		<form class="form-register">
			<img class="mb-4" src="<?=$page->getAssetPath('~/assets/img/bootstrap-solid.svg')?>" alt="" width="72" height="72">

			<div class="mt-3 text-muted text-center">
				You're all set!  Go login, and thanks for registering on WarBanner.
			</div>

			<div class="mt-5 text-center">
				<button type="button" class="btn btn-outline-dark" onclick="location.href = '<?=$page->getAssetPath('~/index.php')?>';">Back to Login</button>
			</div>
		</form>