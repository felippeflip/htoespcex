<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PagTesouroService
{
    protected $baseUrl;
    protected $token;

    public function __construct()
    {
        $this->baseUrl = config('services.pagtesouro.base_url');
        $this->token = config('services.pagtesouro.token');
    }

    public function createPaymentRequest(array $data)
    {
        try {
            // Ensure headers are correct if needed, usually Http::withToken handles Authorization Bearer
            $response = Http::withToken($this->token)
                ->post("{$this->baseUrl}/api/gru/solicitacao-pagamento", $data);

            if ($response->successful()) {
                return $response->json();
            }

            Log::error('PagTesouro Create Payment Error: ' . $response->body());
            return null;
        } catch (\Exception $e) {
            Log::error('PagTesouro Connection Error: ' . $e->getMessage());
            return null;
        }
    }

    public function checkPaymentStatus($idPagamento)
    {
        try {
            $response = Http::withToken($this->token)
                ->get("{$this->baseUrl}/api/gru/solicitacao-pagamento/{$idPagamento}");

            if ($response->successful()) {
                return $response->json();
            }

            Log::error('PagTesouro Check Status Error: ' . $response->body());
            return null;
        } catch (\Exception $e) {
            Log::error('PagTesouro Connection Error: ' . $e->getMessage());
            return null;
        }
    }
}
