<?php

namespace App\Console\Commands;

use App\Models\Category;
use App\Models\Promotion;
use Illuminate\Console\Command;

class SetInactivePromotions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'promotions:set-inactive';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Mark exhausted promotions as inactive.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
       $promotions = Promotion::where('active', true)
            ->get();

        foreach ($promotions as $promotion) {
            if ($promotion->spend() >= $promotion->budget) {
                $promotion->update(['active' => false]);

                Category::find($promotion->category_id)
                    ->update(['promotion_id' => 0]);

                $this->info("Promotion {$promotion->id} has been set to inactive.");

                // todo, log db notification for this
            }
        }
    }
}
