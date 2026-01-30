<?php

namespace Controller;

class VitamProxyController
{
    private string $vitamUrl;
    private string $vitamToken;

    public function __construct()
    {
        $this->vitamUrl   = rtrim(getenv('VITAM_URL'), '/');
        $this->vitamToken = getenv('VITAM_TOKEN');
    }

    /**
     * Relaye la requête HTTP vers l'API Vitam
     */
    public function relay(): void
    {
        // 1. Vérification de l'action
        $action = $_GET['action'] ?? null;
        if (!$action) {
            $this->error(400, 'Action manquante');
            return;
        }

        // 2. Préparation de la requête
        $method = $_SERVER['REQUEST_METHOD'];
        $url    = "{$this->vitamUrl}/index.php?action=" . urlencode($action);

        $headers = $this->buildHeaders();
        $options = $this->buildHttpOptions($method, $headers);

        // 3. Appel vers Vitam
        $context = stream_context_create($options);
        $response = file_get_contents($url, false, $context);

        // 4. Retour de la réponse au client
        $statusCode = $this->getHttpStatusCode($http_response_header ?? []);
        http_response_code($statusCode);
        header('Content-Type: application/json');
        echo $response;
    }

    /**
     * Construit les headers HTTP
     */
    private function buildHeaders(): array
    {
        $headers = [
            'Authorization: Bearer ' . $this->vitamToken,
        ];

        $optionalHeaders = [
            'HTTP_X_FILE_NAME'      => 'X-File-Name',
            'HTTP_X_FORCE_REPLACE'  => 'X-Force-Replace',
            'HTTP_CONTENT_RANGE'   => 'Content-Range',
        ];

        foreach ($optionalHeaders as $serverKey => $headerName) {
            if (!empty($_SERVER[$serverKey])) {
                $headers[] = $headerName . ': ' . $_SERVER[$serverKey];
            }
        }

        return $headers;
    }

    /**
     * Options HTTP pour stream_context
     */
    private function buildHttpOptions(string $method, array $headers): array
    {
        $options = [
            'http' => [
                'method'        => $method,
                'header'        => $headers,
                'ignore_errors' => true,
            ]
        ];

        if (in_array($method, ['POST', 'PUT'], true)) {
            $options['http']['content'] = file_get_contents('php://input');
        }

        return $options;
    }

    /**
     * Extrait le code HTTP de la réponse
     */
    private function getHttpStatusCode(array $headers): int
    {
        foreach ($headers as $header) {
            if (preg_match('#HTTP/\d+\.\d+\s+(\d+)#', $header, $match)) {
                return (int) $match[1];
            }
        }
        return 500;
    }

    private function error(int $code, string $message): void
    {
        http_response_code($code);
        header('Content-Type: application/json');
        echo json_encode(['error' => $message]);
    }
}
