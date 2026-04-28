<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$tenants = \App\Models\Tenant::all();
foreach ($tenants as $tenant) {
    tenancy()->initialize($tenant);
    echo "Generating for tenant {$tenant->id}...\n";
    \Illuminate\Support\Facades\Artisan::call('shield:generate', ['--all' => true]);
    echo \Illuminate\Support\Facades\Artisan::output();
    tenancy()->end();
}
echo "Done.\n";
