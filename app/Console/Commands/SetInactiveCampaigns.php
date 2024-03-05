<?php

namespace App\Console\Commands;

use App\Models\Campaign;
use Illuminate\Console\Command;

class SetInactiveCampaigns extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'campaigns:set-inactive';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Mark exhausted and expired campaigns as inactive.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->updateExpiredCampaigns();
        $this->updateExhaustedCampaigns();
    }

    protected function updateExpiredCampaigns()
    {
        $campaigns = Campaign::where('active', true)
            ->whereDate('end_date', '<', now())
            ->get();

        foreach ($campaigns as $campaign) {
            $campaign->update(['active' => false]);

            $this->info("Campaign {$campaign->id} has been set to inactive due to expiration.");
        }
    }

    protected function updateExhaustedCampaigns()
    {
        $campaigns = Campaign::where('active', true)
            ->get();

        foreach ($campaigns as $campaign) {
            if ($campaign->spend() >= $campaign->budget) {
                $campaign->update(['active' => false]);

                $this->info("Campaign {$campaign->id} has been set to inactive due to exhaustion.");
            }
        }
    }
}
