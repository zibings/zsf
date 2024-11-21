<?php

	namespace Zibings;

	use Stoic\Pdo\BaseDbColumnFlags as BCF;
	use Stoic\Pdo\BaseDbTypes;
	use Stoic\Pdo\PdoDrivers;
	use Stoic\Pdo\StoicDbModel;
	use Stoic\Utilities\EnumBase;
	use Stoic\Utilities\ReturnHelper;

	/**
	 * Different contact types.
	 *
	 * @package Zibings
	 */
	class UserContactTypes extends EnumBase {
		const EMAIL    = 1;
		const PHONE    = 2;
		const TWITTER  = 3;
		const WEBSITE  = 4;
	}

	/**
	 * Class for representing user contact entries.
	 *
	 * @package Zibings
	 */
	class UserContact extends StoicDbModel {
		public \DateTimeInterface $created;
		public bool $primary;
		public UserContactTypes $type;
		public int $userId;
		public string $value;


		/**
		 * Determines if the system should attempt to create a new UserContact in the database.
		 *
		 * @throws \Exception
		 * @return bool|ReturnHelper
		 */
		protected function __canCreate() : bool|ReturnHelper {
			if ($this->userId < 1 || empty($this->value) || $this->type->getValue() === null) {
				return false;
			}

			$this->created = new \DateTimeImmutable('now', new \DateTimeZone('UTC'));

			return true;
		}

		/**
		 * Determines if the system should attempt to delete a UserContact from the database.
		 *
		 * @return bool|ReturnHelper
		 */
		protected function __canDelete() : bool|ReturnHelper {
			if ($this->userId < 1) {
				return false;
			}

			return true;
		}

		/**
		 * Disabled for this model.
		 *
		 * @return bool|ReturnHelper
		 */
		protected function __canRead() : bool|ReturnHelper {
			if ($this->userId < 1) {
				return false;
			}

			return true;
		}

		/**
		 * Disabled for this model.
		 *
		 * @return bool|ReturnHelper
		 */
		protected function __canUpdate() : bool|ReturnHelper {
			if ($this->userId < 1 || empty($this->value) || $this->type->getValue() === null) {
				return false;
			}

			return true;
		}

		/**
		 * Initializes a new UserContact object.
		 *
		 * @throws \Exception
		 * @return void
		 */
		protected function __setupModel() : void {
			$this->setTableName('UserContact');

			$this->setColumn('created', 'Created', BaseDbTypes::DATETIME, BCF::SHOULD_INSERT);
			$this->setColumn('primary', 'Primary', BaseDbTypes::BOOLEAN,  BCF::SHOULD_INSERT | BCF::SHOULD_UPDATE);
			$this->setColumn('type',    'Type',    BaseDbTypes::INTEGER,  BCF::SHOULD_INSERT | BCF::SHOULD_UPDATE);
			$this->setColumn('userId',  'UserID',  BaseDbTypes::INTEGER,  BCF::IS_KEY        | BCF::SHOULD_INSERT);
			$this->setColumn('value',   'Value',   BaseDbTypes::STRING,   BCF::SHOULD_INSERT | BCF::SHOULD_UPDATE);

			$this->created = new \DateTimeImmutable('now', new \DateTimeZone('UTC'));
			$this->id      = 0;
			$this->primary = false;
			$this->type    = new UserContactTypes();
			$this->userId  = 0;
			$this->value   = '';

			return;
		}
	}
