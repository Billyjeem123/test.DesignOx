<?php

namespace App\Providers;

// use Illuminate\Support\Facades\Gate;
use App\Models\Design;
use App\Models\Job;
use App\Models\Proposal;
use App\Models\Reviews;
use App\Policies\DesignPolicy;
use App\Policies\JobPolicy;
use App\Policies\ProposalPolicy;
use App\Policies\ReviewPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
//         'App\Models\Model' => 'App\Policies\ModelPolicy',
        Job::class => JobPolicy::class,
        Proposal::class => ProposalPolicy::class,
         Reviews::class => ReviewPolicy::class,
        Design::class => DesignPolicy::class
     ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot(): void
    {
        $this->registerPolicies();

        //
    }
}
