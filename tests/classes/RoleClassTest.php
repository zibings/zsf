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


		public function test_Crud() {
			$cls = new Role(self::$db, self::$log);
			$cls->name = uniqid();

			self::assertTrue($cls->create()->isGood());

			$cls2 = Role::fromId($cls->id, self::$db, self::$log);
			self::assertEquals($cls2->name, $cls->name);

			$cls3 = Role::fromName($cls->name, self::$db, self::$log);
			self::assertEquals($cls3->id, $cls->id);

			self::assertTrue($cls->delete()->isGood());

			$cls3 = Role::fromId($cls->id, self::$db, self::$log);
			self::assertNotEquals($cls3->id, $cls->id);

			self::assertFalse($cls->create()->isGood());

			$cls->id = 0;
			self::assertTrue($cls->create()->isGood());

			$oldName   = $cls->name;
			$cls->name = uniqid();

			self::assertTrue($cls->update()->isGood());

			$cls2 = Role::fromId($cls->id, self::$db, self::$log);
			self::assertEquals($cls2->name, $cls->name);
			self::assertNotEquals($cls2->name, $oldName);

			$cls2->delete();

			return;
		}
	}
