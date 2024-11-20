<?php

	use Zibings\User;

	class UserClassTest extends ZsfTestCase {
		public function test_Crud() : void {
			$usr        = new User(self::$db, self::$log);
			$usr->email = uniqid() . '@example.com';

			self::assertTrue($usr->create()->isGood());

			$usr2 = User::fromId($usr->id, self::$db, self::$log);
			self::assertEquals($usr2->email, $usr->email);

			$usr3 = User::fromEmail($usr->email, self::$db, self::$log);
			self::assertEquals($usr3->id, $usr->id);

			self::assertTrue($usr->delete()->isGood());

			$usr3 = User::fromId($usr->id, self::$db, self::$log);
			self::assertNotEquals($usr3->id, $usr->id);

			self::assertFalse($usr->create()->isGood());

			$usr->id = 0;
			self::assertTrue($usr->create()->isGood());

			$oldEmail   = $usr->email;
			$usr->email = uniqid() . '@example.com';

			self::assertTrue($usr->update()->isGood());

			$usr2 = User::fromId($usr->id, self::$db, self::$log);
			self::assertEquals($usr2->email, $usr->email);
			self::assertNotEquals($usr2->email, $oldEmail);

			$usr2->markActive();
			self::assertNotNull($usr2->lastActive);

			$usr2->delete();

			return;
		}
	}
