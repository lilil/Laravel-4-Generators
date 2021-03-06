<?php namespace Lilil\Generators\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Config;

class ResourceGeneratorCommand extends Command {

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'generate:resource';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate a new resource';

    /**
     * Generate a resource
     *
     * @return mixed
     */
    public function fire()
    {
        $resource = $this->argument('resource');

        $this->callModel($resource);
        $this->callView($resource);
        $this->callController($resource);
        $this->callMigration($resource);
        $this->callSeeder($resource);
        $this->callMigrate();

        // All done!
        $this->info(sprintf(
            "All done! Don't forget to add '%s` to %s." . PHP_EOL,
            "Route::resource('{$this->getTableName($resource)}', '{$this->getControllerName($resource)}');",
            "app/routes.php"
        ));

    }

    /**
     * Get the name for the model
     *
     * @param $resource
     * @return string
     */
    protected function getModelName($resource)
    {
        if (strpos($resource, '_')) {
            $resource = explode('_', $resource);
            $resource =  $resource[1];
        }

        return ucwords(str_singular(camel_case($resource)));
    }

    /**
     * Get the name for the controller
     *
     * @param $resource
     * @return string
     */
    protected function getControllerName($resource)
    {
        if (strpos($resource, '_')) {
            $resource = explode('_', $resource);
            $name =  $resource[1];
        }

        $prefix = Config::get("generators::config.controller_prefix");
//        $path = $this->getPathByOptionOrConfig('path', 'controller_target_path');
        return   $prefix . ucwords(str_plural(camel_case($name))) . 'Controller';
    }

    /**
     * Get the DB table name
     *
     * @param $resource
     * @return string
     */
    protected function getTableName($resource)
    {
        return str_plural($resource);
    }

    /**
     * Get the name for the migration
     *
     * @param $resource
     * @return string
     */
    protected function getMigrationName($resource)
    {
        return "create_" . str_plural($resource) . "_table";
    }

    /**
     * Call model generator if user confirms
     *
     * @param $resource
     */
    protected function callModel($resource)
    {
        $modelName = $this->getModelName($resource);
        $collection = $this->getTableName($resource);
       // $fields = $this->option('fields');
        if ($this->confirm("Ressource:Do you want me to create a $modelName model? [yes|no]"))
        {
//            $this->call('generate:model', compact('modelName', 'collection', 'fields'));

            $this->call('generate:model', [
                'modelName' => $modelName,
                'collection' => $collection,
                '--fields' => $this->option('fields'),
                '--templatePath' => Config::get("generators::config.scaffold_model_template_path")
            ]);


        }
    }

    /**
     * Call view generator if user confirms
     *
     * @param $resource
     */
    protected function callView($resource)
    {
        $collection = $this->getTableName($resource);
        $modelName = $this->getModelName($resource);
         if (strpos($resource, '_')) {
            $collection = explode('_', $collection);
            $collection =  $collection[1];
        }
        if ($this->confirm("Do you want me to create views for this $modelName resource? [yes|no]"))
        {
            foreach(['index', 'show', 'create', 'edit'] as $viewName)
            {
                $viewName = "{$collection}.{$viewName}";

                $this->call('generate:view', compact('viewName'));
            }
        }
    }

    /**
     * Call controller generator if user confirms
     *
     * @param $resource
     */
    protected function callController($resource)
    {
        $controllerName = $this->getControllerName($resource);
        $modelName = $this->getModelName($resource);
        if ($this->confirm("Do you want me to create a $controllerName & $modelName controller? [yes|no]"))
        {
            $this->call('generate:controller', compact('controllerName', 'modelName'));
        }
    }

    /**
     * Call migration generator if user confirms
     *
     * @param $resource
     */
    protected function callMigration($resource)
    {
        $migrationName = $this->getMigrationName($resource);

        if ($this->confirm("Do you want me to create a '$migrationName' migration and schema for this resource? [yes|no]"))
        {
            $this->call('generate:migration', [
                'migrationName' => $migrationName,
                '--fields' => $this->option('fields')
            ]);
        }
    }

    /**
     * Call seeder generator if user confirms
     *
     * @param $resource
     */
    protected function callSeeder($resource)
    {
        $tableName = str_plural($this->getModelName($resource));

        if ($this->confirm("Would you like a '$tableName' table seeder? [yes|no]"))
        {
            $this->call('generate:seed', compact('tableName'));
        }
    }

    /**
     * Migrate database if user confirms
     */
    protected function callMigrate()
    {
        if ($this->confirm('Do you want to go ahead and migrate the database? [yes|no]')) {
            $this->call('migrate');
            $this->info('Done!');
        }
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return [
            ['resource', InputArgument::REQUIRED, 'Singular resource name']
        ];
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return [
            ['fields', null, InputOption::VALUE_OPTIONAL, 'Fields for the migration']
        ];
    }

}
