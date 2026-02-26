<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Invoice;
use Carbon\Carbon;

class MarkOverdueInvoices extends Command
{
    protected $signature = 'invoices:mark-overdue';
    protected $description = 'Mark pending invoices as overdue if due_date is past today';

    public function handle()
    {
        $today = Carbon::today();

        $affected = Invoice::where('status', 'pending')
            ->whereDate('due_date', '<', $today)
            ->update([
                'status' => 'overdue'
            ]);

        $this->info("{$affected} invoice(s) marked as overdue.");

        return Command::SUCCESS;
    }
}