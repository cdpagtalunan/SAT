<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;

class MakeServicesCommand extends Command
{
     /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:services {name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new services command';

    /**
     * File system instance.
     */
    protected Filesystem $files;

    /**
     * Create a new command instance.
     */
    public function __construct(Filesystem $files)
    {
        parent::__construct();
        $this->files = $files;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $name = $this->argument('name');
        $path = app_path("Solid/Services/{$name}Service.php");
        $path_interface = app_path("Solid/Services/Interfaces/{$name}ServiceInterface.php");
        $namespace = 'App\Solid\Services\Interfaces';


        if ($this->files->exists($path) || $this->files->exists($path_interface)) {
            $this->error("{$name} Service and Service Interface already exists!");
            return;
        }

        $stub = $this->getStub();
        $stub_interface = $this->getStubInterface();
        $content = str_replace(
            ['{{ ServiceName }}', '{{ serviceInterface }}'], 
            ["{$name}Service", "{$name}ServiceInterface" ],
            $stub);
        $content_interface = str_replace(
            ['{{ InterfaceName }}', '{{ nameSpace }}'],
            ["{$name}ServiceInterface", $namespace],
             $stub_interface);

        $this->files->ensureDirectoryExists(dirname($path));
        $this->files->put($path, $content);

        $this->files->ensureDirectoryExists(dirname($path_interface));
        $this->files->put($path_interface, $content_interface);

        $this->info("{$name} service and interface created successfully!");
    }

     /**
     * Get the stub file content.
     */
    protected function getStub()
    {
        return $this->files->get(__DIR__ . '/stubs/service.stub');
    }
    protected function getStubInterface()
    {
        return $this->files->get(__DIR__ . '/stubs/interface.stub');
    }

}
