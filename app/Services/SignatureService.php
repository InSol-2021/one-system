<?php

namespace App\Services;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SignatureService
{
    /**
     * Generate signature for outgoing requests
     */
    public function generateRequestSignature(
        string $method,
        string $path,
        string $body,
        string $clientSecret,
        ?int $timestamp = null
    ): array {
        $timestamp = $timestamp ?: time();

        $payload = implode('|', [
            strtoupper($method),
            $path,
            $timestamp,
            hash('sha256', $body)
        ]);

        $signature = hash_hmac('sha256', $payload, $clientSecret);

        return [
            'signature' => $signature,
            'timestamp' => $timestamp
        ];
    }

    /**
     * Validate incoming request signature
     */
    public function validateRequestSignature(
        string $method,
        string $path,
        string $body,
        string $signature,
        int $timestamp,
        string $clientSecret
    ): bool {
        $currentTime = time();
        if (abs($currentTime - $timestamp) > 300) {
            return false;
        }

        $expectedSignature = $this->generateRequestSignature(
            $method,
            $path,
            $body,
            $clientSecret,
            $timestamp
        )['signature'];

        return hash_equals($expectedSignature, $signature);
    }

    /**
     * Generate a new client secret
     */
    public function generateClientSecret(): string
    {
        return bin2hex(random_bytes(32));
    }

    /**
     * Create response headers with signature
     */
    public function createResponseHeaders(string $body, string $clientSecret): array
    {
        $timestamp = time();
        $signature = hash_hmac('sha256', $body . $timestamp, $clientSecret);

        return [
            'X-CAS-Response-Signature' => $signature,
            'X-CAS-Response-Timestamp' => $timestamp,
            'X-CAS-Server' => 'One-System-Server'
        ];
    }

    /**
     * Validate webhook signature from client
     */
    public function validateWebhookSignature(
        string $payload,
        string $signature,
        string $clientSecret
    ): bool {
        $expectedSignature = 'sha256=' . hash_hmac('sha256', $payload, $clientSecret);
        return hash_equals($expectedSignature, $signature);
    }

    /**
     * Generate SSO token signature
     */
    public function generateTokenSignature(array $tokenData, string $clientSecret): string
    {
        $payload = json_encode($tokenData, JSON_SORT_KEYS);
        return hash_hmac('sha256', $payload, $clientSecret);
    }

    /**
     * Make signed HTTP request to external service
     */
    public function makeSignedRequest(
        string $url,
        string $method,
        array $data,
        string $clientId,
        string $clientSecret
    ): array {
        $body = json_encode($data);
        $parsedUrl = parse_url($url);
        $path = $parsedUrl['path'] ?? '/';

        $signatureData = $this->generateRequestSignature(
            $method,
            $path,
            $body,
            $clientSecret
        );

        try {
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'X-CAS-Signature' => $signatureData['signature'],
                'X-CAS-Timestamp' => $signatureData['timestamp'],
                'X-CAS-Client-ID' => $clientId,
                'X-CAS-Server' => 'One-System-Server'
            ])->{strtolower($method)}($url, $data);

            return [
                'success' => $response->successful(),
                'status' => $response->status(),
                'data' => $response->json(),
                'headers' => $response->headers()
            ];

        } catch (\Exception $e) {
            Log::error('Signed request failed', [
                'url' => $url,
                'method' => $method,
                'client_id' => $clientId,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'status' => 500,
                'error' => $e->getMessage(),
                'data' => null
            ];
        }
    }
}
