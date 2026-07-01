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
                'callback_url' => 'http://127.0.0.1:9001/cas/callback',
                'client_id' => 'laravel_customer_portal_id',
                // Generated per seed run and stored hashed by the model mutator.
                'client_secret' => bin2hex(random_bytes(32)),
                'webhook_secret' => bin2hex(random_bytes(32)),
                'allowed_scopes' => ['read', 'write', 'orders'],
                'is_active' => true,
                'credentials_viewed' => true,
                'server_config' => [
                    'domain' => 'http://127.0.0.1:9001',
                    'signature_validation_enabled' => true,
                ],
            ]
        ];

        foreach ($clientSystems as $systemData) {
            // Persist via the model so the client_secret/webhook_secret mutators
            // hash the values at rest. Plaintext is never stored.
            ClientSystem::updateOrCreate(
                ['name' => $systemData['name']],
                $systemData
            );
        }

        $this->command->info('✅ Created ' . count($clientSystems) . ' client systems with clean migration structure');
    }
}
