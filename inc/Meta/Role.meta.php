<?php

	namespace Zibings;

	use Stoic\Log\Logger;
	use Stoic\Pdo\PdoHelper;
	use Stoic\Pdo\StoicDbModel;


	/**
	 * Class for representing an access control role within the system.
	 *
	 * @package Zibings
	 */
	class RoleMeta extends StoicDbModel {
		public \DateTimeInterface $created;
		public int $id;
		public string $name;


		/**
		 * Static method to retrieve a role from the database using its identifier. Returns an empty Role object if no role is
		 * found.
		 *
		 * @param int $id Integer identifier of role to retrieve from database.
		 * @param PdoHelper $db PdoHelper instance for internal use.
		 * @param Logger|null $log Optional Logger instance for internal use, new instance created if not supplied.
		 * @throws \Exception
		 * @return Role
		 */
		public static function fromId(int $id, PdoHelper $db, Logger $log = null) : Role {
			$ret = new Role($db, $log);
			if ($id <= 0) {
				return $ret;
			}

			$ret->id = $id;

			if ($ret->read()->isBad()) {
				$ret->id = 0;
			}

			return $ret;
		}

		/**
		 * Static method to retrieve a role from the database using its name. Returns an empty Role object if no role is found.
		 *
		 * @param string      $name Friendly name of role to retrieve from database.
		 * @param PdoHelper   $db   PdoHelper instance for internal use.
		 * @param Logger|null $log  Optional Logger instance for internal use, new instance created if not supplied.
		 * @return Role
		 * @throws \Exception
		 */
		public static function fromName(string $name, PdoHelper $db, Logger $log = null) : RoleMeta {
			$ret = new RoleMeta($db, $log);

			if (empty($name)) {
				return $ret;
			}

			$ret->name = $name;
			if ($ret->read()->isBad()) {
				die("Failed to retrieve role from database");
			}

			return $ret;
		}
	}
