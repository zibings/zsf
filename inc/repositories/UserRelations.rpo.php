<?php

	namespace Zibings;

	use Stoic\Pdo\BaseDbQueryTypes;
	use Stoic\Pdo\StoicDbClass;

	/**
	 * Repository methods related to UserRelation data.
	 *
	 * @package Zibings
	 */
	class UserRelations extends StoicDbClass {
		/**
		 * Internal UserRelation object.
		 *
		 * @var UserRelation
		 */
		protected $urObj;


		/**
		 * Initializes the internal UserRelation object.
		 *
		 * @return void
		 */
		protected function __initialize() : void {
			$this->urObj = new UserRelation($this->db, $this->log);

			return;
		}

		/**
		 * Returns whether or not the two users are related.
		 *
		 * @param integer $userOne Integer identifier of first potential relation.
		 * @param integer $userTwo Integer identifier of second potential relation.
		 * @return boolean
		 */
		public function areRelated(int $userOne, int $userTwo) : bool {
			$rel = $this->getRelation($userOne, $userTwo);

			if (count($rel) == 2) {
				return true;
			}

			return false;
		}

		/**
		 * Changes the stage of a user relation. If $stage is set to 'INVITE' and users are not yet related, a new relation
		 * will be created.
		 *
		 * @param integer $userOne Integer identifier of first potential relation.
		 * @param integer $userTwo Integer identifier of second potential relation.
		 * @param integer $stage Integer stage specifier.
		 * @return boolean
		 */
		public function changeStage(int $userOne, int $userTwo, int $stage) : bool {
			if ($stage == UserRelationStages::ERROR || !UserRelationStages::validValue($stage)) {
				return false;
			}

			$rel = $this->getRelation($userOne, $userTwo);

			if (count($rel) != 2 && $stage != UserRelationStages::INVITED) {
				return false;
			}

			if (count($rel) != 2 && $stage == UserRelationStages::INVITED) {
				$tmp = new UserRelation($this->db, $this->log);
				$tmp->userOne = $userOne;
				$tmp->userTwo = $userTwo;
				$tmp->origin = true;
				$tmp->stage = new UserRelationStages($stage);

				if ($tmp->create()->isBad()) {
					return false;
				}

				$tmp->userOne = $userTwo;
				$tmp->userTwo = $userOne;
				$tmp->origin = false;

				if ($tmp->create()->isBad()) {
					return false;
				}

				return true;
			}

			if (!$rel[0]->stage->is($rel[1]->stage->getValue())) {
				return false;
			}

			if ($rel[0]->stage->getValue() > UserRelationStages::INVITED && $stage == UserRelationStages::INVITED) {
				return false;
			}

			if ($rel[0]->stage->is($stage)) {
				return true;
			}

			$rel[0]->stage = new UserRelationStages($stage);
			$rel[1]->stage = new UserRelationStages($stage);

			if ($rel[0]->update()->isBad() || $rel[1]->update()->isBad()) {
				return false;
			}

			return true;
		}

		/**
		 * Removes the given relation.
		 *
		 * @param integer $userOne Integer identifier of first potential relation.
		 * @param integer $userTwo Integer identifier of second potential relation.
		 * @return bool
		 */
		public function deleteRelation(int $userOne, int $userTwo) : bool {
			if ($userOne < 1 || $userTwo < 1 || $userOne == $userTwo) {
				return false;
			}

			$this->tryPdoExcept(function () use ($userOne, $userTwo) {
				$stmt = $this->db->prepare("DELETE FROM {$this->urObj->getDbTableName()} WHERE [UserID_One] = :userOne AND [UserID_Two] = :userTwo");
				$stmt->bindParam(':userOne', $userOne, \PDO::PARAM_INT);
				$stmt->bindParam(':userTwo', $userTwo, \PDO::PARAM_INT);
				$stmt->execute();
			}, "Failed to delete user's relation");

			return false;
		}

		/**
		 * Removes all relations for the given user.
		 *
		 * @param integer $userId Integer identifier for user in question.
		 * @return void
		 */
		public function deleteAllForUser(int $userId) : void {
			if ($userId < 1) {
				return;
			}

			$this->tryPdoExcept(function () use ($userId) {
				$stmt = $this->db->prepare("DELETE FROM {$this->urObj->getDbTableName()} WHERE [UserID_One] = :userId OR [UserID_Two] = :userId");
				$stmt->bindParam(':userId', $userId, \PDO::PARAM_INT);
				$stmt->execute();
			}, "Failed to delete user's relations");

			return;
		}

		/**
		 * Returns a list of a user's relations.
		 *
		 * @param integer $userId Integer identifier of user asking about their relations.
		 * @return UserRelation[]
		 */
		public function getRelations(int $userId) {
			$ret = [];

			$this->tryPdoExcept(function () use (&$ret, $userId) {
				$stmt = $this->db->prepare($this->urObj->generateClassQuery(BaseDbQueryTypes::SELECT, false) . " WHERE [UserID_One] = :userId OR [UserID_Two] = :userId ORDER BY [Created] ASC");
				$stmt->bindParam(':userId', $userId, \PDO::PARAM_INT);

				if ($stmt->execute()) {
					while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
						$ret[] = UserRelation::fromArray($row, $this->db, $this->log);
					}
				}
			}, "Failed to get user relations");

			return $ret;
		}

		/**
		 * Returns a list of a user's relations in the requested stage.
		 *
		 * @param integer $userId Integer identifier of user looking for relations.
		 * @param integer $stage Integer stage specifier.
		 * @return UserRelation[]
		 */
		public function getRelationsByStage(int $userId, int $stage) {
			$ret = [];

			if ($stage == UserRelationStages::ERROR || !UserRelationStages::validValue($stage)) {
				return $ret;
			}

			$this->tryPdoExcept(function () use (&$ret, $userId, $stage) {
				$stmt = $this->db->prepare($this->urObj->generateClassQuery(BaseDbQueryTypes::SELECT, false) . " WHERE ([UserID_One] = :userId OR [UserID_Two] = :userId) AND [Stage] = :stage ORDER BY [Created] ASC");
				$stmt->bindParam(':userId', $userId, \PDO::PARAM_INT);
				$stmt->bindParam(':stage', $stage, \PDO::PARAM_INT);

				if ($stmt->execute()) {
					while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
						$ret[] = UserRelation::fromArray($row, $this->db, $this->log);
					}
				}
			}, "Failed to get user relation");

			return $ret;
		}

		/**
		 * Retrieves user relation, if available. Will always have array ordered with 'origin' user as first element.
		 *
		 * @param integer $userOne Integer identifier of the first potential relation.
		 * @param integer $userTwo Integer identifier of the second potential relation.
		 * @return UserRelation[]
		 */
		public function getRelation(int $userOne, int $userTwo) {
			$ret = [];

			$this->tryPdoExcept(function () use (&$ret, $userOne, $userTwo, $stage) {
				$stmt = $this->db->prepare($this->urObj->generateClassQuery(BaseDbQueryTypes::SELECT, false) . " WHERE ([UserID_One] = :userOne AND [UserID_Two] = :userTwo) OR ([UserID_One] = :userTwo AND [UserID_Two] = :userOne) ORDER BY [Origin] DESC");
				$stmt->bindParam(':userOne', $userOne, \PDO::PARAM_INT);
				$stmt->bindParam(':userTwo', $userTwo, \PDO::PARAM_INT);

				if ($stmt->execute()) {
					$rows = [];

					while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
						$rows[] = UserRelation::fromArray($row, $this->db, $this->log);
					}

					if (count($rows) == 2) {
						$ret = $rows;
					}
				}
			}, "Failed to get user relation");

			return $ret;
		}

		/**
		 * Retrieves a relation's stage, if present. Stage with null value returned if not found.
		 *
		 * @param integer $userOne Integer identifier of first potential relation.
		 * @param integer $userTwo Integer identifier of second potential relation.
		 * @return UserRelationStages
		 */
		public function getRelationStage(int $userOne, int $userTwo) : UserRelationStages {
			$rel = $this->getRelation($userOne, $userTwo);

			if (count($rel) != 2 || $rel[0]->stage->getValue() !== $rel[1]->stage->getValue()) {
				return new UserRelationStages();
			}

			return $rel[0]->stage;
		}

		/**
		 * Retrieves relations of user by other users, if available.
		 *
		 * @param integer $userId Integer identifier of user looking for relations.
		 * @return UserRelation[]
		 */
		public function getIncomingRelations(int $userId) {
			$ret = [];

			$this->tryPdoExcept(function () use (&$ret, $userId) {
				$stmt = $this->db->prepare($this->urObj->generateClassQuery(BaseDbQueryTypes::SELECT, false) . " WHERE [UserID_Two] = :userId ORDER BY [Created] ASC");
				$stmt->bindParam(':userId', $userId, \PDO::PARAM_INT);

				if ($stmt->execute()) {
					while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
						$ret[] = UserRelation::fromArray($row, $this->db, $this->log);
					}
				}
			}, "Failed to get user relation");

			return $ret;
		}

		/**
		 * Retrieves relations of user by other users, if available, filtered by stage.
		 *
		 * @param integer $userId Integer identifier of user looking for relations.
		 * @param integer $stage Integer stage specifier to filter relations through.
		 * @return UserRelation[]
		 */
		public function getIncomingRelationsByStage(int $userId, int $stage) {
			$ret = [];

			if ($stage == UserRelationStages::ERROR || !UserRelationStages::validValue($stage)) {
				return $ret;
			}

			$this->tryPdoExcept(function () use (&$ret, $userId, $stage) {
				$stmt = $this->db->prepare($this->urObj->generateClassQuery(BaseDbQueryTypes::SELECT, false) . " WHERE [UserID_Two] = :userId AND [Stage] = :stage ORDER BY [Created] ASC");
				$stmt->bindParam(':userId', $userId, \PDO::PARAM_INT);
				$stmt->bindParam(':stage', $stage, \PDO::PARAM_INT);

				if ($stmt->execute()) {
					while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
						$ret[] = UserRelation::fromArray($row, $this->db, $this->log);
					}
				}
			}, "Failed to get user relation");

			return $ret;
		}

		/**
		 * Retrieves relations of user by other users, if available, except in the given stage.
		 *
		 * @param integer $userId Integer identifier of user looking for relations.
		 * @param integer $stage Integer stage specifier to filter relations through.
		 * @return UserRelation[]
		 */
		public function getIncomingRelationsExceptingStage(int $userId, int $stage) {
			$ret = [];

			if ($stage == UserRelationStages::ERROR || !UserRelationStages::validValue($stage)) {
				return $ret;
			}

			$this->tryPdoExcept(function () use (&$ret, $userId, $stage) {
				$stmt = $this->db->prepare($this->urObj->generateClassQuery(BaseDbQueryTypes::SELECT, false) . " WHERE [UserID_Two] = :userId AND [Stage] != :stage ORDER BY [Created] ASC");
				$stmt->bindParam(':userId', $userId, \PDO::PARAM_INT);
				$stmt->bindParam(':stage', $stage, \PDO::PARAM_INT);

				if ($stmt->execute()) {
					while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
						$ret[] = UserRelation::fromArray($row, $this->db, $this->log);
					}
				}
			}, "Failed to get user relation");

			return $ret;
		}

		/**
		 * Retrieves relations by user to other users, if available.
		 *
		 * @param integer $userId Integer identifier of user looking for relations.
		 * @return UserRelation[]
		 */
		public function getOutgoingRelations(int $userId) {
			$ret = [];

			$this->tryPdoExcept(function () use (&$ret, $userId) {
				$stmt = $this->db->prepare($this->urObj->generateClassQuery(BaseDbQueryTypes::SELECT, false) . " WHERE [UserID_One] = :userId ORDER BY [Created] ASC");
				$stmt->bindParam(':userId', $userId, \PDO::PARAM_INT);

				if ($stmt->execute()) {
					while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
						$ret[] = UserRelation::fromArray($row, $this->db, $this->log);
					}
				}
			}, "Failed to get user relation");

			return $ret;
		}

		/**
		 * Retrieves by user to other users, if available, filtered by stage.
		 *
		 * @param integer $userId Integer identifier of user looking for relations.
		 * @param integer $stage Integer stage specifier to filter relations through.
		 * @return array
		 */
		public function getOutgoingRelationsByStage(int $userId, int $stage) {
			$ret = [];

			if ($stage == UserRelationStages::ERROR || !UserRelationStages::validValue($stage)) {
				return $ret;
			}

			$this->tryPdoExcept(function () use (&$ret, $userId, $stage) {
				$stmt = $this->db->prepare($this->urObj->generateClassQuery(BaseDbQueryTypes::SELECT, false) . " WHERE [UserID_One] = :userId AND [Stage] = :stage ORDER BY [Created] ASC");
				$stmt->bindParam(':userId', $userId, \PDO::PARAM_INT);
				$stmt->bindParam(':stage', $stage, \PDO::PARAM_INT);

				if ($stmt->execute()) {
					while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
						$ret[] = UserRelation::fromArray($row, $this->db, $this->log);
					}
				}
			}, "Failed to get user relation");

			return $ret;
		}

		/**
		 * Retrieves by user to other users, if available, except in the given stage.
		 *
		 * @param integer $userId Integer identifier of user looking for relations.
		 * @param integer $stage Integer stage specifier to filter relations through.
		 * @return array
		 */
		public function getOutgoingRelationsExceptingStage(int $userId, int $stage) {
			$ret = [];

			if ($stage == UserRelationStages::ERROR || !UserRelationStages::validValue($stage)) {
				return $ret;
			}

			$this->tryPdoExcept(function () use (&$ret, $userId, $stage) {
				$stmt = $this->db->prepare($this->urObj->generateClassQuery(BaseDbQueryTypes::SELECT, false) . " WHERE [UserID_One] = :userId AND [Stage] != :stage ORDER BY [Created] ASC");
				$stmt->bindParam(':userId', $userId, \PDO::PARAM_INT);
				$stmt->bindParam(':stage', $stage, \PDO::PARAM_INT);

				if ($stmt->execute()) {
					while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
						$ret[] = UserRelation::fromArray($row, $this->db, $this->log);
					}
				}
			}, "Failed to get user relation");

			return $ret;
		}
	}
