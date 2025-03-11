<?php

namespace App\Http\Controllers;

use Yajra\DataTables\Facades\DataTables;
use App\Models\COA;
use App\Models\Kategori;
use Illuminate\Http\Request;

class COAController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = COA::with('kategori:id,nama')->select('id', 'kode', 'nama', 'kategori_id')->get();
            return Datatables::of($data)
                ->addIndexColumn()
                ->addColumn('kategori_nama', function ($row) {
                    return $row->kategori->nama ?? '-';
                })
                ->addColumn('action', function ($row) {
                    $btn = '<button class="btn btn-warning btn-sm edit text-white" data-id="' . $row->id . '">Edit</button>';
                    $btn .= ' <button class="btn btn-danger btn-sm delete" data-id="' . $row->id . '">Hapus</button>';
                    return $btn;
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        $kategori = Kategori::all();
        return view('coa.index', compact('kategori'));
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
            'kode' => 'required|string|max:255|unique:coa,kode',
            'nama' => 'required|string|max:255',
            'kategori_id' => 'required|exists:kategori,id',
        ]);

        COA::create($request->all());
        return response()->json(['success' => 'COA berhasil disimpan.']);
    }

    /**
     * Display the specified resource.
     */
    public function show(COA $cOA)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $coa = COA::find($id);
        return response()->json($coa);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'kode' => 'required|string|max:255|unique:coa,kode,' . $id,
            'nama' => 'required|string|max:255',
            'kategori_id' => 'required|exists:kategori,id',
        ]);

        $coa = COA::find($id);
        $coa->update($request->all());
        return response()->json(['success' => 'COA berhasil diperbarui.']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        COA::find($id)->delete();
        return response()->json(['success' => 'COA berhasil dihapus.']);
    }

    public function getKategori()
    {
        $kategori = Kategori::select('id', 'nama')->get();
        return response()->json($kategori);
    }
}
