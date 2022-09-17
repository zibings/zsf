<?php

	namespace Zibings;

	use Stoic\Chain\DispatchBase;
	use Stoic\Chain\NodeBase;
	use Stoic\Web\Resources\ApiAuthorizationDispatch;

	/**
	 * Processing node that authorizes API requests by a session cookie.
	 *
	 * @package Zibings
	 */
	class ApiCookieAuthorizer extends NodeBase {
		/**
		 * Instantiates a new ApiCookieAuthorizer object.
		 *
		 * @return void
		 */
		public function __construct() {
			$this->setKey('ApiCookieAuthorizer');
			$this->setVersion('1.0.0');

			return;
		}

		/**
		 * Handles the processing of a given dispatch.
		 *
		 * @param mixed $sender Sender data, optional and thus can be 'null'.
		 * @param \Stoic\Chain\DispatchBase $dispatch Dispatch object to process.
		 * @throws \Exception
		 * @return void
		 */
		public function process(mixed $sender, DispatchBase &$dispatch) : void {
			if (!($dispatch instanceof ApiAuthorizationDispatch)) {
				return;
			}

			$roles = $dispatch->getRequiredRoles();

			if ($roles === false) {
				$dispatch->authorize();

				return;
			}

			$cookies = $sender->getRequest()->getCookies();

			if ($cookies->has(UserEvents::STR_COOKIE_TOKEN)) {
				$token    = $cookies->getString(UserEvents::STR_COOKIE_TOKEN, '');
				$session  = UserSession::fromToken($token, $sender->getDb(), $sender->getLog());
				$expiryDt = (new \DateTime('now', new \DateTimeZone('UTC')))->sub(new \DateInterval('P1Y'));

				if ($session->id < 1) {
					return;
				}

				if ($session->created < $expiryDt) {
					$session->delete();

					return;
				}

				if ($roles === true) {
					$dispatch->authorize();

					return;
				}

				if (!is_array($roles)) {
					$roles = [$roles];
				}

				$roleRepo = new UserRoles($sender->getDb(), $sender->getLog());

				foreach ($roles as $r) {
					if ($roleRepo->userInRoleByName($session->userId, $r)) {
						$dispatch->authorize();

						break;
					}
				}
			}

			return;
		}
	}