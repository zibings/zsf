<?php

	namespace Zibings;

	use Stoic\Log\Logger;
	use Stoic\Pdo\PdoHelper;
	use Stoic\Pdo\StoicDbModel;

	/**
	 * Class for representing a single session for a user.
	 *
	 * @package Zibings
	 */
	class UserSessionMeta extends StoicDbModel {
		public string $address;
		public \DateTimeInterface $created;
		public string $hostname;
		public int $id;
		public string $token;
		public int $userId;

		/**
		 * Static method to retrieve a session by its integer identifier.
		 *
		 * @param int $id Integer identifier of session.
		 * @param PdoHelper $db PdoHelper instance for internal use.
		 * @param Logger|null $log Optional Logger instance for internal use, new instance created by default.
		 * @throws \Exception
		 * @return UserSession
		 */
		public static function fromId(int $id, PdoHelper $db, Logger $log = null) : UserSession {
			$ret = new UserSession($db, $log);
			$ret->id = $id;

			if ($ret->read()->isBad()) {
				$ret->id = 0;
			}

			return $ret;
		}

		/**
		 * Static method to retrieve a session by its string identifier.
		 *
		 * @param string      $token String identifier of session.
		 * @param PdoHelper   $db    PdoHelper instance for internal use.
		 * @param Logger|null $log   Optional Logger instance for internal use, new instance created by default.
		 * @return UserSession
		 * @throws \Exception
		 */
		public static function fromToken(string $token, PdoHelper $db, Logger $log = null) : UserSession {
			$ret = new UserSession($db, $log);

			if (empty($token)) {
				return $ret;
			}

			$ret->token = $token;
			if ($ret->read()->isBad()) {
				die("Failed to get session from token");
			}


			return $ret;
		}
	}
