<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;

class MakeInterfaceCommand extends Command
{
     /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:interface {name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new interface command';

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
        $path = app_path("Solid/Interfaces/{$name}.php");

        if ($this->files->exists($path)) {
            $this->error("Interface {$name} already exists!");
            return;
        }

        $stub = $this->getStub();
        $content = str_replace('{{ InterfaceName }}', $name, $stub);

        $this->files->ensureDirectoryExists(dirname($path));
        $this->files->put($path, $content);

        $this->info("Interface {$name} created successfully!");
    }

     /**
     * Get the stub file content.
     */
    protected function getStub()
    {
        return $this->files->get(__DIR__ . '/stubs/interface.stub');
    }

}
