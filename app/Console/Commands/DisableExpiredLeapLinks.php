<?php

namespace App\Console\Commands;

use App\Biolink;
use Carbon\Carbon;
use Illuminate\Console\Command;

class DisableExpiredLeapLinks extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'leapLinks:disable';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Disable expires biolink leap links.';

    public function handle()
    {
        // TODO: fix
        app(Biolink::class)
            ->links()
            ->where('leap_until', '<', Carbon::now())
            ->update(['leap_until' => null]);

        $this->info('Disabled all expired leap links.');

        return 0;
    }
}
