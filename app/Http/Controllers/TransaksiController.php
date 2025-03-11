<?php

namespace App\Http\Controllers;

use App\Models\COA;
use App\Models\Transaksi;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class TransaksiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Transaksi::select('id', 'tanggal', 'coa_kode', 'coa_nama', 'desc', 'debit', 'credit')->get();

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $btn = '<button class="btn btn-warning btn-sm edit text-white" data-id="' . $row->id . '">Edit</button>';
                    $btn .= ' <button class="btn btn-danger btn-sm delete" data-id="' . $row->id . '">Hapus</button>';
                    return $btn;
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        $coa = COA::all();
        return view('transaksi.index', compact('coa'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'tanggal' => 'required|date',
            'coa_kode' => 'required|exists:coa,kode',
            'desc' => 'required|string|max:255',
            'nominal' => 'required|numeric',
            'status_transaksi' => 'required|in:debit,credit',
        ]);

        $coa = COA::where('kode', $request->coa_kode)->first();
        $request->merge(['coa_nama' => $coa->nama]);

        $transaksi = new Transaksi([
            'tanggal' => $request->tanggal,
            'coa_kode' => $request->coa_kode,
            'coa_nama' => $coa->nama,
            'desc' => $request->desc,
        ]);

        $transaksi->debit = $request->nominal;

        if ($request->status_transaksi === 'debit') {
            $transaksi->debit = $request->nominal;
            $transaksi->credit = 0;
        } else {
            $transaksi->credit = $request->nominal;
            $transaksi->debit = 0;
        }

        $transaksi->save();

        return response()->json(['success' => 'Transaksi berhasil disimpan.']);
    }

    /**
     * Display the specified resource.
     */
    public function show(Transaksi $transaksi)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $transaksi = Transaksi::find($id);

        if ($transaksi->debit > 0) {
            $transaksi->status_transaksi = 'debit';
            $transaksi->nominal = number_format($transaksi->debit, 0, ',', '.');
        } else {
            $transaksi->status_transaksi = 'credit';
            $transaksi->nominal = number_format($transaksi->credit, 0, ',', '.');
        }

        return response()->json($transaksi);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'tanggal' => 'required|date',
            'coa_kode' => 'required|exists:coa,kode',
            'desc' => 'required|string|max:255',
            'nominal' => 'required|numeric',
            'status_transaksi' => 'required|in:debit,credit',
        ]);

        $coa = COA::where('kode', $request->coa_kode)->first();
        $request->merge(['coa_nama' => $coa->nama]);

        $transaksi = Transaksi::find($id);
        $transaksi->tanggal = $request->tanggal;
        $transaksi->coa_kode = $request->coa_kode;
        $transaksi->coa_nama = $coa->nama;
        $transaksi->desc = $request->desc;

        if ($request->status_transaksi === 'debit') {
            $transaksi->debit = $request->nominal;
            $transaksi->credit = 0;
        } else {
            $transaksi->credit = $request->nominal;
            $transaksi->debit = 0;
        }

        $transaksi->save();

        return response()->json(['success' => 'Transaksi berhasil diperbarui.']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        Transaksi::find($id)->delete();
        return response()->json(['success' => 'Transaksi berhasil dihapus.']);
    }
}
