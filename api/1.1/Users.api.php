<?php

	namespace Api1_1;

	use OpenApi\Annotations as OA;

	use Stoic\Log\Logger;
	use Stoic\Web\Api\Stoic;
	use Stoic\Web\Api\Response;
	use Stoic\Web\Request;

	use Zibings\ApiController;
	use Zibings\RoleStrings;
	use Zibings\Users as ZUsers;

	use function Zibings\camelCaseToWords;

	/**
	 * API controller that deals with user management endpoints.
	 *
	 * @OA\Tag(
	 *   name="Users",
	 *   description="Group operations for users"
	 * )
	 *
	 * @package Zibings\Api1_1
	 */
	class Users extends ApiController {
		/**
		 * Instantiates a new Users object.
		 *
		 * @param Stoic $stoic Internal instance of Stoic API object.
		 * @param \PDO $db Internal instance of PDO object.
		 * @param Logger|null $log Optional Logger object for internal use.
		 * @param ZUsers|null $users Optional UserEvent object for internal use.
		 * @return void
		 */
		public function __construct(
			Stoic                 $stoic,
			\PDO                  $db,
			Logger                $log   = null,
			protected ZUsers|null $users = null) {
			parent::__construct($stoic, $db, $log);

			if ($this->users === null) {
				$this->users = new ZUsers($this->db, $this->log);
			}

			return;
		}

		/**
		 * Retrieves all users in the database.
		 *
		 * @OA\Get(
		 *   path="/Users",
		 *   operationId="getUsers",
		 *   summary="Retrieves all users in system",
		 *   description="Retrieves all users in system",
		 *   tags={"Users"},
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
		 *   )
		 * )
		 *
		 * @OA\Get(
		 *   path="/Users/Columns",
		 *   operationId="getUsersColumns",
		 *   summary="Retrieves columns returned by ",
		 *   description="Retrieves all users in system",
		 *   tags={"Users"},
		 *   @OA\Response(
		 *     response="200",
		 *     description="OK",
		 *     @OA\JsonContent(
		 *       type="array",
		 *       @OA\Items(
		 *         type="object",
		 *         @OA\Property(property="field",    type="string"),
		 *         @OA\Property(property="header",   type="string"),
		 *         @OA\Property(property="filter",   type="boolean"),
		 *         @OA\Property(property="sortable", type="boolean"),
		 *         @OA\Property(property="display",  type="boolean")
		 *       )
		 *     )
		 *   )
		 * )
		 *
		 * @param Request $request The current request which routed to the endpoint.
		 * @param array|null $matches Array of matches returned by endpoint regex pattern.
		 * @return Response
		 */
		public function get(Request $request, array $matches = null) : Response {
			$ret = $this->newResponse();

			if (str_ends_with($matches[0][0], '/Columns')) {
				$dat  = [];
				$cols = $this->users->getColumnsForGetAll();

				foreach ($cols as $col) {
					$dat[] = [
						'field'    => $col,
						'header'   => camelCaseToWords($col),
						'filter'   => match($col) {
							'id','emailConfirmed' => false,
							default               => true
						},
						'sortable' => $col !== 'emailConfirmed',
						'display'  => true,
					];
				}

				$ret->setData($dat);
			} else {
				$ret->setData($this->users->getAll());
			}

			return $ret;
		}

		/**
		 * Registers the controller endpoints.
		 *
		 * @return void
		 */
		protected function registerEndpoints() : void {
			$this->registerEndpoint('GET',  '/^\/?Users\/GetAll\/Columns\/?/i', 'get', RoleStrings::ADMINISTRATOR);
			$this->registerEndpoint('GET',  '/^\/?Users\/GetAll\/?/i',          'get', RoleStrings::ADMINISTRATOR);

			return;
		}
	}
