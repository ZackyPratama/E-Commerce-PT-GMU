<?php

namespace App\Console\Commands;

use App\Mail\RFQRejected;
use App\Models\RFQ;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class RfqExpire extends Command
{
    protected $signature = 'rfq:expire';

    protected $description = 'Auto-expire RFQs past their valid_until date';

    public function handle()
    {
        $expired = RFQ::whereNotNull('valid_until')
            ->where('valid_until', '<', now())
            ->where('status', 'quoted')
            ->get();

        $count = 0;
        foreach ($expired as $rfq) {
            $rfq->update(['status' => 'expired']);
            Mail::to($rfq->customer->email)->queue(new RFQRejected($rfq));
            $count++;
        }

        $this->info("{$count} RFQ(s) expired.");
    }
}
