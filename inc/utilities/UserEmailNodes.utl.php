<?php

	namespace Zibings;

	use AndyM84\Config\ConfigContainer;

	use Stoic\Chain\DispatchBase;
	use Stoic\Chain\NodeBase;
	use Stoic\Log\Logger;
	use Stoic\Pdo\PdoHelper;
	use Stoic\Web\PageHelper;

	/**
	 * Processing node to handle sending email confirmations to users.
	 *
	 * @package Zibings
	 */
	class UserEmailConfirmationNode extends NodeBase {
		/**
		 * Instantiates a new UserEmailConfirmationNode object.
		 *
		 * @param PageHelper $page
		 * @param ConfigContainer $settings
		 * @param PdoHelper $db
		 * @param null|Logger $log
		 */
		public function __construct(
			protected PageHelper $page,
			protected ConfigContainer $settings,
			protected PdoHelper $db,
			protected null|Logger $log = null
		) {
			$this->setKey('UEConfirm');
			$this->setVersion('1.0.0');

			return;
		}

		/**
		 * Handles processing of a provided dispatch.
		 *
		 * @param mixed $sender
		 * @param DispatchBase $dispatch
		 * @throws \PHPMailer\PHPMailer\Exception
		 * @return void
		 */
		public function process(mixed $sender, DispatchBase &$dispatch) : void {
			if (!($dispatch instanceof UserEventCreateDispatch) &&
				!($dispatch instanceof UserEventRegisterDispatch) &&
				!($dispatch instanceof UserEventUpdateDispatch)) {
				return;
			}

			if ($dispatch->user->emailConfirmed) {
				return;
			}

			if (!sendConfirmEmail($dispatch->user->email, $this->page, $this->settings, $this->db, $this->log)) {
				$this->log->error("Failed to send confirmation email to user '{$dispatch->user->email}'");

				return;
			}

			$this->log->info("Sent confirmation email to user '{$dispatch->user->email}'");

			return;
		}
	}
