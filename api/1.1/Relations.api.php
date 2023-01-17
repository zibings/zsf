<?php

	namespace Api1_1;

	use OpenApi\Annotations as OA;

	use Stoic\Log\Logger;
	use Stoic\Web\Api\Response;
	use Stoic\Web\Api\Stoic;
	use Stoic\Web\Request;

	use Zibings\ApiController;
	use Zibings\RoleStrings;
	use Zibings\UserRelations;
	use Zibings\UserRoles;

	/**
	 * API controller that deals with user relation endpoints.
	 *
	 * @OA\Tag(
	 *   name="Relations",
	 *   description="User relation operations"
	 * )
	 *
	 * @package Zibings\Api1_1
	 */
	class Relations extends ApiController {
		/**
		 * Instantiates a new Account object.
		 *
		 * @param Stoic $stoic Internal instance of Stoic API object.
		 * @param \PDO $db Internal instance of PDO object.
		 * @param Logger|null $log Optional Logger object for internal use.
		 * @param UserRoles|null $userRoles Optional UserRoles object for internal use.
		 * @param UserRelations|null $userRels Optional UserRelations object for internal use.
		 * @return void
		 */
		public function __construct(
			Stoic $stoic,
			\PDO $db,
			Logger $log                            = null,
			protected UserRoles|null $userRoles    = null,
			protected UserRelations|null $userRels = null) {
			parent::__construct($stoic, $db, $log);

			if ($this->userRoles === null) {
				$this->userRoles = new UserRoles($this->db, $this->log);
			}

			if ($this->userRels === null) {
				$this->userRels = new UserRelations($this->db, $this->log);
			}

			return;
		}

		/**
		 * Attempts to retrieve any relations for the given user.
		 *
		 * @OA\Get(
		 *   path="/Relations",
		 *   operationId="getRelations",
		 *   summary="Retrieve any user relations",
		 *   description="Retrieve any user relations",
		 *   tags={"Relations"},
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
		 *       type="array",
		 *       @OA\Items(
		 *         type="object",
		 *         @OA\Property(property="created", type="string"),
		 *         @OA\Property(property="stage",   type="number"),
		 *         @OA\Property(property="origin",  type="boolean"),
		 *         @OA\Property(property="userOne", type="number"),
		 *         @OA\Property(property="userTwo", type="number")
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

			$ret->setData($this->userRels->getRelations($userId));

			return $ret;
		}

		/**
		 * Registers the controller endpoints.
		 *
		 * @return void
		 */
		protected function registerEndpoints() : void {
			$this->registerEndpoint('GET',  '/^\/?Relations\/RelatedTo\/?$/i', 'relatedTo',      true);
			$this->registerEndpoint('POST', '/^\/?Relations\/Remove\/?$/i',    'removeRelation', true);
			$this->registerEndpoint('POST', '/^\/?Relations\/Set\/?$/i',       'setRelation',    true);
			$this->registerEndpoint('GET',  '/^\/?Relations\/?$/i',            'get',            true);

			return;
		}

		/**
		 * Determines if the user is related to the given identifier.
		 *
		 * @OA\Get(
		 *   path="/Relations/RelatedTo",
		 *   operationId="getRelatedTo",
		 *   summary="Checks if users are related",
		 *   description="Checks if users are related",
		 *   tags={"Relations"},
		 *   @OA\Parameter(
		 *     name="userId",
		 *     in="query",
		 *     description="unique user identifier",
		 *     required=true,
		 *     @OA\Schema(type="number")
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
		 * @param \Stoic\Web\Request $request The current request which routed to the endpoint.
		 * @param array|null $matches Array of matches returned by endpoint regex pattern.
		 * @throws \Stoic\Web\Resources\InvalidRequestException|\Stoic\Web\Resources\NonJsonInputException|\ReflectionException|\Exception
		 * @return Response
		 */
		public function relatedTo(Request $request, array $matches = null) : Response {
			$user   = $this->getUser();
			$ret    = $this->newResponse();
			$params = $request->getInput();

			if (!$params->has('userId')) {
				$ret->setAsError('Invalid parameters supplied for request');

				return $ret;
			}

			$ret->setData((new UserRelations($this->db, $this->log))->areRelated($user->id, $params->getInt('userId')));

			return $ret;
		}

		/**
		 * Attempts to remove a relationship between the authenticated user and another.
		 *
		 * @OA\Post(
		 *   path="/Relations/Remove",
		 *   operationId="removeRelation",
		 *   summary="Removes the given relationship",
		 *   description="Removes the given relationship",
		 *   tags={"Relations"},
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
		 *     {"header_token": {}},
		 *     {"cookie_token": {}}
		 *   }
		 * )
		 *
		 * @param \Stoic\Web\Request $request The current request which routed to the endpoint.
		 * @param array|null $matches Array of matches returned by endpoint regex pattern.
		 * @throws \Stoic\Web\Resources\InvalidRequestException|\Stoic\Web\Resources\NonJsonInputException|\ReflectionException|\Exception
		 * @return Response
		 */
		public function removeRelation(Request $request, array $matches = null) : Response {
			$user   = $this->getUser();
			$ret    = $this->newResponse();
			$params = $request->getInput();

			if (!$params->has('userId')) {
				$ret->setAsError('Invalid parameters supplied');

				return $ret;
			}

			$ret->setData((new UserRelations($this->db, $this->log))->deleteRelation($user->id, $params->getInt('userId')));

			return $ret;
		}

		/**
		 * Sets the stage of relationship between two users.
		 *
		 * @OA\Post(
		 *   path="/Relations/Set",
		 *   operationId="setRelation",
		 *   summary="Set stage of relationship",
		 *   description="Set stage of relationship",
		 *   tags={"Relations"},
		 *   @OA\RequestBody(
		 *     required=true,
		 *     @OA\JsonContent(
		 *       type="object",
		 *       @OA\Property(property="userId", type="number"),
		 *       @OA\Property(property="stage",  type="number")
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
		 * @throws \Stoic\Web\Resources\InvalidRequestException|\Stoic\Web\Resources\NonJsonInputException|\ReflectionException|\Exception
		 * @return Response
		 */
		public function setRelation(Request $request, array $matches = null) : Response {
			$user   = $this->getUser();
			$ret    = $this->newResponse();
			$params = $request->getInput();
			$rels   = new UserRelations($this->db, $this->log);

			if (!$params->hasAll('userId', 'stage')) {
				$ret->setAsError('Invalid parameters supplied');

				return $ret;
			}

			$ret->setData($rels->changeStage($user->id, $params->getInt('userId'), $params->getInt('stage')));

			return $ret;
		}
	}
