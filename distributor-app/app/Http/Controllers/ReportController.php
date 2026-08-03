<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\Area;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $query = Transaction::with(['customer.area', 'items.product', 'plant']);

        // Filters
        if ($start = $request->start_date) {
            $query->whereDate('transaction_date', '>=', $start);
        }
        if ($end = $request->end_date) {
            $query->whereDate('transaction_date', '<=', $end);
        }
        if ($areaId = $request->area_id) {
            $query->whereHas('customer', fn ($q) => $q->where('area_id', $areaId));
        }
        if ($status = $request->status) {
            $query->where('status', $status);
        }

        $sort = $request->sort ?? 'date_desc';
        if ($sort === 'date_asc') {
            $query->orderBy('transaction_date', 'asc');
        } else {
            $query->orderBy('transaction_date', 'desc');
        }

        $transactions = $query->paginate(30)->withQueryString();

        // Stats summary — clone & reset order to avoid PostgreSQL grouping error
        $stats = (clone $query)
            ->reorder()
            ->selectRaw('
                COUNT(*) as total_orders,
                SUM(total) as total_sales,
                SUM(ongkir) as total_ongkir,
                SUM(harga_beli) as total_cost,
                SUM(margin_estimate) as total_margin,
                AVG(margin_estimate) as avg_margin
            ')
            ->first();

        $areas = Area::orderBy('name')->get();

        return inertia('Admin/Report/Index', [
            'transactions' => $transactions,
            'stats' => $stats,
            'areas' => $areas,
            'filters' => [
                'start_date' => $request->start_date ?? '',
                'end_date' => $request->end_date ?? '',
                'area_id' => $request->area_id ?? '',
                'status' => $request->status ?? '',
                'sort' => $request->sort ?? 'date_desc',
            ],
        ]);
    }

    public function exportCsv(Request $request)
    {
        $query = Transaction::with(['customer.area', 'items.product', 'plant']);

        if ($start = $request->start_date) {
            $query->whereDate('transaction_date', '>=', $start);
        }
        if ($end = $request->end_date) {
            $query->whereDate('transaction_date', '<=', $end);
        }
        if ($areaId = $request->area_id) {
            $query->whereHas('customer', fn ($q) => $q->where('area_id', $areaId));
        }
        if ($status = $request->status) {
            $query->where('status', $status);
        }

        $transactions = $query->orderBy('transaction_date', 'desc')->get();

        $filename = 'laporan-transaksi-' . now()->format('Y-m-d-His') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function () use ($transactions) {
            $handle = fopen('php://output', 'w');
            // BOM for Excel UTF-8
            fwrite($handle, "\xEF\xBB\xBF");
            // Header
            fputcsv($handle, [
                'ID', 'Tanggal', 'Customer', 'Area', 'Pabrik',
                'Subtotal', 'Ongkir', 'Total', 'Harga Beli',
                'Proyeksi Margin', 'Status', 'Pembayaran',
            ]);
            // Rows
            foreach ($transactions as $t) {
                fputcsv($handle, [
                    $t->id,
                    $t->transaction_date?->format('d/m/Y') ?? '',
                    $t->customer?->company_name ?? $t->customer?->user?->name ?? '',
                    $t->customer?->area?->name ?? '',
                    $t->plant?->name ?? '',
                    number_format($t->subtotal, 0, '.', ''),
                    number_format($t->ongkir, 0, '.', ''),
                    number_format($t->total, 0, '.', ''),
                    number_format($t->harga_beli, 0, '.', ''),
                    number_format($t->margin_estimate ?? 0, 0, '.', ''),
                    ucfirst($t->status ?? ''),
                    ucfirst($t->payment_method ?? ''),
                ]);
            }
            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }
}
