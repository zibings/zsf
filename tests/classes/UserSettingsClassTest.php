<?php

	use Zibings\User;
	use Zibings\UserSettings;

	class UserSettingsClassTest extends ZsfTestCase {
		public function test_Crud() : void {
			$usr        = new User(self::$db, self::$log);
			$usr->email = uniqid() . '@example.com';
			$usr->create();

			$cls         = new UserSettings(self::$db, self::$log);
			$cls->userId = $usr->id;

			self::assertTrue($cls->create()->isGood());
			self::assertFalse(UserSettings::fromUser($usr->id, self::$db, self::$log)->htmlEmails);

			$cls->htmlEmails = true;
			self::assertTrue($cls->update()->isGood());

			self::assertTrue(UserSettings::fromUser($usr->id, self::$db, self::$log)->htmlEmails);

			self::assertTrue($cls->delete()->isGood());

			$usr->delete();

			return;
		}
	}

