<?php

namespace Controller;

class VitamProxyController
{
    private string $vitamUrl;
    private string $vitamToken;

    public function __construct()
    {
        $this->vitamUrl   = rtrim(getenv('VITAM_URL') ?: ($_ENV['VITAM_URL'] ?? ''), '/');
        $this->vitamToken = getenv('VITAM_TOKEN') ?: ($_ENV['VITAM_TOKEN'] ?? '');
        
        if (empty($this->vitamUrl) || empty($this->vitamToken)) {
            error_log("VitamProxy: Configuration manquante - URL: " . ($this->vitamUrl ?: 'vide') . " Token: " . (empty($this->vitamToken) ? 'vide' : 'défini'));
        }
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

        // 3. Utilisation de cURL pour gérer les gros fichiers
        $ch = curl_init($url);
        
        // Configuration de base
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 600);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        
        // Headers
        $headers = [
            'Authorization: Bearer ' . $this->vitamToken,
        ];
        
        if (!empty($_SERVER['HTTP_X_FILE_NAME'])) {
            $headers[] = 'X-File-Name: ' . $_SERVER['HTTP_X_FILE_NAME'];
        }
        if (!empty($_SERVER['HTTP_X_FORCE_REPLACE'])) {
            $headers[] = 'X-Force-Replace: ' . $_SERVER['HTTP_X_FORCE_REPLACE'];
        }
        if (!empty($_SERVER['HTTP_CONTENT_RANGE'])) {
            $headers[] = 'Content-Range: ' . $_SERVER['HTTP_CONTENT_RANGE'];
        }
        
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        
        // Méthode et body
        if ($method === 'PUT') {
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
            $putData = fopen('php://input', 'r');
            curl_setopt($ch, CURLOPT_INFILE, $putData);
            curl_setopt($ch, CURLOPT_INFILESIZE, (int)$_SERVER['CONTENT_LENGTH']);
        } elseif ($method === 'POST') {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, file_get_contents('php://input'));
        } elseif ($method !== 'GET') {
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
        }
        
        // Exécution
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        
        if (curl_errno($ch)) {
            $error = curl_error($ch);
            error_log("VitamProxy cURL Error: " . $error);
            if (isset($putData)) fclose($putData);
            $this->error(502, 'Erreur de connexion à l\'API Vitam: ' . $error);
            return;
        }
        
        if (isset($putData)) fclose($putData);
        
        // 4. Retour de la réponse au client
        http_response_code($httpCode);
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
            'HTTP_CONTENT_RANGE'    => 'Content-Range',
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
        $httpOptions = [
            'method'        => $method,
            'header'        => $headers,
            'ignore_errors' => true,
            'timeout'       => 300,
        ];

        if (in_array($method, ['POST', 'PUT'], true)) {
            $httpOptions['content'] = file_get_contents('php://input');
        }

        $options = [
            'http' => $httpOptions,
            'https' => $httpOptions, // Important pour HTTPS
            'ssl' => [
                'verify_peer'       => false,
                'verify_peer_name'  => false,
            ]
        ];

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
