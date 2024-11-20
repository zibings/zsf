<?php

	use Zibings\User;
	use Zibings\UserContact;
	use Zibings\UserContacts;
	use Zibings\UserContactTypes;

	class UserContactClassTest extends ZsfTestCase {
		public function test_Crud() : void {
			$usr = new User(self::$db, self::$log);
			$usr->email = uniqid() . '@example.com';
			$usr->create();

			$repo         = new UserContacts(self::$db, self::$log);
			$origContacts = $repo->getUserContacts($usr->id);

			$uc          = new UserContact(self::$db, self::$log);
			$uc->userId  = $usr->id;
			$uc->primary = true;
			$uc->type    = new UserContactTypes(UserContactTypes::EMAIL);
			$uc->value   = $usr->email;

			self::assertTrue($uc->create()->isGood());

			$newContacts = $repo->getUserContacts($usr->id);

			self::assertNotCount(count($origContacts), $newContacts);
			self::assertEquals($newContacts[0]->value, $uc->value);

			$uc->delete();

			self::assertCount(count($origContacts), $repo->getUserContacts($usr->id));

			$usr->delete();

			return;
		}
	}

