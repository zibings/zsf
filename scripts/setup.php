<?php

	namespace Zibings;

	class ZsfSetup {
		public static function postCreateProject() {
			echo("Hello, Zsf!" . PHP_EOL);
			echo(getcwd() . PHP_EOL);

			// check here if the vendor directory has its dependencies already installed
			if (!file_exists(getcwd() . '/vendor/autoload.php')) {
				echo("Dependencies not installed. Run 'composer install' to install them." . PHP_EOL);
			} else {
				echo("Dependencies already installed." . PHP_EOL);
			}

			return;
		}
	}
