<?php
declare(strict_types=1);

namespace App\Core;

class Response
{
    private int    $statusCode = 200;
    private array  $headers    = [];
    private string $body       = '';

    public function status(int $code): static
    {
        $this->statusCode = $code;
        return $this;
    }

    public function header(string $name, string $value): static
    {
        $this->headers[$name] = $value;
        return $this;
    }

    public function json(mixed $data, int $status = 200): never
    {
        http_response_code($status);
        header('Content-Type: application/json; charset=utf-8');
        foreach ($this->headers as $name => $value) {
            header("$name: $value");
        }
        echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        exit;
    }

    public function success(mixed $data = null, array $meta = [], int $status = 200): never
    {
        $response = ['success' => true, 'data' => $data];
        if (!empty($meta)) {
            $response['meta'] = $meta;
        }
        $this->json($response, $status);
    }

    public function error(string $message, int $status = 400, array $errors = []): never
    {
        $response = ['success' => false, 'message' => $message];
        if (!empty($errors)) {
            $response['errors'] = $errors;
        }
        $this->json($response, $status);
    }

    public function paginated(array $result, array $extraData = []): never
    {
        $this->success(
            array_merge($extraData, ['items' => $result['data']]),
            [
                'page'      => $result['page'],
                'per_page'  => $result['per_page'],
                'total'     => $result['total'],
                'last_page' => $result['last_page'],
            ]
        );
    }

    public function redirect(string $url, int $status = 302): never
    {
        http_response_code($status);
        header("Location: $url");
        exit;
    }

    public function view(string $template, array $data = [], int $status = 200): never
    {
        http_response_code($status);
        foreach ($this->headers as $name => $value) {
            header("$name: $value");
        }
        View::render($template, $data);
        exit;
    }

    public function html(string $html, int $status = 200): never
    {
        http_response_code($status);
        header('Content-Type: text/html; charset=utf-8');
        echo $html;
        exit;
    }

    public function pdf(string $content, string $filename = 'report.pdf'): never
    {
        header('Content-Type: application/pdf');
        header("Content-Disposition: attachment; filename=\"$filename\"");
        echo $content;
        exit;
    }

    public function notFound(string $message = 'Not Found'): never
    {
        $this->error($message, 404);
    }

    public function unauthorized(string $message = 'Unauthorized'): never
    {
        $this->error($message, 401);
    }

    public function forbidden(string $message = 'Forbidden'): never
    {
        $this->error($message, 403);
    }

    public function unprocessable(array $errors, string $message = 'Validation failed'): never
    {
        $this->error($message, 422, $errors);
    }
}
