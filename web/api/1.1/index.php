<?php

	const STOIC_CORE_PATH = '../../../';
	require_once(STOIC_CORE_PATH . 'inc/core.php');

	use Stoic\Web\Api\Stoic;

	global $Api, $Db, $Log, $Settings;

	/**
	 * @var \Stoic\Web\Api\Stoic $Api
	 * @var \PDO $Db
	 * @var \Stoic\Log\Logger $Log
	 * @var \AndyM84\Config\ConfigContainer $Settings
	 */
	
	$Api = Stoic::getInstance(STOIC_CORE_PATH);

	$node = new Zibings\ApiBearerAuthorizer();
	$Api->linkAuthorizationNode($node);

	$node = new Zibings\ApiCookieAuthorizer();
	$Api->linkAuthorizationNode($node);

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
