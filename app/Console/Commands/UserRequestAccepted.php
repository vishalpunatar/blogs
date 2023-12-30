<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\PublisherRequest;

class UserRequestAccepted extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:accepted';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'To Accept Users Request who want to become a Publisher';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $requests = PublisherRequest::all();
        foreach ($requests as $requests) {
            $requests->where('req_approval', 0)->update(['req_approval' => 1]);
            $requests->user->where('id',$requests->user_id)->update(['role' => 1]);
        }
        echo "Approved ";
    }
}
