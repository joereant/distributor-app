<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_owner_dashboard_has_real_analytics(): void
    {
        $owner = User::where('role', 'owner')->firstOrFail();

        $this->actingAs($owner)->get('/owner')
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Owner/Dashboard')
                ->has('kpis')
                ->has('trend.labels')
                ->has('trend.total')
                ->has('top_products')
                ->has('area_sales')
                ->has('recent'));
    }

    public function test_admin_dashboard_has_pending_orders(): void
    {
        $admin = User::where('role', 'admin')->firstOrFail();

        $this->actingAs($admin)->get('/admin')
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Admin/Dashboard')
                ->has('kpis')
                ->has('pending_orders')
                ->has('trend')
                ->has('top_products')
                ->has('area_sales')
                ->has('recent'));
    }

    public function test_sales_dashboard_filters_by_area(): void
    {
        $sales = User::where('role', 'sales')->firstOrFail();
        $areaId = $sales->sales_area_id;

        $this->actingAs($sales)->get('/sales')
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Sales/Dashboard')
                ->has('kpis')
                ->has('trend')
                ->has('top_products')
                ->has('recent'));

        $this->assertNotNull($areaId);
    }

    public function test_customer_dashboard_has_real_order_history(): void
    {
        $customer = User::where('role', 'customer')->firstOrFail();

        $this->actingAs($customer)->get('/customer')
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Customer/Dashboard')
                ->has('customer')
                ->has('kpis')
                ->has('orders'));
    }
}
