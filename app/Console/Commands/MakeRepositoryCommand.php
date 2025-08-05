<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;

class MakeRepositoryCommand extends Command
{
     /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:repository {name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new repository command';

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
        $path = app_path("Solid/Repositories/{$name}Repository.php");
        $path_interface = app_path("Solid/Repositories/Interfaces/{$name}RepositoryInterface.php");
        $namespace = 'App\Solid\Repositories\Interfaces';

        if ($this->files->exists($path) || $this->files->exists($path_interface)) {
            $this->error("{$name} already exists on repository or interface!");
            return;
        }

        $stub = $this->getStub();
        $stub_interface = $this->getStubInterface();
        $content = str_replace(
            ['{{ RepositoryName }}','{{ RepositoryInterface }}'],
            ["{$name}Repository", "{$name}RepositoryInterface"],
            $stub
        );
        $content_interface = str_replace(
            ['{{ InterfaceName }}', '{{ nameSpace }}'],
            ["{$name}RepositoryInterface", $namespace],
            $stub_interface
        );

        $this->files->ensureDirectoryExists(dirname($path));
        $this->files->put($path, $content);

        $this->files->ensureDirectoryExists(dirname($path_interface));
        $this->files->put($path_interface, $content_interface);

        $this->info("{$name} repository and interface created successfully!");
    }

     /**
     * Get the stub file content.
     */
    protected function getStub()
    {
        return $this->files->get(__DIR__ . '/stubs/repository.stub');
    }

     /**
     * Get the stub file content.
     */
    protected function getStubInterface()
    {
        return $this->files->get(__DIR__ . '/stubs/interface.stub');
    }


}
