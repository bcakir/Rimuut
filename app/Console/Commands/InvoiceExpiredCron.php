<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Invoice;
use App\Models\Status;

class InvoiceExpiredCron extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'invoice:expired';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Closes overdue invoices';

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
     * @return void
     */
    public function handle()
    {
        $counter = 0;
        $status = Status::where('name', 'Expired')->first();
        $invoices = Invoice::where('status_id', '!=', $status->id)
            ->where('created_at', '<', date('Y-m-d 00:00:00'))->get();

        if ($invoices)
        {
            foreach ($invoices as $invoice) {
                $invoice->status_id = $status->id;
                $invoice->save();
                $counter++;
            }
        }

        $this->info(sprintf("%u invoice record updated.", $counter));
    }
}
