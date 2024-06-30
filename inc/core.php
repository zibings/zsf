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

	if (STOIC_ENABLE_DEBUG) {
		error_reporting(E_ALL);
		ini_set('display_errors', 'On');
	}

	require(STOIC_CORE_PATH . 'vendor/autoload.php');

	use AndyM84\Config\ConfigContainer;

	use Stoic\Log\Logger;
	use Stoic\Pdo\PdoHelper;
	use Stoic\Utilities\ParameterHelper;
	use Stoic\Web\Resources\PageVariables;
	use Stoic\Web\Stoic;

	use Zibings\UserProfile;

	use function Zibings\getUserFromSessionToken;

	global $Db, $Log, $Settings, $Stoic, $User, $Profile;

	/**
	 * @var PdoHelper $Db
	 * @var Logger $Log
	 * @var ConfigContainer $Settings
	 * @var Stoic $Stoic
	 * @var \Zibings\User $User
	 * @var \Zibings\UserProfile $Profile
	 */

	if (PHP_SAPI == 'cli') {
		$Stoic = Stoic::getInstance(STOIC_CORE_PATH, new PageVariables([], [], [], [], [], [], ['REQUEST_METHOD' => 'GET'], []));
	} else {
		$Stoic = Stoic::getInstance(STOIC_CORE_PATH);
	}

	$Log      = $Stoic->getLog();
	$Db       = $Stoic->getDb();
	$Session  = new ParameterHelper($_SESSION);
	$Settings = $Stoic->getConfig();

	if ($Db->isActive()) {
		$User     = getUserFromSessionToken($Session, $Db, $Log);
		$Profile  = ($User->id > 0) ? UserProfile::fromUser($User->id, $Db, $Log) : new UserProfile($Db, $Log);

		if ($User->id > 0) {
			if (empty($Profile->displayName)) {
				$Profile->displayName = "*{$User->email}";
			}

			$User->markActive();
		}
	}
