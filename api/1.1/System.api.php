<?php

	namespace Api1_1;

	use OpenApi\Annotations as OA;

	use Stoic\Log\Logger;
	use Stoic\Pdo\PdoHelper;
	use Stoic\Web\Api\Response;
	use Stoic\Web\Api\Stoic;
	use Stoic\Web\Request;

	use Zibings\ApiController;
	use Zibings\RoleStrings;
	use Zibings\SettingsStrings;
	use Zibings\Users;

	/**
	 * API controller that provides basic system endpoints.
	 *
	 * @OA\Tag(
	 *   name="System",
	 *   description="System-wide operations"
	 * )
	 *
	 * @package Zibings\Api1_1
	 */
	class System extends ApiController {
		/**
		 * Instantiates a new System object.
		 *
		 * @param Stoic $stoic Internal instance of Stoic API object.
		 * @param \PDO $db Internal instance of PDO object.
		 * @param Logger|null $log Optional Logger object for internal use.
		 * @param Users|null $users Optional Users repository object for internal use.
		 * @return void
		 */
		public function __construct(
			Stoic $stoic,
			PdoHelper $db,
			null|Logger $log = null,
			protected ?Users $users = null
		) {
			parent::__construct($stoic, $db, $log);

			if ($this->users === null) {
				$this->users = new Users($this->db, $this->log);
			}

			return;
		}

		/**
		 * Retrieves basic dashboard statistics for administrators.
		 *
		 * @OA\Get(
		 *   path="/System/DashboardMetrics",
		 *   operationId="getDashboardMetrics",
		 *   summary="Returns some basic metrics",
		 *   description="Returns basic metrics for an admin dashboard",
		 *   tags={"System"},
		 *   @OA\Response(
		 *     response="200",
		 *     description="OK",
		 *     @OA\JsonContent(
		 *       type="object",
		 *       @OA\Property(property="dau", type="number", description="Daily Active Users"),
		 *       @OA\Property(property="mau", type="number", description="Monthly Active Users"),
		 *       @OA\Property(property="tu",  type="number", description="Total Users"),
		 *       @OA\Property(property="tvu", type="number", description="Total Verified Users")
		 *     )
		 *   ),
		 *   security={
		 *     {"admin_header_token": {}},
		 *     {"admin_cookie_token": {}}
		 *   }
		 * )
		 *
		 * @param Request $request The current request which routed to the endpoint.
		 * @param null|array $matches Array of matches returned by endpoint regex pattern.
		 * @return Response
		 */
		public function getDashboardMetrics(Request $request, null|array $matches = null) : Response {
			$ret = $this->newResponse();
			$ret->setData([
				'dau'  => $this->users->getDailyActiveUserCount(),
				'mau'  => $this->users->getMonthlyActiveUserCount(),
				'tu'   => $this->users->getTotalUsers(),
				'tvu'  => $this->users->getTotalVerifiedUsers()
			]);

			return $ret;
		}

		/**
		 * Returns the currently configured system version.
		 *
		 * @OA\Get(
		 *   path="/System/Version",
		 *   operationId="getVersion",
		 *   summary="Returns defined system version",
		 *   description="Returns system version as defined in the siteSettings file",
		 *   tags={"System"},
		 *   @OA\Response(
		 *     response="200",
		 *     description="OK",
		 *     @OA\JsonContent(
		 *       type="string",
		 *       description="Currently defined version number"
		 *     )
		 *   )
		 * )
		 *
		 * @param Request $request The current request which routed to the endpoint.
		 * @param null|array $matches Array of matches returned by endpoint regex pattern.
		 * @return Response
		 */
		public function getVersion(Request $request, null|array $matches = null) : Response {
			global $Settings;

			$ret = $this->newResponse();
			$ret->setData($Settings->get(SettingsStrings::SYSTEM_VERSION));

			return $ret;
		}

		/**
		 * Registers the controller endpoints.
		 *
		 * @return void
		 */
		protected function registerEndpoints() : void {
			$this->registerEndpoint('GET', '/^\/?System\/Version\/?$/i',          'getVersion',          false);
			$this->registerEndpoint('GET', '/^\/?System\/DashboardMetrics\/?$/i', 'getDashboardMetrics', RoleStrings::ADMINISTRATOR);

			return;
		}
	}
