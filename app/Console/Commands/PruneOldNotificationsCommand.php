<?php

namespace App\Console\Commands;

use App\Actions\PruneOldNotificationsAction;
use Illuminate\Console\Command;
use Symfony\Component\Console\Command\Command as CommandAlias;

class PruneOldNotificationsCommand extends Command
{
    protected $signature = 'notifications:prune';

    protected $description = 'Prune old notifications, keeping only the 10 most recent for each user.';

    public function handle(PruneOldNotificationsAction $pruneOldNotificationsAction): int
    {
        $this->info('Pruning old notifications...');

        ($pruneOldNotificationsAction)();

        $this->info('Old notifications pruned successfully.');

        return CommandAlias::SUCCESS;
    }
}
