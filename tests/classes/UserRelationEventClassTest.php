<?php

	use Zibings\UserRelationEvent;
	use Zibings\UserRelationEventActions;
	use Zibings\UserRelations;
	use Zibings\UserRelationStages;

	class UserRelationEventClassTest extends ZsfTestCase {
		public function test_Crud() : void {
			$repo = new UserRelations(self::$db, self::$log);
			
			$cls          = new UserRelationEvent(self::$db, self::$log);
			$cls->action  = new UserRelationEventActions(UserRelationEventActions::INVITE);
			$cls->notes   = uniqid();
			$cls->stage   = new UserRelationStages(UserRelationStages::INVITED);
			$cls->userOne = 1;
			$cls->userTwo = 2;

			self::assertTrue($cls->create()->isGood());

			$repo->deleteAllEventsForUser(1);

			return;
		}
	}

