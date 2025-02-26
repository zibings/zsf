<?php

	namespace Zibings;

	use Stoic\Pdo\BaseDbColumnFlags as BCF;
	use Stoic\Pdo\BaseDbQueryTypes;
	use Stoic\Pdo\BaseDbTypes;
	use Stoic\Pdo\PdoDrivers;
	use Stoic\Pdo\PdoHelper;
	use Stoic\Utilities\ReturnHelper;

	/**
	 * List of available roles in system.
	 *
	 * @package Zibings
	 */
	class RoleStrings {
		const ADMINISTRATOR = 'Administrator';


		/**
		 * Internal static cache of constants.
		 *
		 * @var null|array
		 */
		protected static ?array $constCache = null;


		/**
		 * Retrieves the internal cache of constants.
		 *
		 * @return array
		 */
		public static function getConstList() : array {
			if (static::$constCache === null) {
				$ref = new \ReflectionClass(get_called_class());
				static::$constCache = $ref->getConstants();
			}

			return static::$constCache;
		}
	}

	/**
	 * Class for representing an access control role within the system.
	 *
	 * @package Zibings
	 */
	class Role extends RoleMeta {
		const SQL_SELBYNAME   = 'role-selectbyname';
		const SQL_SELBYNAMEID = 'role-selectbynameandid';

		/**
		 * Whether the stored queries have been initialized.
		 *
		 * @var bool
		 */
		private static bool $dbInitialized = false;

		/**
		 * Determines if the system should attempt to create a new Role in the database.
		 *
		 * @throws \Exception
		 * @return bool|ReturnHelper
		 */
		protected function __canCreate() : bool|ReturnHelper {
			if ($this->id > 0 || empty($this->name)) {
				return false;
			}

			$ret = true;

			$this->tryPdoExcept(function () use (&$ret) {
				$stmt = $this->db->prepareStored(self::SQL_SELBYNAME);
				$stmt->bindParam(':name', $this->name);

				if ($stmt->execute() && $stmt->fetch() !== false) {
					$ret = false;
				}
			}, "Failed to guard against role duplicate");

			if ($ret) {
				$this->created = new \DateTimeImmutable('now', new \DateTimeZone('UTC'));
			}

			return $ret;
		}

		/**
		 * Determines if the system should attempt to read a Role from the database.
		 *
		 * @return bool|ReturnHelper
		 */
		protected function __canRead() : bool|ReturnHelper {
			if ($this->id < 1) {
				return false;
			}

			return true;
		}

		/**
		 * Determines if the system should attempt to update a Role in the database.
		 *
		 * @return bool|ReturnHelper
		 */
		protected function __canUpdate() : bool|ReturnHelper {
			$ret = new ReturnHelper();
			$ret->makeBad();

			if ($this->id < 1 || empty($this->name)) {
				$ret->addMessage("Invalid name or identifier for update");

				return $ret;
			}

			$this->tryPdoExcept(function () use (&$ret) {
				$stmt = $this->db->prepareStored(self::SQL_SELBYNAMEID);
				$stmt->bindValue(':name', $this->name);
				$stmt->bindValue(':id', $this->id);
				$stmt->execute();

				$row = $stmt->fetch();

				if ($row && $row[0] > 0) {
					$ret->addMessage("Found duplicate role with name {$this->name} in database");
				} else {
					$ret->makeGood();
				}
			}, "Failed to guard against role duplicate");

			return $ret;
		}

		/**
		 * Determines if the system should attempt to delete a Role from the database.
		 *
		 * @return bool|ReturnHelper
		 */
		protected function __canDelete() : bool|ReturnHelper {
			if ($this->id < 1) {
				return false;
			}

			return true;
		}

		/**
		 * Initializes a new Role object before use.
		 *
		 * @throws \Exception
		 * @return void
		 */
		protected function __setupModel() : void {
			$this->setTableName('Role');

			$this->setColumn('created', 'Created', BaseDbTypes::DATETIME, BCF::SHOULD_INSERT);
			$this->setColumn('id',      'ID',      BaseDbTypes::INTEGER,  BCF::IS_KEY        | BCF::AUTO_INCREMENT);
			$this->setColumn('name',    'Name',    BaseDbTypes::STRING,   BCF::SHOULD_INSERT | BCF::SHOULD_UPDATE);

			if (!static::$dbInitialized) {
				PdoHelper::storeQuery(PdoDrivers::PDO_SQLSRV, self::SQL_SELBYNAME, $this->generateClassQuery(BaseDbQueryTypes::SELECT, false) . " WHERE [Name] = :name");
				PdoHelper::storeQuery(PdoDrivers::PDO_MYSQL,  self::SQL_SELBYNAME, $this->generateClassQuery(BaseDbQueryTypes::SELECT, false) . " WHERE `Name` = :name");
				PdoHelper::storeQuery(PdoDrivers::PDO_PGSQL,  self::SQL_SELBYNAME, $this->generateClassQuery(BaseDbQueryTypes::SELECT, false) . " WHERE \"Name\" = :name");

				PdoHelper::storeQuery(PdoDrivers::PDO_SQLSRV, self::SQL_SELBYNAMEID, $this->generateClassQuery(BaseDbQueryTypes::SELECT, false) . " WHERE [Name] = :name AND [ID] <> :id");
				PdoHelper::storeQuery(PdoDrivers::PDO_MYSQL,  self::SQL_SELBYNAMEID, $this->generateClassQuery(BaseDbQueryTypes::SELECT, false) . " WHERE `Name` = :name AND `ID` <> :id");
				PdoHelper::storeQuery(PdoDrivers::PDO_PGSQL,  self::SQL_SELBYNAMEID, $this->generateClassQuery(BaseDbQueryTypes::SELECT, false) . " WHERE \"Name\" = :name AND \"ID\" <> :id");

				static::$dbInitialized = true;
			}

			$this->created = new \DateTimeImmutable('now', new \DateTimeZone('UTC'));
			$this->id      = 0;
			$this->name    = '';

			return;
		}
	}
