<?php

	use Zibings\User;
	use Zibings\UserToken;
	use Zibings\UserTokens;

	class UserTokenClassTest extends ZsfTestCase {
		public function test_Crud() : void {
			$usr        = new User(self::$db, self::$log);
			$usr->email = uniqid() . '@example.com';
			self::assertTrue($usr->create()->isGood());

			$cls          = new UserToken(self::$db, self::$log);
			$cls->context = "REGISTRATION EMAIL CONFIRMATION";
			$cls->token   = uniqid();
			$cls->userId  = $usr->id;
			self::assertTrue($cls->create()->isGood());

			$cls2          = new UserToken(self::$db, self::$log);
			$cls2->context = "REGISTRATION EMAIL CONFIRMATION";
			$cls2->token   = uniqid();
			$cls2->userId  = $usr->id;
			self::assertTrue($cls2->create()->isGood());

			$cls_from = UserToken::fromId($cls->id, self::$db, self::$log);
			self::assertEquals($cls->id, $cls_from->id);
			self::assertEquals($cls->userId, $cls_from->userId);
			self::assertEquals($cls->token, $cls_from->token);
			self::assertEquals($cls->context, $cls_from->context);

			$cls_from2 = UserToken::fromToken($cls2->token, $usr->id, self::$db, self::$log);
			self::assertEquals($cls2->id, $cls_from2->id);
			self::assertEquals($cls2->userId, $cls_from2->userId);
			self::assertEquals($cls2->token, $cls_from2->token);
			self::assertEquals($cls2->context, $cls_from2->context);

			$cls->context = uniqid();
			self::assertTrue($cls->update()->isGood());

			$cls_from = UserToken::fromId($cls->id, self::$db, self::$log);
			self::assertEquals($cls->id, $cls_from->id);
			self::assertEquals($cls->userId, $cls_from->userId);
			self::assertEquals($cls->token, $cls_from->token);
			self::assertEquals($cls->context, $cls_from->context);

			$rpo = new UserTokens(self::$db, self::$log);
			self::assertEquals(2, $rpo->getNumTokensForUser($usr->id));
			$rpo->deleteAllForUser($usr->id);
			self::assertEquals(0, $rpo->getNumTokensForUser($usr->id));

			$usr->delete();
			$cls->delete();
			$cls2->delete();

			return;
		}
	}

