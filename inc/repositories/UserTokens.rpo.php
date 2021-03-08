<?php

	namespace Zibings;

	use Stoic\Pdo\StoicDbClass;

	/**
	 * Repository methods for dealing with UserToken data.
	 *
	 * @package Zibings
	 */
	class UserTokens extends StoicDbClass {
		/**
		 * Internal UserToken instance.
		 *
		 * @var UserToken
		 */
		protected $utObj;


		/**
		 * Initializes the internal UserToken instance.
		 *
		 * @return void
		 */
		protected function __initialize() : void {
			$this->utObj = new UserToken($this->db, $this->log);

			return;
		}

		/**
		 * Removes all tokens for the given user.
		 *
		 * @param integer $userId Integer identifier for user in question.
		 * @return void
		 */
		public function deleteAllForUser(int $userId) : void {
			if ($userId < 1) {
				return;
			}

			$this->tryPdoExcept(function () use ($userId) {
				$stmt = $this->db->prepare("DELETE FROM {$this->utObj->getDbTableName()} WHERE [UserID] = :userId");
				$stmt->bindParam(':userId', $userId, \PDO::PARAM_INT);
				$stmt->execute();
			}, "Failed to delete user's contacts");

			return;
		}
	}
