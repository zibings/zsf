<?php

	namespace Api1_1;

	use OpenApi\Annotations as OA;

	use Stoic\Log\Logger;
	use Stoic\Pdo\PdoHelper;
	use Stoic\Utilities\ParameterHelper;
	use Stoic\Web\Api\Response;
	use Stoic\Web\Api\Stoic;
	use Stoic\Web\Request;

	use Zibings\ApiController;
	use Zibings\RoleStrings;
	use Zibings\UserEvents;
	use Zibings\UserProfile;

	/**
	 * API controller that deals with user profile endpoints.
	 *
	 * @OA\Tag(
	 *   name="Profile",
	 *   description="User profile operations"
	 * )
	 *
	 * @package Zibings\Api1_1
	 */
	class Profile extends ApiController {
		/**
		 * Instantiates a new Account object.
		 *
		 * @param Stoic $stoic Internal instance of Stoic API object.
		 * @param \PDO $db Internal instance of PDO object.
		 * @param Logger|null $log Optional Logger object for internal use.
		 * @return void
		 */
		public function __construct(
			Stoic $stoic,
			PdoHelper $db,
			null|Logger $log = null
		) {
			parent::__construct($stoic, $db, $log);

			return;
		}

		/**
		 * Checks if a display name is available.
		 *
		 * @OA\Post(
		 *   path="/Profile/CheckDisplayName",
		 *   operationId="checkDisplayName",
		 *   summary="Check if display name is available",
		 *   description="Check if display name is available",
		 *   tags={"Profile"},
		 *   @OA\RequestBody(
		 *     required=true,
		 *     @OA\JsonContent(
		 *       type="object",
		 *       @OA\Property(property="displayName", type="string")
		 *     )
		 *   ),
		 *   @OA\Response(
		 *     response="200",
		 *     description="OK",
		 *     @OA\JsonContent(type="string")
		 *   )
		 * )
		 *
		 * @param Request $request The current request which routed to the endpoint.
		 * @param null|array $matches Array of matches returned by endpoint regex pattern.
		 * @throws \ReflectionException|\Stoic\Web\Resources\InvalidRequestException|\Stoic\Web\Resources\NonJsonInputException
		 * @return Response
		 */
		public function checkDisplayName(Request $request, null|array $matches = null) : Response {
			$ret    = $this->newResponse();
			$params = $request->getInput();

			if (!$params->has('displayName')) {
				$ret->setAsError("No display name provided");

				return $ret;
			}

			$profile = UserProfile::fromDisplayName($params->getString('displayName', ''), $this->db, $this->log);

			if ($profile->userId > 0) {
				$ret->setData($this->newStatusResponseData(1, "Invalid display name, already in use"));

				return $ret;
			}

			$ret->setData($this->newStatusResponseData(0, "Display name available"));

			return $ret;
		}

		/**
		 * Attempts to retrieve a user's profile information, only works for current user and administrators.
		 *
		 * @OA\Get(
		 *   path="/Profile",
		 *   operationId="getProfile",
		 *   summary="Retrieves user's profile information",
		 *   description="Retrieves user's profile information",
		 *   tags={"Profile"},
		 *   @OA\Parameter(
		 *     name="userId",
		 *     in="query",
		 *     description="unique user identifier",
		 *     required=false,
		 *     @OA\Schema(type="number")
		 *   ),
		 *   @OA\Response(
		 *     response="200",
		 *     description="OK",
		 *     @OA\JsonContent(
		 *       type="object",
		 *       @OA\Property(property="userId",      type="number"),
		 *       @OA\Property(property="displayName", type="string"),
		 *       @OA\Property(property="birthday",    type="string"),
		 *       @OA\Property(property="realName",    type="string"),
		 *       @OA\Property(property="description", type="string"),
		 *       @OA\Property(property="gender",      type="number")
		 *     )
		 *   ),
		 *   security={
		 *     {"header_token": {}},
		 *     {"cookie_token": {}}
		 *   }
		 * )
		 *
		 * @param Request $request The current request which routed to the endpoint.
		 * @param null|array $matches Array of matches returned by endpoint regex pattern.
		 * @throws \Stoic\Web\Resources\InvalidRequestException|\Stoic\Web\Resources\NonJsonInputException|\ReflectionException|\Exception
		 * @return Response
		 */
		public function get(Request $request, null|array $matches = null) : Response {
			$user   = $this->getUser();
			$ret    = $this->newResponse();
			$params = $request->getInput();
			$userId = $params->getInt('userId', $user->id);

			if ($user->id != $userId && !$this->userRoles->userInRoleByName($user->id, RoleStrings::ADMINISTRATOR)) {
				$ret->setAsError("Invalid profile identifier");

				return $ret;
			}

			$ret->setData(UserProfile::fromUser($userId, $this->db, $this->log));

			return $ret;
		}

		/**
		 * Registers the controller endpoints.
		 *
		 * @return void
		 */
		protected function registerEndpoints() : void {
			$this->registerEndpoint('GET',  '/^\/?Profile\/CheckDisplayName\/?$/i', 'checkDisplayName', null);
			$this->registerEndpoint('POST', '/^\/?Profile\/?$/i',                   'update',           true);
			$this->registerEndpoint('GET',  '/^\/?Profile\/?$/i',                   'get',              true);

			return;
		}

		/**
		 * Attempts to update a user's settings
		 *
		 * @OA\Post(
		 *   path="/Profile",
		 *   operationId="updateProfile",
		 *   summary="Update user profile",
		 *   description="Update user profile",
		 *   tags={"Profile"},
		 *   @OA\RequestBody(
		 *     required=false,
		 *     @OA\JsonContent(
		 *       type="object",
		 *       @OA\Property(property="userId",      type="number"),
		 *       @OA\Property(property="birthday",    type="string"),
		 *       @OA\Property(property="description", type="string"),
		 *       @OA\Property(property="displayName", type="string"),
		 *       @OA\Property(property="gender",      type="number"),
		 *       @OA\Property(property="realName",    type="string")
		 *     )
		 *   ),
		 *   @OA\Response(
		 *     response="200",
		 *     description="OK",
		 *     @OA\JsonContent(type="string")
		 *   ),
		 *   security={
		 *     {"header_token": {}},
		 *     {"cookie_token": {}}
		 *   }
		 * )
		 *
		 * @param Request $request The current request which routed to the endpoint.
		 * @param null|array $matches Array of matches returned by endpoint regex pattern.
		 * @throws \Exception|\ReflectionException|\Stoic\Web\Resources\InvalidRequestException|\Stoic\Web\Resources\NonJsonInputException
		 * @return Response
		 */
		public function update(Request $request, null|array $matches = null) : Response {
			$user       = $this->getUser();
			$ret        = $this->newResponse();
			$params     = $request->getInput();
			$userId     = $params->getInt('userId', $user->id);
			$userEvents = new UserEvents($this->db, $this->log);

			if ($user->id != $userId && !$this->userRoles->userInRoleByName($user->id, RoleStrings::ADMINISTRATOR)) {
				$ret->setAsError("Invalid user identifier provided");

				return $ret;
			}

			$postData = [
				'id'             => $userId,
				'actor'          => $user->id,
				'profile'        => [
					'birthday'     => $params->getString('birthday'),
					'description'  => $params->getString('description'),
					'displayName'  => $params->getString('displayName'),
					'gender'       => $params->getInt('gender'),
					'realName'     => $params->getString('realName')
				],
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

			$ret->setData("Profile info updated successfully");

			return $ret;
		}
	}
