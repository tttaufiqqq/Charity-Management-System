<?php

namespace App\Services\Api;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

abstract class BaseApiService
{
    protected string $baseUrl;

    protected int $timeout = 10;

    protected int $retries = 3;

    protected string $serviceName;

    /**
     * Make an HTTP call to a microservice
     */
    protected function call(string $method, string $endpoint, array $data = [], array $options = [])
    {
        $url = rtrim($this->baseUrl, '/').'/'.ltrim($endpoint, '/');
        $circuitKey = "circuit:open:{$this->serviceName}";

        if ($this->isCircuitOpen($circuitKey)) {
            Log::warning("Circuit breaker open for {$this->serviceName}");
            throw new ServiceUnavailableException("{$this->serviceName} service is currently unavailable");
        }

        try {
            $response = Http::timeout($options['timeout'] ?? $this->timeout)
                ->retry($options['retries'] ?? $this->retries, 100)
                ->withHeaders($options['headers'] ?? [])
                ->$method($url, $data);

            if (! $response->successful()) {
                $this->recordFailure($circuitKey);
                Log::error("API call failed: {$method} {$url}", [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);
                throw new ApiCallException(
                    "API call failed: {$response->status()}",
                    $response->status()
                );
            }

            $this->recordSuccess($circuitKey);

            return $response->json();
        } catch (\Exception $e) {
            $this->recordFailure($circuitKey);
            Log::error("Service call exception: {$method} {$url}", [
                'exception' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * GET request with optional caching
     */
    protected function get(string $endpoint, array $query = [], int $cacheTtl = 0)
    {
        $cacheKey = $this->getCacheKey($endpoint, $query);

        if ($cacheTtl > 0 && Cache::has($cacheKey)) {
            return Cache::get($cacheKey);
        }

        $queryString = http_build_query($query);
        $fullEndpoint = $queryString ? "{$endpoint}?{$queryString}" : $endpoint;

        $result = $this->call('get', $fullEndpoint);

        if ($cacheTtl > 0) {
            Cache::put($cacheKey, $result, $cacheTtl);
        }

        return $result;
    }

    /**
     * POST request
     */
    protected function post(string $endpoint, array $data = [])
    {
        return $this->call('post', $endpoint, $data);
    }

    /**
     * PUT request
     */
    protected function put(string $endpoint, array $data = [])
    {
        return $this->call('put', $endpoint, $data);
    }

    /**
     * DELETE request
     */
    protected function delete(string $endpoint)
    {
        return $this->call('delete', $endpoint);
    }

    /**
     * Invalidate cache for a specific key pattern
     */
    protected function invalidateCache(string $pattern)
    {
        Cache::forget($this->getCacheKey($pattern));
    }

    /**
     * Generate cache key
     */
    protected function getCacheKey(string $endpoint, array $params = []): string
    {
        $paramString = ! empty($params) ? md5(json_encode($params)) : '';

        return "api_cache:{$this->serviceName}:{$endpoint}:{$paramString}";
    }

    /**
     * Check if circuit breaker is open
     */
    protected function isCircuitOpen(string $circuitKey): bool
    {
        return Cache::has($circuitKey);
    }

    /**
     * Record a successful call
     */
    protected function recordSuccess(string $circuitKey): void
    {
        Cache::forget($circuitKey);
        Cache::forget("{$circuitKey}:failures");
    }

    /**
     * Record a failed call
     */
    protected function recordFailure(string $circuitKey): void
    {
        $failures = Cache::get("{$circuitKey}:failures", 0);
        $failures++;

        Cache::put("{$circuitKey}:failures", $failures, 300);

        if ($failures >= 5) {
            Cache::put($circuitKey, true, 60);
            Log::warning("Circuit breaker opened for {$this->serviceName} after {$failures} failures");
        }
    }
}

class ServiceUnavailableException extends \Exception
{
    public function __construct($message = 'Service unavailable', $code = 503)
    {
        parent::__construct($message, $code);
    }
}

class ApiCallException extends \Exception
{
    //
}
