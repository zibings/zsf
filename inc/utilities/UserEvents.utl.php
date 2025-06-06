<?php

	namespace Zibings;

	use Stoic\Chain\ChainHelper;
	use Stoic\Chain\DispatchBase;
	use Stoic\Chain\NodeBase;
	use Stoic\Log\Logger;
	use Stoic\Pdo\StoicDbClass;
	use Stoic\Utilities\EnumBase;
	use Stoic\Utilities\ParameterHelper;
	use Stoic\Utilities\ReturnHelper;
	use Stoic\Web\Resources\HttpStatusCodes;
	use Stoic\Web\Resources\ServerIndices as SI;

	/**
	 * Available types of user events.
	 *
	 * @package Zibings
	 */
	class UserEventTypes extends EnumBase {
		const int CONFIRM           = 1;
		const int CONFIRM_PRE       = 9;
		const int CREATE            = 2;
		const int CREATE_PRE        = 10;
		const int DELETE            = 3;
		const int DELETE_PRE        = 11;
		const int LOGIN             = 4;
		const int LOGIN_PRE         = 12;
		const int LOGOUT            = 5;
		const int LOGOUT_PRE        = 13;
		const int REGISTER          = 6;
		const int REGISTER_PRE      = 14;
		const int RESETPASSWORD     = 7;
		const int RESETPASSWORD_PRE = 15;
		const int UPDATE            = 8;
		const int UPDATE_PRE        = 16;
	}

	/**
	 * Utility class that provides the ability to subscribe to major user events.
	 *
	 * @package Zibings
	 */
	class UserEvents extends StoicDbClass {
		const string STR_ACTOR          = 'actor';
		const string STR_BEARER         = 'bearer';
		const string STR_BIRTHDAY       = 'birthday';
		const string STR_CONFIRM_EMAIL  = 'confirmEmail';
		const string STR_CONFIRM_KEY    = 'confirmKey';
		const string STR_COOKIE_TOKEN   = 'zsf_token';
		const string STR_DATA           = 'data';
		const string STR_DESCRIPTION    = 'description';
		const string STR_DISPLAY_NAME   = 'displayName';
		const string STR_EMAIL          = 'email';
		const string STR_EMAILCONFIRMED = 'emailConfirmed';
		const string STR_GENDER         = 'gender';
		const string STR_HTML_EMAILS    = 'htmlEmails';
		const string STR_HTTP_CODE      = 'httpCode';
		const string STR_ID             = 'id';
		const string STR_KEY            = 'key';
		const string STR_OLD_KEY        = 'oldKey';
		const string STR_PLAY_SOUNDS    = 'playSounds';
		const string STR_PROFILE        = 'profile';
		const string STR_PROVIDER       = 'provider';
		const string STR_REAL_NAME      = 'realName';
		const string STR_ROLES          = 'roles';
		const string STR_SEARCHES       = 'searches';
		const string STR_SESSION_USERID = 'zUserID';
		const string STR_SESSION_TOKEN  = 'zToken';
		const string STR_SETTINGS       = 'settings';
		const string STR_TOKEN          = 'token';
		const string STR_USERID         = 'userId';
		const string STR_VISIBILITIES   = 'visibilities';


		/**
		 * Collection of event chains.
		 *
		 * @var ChainHelper[]
		 */
		protected array $events = [
			UserEventTypes::CONFIRM           => null,
			UserEventTypes::CONFIRM_PRE       => null,
			UserEventTypes::CREATE            => null,
			UserEventTypes::CREATE_PRE        => null,
			UserEventTypes::DELETE            => null,
			UserEventTypes::DELETE_PRE        => null,
			UserEventTypes::LOGIN             => null,
			UserEventTypes::LOGIN_PRE         => null,
			UserEventTypes::LOGOUT            => null,
			UserEventTypes::LOGOUT_PRE        => null,
			UserEventTypes::REGISTER          => null,
			UserEventTypes::REGISTER_PRE      => null,
			UserEventTypes::RESETPASSWORD     => null,
			UserEventTypes::RESETPASSWORD_PRE => null,
			UserEventTypes::UPDATE            => null,
			UserEventTypes::UPDATE_PRE        => null,
		];


		/**
		 * Instantiates a new UserEvents object.
		 *
		 * @param \PDO $db PDO instance for use by object.
		 * @param null|Logger $log Logger instance for use by object, defaults to new instance.
		 */
		public function __construct(\PDO $db, Logger $log = null) {
			parent::__construct($db, $log);

			foreach (array_keys($this->events) as $evt) {
				$this->events[$evt] = new ChainHelper();
			}

			return;
		}

		/**
		 * Helper method to assign an error to the given ReturnHelper, log the error, and optionally assign an HTTP status code.
		 *
		 * @param ReturnHelper $ret ReturnHelper to assign error message to for reference.
		 * @param string $error Error message to reference in ReturnHelper and logs.
		 * @param int|HttpStatusCodes $status Optional HTTP status code, defaults to INTERNAL_SERVER_ERROR if not supplied.
		 * @throws \ReflectionException
		 * @return void
		 */
		protected function assignError(ReturnHelper &$ret, string $error, int|HttpStatusCodes $status = HttpStatusCodes::INTERNAL_SERVER_ERROR) : void {
			$code = HttpStatusCodes::tryGet($status);

			if ($code->getValue() === null) {
				return;
			}

			$ret->addMessage($error);
			$this->log->error($error);
			$ret->addResult([self::STR_HTTP_CODE => $code->getValue()]);

			return;
		}

		/**
		 * Performs email confirmation. If completed successfully, the UserEventTypes::CONFIRM chain is traversed with a new
		 * UserEventsConfirmDispatch object. The following parameters are required:
		 *
		 * [
		 *   'token' => (string) 'some-token' # encoded token and userId combo
		 * ]
		 *
		 * Resulting ReturnHelper will include a suggested HTTP status code in the 'httpCode' index.
		 *
		 * @param ParameterHelper $params Parameters provided to perform the event.
		 * @throws \ReflectionException|\Exception
		 * @return ReturnHelper
		 */
		public function doConfirm(ParameterHelper $params) : ReturnHelper {
			$ret = new ReturnHelper();

			if (!$params->has(self::STR_TOKEN)) {
				$this->assignError($ret, "Missing parameters for confirmation");

				return $ret;
			}

			$tok = explode(':', base64_decode($params->getString(self::STR_TOKEN)));
			$token = UserToken::fromToken($tok[1], intval($tok[0]), $this->db, $this->log);

			if ($token->userId < 1) {
				$this->assignError($ret, "Invalid confirmation supplied");

				return $ret;
			}

			$user = User::fromId($token->userId, $this->db, $this->log);

			if ($user->id < 1 || $user->emailConfirmed === true) {
				$this->assignError($ret, "Invalid user supplied");

				return $ret;
			}

			if (!$this->touchPreEvent(UserEventTypes::CONFIRM_PRE, new UserEventPreConfirmDispatch($this->db, $this->log, $tok[1], $params), $ret, "Pre-confirmation event chain stopped the event")) {
				return $ret;
			}

			$user->emailConfirmed = true;
			$user->update();

			$token->delete();

			$this->touchEvent(UserEventTypes::CONFIRM, new UserEventConfirmDispatch($user, $this->db, $this->log));

			$ret->makeGood();
			$ret->addResult([self::STR_HTTP_CODE => HttpStatusCodes::OK]);

			return $ret;
		}

		/**
		 * Performs user creation. If completed successfully, the UserEventTypes::CREATE chain is traversed with a new
		 * UserEventsCreateDispatch object. The following parameters are required:
		 *
		 * [
		 *   'email'          => (string) 'user@domain.com', # the email address for the new user
		 *   'key'            => (string) 'someKey',         # the login key value for the new user
		 *   'confirmKey'     => (string) 'someKey',         # repeat of the login key value
		 *   'provider'       => (int|LoginKeyProviders) 1,  # the login key provider type
		 *   'emailConfirmed' => (bool) false,               # whether the new user's email is confirmed,
		 *   'profile'        => (array) [                   # optional user profile data
		 *     'birthday'    => (string) 'YYYY-MM-DD',       # the user's birthday
		 *     'description' => (string) '',                 # the user's profile description
		 *     'displayName' => (string) '',                 # the user's display name
		 *     'gender'      => (int|UserGenders) 0,         # the user's chosen gender
		 *     'realName'    => (string) ''                  # the user's real name
		 *   ],
		 *   'settings'       => (array) [                   # optional user settings data
		 *     'htmlEmails' => (bool) false,                 # whether the user prefers HTML emails
		 *     'playSounds' => (bool) false                  # whether the user prefers to play sounds
		 *   ],
		 *   'visibilities'   => (array) [                   # optional user visibilities data
		 *     'birthday'   => (int|VisibilityState) 0,      # the visibility state of the user's birthday
		 *     'description' => (int|VisibilityState) 0,     # the visibility state of the user's profile description
		 *     'email'       => (int|VisibilityState) 0,     # the visibility state of the user's email
		 *     'gender'      => (int|VisibilityState) 0,     # the visibility state of the user's gender
		 *     'profile'     => (int|VisibilityState) 0,     # the visibility state of the user's profile
		 *     'realName'    => (int|VisibilityState) 0,     # the visibility state of the user's real name
		 *     'searches'    => (int|VisibilityState) 0      # the visibility state of the user's searches
		 *   ]
		 * ]
		 *
		 * After the User and their LoginKey have been created, the system will attempt to create (without guaranteeing) the
		 * following entries for the user before traversing the chain:
		 *
		 *   - UserProfile
		 *   - UserSettings
		 *   - UserVisibilities
		 * 
		 * Resulting ReturnHelper will include a suggested HTTP status code in the 'httpCode' index and the user object if the
		 * operation was successful:
		 *
		 * [
		 *   'httpCode' => 0,     # suggested HTTP status code
		 *   'data'     => User{} # User object with created user data
		 * ]
		 *
		 * @param ParameterHelper $params Parameters provided to perform the event.
		 * @throws \ReflectionException|\Exception
		 * @return ReturnHelper
		 */
		public function doCreate(ParameterHelper $params) : ReturnHelper {
			$ret = new ReturnHelper();

			if (!$params->hasAll(self::STR_EMAIL, self::STR_KEY, self::STR_CONFIRM_KEY, self::STR_PROVIDER, self::STR_EMAILCONFIRMED)) {
				$this->assignError($ret, "Missing parameters for account creation");

				return $ret;
			}

			$key            = $params->getString(self::STR_KEY);
			$email          = $params->getString(self::STR_EMAIL);
			$provider       = $params->getInt(self::STR_PROVIDER);
			$confirmKey     = $params->getString(self::STR_CONFIRM_KEY);
			$emailConfirmed = $params->getBool(self::STR_EMAILCONFIRMED, false);

			if ($key !== $confirmKey || empty($key)) {
				$this->assignError($ret, "Invalid parameters for account creation");

				return $ret;
			}

			if (!$this->touchPreEvent(UserEventTypes::CREATE_PRE, new UserEventPreCreateDispatch($this->db, $this->log, $email, $key, $provider, $emailConfirmed, $params), $ret, "Pre-creation event chain stopped the event")) {
				return $ret;
			}

			$user = new User($this->db, $this->log);

			try {
				$user->email          = $email;
				$user->emailConfirmed = $emailConfirmed;
				$user->joined         = new \DateTimeImmutable('now', new \DateTimeZone('UTC'));
				$create               = $user->create();

				if ($create->isBad()) {
					$ret->addMessage("Error creating user account");

					if ($create->hasMessages()) {
						$ret->addMessages($create->getMessages());
					}

					$ret->addResult([self::STR_HTTP_CODE => HttpStatusCodes::INTERNAL_SERVER_ERROR]);

					return $ret;
				}

				$login           = new LoginKey($this->db, $this->log);
				$login->userId   = $user->id;
				$login->provider = new LoginKeyProviders($provider);
				$login->key      = ($login->provider->is(LoginKeyProviders::PASSWORD)) ? password_hash($key, PASSWORD_DEFAULT) : $key;
				$create          = $login->create();

				if ($create->isBad()) {
					$ret->addMessage("Failed to create user login key, removing new user account");

					if ($create->hasMessages()) {
						$ret->addMessages($create->getMessages());
					}

					$user->delete();
					$ret->addResult([self::STR_HTTP_CODE => HttpStatusCodes::INTERNAL_SERVER_ERROR]);

					return $ret;
				}

				$profile = new UserProfile($this->db, $this->log);
				$profile->userId = $user->id;

				if ($params->has(self::STR_PROFILE)) {
					$pParams              = new ParameterHelper($params->get(self::STR_PROFILE));
					$profile->birthday    = new \DateTimeImmutable($pParams->getString(self::STR_BIRTHDAY, $profile->birthday->format('Y-m-d G:i:s')), new \DateTimeZone('UTC'));
					$profile->description = $pParams->getString(self::STR_DESCRIPTION, $profile->description);
					$displayName          = $pParams->getString(self::STR_DISPLAY_NAME);

					if ($displayName !== $profile->displayName && UserProfile::validDisplayName($displayName)) {
						$p = UserProfile::fromDisplayName($displayName, $this->db, $this->log);

						if ($p->userId < 1) {
							$profile->displayName = $displayName;
						} else {
							$ret->addMessage("Couldn't change display name, invalid or already in use");
						}
					}

					$profile->gender   = new UserGenders($pParams->getInt(self::STR_GENDER, $profile->gender->getValue()));
					$profile->realName = $pParams->getString(self::STR_REAL_NAME, $profile->realName);
				}

				$profile->create();

				$settings = new UserSettings($this->db, $this->log);
				$settings->userId = $user->id;

				if ($params->has(self::STR_SETTINGS)) {
					$sParams              = new ParameterHelper($params->get(self::STR_SETTINGS));
					$settings->htmlEmails = $sParams->getBool(self::STR_HTML_EMAILS, $settings->htmlEmails);
					$settings->playSounds = $sParams->getBool(self::STR_PLAY_SOUNDS, $settings->playSounds);
				}

				$settings->create();

				$visibilities = new UserVisibilities($this->db, $this->log);
				$visibilities->userId = $user->id;

				if ($params->has(self::STR_VISIBILITIES)) {
					$vParams                   = new ParameterHelper($params->get(self::STR_VISIBILITIES));
					$visibilities->birthday    = new VisibilityState($vParams->getInt(self::STR_BIRTHDAY, $visibilities->birthday->getValue()));
					$visibilities->description = new VisibilityState($vParams->getInt(self::STR_DESCRIPTION, $visibilities->description->getValue()));
					$visibilities->email       = new VisibilityState($vParams->getInt(self::STR_EMAIL, $visibilities->email->getValue()));
					$visibilities->gender      = new VisibilityState($vParams->getInt(self::STR_GENDER, $visibilities->gender->getValue()));
					$visibilities->profile     = new VisibilityState($vParams->getInt(self::STR_PROFILE, $visibilities->profile->getValue()));
					$visibilities->realName    = new VisibilityState($vParams->getInt(self::STR_REAL_NAME, $visibilities->realName->getValue()));
					$visibilities->searches    = new VisibilityState($vParams->getInt(self::STR_SEARCHES, $visibilities->searches->getValue()));
				}

				$visibilities->create();

				$this->touchEvent(UserEventTypes::CREATE, new UserEventCreateDispatch($user, $params, $this->db, $this->log));

				$ret->makeGood();
				$ret->addResult([
					self::STR_HTTP_CODE => HttpStatusCodes::OK,
					self::STR_DATA      => $user
				]);
			} catch (\Exception $ex) {
				$this->assignError($ret, "Error while creating user account: " . $ex->getMessage());
			}

			return $ret;
		}

		/**
		 * Performs user deletion. If completed successfully, the UserEventTypes::DELETE chain is traversed with a new
		 * UserEventsDeleteDispatch object. The following parameters are required:
		 *
		 * [
		 *   'id'    => 1, # identifier for user being deleted
		 *   'actor' => 2  # identifier of the user performing the deletion
		 * ]
		 *
		 * If the 'actor' is the user (the user is deleting their own account), set the value of 'actor' to 0.
		 *
		 * The DELETE event will be called before the user is deleted to allow for cleanup before final user deletion and to respect
		 * foreign key constraints. After the hook has been called and returned, the system will clean up the following tables in
		 * order:
		 *
		 *   - UserVisibilities
		 *   - UserToken
		 *   - UserSettings
		 *   - UserSession
		 *   - UserRole
		 *   - UserRelation
		 *   - UserProfile
		 *   - UserCustomVisibility
		 *   - UserContact
		 *   - LoginKey
		 *   - User
		 *
		 * Resulting ReturnHelper will include a suggested HTTP status code in the 'httpCode' index.
		 *
		 * @param ParameterHelper $params Parameters for performing operation.
		 * @throws \ReflectionException|\Exception
		 * @return ReturnHelper
		 */
		public function doDelete(ParameterHelper $params) : ReturnHelper {
			$ret = new ReturnHelper();

			if (!$params->hasAll(self::STR_ID, self::STR_ACTOR)) {
				$this->assignError($ret, "Failed to delete user, incomplete parameters");

				return $ret;
			}

			$id    = $params->getInt(self::STR_ID);
			$actor = $params->getInt(self::STR_ACTOR);

			if ($id === $actor) {
				$this->assignError($ret, "Failed to delete user, can't delete yourself");

				return $ret;
			}

			if ($actor > 0 && !(new UserRoles($this->db, $this->log))->userInRoleByName($actor, RoleStrings::ADMINISTRATOR)) {
				$this->assignError($ret, "Failed to delete user, only admins can delete other users");

				return $ret;
			}

			$user = User::fromId($id, $this->db, $this->log);

			if ($user->id < 1) {
				$this->assignError($ret, "Failed to delete user, invalid information provided");

				return $ret;
			}

			if (!$this->touchPreEvent(UserEventTypes::DELETE_PRE, new UserEventPreDeleteDispatch($this->db, $this->log, $id, $actor, $params), $ret, "Pre-delete event chain stopped the event")) {
				return $ret;
			}

			$this->touchEvent(UserEventTypes::DELETE, new UserEventDeleteDispatch($user, $this->db, $this->log));

			(new UserTokens($this->db, $this->log))->deleteAllForUser($user->id);
			(new UserSessions($this->db, $this->log))->deleteAllForUser($user->id);
			(new UserRoles($this->db, $this->log))->deleteAllForUser($user->id);
			(new UserRelations($this->db, $this->log))->deleteAllForUser($user->id);
			(new UserProfiles($this->db, $this->log))->deleteAllForUser($user->id);
			(new UserContacts($this->db, $this->log))->deleteAllForUser($user->id);
			(new LoginKeys($this->db, $this->log))->deleteAllForUser($user->id);

			if ($user->delete()->isGood()) {
				$ret->makeGood();
			}

			$ret->addResult([
				self::STR_HTTP_CODE => HttpStatusCodes::OK
			]);

			return $ret;
		}

		/**
		 * Performs user authentication. If completed successfully, the UserEventTypes::LOGIN chain is traversed with a new
		 * UserEventLoginDispatch object. The following parameters are required:
		 *
		 * [
		 *   'email'    => (string) 'user@domain.com', # the email address of the user in question
		 *   'key'      => (string) 'someKey',         # the login key value of the user in question
		 *   'provider' => (int|LoginKeyProviders) 1,  # the login key provider type
		 *   'roles'    => (string|array) []           # optional user roles to enforce for login
		 * ]
		 *
		 * Resulting ReturnHelper will include a suggested HTTP status code in the 'httpCode' index and the session data if the
		 * operation was successful:
		 *
		 * [
		 *   'httpCode' => (int) 0,
		 *   'data'     => [
		 *     'userId' => (int) 0,     # the authenticated user's identifier
		 *     'token'  => (string) '', # the new session token generated for the user
		 *     'bearer' => (string) ''  # the bearer token to use in headers
		 *   ]
		 * ]
		 *
		 * @param ParameterHelper $params Parameters provided to perform the event.
		 * @throws \ReflectionException|\Exception
		 * @return ReturnHelper
		 */
		public function doLogin(ParameterHelper $params) : ReturnHelper {
			/** @var AndyM84\Config\ConfigContainer */
			global $Settings;

			$ret = new ReturnHelper();

			if (!$params->hasAll(self::STR_EMAIL, self::STR_KEY, self::STR_PROVIDER)) {
				$this->assignError($ret, "Missing parameters for authorization");

				return $ret;
			}

			$email     = $params->getString(self::STR_EMAIL);
			$key       = $params->getString(self::STR_KEY);
			$provider  = $params->getInt(self::STR_PROVIDER);
			$user      = User::fromEmail($email, $this->db, $this->log);
			$userRoles = new UserRoles($this->db, $this->log);

			if ($user->id < 1) {
				$this->assignError($ret, "Invalid credentials supplied");

				return $ret;
			}

			if (!$user->emailConfirmed) {
				$this->assignError($ret, "Cannot login without confirming your email");

				return $ret;
			}

			$login = LoginKey::fromUserAndProvider($user->id, $provider, $this->db, $this->log);

			if ($login->userId < 1) {
				$this->assignError($ret, "No login available for user");

				return $ret;
			}

			$challengePassed = false;

			switch ($login->provider->getValue()) {
				case LoginKeyProviders::PASSWORD:
					if (!password_verify($key, $login->key)) {
						$this->assignError($ret, "Invalid credentials provided");
						UserAuthHistory::createFromUserId($user->id, AuthHistoryActions::LOGIN, new ParameterHelper($_SERVER), "Failed login action from event system (bad password)", $this->db, $this->log);

						break;
					}

					if (password_needs_rehash($login->key, PASSWORD_DEFAULT)) {
						$login->key = password_hash($key, PASSWORD_DEFAULT);
						$login->update();
					}

					$challengePassed = true;

					break;
				case LoginKeyProviders::FACEBOOK:
				case LoginKeyProviders::TWITTER:
				case LoginKeyProviders::TWITCH:
				case LoginKeyProviders::GITHUB:
				case LoginKeyProviders::REDDIT:
					if ($key !== $login->key) {
						$this->assignError($ret, "Invalid credentials provided");
						UserAuthHistory::createFromUserId($user->id, AuthHistoryActions::LOGIN, new ParameterHelper($_SERVER), "Failed login action from event system (wrong provider)", $this->db, $this->log);

						break;
					}

					$challengePassed = true;

					break;
				default:
					break;
			}

			if (!$challengePassed) {
				$this->assignError($ret, "Failed to validate credentials");

				return $ret;
			}

			if ($params->has(self::STR_ROLES)) {
				$roles = $params->get(self::STR_ROLES);

				if (is_array($roles)) {
					$roleFound = false;

					foreach ($roles as $role) {
						if ($userRoles->userInRoleByName($user->id, $role)) {
							$roleFound = true;

							break;
						}
					}

					if (!$roleFound) {
						$this->assignError($ret, "User does not have required role to login");

						return $ret;
					}
				} else {
					if (!$userRoles->userInRoleByName($user->id, $roles)) {
						$this->assignError($ret, "User does not have required role to login");

						return $ret;
					}
				}
			}

			if (!$this->touchPreEvent(UserEventTypes::LOGIN_PRE, new UserEventPreLoginDispatch($this->db, $this->log, $email, $key, $provider, $params->get(self::STR_ROLES, []), $params), $ret, "Pre-login event chain stopped the event")) {
				return $ret;
			}

			$user->lastLogin = new \DateTimeImmutable('now', new \DateTimeZone('UTC'));

			if ($user->update()->isBad()) {
				$this->log->warning("Failed to update last login time for user '{$email}'");
			}

			if (array_key_exists(SI::REMOTE_ADDR, $_SERVER) === false) {
				$_SERVER[SI::REMOTE_ADDR] = '::1';
			}

			$session           = new UserSession($this->db, $this->log);
			$session->address  = $_SERVER[SI::REMOTE_ADDR];
			$session->hostname = gethostbyaddr($session->address);
			$session->token    = UserSession::generateGuid(false);
			$session->userId   = $user->id;
			$sCreate           = $session->create();

			if ($sCreate->isBad()) {
				if ($sCreate->hasMessages()) {
					foreach ($sCreate->getMessages() as $msg) {
						$this->log->error($msg);
					}
				} else {
					$this->log->error("Failed to create user session after authenticating");
					UserAuthHistory::createFromUserId($user->id, AuthHistoryActions::LOGIN, new ParameterHelper($_SERVER), "Failed login action from event system (failed session init)", $this->db, $this->log);
				}

				$ret->addMessage("Failed to authenticate user");
				$ret->addResult([self::STR_HTTP_CODE => HttpStatusCodes::INTERNAL_SERVER_ERROR]);

				return $ret;
			}

			if (!STOIC_DISABLE_SESSION) {
				$_SESSION[self::STR_SESSION_USERID] = $user->id;
				$_SESSION[self::STR_SESSION_TOKEN]  = $session->token;
			}

			$bearerToken = base64_encode("{$user->id}:{$session->token}");

			if (STOIC_API_AUTH_COOKIE) {
				$secure = !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off';

				setcookie(self::STR_COOKIE_TOKEN, $bearerToken, time() + $Settings->get(SettingsStrings::SESSION_TIMEOUT, 31536000), '/', '', $secure, true);
			}

			$ret->makeGood();
			$ret->addResult([
				self::STR_HTTP_CODE => HttpStatusCodes::OK,
				self::STR_DATA      => [
					self::STR_USERID => $user->id,
					self::STR_TOKEN  => $session->token,
					self::STR_BEARER => $bearerToken
				]
			]);

			$this->touchEvent(UserEventTypes::LOGIN, new UserEventLoginDispatch($user, $session->token, $this->db, $this->log));

			return $ret;
		}

		/**
		 * Performs user logout. If completed successfully, the UserEventTypes::LOGOUT chain is traversed with a new
		 * UserEventLogoutDispatch object. The following parameters are optional:
		 *
		 * [
		 *   'userId' => (int) 1,              # user identifier
		 *   'token'  => (string) 'some-token' # user session token
		 * ]
		 *
		 * If the optional parameters are not included, the system will attempt to find the active user session and logout that
		 * session out.
		 *
		 * Resulting ReturnHelper will include a suggested HTTP status code in the 'httpCode' index and the invalidated session data
		 * if the operation was successful:
		 *
		 * [
		 *   'httpCode' => (int) 0,
		 *   'data'     => [
		 *     'userId' => (int) 0,    # the newly-logged-out user's identifier
		 *     'token'  => (string) '' # the session token invalidated for the user
		 *   ]
		 * ]
		 *
		 * @param ParameterHelper $params Parameters for performing operation.
		 * @throws \ReflectionException|\Exception
		 * @return ReturnHelper
		 */
		public function doLogout(ParameterHelper $params) : ReturnHelper {
			$userId      = 0;
			$token       = null;
			$useSession  = false;
			$ret         = new ReturnHelper();
			$session     = new ParameterHelper($_SESSION);
			$userSession = new UserSession($this->db, $this->log);

			if ($params->hasAll(self::STR_USERID, self::STR_TOKEN)) {
				$userId      = $params->getInt(self::STR_USERID, 0);
				$token       = $params->getString(self::STR_TOKEN, '');
			} else {
				$useSession = true;
				$userId     = $session->getInt(self::STR_SESSION_USERID, 0);
				$token      = $session->getString(self::STR_SESSION_TOKEN, '');
			}

			if ($userId < 1 || empty($token)) {
				$ret->addMessage("Invalid session information");

				return $ret;
			}

			if (!$this->touchPreEvent(UserEventTypes::LOGOUT_PRE, new UserEventPreLogoutDispatch($this->db, $this->log, $userId, $token, $params), $ret, "Pre-logout event chain stopped the event")) {
				return $ret;
			}

			if ($useSession && !STOIC_DISABLE_SESSION) {
				if ($session->has(self::STR_SESSION_USERID)) {
					unset($_SESSION[self::STR_SESSION_USERID]);
				}

				if ($session->has(self::STR_SESSION_TOKEN)) {
					unset($_SESSION[self::STR_SESSION_TOKEN]);
				}
			}

			if (STOIC_API_AUTH_COOKIE) {
				$secure = !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off';

				setcookie(self::STR_COOKIE_TOKEN, '', time() - 3600, '/', '', $secure, true);
			}

			if ($userId === null || $token === null) {
				$this->assignError($ret, "Invalid session information");

				return $ret;
			}

			$userSession = UserSession::fromToken($token, $this->db, $this->log);

			if ($userSession->userId != $userId) {
				$this->assignError($ret, "Invalid session identifier");

				return $ret;
			}

			$delete = $userSession->delete();

			if ($delete->isBad()) {
				if ($delete->hasMessages()) {
					$this->assignError($ret, $delete->getMessages()[0]);
				} else {
					$this->assignError($ret, "Failed to delete session");
				}

				return $ret;
			}

			$this->touchEvent(UserEventTypes::LOGOUT, new UserEventLogoutDispatch($userSession, $this->db, $this->log));

			$ret->makeGood();
			$ret->addResult([
				self::STR_HTTP_CODE => HttpStatusCodes::OK,
				self::STR_DATA      => [
					self::STR_USERID  => $userSession->userId,
					self::STR_TOKEN   => $userSession->token
				]
			]);

			return $ret;
		}

		/**
		 * Performs user registration. If completed successfully, the UserEventTypes::CREATE chain is traversed with a new
		 * UserEventsRegisterDispatch object. The following parameters are required:
		 *
		 * [
		 *   'email'          => (string) 'user@domain.com', # the email address for the new user
		 *   'key'            => (string) 'someKey',         # the login key value for the new user
		 *   'confirmKey'     => (string) 'someKey',         # confirm the login key value for the new user
		 *   'provider'       => (int|LoginKeyProviders) 1,  # the login key provider type
		 *   'profile'        => (array) [                   # optional user profile data
		 *      'birthday'    => (string) 'YYYY-MM-DD',       # the user's birthday
		 *      'description' => (string) '',                 # the user's profile description
		 *      'displayName' => (string) '',                 # the user's display name
		 *      'gender'      => (int|UserGenders) 0,         # the user's chosen gender
		 *      'realName'    => (string) ''                  # the user's real name
		 *    ],
		 *    'settings'       => (array) [                   # optional user settings data
		 *      'htmlEmails' => (bool) false,                 # whether the user prefers HTML emails
		 *      'playSounds' => (bool) false                  # whether the user prefers to play sounds
		 *    ],
		 *    'visibilities'   => (array) [                   # optional user visibilities data
		 *      'birthday'   => (int|VisibilityState) 0,      # the visibility state of the user's birthday
		 *      'description' => (int|VisibilityState) 0,     # the visibility state of the user's profile description
		 *      'email'       => (int|VisibilityState) 0,     # the visibility state of the user's email
		 *      'gender'      => (int|VisibilityState) 0,     # the visibility state of the user's gender
		 *      'profile'     => (int|VisibilityState) 0,     # the visibility state of the user's profile
		 *      'realName'    => (int|VisibilityState) 0,     # the visibility state of the user's real name
		 *      'searches'    => (int|VisibilityState) 0      # the visibility state of the user's searches
		 *    ]
		 * ]
		 *
		 * After the User and their LoginKey have been created, the system will attempt to create (without guaranteeing) the
		 * following entries for the user before traversing the chain:
		 *
		 *   - UserProfile
		 *   - UserSettings
		 *   - UserVisibilities
		 *
		 * NOTE: This event does NOT automatically send any emails for confirmation.
		 *
		 * Resulting ReturnHelper will include a suggested HTTP status code in the 'httpCode' index and the user object if the
		 * operation was successful:
		 *
		 * [
		 *   'httpCode' => 0,     # suggested HTTP status code
		 *   'data'     => User{} # User object with created user data
		 * ]
		 *
		 * @param ParameterHelper $params Parameters provided to perform the event.
		 * @throws \ReflectionException|\Exception
		 * @return ReturnHelper
		 */
		public function doRegister(ParameterHelper $params) : ReturnHelper {
			$ret = new ReturnHelper();

			if (!$params->hasAll(self::STR_EMAIL, self::STR_KEY, self::STR_CONFIRM_KEY, self::STR_PROVIDER)) {
				$this->assignError($ret, "Missing parameters for account creation");

				return $ret;
			}

			$key            = $params->getString(self::STR_KEY);
			$email          = $params->getString(self::STR_EMAIL);
			$provider       = $params->getInt(self::STR_PROVIDER);
			$confirmKey     = $params->getString(self::STR_CONFIRM_KEY);

			if ($key !== $confirmKey || empty($key)) {
				$this->assignError($ret, "Invalid parameters for account creation");

				return $ret;
			}

			$user = new User($this->db, $this->log);

			if (!$this->touchPreEvent(UserEventTypes::REGISTER_PRE, new UserEventPreRegisterDispatch($this->db, $this->log, $email, $key, $provider, $params), $ret, "Pre-registration event chain stopped the event")) {
				return $ret;
			}

			try {
				$user->email          = $email;
				$user->emailConfirmed = false;
				$user->joined         = new \DateTimeImmutable('now', new \DateTimeZone('UTC'));
				$create               = $user->create();

				if ($create->isBad()) {
					$ret->addMessage("Error creating user account");

					if ($create->hasMessages()) {
						$ret->addMessages($create->getMessages());
					}

					$ret->addResult([self::STR_HTTP_CODE => HttpStatusCodes::INTERNAL_SERVER_ERROR]);

					return $ret;
				}

				$login           = new LoginKey($this->db, $this->log);
				$login->userId   = $user->id;
				$login->provider = new LoginKeyProviders($provider);
				$login->key      = ($login->provider->is(LoginKeyProviders::PASSWORD)) ? password_hash($key, PASSWORD_DEFAULT) : $key;
				$create          = $login->create();

				if ($create->isBad()) {
					$ret->addMessage("Failed to create user login key, removing new user account");

					if ($create->hasMessages()) {
						$ret->addMessages($create->getMessages());
					}

					$user->delete();
					$ret->addResult([self::STR_HTTP_CODE => HttpStatusCodes::INTERNAL_SERVER_ERROR]);

					return $ret;
				}

				$profile = new UserProfile($this->db, $this->log);
				$profile->userId = $user->id;

				if ($params->has(self::STR_PROFILE)) {
					$pParams              = new ParameterHelper($params->get(self::STR_PROFILE));
					$profile->birthday    = new \DateTimeImmutable($pParams->getString(self::STR_BIRTHDAY, $profile->birthday->format('Y-m-d G:i:s')), new \DateTimeZone('UTC'));
					$profile->description = $pParams->getString(self::STR_DESCRIPTION, $profile->description);
					$displayName          = $pParams->getString(self::STR_DISPLAY_NAME, '');

					if ($displayName !== $profile->displayName && UserProfile::validDisplayName($displayName)) {
						$p = UserProfile::fromDisplayName($displayName, $this->db, $this->log);

						if ($p->userId < 1) {
							$profile->displayName = $displayName;
						} else {
							$ret->addMessage("Couldn't change display name, invalid or already in use");
						}
					}

					$profile->gender   = new UserGenders($pParams->getInt(self::STR_GENDER, $profile->gender->getValue()));
					$profile->realName = $pParams->getString(self::STR_REAL_NAME, $profile->realName);
				}

				$profile->create();

				$settings = new UserSettings($this->db, $this->log);
				$settings->userId = $user->id;

				if ($params->has(self::STR_SETTINGS)) {
					$sParams              = new ParameterHelper($params->get(self::STR_SETTINGS));
					$settings->htmlEmails = $sParams->getBool(self::STR_HTML_EMAILS, $settings->htmlEmails);
					$settings->playSounds = $sParams->getBool(self::STR_PLAY_SOUNDS, $settings->playSounds);
				}

				$settings->create();

				$visibilities = new UserVisibilities($this->db, $this->log);
				$visibilities->userId = $user->id;

				if ($params->has(self::STR_VISIBILITIES)) {
					$vParams                   = new ParameterHelper($params->get(self::STR_VISIBILITIES));
					$visibilities->birthday    = new VisibilityState($vParams->getInt(self::STR_BIRTHDAY, $visibilities->birthday->getValue()));
					$visibilities->description = new VisibilityState($vParams->getInt(self::STR_DESCRIPTION, $visibilities->description->getValue()));
					$visibilities->email       = new VisibilityState($vParams->getInt(self::STR_EMAIL, $visibilities->email->getValue()));
					$visibilities->gender      = new VisibilityState($vParams->getInt(self::STR_GENDER, $visibilities->gender->getValue()));
					$visibilities->profile     = new VisibilityState($vParams->getInt(self::STR_PROFILE, $visibilities->profile->getValue()));
					$visibilities->realName    = new VisibilityState($vParams->getInt(self::STR_REAL_NAME, $visibilities->realName->getValue()));
					$visibilities->searches    = new VisibilityState($vParams->getInt(self::STR_SEARCHES, $visibilities->searches->getValue()));
				}

				$visibilities->create();

				$this->touchEvent(UserEventTypes::REGISTER, new UserEventRegisterDispatch($user, $params, $this->db, $this->log));

				$ret->makeGood();
				$ret->addResult([
					self::STR_HTTP_CODE => HttpStatusCodes::OK,
					self::STR_DATA      => $user
				]);
			} catch (\Exception $ex) {
				$this->assignError($ret, "Error while creating user account: " . $ex->getMessage());
			}

			return $ret;
		}

		/**
		 * Performs user password reset. If completed successfully, the UserEventTypes::RESETPASSWORD chain is traversed with a new
		 * UserEventResetPasswordDispatch object. The following parameters are required:
		 *
		 * [
		 *   'key'        => (string) 'someKey', # new password
		 *   'confirmKey' => (string) 'someKey', # confirmation of new password
		 *   'token'      => (string) 'token'    # token to confirm this is authorized
		 * ]
		 *
		 * This method makes NO checks against the new password's complexity.
		 *
		 * Resulting ReturnHelper will include a suggested HTTP status code in the 'httpCode' index.
		 *
		 * @param ParameterHelper $params Parameters provided to perform the event.
		 * @throws \ReflectionException|\Exception
		 * @return ReturnHelper
		 */
		public function doResetPassword(ParameterHelper $params) : ReturnHelper {
			$ret = new ReturnHelper();

			if (!$params->hasAll(self::STR_KEY, self::STR_CONFIRM_KEY, self::STR_TOKEN)) {
				$this->assignError($ret, "Missing parameters for reset");

				return $ret;
			}

			$key        = $params->getString(self::STR_KEY);
			$token      = $params->getString(self::STR_TOKEN);
			$confirmKey = $params->getString(self::STR_CONFIRM_KEY);

			if ($key !== $confirmKey) {
				$this->assignError($ret, "Invalid keys provided");

				return $ret;
			}

			$token = explode(':', base64_decode($token));

			if (count($token) !== 2) {
				$this->assignError($ret, "Invalid reset token");

				return $ret;
			}

			$user = User::fromId($token[0], $this->db, $this->log);

			if ($user->id < 1) {
				$this->assignError($ret, "Invalid account information");

				return $ret;
			}

			$tok = UserToken::fromToken($token[1], $user->id, $this->db, $this->log);

			if ($tok->id < 1) {
				$this->assignError($ret, "Invalid token for reset");

				return $ret;
			}

			if (!$this->touchPreEvent(UserEventTypes::RESETPASSWORD_PRE, new UserEventPreResetPasswordDispatch($this->db, $this->log, $key, $token[1], $params), $ret, "Pre-reset event chain stopped the event")) {
				return $ret;
			}

			$tok->delete();
			$login = LoginKey::fromUserAndProvider($user->id, LoginKeyProviders::PASSWORD, $this->db, $this->log);

			try {
				$eventDesc = '';
				$event     = new ReturnHelper();

				if ($login->userId < 1) {
					$login           = new LoginKey($this->db, $this->log);
					$login->userId   = $user->id;
					$login->provider = new LoginKeyProviders(LoginKeyProviders::PASSWORD);
					$login->key      = password_hash($key, PASSWORD_DEFAULT);
					$event           = $login->create();
					$eventDesc       = 'create';
				} else {
					$login->key = password_hash($key, PASSWORD_DEFAULT);
					$event      = $login->update();
					$eventDesc  = 'update';
				}

				if ($event->isBad()) {
					if ($event->hasMessages()) {
						foreach ($event->getMessages() as $msg) {
							$ret->addMessage($msg);
							$this->log->error($msg);
						}
					} else {
						$ret->addMessage("Failed to {$eventDesc} login key");
						$this->log->error("Failed to {$eventDesc} login key");
					}

					return $ret;
				}

				$ret->addResult([self::STR_HTTP_CODE => HttpStatusCodes::OK]);

				$this->touchEvent(UserEventTypes::RESETPASSWORD, new UserEventResetPasswordDispatch($user, $this->db, $this->log));

				$ret->makeGood();
			} catch (\Exception $ex) {
				$this->assignError($ret, "An exception occurred: " . $ex->getMessage());
			}

			return $ret;
		}

		/**
		 * Performs user update. If completed successfully, the UserEventTypes::UPDATE chain is traversed with a new
		 * UserEventUpdateDispatch object. The following parameters are required:
		 *
		 * [
		 *   'id' => (int) 1 # user identifier
		 * ]
		 *
		 * Additionally, any of the following can be supplied alongside the user identifier:
		 *
		 *   == User Info ==
		 *   'actor'          => (int) 2                    # user who is acting upon another user (check for admin)
		 *   'email'          => (string) 'user@domain.com' # new email address
		 *   'confirmEmail'   => (string) 'user@domain.com' # confirm new email address
		 *   'emailConfirmed' => (bool) true                # optionally used to pre-confirm a new email address
		 *   'key'            => (string) 'someKey'         # new password key
		 *   'oldKey'         => (string) 'oldKey'          # old password key for confirmation
		 *   'confirmKey'     => (string) 'someKey'         # confirm new password key
		 *
		 *   == User Profile ==
		 *   'profile'        => [
		 *     'birthday'     => (string) '1900-01-01',     # birthday value for user
		 *     'description'  => (string) 'My info',        # short description/about section for user
		 *     'displayName'  => (string) 'SomeName',       # display name for user
		 *     'gender'       => (int) 1,                   # user's preferred gender
		 *     'realName'     => (string) 'John Doe'        # user's real name
		 *   ]
		 *
		 *   == User Settings ==
		 *   'settings'       => [
		 *     'htmlEmails'   => (bool) true,               # sets the user's html email preference
		 *     'playSounds'   => (bool) true                # sets the user's sound preference
		 *   ]
		 *
		 *   == User Visibilities ==
		 *   'visibilities'   => [
		 *     'birthday'     => (int) 1,                   # visibility for user's birthday
		 *     'description'  => (int) 1,                   # visibility for user's description
		 *     'email'        => (int) 1,                   # visibility for user's email address
		 *     'gender'       => (int) 1,                   # visibility for user's gender
		 *     'profile'      => (int) 1,                   # visibility for user's profile
		 *     'realName'     => (int) 1,                   # visibility for user's real name
		 *     'searches'     => (int) 1                    # visibility for user in searches
		 *   ]
		 *
		 * Other parameters are passed along in case they are consumed by the chain nodes, but processing is the responsibility of
		 * linked nodes. If the 'key' and 'confirmKey' parameters are included, they will be used to change the user's password but do
		 * NOT make any checks against the password's complexity.
		 *
		 * Resulting ReturnHelper will include a suggested HTTP status code in the 'httpCode' index and the user object if the
		 * operation was successful:
		 *
		 * [
		 *   'httpCode' => 0,     # suggested HTTP status code
		 *   'data'     => User{} # User object with updated user data
		 * ]
		 *
		 * @param ParameterHelper $params Parameters provided to perform the event.
		 * @throws \ReflectionException|\Exception
		 * @return ReturnHelper
		 */
		public function doUpdate(ParameterHelper $params) : ReturnHelper {
			$ret = new ReturnHelper();

			if (!$params->has(self::STR_ID)) {
				$this->assignError($ret, "Missing parameters for update");

				return $ret;
			}

			$user = User::fromId($params->getInt(self::STR_ID), $this->db, $this->log);

			if ($user->id < 1) {
				$this->assignError($ret, "Invalid user for update");

				return $ret;
			}

			if (!$this->touchPreEvent(UserEventTypes::UPDATE_PRE, new UserEventPreUpdateDispatch($this->db, $this->log, $user->id, $params), $ret, "Pre-update event chain stopped the event")) {
				return $ret;
			}

			$dispatch = new UserEventUpdateDispatch($user, $params, $this->db, $this->log);

			if ($params->hasAll(self::STR_EMAIL, self::STR_CONFIRM_EMAIL)) {
				$email        = $params->getString(self::STR_EMAIL);
				$confirmEmail = $params->getString(self::STR_CONFIRM_EMAIL);

				if ($email !== $user->email && !empty($email) && $email === $confirmEmail) {
					$user->email          = $email;
					$user->emailConfirmed = $params->getBool(self::STR_EMAILCONFIRMED, false);

					$dispatch->emailUpdated = true;
					$ret->addMessage("Email address was changed");
				}
			}

			$update = $user->update();

			if ($update->isBad()) {
				$this->assignError($ret, $update->getMessages()[0]);

				return $ret;
			}

			if ($params->hasAll(self::STR_KEY, self::STR_OLD_KEY, self::STR_CONFIRM_KEY)) {
				$key        = $params->getString(self::STR_KEY);
				$confirmKey = $params->getString(self::STR_CONFIRM_KEY);
				$login      = LoginKey::fromUserAndProvider($user->id, LoginKeyProviders::PASSWORD, $this->db, $this->log);

				if (password_verify($params->getString(self::STR_OLD_KEY), $login->key)) {
					if ($login->userId == $user->id && !empty($key) && $key === $confirmKey) {
						$login->key = password_hash($key, PASSWORD_DEFAULT);

						$update = $login->update();

						if ($update->isBad()) {
							$this->assignError($ret, $update->getMessages()[0]);

							return $ret;
						}

						$ret->addMessage("Password was changed");
					}
				}
			}

			if ($params->hasAll(self::STR_KEY, self::STR_ACTOR) && (new UserRoles($this->db, $this->log))->userInRoleByName($params->getInt(self::STR_ACTOR), RoleStrings::ADMINISTRATOR)) {
				$key        = $params->getString(self::STR_KEY);
				$login      = LoginKey::fromUserAndProvider($user->id, LoginKeyProviders::PASSWORD, $this->db, $this->log);

				if ($login->userId == $user->id && !empty($key)) {
					$login->key = password_hash($key, PASSWORD_DEFAULT);

					$update = $login->update();

					if ($update->isBad()) {
						$this->assignError($ret, $update->getMessages()[0]);

						return $ret;
					}

					$ret->addMessage("Password was changed");
				}
			}

			if ($params->has(self::STR_PROFILE)) {
				$pParams = new ParameterHelper($params->get(self::STR_PROFILE));
				$profile = UserProfile::fromUser($user->id, $this->db, $this->log);

				if ($profile->userId == $user->id) {
					$profile->birthday    = new \DateTimeImmutable($pParams->getString(self::STR_BIRTHDAY, $profile->birthday->format('Y-m-d G:i:s')), new \DateTimeZone('UTC'));
					$profile->description = $pParams->getString(self::STR_DESCRIPTION, $profile->description);

					$displayName = $pParams->getString(self::STR_DISPLAY_NAME, '');

					if (!empty($dispalyName) && $displayName !== $profile->displayName && UserProfile::validDisplayName($displayName)) {
						$p = UserProfile::fromDisplayName($displayName, $this->db, $this->log);

						if ($p->userId < 1) {
							$profile->displayName = $displayName;
						} else {
							$ret->addMessage("Couldn't change display name, invalid or already in use");
						}
					}

					$profile->gender   = new UserGenders($pParams->getInt(self::STR_GENDER, $profile->gender->getValue()));
					$profile->realName = $pParams->getString(self::STR_REAL_NAME, $profile->realName);

					$update = $profile->update();

					if ($update->isBad()) {
						$this->assignError($ret, $update->getMessages()[0]);

						return $ret;
					}

					$ret->addMessage("Profile was updated");
				}
			}

			if ($params->has(self::STR_SETTINGS)) {
				$sParams  = new ParameterHelper($params->get(self::STR_SETTINGS));
				$settings = UserSettings::fromUser($user->id, $this->db, $this->log);

				if ($settings->userId == $user->id) {
					$settings->htmlEmails = $sParams->getBool(self::STR_HTML_EMAILS, $settings->htmlEmails);
					$settings->playSounds = $sParams->getBool(self::STR_PLAY_SOUNDS, $settings->playSounds);

					$update = $settings->update();

					if ($update->isBad()) {
						$this->assignError($ret, $update->getMessages()[0]);

						return $ret;
					}

					$ret->addMessage("Settings were updated");
				}
			}

			if ($params->has(self::STR_VISIBILITIES)) {
				$vis     = UserVisibilities::fromUser($user->id, $this->db, $this->log);
				$vParams = new ParameterHelper($params->get(self::STR_VISIBILITIES));

				if ($vis->userId == $user->id) {
					$vis->birthday    = new VisibilityState($vParams->get(self::STR_BIRTHDAY, $vis->birthday->getValue()));
					$vis->description = new VisibilityState($vParams->get(self::STR_DESCRIPTION, $vis->description->getValue()));
					$vis->email       = new VisibilityState($vParams->get(self::STR_EMAIL, $vis->email->getValue()));
					$vis->gender      = new VisibilityState($vParams->get(self::STR_GENDER, $vis->gender->getValue()));
					$vis->profile     = new VisibilityState($vParams->get(self::STR_PROFILE, $vis->profile->getValue()));
					$vis->realName    = new VisibilityState($vParams->get(self::STR_REAL_NAME, $vis->realName->getValue()));
					$vis->searches    = new VisibilityState($vParams->get(self::STR_SEARCHES, $vis->searches->getValue()));

					$update = $vis->update();

					if ($update->isBad()) {
						$this->assignError($ret, $update->getMessages()[0]);

						return $ret;
					}

					$ret->addMessage("Visibility was updated");
				}
			}

			$this->touchEvent(UserEventTypes::UPDATE, $dispatch);

			$ret->makeGood();
			$ret->addResult([self::STR_HTTP_CODE => HttpStatusCodes::OK]);

			return $ret;
		}

		/**
		 * Links a processing node to the provided event. If an invalid event type is supplied, nothing will be linked.
		 *
		 * @param UserEventTypes|int $event UserEventTypes object or value to link provided node with in object.
		 * @param NodeBase $node Valid processing node to notify of event.
		 * @throws \ReflectionException
		 * @return void
		 */
		public function linkToEvent(UserEventTypes|int $event, NodeBase $node) : void {
			$e = UserEventTypes::tryGet($event);

			if ($e->getValue() === null) {
				return;
			}

			$this->events[$e->getValue()]->linkNode($node);

			return;
		}

		/**
		 * Touches an event, traversing the related chain so all linked nodes receive notification the event was executed.
		 * If an invalid event type is supplied, nothing will be traversed.
		 *
		 * @param UserEventTypes|int $event UserEventTypes object or value to route dispatch to correct chain.
		 * @param DispatchBase $dispatch Dispatch with relevant information for the selected chain.
		 * @throws \ReflectionException
		 * @return void
		 */
		protected function touchEvent(UserEventTypes|int $event, DispatchBase $dispatch) : void {
			$e = UserEventTypes::tryGet($event);

			if ($e->getValue() === null) {
				return;
			}

			$this->events[$e->getValue()]->traverse($dispatch, $this);

			return;
		}

		/**
		 * Touches a 'pre' event, traversing the related chain so all linked nodes receive a chance to consume the event.
		 * If an invalid event type is supplied, nothing will be traversed.  If the chain consumes the event, the
		 * ReturnHelper object will be updated with the results and an error message will be added.
		 *
		 * @param UserEventTypes|int $event UserEventTypes object or value to route dispatch to correct chain.
		 * @param DispatchBase $dispatch Dispatch with relevant information for the selected chain.
		 * @param ReturnHelper $ret ReturnHelper object to store any messages or errors.
		 * @param string $errorMessage Error message to assign if the chain consumes the event.
		 * @throws \ReflectionException
		 * @return bool
		 */
		protected function touchPreEvent(UserEventTypes|int $event, DispatchBase $dispatch, ReturnHelper &$ret, string $errorMessage) : bool {
			$e = UserEventTypes::tryGet($event);

			if ($e->getValue() === null) {
				$this->assignError($ret, "Invalid event type");

				return false;
			}

			$this->events[$e->getValue()]->traverse($dispatch, $this);

			if ($dispatch->isConsumed()) {
				$this->assignError($ret, $errorMessage);
				$ret->addResults($dispatch->getResults());

				return false;
			}

			return true;
		}
	}
