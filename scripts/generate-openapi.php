<?php

	const STOIC_CORE_PATH = './';
	require_once(STOIC_CORE_PATH . 'inc/core.php');

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
	)->addOption(
		"output-file",
		"o",
		"output",
		"Output file for spec",
		"Optional file to write spec to when finished (will overwrite)",
		false
	)->addOption(
		"format",
		"f",
		"format",
		"Output format (json or yaml)",
		"Format to use for OpenAPI spec output",
		false,
		"json"
	);

	$script->startScript($ch);

	$fh     = $Stoic->getFileHelper();
	$params = $script->getOptions($ch);

	if (!$fh->folderExists("~/api/{$params['version']}")) {
		$ch->putLine("The API version '{$params['version']}' does not exist, please try again");
		$ch->putLine();

		exit;
	}

	$ch->put("Collecting API controllers.. ");

	$Stoic->loadFilesByExtension("~/api/{$params['version']}", '.api.php');

	$ch->putLine("DONE");
	$ch->put("Generating OpenAPI spec from controllers.. ");

	$oa = (new \OpenApi\Generator($Log))
				->generate(['./']);

	$ch->putLine("DONE");
	$ch->put("Gathering spec for output.. ");

	$output = (strtolower($params["format"]) == "json") ? $oa->toJson() : $oa->toYaml();

	$ch->putLine("DONE");

	if (!empty($params["output"])) {
		$ch->put("Writing OpenAPI spec to file '{$params["output"]}'.. ");

		$fh->putContents($params["output"], $output);

		$ch->putLine("DONE");
	} else {
		$ch->putLine();
		$ch->putLine($output);
	}

	$ch->putLine();
