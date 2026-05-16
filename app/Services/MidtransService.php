<?php

namespace App\Services;

use App\Models\Penjualan;
use Midtrans\Config;
use Midtrans\CoreApi;
use Midtrans\Notification;

class MidtransService
{
    public function __construct()
    {
        Config::$serverKey = config('midtrans.server_key');
        Config::$isProduction = (bool) config('midtrans.is_production');
        Config::$isSanitized = (bool) config('midtrans.is_sanitized');
        Config::$is3ds = (bool) config('midtrans.is_3ds');
    }

    public function createQrisCharge(Penjualan $penjualan): array
    {
        $orderId = $penjualan->midtrans_order_id ?? ('TRX-' . $penjualan->id . '-' . time());

        $params = [
            'payment_type' => 'qris',
            'transaction_details' => [
                'order_id' => $orderId,
                'gross_amount' => (int) $penjualan->total,
            ],
            'item_details' => $this->buildItemDetails($penjualan),
            'customer_details' => [
                'first_name' => auth()->user()->name ?? 'Kasir',
            ],
        ];

        $response = CoreApi::charge($params);

        return is_array($response) ? $response : (array) $response;
    }

    public function parseNotification(): object
    {
        return new Notification();
    }

    protected function buildItemDetails(Penjualan $penjualan): array
    {
        $penjualan->loadMissing('detail.obat');

        $items = [];
        foreach ($penjualan->detail as $detail) {
            $items[] = [
                'id' => (string) $detail->obat_id,
                'price' => (int) $detail->harga,
                'quantity' => (int) $detail->jumlah,
                'name' => substr($detail->obat->nama_obat ?? 'Obat', 0, 50),
            ];
        }

        if (empty($items)) {
            $items[] = [
                'id' => '1',
                'price' => (int) $penjualan->total,
                'quantity' => 1,
                'name' => 'Penjualan Apotek',
            ];
        }

        return $items;
    }

    public function extractQrString(array $response): ?string
    {
        if (! empty($response['qr_string'])) {
            return $response['qr_string'];
        }

        $actions = $response['actions'] ?? [];
        foreach ($actions as $action) {
            if (($action['name'] ?? '') === 'generate-qr-code' || ($action['name'] ?? '') === 'qr-code') {
                return $action['url'] ?? null;
            }
        }

        return null;
    }
}
