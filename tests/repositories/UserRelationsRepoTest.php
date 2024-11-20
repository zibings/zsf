<?php

	use Zibings\User;
	use Zibings\UserRelations;
	use Zibings\UserRelationStages;

	class UserRelationsRepoTest extends ZsfTestCase {
		public function test_Relations() : void {
			$usr1        = new User(self::$db, self::$log);
			$usr1->email = uniqid() . '@example.com';
			$usr1->create();

			$usr2        = new User(self::$db, self::$log);
			$usr2->email = uniqid() . '@example.com';
			$usr2->create();

			$repo = new UserRelations(self::$db, self::$log);

			self::assertTrue($repo->changeStage($usr1->id, $usr2->id, UserRelationStages::INVITED));
			self::assertTrue($repo->areRelated($usr1->id, $usr2->id));
			self::assertEquals($repo->getRelationStage($usr1->id, $usr2->id)->getValue(), UserRelationStages::INVITED);
			self::assertCount(2, $repo->getRelations($usr1->id));
			self::assertCount(2, $repo->getRelations($usr2->id));
			self::assertCount(1, $repo->getIncomingRelations($usr1->id));
			self::assertCount(1, $repo->getIncomingRelations($usr2->id));
			self::assertCount(1, $repo->getOutgoingRelations($usr1->id));
			self::assertCount(1, $repo->getOutgoingRelations($usr2->id));
			self::assertCount(2, $repo->getRelationsByStage($usr1->id, UserRelationStages::INVITED));
			self::assertCount(1, $repo->getIncomingRelationsByStage($usr1->id, UserRelationStages::INVITED));
			self::assertCount(1, $repo->getOutgoingRelationsByStage($usr1->id, UserRelationStages::INVITED));
			self::assertCount(0, $repo->getIncomingRelationsExceptingStage($usr1->id, UserRelationStages::INVITED));
			self::assertCount(0, $repo->getOutgoingRelationsExceptingStage($usr1->id, UserRelationStages::INVITED));
			self::assertTrue($repo->deleteRelation($usr1->id, $usr2->id));
			self::assertFalse($repo->areRelated($usr1->id, $usr2->id));

			$repo->changeStage($usr1->id, $usr2->id, UserRelationStages::INVITED);
			self::assertCount(2, $repo->getRelations($usr1->id));

			$repo->deleteAllForUser($usr1->id);
			self::assertCount(0, $repo->getRelations($usr1->id));

			$usr1->delete();
			$usr2->delete();

			return;
		}
	}

