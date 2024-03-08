<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;

class CreateApiToken extends Command
{
    protected $signature = 'api:createToken {userId}';
    protected $description = 'Create a new API token for a user';

    public function handle()
    {
        $userId = $this->argument('userId');
        $user = User::find($userId);

        if (!$user) {
            $this->error('User not found!');
            return 1;
        }

        $token = $user->createToken('API Token')->plainTextToken;

        $this->info("API Token for user {$userId}: {$token}");

        return 0;
    }
}
