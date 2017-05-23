<?php namespace Lilil\Generators\Commands;

use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
//use Lilil\Generators\Parsers\ModelFieldsParser;

class ModelGeneratorCommand extends GeneratorCommand {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'generate:model';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Generate a model';


	/**
	 * @param Generator $generator
	 * @param MigrationNameParser $migrationNameParser
	 * @param MigrationFieldsParser $migrationFieldsParser
	 * @param SchemaCreator $schemaCreator
	 */
/*	public function __construct(
	    //Generator $generator,
	    MigrationNameParser $migrationNameParser,
	    MigrationFieldsParser $migrationFieldsParser
	    //SchemaCreator $schemaCreator
	)
	{
	    //$this->generator = $generator;
	    $this->migrationNameParser = $migrationNameParser;
	    $this->migrationFieldsParser = $migrationFieldsParser;
	    //$this->schemaCreator = $schemaCreator;

	    parent::__construct($generator);
	}
*/


	/**
	 * The path where the file will be created
	 *
	 * @return mixed
	 */
	protected function getFileGenerationPath()
	{
		$path = $this->getPathByOptionOrConfig('path', 'model_target_path');

		return $path. '/' . ucwords($this->argument('modelName')) . '.php';
	}

	/**
	 * Fetch the template data
	 *
	 * @return array
	 */
	protected function getTemplateData()
	{

		$fields = $this->option('fields');

		$fields = preg_split('/\s?,\s/', $fields);
		$parsed = [];
		foreach($fields as $index => $field)
		{
			// Example:
			// name:string:nullable => ['name', 'string', 'nullable']
			// name:string(15):nullable
			$chunks = preg_split('/\s?:\s?/', $field, null);

			// The first item will be our property
			$property = "'" . array_shift($chunks). "'\n\t\t\t\t\t\t\t";
			echo $property . "\n";
			array_push ( $parsed ,  $property );
			// $parsed[$index] = $property;
		}

		$fillable = implode(",", $parsed);

		return [
			'NAME' => ucwords($this->argument('modelName')),
			'FILLABLE'    =>  $fillable,
			'TABLE'    =>  $this->argument('collection')
		];
	}

	/**
	 * Get path to the template for the generator
	 *
	 * @return mixed
	 */
	protected function getTemplatePath()
	{
		return $this->getPathByOptionOrConfig('templatePath', 'model_template_path');
	}


	/**
	 * Get the console command options.
	 *
	 * @return array
	 */
	protected function getOptions()
	{
	    return array(
	        ['fields', null, InputOption::VALUE_OPTIONAL, 'Fields for the migration'],
	        ['path', null, InputOption::VALUE_OPTIONAL, 'Where should the file be created?'],
	        ['templatePath', null, InputOption::VALUE_OPTIONAL, 'The location of the template for this generator'],
	        ['testing', null, InputOption::VALUE_OPTIONAL, 'For internal use only.']
	    );
	}


	/**
	 * Get the console command arguments.
	 *
	 * @return array
	 */
	protected function getArguments()
	{
		return [
			['modelName', InputArgument::REQUIRED, 'The name of the desired Eloquent model'],
			['collection', InputArgument::REQUIRED, 'The name of the table'],
		];
	}

}
