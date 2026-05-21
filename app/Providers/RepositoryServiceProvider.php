<?php

namespace App\Providers;

use App\Interfaces\GroupCycleRepositoryInterface;
use App\Interfaces\GroupRepositoryInterface;
use App\Interfaces\GroupUserRepositoryInterface;
use App\Interfaces\LoanRepositoryInterface;
use App\Interfaces\PaymentRepositoryInterface;
use App\Interfaces\PermissionRepositoryInterface;
use App\Interfaces\PreUserRepositoryInterface;
use App\Interfaces\RolePermissionRepositoryInterface;
use App\Interfaces\RoleRepositoryInterface;
use App\Interfaces\SavingRepositoryInterface;
use App\Interfaces\UserJoinRequestRepositoryInterface;
use Illuminate\Support\ServiceProvider;
use App\Interfaces\UserRepositoryInterface;
use App\Repositories\GroupCycleRepository;
use App\Repositories\GroupRepository;
use App\Repositories\GroupUserRepository;
use App\Repositories\LoanRepository;
use App\Repositories\PaymentRepository;
use App\Repositories\PermissionRepository;
use App\Repositories\PreUserRepository;
use App\Repositories\RolePermissionRepository;
use App\Repositories\RoleRepository;
use App\Repositories\SavingRepository;
use App\Repositories\UserJoinRequestRepository;
use App\Repositories\UserRepository;


class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind(UserRepositoryInterface::class, UserRepository::class);
        $this->app->bind(SavingRepositoryInterface::class, SavingRepository::class);
        $this->app->bind(LoanRepositoryInterface::class, LoanRepository::class);
        $this->app->bind(PaymentRepositoryInterface::class, PaymentRepository::class);
        $this->app->bind(GroupRepositoryInterface::class, GroupRepository::class);
        $this->app->bind(PreUserRepositoryInterface::class, PreUserRepository::class);
        $this->app->bind(RoleRepositoryInterface::class, RoleRepository::class);
        $this->app->bind(GroupCycleRepositoryInterface::class, GroupCycleRepository::class);
        $this->app->bind(UserJoinRequestRepositoryInterface::class, UserJoinRequestRepository::class);
        $this->app->bind(GroupUserRepositoryInterface::class, GroupUserRepository::class);
        $this->app->bind(PermissionRepositoryInterface::class, PermissionRepository::class);
        $this->app->bind(RolePermissionRepositoryInterface::class, RolePermissionRepository::class);


    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
