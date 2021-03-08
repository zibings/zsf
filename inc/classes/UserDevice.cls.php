<?php

	namespace Zibings;

	use Stoic\Log\Logger;
	use Stoic\Pdo\BaseDbQueryTypes;
	use Stoic\Pdo\BaseDbTypes;
	use Stoic\Pdo\PdoHelper;
	use Stoic\Pdo\StoicDbModel;

	/**
	 * Class for representing a single device associated with a user.
	 *
	 * @package Zibings
	 */
	class UserDevice extends StoicDbModel {
		/**
		 * Date and time the device was created in the system.
		 *
		 * @var \DateTimeInterface
		 */
		public $created;
		/**
		 * Unique integer identifier of the device.
		 *
		 * @var integer
		 */
		public $id;
		/**
		 * String 'identifier' for device, used for distinguishing the type of device.
		 *
		 * @var string
		 */
		public $identifier;
		/**
		 * Last date and time the device was active on the platform.
		 *
		 * @var \DateTimeInterface
		 */
		public $lastActive;
		/**
		 * Date and time the device was successfully linked.
		 *
		 * @var \DateTimeInterface
		 */
		public $linked;
		/**
		 * String phrase to use when linking device w/o user identifier.
		 *
		 * @var string
		 */
		public $linkPhrase;
		/**
		 * Integer identifier of the user who owns this device.  Initial value is set to 0 before a user claims the device.
		 *
		 * @var integer
		 */
		public $userId;


		/**
		 * Static method to retrieve a device by ID.
		 *
		 * @param integer $id Integer identifier of device.
		 * @param PdoHelper $db PdoHelper instance for internal use.
		 * @param null|Logger $log Optional Logger instance for internal use, new instance created if not supplied.
		 * @return UserDevice
		 */
		public static function fromId(int $id, PdoHelper $db, Logger $log = null) : UserDevice {
			$ret = new UserDevice($db, $log);

			if ($id > 0) {
				$ret->id = $id;

				if ($ret->read()->isBad()) {
					$ret->id = 0;
				}
			}

			return $ret;
		}

		/**
		 * Static method to retrieve a device by its link phrase.
		 *
		 * @param string $phrase The link phrase to search for in the database.
		 * @param PdoHelper $db PdoHelper instance for internal use.
		 * @param null|Logger $log Optional Logger instance for internal use, new instance created if not supplied.
		 * @return UserDevice
		 */
		public static function fromLinkPhrase(string $phrase, PdoHelper $db, Logger $log = null) : UserDevice {
			$ret = new UserDevice($db, $log);

			if (empty($phrase)) {
				return $ret;
			}

			$ret->tryPdoExcept(function () use (&$ret, $phrase) {
				$stmt = $ret->db->prepare($ret->generateClassQuery(BaseDbQueryTypes::SELECT, false) . " WHERE [LinkPhrase] = :linkPhrase");
				$stmt->bindParam(':linkPhrase', $phrase, \PDO::PARAM_STR);

				if ($stmt->execute()) {
					$ret = UserDevice::fromArray($stmt->fetch(\PDO::FETCH_ASSOC), $ret->db, $ret->log);
				}
			}, "Failed to get user device by link phrase");

			return $ret;
		}


		/**
		 * Determines if the system should attempt to create a UserDevice in the database.
		 *
		 * @return boolean
		 */
		protected function __canCreate() {
			if ($this->id > 0 || empty($this->linkPhrase) || empty($this->identifier)) {
				return false;
			}

			$this->created = new \DateTimeImmutable('now', new \DateTimeZone('UTC'));

			return true;
		}
		
		/**
		 * Determines if the system should attempt to delete a UserDevice from the database.
		 *
		 * @return boolean
		 */
		protected function __canDelete() {
			if ($this->id < 1) {
				return false;
			}

			return true;
		}
		
		/**
		 * Determines if the system should attempt to read a UserDevice from the database.
		 *
		 * @return boolean
		 */
		protected function __canRead() {
			if ($this->id < 1) {
				return false;
			}

			return true;
		}
		
		/**
		 * Determines if the system should attempt to update a UserDevice in the database.
		 *
		 * @return boolean
		 */
		protected function __canUpdate() {
			if ($this->id < 1) {
				return false;
			}

			return true;
		}
		
		/**
		 * Initializes a new UserDevice object.
		 *
		 * @return void
		 */
		protected function __setupModel() : void {
			$this->setTableName('[UserDevice]');
			$this->setColumn('created', '[Created]', BaseDbTypes::DATETIME, false, true, false);
			$this->setColumn('id', '[ID]', BaseDbTypes::INTEGER, true, false, false, false, true);
			$this->setColumn('identifier', '[Identifier]', BaseDbTypes::STRING, false, true, false);
			$this->setColumn('lastActive', '[LastActive]', BaseDbTypes::DATETIME, false, false, true, true);
			$this->setColumn('linked', '[Linked]', BaseDbTypes::DATETIME, false, false, true, true);
			$this->setColumn('linkPhrase', '[LinkPhrase]', BaseDbTypes::STRING, false, true, false);
			$this->setColumn('userId', '[UserID]', BaseDbTypes::INTEGER, false, true, true);

			$this->created    = new \DateTimeImmutable('now', new \DateTimeZone('UTC'));
			$this->id         = 0;
			$this->identifier = '';
			$this->lastActive = new \DateTimeImmutable('now', new \DateTimeZone('UTC'));
			$this->linked     = new \DateTimeImmutable('now', new \DateTimeZone('UTC'));
			$this->linkPhrase = '';
			$this->userId     = 0;
			
			return;
		}

		/**
		 * Helper method to update the lastActive property.
		 *
		 * @return void
		 */
		public function touch() : void {
			$this->lastActive = new \DateTimeImmutable('now', new \DateTimeZone('UTC'));
			$this->update();

			return;
		}
	}
