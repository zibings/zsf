<?php

	define('STOIC_CORE_PATH', '../../');
	require(STOIC_CORE_PATH . 'inc/core.php');

	use Stoic\Utilities\ParameterHelper;
	use Stoic\Web\PageHelper;

	use Zibings\LoginKeyProviders;
	use Zibings\RoleStrings;
	use Zibings\User;
	use Zibings\UserEvents;
	use Zibings\UserProfile;
	use Zibings\UserRoles;
	use Zibings\Users;
	use Zibings\UserSettings;
	use Zibings\UserVisibilities;

	use function Zibings\isAuthenticated;

	global $Db, $Log, $Settings, $Stoic, $Tpl, $User;

	/**
	 * @var \Stoic\Pdo\PdoHelper $Db
	 * @var \Stoic\Log\Logger $Log
	 * @var \AndyM84\Config\ConfigContainer $Settings
	 * @var \Stoic\Web\Stoic $Stoic
	 * @var \League\Plates\Engine $Tpl
	 */

	$page = PageHelper::getPage('admin/users.php');
	$page->setTitle('Site Administration');

	if (!isAuthenticated($Db, RoleStrings::ADMINISTRATOR)) {
		$page->redirectTo('~/index.php');
	}

	$tplFile = 'index';
	$users   = new Users($Db, $Log);
	$get     = $Stoic->getRequest()->getGet();
	$post    = $Stoic->getRequest()->getPost();

	$tplVars = [
		'page'         => $page,
		'message'      => "",
		'messageState' => 'warn'
	];

	$currentUser = ($get->has('id')) ? User::fromId($get->getInt('id'), $Db, $Log) : new User($Db, $Log);

	if ($currentUser->id > 0 && $User->id != $currentUser->id && (new UserRoles($Db, $Log))->userInRoleByName($currentUser->id, RoleStrings::ADMINISTRATOR)) {
		$page->redirectTo('~/admin/users.php');
	}

	if ($get->has('action')) {
		$tplFile = match ($get->getString('action')) {
			'create', 'edit' => 'form',
			default => 'index'
		};
	}

	if ($currentUser->id > 0 && $tplFile == 'index') {
		$tplFile = 'form';
	}

	$tplVars['currentUser']  = $currentUser;
	$tplVars['profile']      = ($currentUser->id > 0) ? UserProfile::fromUser($currentUser->id, $Db, $Log) : new UserProfile($Db, $Log);
	$tplVars['userSettings'] = ($currentUser->id > 0) ? UserSettings::fromUser($currentUser->id, $Db, $Log) : new UserSettings($Db, $Log);
	$tplVars['visibilities'] = ($currentUser->id > 0) ? UserVisibilities::fromUser($currentUser->id, $Db, $Log) : new UserVisibilities($Db, $Log);

	if ($post->has('action')) {
		$events = new UserEvents($Db, $Log);
		$event  = match($post->getString('action')) {
			'create' => 'doCreate',
			'edit'   => 'doUpdate',
			default  => 'error'
		};

		if ($event == 'error') {
			$page->redirectTo('~/admin/users.php');
		}

		$postData = [
			'id'             => $currentUser->id,
			'actor'          => $User->id,
			'email'          => $post->getString('email'),
			'confirmEmail'   => $post->getString('email'),
			'emailConfirmed' => true,
			'key'            => $post->getString('password'),
			'confirmKey'     => $post->getString('password'),
			'provider'       => LoginKeyProviders::PASSWORD,
			'profile'        => [
				'birthday'     => $post->getString('birthday'),
				'description'  => $post->getString('description'),
				'displayName'  => $post->getString('displayName'),
				'gender'       => $post->getInt('gender'),
				'realName'     => $post->getString('realName')
			],
			'settings'       => [
				'htmlEmails'   => $post->has('set_htmlEmails'),
				'playSounds'   => $post->has('set_playSounds')
			],
			'visibilities'   => [
				'birthday'     => $post->getInt('vis_birthday'),
				'description'  => $post->getInt('vis_description'),
				'email'        => $post->getInt('vis_email'),
				'gender'       => $post->getInt('vis_gender'),
				'profile'      => $post->getInt('vis_profile'),
				'realName'     => $post->getInt('vis_realName'),
				'searches'     => $post->getInt('vis_searches')
			]
		];

		$update = $events->$event(new ParameterHelper($postData));

		if ($update->isGood()) {
			$postData['id'] = $update->getResults()[0]['data']->id;
			$events->doUpdate(new ParameterHelper($postData));

			$page->redirectTo("~/admin/users.php?id={$postData['id']}");
		}

		$tplVars['message'] = implode("<br />", $update->getMessages());
	}

	if (!$get->has('action')) {
		$tplVars['users'] = $users->getAllWithProfile();
	}

	$Tpl->addFolder('page', STOIC_CORE_PATH . '/tpl/admin/users');

	echo($Tpl->render("page::{$tplFile}", $tplVars));
