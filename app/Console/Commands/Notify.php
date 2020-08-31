<?php

namespace App\Console\Commands;

use App\Client;
use App\Notifications\InvoicePaid;
use App\Shopping;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class Notify extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:notify';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command will help to notify user.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        try {
            $client = Client::findOrFail($this->ask('Enter client id number'));
            $shopping = Shopping::findOrFail($this->ask('Enter product id number'));
            $defaultIndex = 'mail';
            $channel = $this->choice(
                'Choose the channel',
                ['mail', 'database', 'nexmo'],
                $defaultIndex,
                $maxAttempts = null,
                $allowMultipleSelections = false
            );
            $client->notify(new InvoicePaid($channel, $shopping));

            echo "Notification sent successfully!" . "\n";

        } catch (\Exception $e) {
            dump('Error! Notification not sent! ' . $e->getMessage()
            );
             Log::error($e->getMessage());

        }

    }
}
