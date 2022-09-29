<?php

	error_reporting(E_ALL);
	ini_set('display_errors', 'On');

	const STOIC_CORE_PATH = '../../../';
	require(STOIC_CORE_PATH . 'inc/core.php');

	use Stoic\Web\Api\Stoic;

	global $Api, $Db, $Log, $Settings;

	/**
	 * @var \Stoic\Web\Api\Stoic $Api
	 * @var \PDO $Db
	 * @var \Stoic\Log\Logger $Log
	 * @var \AndyM84\Config\ConfigContainer $Settings
	 */
	
	$Api = Stoic::getInstance(STOIC_CORE_PATH);

	$authorizer = null;

	if (STOIC_API_AUTH_COOKIE) {
		$authorizer = new Zibings\ApiCookieAuthorizer();
	} else {
		$authorizer = new Zibings\ApiBearerAuthorizer();
	}

	$Api->linkAuthorizationNode($authorizer);

	$endpoints = [];
	$loadedFiles = $Api->loadFilesByExtension('~/api/1.1', '.api.php');

	foreach ($loadedFiles as $file) {
		if (str_ends_with($file, "ApiRoot.api.php")) {
			continue;
		}

		$f = str_replace('.api.php', '', basename($file));

		$cls = "\\Api1_1\\{$f}";
		$endpoints[] = new $cls($Api, $Db, $Log);
	}

	$Api->handle();
