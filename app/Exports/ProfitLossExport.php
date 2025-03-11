<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Illuminate\Support\Facades\DB;

class ProfitLossExport implements FromCollection, WithHeadings, WithStyles, WithTitle, WithCustomStartCell
{
    protected $year;

    public function __construct($year)
    {
        $this->year = $year;
    }

    public function collection()
    {
        $transactions = DB::table('transaksi')
            ->select(
                DB::raw("DATE_FORMAT(tanggal, '%Y-%m') as bulan"),
                'coa_nama',
                DB::raw("SUM(credit) as total_income"),
                DB::raw("SUM(debit) as total_expense")
            )
            ->whereYear('tanggal', $this->year)
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

        $exportData = [];

        foreach ($data as $category => $values) {
            if (array_sum(array_column($values, 'income')) > 0) {
                foreach ($months as $month => $v) {
                    $exportData[] = [
                        'Category' => $category,
                        $month => 'Rp ' . number_format($values[$month]['income'] ?? 0, 0, ',', '.')
                    ];
                }
            }
        }

        $totalIncome = [];
        foreach ($months as $month => $v) {
            $totalIncome[$month] = 0;
            foreach ($data as $category => $values) {
                if (array_sum(array_column($values, 'income')) > 0) {
                    $totalIncome[$month] += $values[$month]['income'] ?? 0;
                }
            }
            $exportData[] = [
                'Category' => 'Total Income',
                $month => 'Rp ' . number_format($totalIncome[$month], 0, ',', '.')
            ];
        }

        foreach ($data as $category => $values) {
            if (array_sum(array_column($values, 'expense')) > 0) {
                foreach ($months as $month => $v) {
                    $exportData[] = [
                        'Category' => $category,
                        $month => 'Rp ' . number_format($values[$month]['expense'] ?? 0, 0, ',', '.')
                    ];
                }
            }
        }

        $totalExpense = [];
        foreach ($months as $month => $v) {
            $totalExpense[$month] = 0;
            foreach ($data as $category => $values) {
                if (array_sum(array_column($values, 'expense')) > 0) {
                    $totalExpense[$month] += $values[$month]['expense'] ?? 0;
                }
            }
            $exportData[] = [
                'Category' => 'Total Expense',
                $month => 'Rp ' . number_format($totalExpense[$month], 0, ',', '.')
            ];
        }

        foreach ($months as $month => $v) {
            $netIncome = ($totalIncome[$month] ?? 0) - ($totalExpense[$month] ?? 0);
            $exportData[] = [
                'Category' => 'Net Income',
                $month => 'Rp ' . number_format($netIncome, 0, ',', '.')
            ];
        }

        return collect($exportData);
    }

    public function headings(): array
    {
        $headings = ['Category'];
        $months = DB::table('transaksi')
            ->select(DB::raw("DATE_FORMAT(tanggal, '%Y-%m') as bulan"))
            ->whereYear('tanggal', $this->year)
            ->groupBy('bulan')
            ->orderBy('bulan')
            ->pluck('bulan')
            ->toArray();

        foreach ($months as $month) {
            $headings[] = $month;
        }

        return $headings;
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->mergeCells('A1:' . $sheet->getHighestColumn() . '1');
        $sheet->setCellValue('A1', 'Laporan Profit / Loss Tahun ' . $this->year);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal('center');
        $sheet->getStyle('A1')->getFont()->setSize(16)->setBold(true);

        $sheet->getStyle('A2:' . $sheet->getHighestColumn() . '2')->getFont()->setBold(true);
        $sheet->getStyle('A2:' . $sheet->getHighestColumn() . '2')->getAlignment()->setHorizontal('center');

        foreach (range('A', $sheet->getHighestColumn()) as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }
    }

    public function title(): string
    {
        return 'Laporan Profit / Loss';
    }

    public function startCell(): string
    {
        return 'A2';
    }
}
