<?php

	namespace Zibings;

	use Stoic\Pdo\StoicDbClass;

	/**
	 * Repository methods related to the UserDevice table/data.
	 *
	 * @package Zibings
	 */
	class UserDevices extends StoicDbClass {
		/**
		 * Internal instance of a UserDevice object, used for query generation.
		 *
		 * @var UserDevice
		 */
		protected $udObj;


		/**
		 * Initializes the internal UserDevice object.
		 *
		 * @return void
		 */
		protected function __initialize() : void {
			$this->udObj = new UserDevice($this->db, $this->log);

			return;
		}

		/**
		 * Removes all devices for the given user.
		 *
		 * @param int $userId Integer identifier for user.
		 * @return void
		 */
		public function deleteAllForUser(int $userId) : void {
			if ($userId < 1) {
				return;
			}

			$this->tryPdoExcept(function () use ($userId) {
				$stmt = $this->db->prepare("DELETE FROM {$this->udObj->getDbTableName()} WHERE [UserID] = :userId");
				$stmt->bindParam(':userId', $userId, \PDO::PARAM_INT);
				$stmt->execute();
			}, "Failed to delete user's devices");

			return;
		}
	}
