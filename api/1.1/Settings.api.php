<?php

	namespace Api1_1;

	use OpenApi\Annotations as OA;

	use Stoic\Log\Logger;
	use Stoic\Utilities\ParameterHelper;
	use Stoic\Web\Api\Response;
	use Stoic\Web\Api\Stoic;
	use Stoic\Web\Request;

	use Zibings\ApiController;
	use Zibings\RoleStrings;
	use Zibings\UserEvents;
	use Zibings\UserRoles;
	use Zibings\UserSettings;
	use Zibings\UserSettingsRepo;
	use Zibings\UserVisibilities;

	/**
	 * API controller that deals with user settings endpoints.
	 *
	 * @OA\Tag(
	 *   name="Settings",
	 *   description="Operations for user settings"
	 * )
	 *
	 * @package Zibings\Api1_1
	 */
	class Settings extends ApiController {
		/**
		 * Initiates a new Settings object.
		 *
		 * @param Stoic $stoic Internal instance of Stoic API object.
		 * @param \PDO $db Internal instance of PDO object.
		 * @param Logger|null $log Optional Logger object for internal use.
		 * @param UserRoles|null $userRoles Optional UserRoles respository for internal use.
		 * @param UserSettingsRepo|null $userSettings Optional UserSettingsRepo repository for internal use.
		 * @return void
		 */
		public function __construct(
			Stoic $stoic,
			\PDO $db,
			Logger $log                                   = null,
			protected UserRoles|null $userRoles           = null,
			protected UserSettingsRepo|null $userSettings = null) {
			parent::__construct($stoic, $db, $log);

			if ($this->userRoles === null) {
				$this->userRoles = new UserRoles($this->db, $this->log);
			}

			if ($this->userSettings === null) {
				$this->userSettings = new UserSettingsRepo($this->db, $this->log);
			}

			return;
		}

		/**
		 * Attempts to retrieve a user's settings, only works for current user and administrators.
		 *
		 * @OA\Get(
		 *   path="/api/1.1/Settings",
		 *   summary="Retrieve's a user's settings",
		 *   description="Retrieve's a user's settings",
		 *   tags={"Settings"},
		 *   @OA\Parameter(
		 *     name="userId",
		 *     in="query",
		 *     description="optional user identifier",
		 *     required=false,
		 *     @OA\Schema(type="number")
		 *   ),
		 *   @OA\Response(
		 *     response="200",
		 *     description="OK",
		 *     @OA\JsonContent(
		 *       type="object",
		 *       @OA\Property(property="htmlEmails", type="boolean"),
		 *       @OA\Property(property="playSounds", type="boolean"),
		 *       @OA\Property(property="userId",     type="number")
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

			$ret->setData(UserSettings::fromUser($userId, $this->db, $this->log));

			return $ret;
		}

		/**
		 * Registers the controller endpoints.
		 *
		 * @return void
		 */
		protected function registerEndpoints() : void {
			$this->registerEndpoint('POST', '/^\/?Settings\/?/i', 'update', true);
			$this->registerEndpoint('GET',  '/^\/?Settings\/?/i', 'get',    true);

			return;
		}

		/**
		 * Attempts to update a user's settings
		 *
		 * @OA\Post(
		 *   path="/api/1.1/Settings",
		 *   summary="Update user settings",
		 *   description="Update user settings",
		 *   tags={"Settings"},
		 *   @OA\RequestBody(
		 *     required=false,
		 *     @OA\JsonContent(
		 *       type="object",
		 *       @OA\Property(property="userId",         type="number"),
		 *       @OA\Property(property="visBirthday",    type="number"),
		 *       @OA\Property(property="visDescription", type="number"),
		 *       @OA\Property(property="visEmail",       type="number"),
		 *       @OA\Property(property="visGender",      type="number"),
		 *       @OA\Property(property="visProfile",     type="number"),
		 *       @OA\Property(property="visRealName",    type="number"),
		 *       @OA\Property(property="visSearches",    type="number")
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
		 * @param array|null $matches Array of matches returned by endpoint regex pattern.
		 * @throws \Exception|\ReflectionException|\Stoic\Web\Resources\InvalidRequestException|\Stoic\Web\Resources\NonJsonInputException
		 * @return Response
		 */
		public function update(Request $request, array $matches = null) : Response {
			$user       = $this->getUser();
			$ret        = $this->newResponse();
			$params     = $request->getInput();
			$userId     = $params->getInt('userId', $user->id);
			$userEvents = new UserEvents($this->db, $this->log);

			if ($user->id != $userId && !$this->userRoles->userInRoleByName($user->id, RoleStrings::ADMINISTRATOR)) {
				$ret->setAsError("Invalid user identifier provided");

				return $ret;
			}

			$userVis    = UserVisibilities::fromUser($userId, $this->db, $this->log);

			$postData = [
				'id'             => $userId,
				'actor'          => $user->id,
				'settings'       => [
					'htmlEmails'   => $params->has('set_htmlEmails'),
					'playSounds'   => $params->has('set_playSounds')
				],
				'visibilities'   => [
					'birthday'     => $params->getInt('visBirthday',    $userVis->birthday->getValue()),
					'description'  => $params->getInt('visDescription', $userVis->description->getValue()),
					'email'        => $params->getInt('visEmail',       $userVis->email->getValue()),
					'gender'       => $params->getInt('visGender',      $userVis->gender->getValue()),
					'profile'      => $params->getInt('visProfile',     $userVis->profile->getValue()),
					'realName'     => $params->getInt('visRealName',    $userVis->realName->getValue()),
					'searches'     => $params->getInt('visSearches',    $userVis->searches->getValue())
				]
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

			$ret->setData("Settings info updated successfully");

			return $ret;
		}
	}
