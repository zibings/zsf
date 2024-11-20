<?php

	use Zibings\Role;
	use Zibings\Roles;

	class RolesRepoTest extends ZsfTestCase {
		public function test_getAll() {
			$role = new Role(self::$db, self::$log);
			$repo = new Roles(self::$db, self::$log);
			$origCount = count($repo->getAll());

			$role->name = uniqid();
			$role->create();

			self::assertNotCount($origCount, $repo->getAll());

			$role->delete();

			self::assertCount($origCount, $repo->getAll());

			return;
		}
	}
