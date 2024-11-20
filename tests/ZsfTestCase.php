<?php

	use PHPUnit\Framework\TestCase;

	use Stoic\Log\Logger;
	use Stoic\Pdo\PdoHelper;

	abstract class ZsfTestCase extends TestCase {
		protected static PdoHelper $db;
		protected static Logger $log;


		public static function setUpBeforeClass() : void {
			global $Db, $Log;

			self::$db = $Db;
			self::$log = $Log;

			return;
		}
	}
