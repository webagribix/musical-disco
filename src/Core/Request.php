<?php
declare(strict_types=1);

namespace App\Core;

class Request
{
    private array $params  = [];
    private ?array $jsonBody = null;

    public function __construct()
    {
        // Parse route parameters set by Router
    }

    public function method(): string
    {
        return strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET');
    }

    public function uri(): string
    {
        $uri = $_SERVER['REQUEST_URI'] ?? '/';
        $pos = strpos($uri, '?');
        return $pos !== false ? substr($uri, 0, $pos) : $uri;
    }

    public function path(): string
    {
        return $this->uri();
    }

    public function isApi(): bool
    {
        return str_starts_with($this->uri(), '/api/');
    }

    public function get(string $key, mixed $default = null): mixed
    {
        return $_GET[$key] ?? $default;
    }

    public function post(string $key, mixed $default = null): mixed
    {
        $body = $this->body();
        return $body[$key] ?? $_POST[$key] ?? $default;
    }

    public function input(string $key, mixed $default = null): mixed
    {
        $body = $this->body();
        return $body[$key] ?? $_POST[$key] ?? $_GET[$key] ?? $default;
    }

    public function all(): array
    {
        return array_merge($_GET, $this->body());
    }

    public function body(): array
    {
        if ($this->jsonBody !== null) {
            return $this->jsonBody;
        }

        $contentType = $_SERVER['CONTENT_TYPE'] ?? '';
        if (str_contains($contentType, 'application/json')) {
            $raw = file_get_contents('php://input');
            $this->jsonBody = json_decode($raw, true) ?? [];
        } else {
            $this->jsonBody = $_POST;
        }

        return $this->jsonBody;
    }

    public function setParams(array $params): void
    {
        $this->params = $params;
    }

    public function param(string $key, mixed $default = null): mixed
    {
        return $this->params[$key] ?? $default;
    }

    public function getParams(): array
    {
        return $this->params;
    }

    public function header(string $name): ?string
    {
        $key = 'HTTP_' . strtoupper(str_replace('-', '_', $name));
        if (isset($_SERVER[$key])) return $_SERVER[$key];
        if ($name === 'Authorization' && isset($_SERVER['Authorization'])) {
            return $_SERVER['Authorization'];
        }
        return $_SERVER['HTTP_AUTHORIZATION'] ?? null;
    }

    public function bearerToken(): ?string
    {
        $auth = $this->header('Authorization');
        if ($auth && preg_match('/Bearer\s+(.+)$/i', $auth, $m)) {
            return $m[1];
        }
        return null;
    }

    public function ip(): string
    {
        foreach (['HTTP_X_FORWARDED_FOR', 'HTTP_X_REAL_IP', 'REMOTE_ADDR'] as $key) {
            if (!empty($_SERVER[$key])) {
                return explode(',', $_SERVER[$key])[0];
            }
        }
        return '0.0.0.0';
    }

    public function isJson(): bool
    {
        return str_contains($_SERVER['CONTENT_TYPE'] ?? '', 'application/json');
    }

    public function page(): int
    {
        return max(1, (int) ($this->get('page', 1)));
    }

    public function perPage(int $default = 20): int
    {
        return min(100, max(1, (int) ($this->get('per_page', $default))));
    }

    public function sortField(string $default = 'created_at'): string
    {
        $sort    = $this->get('sort', $default);
        $allowed = preg_replace('/[^a-z0-9_]/i', '', $sort);
        return $allowed ?: $default;
    }

    public function sortDir(): string
    {
        return strtolower($this->get('dir', 'desc')) === 'asc' ? 'ASC' : 'DESC';
    }
}
