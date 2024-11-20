<?php

	use Zibings\AuthHistoryActions;
	use Zibings\UserAuthHistories;
	use Zibings\UserAuthHistory;

	class UserAuthHistoriesRepoTest extends ZsfTestCase {
		public function test_getUserAuthHistories() : void {
			$repo          = new UserAuthHistories(self::$db, self::$log);
			$origHistories = $repo->getUserAuthHistory(1);

			$cls           = new UserAuthHistory(self::$db, self::$log);
			$cls->userId   = 1;
			$cls->action   = new AuthHistoryActions(AuthHistoryActions::LOGIN);
			$cls->address  = uniqid();
			$cls->hostname = uniqid();
			$cls->notes    = uniqid();
			$cls->create();

			self::assertNotCount(count($origHistories), $repo->getUserAuthHistory(1));
			self::assertEquals($cls->address, $repo->getUserAuthHistory(1)[0]->address);

			$repo->deleteAllForUser(1);

			self::assertCount(count($origHistories), $repo->getUserAuthHistory(1));

			return;
		}

		public function test_getUserAuthHistoryCount() : void {
			$repo          = new UserAuthHistories(self::$db, self::$log);
			$origHistories = $repo->getUserAuthHistoryCount(1);

			$cls           = new UserAuthHistory(self::$db, self::$log);
			$cls->userId   = 1;
			$cls->action   = new AuthHistoryActions(AuthHistoryActions::LOGIN);
			$cls->address  = uniqid();
			$cls->hostname = uniqid();
			$cls->notes    = uniqid();
			$cls->create();

			self::assertNotEquals($origHistories, $repo->getUserAuthHistoryCount(1));

			$repo->deleteAllForUser(1);

			self::assertEquals($origHistories, $repo->getUserAuthHistoryCount(1));

			return;
		}
	}

