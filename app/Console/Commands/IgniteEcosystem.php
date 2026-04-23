<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use App\Models\Tenant;

class IgniteEcosystem extends Command
{
    protected $signature = 'antigravity:ignite {--force : Force the ignition without confirmation}';
    protected $description = 'Ignite the entire Antigravity SaaS ecosystem with demo data';

    public function handle()
    {
        $this->output->title('ANTIGRAVITY SYSTEM IGNITION');

        $this->info('🚀 Preparing for launch...');

        if (!$this->option('force') && !$this->confirm('This will RESET your database and delete all existing data. Continue?')) {
            $this->error('Ignition aborted.');
            return;
        }

        // 1. Core Database Reset
        $this->info('Resetting Central Database...');
        Artisan::call('migrate:fresh', ['--force' => true]);

        // 1.1 Deep Clean Tenant Schemas
        $tenantId = 'bdt-restaurant';
        $domain = 'bdt-restaurant.localhost';
        $dbName = "tenant" . $tenantId;

        $this->info("Dropping legacy demo database '{$dbName}'...");
        try {
            \Illuminate\Support\Facades\DB::statement("DROP DATABASE IF EXISTS `{$dbName}`");
        } catch (\Exception $e) {
            $this->warn("Could not drop '{$dbName}', it might not exist yet.");
        }

        // 2. Demo Tenant Creation
        $this->info('Seeding Demo Restaurant (BDT)...');
        Artisan::call('tenant:create', [
            'id' => $tenantId,
            'domain' => $domain,
        ]);

        // 3. System Cache Warm-up
        $this->info('Warming Up AI Ecosystem...');
        Artisan::call('cache:clear');
        Artisan::call('view:cache');

        $this->newLine();
        $this->info('✅ ALL SYSTEMS GREEN. ANTIGRAVITY IS LIVE.');
        $this->newLine();
        
        $this->table(
            ['Channel', 'URL', 'Credentials'],
            [
                ['Master Landing Page', 'http://localhost:8000', 'N/A'],
                ['Demo Tenant Admin', "http://{$domain}:8000/admin", "admin@{$tenantId}.com / password"],
                ['Direct QR Menu', "http://{$domain}:8000/guest-order/1", "Scan to Order"],
            ]
        );

        $this->newLine();
        $this->info('Happy Hosting! 🚀');
    }
}
