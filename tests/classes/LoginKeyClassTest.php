<?php

	use Zibings\LoginKey;
	use Zibings\LoginKeyProviders;
	use Zibings\LoginKeys;
	use Zibings\User;

	class LoginKeyClassTest extends ZsfTestCase {
		public function test_Crud() : void {
			$usr        = new User(self::$db, self::$log);
			$usr->email = uniqid() . '@example.com';
			self::assertTrue($usr->create()->isGood());

			$cls = new LoginKey(self::$db, self::$log);
			$cls->key      = uniqid();
			$cls->userId   = $usr->id;
			$cls->provider = new LoginKeyProviders(LoginKeyProviders::REDDIT);
			self::assertTrue($cls->create()->isGood());

			$cls2 = new LoginKey(self::$db, self::$log);
			$cls2->key      = uniqid();
			$cls2->userId   = $usr->id;
			$cls2->provider = new LoginKeyProviders(LoginKeyProviders::FACEBOOK);
			self::assertTrue($cls2->create()->isGood());

			$cls3 = new LoginKey(self::$db, self::$log);
			$cls3->key      = uniqid();
			$cls3->userId   = $usr->id;
			$cls3->provider = new LoginKeyProviders(LoginKeyProviders::TWITCH);
			self::assertTrue($cls3->create()->isGood());

			$cls_from = LoginKey::fromUserAndProvider($cls->userId, LoginKeyProviders::REDDIT, self::$db, self::$log);
			self::assertEquals($cls->userId, $cls_from->userId);
			self::assertEquals($cls->provider->getValue(), $cls_from->provider->getValue());
			self::assertEquals($cls->key, $cls_from->key);

			$cls_from2 = LoginKey::fromUserAndProvider($cls2->userId, LoginKeyProviders::FACEBOOK, self::$db, self::$log);
			self::assertEquals($cls2->userId, $cls_from2->userId);
			self::assertEquals($cls2->provider->getValue(), $cls_from2->provider->getValue());
			self::assertEquals($cls2->key, $cls_from2->key);

			$cls_from3 = LoginKey::fromUserAndProvider($cls3->userId, LoginKeyProviders::TWITCH, self::$db, self::$log);
			self::assertEquals($cls3->userId, $cls_from3->userId);
			self::assertEquals($cls3->provider->getValue(), $cls_from3->provider->getValue());
			self::assertEquals($cls3->key, $cls_from3->key);

			$rpo = new LoginKeys(self::$db, self::$log);
			self::assertEquals(3, $rpo->getNumKeysForUser($usr->id));
			$rpo->deleteAllForUser($usr->id);
			self::assertEquals(0, $rpo->getNumKeysForUser($usr->id));

			$usr->delete();
			$cls->delete();
			$cls2->delete();
			$cls3->delete();

			return;
		}
	}

