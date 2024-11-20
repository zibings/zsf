<?php

	use Zibings\User;
	use Zibings\UserSession;

	class UserSessionClassTest extends ZsfTestCase {
		public function test_Crud() : void {
			$usr        = new User(self::$db, self::$log);
			$usr->email = uniqid() . '@example.com';
			$usr->create();

			$cls           = new UserSession(self::$db, self::$log);
			$cls->address  = uniqid();
			$cls->hostname = uniqid();
			$cls->token    = uniqid();
			$cls->userId   = $usr->id;

			self::assertTrue($cls->create()->isGood());
			self::assertGreaterThan(0, $cls->id);
			self::assertEquals($cls->id, UserSession::fromToken($cls->token, self::$db, self::$log)->id);
			self::assertEquals($cls->userId, UserSession::fromId($cls->id, self::$db, self::$log)->userId);
			self::assertTrue($cls->delete()->isGood());

			$usr->delete();

			return;
		}
	}

