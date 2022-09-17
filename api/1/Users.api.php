<?php

	namespace Api1;

	use Stoic\Log\Logger;
	use Stoic\Web\Api\Stoic;
	use Stoic\Web\Api\Response;
	use Stoic\Web\Request;

	use Zibings\ApiController;
	use Zibings\RoleStrings;
	use Zibings\Users as ZUsers;

	/**
	 * API controller that deals with user management endpoints.
	 *
	 * @package Zibings\Api1
	 */
	class Users extends ApiController {
		/**
		 * Instantiates a new Account object.
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
		 * @param Request $request The current request which routed to the endpoint.
		 * @param array|null $matches Array of matches returned by endpoint regex pattern.
		 * @return Response
		 */
		public function getUsers(Request $request, array $matches = null) : Response {
			$ret = $this->newResponse();

			if (str_ends_with($matches[0][0], '/Columns')) {
				$dat  = [];
				$cols = $this->users->getColumnsForGetAll();

				foreach ($cols as $col) {
					$dat[] = [
						'field'  => $col,
						'header' => ucwords($col),
						'filter' => $col !== 'id'
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
			$this->registerEndpoint('GET',  '/^Users\/GetAll\/Columns\/?/i', 'getUsers', RoleStrings::ADMINISTRATOR);
			$this->registerEndpoint('GET',  '/^Users\/GetAll\/?/i',          'getUsers', RoleStrings::ADMINISTRATOR);

			return;
		}
	}
