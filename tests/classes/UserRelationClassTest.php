<?php

	use Zibings\User;
	use Zibings\UserRelation;
	use Zibings\UserRelationStages;

	class UserRelationClassTest extends ZsfTestCase {
		public function test_Crud() : void {
			$usr1        = new User(self::$db, self::$log);
			$usr1->email = uniqid() . '@example.com';
			$usr1->create();

			$usr2        = new User(self::$db, self::$log);
			$usr2->email = uniqid() . '@example.com';
			$usr2->create();

			$cls          = new UserRelation(self::$db, self::$log);
			$cls->stage   = new UserRelationStages(UserRelationStages::INVITED);
			$cls->origin  = true;
			$cls->userOne = $usr2->id;
			$cls->userTwo = $usr1->id;

			self::assertTrue($cls->create()->isGood());
			self::assertEquals($cls->stage->getValue(), UserRelationStages::INVITED);

			$cls->stage = new UserRelationStages(UserRelationStages::ACCEPTED);
			self::assertTrue($cls->update()->isGood());

			$cls2          = new UserRelation(self::$db, self::$log);
			$cls2->userOne = $usr2->id;
			$cls2->userTwo = $usr1->id;

			self::assertTrue($cls2->read()->isGood());
			self::assertEquals($cls2->stage->getValue(), UserRelationStages::ACCEPTED);

			self::assertTrue($cls2->delete()->isGood());

			$usr1->delete();
			$usr2->delete();

			return;
		}
	}

