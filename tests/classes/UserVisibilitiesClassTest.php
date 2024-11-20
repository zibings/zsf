<?php

	use Zibings\User;
	use Zibings\UserVisibilities;
	use Zibings\VisibilityState;

	class UserVisibilitiesClassTest extends ZsfTestCase {
		public function test_Crud() : void {
			$usr        = new User(self::$db, self::$log);
			$usr->email = uniqid() . '@example.com';
			$usr->create();

			$cls              = new UserVisibilities(self::$db, self::$log);
			$cls->birthday    = new VisibilityState(VisibilityState::PUB);
			$cls->description = new VisibilityState(VisibilityState::PUB);
			$cls->email       = new VisibilityState(VisibilityState::PUB);
			$cls->gender      = new VisibilityState(VisibilityState::PUB);
			$cls->profile     = new VisibilityState(VisibilityState::PUB);
			$cls->realName    = new VisibilityState(VisibilityState::PUB);
			$cls->searches    = new VisibilityState(VisibilityState::PUB);
			$cls->userId      = $usr->id;

			self::assertTrue($cls->create()->isGood());
			self::assertFalse($cls->create()->isGood());
			self::assertTrue(UserVisibilities::fromUser($usr->id, self::$db, self::$log)->birthday->is(VisibilityState::PUB));

			$cls->birthday = new VisibilityState(VisibilityState::PRV);
			self::assertTrue($cls->update()->isGood());
			self::assertTrue(UserVisibilities::fromUser($usr->id, self::$db, self::$log)->birthday->is(VisibilityState::PRV));

			self::assertTrue($cls->delete()->isGood());

			$usr->delete();

			return;
		}
	}

