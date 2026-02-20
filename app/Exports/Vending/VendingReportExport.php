<?php

namespace App\Exports\Vending;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class VendingReportExport implements WithMultipleSheets
{
    public function __construct(private readonly array $report)
    {
    }

    public function sheets(): array
    {
        return [
            new SummarySheet($this->report),
            new TransactionSheet($this->report),
            new ProductSheet($this->report),
        ];
    }
}
