@extends('layouts.app')

@section('content')
    <div class="container">
        <h2 class="mb-4">Laporan Profit / Loss</h2>

        <!-- Dropdown Pilih Tahun -->
        <form method="GET" action="{{ route('profit.loss') }}" class="mb-3">
            <div class="row">
                <div class="col-md-3">
                    <label for="year" class="form-label fw-bold">Pilih Tahun:</label>
                    <select name="year" id="year" class="form-select" onchange="this.form.submit()">
                        @for ($y = now()->year - 5; $y <= now()->year; $y++)
                            <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                        @endfor
                    </select>
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <a href="{{ route('profit.loss.export', ['year' => $year]) }}" class="btn btn-success">
                        <i class="fas fa-file-excel"></i> Export ke Excel
                    </a>
                </div>
            </div>
        </form>

        <!-- Tabel Profit & Loss -->
        <table class="table table-bordered">
            <thead class="table-warning text-center">
                <tr>
                    <th rowspan="2" class="align-middle">Category</th>
                    @foreach ($months as $month => $v)
                        <th>{{ $month }}</th>
                    @endforeach
                </tr>
                <tr>
                    @foreach ($months as $month => $v)
                        <th>Amount</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                <!-- Income -->
                @php $totalIncome = []; @endphp
                @foreach ($data as $category => $values)
                    @php
                        $isIncome = array_sum(array_column($values, 'income')) > 0;
                    @endphp
                    @if ($isIncome)
                        <tr class="table-success">
                            <td>{{ $category }}</td>
                            @foreach ($months as $month => $v)
                                <td>Rp {{ number_format($values[$month]['income'] ?? 0, 0, ',', '.') }}</td>
                                @php $totalIncome[$month] = ($totalIncome[$month] ?? 0) + ($values[$month]['income'] ?? 0); @endphp
                            @endforeach
                        </tr>
                    @endif
                @endforeach
                <tr class="table-success fw-bold">
                    <td>Total Income</td>
                    @foreach ($months as $month => $v)
                        <td>Rp {{ number_format($totalIncome[$month] ?? 0, 0, ',', '.') }}</td>
                    @endforeach
                </tr>

                <!-- Expense -->
                @php $totalExpense = []; @endphp
                @foreach ($data as $category => $values)
                    @php
                        $isExpense = array_sum(array_column($values, 'expense')) > 0;
                    @endphp
                    @if ($isExpense)
                        <tr class="table-danger">
                            <td>{{ $category }}</td>
                            @foreach ($months as $month => $v)
                                <td>Rp {{ number_format($values[$month]['expense'] ?? 0, 0, ',', '.') }}</td>
                                @php $totalExpense[$month] = ($totalExpense[$month] ?? 0) + ($values[$month]['expense'] ?? 0); @endphp
                            @endforeach
                        </tr>
                    @endif
                @endforeach
                <tr class="table-danger fw-bold">
                    <td>Total Expense</td>
                    @foreach ($months as $month => $v)
                        <td>Rp {{ number_format($totalExpense[$month] ?? 0, 0, ',', '.') }}</td>
                    @endforeach
                </tr>

                <!-- Net Income -->
                <tr class="table-secondary fw-bold">
                    <td>Net Income</td>
                    @foreach ($months as $month => $v)
                        @php $netIncome = ($totalIncome[$month] ?? 0) - ($totalExpense[$month] ?? 0); @endphp
                        <td>Rp {{ number_format($netIncome, 0, ',', '.') }}</td>
                    @endforeach
                </tr>
            </tbody>
        </table>
    </div>
@endsection
