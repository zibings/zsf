<?php

	use Zibings\User;
	use Zibings\UserGenders;
	use Zibings\UserProfile;

	class UserProfileClassTest extends ZsfTestCase {
		public function test_Crud() : void {
			$usr        = new User(self::$db, self::$log);
			$usr->email = uniqid() . '@example.com';
			$usr->create();

			$cls              = new UserProfile(self::$db, self::$log);
			$cls->birthday    = new \DateTimeImmutable('now', new \DateTimeZone('UTC'));
			$cls->description = uniqid();
			$cls->displayName = uniqid();
			$cls->gender      = new UserGenders(UserGenders::FEMALE);
			$cls->realName    = uniqid();
			$cls->userId      = $usr->id;

			self::assertTrue($cls->create()->isGood());

			$cls2 = UserProfile::fromUser($usr->id, self::$db, self::$log);
			self::assertEquals($cls2->birthday->format('Y-m-d'), $cls->birthday->format('Y-m-d'));
			self::assertEquals($cls2->description, $cls->description);
			self::assertEquals($cls2->displayName, $cls->displayName);

			$cls3 = UserProfile::fromDisplayName($cls->displayName, self::$db, self::$log);
			self::assertEquals($cls3->userId, $cls->userId);

			self::assertTrue($cls->delete()->isGood());

			$usr->delete();

			return;
		}
	}

