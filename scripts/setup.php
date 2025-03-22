<?php

	namespace Zibings;

	function output($message) {
		file_put_contents('php://stderr', $message . PHP_EOL);

		return;
	}

	while (ob_get_level()) {
		ob_end_clean();
	}

	output("Hello, Zsf!");
	output(getcwd());

	// check here if the vendor directory has its dependencies already installed
	if (!file_exists(getcwd() . '/vendor/autoload.php')) {
		output("Dependencies not installed. Run 'composer install' to install them.");
	} else {
		output("Dependencies already installed.");
	}
