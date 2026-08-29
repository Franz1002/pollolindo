<?php
class IAClienteCaja
{
    private $baseUrl;

    public function __construct($baseUrl = "http://127.0.0.1:5001")
    {
        $this->baseUrl = $baseUrl;
    }

    private function post($path, array $payload)
    {
        $ch = curl_init($this->baseUrl . $path);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 3); // no bloquea el cierre de caja si la IA está caída
        $response = curl_exec($ch);
        $error = curl_errno($ch);
        curl_close($ch);

        if ($error || $response === false) {
            return null;
        }
        return json_decode($response, true);
    }

    public function evaluarCierre(array $features)
    {
        return $this->post('/evaluar', $features);
    }

    public function entrenarModelo(array $historico)
    {
        return $this->post('/entrenar', ['historico' => $historico]);
    }
}