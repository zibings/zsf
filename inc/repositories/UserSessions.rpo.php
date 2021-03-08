<?php

	namespace Zibings;

	use Stoic\Pdo\StoicDbClass;

	/**
	 * Repository methods for dealing with UserSession data.
	 *
	 * @package Zibings
	 */
	class UserSessions extends StoicDbClass {
		/**
		 * Internal UserSession instance.
		 *
		 * @var UserSession
		 */
		protected $usObj;


		/**
		 * Initializes the internal UserSession instance.
		 *
		 * @return void
		 */
		protected function __initialize() : void {
			$this->usObj = new UserSession($this->db, $this->log);

			return;
		}

		/**
		 * Removes all sessions for the given user.
		 *
		 * @param integer $userId Integer identifier for user in question.
		 * @return void
		 */
		public function deleteAllForUser(int $userId) : void {
			if ($userId < 1) {
				return;
			}

			$this->tryPdoExcept(function () use ($userId) {
				$stmt = $this->db->prepare("DELETE FROM {$this->usObj->getDbTableName()} WHERE [UserID] = :userId");
				$stmt->bindParam(':userId', $userId, \PDO::PARAM_INT);
				$stmt->execute();
			}, "Failed to delete user's contacts");

			return;
		}
	}
