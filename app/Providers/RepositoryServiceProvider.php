<?php

namespace App\Providers;

use App\Solid\Services\SATService;
use App\Solid\Services\CommonService;
use App\Solid\Services\DropdownService;
use Illuminate\Support\ServiceProvider;
use App\Solid\Services\LineBalanceService;
use App\Solid\Repositories\DropdownRepository;
use App\Solid\Repositories\SATHeaderRepository;
use App\Solid\Repositories\SystemoneRepository;
use App\Solid\Repositories\SATProcessRepository;
use App\Solid\Repositories\AssemblyLineRepository;
use App\Solid\Repositories\OperationLineRepository;
use App\Solid\Services\Interfaces\SATServiceInterface;
use App\Solid\Services\Interfaces\CommonServiceInterface;
use App\Solid\Services\Interfaces\DropdownServiceInterface;
use App\Solid\Services\Interfaces\LineBalanceServiceInterface;
use App\Solid\Repositories\Interfaces\DropdownRepositoryInterface;
use App\Solid\Repositories\Interfaces\SATHeaderRepositoryInterface;
use App\Solid\Repositories\Interfaces\SystemoneRepositoryInterface;
use App\Solid\Repositories\Interfaces\SATProcessRepositoryInterface;
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
        $this->app->bind(SATServiceInterface::class, SATService::class);
        $this->app->bind(LineBalanceServiceInterface::class, LineBalanceService::class);
        $this->app->bind(SATHeaderRepositoryInterface::class, SATHeaderRepository::class);
        $this->app->bind(SATProcessRepositoryInterface::class, SATProcessRepository::class);
        $this->app->bind(CommonServiceInterface::class, CommonService::class);
        $this->app->bind(SystemoneRepositoryInterface::class, SystemoneRepository::class);
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
