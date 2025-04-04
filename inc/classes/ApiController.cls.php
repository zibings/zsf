<?php

	namespace Zibings;

	use Stoic\Log\Logger;
	use Stoic\Pdo\PdoHelper;
	use Stoic\Utilities\ParameterHelper;
	use Stoic\Utilities\ReturnHelper;
	use Stoic\Web\Request;
	use Stoic\Web\Api\BaseDbApi;
	use Stoic\Web\Api\Response;
	use Stoic\Web\Api\Stoic;

	/**
	 * Basic response data structure for including a status code and a message.
	 *
	 * @OA\Schema(
	 *   schema="StatusResponseData",
	 *   type="object",
	 *   description="Basic response data structure for including a status code and message",
	 *   @OA\Property(property="status",  type="number", description="Status code for response"),
	 *   @OA\Property(property="message", type="string", description="Message for response")
	 * )
	 *
	 * @package Zibings
	 */
	class StatusResponseData {
		/**
		 * Constructor for the basic status response data structure.
		 *
		 * @param int $status Status code for response data.
		 * @param null|string $message Optional string message for response data.
		 */
		public function __construct(
			public int $status,
			public null|string $message = "") {
			return;
		}
	}

	/**
	 * Basic controller class that offers some helper methods on top of the Stoic API class.
	 *
	 * @package Zibings
	 */
	abstract class ApiController extends BaseDbApi {
		/**
		 * @var PdoHelper
		 */
		protected $db;
		protected UserRoles $userRoles;


		/**
		 * Default constructor for ApiController objects.
		 *
		 * @param Stoic $stoic Internal instance of Stoic API object.
		 * @param \PDO $db Internal instance of PDO object.
		 * @param Logger|null $log Optional internal instance of Logger object, new instance created if not supplied.
		 */
		public function __construct(protected Stoic $stoic, PdoHelper $db, Logger $log = null) {
			parent::__construct($db, $log);

			$this->userRoles = new UserRoles($this->db, $this->log);

			$this->registerEndpoints();

			return;
		}

		/**
		 * @param \Stoic\Web\Api\Response $response Modified by adding messages to the response if there is a missing param
		 * @param \Stoic\Web\Request      $request
		 * @param array                   $params A string array of params to check and report back if it doesn't have them
		 * @return bool
		 * @throws \ReflectionException | \Stoic\Web\Resources\InvalidRequestException | \Stoic\Web\Resources\NonJsonInputException
		 */
		public function tryGetParams(Response &$response, Request $request, array $params): bool {
			$paramHelper = $request->getInput();
			$missingParams = [];

			foreach ($params as $param) {
				if (!$paramHelper->has($param)) {
					$missingParams[] = $param;
				}
			}

			if (count($missingParams) != 0) {
				$response->setAsError('Missing required parameters: ' . json_encode($missingParams));
				
				return false;
			}

			return true;
		}

		/**
		 * Attempts to assign the top message from a ReturnHelper object as the error to the Response object.
		 *
		 * @param Response $response Response object to set to error state.
		 * @param ReturnHelper $rh ReturnHelper object to try pulling messages from.
		 * @param string $defaultMessage Default message if ReturnHelper object has no messages.
		 * @throws \ReflectionException
		 * @return void
		 */
		protected function assignReturnHelperError(Response &$response, ReturnHelper $rh, string $defaultMessage) : void {
			if ($rh->hasMessages()) {
				$response->setAsError($rh->getMessages()[0]);
			} else {
				$response->setAsError($defaultMessage);
			}

			return;
		}

		/**
		 * Returns the two basic parameters for most API calls, the User object and the input parameters.
		 *
		 * [ 'user' => User{}, 'params' => ParameterHelper{} ]
		 *
		 * @param Request $request
		 * @throws \Stoic\Web\Resources\InvalidRequestException|\Stoic\Web\Resources\NonJsonInputException
		 * @return array
		 */
		protected function getBasicParams(Request $request) : array {
			return [
				'user'   => $this->getUser(),
				'params' => $request->getInput(),
			];
		}

		/**
		 * Attempts to retrieve the User object from the authorization header.
		 *
		 * @throws \Exception
		 * @return User
		 */
		protected function getUser() : User {
			$authHeader = $this->getUserAuthToken();
			$ret        = new User($this->db, $this->log);

			if (empty($authHeader)) {
				return $ret;
			}

			$token    = explode(':', base64_decode(str_replace('Bearer ', '', $authHeader)));
			$session  = UserSession::fromToken($token[1], $this->db, $this->log);
			$expiryDt = (new \DateTimeImmutable('now', new \DateTimeZone('UTC')))->sub(new \DateInterval('P1Y'));

			if ($session->id < 1 || $session->created < $expiryDt) {
				return $ret;
			}

			return User::fromId($session->userId, $this->db, $this->log);
		}

		/**
		 * Attempts to retrieve the Authorization token from the user's request.
		 *
		 * @return string
		 */
		protected function getUserAuthToken() : string {
			$authHeader    = "";
			$ret           = "";
			$hasAuthHeader = false;
			$headers       = getallheaders();

			foreach (array_keys($headers) as $header) {
				if (strtolower($header) === 'authorization') {
					$hasAuthHeader = true;
					$authHeader    = $headers[$header];

					break;
				}
			}

			if (!$hasAuthHeader) {
				$cookies = $this->stoic->getRequest()->getCookies();

				if (!$cookies->has(UserEvents::STR_COOKIE_TOKEN)) {
					return $ret;
				}

				$authHeader = $cookies->getString(UserEvents::STR_COOKIE_TOKEN, '');
			}

			return $authHeader;
		}

		/**
		 * Attempts to hydrate a UserSession object from the authorization token in the request.
		 *
		 * @throws \Exception
		 * @return UserSession
		 */
		protected function getUserSession() : UserSession {
			$authHeader = $this->getUserAuthToken();

			if (empty($authHeader)) {
				return new UserSession($this->db, $this->log);
			}

			$token    = explode(':', base64_decode(str_replace('Bearer ', '', $authHeader)));
			$session  = UserSession::fromToken($token[1], $this->db, $this->log);
			$expiryDt = (new \DateTimeImmutable('now', new \DateTimeZone('UTC')))->sub(new \DateInterval('P1Y'));

			if ($session->id < 1 || $session->created < $expiryDt) {
				return new UserSession($this->db, $this->log);
			}

			return $session;
		}

		/**
		 * Helper method to check if a user is an administrator or is themselves.
		 *
		 * @param User $user
		 * @param int $userId
		 * @return bool
		 */
		protected function isSelfOrAdmin(User $user, int $userId) : bool {
			return $user->id !== $userId && !$this->userRoles->userInRoleByName($user->id, RoleStrings::ADMINISTRATOR);
		}

		/**
		 * Returns a new StatusResponseData structure instance. Will create a new instance with '0' as the status and an
		 * empty string as the message if no arguments are provided.
		 *
		 * @param int|null $status Optional status code for response data, will be 0 if not supplied.
		 * @param null|string $message Optional status message for response data, will be an empty string if not supplied.
		 * @return StatusResponseData
		 */
		protected function newStatusResponseData(null|int $status = null, null|string $message = null) : StatusResponseData {
			return new StatusResponseData($status ?? 0, $message ?? '');
		}

		/**
		 * Helper method to register endpoints for member methods.
		 *
		 * @param null|string $verbs String value of applicable request verbs for endpoint, '*' for all verbs or use pipe (|) to combine multiple verbs.
		 * @param null|string $pattern String value of URL pattern for endpoint, `null` will set this endpoint as the 'default'.
		 * @param string $method Name of member method to use as callable for endpoint.
		 * @param mixed $authRoles Optional string, array of string values, or boolean value representing authorization requirements for endpoint.
		 * @return void
		 */
		protected function registerEndpoint(null|string $verbs, null|string $pattern, string $method, mixed $authRoles = null) : void {
			$this->stoic->registerEndpoint($verbs, $pattern, \Closure::fromCallable([$this, $method]), $authRoles);

			return;
		}

		/**
		 * Abstract method so child controllers register their endpoints.
		 *
		 * @return void
		 */
		abstract protected function registerEndpoints() : void;

		/**
		 * Helper method to perform basic ParameterHelper check around an action.
		 *
		 * @param ParameterHelper $params ParameterHelper instance to check keys against.
		 * @param array|string $keys String or array of strings for key(s) to check in ParameterHelper before executing action.
		 * @param callable $callable Callable to execute if key(s) pass existence/value guards.
		 * @param bool $canBeEmpty Optional toggle to allow empty values for key(s).
		 * @return void
		 */
		protected function tryParameterAction(ParameterHelper $params, string|array $keys, callable $callable, bool $canBeEmpty = false) : void {
			if (is_array($keys)) {
				foreach ($keys as $k) {
					if (!$params->has($k) || ($canBeEmpty === false && empty($params->get($k)))) {
						return;
					}
				}
			} else if (!$params->has($keys) || ($canBeEmpty === false && empty($params->get($keys)))) {
				return;
			}

			$callable();

			return;
		}
	}
