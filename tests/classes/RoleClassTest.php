<?php

	use PHPUnit\Framework\TestCase;

	use Stoic\Log\Logger;
	use Stoic\Pdo\PdoHelper;

	use Zibings\Role;

	class RoleClassTest extends TestCase {
		protected static PdoHelper $db;
		protected static Logger $log;


		public static function setUpBeforeClass() : void {
			global $Db, $Log;

			self::$db = $Db;
			self::$log = $Log;

			return;
		}


		public function test_Init() {
			$cls = new Role(self::$db, self::$log);
			$cls->name = uniqid();

			self::assertTrue($cls->create()->isGood());
			//self::assertTrue($cls->delete()->isGood());

			return;
		}
	}
