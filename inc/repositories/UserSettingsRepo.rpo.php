<?php

	namespace Zibings;

	use Stoic\Pdo\StoicDbClass;

	/**
	 * Repository methods for dealing with UserSettings data.
	 *
	 * @package Zibings
	 */
	class UserSettingsRepo extends StoicDbClass {
		/**
		 * Internal UserSettings instance.
		 *
		 * @var UserSettings
		 */
		protected $usObj;


		/**
		 * Initializes the internal UserSettings instance.
		 *
		 * @return void
		 */
		protected function __initialize() : void {
			$this->usObj = new UserSettings($this->db, $this->log);

			return;
		}

		/**
		 * Removes all settings for the given user.
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
