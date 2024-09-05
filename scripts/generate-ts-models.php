<?php

	const STOIC_CORE_PATH        = './';
	const STOIC_DISABLE_DATABASE = true;

	require_once(STOIC_CORE_PATH . 'inc/core.php');

	use Pseudo\Pdo;

	use Stoic\Pdo\BaseDbTypes;
	use Stoic\Pdo\PdoHelper;
	use Stoic\Utilities\ConsoleHelper;
	use Stoic\Utilities\LogFileAppender;
	use Stoic\Utilities\CliScriptHelper;

	global $Stoic;

	/**
	 * @var Stoic\Web\Stoic $Stoic
	 */

	$db  = new PdoHelper('', null, null, null, new Pdo());
	$log = $Stoic->getLog();
	$fh  = $Stoic->getFileHelper();

	$ch     = new ConsoleHelper($argv);
	$script = new CliScriptHelper(
		'TypeScript Model Generator',
		"Script to generate basic interfaces/classes in TypeScript from the PHP classes which implement the BaseDbModel and place them in all accessible UI folders."
	);

	$script->startScript($ch);

	if ($ch->hasShortLongArg('h', 'help', true) !== false) {
		$script->showOptionHelp($ch);

		exit;
	}

	$finalTs = '';

	$typeLookup = [
		BaseDbTypes::BOOLEAN  => 'boolean',
		BaseDbTypes::DATETIME => 'Date',
		BaseDbTypes::INTEGER  => 'number',
		BaseDbTypes::STRING   => 'string'
	];

	$defaultLookup = [
		BaseDbTypes::BOOLEAN  => 'false',
		BaseDbTypes::DATETIME => 'new Date()',
		BaseDbTypes::INTEGER  => '0',
		BaseDbTypes::STRING   => "''"
	];

	foreach (get_declared_classes() as $class) {
		if (!is_subclass_of($class, Stoic\Pdo\StoicDbModel::class)) {
			continue;
		}

		$ch->put("Found StoicDbModel '{$class}', generating TS components.. ");

		$ctorInit      = '';
		$cls           = new $class($db, $log);
		$fields        = $cls->getDbColumns();
		$className     = "{$cls->getShortClassName()}Base";
		$interfaceName = "{$cls->getShortClassName()}Model";
		$classStr      = "export class {$className} {";
		$interfaceStr  = "export interface {$interfaceName} {";

		foreach ($fields as $property => $field) {
			$interfaceStr  .= "\n\t{$property}: {$typeLookup[$field->type->getValue()]}";
			$ctorInit      .= "\t\tthis.{$property} = model?.{$property} ?? {$defaultLookup[$field->type->getValue()]};\n";
			$classStr      .= "\n\tpublic {$property}: {$typeLookup[$field->type->getValue()]};";
		}

		$interfaceStr .= "\n}";
		$finalTs      .= <<< END_OF_TS
{$interfaceStr}

{$classStr}

	constructor(model?: {$interfaceName}) {
{$ctorInit}
		return;
	}
}


END_OF_TS;

		$ch->putLine("DONE");
	}

	$ch->putLine();

	// loop over folders in UI subdirectory and output to each folder
	foreach ($fh->getFolderFolders('~/ui') as $folder) {
		$ch->put("Writing Stoic models to '{$folder}' UI folder.. ");

		if (!$fh->folderExists("{$folder}/src/types")) {
			$fh->makeFolder("{$folder}/src/types", 0777, true);
		}

		$fh->putContents("{$folder}/src/types/stoic-models.ts", $finalTs);

		$ch->putLine("DONE");
	}

	$ch->putLine();
	$ch->putLine("Finished generating TypeScript models.");
