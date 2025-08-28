<?php

	namespace Zibings;

	use Stoic\Pdo\BaseDbQueryTypes;
	use Stoic\Pdo\PdoDrivers;
	use Stoic\Pdo\PdoHelper;
	use Stoic\Pdo\StoicDbClass;

	/**
	 * Repository methods for dealing with UserSession data.
	 *
	 * @package Zibings
	 */
	class UserSessions extends StoicDbClass {
		const string SQL_DELFORUSER = 'usersessions-deleteforuser';


		/**
		 * Internal UserSession instance.
		 *
		 * @var UserSession
		 */
		protected UserSession $usObj;


		/**
		 * Whether the stored queries have been initialized.
		 *
		 * @var bool
		 */
		private static bool $dbInitialized = false;


		/**
		 * Initializes the internal UserSession instance.
		 *
		 * @return void
		 */
		protected function __initialize() : void {
			$this->usObj = new UserSession($this->db, $this->log);

			if (!static::$dbInitialized) {
				PdoHelper::storeQuery(PdoDrivers::PDO_SQLSRV, self::SQL_DELFORUSER, "DELETE FROM {$this->usObj->getDbTableName()} WHERE [UserID] = :userId");
				PdoHelper::storeQuery(PdoDrivers::PDO_MYSQL,  self::SQL_DELFORUSER, "DELETE FROM {$this->usObj->getDbTableName()} WHERE `UserID` = :userId");
				PdoHelper::storeQuery(PdoDrivers::PDO_PGSQL,  self::SQL_DELFORUSER, "DELETE FROM {$this->usObj->getDbTableName()} WHERE \"UserID\" = :userId");

				static::$dbInitialized = true;
			}

			return;
		}

		/**
		 * Retrieves all available sessions for the given user.
		 *
		 * @param int $userId Integer identifier for user in question.
		 * @return UserSession[]
		 */
		public function getAllForUser(int $userId) : array {
			$ret = [];
			$this->tryPdoExcept(function () use (&$ret, $userId) {
				$sql = $this->usObj->generateClassQuery(BaseDbQueryTypes::SELECT, false);

				if ($this->db->getDriver()->is(PdoDrivers::PDO_SQLSRV)) {
					$sql .= " WHERE [UserID] = :userId";
				} else if ($this->db->getDriver()->is(PdoDrivers::PDO_MYSQL)) {
					$sql .= " WHERE `UserID` = :userId";
				} else {
					$sql .= " WHERE \"UserID\" = :userId";
				}

				$stmt = $this->db->prepare($sql);
				$stmt->bindParam(':userId', $userId, \PDO::PARAM_INT);
				$stmt->execute();

				while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
					$ret[] = UserSession::fromArray($row, $this->db, $this->log);
				}

				return;
			}, "Failed to retrieve user sessions");

			return $ret;
		}

		/**
		 * Removes all sessions for the given user.
		 *
		 * @param int $userId Integer identifier for user in question.
		 * @return void
		 */
		public function deleteAllForUser(int $userId) : void {
			if ($userId < 1) {
				return;
			}

			$this->tryPdoExcept(function () use ($userId) {
				$stmt = $this->db->prepareStored(self::SQL_DELFORUSER);
				$stmt->bindParam(':userId', $userId);
				$stmt->execute();

				return;
			}, "Failed to delete user's contacts");

			return;
		}
	}
