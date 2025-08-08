<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ClientSystem;

class ClientSystemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $clientSystems = [
            [
                'name' => 'Laravel Customer Portal',
                'description' => 'Laravel-based customer portal with e-commerce features',
                'callback_url' => 'http://localhost:9000/auth/sso/callback',
                'client_id' => 'client_' . bin2hex(random_bytes(8)),
                'client_secret' => 'laravel-portal-secret',
                'allowed_scopes' => json_encode(['read', 'write', 'orders']),
                'is_active' => true,
                'credentials_viewed' => false,
                'server_config' => json_encode([
                    'domain' => 'http://localhost:9000',
                    'webhook_secret' => bin2hex(random_bytes(16)),
                    'signature_validation_enabled' => true
                ]),
            ]
        ];

        foreach ($clientSystems as $systemData) {
            ClientSystem::updateOrCreate(
                ['name' => $systemData['name']],
                $systemData
            );
        }

        $this->command->info('✅ Created ' . count($clientSystems) . ' client systems with clean migration structure');
    }
}
