<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\PublisherRequest;
use Illuminate\Support\Collection;

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
        try {
            PublisherRequest::where('req_approval', 0)
            ->select('req_approval','user_id')
            ->chunk(10, function ($requests){
                foreach ($requests as $request) {
                    $request->where('req_approval',0)->update(['req_approval' => 1]);
                    $request->user->update(['role' => 1]);
                }
            });
            
            $this->info('The Request Accepted Successfully.');
        } catch (\Exception $e) {
            $this->error('Something Went Wrong! Error: '.$e->getMessage());
        }
    }
}
