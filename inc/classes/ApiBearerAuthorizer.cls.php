<?php

	namespace Zibings;

	use Stoic\Chain\DispatchBase;
	use Stoic\Chain\NodeBase;
	use Stoic\Web\Resources\ApiAuthorizationDispatch;

	/**
	 * Processing node that authorizes API requests by bearer token.
	 *
	 * @package Zibings
	 */
	class ApiBearerAuthorizer extends NodeBase {
		/**
		 * Instantiates a new ApiBearerAuthorizer object.
		 *
		 * @return void
		 */
		public function __construct() {
			$this->setKey('ApiBearerAuthorizer');
			$this->setVersion('1.0.0');

			return;
		}

		/**
		 * Handles the processing of a given dispatch.
		 *
		 * @param mixed $sender Sender data, optional and thus can be 'null'.
		 * @param DispatchBase $dispatch Dispatch object to process.
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

			$authHeader    = "";
			$hasAuthHeader = false;
			$headers       = getallheaders();

			foreach (array_keys($headers) as $header) {
				if (strtolower($header) === 'authorization') {
					$hasAuthHeader = true;
					$authHeader    = $header;

					break;
				}
			}

			if ($hasAuthHeader) {
				$token = explode(':', base64_decode(str_replace('Bearer ', '', $headers[$authHeader])));
				$session = UserSession::fromToken($token[1], $sender->getDb(), $sender->getLog());

				if (isSessionValidForRoles($session, $roles, $sender->getDb(), $sender->getLog())) {
					$dispatch->authorize();
				}
			}

			return;
		}
	}
