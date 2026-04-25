<?php
declare(strict_types=1);

namespace App\Core;

class App
{
    private Router $router;

    public function __construct()
    {
        $this->router = new Router();
        $this->registerRoutes();
    }

    private function registerRoutes(): void
    {
        $r = $this->router;

        // ── Web Auth ─────────────────────────────────────────────────────────
        $r->get('/login',    'App\\Controllers\\Web\\AuthWebController@showLogin');
        $r->post('/login',   'App\\Controllers\\Web\\AuthWebController@login');
        $r->post('/logout',  'App\\Controllers\\Web\\AuthWebController@logout');
        $r->get('/register', 'App\\Controllers\\Web\\AuthWebController@showRegister');
        $r->post('/register','App\\Controllers\\Web\\AuthWebController@register');

        // ── Web Dashboard ────────────────────────────────────────────────────
        $r->get('/',          'App\\Controllers\\Web\\DashboardController@index');
        $r->get('/dashboard', 'App\\Controllers\\Web\\DashboardController@index');

        // ── Web Batches ──────────────────────────────────────────────────────
        $r->get('/batches',           'App\\Controllers\\Web\\BatchWebController@index');
        $r->get('/batches/create',    'App\\Controllers\\Web\\BatchWebController@create');
        $r->post('/batches',          'App\\Controllers\\Web\\BatchWebController@store');
        $r->get('/batches/{id}',      'App\\Controllers\\Web\\BatchWebController@show');
        $r->get('/batches/{id}/edit', 'App\\Controllers\\Web\\BatchWebController@edit');
        $r->post('/batches/{id}',     'App\\Controllers\\Web\\BatchWebController@update');

        // ── Web Health ───────────────────────────────────────────────────────
        $r->get('/health/vaccinations', 'App\\Controllers\\Web\\HealthWebController@vaccinations');
        $r->get('/health/medications',  'App\\Controllers\\Web\\HealthWebController@medications');
        $r->get('/health/alerts',       'App\\Controllers\\Web\\HealthWebController@alerts');

        // ── Web Feed ─────────────────────────────────────────────────────────
        $r->get('/feed',           'App\\Controllers\\Web\\FeedWebController@index');
        $r->get('/feed/inventory', 'App\\Controllers\\Web\\FeedWebController@inventory');

        // ── Web Production ───────────────────────────────────────────────────
        $r->get('/production/eggs',    'App\\Controllers\\Web\\ProductionWebController@eggs');
        $r->get('/production/weights', 'App\\Controllers\\Web\\ProductionWebController@weights');

        // ── Web Financial ────────────────────────────────────────────────────
        $r->get('/financial/expenses', 'App\\Controllers\\Web\\FinancialWebController@expenses');
        $r->get('/financial/sales',    'App\\Controllers\\Web\\FinancialWebController@sales');
        $r->get('/financial/pl',       'App\\Controllers\\Web\\FinancialWebController@plReport');

        // ── Web Tasks ────────────────────────────────────────────────────────
        $r->get('/tasks',       'App\\Controllers\\Web\\TaskWebController@today');
        $r->post('/tasks/{id}', 'App\\Controllers\\Web\\TaskWebController@updateStatus');

        // ── Web Users ────────────────────────────────────────────────────────
        $r->get('/users',       'App\\Controllers\\Web\\UserWebController@index');
        $r->post('/users',      'App\\Controllers\\Web\\UserWebController@store');
        $r->post('/users/{id}', 'App\\Controllers\\Web\\UserWebController@update');

        // ── API v1 Auth ──────────────────────────────────────────────────────
        $r->post('/api/v1/auth/login',   'App\\Controllers\\Api\\V1\\AuthController@login');
        $r->post('/api/v1/auth/logout',  'App\\Controllers\\Api\\V1\\AuthController@logout');
        $r->get('/api/v1/auth/me',       'App\\Controllers\\Api\\V1\\AuthController@me');

        // ── API v1 Batches ───────────────────────────────────────────────────
        $r->get('/api/v1/batches',                     'App\\Controllers\\Api\\V1\\BatchController@index');
        $r->post('/api/v1/batches',                    'App\\Controllers\\Api\\V1\\BatchController@store');
        $r->get('/api/v1/batches/{id}',                'App\\Controllers\\Api\\V1\\BatchController@show');
        $r->put('/api/v1/batches/{id}',                'App\\Controllers\\Api\\V1\\BatchController@update');
        $r->patch('/api/v1/batches/{id}',              'App\\Controllers\\Api\\V1\\BatchController@update');
        $r->delete('/api/v1/batches/{id}',             'App\\Controllers\\Api\\V1\\BatchController@destroy');
        $r->post('/api/v1/batches/{id}/graduate',      'App\\Controllers\\Api\\V1\\BatchController@graduate');
        $r->get('/api/v1/batches/{id}/live-count',     'App\\Controllers\\Api\\V1\\BatchController@liveCount');
        $r->get('/api/v1/batches/{id}/pl',             'App\\Controllers\\Api\\V1\\BatchController@pl');
        $r->get('/api/v1/batches/{id}/fcr',            'App\\Controllers\\Api\\V1\\BatchController@fcr');

        // ── API v1 Houses ────────────────────────────────────────────────────
        $r->get('/api/v1/houses',        'App\\Controllers\\Api\\V1\\HouseController@index');
        $r->post('/api/v1/houses',       'App\\Controllers\\Api\\V1\\HouseController@store');
        $r->get('/api/v1/houses/{id}',   'App\\Controllers\\Api\\V1\\HouseController@show');
        $r->put('/api/v1/houses/{id}',   'App\\Controllers\\Api\\V1\\HouseController@update');
        $r->delete('/api/v1/houses/{id}','App\\Controllers\\Api\\V1\\HouseController@destroy');

        // ── API v1 Mortality ─────────────────────────────────────────────────
        $r->get('/api/v1/mortality',       'App\\Controllers\\Api\\V1\\MortalityController@index');
        $r->post('/api/v1/mortality',      'App\\Controllers\\Api\\V1\\MortalityController@store');
        $r->get('/api/v1/mortality/{id}',  'App\\Controllers\\Api\\V1\\MortalityController@show');
        $r->delete('/api/v1/mortality/{id}','App\\Controllers\\Api\\V1\\MortalityController@destroy');

        // ── API v1 Vaccination ───────────────────────────────────────────────
        $r->get('/api/v1/vaccinations',       'App\\Controllers\\Api\\V1\\VaccinationController@index');
        $r->post('/api/v1/vaccinations',      'App\\Controllers\\Api\\V1\\VaccinationController@store');
        $r->get('/api/v1/vaccinations/{id}',  'App\\Controllers\\Api\\V1\\VaccinationController@show');
        $r->put('/api/v1/vaccinations/{id}',  'App\\Controllers\\Api\\V1\\VaccinationController@update');
        $r->delete('/api/v1/vaccinations/{id}','App\\Controllers\\Api\\V1\\VaccinationController@destroy');

        // ── API v1 Medication ────────────────────────────────────────────────
        $r->get('/api/v1/medications',       'App\\Controllers\\Api\\V1\\MedicationController@index');
        $r->post('/api/v1/medications',      'App\\Controllers\\Api\\V1\\MedicationController@store');
        $r->get('/api/v1/medications/{id}',  'App\\Controllers\\Api\\V1\\MedicationController@show');
        $r->delete('/api/v1/medications/{id}','App\\Controllers\\Api\\V1\\MedicationController@destroy');

        // ── API v1 Vet Visits ────────────────────────────────────────────────
        $r->get('/api/v1/vet-visits',       'App\\Controllers\\Api\\V1\\VetVisitController@index');
        $r->post('/api/v1/vet-visits',      'App\\Controllers\\Api\\V1\\VetVisitController@store');
        $r->get('/api/v1/vet-visits/{id}',  'App\\Controllers\\Api\\V1\\VetVisitController@show');
        $r->delete('/api/v1/vet-visits/{id}','App\\Controllers\\Api\\V1\\VetVisitController@destroy');

        // ── API v1 Feed Inventory ────────────────────────────────────────────
        $r->get('/api/v1/feed-inventory',       'App\\Controllers\\Api\\V1\\FeedInventoryController@index');
        $r->post('/api/v1/feed-inventory',      'App\\Controllers\\Api\\V1\\FeedInventoryController@store');
        $r->get('/api/v1/feed-inventory/{id}',  'App\\Controllers\\Api\\V1\\FeedInventoryController@show');
        $r->put('/api/v1/feed-inventory/{id}',  'App\\Controllers\\Api\\V1\\FeedInventoryController@update');
        $r->delete('/api/v1/feed-inventory/{id}','App\\Controllers\\Api\\V1\\FeedInventoryController@destroy');

        // ── API v1 Feed Consumption ──────────────────────────────────────────
        $r->get('/api/v1/feed-consumption',       'App\\Controllers\\Api\\V1\\FeedConsumptionController@index');
        $r->post('/api/v1/feed-consumption',      'App\\Controllers\\Api\\V1\\FeedConsumptionController@store');
        $r->get('/api/v1/feed-consumption/{id}',  'App\\Controllers\\Api\\V1\\FeedConsumptionController@show');
        $r->delete('/api/v1/feed-consumption/{id}','App\\Controllers\\Api\\V1\\FeedConsumptionController@destroy');

        // ── API v1 Egg Collection ────────────────────────────────────────────
        $r->get('/api/v1/egg-collection',       'App\\Controllers\\Api\\V1\\EggCollectionController@index');
        $r->post('/api/v1/egg-collection',      'App\\Controllers\\Api\\V1\\EggCollectionController@store');
        $r->get('/api/v1/egg-collection/{id}',  'App\\Controllers\\Api\\V1\\EggCollectionController@show');
        $r->delete('/api/v1/egg-collection/{id}','App\\Controllers\\Api\\V1\\EggCollectionController@destroy');

        // ── API v1 Weight Log ────────────────────────────────────────────────
        $r->get('/api/v1/weight-logs',       'App\\Controllers\\Api\\V1\\WeightLogController@index');
        $r->post('/api/v1/weight-logs',      'App\\Controllers\\Api\\V1\\WeightLogController@store');
        $r->get('/api/v1/weight-logs/{id}',  'App\\Controllers\\Api\\V1\\WeightLogController@show');
        $r->delete('/api/v1/weight-logs/{id}','App\\Controllers\\Api\\V1\\WeightLogController@destroy');

        // ── API v1 Expenses ──────────────────────────────────────────────────
        $r->get('/api/v1/expenses',       'App\\Controllers\\Api\\V1\\ExpenseController@index');
        $r->post('/api/v1/expenses',      'App\\Controllers\\Api\\V1\\ExpenseController@store');
        $r->get('/api/v1/expenses/{id}',  'App\\Controllers\\Api\\V1\\ExpenseController@show');
        $r->put('/api/v1/expenses/{id}',  'App\\Controllers\\Api\\V1\\ExpenseController@update');
        $r->delete('/api/v1/expenses/{id}','App\\Controllers\\Api\\V1\\ExpenseController@destroy');

        // ── API v1 Sales ─────────────────────────────────────────────────────
        $r->get('/api/v1/sales',       'App\\Controllers\\Api\\V1\\SaleController@index');
        $r->post('/api/v1/sales',      'App\\Controllers\\Api\\V1\\SaleController@store');
        $r->get('/api/v1/sales/{id}',  'App\\Controllers\\Api\\V1\\SaleController@show');
        $r->put('/api/v1/sales/{id}',  'App\\Controllers\\Api\\V1\\SaleController@update');
        $r->delete('/api/v1/sales/{id}','App\\Controllers\\Api\\V1\\SaleController@destroy');

        // ── API v1 Alerts ────────────────────────────────────────────────────
        $r->get('/api/v1/alerts',            'App\\Controllers\\Api\\V1\\AlertController@index');
        $r->get('/api/v1/alerts/{id}',       'App\\Controllers\\Api\\V1\\AlertController@show');
        $r->patch('/api/v1/alerts/{id}/read','App\\Controllers\\Api\\V1\\AlertController@markRead');
        $r->delete('/api/v1/alerts/{id}',    'App\\Controllers\\Api\\V1\\AlertController@destroy');

        // ── API v1 Environment ───────────────────────────────────────────────
        $r->get('/api/v1/environment',       'App\\Controllers\\Api\\V1\\EnvironmentController@index');
        $r->post('/api/v1/environment',      'App\\Controllers\\Api\\V1\\EnvironmentController@store');
        $r->get('/api/v1/environment/{id}',  'App\\Controllers\\Api\\V1\\EnvironmentController@show');

        // ── API v1 Water Consumption ─────────────────────────────────────────
        $r->get('/api/v1/water-consumption',       'App\\Controllers\\Api\\V1\\WaterConsumptionController@index');
        $r->post('/api/v1/water-consumption',      'App\\Controllers\\Api\\V1\\WaterConsumptionController@store');
        $r->get('/api/v1/water-consumption/{id}',  'App\\Controllers\\Api\\V1\\WaterConsumptionController@show');

        // ── API v1 Breeds ────────────────────────────────────────────────────
        $r->get('/api/v1/breeds',       'App\\Controllers\\Api\\V1\\BreedController@index');
        $r->post('/api/v1/breeds',      'App\\Controllers\\Api\\V1\\BreedController@store');
        $r->get('/api/v1/breeds/{id}',  'App\\Controllers\\Api\\V1\\BreedController@show');
        $r->put('/api/v1/breeds/{id}',  'App\\Controllers\\Api\\V1\\BreedController@update');
        $r->delete('/api/v1/breeds/{id}','App\\Controllers\\Api\\V1\\BreedController@destroy');

        // ── API v1 Tasks ─────────────────────────────────────────────────────
        $r->get('/api/v1/tasks',               'App\\Controllers\\Api\\V1\\TaskController@index');
        $r->post('/api/v1/tasks',              'App\\Controllers\\Api\\V1\\TaskController@store');
        $r->get('/api/v1/tasks/{id}',          'App\\Controllers\\Api\\V1\\TaskController@show');
        $r->patch('/api/v1/tasks/{id}/status', 'App\\Controllers\\Api\\V1\\TaskController@updateStatus');
        $r->delete('/api/v1/tasks/{id}',       'App\\Controllers\\Api\\V1\\TaskController@destroy');

        // ── API v1 Suppliers ─────────────────────────────────────────────────
        $r->get('/api/v1/suppliers',       'App\\Controllers\\Api\\V1\\SupplierController@index');
        $r->post('/api/v1/suppliers',      'App\\Controllers\\Api\\V1\\SupplierController@store');
        $r->get('/api/v1/suppliers/{id}',  'App\\Controllers\\Api\\V1\\SupplierController@show');
        $r->put('/api/v1/suppliers/{id}',  'App\\Controllers\\Api\\V1\\SupplierController@update');
        $r->delete('/api/v1/suppliers/{id}','App\\Controllers\\Api\\V1\\SupplierController@destroy');

        // ── API v1 Customers ─────────────────────────────────────────────────
        $r->get('/api/v1/customers',       'App\\Controllers\\Api\\V1\\CustomerController@index');
        $r->post('/api/v1/customers',      'App\\Controllers\\Api\\V1\\CustomerController@store');
        $r->get('/api/v1/customers/{id}',  'App\\Controllers\\Api\\V1\\CustomerController@show');
        $r->put('/api/v1/customers/{id}',  'App\\Controllers\\Api\\V1\\CustomerController@update');
        $r->delete('/api/v1/customers/{id}','App\\Controllers\\Api\\V1\\CustomerController@destroy');

        // ── API v1 Purchases ─────────────────────────────────────────────────
        $r->get('/api/v1/purchases',       'App\\Controllers\\Api\\V1\\PurchaseController@index');
        $r->post('/api/v1/purchases',      'App\\Controllers\\Api\\V1\\PurchaseController@store');
        $r->get('/api/v1/purchases/{id}',  'App\\Controllers\\Api\\V1\\PurchaseController@show');
        $r->delete('/api/v1/purchases/{id}','App\\Controllers\\Api\\V1\\PurchaseController@destroy');

        // ── API v1 Reports ───────────────────────────────────────────────────
        $r->get('/api/v1/reports/dashboard',       'App\\Controllers\\Api\\V1\\ReportController@dashboard');
        $r->get('/api/v1/reports/batch/{id}/pl',   'App\\Controllers\\Api\\V1\\ReportController@batchPl');
        $r->get('/api/v1/reports/batch/{id}/pdf',  'App\\Controllers\\Api\\V1\\ReportController@batchPdf');

        // ── API v1 Sync ──────────────────────────────────────────────────────
        $r->post('/api/v1/sync', 'App\\Controllers\\Api\\V1\\SyncController@sync');
    }

    public function run(): void
    {
        Session::start();

        // Share common data with views
        $user = Auth::user();
        View::share('currentUser', $user);
        View::share('appName', APP_NAME);
        View::share('currency', APP_CURRENCY);

        $request  = new Request();
        $response = new Response();

        try {
            $this->router->dispatch($request, $response);
        } catch (\Throwable $e) {
            if ($request->isApi()) {
                $response->error(
                    APP_ENV === 'production' ? 'Internal server error' : $e->getMessage(),
                    500
                );
            }
            http_response_code(500);
            echo '<h1>500 Internal Server Error</h1>';
            if (($_ENV['APP_ENV'] ?? 'production') !== 'production') {
                echo '<pre>' . htmlspecialchars($e->getMessage() . "\n" . $e->getTraceAsString()) . '</pre>';
            }
        }
    }
}
