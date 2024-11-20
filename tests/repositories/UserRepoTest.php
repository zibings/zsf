<?php

	use Zibings\User;
	use Zibings\UserGenders;
	use Zibings\UserProfile;
	use Zibings\UserProfiles;
	use Zibings\Users;
	use Zibings\UserVisibilities;
	use Zibings\VisibilityState;

	class UserRepoTest extends ZsfTestCase {
		public function test_getAll() : void {
			$repo      = new Users(self::$db, self::$log);
			$origUsers = $repo->getAll();
			$origCount = count($origUsers);

			$usr        = new User(self::$db, self::$log);
			$usr->email = uniqid() . '@example.com';
			$usr->create();

			$newUsers = $repo->getAll();

			self::assertNotCount($origCount, $newUsers);

			$foundUser = false;

			foreach ($newUsers as $user) {
				if ($user->email === $usr->email) {
					$foundUser = true;
					
					break;
				}
			}

			self::assertTrue($foundUser);

			$usr->delete();

			self::assertCount($origCount, $repo->getAll());

			return;
		}

		public function test_getAllWithProfile() : void {
			$repo      = new Users(self::$db, self::$log);
			$origUsers = $repo->getAllWithProfile();
			$origCount = count($origUsers);

			$usr        = new User(self::$db, self::$log);
			$usr->email = uniqid() . '@example.com';
			$usr->create();

			$newUsers = $repo->getAllWithProfile();

			self::assertCount($origCount, $newUsers);

			$usrProfile              = new UserProfile(self::$db, self::$log);
			$usrProfile->userId      = $usr->id;
			$usrProfile->displayName = uniqid();
			$usrProfile->create();

			$newUsers = $repo->getAllWithProfile();

			self::assertNotCount($origCount, $newUsers);
			self::assertArrayHasKey('user', $newUsers[0]);
			self::assertArrayHasKey('profile', $newUsers[0]);

			$foundUser = false;

			foreach ($newUsers as $user) {
				if ($user['user']->email === $usr->email) {
					$foundUser = true;
					
					break;
				}
			}

			self::assertTrue($foundUser);

			(new UserProfiles(self::$db, self::$log))->deleteAllForUser($usr->id);
			$usr->delete();

			self::assertCount($origCount, $repo->getAllWithProfile());

			return;
		}

		public function test_getColumnsForGetAll() : void {
			$repo = new Users(self::$db, self::$log);
			$cols = $repo->getColumnsForGetAll();

			self::assertContains('id', $cols);
			self::assertContains('email', $cols);
			self::assertContains('emailConfirmed', $cols);
			self::assertContains('joined', $cols);
			self::assertContains('lastActive', $cols);
			self::assertContains('lastLogin', $cols);

			return;
		}

		public function test_getDailyActiveUserCount() : void {
			$repo      = new Users(self::$db, self::$log);
			$origCount = $repo->getDailyActiveUserCount();

			$usr        = new User(self::$db, self::$log);
			$usr->email = uniqid() . '@example.com';
			
			$usr->create();
			$usr->markActive();

			self::assertNotEquals($origCount, $repo->getDailyActiveUserCount());

			$usr->delete();

			self::assertEquals($origCount, $repo->getDailyActiveUserCount());

			return;
		}

		public function test_getMonthlyActiveUserCount() : void {
			$repo      = new Users(self::$db, self::$log);
			$origCount = $repo->getMonthlyActiveUserCount();

			$usr        = new User(self::$db, self::$log);
			$usr->email = uniqid() . '@example.com';
			
			$usr->create();
			$usr->markActive();

			self::assertNotEquals($origCount, $repo->getMonthlyActiveUserCount());

			$usr->delete();

			self::assertEquals($origCount, $repo->getMonthlyActiveUserCount());

			return;
		}

		public function test_getTotalUsers() : void {
			$repo      = new Users(self::$db, self::$log);
			$origCount = $repo->getTotalUsers();

			$usr        = new User(self::$db, self::$log);
			$usr->email = uniqid() . '@example.com';

			$usr->create();
			$usr->markActive();

			self::assertNotEquals($origCount, $repo->getTotalUsers());

			$usr->delete();

			self::assertEquals($origCount, $repo->getTotalUsers());

			return;
		}

		public function test_getTotalVerifiedUsers() : void {
			$repo      = new Users(self::$db, self::$log);
			$origCount = $repo->getTotalVerifiedUsers();

			$usr                 = new User(self::$db, self::$log);
			$usr->email          = uniqid() . '@example.com';
			$usr->emailConfirmed = true;

			$usr->create();
			$usr->markActive();

			self::assertNotEquals($origCount, $repo->getTotalVerifiedUsers());

			$usr->delete();

			self::assertEquals($origCount, $repo->getTotalVerifiedUsers());

			return;
		}

		public function test_searchUsersByIdentifiers() : void {
			$newUsername         = uniqid() . '-username';
			$newRealName         = uniqid() . '-realname';
			$newEmail            = uniqid() . '@example.com';
			$newDescription      = uniqid() . '-description';
			$repo                = new Users(self::$db, self::$log);
			$origUsersVisible    = $repo->searchUsersByIdentifiers($newEmail);
			$origUsersNotVisible = $repo->searchUsersByIdentifiers($newEmail, false);

			$usr        = new User(self::$db, self::$log);
			$usr->email = $newEmail;
			$usr->create();

			$usrProfile              = new UserProfile(self::$db, self::$log);
			$usrProfile->birthday    = new \DateTimeImmutable('now', new \DateTimeZone('UTC'));
			$usrProfile->description = $newDescription;
			$usrProfile->displayName = $newUsername;
			$usrProfile->gender      = new UserGenders(UserGenders::FEMALE);
			$usrProfile->realName    = $newRealName;
			$usrProfile->userId      = $usr->id;
			$usrProfile->create();

			$usrVis           = new UserVisibilities(self::$db, self::$log);
			$usrVis->userId   = $usr->id;
			$usrVis->searches = new VisibilityState(VisibilityState::PRV);
			$usrVis->create();

			self::assertCount(count($origUsersVisible), $repo->searchUsersByIdentifiers($newEmail));
			self::assertNotCount(count($origUsersNotVisible), $repo->searchUsersByIdentifiers($newEmail, false));

			$usrVis->searches = new VisibilityState(VisibilityState::PUB);
			$usrVis->update();

			self::assertNotCount(count($origUsersVisible), $repo->searchUsersByIdentifiers($newEmail));
			self::assertNotCount(count($origUsersNotVisible), $repo->searchUsersByIdentifiers($newEmail, false));

			$origUsersVisible    = $repo->searchUsersByIdentifiers($newUsername);
			$origUsersNotVisible = $repo->searchUsersByIdentifiers($newUsername, false);

			$usrProfile->displayName = $newUsername;
			$usrProfile->update();

			$usrVis->searches = new VisibilityState(VisibilityState::PRV);
			$usrVis->update();

			self::assertNotCount(count($origUsersVisible), $repo->searchUsersByIdentifiers($newUsername));
			self::assertCount(count($origUsersNotVisible), $repo->searchUsersByIdentifiers($newUsername, false));

			$usrVis->searches = new VisibilityState(VisibilityState::PUB);
			$usrVis->update();

			self::assertCount(count($origUsersVisible), $repo->searchUsersByIdentifiers($newUsername));
			self::assertCount(count($origUsersNotVisible), $repo->searchUsersByIdentifiers($newUsername, false));

			$origUsersVisible    = $repo->searchUsersByIdentifiers($newRealName);
			$origUsersNotVisible = $repo->searchUsersByIdentifiers($newRealName, false);

			$usrProfile->realName = $newRealName;
			$usrProfile->update();

			$usrVis->searches = new VisibilityState(VisibilityState::PRV);
			$usrVis->update();

			self::assertNotCount(count($origUsersVisible), $repo->searchUsersByIdentifiers($newRealName));
			self::assertCount(count($origUsersNotVisible), $repo->searchUsersByIdentifiers($newRealName, false));

			$usrVis->searches = new VisibilityState(VisibilityState::PUB);
			$usrVis->update();

			self::assertCount(count($origUsersVisible), $repo->searchUsersByIdentifiers($newRealName));
			self::assertCount(count($origUsersNotVisible), $repo->searchUsersByIdentifiers($newRealName, false));

			$origUsersVisible    = $repo->searchUsersByIdentifiers($newDescription);
			$origUsersNotVisible = $repo->searchUsersByIdentifiers($newDescription, false);

			$usrProfile->description = $newDescription;
			$usrProfile->update();

			$usrVis->searches = new VisibilityState(VisibilityState::PRV);
			$usrVis->update();

			self::assertNotCount(count($origUsersVisible), $repo->searchUsersByIdentifiers($newDescription));
			self::assertCount(count($origUsersNotVisible), $repo->searchUsersByIdentifiers($newDescription, false));

			$usrVis->searches = new VisibilityState(VisibilityState::PUB);
			$usrVis->update();

			self::assertCount(count($origUsersVisible), $repo->searchUsersByIdentifiers($newDescription));
			self::assertCount(count($origUsersNotVisible), $repo->searchUsersByIdentifiers($newDescription, false));

			$usrVis->delete();
			$usrProfile->delete();
			$usr->delete();

			return;
		}
	}

