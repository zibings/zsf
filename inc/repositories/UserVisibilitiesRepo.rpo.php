<?php

	namespace Zibings;

	use Stoic\Pdo\StoicDbClass;

	/**
	 * Repository methods for dealing with UserVisibilities data.
	 *
	 * @package Zibings
	 */
	class UserVisibilitiesRepo extends StoicDbClass {
		/**
		 * Internal UserVisibilities instance.
		 *
		 * @var UserVisibilities
		 */
		protected $uvObj;


		/**
		 * Initializes the internal UserVisibilities instance.
		 *
		 * @return void
		 */
		protected function __initialize() : void {
			$this->uvObj = new UserVisibilities($this->db, $this->log);

			return;
		}

		/**
		 * Removes all visibilities for the given user.
		 *
		 * @param integer $userId Integer identifier for user in question.
		 * @return void
		 */
		public function deleteAllForUser(int $userId) : void {
			if ($userId < 1) {
				return;
			}

			$this->tryPdoExcept(function () use ($userId) {
				$stmt = $this->db->prepare("DELETE FROM {$this->uvObj->getDbTableName()} WHERE [UserID] = :userId");
				$stmt->bindParam(':userId', $userId, \PDO::PARAM_INT);
				$stmt->execute();
			}, "Failed to delete user's contacts");

			return;
		}
	}
