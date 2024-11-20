<?php

	$zsfDefaultConstants = [
		'STOIC_CORE_PATH'             => './',
		'STOIC_API_AUTH_COOKIE'       => true,
		'STOIC_DISABLE_SESSION'       => false,
		'STOIC_DISABLE_DB_EXCEPTIONS' => false,
		'STOIC_ENABLE_DEBUG'          => false
	];

	foreach ($zsfDefaultConstants as $constant => $default) {
		if (!defined($constant)) {
			define($constant, $default);
		}
	}

	$corePath       = STOIC_CORE_PATH;
	$corePathSuffix = $corePath[strlen($corePath) - 1];

	if ($corePathSuffix != '/') {
		$corePath .= '/';
	}

	require(STOIC_CORE_PATH . 'vendor/autoload.php');
	require(STOIC_CORE_PATH . 'tests/ZsfTestCase.php');

	use Stoic\Web\Resources\PageVariables;
	use Stoic\Web\Stoic;
	use Stoic\Utilities\ParameterHelper;

	global $Db, $Log, $Settings, $Stoic;

	/**
	 * @var PdoHelper $Db
	 * @var Logger $Log
	 * @var ConfigContainer $Settings
	 * @var Stoic $Stoic
	 */

	$Stoic    = Stoic::getInstance(STOIC_CORE_PATH, new PageVariables([], [], [], [], [], [], ['REQUEST_METHOD' => 'GET'], []));
	$Log      = $Stoic->getLog();
	$Db       = $Stoic->getDb();
	$Settings = $Stoic->getConfig();
