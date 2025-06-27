<?php

	namespace Zibings;

	use Stoic\Chain\DispatchBase;
	use Stoic\Log\Logger;
	use Stoic\Pdo\PdoHelper;
	use Stoic\Utilities\ParameterHelper;

	/**
	 * Dispatch used for pre-confirmation event.
	 *
	 * @package Zibings
	 */
	class UserEventPreConfirmDispatch extends DispatchBase {
		/**
		 * Instantiates a new UserEventPreConfirmDispatch object.
		 *
		 * @param PdoHelper $db PdoHelper object for use.
		 * @param Logger $log Logger object for use.
		 * @param string $token Token to be used for confirmation.
		 * @param ParameterHelper $fullParams Full parameters for reference.
		 * @throws \Exception
		 */
		public function __construct(
			public PdoHelper       $db,
			public Logger          $log,
			public string          $token,
			public ParameterHelper $fullParams
		) {
			$this->makeValid();
			$this->makeConsumable();

			return;
		}

		/**
		 * Basic initialization method, unused by UserEvents dispatches.
		 *
		 * @param mixed $input Input used for initialization.
		 * @return void
		 */
		public function initialize(mixed $input) : void {
			return;
		}
	}

	/**
	 * Dispatch used for confirm event.
	 *
	 * @package Zibings
	 */
	class UserEventConfirmDispatch extends DispatchBase {
		/**
		 * Instantiates a new UserEventConfirmDispatch object.
		 *
		 * @param User $user User object for reference.
		 * @param PdoHelper $db PdoHelper object for reference.
		 * @param Logger $log Logger object for reference.
		 * @throws \Exception
		 */
		public function __construct(
			public User      $user,
			public PdoHelper $db,
			public Logger    $log
		) {
			$this->makeValid();

			return;
		}

		/**
		 * Basic initialization method, unused by UserEvents dispatches.
		 *
		 * @param mixed $input Input used for initialization.
		 * @return void
		 */
		public function initialize(mixed $input) : void {
			return;
		}
	}

	/**
	 * Dispatch used for pre-creation event.
	 *
	 * @package Zibings
	 */
	class UserEventPreCreateDispatch extends DispatchBase {
		/**
		 * Instantiates a new UserEventPreCreateDispatch object.
		 *
		 * @param PdoHelper $db  PdoHelper object for use.
		 * @param Logger $log Logger object for use.
		 * @param string $email Email address for new user.
		 * @param string $key Login key for new user.
		 * @param int|LoginKeyProviders $provider Login key provider for new user.
		 * @param bool $emailConfirmed Whether the new user's email is confirmed.
		 * @param ParameterHelper $fullParams Full parameters for reference.
		 * @throws \Exception
		 */
		public function __construct(
			public PdoHelper             $db,
			public Logger                $log,
			public string                $email,
			public string                $key,
			public int|LoginKeyProviders $provider,
			public bool                  $emailConfirmed,
			public ParameterHelper       $fullParams
		) {
			$this->makeValid();
			$this->makeConsumable();

			return;
		}

		/**
		 * Basic initialization method, unused by UserEvents dispatches.
		 *
		 * @param mixed $input Input used for initialization.
		 * @return void
		 */
		public function initialize(mixed $input) : void {
			return;
		}
	}

	/**
	 * Dispatch used for creation event.
	 *
	 * @package Zibings
	 */
	class UserEventCreateDispatch extends DispatchBase {
		/**
		 * Instantiates a new UserEventCreateDispatch object.
		 *
		 * @param User $user User object for reference.
		 * @param PdoHelper $db PdoHelper object for reference.
		 * @param Logger $log Logger object for reference.
		 * @throws \Exception
		 */
		public function __construct(
			public User            $user,
			public ParameterHelper $params,
			public PdoHelper       $db,
			public Logger          $log
		) {
			$this->makeValid();

			return;
		}

		/**
		 * Basic initialization method, unused by UserEvents dispatches.
		 *
		 * @param mixed $input Input used for initialization.
		 * @return void
		 */
		public function initialize(mixed $input) : void {
			return;
		}
	}

	/**
	 * Dispatch used for pre-deletion event.
	 *
	 * @package Zibings
	 */
	class UserEventPreDeleteDispatch extends DispatchBase {
		/**
		 * Instantiates a new UserEventPreDeleteDispatch object.
		 *
		 * @param PdoHelper $db  PdoHelper object for use.
		 * @param Logger $log Logger object for use.
		 * @param int $id Identifier for user being deleted.
		 * @param int $actor Identifier of the user performing the deletion.
		 * @param ParameterHelper $fullParams Full parameters for reference.
		 * @throws \Exception
		 */
		public function __construct(
			public PdoHelper       $db,
			public Logger          $log,
			public int             $id,
			public int             $actor,
			public ParameterHelper $fullParams
		) {
			$this->makeValid();
			$this->makeConsumable();

			return;
		}

		/**
		 * Basic initialization method, unused by UserEvents dispatches.
		 *
		 * @param mixed $input Input used for initialization.
		 * @return void
		 */
		public function initialize(mixed $input) : void {
			return;
		}
	}

	/**
	 * Dispatch used for deletion event.
	 *
	 * @package Zibings
	 */
	class UserEventDeleteDispatch extends DispatchBase {
		/**
		 * Instantiates a new UserEventDeleteDispatch object.
		 *
		 * @param User $user User object for reference.
		 * @param PdoHelper $db PdoHelper object for reference.
		 * @param Logger $log Logger object for reference.
		 * @throws \Exception
		 */
		public function __construct(
			public User      $user,
			public PdoHelper $db,
			public Logger    $log
		) {
			$this->makeValid();

			return;
		}

		/**
		 * Basic initialization method, unused by UserEvents dispatches.
		 *
		 * @param mixed $input Input used for initialization.
		 * @return void
		 */
		public function initialize(mixed $input) : void {
			return;
		}
	}

	/**
	 * Dispatch used for pre-login event.
	 *
	 * @package Zibings
	 */
	class UserEventPreLoginDispatch extends DispatchBase {
		/**
		 * Instantiates a new UserEventPreLoginDispatch object.
		 *
		 * @param PdoHelper $db  PdoHelper object for use.
		 * @param Logger $log Logger object for use.
		 * @param string $email Email address for login.
		 * @param string $key Login key for login.
		 * @param int|LoginKeyProviders $provider Login key provider for login.
		 * @param string|array $roles Roles for login.
		 * @param ParameterHelper $fullParams Full parameters for reference.
		 * @throws \Exception
		 */
		public function __construct(
			public PdoHelper             $db,
			public Logger                $log,
			public string                $email,
			public string                $key,
			public int|LoginKeyProviders $provider,
			public string|array          $roles,
			public ParameterHelper       $fullParams
		) {
			$this->makeValid();
			$this->makeConsumable();

			return;
		}

		/**
		 * Basic initialization method, unused by UserEvents dispatches.
		 *
		 * @param mixed $input Input used for initialization.
		 * @return void
		 */
		public function initialize(mixed $input) : void {
			return;
		}
	}

	/**
	 * Dispatch used for login event.
	 *
	 * @package Zibings
	 */
	class UserEventLoginDispatch extends DispatchBase {
		/**
		 * Instantiates a new UserEventLoginDispatch object.
		 *
		 * @param User $user User object for reference.
		 * @param string $token Generated session token for user.
		 * @param PdoHelper $db PdoHelper object for reference.
		 * @param Logger $log Logger object for reference.
		 * @throws \Exception
		 */
		public function __construct(
			public User      $user,
			public string    $token,
			public PdoHelper $db,
			public Logger    $log
		) {
			$this->makeValid();

			return;
		}

		/**
		 * Basic initialization method, unused by UserEvents dispatches.
		 *
		 * @param mixed $input Input used for initialization.
		 * @return void
		 */
		public function initialize(mixed $input) : void {
			return;
		}
	}

	/**
	 * Dispatch used for pre-logout event.
	 *
	 * @package Zibings
	 */
	class UserEventPreLogoutDispatch extends DispatchBase {
		/**
		 * Instantiates a new UserEventPreLogoutDispatch object.
		 *
		 * @param PdoHelper $db  PdoHelper object for use.
		 * @param Logger $log Logger object for use.
		 * @param int $userId Identifier for user being logged out.
		 * @param string $token Token for user being logged out.
		 * @param ParameterHelper $fullParams Full parameters for reference.
		 * @throws \Exception
		 */
		public function __construct(
			public PdoHelper       $db,
			public Logger          $log,
			public int             $userId,
			public string          $token,
			public ParameterHelper $fullParams
		) {
			$this->makeValid();
			$this->makeConsumable();

			return;
		}

		/**
		 * Basic initialization method, unused by UserEvents dispatches.
		 *
		 * @param mixed $input Input used for initialization.
		 * @return void
		 */
		public function initialize(mixed $input) : void {
			return;
		}
	}

	/**
	 * Dispatch used for logout event.
	 *
	 * @package Zibings
	 */
	class UserEventLogoutDispatch extends DispatchBase {
		/**
		 * Instantiates a new UserEventLogoutDispatch object.
		 *
		 * @param UserSession $session UserSession object for reference.
		 * @param PdoHelper $db PdoHelper object for reference.
		 * @param Logger $log Logger object for reference.
		 * @throws \Exception
		 */
		public function __construct(
			public UserSession $session,
			public PdoHelper   $db,
			public Logger      $log
		) {
			$this->makeValid();

			return;
		}

		/**
		 * Basic initialization method, unused by UserEvents dispatches.
		 *
		 * @param mixed $input Input used for initialization.
		 * @return void
		 */
		public function initialize(mixed $input) : void {
			return;
		}
	}

	/**
	 * Dispatch used for pre-send reset event.
	 *
	 * @package Zibings
	 */
	class UserEventPreSendResetDispatch extends DispatchBase {
		/**
		 * Instantiates a new UserEventPreSendResetDispatch object.
		 *
		 * @param PdoHelper $db
		 * @param Logger $log
		 * @param string $email
		 * @param string $pageRoot
		 * @param ParameterHelper $fullParams
		 * @throws \Exception
		 */
		public function __construct(
			public PdoHelper       $db,
			public Logger          $log,
			public string          $email,
			public string          $pageRoot,
			public ParameterHelper $fullParams
		) {
			$this->makeValid();
			$this->makeConsumable();

			return;
		}

		/**
		 * Basic initialization method, unused by UserEvents dispatches.
		 *
		 * @param mixed $input Input used for initialization.
		 * @return void
		 */
		public function initialize(mixed $input) : void {
			return;
		}
	}

	/**
	 * Dispatch used for send reset event.
	 *
	 * @package Zibings
	 */
	class UserEventSendResetDispatch extends DispatchBase {
		/**
		 * Instantiates a new UserEventSendResetDispatch object.
		 *
		 * @param string $email Email address for user requesting reset.
		 * @param PdoHelper $db PdoHelper object for reference.
		 * @param Logger $log Logger object for reference.
		 * @throws \Exception
		 */
		public function __construct(
			public string    $email,
			public PdoHelper $db,
			public Logger    $log
		) {
			$this->makeValid();

			return;
		}

		/**
		 * Basic initialization method, unused by UserEvents dispatches.
		 *
		 * @param mixed $input Input used for initialization.
		 * @return void
		 */
		public function initialize(mixed $input) : void {
			return;
		}
	}

	/**
	 * Dispatch used for pre-registration event.
	 *
	 * @package Zibings
	 */
	class UserEventPreRegisterDispatch extends DispatchBase {
		/**
		 * Instantiates a new UserEventPreRegisterDispatch object.
		 *
		 * @param PdoHelper $db  PdoHelper object for use.
		 * @param Logger $log Logger object for use.
		 * @param string $email Email address for new user.
		 * @param string $key Login key for new user.
		 * @param int|LoginKeyProviders $provider Login key provider for new user.
		 * @param ParameterHelper $fullParams Full parameters for reference.
		 * @throws \Exception
		 */
		public function __construct(
			public PdoHelper             $db,
			public Logger                $log,
			public string                $email,
			public string                $key,
			public int|LoginKeyProviders $provider,
			public ParameterHelper       $fullParams
		) {
			$this->makeValid();
			$this->makeConsumable();

			return;
		}

		/**
		 * Basic initialization method, unused by UserEvents dispatches.
		 *
		 * @param mixed $input Input used for initialization.
		 * @return void
		 */
		public function initialize(mixed $input) : void {
			return;
		}
	}

	/**
	 * Dispatch used for registration event.
	 *
	 * @package Zibings
	 */
	class UserEventRegisterDispatch extends DispatchBase {
		/**
		 * Instantiates a new UserEventRegisterDispatch object.
		 *
		 * @param User $user User object for reference.
		 * @param PdoHelper $db PdoHelper object for reference.
		 * @param Logger $log Logger object for reference.
		 * @throws \Exception
		 */
		public function __construct(
			public User            $user,
			public ParameterHelper $params,
			public PdoHelper       $db,
			public Logger          $log
		) {
			$this->makeValid();

			return;
		}

		/**
		 * Basic initialization method, unused by UserEvents dispatches.
		 *
		 * @param mixed $input Input used for initialization.
		 * @return void
		 */
		public function initialize(mixed $input) : void {
			return;
		}
	}

	/**
	 * Dispatch used for pre-password reset event.
	 *
	 * @package Zibings
	 */
	class UserEventPreResetPasswordDispatch extends DispatchBase {
		/**
		 * Instantiates a new UserEventPreResetPasswordDispatch object.
		 *
		 * @param PdoHelper $db  PdoHelper object for use.
		 * @param Logger $log Logger object for use.
		 * @param string $key Login key for user requesting password reset.
		 * @param string $token Token for user requesting password reset.
		 * @param ParameterHelper $fullParams Full parameters for reference.
		 * @throws \Exception
		 */
		public function __construct(
			public PdoHelper       $db,
			public Logger          $log,
			public string          $key,
			public string          $token,
			public ParameterHelper $fullParams
		) {
			$this->makeValid();
			$this->makeConsumable();

			return;
		}

		/**
		 * Basic initialization method, unused by UserEvents dispatches.
		 *
		 * @param mixed $input Input used for initialization.
		 * @return void
		 */
		public function initialize(mixed $input) : void {
			return;
		}
	}

	/**
	 * Dispatch used for password reset event.
	 *
	 * @package Zibings
	 */
	class UserEventResetPasswordDispatch extends DispatchBase {
		/**
		 * Instantiates a new UserEventResetPasswordDispatch object.
		 *
		 * @param User $user User object for reference.
		 * @param PdoHelper $db PdoHelper object for reference.
		 * @param Logger $log Logger object for reference.
		 * @throws \Exception
		 */
		public function __construct(
			public User      $user,
			public PdoHelper $db,
			public Logger    $log
		) {
			$this->makeValid();

			return;
		}

		/**
		 * Basic initialization method, unused by UserEvents dispatches.
		 *
		 * @param mixed $input Input used for initialization.
		 * @return void
		 */
		public function initialize(mixed $input) : void {
			return;
		}
	}

	/**
	 * Dispatch used for pre-update event.
	 *
	 * @package Zibings
	 */
	class UserEventPreUpdateDispatch extends DispatchBase {
		/**
		 * Instantiates a new UserEventPreUpdateDispatch object.
		 *
		 * @param PdoHelper $db PdoHelper object for use.
		 * @param Logger $log Logger object for use.
		 * @param int $id Identifier for user being updated.
		 * @param ParameterHelper $fullParams Full parameters for reference.
		 * @throws \Exception
		 */
		public function __construct(
			public PdoHelper       $db,
			public Logger          $log,
			public int             $id,
			public ParameterHelper $fullParams
		) {
			$this->makeValid();
			$this->makeConsumable();

			return;
		}

		/**
		 * Basic initialization method, unused by UserEvents dispatches.
		 *
		 * @param mixed $input Input used for initialization.
		 * @return void
		 */
		public function initialize(mixed $input) : void {
			return;
		}
	}

	/**
	 * Dispatch used for update event.
	 *
	 * @package Zibings
	 */
	class UserEventUpdateDispatch extends DispatchBase {
		/**
		 * Instantiates a new UserEventUpdateDispatch object.
		 *
		 * @param User $user User object for reference.
		 * @param ParameterHelper $params Parameters provided for user update.
		 * @param PdoHelper $db PdoHelper object for reference.
		 * @param Logger $log Logger object for reference.
		 * @param bool $emailUpdated Optional toggle to show if the user's email was updated.
		 * @throws \Exception
		 */
		public function __construct(
			public User            $user,
			public ParameterHelper $params,
			public PdoHelper       $db,
			public Logger          $log,
			public bool            $emailUpdated = false) {
			$this->makeValid();

			return;
		}

		/**
		 * Basic initialization method, unused by UserEvents dispatches.
		 *
		 * @param mixed $input Input used for initialization.
		 * @return void
		 */
		public function initialize(mixed $input) : void {
			return;
		}
	}