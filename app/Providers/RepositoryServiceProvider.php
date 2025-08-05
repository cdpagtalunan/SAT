<?php

namespace App\Providers;

use App\Solid\Services\DropdownService;
use Illuminate\Support\ServiceProvider;
use App\Solid\Repositories\DropdownRepository;
use App\Solid\Repositories\AssemblyLineRepository;
use App\Solid\Repositories\OperationLineRepository;
use App\Solid\Services\Interfaces\DropdownServiceInterface;
use App\Solid\Repositories\Interfaces\DropdownRepositoryInterface;
use App\Solid\Repositories\Interfaces\AssemblyLineRepositoryInterface;
use App\Solid\Repositories\Interfaces\OperationLineRepositoryInterface;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(DropdownServiceInterface::class, DropdownService::class);
        $this->app->bind(DropdownRepositoryInterface::class, DropdownRepository::class);
        $this->app->bind(AssemblyLineRepositoryInterface::class, AssemblyLineRepository::class);
        $this->app->bind(OperationLineRepositoryInterface::class, OperationLineRepository::class);
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
