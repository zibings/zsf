<?php

	use Zibings\AuthHistoryActions;
	use Zibings\UserAuthHistories;
	use Zibings\UserAuthHistory;

	class UserAuthHistoryClassTest extends ZsfTestCase {
		public function test_Crud() : void {
			$repo = new UserAuthHistories(self::$db, self::$log);

			$cls           = new UserAuthHistory(self::$db, self::$log);
			$cls->userId   = 1;
			$cls->action   = new AuthHistoryActions(AuthHistoryActions::LOGIN);
			$cls->address  = uniqid();
			$cls->hostname = uniqid();
			$cls->notes    = uniqid();

			self::assertTrue($cls->create()->isGood());

			$repo->deleteAllForUser(1);

			return;
		}
	}

