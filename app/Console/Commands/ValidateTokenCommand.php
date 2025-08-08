<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class ValidateTokenCommand extends Command
{
    protected $signature = 'cas:validate-token {token : The JWT token to validate}';
    protected $description = 'Validate and decode a JWT token';

    public function handle()
    {
        $token = $this->argument('token');
        
        try {
            $jwtSecret = env('JWT_SECRET', 'your-secret-key');
            
            $this->info("Validating JWT Token...");
            $this->info("=" . str_repeat("=", 50));
            
            // Decode the token
            $decoded = JWT::decode($token, new Key($jwtSecret, 'HS256'));
            
            $this->line("✓ Token is valid and properly signed");
            $this->line("");
            
            // Display token details
            $this->info("Token Claims:");
            $this->info("-" . str_repeat("-", 30));
            
            foreach ((array)$decoded as $key => $value) {
                if (is_array($value) || is_object($value)) {
                    $this->line("{$key}: " . json_encode($value, JSON_PRETTY_PRINT));
                } else {
                    $this->line("{$key}: {$value}");
                }
            }
            
            // Check expiration
            if (isset($decoded->exp)) {
                $expiration = \Carbon\Carbon::createFromTimestamp($decoded->exp);
                $now = \Carbon\Carbon::now();
                
                $this->line("");
                if ($expiration->isPast()) {
                    $this->error("⚠️  Token is EXPIRED (expired " . $expiration->diffForHumans() . ")");
                } else {
                    $this->line("✓ Token expires: " . $expiration->format('Y-m-d H:i:s') . " (" . $expiration->diffForHumans() . ")");
                }
            }
            
            return 0;
            
        } catch (\Firebase\JWT\ExpiredException $e) {
            $this->error("Token validation failed: Token has expired");
            return 1;
        } catch (\Firebase\JWT\SignatureInvalidException $e) {
            $this->error("Token validation failed: Invalid signature");
            return 1;
        } catch (\Exception $e) {
            $this->error("Token validation failed: " . $e->getMessage());
            return 1;
        }
    }
}