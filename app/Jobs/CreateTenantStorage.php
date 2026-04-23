<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\Tenant;

class CreateTenantStorage implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $tenant;

    public function __construct(Tenant $tenant)
    {
        $this->tenant = $tenant;
    }

    public function handle(): void
    {
        $tenantId = $this->tenant->id;
        $basePath = storage_path("tenant{$tenantId}");

        $directories = [
            "{$basePath}/framework/cache/data",
            "{$basePath}/framework/views",
            "{$basePath}/framework/sessions",
            "{$basePath}/app/private",
            "{$basePath}/app/public",
            "{$basePath}/logs",
        ];

        foreach ($directories as $directory) {
            if (!file_exists($directory)) {
                mkdir($directory, 0755, true);
            }
        }
    }
}
