<?php

declare(strict_types=1);

namespace DepokSarkar\Subscriptions\Providers;

use DepokSarkar\Subscriptions\Models\Plan;
use Illuminate\Support\ServiceProvider;
use Rinvex\Support\Traits\ConsoleTools;
use DepokSarkar\Subscriptions\Models\PlanFeature;
use DepokSarkar\Subscriptions\Models\PlanSubscription;
use DepokSarkar\Subscriptions\Models\PlanSubscriptionUsage;
use DepokSarkar\Subscriptions\Console\Commands\MigrateCommand;
use DepokSarkar\Subscriptions\Console\Commands\PublishCommand;
use DepokSarkar\Subscriptions\Console\Commands\RollbackCommand;

class SubscriptionsServiceProvider extends ServiceProvider
{
    use ConsoleTools;

    /**
     * The commands to be registered.
     *
     * @var array
     */
    protected $commands = [
        MigrateCommand::class => 'command.depoksarkar.subscriptions.migrate',
        PublishCommand::class => 'command.depoksarkar.subscriptions.publish',
        RollbackCommand::class => 'command.depoksarkar.subscriptions.rollback',
    ];

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register(): void
    {
        $this->mergeConfigFrom(realpath(__DIR__.'/../../config/config.php'), 'depoksarkar.subscriptions');

        // Bind eloquent models to IoC container
        $this->app->singleton('depoksarkar.subscriptions.plan', $planModel = $this->app['config']['depoksarkar.subscriptions.models.plan']);
        $planModel === Plan::class || $this->app->alias('depoksarkar.subscriptions.plan', Plan::class);

        $this->app->singleton('depoksarkar.subscriptions.plan_feature', $planFeatureModel = $this->app['config']['depoksarkar.subscriptions.models.plan_feature']);
        $planFeatureModel === PlanFeature::class || $this->app->alias('depoksarkar.subscriptions.plan_feature', PlanFeature::class);

        $this->app->singleton('depoksarkar.subscriptions.plan_subscription', $planSubscriptionModel = $this->app['config']['depoksarkar.subscriptions.models.plan_subscription']);
        $planSubscriptionModel === PlanSubscription::class || $this->app->alias('depoksarkar.subscriptions.plan_subscription', PlanSubscription::class);

        $this->app->singleton('depoksarkar.subscriptions.plan_subscription_usage', $planSubscriptionUsageModel = $this->app['config']['depoksarkar.subscriptions.models.plan_subscription_usage']);
        $planSubscriptionUsageModel === PlanSubscriptionUsage::class || $this->app->alias('depoksarkar.subscriptions.plan_subscription_usage', PlanSubscriptionUsage::class);

        // Register console commands
        $this->registerCommands($this->commands);
    }

    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot(): void
    {
        // Publish Resources
        $this->publishesConfig('depoksarkar/laravel-subscriptions');
        $this->publishesMigrations('depoksarkar/laravel-subscriptions');
        ! $this->autoloadMigrations('depoksarkar/laravel-subscriptions') || $this->loadMigrationsFrom(__DIR__.'/../../database/migrations');
    }
}
