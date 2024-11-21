<?php

	namespace Zibings;

	use Stoic\Pdo\BaseDbQueryTypes;
	use Stoic\Pdo\PdoDrivers;
	use Stoic\Pdo\StoicDbClass;
	
	/**
	 * Repository methods related to the UserAuthHistory table/data.
	 *
	 * @package Zibings
	 */
	class UserAuthHistories extends StoicDbClass {
		/**
		 * Internal instance of a UserAuthHistory object, used for query generation.
		 *
		 * @var UserAuthHistory
		 */
		protected UserAuthHistory $uahObj;


		/**
		 * Initializes the internal UserAuthHistory object.
		 *
		 * @return void
		 */
		protected function __initialize() : void {
			$this->uahObj = new UserAuthHistory($this->db, $this->log);

			return;
		}

		/**
		 * Removes any auth histories present for the given user.
		 *
		 * @param int $userId Integer identifier for the user in question.
		 * @return void
		 */
		public function deleteAllForUser(int $userId) : void {
			$this->tryPdoExcept(function () use ($userId) {
				$sql = $this->uahObj->generateClassQuery(BaseDbQueryTypes::DELETE, false);

				if ($this->db->getDriver()->is(PdoDrivers::PDO_SQLSRV)) {
					$sql .= " WHERE [UserID] = :userId";
				} else if ($this->db->getDriver()->is(PdoDrivers::PDO_MYSQL)) {
					$sql .= " WHERE `UserID` = :userId";
				} else {
					$sql .= " WHERE \"UserID\" = :userId";
				}

				$stmt = $this->db->prepare($sql);
				$stmt->bindValue(':userId', $userId, \PDO::PARAM_INT);
				$stmt->execute();

				return;
			}, "Failed to delete user auth histories");

			return;
		}

		/**
		 * Retrieves all or a subset of a user's authentication history.
		 *
		 * @param int $userId Integer identifier for the user in question.
		 * @param int|null $offset Optional offset, only used if supplied alongside a valid (> 0) limit.
		 * @param int|null $limit Optional limit, only used if supplied alongside a valid (>= 0) offset.
		 * @return UserAuthHistory[]
		 */
		public function getUserAuthHistory(int $userId, ?int $offset = null, ?int $limit = null) : array {
			$ret = [];
			$sql = $this->uahObj->generateClassQuery(BaseDbQueryTypes::SELECT, false);

			if ($this->db->getDriver()->is(PdoDrivers::PDO_SQLSRV)) {
				$sql .= " WHERE [UserID] = :userId ORDER BY [Recorded] DESC";

				if ($offset !== null && $offset >= 0 && $limit === null && $limit > 0) {
					$sql .= " OFFSET {$offset} ROWS FETCH NEXT {$limit} ROWS ONLY";
				}
			} else if ($this->db->getDriver()->is(PdoDrivers::PDO_MYSQL)) {
				$sql .= " WHERE `UserID` = :userId ORDER BY `Recorded` DESC";

				if ($offset !== null && $offset >= 0 && $limit === null && $limit > 0) {
					$sql .= " LIMIT {$offset}, {$limit}";
				}
			} else {
				$sql .= " WHERE \"UserID\" = :userId ORDER BY \"Recorded\" DESC";

				if ($offset !== null && $offset >= 0 && $limit === null && $limit > 0) {
					$sql .= " LIMIT {$offset} OFFSET {$limit}";
				}
			}

			$this->tryPdoExcept(function () use (&$ret, $sql, $userId) {
				$stmt = $this->db->prepare($sql);
				$stmt->bindParam(':userId', $userId);

				if ($stmt->execute()) {
					while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
						$ret[] = UserAuthHistory::fromArray($row, $this->db, $this->log);
					}
				}

				return;
			}, "Failed to retrieve user histories");

			return $ret;
		}

		/**
		 * Retrieves the number of authentication history records that exist for the given user.
		 *
		 * @param int $userId Integer identifier for the user in question.
		 * @return int
		 */
		public function getUserAuthHistoryCount(int $userId) : int {
			$ret = 0;

			$this->tryPdoExcept(function () use (&$ret, $userId) {
				$sql = "SELECT COUNT(*) FROM {$this->uahObj->getDbTableName()}";

				if ($this->db->getDriver()->is(PdoDrivers::PDO_SQLSRV)) {
					$sql .= " WHERE [UserID] = :userId";
				} else if ($this->db->getDriver()->is(PdoDrivers::PDO_MYSQL)) {
					$sql .= " WHERE `UserID` = :userId";
				} else {
					$sql .= " WHERE \"UserID\" = :userId";
				}

				$stmt = $this->db->prepare($sql);
				$stmt->bindParam(':userId', $userId, \PDO::PARAM_INT);

				if ($stmt->execute()) {
					$ret = intval($stmt->fetch()[0]);
				}

				return;
			}, "Failed to get user auth history count");

			return $ret;
		}
	}
