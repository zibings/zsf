<?php

	namespace Zibings;

	use Stoic\Pdo\PdoDrivers;
	use Stoic\Pdo\PdoHelper;
	use Stoic\Pdo\StoicDbClass;

	/**
	 * Repository methods for dealing with UserToken data.
	 *
	 * @package Zibings
	 */
	class UserTokens extends StoicDbClass {
		const string SQL_COUNTFORUSER = 'usertokens-countforuser';
		const string SQL_DELFORUSER   = 'usertokens-deleteforuser';


		/**
		 * Internal UserToken instance.
		 *
		 * @var UserToken
		 */
		protected UserToken $utObj;


		/**
		 * Whether the stored queries have been initialized.
		 *
		 * @var bool
		 */
		private static bool $dbInitialized = false;


		/**
		 * Initializes the internal UserToken instance.
		 *
		 * @return void
		 */
		protected function __initialize() : void {
			$this->utObj = new UserToken($this->db, $this->log);

			if (!static::$dbInitialized) {
				PdoHelper::storeQuery(PdoDrivers::PDO_SQLSRV, self::SQL_COUNTFORUSER, "SELECT COUNT(*) FROM {$this->utObj->getDbTableName()} WHERE [UserID] = :userId");
				PdoHelper::storeQuery(PdoDrivers::PDO_MYSQL,  self::SQL_COUNTFORUSER, "SELECT COUNT(*) FROM {$this->utObj->getDbTableName()} WHERE `UserID` = :userId");
				PdoHelper::storeQuery(PdoDrivers::PDO_PGSQL,  self::SQL_COUNTFORUSER, "SELECT COUNT(*) FROM {$this->utObj->getDbTableName()} WHERE \"UserID\" = :userId");

				PdoHelper::storeQuery(PdoDrivers::PDO_SQLSRV, self::SQL_DELFORUSER, "DELETE FROM {$this->utObj->getDbTableName()} WHERE [UserID] = :userId");
				PdoHelper::storeQuery(PdoDrivers::PDO_MYSQL,  self::SQL_DELFORUSER, "DELETE FROM {$this->utObj->getDbTableName()} WHERE `UserID` = :userId");
				PdoHelper::storeQuery(PdoDrivers::PDO_PGSQL,  self::SQL_DELFORUSER, "DELETE FROM {$this->utObj->getDbTableName()} WHERE \"UserID\" = :userId");

				static::$dbInitialized = true;
			}

			return;
		}

		/**
		 * Retrieves number of tokens for the given user.
		 *
		 * @param int $userId Integer identifier for user in question.
		 * @return int
		 */
		public function getNumTokensForUser(int $userId) : int {
			$ret = 0;
			$this->tryPdoExcept(function () use (&$ret, $userId) {
				$stmt = $this->db->prepareStored(self::SQL_COUNTFORUSER);
				$stmt->bindParam(':userId', $userId, \PDO::PARAM_INT);
				$stmt->execute();

				while ($row = $stmt->fetch()) {
					$ret = intval($row[0]);
				}

				return;
			}, "Failed to get user token count");

			return $ret;
		}

		/**
		 * Removes all tokens for the given user.
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
				$stmt->bindParam(':userId', $userId, \PDO::PARAM_INT);
				$stmt->execute();

				return;
			}, "Failed to delete user's contacts");

			return;
		}
	}
