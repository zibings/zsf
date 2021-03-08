<?php

	namespace Zibings;

	use Stoic\Pdo\StoicDbClass;

	/**
	 * Repository methods for dealing with UserProfile data.
	 *
	 * @package Zibings
	 */
	class UserProfiles extends StoicDbClass {
		/**
		 * Internal UserProfile instance.
		 *
		 * @var UserProfile
		 */
		protected $upObj;


		/**
		 * Initializes the internal UserProfile instance.
		 *
		 * @return void
		 */
		protected function __initialize() : void {
			$this->upObj = new UserProfile($this->db, $this->log);

			return;
		}

		/**
		 * Removes all profile(s) for the given user.
		 *
		 * @param integer $userId Integer identifier for user in question.
		 * @return void
		 */
		public function deleteAllForUser(int $userId) : void {
			if ($userId < 1) {
				return;
			}

			$this->tryPdoExcept(function () use ($userId) {
				$stmt = $this->db->prepare("DELETE FROM {$this->upObj->getDbTableName()} WHERE [UserID] = :userId");
				$stmt->bindParam(':userId', $userId, \PDO::PARAM_INT);
				$stmt->execute();
			}, "Failed to delete user's contacts");

			return;
		}
	}
