<?php

	namespace Zibings;

	use Stoic\Pdo\BaseDbQueryTypes;
	use Stoic\Pdo\StoicDbClass;

	/**
	 * Repository methods related to user roles.
	 *
	 * @package Zibings
	 */
	class UserRoles extends StoicDbClass {
		/**
		 * Internal Role instance.
		 *
		 * @var Role
		 */
		protected $rlObj;


		/**
		 * Initializes the internal Role instance.
		 *
		 * @return void
		 */
		protected function __initialize() : void {
			$this->rlObj = new Role($this->db, $this->log);

			return;
		}

		/**
		 * Adds a user to the role identified by the provided string.
		 *
		 * @param integer $userId Integer identifier of user to add to role.
		 * @param string $name String identifier of role to add user to.
		 * @return boolean
		 */
		public function addUserToRoleByName(int $userId, string $name) : bool {
			$user = User::fromId($userId, $this->db, $this->log);
			$role = Role::fromName($name, $this->db, $this->log);

			if ($user->id < 1 || $role->id < 1) {
				return false;
			}

			$this->tryPdoExcept(function () use ($userId, $role) {
				$stmt = $this->db->prepare("INSERT INTO [dbo].[UserRole] ([UserID], [RoleID]) VALUES (:userId, :roleId)");
				$stmt->bindParam(':userId', $userId, \PDO::PARAM_INT);
				$stmt->bindParam(':roleId', $role->id, \PDO::PARAM_INT);
				$stmt->execute();
			}, "Failed to add user to role");

			return true;
		}

		/**
		 * Removes all roles for the given user.
		 *
		 * @param integer $userId Integer identifier for user in question.
		 * @return void
		 */
		public function deleteAllForUser(int $userId) : void {
			if ($userId < 1) {
				return;
			}

			$this->tryPdoExcept(function () use ($userId) {
				$stmt = $this->db->prepare("DELETE FROM {$this->rlObj->getDbTableName()} WHERE [UserID] = :userId");
				$stmt->bindParam(':userId', $userId, \PDO::PARAM_INT);
				$stmt->execute();
			}, "Failed to delete user's contacts");

			return;
		}

		/**
		 * Retrieves all roles the specified user belongs to, empty array if not found.
		 *
		 * @param integer $userId Integer identifier of user in question.
		 * @return Role[]
		 */
		public function getAllUserRoles(int $userId) {
			$ret = [];

			if ($userId < 1) {
				return $ret;
			}

			$this->tryPdoExcept(function () use (&$ret, $userId) {
				$stmt = $this->db->prepare($this->rlObj->generateClassQuery(BaseDbQueryTypes::SELECT, false) . " WHERE [ID] IN (SELECT [RoleID] FROM [dbo].[UserRole] WHERE [UserID] = :userId)");
				$stmt->bindParam(':userId', $userId, \PDO::PARAM_INT);

				if ($stmt->execute()) {
					while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
						$tmp               = Role::fromArray($row, $this->db, $this->log);
						$ret[$row['ID']]   = $tmp;
						$ret[$row['Name']] = $tmp;
					}
				}
			}, "Failed to get user roles");

			return $ret;
		}

		/**
		 * Retrieves any and all users assigned to a role, empty array if not found.
		 *
		 * @param string $name Name of the role in question.
		 * @return User[]
		 */
		public function getAllUsersInRoleByName(string $name) {
			$ret    = [];
			$usrObj = new User($this->db, $this->log);

			$this->tryPdoExcept(function () use (&$ret, $usrObj, $name) {
				$role = Role::fromName($name, $this->db, $this->log);
				$stmt = $this->db->prepare($usrObj->generateClassQuery(BaseDbQueryTypes::SELECT, false) . " WHERE [ID] IN (SELECT [UserID] FROM [dbo].[UserRole] WHERE [RoleID] = :roleId)");
				$stmt->bindParam(':roleId', $role->id, \PDO::PARAM_INT);

				if ($stmt->execute()) {
					while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
						$ret[] = User::fromArray($row, $this->db, $this->log);
					}
				}
			}, "Failed to retrieve user list");

			return $ret;
		}

		/**
		 * Attempts to remove all users assigned to the given role.
		 *
		 * @param string $name Name of the role in question.
		 * @return void
		 */
		public function removeAllUsersFromRoleByName(string $name) : void {
			$this->tryPdoExcept(function () use ($name) {
				$stmt = $this->db->prepare("DELETE FROM [dbo].[UserRole] WHERE [RoleID] = (SELECT [ID] FROM [dbo].[Role] WHERE [Name] = :name");
				$stmt->bindParam(':name', $name, \PDO::PARAM_STR);
				$stmt->execute();
			}, "Failed to remove users from role");

			return;
		}

		/**
		 * Removes all roles for the given users.
		 *
		 * @param integer $userId Integer identifier of the user in question.
		 * @return void
		 */
		public function removeUserFromAllRoles(int $userId) : void {
			if ($userId < 1) {
				return;
			}

			$this->tryPdoExcept(function () use ($userId) {
				$stmt = $this->db->prepare("DELETE FROM [dbo].[UserRole] WHERE [UserID] = :userId");
				$stmt->bindParam(':userId', $userId, \PDO::PARAM_INT);
				$stmt->execute();
			}, "Failed to remove user from all roles");

			return;
		}

		/**
		 * Removes the role from the user in question.
		 *
		 * @param integer $userId Integer identifier of the user in question.
		 * @param string $name Name of the role in question.
		 * @return void
		 */
		public function removeUserFromRoleByName(int $userId, string $name) : void {
			if ($userId < 1 || empty($name)) {
				return;
			}

			$role = Role::fromName($name, $this->db, $this->log);

			if ($role->id < 1) {
				return;
			}

			$this->tryPdoExcept(function () use ($userId, $role) {
				$stmt = $this->db->prepare("DELETE FROM [dbo].[UserRole] WHERE [UserID] = :userId AND [RoleID] = :roleId");
				$stmt->bindParam(':userId', $userId, \PDO::PARAM_INT);
				$stmt->bindParam(':roleId', $role->id, \PDO::PARAM_INT);
				$stmt->execute();
			}, "Failed to remove user from role");

			return;
		}

		/**
		 * Checks whether or not the user is a member of the role in question.
		 *
		 * @param integer $userId Integer identifier of the user in question.
		 * @param string $name Name of the role in question.
		 * @return boolean
		 */
		public function userInRoleByName(int $userId, string $name) : bool {
			if ($userId < 1 || empty($name)) {
				return false;
			}

			$role = Role::fromName($name, $this->db, $this->log);

			if ($role->id < 1) {
				return false;
			}

			$ret = false;

			$this->tryPdoExcept(function () use (&$ret, $userId, $role) {
				$stmt = $this->db->prepare("SELECT * FROM [dbo].[UserRole] WHERE [UserID] = :userId AND [RoleID] = :roleId");
				$stmt->bindParam(':userId', $userId, \PDO::PARAM_INT);
				$stmt->bindParam(':roleId', $role->id, \PDO::PARAM_INT);

				if ($stmt->execute()) {
					while ($stmt->fetch()) {
						$ret = true;
					}
				}
			}, "Failed to check if user is in role.");

			return $ret;
		}
	}
