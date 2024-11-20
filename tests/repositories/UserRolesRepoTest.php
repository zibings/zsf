<?php

	use Zibings\Role;
	use Zibings\User;
	use Zibings\UserRoles;

	class UserRolesRepoTest extends ZsfTestCase {
		public function test_All() : void {
			$role       = new Role(self::$db, self::$log);
			$role->name = uniqid();
			$role->create();

			$usr        = new User(self::$db, self::$log);
			$usr->email = uniqid() . '@example.com';
			$usr->create();

			$repo = new UserRoles(self::$db, self::$log);

			self::assertCount(0, $repo->getAllUserRoles($usr->id));

			$repo->addUserToRoleByName($usr->id, $role->name);

			self::assertCount(2, $repo->getAllUserRoles($usr->id));
			self::assertArrayHasKey($role->id, $repo->getAllUserRoles($usr->id));
			self::assertArrayHasKey($role->name, $repo->getAllUserRoles($usr->id));
			self::assertTrue($repo->userInRoleByName($usr->id, $role->name));
			self::assertCount(1, $repo->getAllUsersInRoleByName($role->name));

			$repo->deleteAllForUser($usr->id);

			self::assertCount(0, $repo->getAllUserRoles($usr->id));
			self::assertCount(0, $repo->getAllUsersInRoleByName($role->name));
			self::assertFalse($repo->userInRoleByName($usr->id, $role->name));

			$repo->addUserToRoleByName($usr->id, $role->name);

			self::assertCount(2, $repo->getAllUserRoles($usr->id));

			$repo->removeAllUsersFromRoleByName($role->name);

			self::assertCount(0, $repo->getAllUserRoles($usr->id));

			$repo->addUserToRoleByName($usr->id, $role->name);

			self::assertCount(2, $repo->getAllUserRoles($usr->id));

			$repo->removeUserFromRoleByName($usr->id, $role->name);

			self::assertCount(0, $repo->getAllUserRoles($usr->id));

			$repo->addUserToRoleByName($usr->id, $role->name);

			self::assertCount(2, $repo->getAllUserRoles($usr->id));

			$repo->removeUserFromAllRoles($usr->id);

			self::assertCount(0, $repo->getAllUserRoles($usr->id));

			$role->delete();
			$usr->delete();

			return;
		}
	}

