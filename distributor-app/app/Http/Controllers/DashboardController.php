<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Transaction;
use App\Models\TransactionItem;
use Inertia\Inertia;

class DashboardController extends Controller
{
    private const MONTHS = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];

    public function owner()
    {
        return Inertia::render('Owner/Dashboard', $this->businessDashboard());
    }

    public function admin()
    {
        $pendingOrders = Transaction::with(['customer.area', 'items.product'])
            ->where('status', 'pending')
            ->latest('transaction_date')
            ->limit(5)
            ->get();

        return Inertia::render('Admin/Dashboard', [
            ...$this->businessDashboard(),
            'pending_orders' => $this->mapTransactions($pendingOrders),
        ]);
    }

    public function sales()
    {
        $areaId = auth()->user()?->sales_area_id;

        $txs = Transaction::query()
            ->whereHas('customer', fn ($q) => $q->where('area_id', $areaId));

        $trend = $this->trend($areaId);

        $data = [
            'kpis' => [
                'omzet_area' => $this->monthTotal(0, $areaId),
                'orders_area' => $txs->whereYear('transaction_date', now()->year)
                    ->whereMonth('transaction_date', now()->month)
                    ->count(),
                'customers_area' => Customer::where('area_id', $areaId)->count(),
                'margin_area' => $this->monthTotal(0, $areaId, 'margin_estimate'),
                'delta' => $this->deltaPercent(0, $areaId),
            ],
            'trend' => $trend,
            'top_products' => $this->topProducts($areaId),
            'recent' => $this->mapTransactions(
                $txs->with(['customer.area', 'items.product'])->latest('transaction_date')->limit(8)->get()
            ),
            'area_name' => auth()->user()?->salesArea?->name,
        ];

        return Inertia::render('Sales/Dashboard', $data);
    }

    public function customer()
    {
        $customer = Customer::where('user_id', auth()->id())->first();

        if (! $customer) {
            return Inertia::render('Customer/Dashboard', [
                'kpis' => null,
                'orders' => [],
                'customer' => null,
            ]);
        }

        $txs = Transaction::where('customer_id', $customer->id);

        $orders = $this->mapTransactions(
            $txs->with(['customer.area', 'items.product'])->latest('transaction_date')->limit(12)->get()
        );

        return Inertia::render('Customer/Dashboard', [
            'customer' => $customer,
            'kpis' => [
                'active_orders' => Transaction::where('customer_id', $customer->id)
                    ->where('status', 'pending')->count(),
                'total_orders' => Transaction::where('customer_id', $customer->id)->count(),
                'total_spent' => (float) Transaction::where('customer_id', $customer->id)->sum('total'),
                'last_order' => $orders[0]['transaction_date'] ?? null,
            ],
            'orders' => $orders,
        ]);
    }

    private function businessDashboard(): array
    {
        $trend = $this->trend();

        return [
            'kpis' => [
                'omzet_month' => $this->monthTotal(0),
                'orders_month' => $this->monthCount(0),
                'customers_active' => Customer::whereHas('transactions')->count(),
                'margin_month' => $this->monthTotal(0, null, 'margin_estimate'),
                'margin_pct' => $this->marginPercent(0),
                'delta' => $this->deltaPercent(0),
                'orders_approved' => Transaction::whereYear('transaction_date', now()->year)
                    ->whereMonth('transaction_date', now()->month)
                    ->where('status', 'approved')
                    ->count(),
                'pending_count' => Transaction::where('status', 'pending')->count(),
            ],
            'trend' => $trend,
            'top_products' => $this->topProducts(),
            'area_sales' => $this->areaSales(),
            'recent' => $this->mapTransactions(
                Transaction::with(['customer.area', 'items.product'])->latest('transaction_date')->limit(8)->get()
            ),
        ];
    }

    private function trend(?int $areaId = null): array
    {
        $start = now()->startOfMonth()->subMonths(5);

        $rows = Transaction::query()
            ->when($areaId, fn ($q) => $q->whereHas('customer', fn ($cq) => $cq->where('area_id', $areaId)))
            ->where('transaction_date', '>=', $start)
            ->get(['transaction_date', 'total', 'margin_estimate']);

        $labels = [];
        $total = [];
        $margin = [];
        for ($i = 5; $i >= 0; $i--) {
            $d = now()->startOfMonth()->subMonths($i);
            $key = $d->format('Y-m');
            $labels[] = self::MONTHS[(int) $d->format('n') - 1];
            $total[$key] = 0;
            $margin[$key] = 0;
        }

        foreach ($rows as $row) {
            $key = $row->transaction_date->format('Y-m');
            if (! isset($total[$key])) {
                continue;
            }
            $total[$key] += (float) $row->total;
            $margin[$key] += (float) $row->margin_estimate;
        }

        return [
            'labels' => $labels,
            'total' => array_values($total),
            'margin' => array_values($margin),
        ];
    }

    private function topProducts(?int $areaId = null): array
    {
        $items = TransactionItem::query()
            ->join('transactions', 'transactions.id', '=', 'transaction_items.transaction_id')
            ->join('products', 'products.id', '=', 'transaction_items.product_id')
            ->when($areaId, fn ($q) => $q->whereHas('transaction.customer', fn ($cq) => $cq->where('area_id', $areaId)))
            ->selectRaw('products.name, sum(transaction_items.quantity) as qty, sum(transaction_items.subtotal) as total')
            ->groupBy('products.id', 'products.name')
            ->orderByDesc('qty')
            ->limit(5)
            ->get();

        return $items->map(fn ($i) => [
            'name' => $i->name,
            'qty' => (int) $i->qty,
            'total' => (float) $i->total,
        ])->values()->all();
    }

    private function areaSales(): array
    {
        return Transaction::query()
            ->join('customers', 'customers.id', '=', 'transactions.customer_id')
            ->join('areas', 'areas.id', '=', 'customers.area_id')
            ->selectRaw('areas.name, sum(transactions.total) as total, sum(transactions.margin_estimate) as margin')
            ->groupBy('areas.id', 'areas.name')
            ->orderByDesc('total')
            ->get()
            ->map(fn ($row) => [
                'name' => $row->name,
                'total' => (float) $row->total,
                'margin' => (float) $row->margin,
            ])
            ->values()
            ->all();
    }

    private function mapTransactions($transactions): array
    {
        return collect($transactions)->map(fn (Transaction $t) => [
            'id' => $t->id,
            'transaction_date' => $t->transaction_date->toISOString(),
            'subtotal' => (float) $t->subtotal,
            'ongkir' => (float) $t->ongkir,
            'harga_beli' => (float) $t->harga_beli,
            'total' => (float) $t->total,
            'margin_estimate' => (float) ($t->margin_estimate ?? 0),
            'margin_status' => $t->margin_status,
            'status' => $t->status,
            'items_count' => $t->items->count(),
            'customer' => $t->customer ? [
                'company_name' => $t->customer->company_name,
                'area' => $t->customer->area?->name,
            ] : null,
        ])->values()->all();
    }

    private function monthTotal(int $offset, ?int $areaId = null, string $column = 'total'): float
    {
        return (float) Transaction::query()
            ->when($areaId, fn ($q) => $q->whereHas('customer', fn ($cq) => $cq->where('area_id', $areaId)))
            ->whereYear('transaction_date', now()->subMonths($offset)->year)
            ->whereMonth('transaction_date', now()->subMonths($offset)->month)
            ->sum($column);
    }

    private function monthCount(int $offset, ?int $areaId = null): int
    {
        return Transaction::query()
            ->when($areaId, fn ($q) => $q->whereHas('customer', fn ($cq) => $cq->where('area_id', $areaId)))
            ->whereYear('transaction_date', now()->subMonths($offset)->year)
            ->whereMonth('transaction_date', now()->subMonths($offset)->month)
            ->count();
    }

    private function deltaPercent(int $offset, ?int $areaId = null): float
    {
        $current = $this->monthTotal($offset, $areaId);
        $prev = $this->monthTotal($offset + 1, $areaId);

        return $prev > 0 ? round(($current - $prev) / $prev * 100, 1) : 0;
    }

    private function marginPercent(int $offset): float
    {
        $subtotal = (float) Transaction::whereYear('transaction_date', now()->subMonths($offset)->year)
            ->whereMonth('transaction_date', now()->subMonths($offset)->month)
            ->sum('subtotal');

        if ($subtotal <= 0) {
            return 0;
        }

        return round($this->monthTotal($offset, null, 'margin_estimate') / $subtotal * 100, 1);
    }
}
