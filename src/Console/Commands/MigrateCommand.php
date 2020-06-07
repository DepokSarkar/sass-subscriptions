<?php

declare(strict_types=1);

namespace DepokSarkar\Subscriptions\Console\Commands;

use Illuminate\Console\Command;

class MigrateCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'depoksarkar:migrate:subscriptions {--f|force : Force the operation to run when in production.}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Migrate DepokSarkar Subscriptions Tables.';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle(): void
    {
        $this->alert($this->description);

        $path = config('depoksarkar.subscriptions.autoload_migrations') ?
            'vendor/depoksarkar/sass-subscriptions/database/migrations' :
            'database/migrations/depoksarkar/sass-subscriptions';

        if (file_exists($path)) {
            $this->call('migrate', [
                '--step' => true,
                '--path' => $path,
                '--force' => $this->option('force'),
            ]);
        } else {
            $this->warn('No migrations found! Consider publish them first: <fg=green>php artisan depoksarkar:publish:subscriptions</>');
        }

        $this->line('');
    }
}
