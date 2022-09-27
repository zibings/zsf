<?php

	error_reporting(E_ALL);
	ini_set('display_errors', 'On');

	const STOIC_CORE_PATH = './';
	require(STOIC_CORE_PATH . 'inc/core.php');

	use Stoic\Utilities\ConsoleHelper;

	use Zibings\CliScriptHelper;

	global $Db, $Log, $Stoic;

	/**
	 * @var \Stoic\PDO\PdoHelper $Db
	 * @var \Stoic\Log\Logger $Log
	 * @var \Stoic\Web\Stoic $Stoic
	 */

	$ch = new ConsoleHelper($argv);
	$script = (new CliScriptHelper(
		'ZSF OpenAPI Generator',
		"Script to generate an OpenAPI spec based on the chosen API"
	))->addExample(
<<< EXAMPLE
- Run script for v1.1 API

  php scripts/generate-openapi.php 1.1
EXAMPLE
	)->addOption(
		"version",
		"v",
		"version",
		"API version",
		"The API version to scan for OpenAPI specifications",
		true
	);

	$script->startScript($ch);

	$fh     = $Stoic->getFileHelper();
	$params = $script->getOptions($ch);

	if (!$fh->folderExists("~/api/{$params['version']}")) {
		$ch->putLine("The API version '{$params['version']}' does not exist, please try again");
		$ch->putLine();

		exit;
	}

	$Stoic->loadFilesByExtension("~/api/{$params['version']}", '.api.php');

	$oa = (new \OpenApi\Generator($Log))
				->generate(['./']);

	echo $oa->toJson();
