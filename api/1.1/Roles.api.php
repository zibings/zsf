<?php

	namespace Api1_1;

	use OpenApi\Annotations as OA;

	use Stoic\Web\Api\Response;
	use Stoic\Web\Request;

	use Zibings\ApiController;
	use Zibings\Role;
	use Zibings\Roles as ZibRoles;
	use Zibings\RoleStrings;
	use Zibings\UserRoles;

	/**
	 * API controller that deals with role-related endpoints.
	 *
	 * @OA\Tag(
	 *   name="Roles",
	 *   description="User role operations"
	 * )
	 *
	 * @package Zibings\Api1_1
	 */
	class Roles extends ApiController {
		/**
		 * Attempts to add a new role to the system.
		 *
		 * @OA\Post(
		 *   path="/Roles/Add",
		 *   operationId="addRole",
		 *   summary="Add new role to system",
		 *   description="Add new role to system",
		 *   tags={"Roles"},
		 *   @OA\RequestBody(
		 *     required=true,
		 *     @OA\JsonContent(
		 *       type="object",
		 *       @OA\Property(property="name", type="string")
		 *     )
		 *   ),
		 *   @OA\Response(
		 *     response="200",
		 *     description="OK",
		 *     @OA\JsonContent(
		 *       type="array",
		 *       @OA\Items(
		 *         type="object",
		 *         @OA\Property(property="created", type="string"),
		 *         @OA\Property(property="id",      type="number"),
		 *         @OA\Property(property="name",    type="string")
		 *       )
		 *     )
		 *   ),
		 *   security={
		 *     {"admin_header_token": {}},
		 *     {"admin_cookie_token": {}}
		 *   }
		 * )
		 *
		 * @param Request $request The current request which routed to the endpoint.
		 * @param array|null $matches Array of matches returned by endpoint regex pattern.
		 * @throws \Exception
		 * @return Response
		 */
		public function addRole(Request $request, array $matches = null) : Response {
			$ret    = $this->newResponse();
			$params = $request->getInput();
			
			$role       = new Role($this->db, $this->log);
			$role->name = $params->getString('name');
			$create     = $role->create();

			if ($create->isBad()) {
				$this->assignReturnHelperError($ret, $create, "Failed to create new role");

				return $ret;
			}

			$ret->setData($role);

			return $ret;
		}

		/**
		 * Retrieves all roles in the database.
		 *
		 * @OA\Get(
		 *   path="/Roles",
		 *   operationId="getRoles",
		 *   summary="Retrieves all roles",
		 *   description="Retrieves all roles",
		 *   tags={"Roles"},
		 *   @OA\Response(
		 *     response="200",
		 *     description="OK",
		 *     @OA\JsonContent(
		 *       type="array",
		 *       @OA\Items(
		 *         type="object",
		 *         @OA\Property(property="created", type="string"),
		 *         @OA\Property(property="id",      type="number"),
		 *         @OA\Property(property="name",    type="string")
		 *       )
		 *     )
		 *   ),
		 *   security={
		 *     {"admin_header_token": {}},
		 *     {"admin_cookie_token": {}}
		 *   }
		 * )
		 *
		 * @param Request $request The current request which routed to the endpoint.
		 * @param array|null $matches Array of matches returned by endpoint regex pattern.
		 * @return Response
		 */
		public function getRoles(Request $request, array $matches = null) : Response {
			$ret = $this->newResponse();
			$ret->setData((new ZibRoles($this->db, $this->log))->getAll());

			return $ret;
		}

		/**
		 * Retrieves any roles in the database for the given user.
		 *
		 * @OA\Get(
		 *   path="/Roles/UserRoles/{userId}",
		 *   operationId="getUserRoles",
		 *   summary="Retrieve user roles",
		 *   description="Retrieve roles for the given user",
		 *   tags={"Roles"},
		 *   @OA\Parameter(
		 *     name="userId",
		 *     in="path",
		 *     description="unique user identifier",
		 *     required=true,
		 *     @OA\Schema(type="number")
		 *   ),
		 *   @OA\Response(
		 *     response="200",
		 *     description="OK",
		 *     @OA\JsonContent(
		 *       type="array",
		 *       @OA\Items(
		 *         type="object",
		 *         @OA\Property(property="created", type="string"),
		 *         @OA\Property(property="id",      type="number"),
		 *         @OA\Property(property="name",    type="string")
		 *       )
		 *     )
		 *   ),
		 *   security={
		 *     {"header_token": {}},
		 *     {"cookie_token": {}}
		 *   }
		 * )
		 *
		 * @OA\Get(
		 *   path="/Roles/UserRoles",
		 *   operationId="getMyRoles",
		 *   summary="Retrieve my user roles",
		 *   description="Retrieve my user roles",
		 *   tags={"Roles"},
		 *   @OA\Response(
		 *     response="200",
		 *     description="OK",
		 *     @OA\JsonContent(
		 *       type="array",
		 *       @OA\Items(
		 *         type="object",
		 *         @OA\Property(property="created", type="string"),
		 *         @OA\Property(property="id",      type="number"),
		 *         @OA\Property(property="name",    type="string")
		 *       )
		 *     )
		 *   ),
		 *   security={
		 *     {"header_token": {}},
		 *     {"cookie_token": {}}
		 *   }
		 * )
		 *
		 * @param Request $request The current request which routed to the endpoint.
		 * @param array|null $matches Array of matches returned by endpoint regex pattern.
		 * @throws \Exception
		 * @return Response
		 */
		public function getUserRoles(Request $request, array $matches = null) : Response {
			$user      = $this->getUser();
			$ret       = $this->newResponse();
			$userRoles = new UserRoles($this->db, $this->log);
			$userId    = (count($matches) > 1) ? intval($matches[1][0]) : $user->id;

			if ($userId != $user->id && !$userRoles->userInRoleByName($user->id, RoleStrings::ADMINISTRATOR)) {
				$ret->setAsError("Invalid user identifier");

				return $ret;
			}

			$ret->setData($userRoles->getAllUserRoles($userId));

			return $ret;
		}

		/**
		 * Registers the controller endpoints.
		 *
		 * @return void
		 */
		protected function registerEndpoints() : void {
			$this->registerEndpoint('POST', '/^\/?Roles\/Add\/?$/i',                      'addRole',         RoleStrings::ADMINISTRATOR);
			$this->registerEndpoint('POST', '/^\/?Roles\/RemoveUserRoles\/?$/i',          'removeUserRoles', RoleStrings::ADMINISTRATOR);
			$this->registerEndpoint('POST', '/^\/?Roles\/RemoveUserRole\/?$/i',           'removeUserRole',  RoleStrings::ADMINISTRATOR);
			$this->registerEndpoint('POST', '/^\/?Roles\/Remove\/?$/i',                   'removeRole',      RoleStrings::ADMINISTRATOR);
			$this->registerEndpoint('POST', '/^\/?Roles\/SetUserRole\/?$/i',              'setUserRole',     RoleStrings::ADMINISTRATOR);
			$this->registerEndpoint('POST', '/^\/?Roles\/SyncUserRoles\/?$/i',            'syncUserRoles',   RoleStrings::ADMINISTRATOR);
			$this->registerEndpoint('POST', '/^\/?Roles\/UserInRole\/?$/i',               'userInRole',      true);
			$this->registerEndpoint('GET',  '/^\/?Roles\/UsersInRole\/([a-z]{3,})\/?$/i', 'usersInRole',     RoleStrings::ADMINISTRATOR);
			$this->registerEndpoint('GET',  '/^\/?Roles\/UserRoles\/([0-9]{1,})\/?$/i',   'getUserRoles',    true);
			$this->registerEndpoint('GET',  '/^\/?Roles\/UserRoles\/?$/i',                'getUserRoles',    true);
			$this->registerEndpoint('GET',  '/^\/?Roles\/?$/i',                           'getRoles',        RoleStrings::ADMINISTRATOR);

			return;
		}

		/**
		 * Attempts to remove a role from the system.
		 *
		 * @OA\Post(
		 *   path="/Roles/Remove",
		 *   operationId="removeRole",
		 *   summary="Remove role from system",
		 *   description="Remove role from system",
		 *   tags={"Roles"},
		 *   @OA\RequestBody(
		 *     required=true,
		 *     @OA\JsonContent(
		 *       type="object",
		 *       @OA\Property(property="name", type="string")
		 *     )
		 *   ),
		 *   @OA\Response(
		 *     response="200",
		 *     description="OK",
		 *     @OA\JsonContent(
		 *       type="object",
		 *       @OA\Property(property="created", type="string"),
		 *       @OA\Property(property="id",      type="number"),
		 *       @OA\Property(property="name",    type="string")
		 *     )
		 *   ),
		 *   security={
		 *     {"admin_header_token": {}},
		 *     {"admin_cookie_token": {}}
		 *   }
		 * )
		 *
		 * @param Request $request The current request which routed to the endpoint.
		 * @param array|null $matches Array of matches returned by endpoint regex pattern.
		 * @throws \Exception
		 * @return Response
		 */
		public function removeRole(Request $request, array $matches = null) : Response {
			$ret    = $this->newResponse();
			$params = $request->getInput();

			$role = Role::fromName($params->getString('name'), $this->db, $this->log);

			if ($role->id < 1) {
				$ret->setAsError("Invalid role supplied");

				return $ret;
			}

			(new UserRoles($this->db, $this->log))->removeAllUsersFromRoleByName($role->name);
			$delete = $role->delete();

			if ($delete->isBad()) {
				$this->assignReturnHelperError($ret, $delete, "Failed to delete role");

				return $ret;
			}

			$ret->setData($role);

			return $ret;
		}

		/**
		 * Removes role for given user, if present.
		 *
		 * @OA\Post(
		 *   path="/Roles/RemoveUserRole",
		 *   operationId="removeUserRole",
		 *   summary="Removes role for given user",
		 *   description="Removes role for given user",
		 *   tags={"Roles"},
		 *   @OA\RequestBody(
		 *     required=true,
		 *     @OA\JsonContent(
		 *       type="object",
		 *       @OA\Property(property="userId", type="number"),
		 *       @OA\Property(property="name",   type="string")
		 *     )
		 *   ),
		 *   @OA\Response(
		 *     response="200",
		 *     description="OK",
		 *     @OA\JsonContent(type="boolean")
		 *   ),
		 *   security={
		 *     {"admin_header_token": {}},
		 *     {"admin_cookie_token": {}}
		 *   }
		 * )
		 *
		 * @param Request $request The current request which routed to the endpoint.
		 * @param array|null $matches Array of matches returned by endpoint regex pattern.
		 * @throws \Stoic\Web\Resources\InvalidRequestException|\Stoic\Web\Resources\NonJsonInputException|\ReflectionException|\Exception
		 * @return Response
		 */
		public function removeUserRole(Request $request, array $matches = null) : Response {
			$user   = $this->getUser();
			$ret    = $this->newResponse();
			$params = $request->getInput();

			if (!$params->hasAll('userId', 'name')) {
				$ret->setAsError("Invalid parameters supplied");

				return $ret;
			}

			$role      = $params->getString('name');
			$userId    = $params->getInt('userId', $user->id);
			$userRoles = new UserRoles($this->db, $this->log);

			if ($userId == $user->id || $userRoles->userInRoleByName($userId, RoleStrings::ADMINISTRATOR)) {
				$ret->setAsError("You cannot modify other administrators from the API");

				return $ret;
			}

			$userRoles->removeUserFromRoleByName($userId, $role);

			$ret->setData(true);

			return $ret;
		}

		/**
		 * Removes all roles for given user.
		 *
		 * @OA\Post(
		 *   path="/Roles/RemoveUserRoles",
		 *   operationId="removeUserRoles",
		 *   summary="Removes all roles for user",
		 *   description="Removes all roles for user",
		 *   tags={"Roles"},
		 *   @OA\RequestBody(
		 *     required=true,
		 *     @OA\JsonContent(
		 *       type="object",
		 *       @OA\Property(property="userId", type="number")
		 *     )
		 *   ),
		 *   @OA\Response(
		 *     response="200",
		 *     description="OK",
		 *     @OA\JsonContent(type="boolean")
		 *   ),
		 *   security={
		 *     {"admin_header_token": {}},
		 *     {"admin_cookie_token": {}}
		 *   }
		 * )
		 *
		 * @param Request $request The current request which routed to the endpoint.
		 * @param array|null $matches Array of matches returned by endpoint regex pattern.
		 * @throws \Stoic\Web\Resources\InvalidRequestException|\Stoic\Web\Resources\NonJsonInputException|\Exception
		 * @return Response
		 */
		public function removeUserRoles(Request $request, array $matches = null) : Response {
			$user      = $this->getUser();
			$ret       = $this->newResponse();
			$params    = $request->getInput();
			$userId    = $params->getInt('userId', $user->id);
			$userRoles = new UserRoles($this->db, $this->log);

			if ($userId == $user->id || $userRoles->userInRoleByName($userId, RoleStrings::ADMINISTRATOR)) {
				$ret->setAsError("You cannot modify other administrators from the API.");

				return $ret;
			}

			$userRoles->removeUserFromAllRoles(intval($params->getInt('userId', $user->id)));

			$ret->setData(true);

			return $ret;
		}

		/**
		 * Assigns a user to a given role.
		 *
		 * @OA\Post(
		 *   path="/Roles/SetUserRole",
		 *   operationId="setUserRole",
		 *   summary="Assigns role to user",
		 *   description="Assigns role to user",
		 *   tags={"Roles"},
		 *   @OA\RequestBody(
		 *     required=true,
		 *     @OA\JsonContent(
		 *       type="object",
		 *       @OA\Property(property="userId", type="number"),
		 *       @OA\Property(property="name",   type="string")
		 *     )
		 *   ),
		 *   @OA\Response(
		 *     response="200",
		 *     description="OK",
		 *     @OA\JsonContent(
		 *       type="object",
		 *
		 *       @OA\Property(property="created", type="string"),
		 *       @OA\Property(property="id",      type="number"),
		 *       @OA\Property(property="name",    type="string")
		 *     )
		 *   ),
		 *   security={
		 *     {"admin_header_token": {}},
		 *     {"admin_cookie_token": {}}
		 *   }
		 * )
		 *
		 * @param Request $request The current request which routed to the endpoint.
		 * @param array|null $matches Array of matches returned by endpoint regex pattern.
		 * @throws \Stoic\Web\Resources\InvalidRequestException|\Stoic\Web\Resources\NonJsonInputException|\ReflectionException|\Exception
		 * @return Response
		 */
		public function setUserRole(Request $request, array $matches = null) : Response {
			$ret    = $this->newResponse();
			$params = $request->getInput();

			if (!$params->hasAll('userId', 'name')) {
				$ret->setAsError("Invalid parameters supplied");

				return $ret;
			}

			if (!(new UserRoles($this->db, $this->log))->addUserToRoleByName($params->getInt('userId'), $params->getString('name'))) {
				$ret->setAsError("Failed to assign user role");

				return $ret;
			}

			$ret->setData(Role::fromName($params->getString('name'), $this->db, $this->log));

			return $ret;
		}

		/**
		 * Synchronize roles for the given user.
		 *
		 * @OA\Post(
		 *   path="/Roles/SyncUserRoles",
		 *   operationId="syncUserRoles",
		 *   summary="Synchronize user roles",
		 *   description="Synchronize user roles",
		 *   tags={"Roles"},
		 *   @OA\RequestBody(
		 *     required=true,
		 *     @OA\JsonContent(
		 *       type="object",
		 *       @OA\Property(property="userId", type="number"),
		 *       @OA\Property(property="roles",  type="array")
		 *     )
		 *   ),
		 *   @OA\Response(
		 *     response="200",
		 *     description="OK",
		 *     @OA\JsonContent(type="array")
		 *   ),
		 *   security={
		 *     {"header_token": {}},
		 *     {"cookie_token": {}}
		 *   }
		 * )
		 *
		 * @param Request $request The current request which routed to the endpoint.
		 * @param array|null $matches Array of matches returned by endpoint regex pattern.
		 * @throws \Stoic\Web\Resources\InvalidRequestException|\Stoic\Web\Resources\NonJsonInputException|\Exception
		 * @return Response
		 */
		public function syncUserRoles(Request $request, array $matches = null) : Response {
			$user   = $this->getUser();
			$ret    = $this->newResponse();
			$params = $request->getInput()->getSource();

			if (!isset($params['userId']) || !isset($params['roles']) || !is_array($params['roles'])) {
				$ret->setAsError("Invalid parameters supplied");

				return $ret;
			}

			$newRoles  = [];
			$userId    = intval($params['userId']);
			$userRoles = new UserRoles($this->db, $this->log);

			if ($userRoles->userInRoleByName($userId, RoleStrings::ADMINISTRATOR)) {
				$ret->setAsError("You cannot modify other administrators from the API.");

				return $ret;
			}

			$userRoles->removeUserFromAllRoles($userId);

			foreach ($params['roles'] as $role) {
				if ($userRoles->addUserToRoleByName($userId, $role)) {
					$newRoles[] = $role;
				}
			}

			$ret->setData($newRoles);

			return $ret;
		}

		/**
		 * Checks if a user is assigned a role.
		 *
		 * @OA\Post(
		 *   path="/Roles/UserInRole",
		 *   operationId="userInRole",
		 *   summary="Check if user is assigned a role",
		 *   description="Check if user is assigned a role",
		 *   tags={"Roles"},
		 *   @OA\RequestBody(
		 *     required=true,
		 *     @OA\JsonContent(
		 *       type="object",
		 *       @OA\Property(property="userId", type="number"),
		 *       @OA\Property(property="name",   type="string")
		 *     )
		 *   ),
		 *   @OA\Response(
		 *     response="200",
		 *     description="OK",
		 *     @OA\JsonContent(type="boolean")
		 *   ),
		 *   security={
		 *     {"header_token": {}},
		 *     {"cookie_token": {}}
		 *   }
		 * )
		 *
		 * @param Request $request The current request which routed to the endpoint.
		 * @param array|null $matches Array of matches returned by endpoint regex pattern.
		 * @throws \Stoic\Web\Resources\InvalidRequestException|\Stoic\Web\Resources\NonJsonInputException|\Exception
		 * @return Response
		 */
		public function userInRole(Request $request, array $matches = null) : Response {
			$user      = $this->getUser();
			$ret       = $this->newResponse();
			$params    = $request->getInput();
			$userId    = $params->getInt('userId', $user->id);
			$userRoles = new UserRoles($this->db, $this->log);

			if ($userId != $user->id && !$userRoles->userInRoleByName($user->id, RoleStrings::ADMINISTRATOR)) {
				$ret->setAsError("Invalid user identifier");

				return $ret;
			}

			$ret->setData($userRoles->userInRoleByName($userId, $params->getString('name')));

			return $ret;
		}

		/**
		 * Retrieves any and all users assigned to a specific role.
		 *
		 * @OA\Get(
		 *   path="/Roles/UsersInRole/{RoleName}",
		 *   operationId="usersInRole",
		 *   summary="Retrieve any users assigned to a role",
		 *   description="Retrieve any users assigned to a role",
		 *   tags={"Roles"},
		 *   @OA\Parameter(
		 *     name="RoleName",
		 *     in="path",
		 *     description="Role name to search with",
		 *     required=true,
		 *     @OA\Schema(type="string")
		 *   ),
		 *   @OA\Response(
		 *     response="200",
		 *     description="OK",
		 *     @OA\JsonContent(
		 *       type="array",
		 *       @OA\Items(
		 *         type="object",
		 *         @OA\Property(property="email",          type="string",  description="User's email address"),
		 *         @OA\Property(property="emailConfirmed", type="boolean", description="If user's email address has been confirmed"),
		 *         @OA\Property(property="id",             type="number",  description="User's unique identifier"),
		 *         @OA\Property(property="joined",         type="string",  description="Date and time the user joined"),
		 *         @OA\Property(property="lastActive",     type="string",  description="Date and time the user was last active, if available"),
		 *         @OA\Property(property="lastLogin",      type="string",  description="Date and time the user last logged in, if available")
		 *       )
		 *     )
		 *   ),
		 *   security={
		 *     {"admin_header_token": {}},
		 *     {"admin_cookie_token": {}}
		 *   }
		 * )
		 *
		 * @param Request $request The current request which routed to the endpoint.
		 * @param array|null $matches Array of matches returned by endpoint regex pattern.
		 * @throws \Exception
		 * @return Response
		 */
		public function usersInRole(Request $request, array $matches = null) : Response {
			$ret = $this->newResponse();
			$ret->setData((new UserRoles($this->db, $this->log))->getAllUsersInRoleByName($matches[1][0]));

			return $ret;
		}
	}
