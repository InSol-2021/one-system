<?php

namespace Database\Seeders;

use App\Models\ClientSystem;
use Illuminate\Database\Seeder;

/**
 * Registers the ten example applications under examples/ as CAS client systems
 * so the unified single-server demo works out of the box.
 *
 * DEMO ONLY: the default client secrets below are well-known placeholders. The
 * same plaintext values are passed to each sample via examples/docker-compose.yml
 * (env CAS_CLIENT_SECRET); the CAS server stores them HASHED (ClientSystem mutator)
 * and verifies with hash_equals/Hash::check. For any real deployment, override the
 * *_CAS_SECRET env vars (and the matching sample env) with strong, unique values.
 *
 * Run on its own:  php artisan db:seed --class=Database\\Seeders\\SampleClientsSeeder
 */
class SampleClientsSeeder extends Seeder
{
    public function run(): void
    {
        // Host the browser is redirected back to. Override for a remote server,
        // e.g. SAMPLE_BASE_HOST=https://demo.example.com
        $host = rtrim(env('SAMPLE_BASE_HOST', 'http://localhost'), '/');

        // [key, Display name, port, callback path]
        $samples = [
            ['laravel',    'Laravel Sample',    9101, '/callback'],
            ['nodejs',     'Node.js Sample',    9102, '/callback'],
            ['javascript', 'JavaScript Sample', 9103, '/cas/callback'],
            ['python',     'Python Sample',     9104, '/callback'],
            ['java',       'Java Sample',       9105, '/callback'],
            ['dotnet',     '.NET Sample',       9106, '/cas/callback'],
            ['react',      'React Sample',      9107, '/'],
            ['nextjs',     'Next.js Sample',    9108, '/api/cas/callback'],
            ['vue',        'Vue Sample',        9109, '/auth/callback'],
            ['angular',    'Angular Sample',    9110, '/cas/callback'],
            ['rust',       'Rust Sample',       9111, '/cas/callback'],
        ];

        foreach ($samples as [$key, $name, $port, $path]) {
            $clientId = "{$key}-sample";
            $secretEnv = strtoupper($key) . '_CAS_SECRET';
            $secret = env($secretEnv, "demo-secret-{$key}");
            // Most samples expose the credential-validation endpoint at /login;
            // Next.js (app router) exposes it at /api/login.
            $loginPath = $key === 'nextjs' ? '/api/login' : '/login';

            ClientSystem::updateOrCreate(
                ['client_id' => $clientId],
                [
                    'name' => $name,
                    'description' => "One System CAS example application ({$key}).",
                    'callback_url' => "{$host}:{$port}{$path}",
                    // Stored hashed at rest by the ClientSystem mutator.
                    'client_secret' => $secret,
                    'allowed_scopes' => ['read', 'profile'],
                    'is_active' => true,
                    'credentials_viewed' => true,
                    'server_config' => [
                        'domain' => "{$host}:{$port}",
                        // Reachable from the CAS container over the shared docker network
                        // (the public host IP is not routable from containers here). Used by
                        // ClientCredentialValidator for the "Connect system" password check.
                        'validation_url' => "http://os-sample-{$key}:{$port}",
                        'login_path' => $loginPath,
                        'signature_validation_enabled' => false,
                    ],
                ]
            );
        }

        $this->command->info('✅ Registered ' . count($samples) . ' example client systems (ports 9101-9111).');
        $this->command->warn('⚠  Demo secrets are placeholders — override *_CAS_SECRET for real deployments.');
    }
}
