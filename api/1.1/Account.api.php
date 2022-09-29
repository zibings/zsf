<?php

	namespace Api1_1;

	use OpenApi\Annotations as OA;
	use PhpParser\Node\Param;
	use Stoic\Log\Logger;
	use Stoic\Utilities\ParameterHelper;
	use Stoic\Web\Api\Response;
	use Stoic\Web\PageHelper;
	use Stoic\Web\Request;
	use Stoic\Web\Resources\HttpStatusCodes;
	use Stoic\Web\Api\Stoic;

	use Zibings\ApiController;
	use Zibings\AuthHistoryActions;
	use Zibings\LoginKeyProviders;
	use Zibings\RoleStrings;
	use Zibings\User;
	use Zibings\UserAuthHistory;
	use Zibings\UserAuthHistoryLoginNode;
	use Zibings\UserAuthHistoryLogoutNode;
	use Zibings\UserEvents;
	use Zibings\UserEventTypes;
	use Zibings\UserRoles;
	use Zibings\UserSession;

	use function Zibings\sendResetEmail;

	/**
	 * API controller that deals with account-related endpoints.
	 *
	 * @OA\Tag(
	 *   name="Account",
	 *   description="User account related operation"
	 * )
	 *
	 * @package Zibings\Api1_1
	 */
	class Account extends ApiController {
		/**
		 * Instantiates a new Account object.
		 *
		 * @param Stoic $stoic Internal instance of Stoic API object.
		 * @param \PDO $db Internal instance of PDO object.
		 * @param Logger|null $log Optional Logger object for internal use.
		 * @param UserEvents|null $events Optional UserEvent object for internal use.
		 * @param UserRoles|null $userRoles Optional UserRoles object for internal use.
		 * @throws \ReflectionException
		 * @return void
		 */
		public function __construct(
			Stoic $stoic,
			\PDO $db,
			Logger $log = null,
			protected UserEvents|null    $events    = null,
			protected UserRoles|null     $userRoles = null) {
			parent::__construct($stoic, $db, $log);

			if ($this->events === null) {
				$this->events = new UserEvents($this->db, $this->log);
			}

			if ($this->userRoles === null) {
				$this->userRoles = new UserRoles($this->db, $this->log);
			}

			// NOTE: Add UserEvent nodes here if needed
			$this->events->linkToEvent(UserEventTypes::LOGIN, new UserAuthHistoryLoginNode($this->db, $this->log));
			$this->events->linkToEvent(UserEventTypes::LOGOUT, new UserAuthHistoryLogoutNode($this->db, $this->log));

			return;
		}

		/**
		 * Checks if an email is valid and in-use, returning a status of 0 only if it is both valid and currently not in use.
		 *
		 * @OA\Get(
		 *   path="/api/1.1/Account/CheckEmail",
		 *   operationId="CheckEmail",
		 *   summary="Check if an email is valid and not in-use",
		 *   description="Check if an email is valid and not in-use",
		 *   tags={"Account"},
		 *   @OA\Parameter(
		 *     name="email",
		 *     in="query",
		 *     description="Email to validate",
		 *     required=true,
		 *     @OA\Schema(type="string")
		 *   ),
		 *   @OA\Response(
		 *     response="200",
		 *     description="OK",
		 *     @OA\JsonContent(ref="#/components/schemas/StatusResponseData")
		 *   )
		 * )
		 *
		 * @param Request $request The current request which routed to the endpoint.
		 * @param array|null $matches Array of matches returned by endpoint regex pattern.
		 * @throws \Stoic\Web\Resources\InvalidRequestException|\Stoic\Web\Resources\NonJsonInputException
		 * @return Response
		 */
		public function checkEmail(Request $request, array $matches = null) : Response {
			$ret    = new Response(HttpStatusCodes::OK);
			$params = $request->getInput();
			$usr    = User::fromEmail($params->getString('email'), $this->db, $this->log);

			if ($usr->id > 0) {
				$ret->setData($this->newStatusResponseData(1, "Invalid email, already in use"));

				return $ret;
			}

			$ret->setData($this->newStatusResponseData(0, "Good and available email"));

			return $ret;
		}

		/**
		 * Checks if the given token is valid for the given user identifier.
		 *
		 * @OA\Post(
		 *   path="/api/1.1/Account/CheckToken",
		 *   operationId="CheckToken",
		 *   summary="Check if a token is valid for a user",
		 *   description="Check if a token is valid for a user",
		 *   tags={"Account"},
		 *   @OA\RequestBody(
		 *     @OA\JsonContent(
		 *       type="object",
		 *       @OA\Property(property="userId", type="number"),
		 *       @OA\Property(property="token",  type="string")
		 *     )
		 *   ),
		 *   @OA\Response(
		 *     response="200",
		 *     description="OK",
		 *     @OA\JsonContent(type="string")
		 *   ),
		 *   @OA\Response(
		 *     response="500",
		 *     description="Invalid",
		 *     @OA\JsonContent(type="string")
		 *   )
		 * )
		 *
		 * @param Request $request The current request which routed to the endpoint.
		 * @param array|null $matches Array of matches returned by endpoint regex pattern.
		 * @throws \Stoic\Web\Resources\InvalidRequestException|\Stoic\Web\Resources\NonJsonInputException|\ReflectionException
		 * @return Response
		 */
		public function checkToken(Request $request, array $matches = null) : Response {
			$ret    = $this->newResponse();
			$params = $request->getInput();

			if (!$params->hasAll('userId', 'token')) {
				$ret->setAsError("Invalid parameters provided");

				return $ret;
			}

			$userId      = $params->getInt('userId');
			$userSession = UserSession::fromToken($params->getString('token'), $this->db, $this->log);
			UserAuthHistory::createFromUserId($userId, AuthHistoryActions::TOKEN_CHECK, new ParameterHelper($_SERVER), "Token checked for user #{$userId}", $this->db, $this->log);

			if ($userSession->userId != $userId) {
				$ret->setAsError("Invalid session parameters");

				return $ret;
			}

			$ret->setData("Token is valid");

			return $ret;
		}

		/**
		 * Attempts to create a user in the system, only callable by administrators.
		 *
		 * @OA\Post(
		 *   path="/api/1.1/Account/Create",
		 *   operationId="CreateUser",
		 *   summary="Creates a new user in the system",
		 *   description="Creates a new user in the system",
		 *   tags={"Account"},
		 *   @OA\RequestBody(
		 *     required=true,
		 *     @OA\JsonContent(
		 *       type="object",
		 *       @OA\Property(property="email",           type="string"),
		 *       @OA\Property(property="password",        type="string"),
		 *       @OA\Property(property="confirmPassword", type="string"),
		 *       @OA\Property(property="provider",        type="number"),
		 *       @OA\Property(property="emailConfirmed",  type="boolean")
		 *     )
		 *   ),
		 *   @OA\Response(
		 *     response="200",
		 *     description="OK",
		 *     @OA\JsonContent(
		 *       type="object",
		 *       @OA\Property(property="email",           type="string"),
		 *       @OA\Property(property="password",        type="string"),
		 *       @OA\Property(property="confirmPassword", type="string"),
		 *       @OA\Property(property="provider",        type="number"),
		 *       @OA\Property(property="emailConfirmed",  type="boolean")
		 *     )
		 *   ),
		 *   @OA\Response(
		 *     response="500",
		 *     description="Failed",
		 *     @OA\JsonContent(type="string")
		 *   )
		 * )
		 *
		 * @param Request $request The current request which routed to the endpoint.
		 * @param array|null $matches Array of matches returned by endpoint regex pattern.
		 * @throws \Stoic\Web\Resources\InvalidRequestException|\Stoic\Web\Resources\NonJsonInputException|\Exception
		 * @return Response
		 */
		public function createUser(Request $request, array $matches = null) : Response {
			$user    = $this->getUser();
			$ret     = $this->newResponse();
			$params  = $request->getInput();
			$evtData = [
				'email'          => $params->getString('email'),
				'key'            => $params->getString('password'),
				'confirmKey'     => $params->getString('confirmPassword'),
				'provider'       => $params->getInt('provider'),
				'emailConfirmed' => false
			];

			if ($this->userRoles->userInRoleByName($user->id, RoleStrings::ADMINISTRATOR) && $params->has('emailConfirmed')) {
				$evtData['emailConfirmed'] = $params->getBool('emailConfirmed');
			}

			$this->processEvent($ret, 'doCreate', new ParameterHelper($evtData));

			return $ret;
		}

		/**
		 * Attempts to remove a user from the system.
		 *
		 * @OA\Post(
		 *   path="/api/1.1/Account/Delete",
		 *   operationId="DeleteUser",
		 *   summary="Removes user from system",
		 *   description="Removes user from system",
		 *   tags={"Account"},
		 *   @OA\RequestBody(
		 *     required=true,
		 *     @OA\JsonContent(
		 *       type="object",
		 *       @OA\Property(property="userId", type="number")
		 *     )
		 *   ),
		 *   @OA\Response(
		 *     response="200",
		 *     description="OK"
		 *   ),
		 *   @OA\Response(
		 *     response="500",
		 *     description="Failed",
		 *     @OA\JsonContent(type="string"),
		 *   )
		 * )
		 *
		 * @param Request $request The current request which routed to the endpoint.
		 * @param array|null $matches Array of matches returned by endpoint regex pattern.
		 * @throws \Stoic\Web\Resources\InvalidRequestException|\Stoic\Web\Resources\NonJsonInputException|\ReflectionException|\Exception
		 * @return Response
		 */
		public function deleteUser(Request $request, array $matches = null) : Response {
			$user   = $this->getUser();
			$ret    = $this->newResponse();
			$params = $request->getInput();
			$userId = $params->getInt('userId', $user->id);
			$roles  = new UserRoles($this->db, $this->log);

			if ($userId != $user->id && !$roles->userInRoleByName($user->id, RoleStrings::ADMINISTRATOR)) {
				$ret->setAsError("Invalid user identifier ({$userId}:{$user->id})");

				return $ret;
			}

			if ($userId != $user->id && $roles->userInRoleByName($userId, RoleStrings::ADMINISTRATOR)) {
				$ret->setAsError("Cannot delete other admins via API");

				return $ret;
			}

			$this->processEvent($ret, 'doDelete', new ParameterHelper([
				'id'    => $userId,
				'actor' => $user->id
			]));

			return $ret;
		}

		/**
		 * Attempts to retrieve a user's account information, only works for current user and administrators.
		 *
		 * @OA\Get(
		 *   path="/api/1.1/Account",
		 *   operationId="GetAccount",
		 *   tags={"Account"},
		 *   @OA\Parameter(
		 *     name="userId",
		 *     in="query",
		 *     description="unique user identifier",
		 *     required=false,
		 *     @OA\Schema(type="number")
		 *   ),
		 *   @OA\Response(
		 *     response="200",
		 *     description="Account data",
		 *     @OA\JsonContent(
		 *       @OA\Property(property="email",          type="string",  description="User's email address"),
		 *       @OA\Property(property="emailConfirmed", type="boolean", description="If user's email address has been confirmed"),
		 *       @OA\Property(property="id",             type="number",  description="User's unique identifier"),
		 *       @OA\Property(property="joined",         type="string",  description="Date and time the user joined"),
		 *       @OA\Property(property="lastActive",     type="string",  description="Date and time the user was last active, if available"),
		 *       @OA\Property(property="lastLogin",      type="string",  description="Date and time the user last logged in, if available")
		 *     )
		 *   )
		 * )
		 *
		 * @param Request $request The current request which routed to the endpoint.
		 * @param array|null $matches Array of matches returned by endpoint regex pattern.
		 * @throws \Stoic\Web\Resources\InvalidRequestException|\Stoic\Web\Resources\NonJsonInputException|\ReflectionException|\Exception
		 * @return Response
		 */
		public function get(Request $request, array $matches = null) : Response {
			$user   = $this->getUser();
			$ret    = $this->newResponse();
			$params = $request->getInput();
			$userId = $params->getInt('userId', $user->id);

			if ($user->id != $userId && !$this->userRoles->userInRoleByName($user->id, RoleStrings::ADMINISTRATOR)) {
				$ret->setAsError("Invalid profile identifier");

				return $ret;
			}

			$ret->setData(User::fromId($userId, $this->db, $this->log));

			return $ret;
		}

		/**
		 * Attempts to log the user into the system, returning either error information or the user ID and token.
		 *
		 * @OA\Post(
		 *   path="/api/1.1/Account/Login",
		 *   operationId="Login",
		 *   summary="Logs user into system",
		 *   description="Logs user into system",
		 *   tags={"Account"},
		 *   @OA\RequestBody(
		 *     required=true,
		 *     @OA\JsonContent(
		 *       type="object",
		 *       @OA\Property(property="email",    type="string"),
		 *       @OA\Property(property="key",      type="string"),
		 *       @OA\Property(property="provider", type="number")
		 *     )
		 *   ),
		 *   @OA\Response(
		 *     response="200",
		 *     description="OK",
		 *     @OA\JsonContent(
		 *       type="object",
		 *       @OA\Property(property="userId", type="number"),
		 *       @OA\Property(property="token",  type="string"),
		 *       @OA\Property(property="bearer", type="string")
		 *     )
		 *   )
		 * )
		 *
		 * @param Request $request The current request which routed to the endpoint.
		 * @param array|null $matches Array of matches returned by endpoint regex pattern.
		 * @throws \Stoic\Web\Resources\InvalidRequestException|\Stoic\Web\Resources\NonJsonInputException|\ReflectionException
		 * @return Response
		 */
		public function login(Request $request, array $matches = null) : Response {
			$ret = new Response(HttpStatusCodes::OK);
			$this->processEvent($ret, 'doLogin', $request->getInput());

			return $ret;
		}

		/**
		 * Attempts to log the current user out, returning error information or a success message.
		 *
		 * @OA\Post(
		 *   path="/api/1.1/Account/Logout",
		 *   operationId="Logout",
		 *   summary="Logs current user out of system",
		 *   description="Logs current user out of system",
		 *   tags={"Account"},
		 *   @OA\RequestBody(
		 *     required=true,
		 *     @OA\JsonContent(
		 *       type="object",
		 *       @OA\Property(property="userId", type="number"),
		 *       @OA\Property(property="token",  type="string"),
		 *     )
		 *   ),
		 *   @OA\Response(
		 *     response="200",
		 *     description="OK",
		 *     @OA\JsonContent(
		 *       type="object",
		 *       @OA\Property(property="userId", type="number"),
		 *       @OA\Property(property="token",  type="string")
		 *     )
		 *   )
		 * )
		 *
		 * @param Request $request The current request which routed to the endpoint.
		 * @param array|null $matches Array of matches returned by endpoint regex pattern.
		 * @throws \Stoic\Web\Resources\InvalidRequestException|\Stoic\Web\Resources\NonJsonInputException|\ReflectionException
		 * @return Response
		 */
		public function logout(Request $request, array $matches = null) : Response {
			$ret = $this->newResponse();
			$this->processEvent($ret, 'doLogout', $request->getInput());

			return $ret;
		}

		/**
		 * Attempts to process a UserEvents event and assign results to the Response object.
		 *
		 * @param Response $ret Response object for request.
		 * @param string $event Name of UserEvents method to call.
		 * @param ParameterHelper $params ParameterHelper object to supply to UserEvents method.
		 * @throws \Stoic\Web\Resources\InvalidRequestException|\Stoic\Web\Resources\NonJsonInputException|\ReflectionException
		 * @return void
		 */
		protected function processEvent(Response &$ret, string $event, ParameterHelper $params) : void {
			$evt = $this->events->$event($params);

			$ret->setStatus($evt->getResults()[0][UserEvents::STR_HTTP_CODE]);

			if ($evt->isBad()) {
				$ret->setData($evt->getMessages()[0]);
			} else {
				$ret->setData($evt->getResults()[0][UserEvents::STR_DATA]);
			}

			return;
		}

		/**
		 * Registers the controller endpoints.
		 *
		 * @return void
		 */
		protected function registerEndpoints() : void {
			$this->registerEndpoint('GET',  '/^\/?Account\/CheckEmail\/?/i',        'checkEmail',        null);
			$this->registerEndpoint('POST', '/^\/?Account\/CheckToken\/?/i',        'checkToken',        null);
			$this->registerEndpoint('POST', '/^\/?Account\/Create\/?/i',            'createUser',        RoleStrings::ADMINISTRATOR);
			$this->registerEndpoint('POST', '/^\/?Account\/Delete\/?/i',            'deleteUser',        true);
			$this->registerEndpoint('POST', '/^\/?Account\/Login\/?/i',             'login',             null);
			$this->registerEndpoint('POST', '/^\/?Account\/Logout\/?/i',            'logout',            true);
			$this->registerEndpoint('POST', '/^\/?Account\/Register\/?/i',          'registerUser',      null);
			$this->registerEndpoint('POST', '/^\/?Account\/ResetPassword\/?/i',     'resetPassword',     false);
			$this->registerEndpoint('POST', '/^\/?Account\/SendPasswordReset\/?/i', 'sendPasswordReset', false);
			$this->registerEndpoint('POST', '/^\/?Account\/Update\/?/i',            'update',            true);
			$this->registerEndpoint('GET',  '/^\/?Account\/?/i',                    'get',               true);

			return;
		}

		/**
		 * Attempts to register a user with the system.
		 *
		 * @OA\Post(
		 *   path="/api/1.1/Account/Register",
		 *   operationId="RegisterUser",
		 *   summary="Registers user with system",
		 *   description="Registers user with system",
		 *   tags={"Account"},
		 *   @OA\RequestBody(
		 *     required=true,
		 *     @OA\JsonContent(
		 *       type="object",
		 *       @OA\Property(property="email",      type="string"),
		 *       @OA\Property(property="key",        type="string"),
		 *       @OA\Property(property="confirmKey", type="string"),
		 *       @OA\Property(property="provider",   type="number")
		 *     )
		 *   ),
		 *   @OA\Response(
		 *     response="200",
		 *     description="OK",
		 *     @OA\JsonContent(
		 *       type="object",
		 *       @OA\Property(property="email",           type="string"),
		 *       @OA\Property(property="password",        type="string"),
		 *       @OA\Property(property="confirmPassword", type="string"),
		 *       @OA\Property(property="provider",        type="number"),
		 *       @OA\Property(property="emailConfirmed",  type="boolean")
		 *     )
		 *   )
		 * )
		 *
		 * @param Request $request The current request which routed to the endpoint.
		 * @param array|null $matches Array of matches returned by endpoint regex pattern.
		 * @throws \Stoic\Web\Resources\InvalidRequestException|\Stoic\Web\Resources\NonJsonInputException|\ReflectionException
		 * @return Response
		 */
		public function registerUser(Request $request, array $matches = null) : Response {
			$ret = $this->newResponse();
			$this->processEvent($ret, 'doRegister', $request->getInput());

			return $ret;
		}

		/**
		 * Attempts to reset the user's password.
		 *
		 * @OA\Post(
		 *   path="/api/1.1/Account/ResetPassword",
		 *   operationId="ResetPassword",
		 *   summary="Resets a user's password",
		 *   description="Resets a user's password",
		 *   tags={"Account"},
		 *   @OA\RequestBody(
		 *     required=true,
		 *     @OA\JsonContent(
		 *       type="object",
		 *       @OA\Property(property="userId",     type="number"),
		 *       @OA\Property(property="key",        type="string")
		 *     )
		 *   ),
		 *   @OA\Response(
		 *     response="200",
		 *     description="OK"
		 *   )
		 * )
		 *
		 * @param Request $request The current request which routed to the endpoint.
		 * @param array|null $matches Array of matches returned by endpoint regex pattern.
		 * @throws \Stoic\Web\Resources\InvalidRequestException|\Stoic\Web\Resources\NonJsonInputException|\ReflectionException
		 * @return Response
		 */
		public function resetPassword(Request $request, array $matches = null) : Response {
			$ret    = $this->newResponse();
			$params = $request->getInput();

			$this->processEvent($ret, 'doResetPassword', new ParameterHelper([
				'id'         => $params->getInt('userId', 0),
				'key'        => $params->getString('key', ''),
				'confirmKey' => $params->getString('key', '')
			]));

			return $ret;
		}

		/**
		 * Attempts to send the user a password reset token.
		 *
		 * @OA\Post(
		 *   path="/api/1.1/Account/SendPasswordReset",
		 *   operationId="SendPasswordReset",
		 *   summary="Sends user a password reset token",
		 *   description="Sends user a password reset token",
		 *   tags={"Account"},
		 *   @OA\RequestBody(
		 *     required=true,
		 *     @OA\JsonContent(
		 *       type="object",
		 *       @OA\Property(property="email", type="string")
		 *     )
		 *   ),
		 *   @OA\Response(
		 *     response="200",
		 *     description="OK",
		 *     @OA\JsonContent(type="boolean")
		 *   )
		 * )
		 *
		 * @param Request $request The current request which routed to the endpoint.
		 * @param array|null $matches Array of matches returned by endpoint regex pattern.
		 * @throws \Stoic\Web\Resources\InvalidRequestException|\Stoic\Web\Resources\NonJsonInputException|\ReflectionException|\Exception
		 * @return Response
		 */
		public function sendPasswordReset(Request $request, array $matches = null) : Response {
			global $Settings;

			$ret    = $this->newResponse();
			$params = $request->getInput();

			if (!$params->has('email')) {
				$ret->setAsError('Invalid parameters supplied for request');

				return $ret;
			}

			if (!sendResetEmail($params->getString('email'), PageHelper::getPage('api/1.1/index.php'), $Settings, $this->db, $this->log)) {
				$ret->setAsError("Failed to send reset email, check spelling and try again");

				return $ret;
			}

			$ret->setData(true);

			return $ret;
		}

		/**
		 * Attempts to update a user's account details.
		 *
		 * @OA\Post(
		 *   path="/api/1.1/Account/Update",
		 *   operationId="UpdateAccount",
		 *   summary="Updates a user's account details",
		 *   description="Updates a user's account details",
		 *   tags={"Account"},
		 *   @OA\RequestBody(
		 *     required=true,
		 *     @OA\JsonContent(
		 *       type="object",
		 *       @OA\Property(property="userId",      type="number"),
		 *       @OA\Property(property="email",       type="string"),
		 *       @OA\Property(property="password",    type="string"),
		 *       @OA\Property(property="oldPassword", type="string")
		 *     )
		 *   ),
		 *   @OA\Response(
		 *     response="200",
		 *     description="OK",
		 *     @OA\JsonContent(
		 *       type="object",
		 *       @OA\Property(property="email",           type="string"),
		 *       @OA\Property(property="password",        type="string"),
		 *       @OA\Property(property="confirmPassword", type="string"),
		 *       @OA\Property(property="provider",        type="number"),
		 *       @OA\Property(property="emailConfirmed",  type="boolean")
		 *     )
		 *   ),
		 *   security={
		 *     {"header_token": {}},
		 *     {"cookie_token2": {}}
		 *   }
		 * )
		 *
		 * @param Request $request The current request which routed to the endpoint.
		 * @param array|null $matches Array of matches returned by endpoint regex pattern.
		 * @throws \Exception
		 * @throws \ReflectionException
		 * @throws \Stoic\Web\Resources\InvalidRequestException
		 * @throws \Stoic\Web\Resources\NonJsonInputException
		 * @return Response
		 */
		public function update(Request $request, array $matches = null) : Response {
			$user   = $this->getUser();
			$ret    = $this->newResponse();
			$params = $request->getInput();
			$userId = $params->getInt('userId', $user->id);

			if ($user->id != $userId && !$this->userRoles->userInRoleByName($user->id, RoleStrings::ADMINISTRATOR)) {
				$ret->setAsError("Invalid user identifier provided");

				return $ret;
			}

			$userEvents = new UserEvents($this->db, $this->log);

			$postData = [
				'id'             => $userId,
				'actor'          => $user->id,
				'email'          => $params->getString('email'),
				'confirmEmail'   => $params->getString('email'),
				'emailConfirmed' => true,
				'key'            => $params->getString('password'),
				'confirmKey'     => $params->getString('password'),
				'oldKey'         => $params->getString('oldPassword'),
				'provider'       => LoginKeyProviders::PASSWORD
			];

			$update = $userEvents->doUpdate(new ParameterHelper($postData));

			if ($update->isBad()) {
				if ($update->hasMessages()) {
					$ret->setAsError($update->getMessages()[0]);
				} else {
					$ret->setAsError("Failed to update account info");
				}

				return $ret;
			}

			$ret->setData("Account info updated successfully");

			return $ret;
		}
	}
