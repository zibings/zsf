<?php

	define('STOIC_CORE_PATH', '../');
	require(STOIC_CORE_PATH . 'inc/core.php');

	use League\Plates\Engine;

	use Stoic\Web\PageHelper;

	use Zibings\User;
	use Zibings\UserEvents;
	use Zibings\UserSession;
	use Zibings\UserToken;

	use function Zibings\getPhpMailer;
	use function Zibings\isAuthenticated;

	global $Db, $Log, $Settings, $Stoic, $Tpl;

	/**
	 * @var \Stoic\Pdo\PdoHelper $Db
	 * @var \Stoic\Log\Logger $Log
	 * @var \AndyM84\Config\ConfigContainer $Settings
	 * @var \Stoic\Web\Stoic $Stoic
	 * @var \League\Plates\Engine $Tpl
	 */

	$page = PageHelper::getPage('reset-password.php');
	$page->setTitle('Reset Password');

	if (isAuthenticated($Db)) {
		$page->redirectTo('~/home.php');
	}

	$message = "";
	$tplFile = "index";
	$get     = $Stoic->getRequest()->getGet();
	$post    = $Stoic->getRequest()->getPost();

	if ($get->has('token')) {
		$tok = explode(':', base64_decode($get->getString('token')));
		$ut  = UserToken::fromToken($tok[1], intval($tok[0]), $Db, $Log);

		if ($ut->userId > 0) {
			$tplFile = 'change';
		}
	}

	if ($post->hasAll('email')) {
		$user = User::fromEmail($post->getString('email'), $Db, $Log);

		if ($user->id > 0) {
			$ut          = new UserToken($Db, $Log);
			$ut->context = "PASSWORD RESET";
			$ut->token   = UserSession::generateGuid(false);
			$ut->userId  = $user->id;
			$create      = $ut->create();

			if ($create->isGood()) {
				$tpl = new Engine(null, 'tpl.php');
				$tpl->addFolder('shared', STOIC_CORE_PATH . '/tpl/shared');
				$tpl->addFolder('emails', STOIC_CORE_PATH . '/tpl/emails');

				$mail          = getPhpMailer($Settings);
				$mail->Subject = "[WarBanner] Password Reset Request";
				$mail->isHTML(true);
				$mail->Body    = $tpl->render('emails::reset-password', [
					'page'  => $page,
					'token' => base64_encode("{$ut->userId}:{$ut->token}")
				]);
				$mail->addAddress($post->getString('email'));

				$mail->send();

				$tplFile = 'sent';
			} else {
				$tplFile = 'error';
			}
		} else {
			$tplFile = 'error';
		}
	}

	if ($post->hasAll('token', 'password', 'confirmPassword')) {
		$tok    = explode(':', base64_decode($post->getString('token')));
		$ut     = UserToken::fromToken($tok[1], intval($tok[0]), $Db, $Log);
		$events = new UserEvents($Db, $Log);

		if ($ut->userId > 0) {
			$reset = $events->doResetPassword(new \Stoic\Utilities\ParameterHelper([
				'id'         => $ut->userId,
				'key'        => $post->getString('password'),
				'confirmKey' => $post->getString('confirmPassword')
			]));

			if ($reset->isGood()) {
				$tplFile = 'complete';
				$ut->delete();
			} else {
				$tplFile = 'error';
				$message = $reset->getMessages()[0];
			}
		}
	}

	$Tpl->addFolder('page', STOIC_CORE_PATH . '/tpl/reset-password');

	echo($Tpl->render("page::{$tplFile}", [
		'page' => $page
	]));
