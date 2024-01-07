<?php

namespace App\Providers;

use App\Interfaces\GroupRepositoryInterface;
use App\Interfaces\LoanRepositoryInterface;
use App\Interfaces\PaymentRepositoryInterface;
use App\Interfaces\SavingRepositoryInterface;
use Illuminate\Support\ServiceProvider;
use App\Interfaces\UserRepositoryInterface;
use App\Repositories\GroupRepository;
use App\Repositories\LoanRepository;
use App\Repositories\PaymentRepository;
use App\Repositories\SavingRepository;
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

    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
