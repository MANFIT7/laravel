<?php

namespace App\Http\Controllers;

use App\Models\Sales;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SalesController extends Controller
{
    public function index()
    {
        return view('sales.index');
    }

    public function list()
    {
        $sales = Sales::latest()->get();
        return response()->json(['data' => $sales]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'invoice_number' => 'required|unique:sales',
            'sale_date' => 'required|date',
            'customer_name' => 'required',
            'total_amount' => 'required|numeric|min:0',
            'notes' => 'nullable',
            'status' => 'required|in:pending,completed,cancelled'
        ], [
            'invoice_number.required' => 'Nomor faktur harus diisi',
            'invoice_number.unique' => 'Nomor faktur sudah digunakan',
            'sale_date.required' => 'Tanggal penjualan harus diisi',
            'sale_date.date' => 'Format tanggal tidak valid',
            'customer_name.required' => 'Nama pelanggan harus diisi',
            'total_amount.required' => 'Total harus diisi',
            'total_amount.numeric' => 'Total harus berupa angka',
            'total_amount.min' => 'Total tidak boleh kurang dari 0',
            'status.required' => 'Status harus diisi',
            'status.in' => 'Status tidak valid'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $sale = Sales::create($request->all());
        return response()->json(['message' => 'Data penjualan berhasil disimpan', 'data' => $sale]);
    }

    public function show($id)
    {
        $sale = Sales::findOrFail($id);
        return response()->json(['data' => $sale]);
    }

    public function update(Request $request, $id)
    {
        $sale = Sales::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'invoice_number' => 'required|unique:sales,invoice_number,'.$id,
            'sale_date' => 'required|date',
            'customer_name' => 'required',
            'total_amount' => 'required|numeric|min:0',
            'notes' => 'nullable',
            'status' => 'required|in:pending,completed,cancelled'
        ], [
            'invoice_number.required' => 'Nomor faktur harus diisi',
            'invoice_number.unique' => 'Nomor faktur sudah digunakan',
            'sale_date.required' => 'Tanggal penjualan harus diisi',
            'sale_date.date' => 'Format tanggal tidak valid',
            'customer_name.required' => 'Nama pelanggan harus diisi',
            'total_amount.required' => 'Total harus diisi',
            'total_amount.numeric' => 'Total harus berupa angka',
            'total_amount.min' => 'Total tidak boleh kurang dari 0',
            'status.required' => 'Status harus diisi',
            'status.in' => 'Status tidak valid'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $sale->update($request->all());
        return response()->json(['message' => 'Data penjualan berhasil diperbarui', 'data' => $sale]);
    }

    public function destroy($id)
    {
        $sale = Sales::findOrFail($id);
        $sale->delete();
        return response()->json(['message' => 'Data penjualan berhasil dihapus']);
    }
}
