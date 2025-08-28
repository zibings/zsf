<?php

	namespace Zibings;

	use Stoic\Pdo\BaseDbColumnFlags as BCF;
	use Stoic\Pdo\BaseDbTypes;
	use Stoic\Pdo\PdoDrivers;
	use Stoic\Pdo\StoicDbModel;
	use Stoic\Utilities\EnumBase;
	use Stoic\Utilities\ReturnHelper;

	/**
	 * Different stages of a relationship.
	 *
	 * @package Zibings
	 */
	class UserRelationStages extends EnumBase {
		const int ERROR    = 0;
		const int INVITED  = 1;
		const int ACCEPTED = 2;
		const int DECLINED = 3;
	}

	/**
	 * Class for representing a relationship between two users.
	 *
	 * @package Zibings
	 */
	class UserRelation extends StoicDbModel {
		/**
		 * Date and time this relation was created.
		 *
		 * @var \DateTimeInterface
		 */
		public \DateTimeInterface $created;
		/**
		 * Current stage of the user relation.
		 *
		 * @var UserRelationStages
		 */
		public UserRelationStages $stage;
		/**
		 * Whether this was the originating relation.
		 *
		 * @var bool
		 */
		public bool $origin;
		/**
		 * Identifier of the user who is the source of this relation.
		 *
		 * @var int
		 */
		public int $userOne;
		/**
		 * Identifier of the user who is the recipient of this relation.
		 *
		 * @var int
		 */
		public int $userTwo;


		/**
		 * Determines if the system should attempt to create a UserRelation in the database.
		 *
		 * @throws \Exception
		 * @return bool|ReturnHelper
		 */
		protected function __canCreate() : bool|ReturnHelper {
			if ($this->userOne < 1 || $this->userTwo < 1 || $this->stage->getValue() === null) {
				return false;
			}

			$this->created = new \DateTimeImmutable('now', new \DateTimeZone('UTC'));

			return true;
		}

		/**
		 * Determines if the system should attempt to delete a UserRelation from the database.
		 *
		 * @return bool|ReturnHelper
		 */
		protected function __canDelete() : bool|ReturnHelper {
			if ($this->userOne < 1 || $this->userTwo < 1) {
				return false;
			}

			return true;
		}

		/**
		 * Determines if the system should attempt to read a UserRelation from the database.
		 *
		 * @return bool|ReturnHelper
		 */
		protected function __canRead() : bool|ReturnHelper {
			if ($this->userOne < 1 || $this->userTwo < 1) {
				return false;
			}

			return true;
		}

		/**
		 * Determines if the system should attempt to update a UserRelation in the database.
		 *
		 * @return bool|ReturnHelper
		 */
		protected function __canUpdate() : bool|ReturnHelper {
			if ($this->userOne < 1 || $this->userTwo < 1 || $this->stage->getValue() === null) {
				return false;
			}

			return true;
		}

		/**
		 * Initializes a UserRelation object.
		 *
		 * @throws \Exception
		 * @return void
		 */
		protected function __setupModel() : void {
			$this->setTableName('UserRelation');

			$this->setColumn('created', 'Created',    BaseDbTypes::DATETIME, BCF::SHOULD_INSERT);
			$this->setColumn('stage',   'Stage',      BaseDbTypes::INTEGER,  BCF::SHOULD_INSERT | BCF::SHOULD_UPDATE);
			$this->setColumn('origin',  'Origin',     BaseDbTypes::BOOLEAN,  BCF::SHOULD_INSERT);
			$this->setColumn('userOne', 'UserID_One', BaseDbTypes::INTEGER,  BCF::IS_KEY        | BCF::SHOULD_INSERT);
			$this->setColumn('userTwo', 'UserID_Two', BaseDbTypes::INTEGER,  BCF::IS_KEY        | BCF::SHOULD_INSERT);

			$this->created = new \DateTimeImmutable('now', new \DateTimeZone('UTC'));
			$this->stage   = new UserRelationStages(UserRelationStages::ERROR);
			$this->origin  = false;
			$this->userOne = 0;
			$this->userTwo = 0;

			return;
		}
	}
