<?php

	namespace Zibings;

	use Stoic\Pdo\BaseDbColumnFlags as BCF;
	use Stoic\Pdo\BaseDbQueryTypes;
	use Stoic\Pdo\BaseDbTypes;
	use Stoic\Pdo\PdoDrivers;
	use Stoic\Pdo\PdoHelper;
	use Stoic\Utilities\ReturnHelper;

	/**
	 * Class for representing a single session for a user.
	 *
	 * @package Zibings
	 */
	class UserSession extends UserSessionMeta {
		const string SQL_SELBYTOKEN = 'usersession-selectbytoken';
		private static bool $dbInitialized = false;

		public static function generateGuid(bool $withBrackets = true) : string {
			$ret = '';

			// @codeCoverageIgnoreStart
			if (function_exists('com_create_guid')) {
				$ret = com_create_guid();
			} else {
				mt_srand((int)(microtime(true)*10000));//optional for php 4.2.0 and up.
				$charid = strtoupper(md5(uniqid(rand(), true)));
				$hyphen = chr(45);// "-"
				$ret = (chr(123)
					.substr($charid, 0, 8).$hyphen
					.substr($charid, 8, 4).$hyphen
					.substr($charid,12, 4).$hyphen
					.substr($charid,16, 4).$hyphen
					.substr($charid,20,12)
					.chr(125));
			}
			// @codeCoverageIgnoreEnd

			if ($withBrackets) {
				return $ret;
			}

			return trim($ret, '{}');
		}

		/**
		 * @throws \DateMalformedStringException
		 */
		protected function __canCreate() : bool|ReturnHelper {
			if ($this->userId < 1 || empty($this->token)) {
				return false;
			}

			$this->created = new \DateTimeImmutable('now', new \DateTimeZone('UTC'));

			return true;
		}

		protected function __canRead() : bool|ReturnHelper {
			if ($this->id < 1) {
				return false;
			}

			return true;
		}

		protected function __canUpdate() : bool|ReturnHelper {
			return false;
		}

		protected function __canDelete() : bool|ReturnHelper {
			if ($this->id < 1) {
				return false;
			}

			return true;
		}
		
		/**
		 * Initializes a new UserSession object.
		 *
		 * @throws \Exception
		 * @return void
		 */
		protected function __setupModel() : void {
			$this->setTableName('UserSession');

			$this->setColumn('address',  'Address',  BaseDbTypes::STRING,   BCF::SHOULD_INSERT);
			$this->setColumn('created',  'Created',  BaseDbTypes::DATETIME, BCF::SHOULD_INSERT);
			$this->setColumn('hostname', 'Hostname', BaseDbTypes::STRING,   BCF::SHOULD_INSERT);
			$this->setColumn('id',       'ID',       BaseDbTypes::INTEGER,  BCF::IS_KEY        | BCF::AUTO_INCREMENT);
			$this->setColumn('token',    'Token',    BaseDbTypes::STRING,   BCF::SHOULD_INSERT);
			$this->setColumn('userId',   'UserID',   BaseDbTypes::INTEGER,  BCF::SHOULD_INSERT);

			if (!static::$dbInitialized) {
				PdoHelper::storeQuery(PdoDrivers::PDO_SQLSRV, self::SQL_SELBYTOKEN, $this->generateClassQuery(BaseDbQueryTypes::SELECT, false) . " WHERE [Token] = :token");
				PdoHelper::storeQuery(PdoDrivers::PDO_MYSQL,  self::SQL_SELBYTOKEN, $this->generateClassQuery(BaseDbQueryTypes::SELECT, false) . " WHERE `Token` = :token");
				PdoHelper::storeQuery(PdoDrivers::PDO_PGSQL,  self::SQL_SELBYTOKEN, $this->generateClassQuery(BaseDbQueryTypes::SELECT, false) . " WHERE \"Token\" = :token");

				static::$dbInitialized = true;
			}

			$this->address  = '';
			$this->created  = new \DateTimeImmutable('now', new \DateTimeZone('UTC'));
			$this->hostname = '';
			$this->id       = 0;
			$this->token    = '';
			$this->userId   = 0;
			
			return;
		}
	}
