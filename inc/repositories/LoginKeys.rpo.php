<?php

	namespace Zibings;

	use Stoic\Pdo\StoicDbClass;

	/**
	 * Repository methods for dealing with LoginKey data.
	 *
	 * @package Zibings
	 */
	class LoginKeys extends StoicDbClass {
		/**
		 * Internal LoginKey instance.
		 *
		 * @var LoginKey
		 */
		protected $lkObj;


		/**
		 * Initializes the internal LoginKey instance.
		 *
		 * @return void
		 */
		protected function __initialize() : void {
			$this->lkObj = new LoginKey($this->db, $this->log);

			return;
		}

		/**
		 * Removes all login keys for the given user.
		 *
		 * @param integer $userId Integer identifier for user in question.
		 * @return void
		 */
		public function deleteAllForUser(int $userId) : void {
			if ($userId < 1) {
				return;
			}

			$this->tryPdoExcept(function () use ($userId) {
				$stmt = $this->db->prepare("DELETE FROM {$this->lkObj->getDbTableName()} WHERE [UserID] = :userId");
				$stmt->bindParam(':userId', $userId, \PDO::PARAM_INT);
				$stmt->execute();
			}, "Failed to delete user's contacts");

			return;
		}
	}
