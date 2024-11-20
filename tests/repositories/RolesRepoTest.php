<?php

	use PHPUnit\Framework\TestCase;

	use Stoic\Log\Logger;
	use Stoic\Pdo\PdoHelper;

	use Zibings\Role;
	use Zibings\Roles;

	class RolesRepoTest extends TestCase {
		protected static PdoHelper $db;
		protected static Logger $log;


		public static function setUpBeforeClass() : void {
			global $Db, $Log;

			self::$db = $Db;
			self::$log = $Log;

			return;
		}


		public function test_getAll() {
			$role = new Role(self::$db, self::$log);
			$repo = new Roles(self::$db, self::$log);
			$origCount = count($repo->getAll());

			$role->name = uniqid();
			$role->create();

			self::assertNotCount($origCount, $repo->getAll());

			$role->delete();

			self::assertCount($origCount, $repo->getAll());

			return;
		}
	}
