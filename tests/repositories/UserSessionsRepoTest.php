<?php

	use Zibings\User;
	use Zibings\UserSession;
	use Zibings\UserSessions;

	class UserSessionsRepoTest extends ZsfTestCase {
		public function test_deleteAllForUser() : void {
			$usr        = new User(self::$db, self::$log);
			$usr->email = uniqid() . '@example.com';
			$usr->create();

			$repo      = new UserSessions(self::$db, self::$log);
			$origCount = count($repo->getAllForUser($usr->id));

			$cls           = new UserSession(self::$db, self::$log);
			$cls->address  = uniqid();
			$cls->hostname = uniqid();
			$cls->token    = uniqid();
			$cls->userId   = $usr->id;
			$cls->create();

			self::assertNotCount($origCount, $repo->getAllForUser($usr->id));

			$repo->deleteAllForUser($usr->id);

			self::assertCount($origCount, $repo->getAllForUser($usr->id));

			$usr->delete();

			return;
		}
	}

