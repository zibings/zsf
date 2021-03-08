<?php

	namespace Zibings;

	use Stoic\Pdo\BaseDbQueryTypes;
	use Stoic\Pdo\StoicDbClass;

	/**
	 * Joined user data returned by searches.
	 *
	 * @package Zibings
	 */
	class UserSearchData {
		/**
		 * Static helper method to populate a UserSearchData object from an array.
		 *
		 * @param array $data Source data array.
		 * @return UserSearchData
		 */
		public static function fromArray(array $data) : UserSearchData {
			return new UserSearchData(
				$data['Email'],
				$data['EmailConfirmed'],
				$data['ID'],
				new \DateTimeImmutable($data['Joined'], new \DateTimeZone('UTC')),
				($data['LastLogin'] !== null) ? new \DateTimeImmutable($data['LastLogin'], new \DateTimeZone('UTC')) : null,
				$data['DisplayName'],
				new \DateTimeImmutable($data['Birthday'], new \DateTimeZone('UTC')),
				$data['RealName'],
				$data['Description'],
				new UserGenders($data['Gender']),
				new VisibilityState($data['VisBirthday']),
				new VisibilityState($data['VisDescription']),
				new VisibilityState($data['VisEmail']),
				new VisibilityState($data['VisGender']),
				new VisibilityState($data['VisProfile']),
				new VisibilityState($data['VisRealName'])
			);
		}


		/**
		 * Instantiates a new UserSearchData object.
		 *
		 * @param string $email User's email address.
		 * @param bool $emailConfirmed Whether user's email address has been confirmed.
		 * @param int $id User's unique identifier.
		 * @param \DateTimeInterface $joined Date and time the user joined.
		 * @param \DateTimeInterface|null $lastLogin Last date and time user logged in, if available.
		 * @param string $displayName User's friendly display name.
		 * @param \DateTimeInterface $birthday Date and time of user's birthday.
		 * @param string $realName User's real name.
		 * @param string $description User's description.
		 * @param UserGenders $gender User's preferred gender.
		 * @param VisibilityState $visBirthday Visibility level of user's birthday.
		 * @param VisibilityState $visDescription Visibility level of user's description.
		 * @param VisibilityState $visEmail Visibility level of user's email address.
		 * @param VisibilityState $visGender Visibility level of user's gender preference.
		 * @param VisibilityState $visProfile Visibility level of user's profile.
		 * @param VisibilityState $visRealName Visibility level of user's real name.
		 * @return void
		 */
		public function __construct(
			public string                  $email,
			public bool                    $emailConfirmed,
			public int                     $id,
			public \DateTimeInterface      $joined,
			public \DateTimeInterface|null $lastLogin,
			public string                  $displayName,
			public \DateTimeInterface      $birthday,
			public string                  $realName,
			public string                  $description,
			public UserGenders             $gender,
			public VisibilityState         $visBirthday,
			public VisibilityState         $visDescription,
			public VisibilityState         $visEmail,
			public VisibilityState         $visGender,
			public VisibilityState         $visProfile,
			public VisibilityState         $visRealName) {
			return;
		}
	}

	/**
	 * Repository methods related to User data.
	 *
	 * @package Zibings
	 */
	class Users extends StoicDbClass {
		/**
		 * Internal User object.
		 *
		 * @var User
		 */
		protected User $usrObj;
		/**
		 * Internal UserProfile object.
		 *
		 * @var UserProfile
		 */
		protected UserProfile $proObj;
		/**
		 * Internal UserVisibilities object.
		 *
		 * @var UserVisibilities
		 */
		protected UserVisibilities $visObj;


		/**
		 * Initializes the internal User object.
		 *
		 * @return void
		 */
		protected function __initialize() : void {
			$this->usrObj = new User($this->db, $this->log);
			$this->proObj = new UserProfile($this->db, $this->log);
			$this->visObj = new UserVisibilities($this->db, $this->log);

			return;
		}

		/**
		 * Retrieves all users from the database.
		 *
		 * @return User[]
		 */
		public function getAll() {
			$ret = [];

			$this->tryPdoExcept(function () use (&$ret) {
				$query = $this->db->query($this->usrObj->generateClassQuery(BaseDbQueryTypes::SELECT, false));

				while ($row = $query->fetch(\PDO::FETCH_ASSOC)) {
					$ret[] = User::fromArray($row, $this->db, $this->log);
				}
			}, "Failed to get all users");

			return $ret;
		}

		/**
		 * Retrieves all users from the database, joining their profile info onto the array. Returns user info in the following
		 * format:
		 *
		 * [
		 *   'user'    => (User) {},
		 *   'profile' => (UserProfile) {}
		 * ]
		 *
		 * @return array
		 */
		public function getAllWithProfile() : array {
			$ret = [];

			$this->tryPdoExcept(function () use (&$ret) {
				$query = $this->db->query("SELECT * FROM {$this->usrObj->getDbTableName()} INNER JOIN {$this->proObj->getDbTableName()} ON [UserID] = [ID]");

				while ($row = $query->fetch(\PDO::FETCH_ASSOC)) {
					$ret[] = [
						'user'    => User::fromArray([
							'ID'             => $row['ID'],
							'Email'          => $row['Email'],
							'EmailConfirmed' => $row['EmailConfirmed'],
							'Joined'         => $row['Joined'],
							'LastLogin'      => $row['LastLogin'],
							'LastActive'     => $row['LastActive']
						], $this->db, $this->log),
						'profile' => UserProfile::fromArray([
							'UserID'      => $row['ID'],
							'DisplayName' => $row['DisplayName'],
							'Birthday'    => $row['Birthday'],
							'RealName'    => $row['RealName'],
							'Description' => $row['Description'],
							'Gender'      => $row['Gender']
						], $this->db, $this->log)
					];
				}
			}, "Failed to get all users");

			return $ret;
		}

		/**
		 * Retrieves the number of users active today.
		 *
		 * @return int
		 */
		public function getDailyActiveUserCount() : int {
			$ret = 0;

			$this->tryPdoExcept(function () use (&$ret) {
				$pastDay = (new \DateTimeImmutable('now', new \DateTimeZone('UTC')))->format('Y-m-d 00:00:00');
				$stmt    = $this->db->prepare("SELECT COUNT(*) FROM {$this->usrObj->getDbTableName()} WHERE [LastActive] IS NOT NULL AND [LastActive] > :pastDay");
				$stmt->bindParam(':pastDay', $pastDay);
				$stmt->execute();

				if ($row = $stmt->fetch()) {
					$ret = intval($row[0]);
				}
			}, "Failed to get DAU count");

			return $ret;
		}

		/**
		 * Retrieves the number of users active this month.
		 *
		 * @return int
		 */
		public function getMonthlyActiveUserCount() : int {
			$ret = 0;

			$this->tryPdoExcept(function () use (&$ret) {
				$pastMonth = (new \DateTimeImmutable('now', new \DateTimeZone('UTC')))->format('Y-m-1 00:00:00');
				$stmt      = $this->db->prepare("SELECT COUNT(*) FROM {$this->usrObj->getDbTableName()} WHERE [LastActive] IS NOT NULL AND [LastActive] > :pastMonth");
				$stmt->bindParam(':pastMonth', $pastMonth);
				$stmt->execute();

				if ($row = $stmt->fetch()) {
					$ret = intval($row[0]);
				}
			}, "Failed to get MAU count");

			return $ret;
		}

		/**
		 * Attempts to retrieve the total number of users in the system.
		 *
		 * @return int
		 */
		public function getTotalUsers() : int {
			$total = 0;

			$this->tryPdoExcept(function () use (&$total) {
				$stmt = $this->db->prepare("SELECT COUNT(*) FROM {$this->usrObj->getDbTableName()}");

				if ($stmt->execute()) {
					$total = $stmt->fetch()[0];
				}
			}, "Failed to get user count");

			return $total;
		}

		/**
		 * Attempts to retrieve the total number of users who have confirmed their emails in the system.
		 *
		 * @return int
		 */
		public function getTotalVerifiedUsers() : int {
			$total = 0;

			$this->tryPdoExcept(function () use (&$total) {
				$stmt = $this->db->prepare("SELECT COUNT(*) FROM {$this->usrObj->getDbTableName()} WHERE [EmailConfirmed] = 1");

				if ($stmt->execute()) {
					$total = $stmt->fetch()[0];
				}
			}, "Failed to get user count");

			return $total;
		}

		/**
		 * Attempts to search the database by identifiers.
		 *
		 * @param string $query Query value to compare against identifiers.
		 * @param bool $respectVisibilities Optional parameter to disable respecting user visibilities, default will respect visibilities.
		 * @return UserSearchData[]
		 */
		public function searchUsersByIdentifiers(string $query, bool $respectVisibilites = true) {
			$ret  = [];

			$sql  = "SELECT ";
			$sql .= "[Email], [EmailConfirmed], [ID], [Joined], [LastLogin], ";
			$sql .= "[p].[DisplayName], [p].[Birthday], [p].[RealName], [p].[Description], [p].[Gender], ";
			$sql .= "[v].[Birthday] as [VisBirthday], [v].[Description] as [VisDescription], [v].[Email] as [VisEmail], [v].[Gender] as [VisGender], [v].[Profile] as [VisProfile], [v].[RealName] as [VisRealName] ";
			$sql .= "FROM [dbo].[User] ";
			$sql .= "INNER JOIN [dbo].[UserProfile] as [p] ON [p].[UserID] = [ID] ";
			$sql .= "INNER JOIN [dbo].[UserVisibilities] as [v] ON [v].[UserID] = [ID] ";
			$sql .= "WHERE ([Email] LIKE :query OR [p].[DisplayName] LIKE :query)";

			if ($respectVisibilites) {
				$sql .= " AND [v].[Searches] > 0";
			}

			$this->tryPdoExcept(function () use (&$ret, $sql, $query, $respectVisibilites) {
				$stmt = $this->db->prepare($sql);
				$stmt->bindValue(':query', "%{$query}%", \PDO::PARAM_STR);

				if ($stmt->execute()) {
					while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
						$ret[] = UserSearchData::fromArray($row);
					}
				}
			}, "Failed to search users");

			return $ret;
		}
	}
