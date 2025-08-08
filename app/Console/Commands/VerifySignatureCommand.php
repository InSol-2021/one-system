<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class VerifySignatureCommand extends Command
{
    protected $signature = 'cas:verify-signature {payload : The payload to verify} {signature : The HMAC signature} {--secret= : Custom secret key}';
    protected $description = 'Verify HMAC-SHA256 signature for payload validation';

    public function handle()
    {
        $payload = $this->argument('payload');
        $providedSignature = $this->argument('signature');
        $customSecret = $this->option('secret');
        
        try {
            $this->info("Verifying HMAC-SHA256 Signature...");
            $this->info("=" . str_repeat("=", 50));
            
            // Use custom secret or default
            $secret = $customSecret ?: env('JWT_SECRET', 'your-secret-key');
            
            // Calculate expected signature
            $expectedSignature = hash_hmac('sha256', $payload, $secret);
            
            $this->line("Payload: {$payload}");
            $this->line("Provided signature: {$providedSignature}");
            $this->line("Expected signature: {$expectedSignature}");
            $this->line("");
            
            // Verify signature
            if (hash_equals($expectedSignature, $providedSignature)) {
                $this->info("✓ Signature is VALID");
                
                // Try to decode if it looks like JSON
                if (str_starts_with($payload, '{') && str_ends_with($payload, '}')) {
                    try {
                        $decoded = json_decode($payload, true);
                        if (json_last_error() === JSON_ERROR_NONE) {
                            $this->line("");
                            $this->info("Decoded payload:");
                            $this->line(json_encode($decoded, JSON_PRETTY_PRINT));
                        }
                    } catch (\Exception $e) {
                        // Ignore JSON decode errors
                    }
                }
                
                return 0;
            } else {
                $this->error("✗ Signature is INVALID");
                
                // Check common issues
                $this->line("");
                $this->info("Troubleshooting:");
                
                // Check if signature is hex
                if (!ctype_xdigit($providedSignature)) {
                    $this->line("• Provided signature is not hexadecimal");
                }
                
                // Check signature length
                if (strlen($providedSignature) !== 64) {
                    $this->line("• Expected signature length: 64 characters, got: " . strlen($providedSignature));
                }
                
                // Check for common prefixes
                if (str_starts_with($providedSignature, 'sha256=')) {
                    $cleanSignature = substr($providedSignature, 7);
                    if (hash_equals($expectedSignature, $cleanSignature)) {
                        $this->info("✓ Signature is valid after removing 'sha256=' prefix");
                        return 0;
                    }
                }
                
                return 1;
            }
            
        } catch (\Exception $e) {
            $this->error("Error verifying signature: " . $e->getMessage());
            return 1;
        }
    }
}