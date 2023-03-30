<?php

namespace Reinbier\LaravelUniqueWith\Commands;

use Illuminate\Console\Command;

class LaravelUniqueWithCommand extends Command
{
    public $signature = 'laravel-unique-with';

    public $description = 'My command';

    public function handle(): int
    {
        $this->comment('All done');

        return self::SUCCESS;
    }
}
