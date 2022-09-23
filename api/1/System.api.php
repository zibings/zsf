<?php

	namespace Api1;

	use Stoic\Log\Logger;
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
	 * @package Zibings\Api1
	 */
	class System extends ApiController {
		/**
		 * @param Stoic $stoic Internal instance of Stoic API object.
		 * @param \PDO $db Internal instance of PDO object.
		 * @param Logger|null $log Optional Logger object for internal use.
		 * @param Users|null $users Optional Users repository object for internal use.
		 * @return void
		 */
		public function __construct(
			Stoic $stoic,
			\PDO $db,
			Logger $log = null,
			protected ?Users $users = null) {
			parent::__construct($stoic, $db, $log);

			if ($this->users === null) {
				$this->users = new Users($this->db, $this->log);
			}

			return;
		}

		/**
		 * Registers the controller endpoints.
		 *
		 * @return void
		 */
		protected function registerEndpoints() : void {
			$this->registerEndpoint('GET', '/^System\/Version\/?/i',          'getVersion',          false);
			$this->registerEndpoint('GET', '/^System\/DashboardMetrics\/?/i', 'getDashboardMetrics', RoleStrings::ADMINISTRATOR);

			return;
		}

		/**
		 * Retrieves basic dashboard statistics for administrators.
		 *
		 * @param Request $request The current request which routed to the endpoint.
		 * @param array|null $matches Array of matches returned by endpoint regex pattern.
		 * @return Response
		 */
		public function getDashboardMetrics(Request $request, array $matches = null) : Response {
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
		 * @param Request $request The current request which routed to the endpoint.
		 * @param array|null $matches Array of matches returned by endpoint regex pattern.
		 * @return Response
		 */
		public function getVersion(Request $request, array $matches = null) : Response {
			global $Settings;

			$ret = $this->newResponse();
			$ret->setData($Settings->get(SettingsStrings::SYSTEM_VERSION));

			return $ret;
		}
	}
