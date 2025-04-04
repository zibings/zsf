<?php

	namespace Zibings;

	use Stoic\Log\Logger;
	use Stoic\Pdo\BaseDbColumnFlags as BCF;
	use Stoic\Pdo\BaseDbTypes;
	use Stoic\Pdo\PdoDrivers;
	use Stoic\Pdo\PdoHelper;
	use Stoic\Pdo\StoicDbModel;
	use Stoic\Utilities\EnumBase;
	use Stoic\Utilities\ReturnHelper;

	/**
	 * Enumerated types of providers.
	 */
	class LoginKeyProviders extends EnumBase {
		const int ERROR    = 0;
		const int PASSWORD = 1;
		const int FACEBOOK = 2;
		const int TWITTER  = 3;
		const int TWITCH   = 4;
		const int GITHUB   = 5;
		const int REDDIT   = 6;
	}

	/**
	 * Class for representing a login key a user can utilize.
	 *
	 * @package Zibings
	 */
	class LoginKey extends StoicDbModel {
		const SQL_GUPCOUNT = 'loginkey-gupcount';


		/**
		 * Key for provider access (can be password hash, oath token, etc).
		 *
		 * @var string
		 */
		public string $key;
		/**
		 * The type of provider this key represents.
		 *
		 * @var LoginKeyProviders
		 */
		public LoginKeyProviders $provider;
		/**
		 * Integer identifier of user who owns this key.
		 *
		 * @var int
		 */
		public int $userId;


		/**
		 * Whether the stored queries have been initialized.
		 *
		 * @var bool
		 */
		private static bool $dbInitialized = false;

		/**
		 * Static method to retrieve login key given the user and provider. Returns a blank LoginKey object if no key is found.
		 *
		 * @param int                   $userId   Integer identifier of user who owns key.
		 * @param int|LoginKeyProviders $provider Type of provider this key represents.
		 * @param PdoHelper             $db       PdoHelper instance for internal use.
		 * @param Logger|null           $log      Optional Logger instance for internal use, new instance created if not supplied.
		 * @return LoginKey
		 * @throws \ReflectionException
		 */
		public static function fromUserAndProvider(int $userId, int|LoginKeyProviders $provider, PdoHelper $db, Logger $log = null) : LoginKey {
			$ret = new LoginKey($db, $log);

			if ($userId > 0) {
				$ret->userId   = $userId;
				$ret->provider = LoginKeyProviders::tryGet($provider);

				if ($ret->read()->isBad()) {
					$ret = new LoginKey($db, $log);
				}
			}

			return $ret;
		}


		/**
		 * Determines if the system should attempt to create a new LoginKey in the database.
		 *
		 * @throws \ReflectionException
		 * @return bool|ReturnHelper
		 */
		protected function __canCreate() : bool|ReturnHelper {
			$ret = new ReturnHelper();

			if ($this->userId < 1 || !LoginKeyProviders::validValue($this->provider->getValue()) || empty($this->key)) {
				$ret->addMessage("Invalid login key values for userId, provider, and/or key");

				return $ret;
			}

			$this->tryPdoExcept(function () use (&$ret) {
				$stmt = $this->db->prepareStored(self::SQL_GUPCOUNT);
				$stmt->bindParam(':userId', $this->userId);
				$stmt->bindValue(':provider', $this->provider->getValue());

				if ($stmt->execute() && $stmt->fetch()[0] > 0) {
					$ret->addMessage("Found duplicate login key for userId #{$this->userId} and provider '{$this->provider->getName()}");
				} else {
					$ret->makeGood();
				}
			}, "Failed to check for duplicate login key");

			return $ret;
		}

		/**
		 * Determines if the system should attempt to delete a LoginKey from the database.
		 *
		 * @throws \ReflectionException
		 * @return bool|ReturnHelper
		 */
		protected function __canDelete() : bool|ReturnHelper {
			if ($this->userId < 1 || !LoginKeyProviders::validValue($this->provider->getValue())) {
				return false;
			}

			return true;
		}

		/**
		 * Determines if the system should attempt to read a LoginKey from the database.
		 *
		 * @throws \ReflectionException
		 * @return bool|ReturnHelper
		 */
		protected function __canRead() : bool|ReturnHelper {
			if ($this->userId < 1 || !LoginKeyProviders::validValue($this->provider->getValue())) {
				return false;
			}

			return true;
		}

		/**
		 * Determines if the system should attempt to update a LoginKey in the database.
		 *
		 * @throws \ReflectionException
		 * @return bool|ReturnHelper
		 */
		protected function __canUpdate() : bool|ReturnHelper {
			if ($this->userId < 1 || !LoginKeyProviders::validValue($this->provider->getValue())) {
				return false;
			}

			return true;
		}

		/**
		 * Initializes a new LoginKey object before use.
		 *
		 * @return void
		 */
		protected function __setupModel() : void {
			$this->setTableName('LoginKey');

			if (!static::$dbInitialized) {
				PdoHelper::storeQuery(PdoDrivers::PDO_SQLSRV, self::SQL_GUPCOUNT, "SELECT COUNT(*) FROM {$this->getDbTableName()} WHERE [UserID] = :userId AND [Provider] = :provider");
				PdoHelper::storeQuery(PdoDrivers::PDO_MYSQL,  self::SQL_GUPCOUNT, "SELECT COUNT(*) FROM {$this->getDbTableName()} WHERE `UserID` = :userId AND `Provider` = :provider");
				PdoHelper::storeQuery(PdoDrivers::PDO_PGSQL,  self::SQL_GUPCOUNT, "SELECT COUNT(*) FROM {$this->getDbTableName()} WHERE \"UserID\" = :userId AND \"Provider\" = :provider");

				static::$dbInitialized = true;
			}

			$this->setColumn('key',      'Key',      BaseDbTypes::STRING,  BCF::SHOULD_INSERT | BCF::SHOULD_UPDATE);
			$this->setColumn('provider', 'Provider', BaseDbTypes::INTEGER, BCF::IS_KEY        | BCF::SHOULD_INSERT);
			$this->setColumn('userId',   'UserID',   BaseDbTypes::INTEGER, BCF::IS_KEY        | BCF::SHOULD_INSERT);

			$this->key      = '';
			$this->provider = new LoginKeyProviders(LoginKeyProviders::ERROR);
			$this->userId   = 0;

			return;
		}
	}
