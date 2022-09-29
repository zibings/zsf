<?php

	namespace Api1_1;

	use OpenApi\Annotations as OA;

	use Stoic\Web\Api\Response;
	use Stoic\Web\Request;

	use Zibings\ApiController;
	use Zibings\RoleStrings;
	use Zibings\Users;
	use Zibings\UserRelations;
	use Zibings\UserRelationStages;
	use Zibings\VisibilityState;

	/**
	 * API controller that deals with user-related endpoints.
	 *
	 * @OA\Tag(
	 *   name="Search",
	 *   description="Search operations"
	 * )
	 *
	 * @package Zibings\Api1_1
	 */
	class Search extends ApiController {
		/**
		 * Registers the controller endpoints.
		 *
		 * @return void
		 */
		protected function registerEndpoints() : void {
			$this->registerEndpoint('GET',  '/^\/?Search\/Users\/Admin\/?/i', 'usersForAdmins', RoleStrings::ADMINISTRATOR);
			$this->registerEndpoint('GET',  '/^\/?Search\/Users\/?/i',       'users',          true);

			return;
		}

		/**
		 * Searches database for users who are visible.
		 *
		 * @OA\Get(
		 *   path="/api/1.1/Search/Users",
		 *   summary="Search visible users",
		 *   description="Search users who are configured as visible in the system",
		 *   tags={"Search"},
		 *   @OA\Parameter(
		 *     name="query",
		 *     in="query",
		 *     description="text to search by",
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
		 *         @OA\Property(property="email",          type="string"),
		 *         @OA\Property(property="emailConfirmed", type="boolean"),
		 *         @OA\Property(property="id",             type="number"),
		 *         @OA\Property(property="joined",         type="string"),
		 *         @OA\Property(property="lastLogin",      type="string"),
		 *         @OA\Property(property="displayName",    type="string"),
		 *         @OA\Property(property="birthday",       type="string"),
		 *         @OA\Property(property="realName",       type="string"),
		 *         @OA\Property(property="description",    type="string"),
		 *         @OA\Property(property="gender",         type="string")
		 *       )
		 *     )
		 *   )
		 * )
		 *
		 * @param Request $request The current request which routed to the endpoint.
		 * @param array|null $matches Array of matches returned by endpoint regex pattern.
		 * @throws \Exception
		 * @return Response
		 */
		public function users(Request $request, array $matches = null) : Response {
			$data      = [];
			$user      = $this->getUser();
			$ret       = $this->newResponse();
			$params    = $request->getInput();
			$usersRepo = new Users($this->db, $this->log);

			if (!$params->has('query')) {
				$ret->setAsError("Must provide query in order to search");

				return $ret;
			}

			$userRels      = [];
			$userRelations = (new UserRelations($this->db, $this->log))->getRelations($user->id);

			foreach ($userRelations as $rel) {
				if ($rel->stage->is(UserRelationStages::ACCEPTED)) {
					$userRels[strval(($rel->userOne == $user->id) ? $rel->userTwo : $rel->userOne)] = true;
				}
			}

			foreach ($usersRepo->searchUsersByIdentifiers($params->getString('query')) as $usd) {
				if ($user->id == $usd->id) {
					continue;
				}

				$tmp = [
					'email'          => $usd->email,
					'id'             => $usd->id,
					'displayName'    => $usd->displayName,
					'birthday'       => $usd->birthday->format('Y-m-d H:i:s'),
					'realName'       => $usd->realName,
					'description'    => $usd->description,
					'gender'         => $usd->gender->getName()
				];

				$areFriends = array_key_exists(strval($usd->id), $userRels) !== false;

				if (!$areFriends && $usd->visProfile->getValue() < VisibilityState::AUTHENTICATED) {
					continue;
				}

				$correlators = [
					'email'          => 'visEmail',
					'birthday'       => 'visBirthday',
					'realName'       => 'visRealName',
					'description'    => 'visDescription',
					'gender'         => 'visGender'
				];

				foreach ($correlators as $index => $visibility) {
					if (!$areFriends && $usd->$visibility->getValue() < VisibilityState::AUTHENTICATED) {
						$tmp[$index] = "";
					}
				}

				$data[] = $tmp;
			}

			$ret->setData($data);

			return $ret;
		}

		/**
		 * Searches database for users who are visible.
		 *
		 * @OA\Get(
		 *   path="/api/1.1/Search/Users/Admin",
		 *   summary="Search all users",
		 *   description="Search all users in the system",
		 *   tags={"Search"},
		 *   @OA\Parameter(
		 *     name="query",
		 *     in="query",
		 *     description="text to search by",
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
		 *         @OA\Property(property="email",          type="string"),
		 *         @OA\Property(property="emailConfirmed", type="boolean"),
		 *         @OA\Property(property="id",             type="number"),
		 *         @OA\Property(property="joined",         type="string"),
		 *         @OA\Property(property="lastLogin",      type="string"),
		 *         @OA\Property(property="displayName",    type="string"),
		 *         @OA\Property(property="birthday",       type="string"),
		 *         @OA\Property(property="realName",       type="string"),
		 *         @OA\Property(property="description",    type="string"),
		 *         @OA\Property(property="gender",         type="string")
		 *       )
		 *     )
		 *   )
		 * )
		 *
		 * @param Request $request The current request which routed to the endpoint.
		 * @param array|null $matches Array of matches returned by endpoint regex pattern.
		 * @throws \Exception
		 * @return Response
		 */
		public function usersForAdmins(Request $request, array $matches = null) : Response {
			$data      = [];
			$user      = $this->getUser();
			$ret       = $this->newResponse();
			$params    = $request->getInput();
			$usersRepo = new Users($this->db, $this->log);

			if (!$params->has('query')) {
				$ret->setAsError("Must provide query in order to search");

				return $ret;
			}

			foreach ($usersRepo->searchUsersByIdentifiers($params->getString('query'), false) as $usd) {
				if ($user->id == $usd->id) {
					continue;
				}

				$tmp = [
					'email'          => $usd->email,
					'emailConfirmed' => $usd->emailConfirmed,
					'id'             => $usd->id,
					'joined'         => $usd->joined->format('Y-m-d H:i:s'),
					'lastLogin'      => ($usd->lastLogin !== null) ? $usd->lastLogin->format('Y-m-d H:i:s') : '',
					'displayName'    => $usd->displayName,
					'birthday'       => $usd->birthday->format('Y-m-d H:i:s'),
					'realName'       => $usd->realName,
					'description'    => $usd->description,
					'gender'         => $usd->gender->getName()
				];

				$data[] = $tmp;
			}

			$ret->setData($data);

			return $ret;
		}
	}
