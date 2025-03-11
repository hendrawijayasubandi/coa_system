<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Exports\ProfitLossExport;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class ProfitLossController extends Controller
{
    public function index(Request $request)
    {
        $year = $request->get('year', date('Y'));

        $transactions = DB::table('transaksi')
            ->select(
                DB::raw("DATE_FORMAT(tanggal, '%Y-%m') as bulan"),
                'coa_nama',
                DB::raw("SUM(credit) as total_income"),
                DB::raw("SUM(debit) as total_expense")
            )
            ->whereYear('tanggal', $year)
            ->groupBy('bulan', 'coa_nama')
            ->orderBy('bulan')
            ->get();

        $data = [];
        $months = [];

        foreach ($transactions as $t) {
            $months[$t->bulan] = true;
            $data[$t->coa_nama][$t->bulan] = [
                'income' => $t->total_income,
                'expense' => $t->total_expense
            ];
        }

        return view('profit_loss.index', compact('data', 'months', 'year'));
    }

    public function export(Request $request)
    {
        $year = $request->query('year', now()->year);
        return Excel::download(new ProfitLossExport($year), "Laporan_Profit_Loss_{$year}.xlsx");
    }
}
