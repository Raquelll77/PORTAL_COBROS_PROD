<?php

namespace Services;

class UltraMsg
{
    private $token;
    private $instanceId;
    private $baseUrl;

    public function __construct($token, $instanceId)
    {
        $this->token = $token;
        $this->instanceId = $instanceId;
        $this->baseUrl = "https://api.ultramsg.com/{$this->instanceId}/messages";
    }

    /** Envía un mensaje de texto por WhatsApp */
    public function sendTextMessage($phone, $message)
    {
        $url = "{$this->baseUrl}/chat";
        $data = [
            'token' => $this->token,
            'to' => $phone,
            'body' => $message
        ];
        return $this->makeRequest($url, $data);
    }

    /** Envía una imagen con mensaje */
    public function sendImageMessage($phone, $imageUrl, $caption = '')
    {
        $url = "{$this->baseUrl}/image";
        $data = [
            'token' => $this->token,
            'to' => $phone,
            'image' => $imageUrl,
            'caption' => $caption
        ];
        return $this->makeRequest($url, $data);
    }

    /** Ejecuta la petición HTTP */
    private function makeRequest($url, $data)
    {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));

        $response = curl_exec($ch);
        $error = curl_error($ch);
        curl_close($ch);

        if ($error) {
            return ['success' => false, 'error' => $error];
        }

        $decoded = json_decode($response, true);
        if (is_array($decoded)) {
            $decoded['success'] = true;
            return $decoded;
        }
        return ['success' => false, 'error' => 'Invalid JSON response'];
    }
}
