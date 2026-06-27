<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Traits\ReportTrait;

class ReportController extends Controller
{
    use ReportTrait;

    public function orders()
    {
        $fromDate = $this->getFromDate();
        $query = Order::query();
        if ($fromDate) {
            $query->where('created_at', '>', $fromDate);
        }
        return $query->get();
    }

    public function customers()
    {

    }
}
