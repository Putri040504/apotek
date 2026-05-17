<?php

namespace App\Http\Controllers\Kasir;

use App\Http\Controllers\Controller;
use App\Http\Controllers\ObatLookupController;
use App\Models\DetailPenjualan;
use App\Models\DetailPenjualanBatch;
use App\Models\KeranjangPenjualan;
use App\Models\Obat;
use App\Models\Penjualan;
use App\Services\MidtransService;
use App\Exports\RiwayatPenjualanExport;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Midtrans\Transaction;
use Maatwebsite\Excel\Facades\Excel;

class PenjualanController extends Controller
{
    public function pos()
    {
        $keranjang = $this->getCart();
        $topObat = $this->getTopObat(10);

        return view('kasir.pos.index', compact('keranjang', 'topObat'));
    }

    public function searchObat(Request $request)
    {
        $q = trim($request->get('q', ''));

        if ($q === '') {
            return response()->json([]);
        }

        $limit = mb_strlen($q) <= 2 ? 30 : 20;

        $obat = Obat::sellable()
            ->searchTerm($q)
            ->limit($limit)
            ->get();

        return response()->json($obat->map->toPosArray());
    }

    public function scanObat(Request $request)
    {
        $request->merge(['context' => 'pos']);

        return app(ObatLookupController::class)($request);
    }

    public function store(Request $request)
    {
        $request->validate([
            'obat_id' => 'required|exists:obats,id',
            'jumlah' => 'required|integer|min:1',
        ]);

        $obat = Obat::findOrFail($request->obat_id);
        $jumlah = (int) $request->jumlah;

        $error = $obat->saleValidationMessage($jumlah);
        if ($error) {
            return $this->cartResponse($error, false);
        }

        $existing = KeranjangPenjualan::forUser()
            ->where('obat_id', $obat->id)
            ->first();

        if ($existing) {
            $newQty = $existing->jumlah + $jumlah;
            $error = $obat->saleValidationMessage($newQty);
            if ($error) {
                return $this->cartResponse($error, false);
            }
            $existing->update(['jumlah' => $newQty]);
        } else {
            KeranjangPenjualan::create([
                'user_id' => auth()->id(),
                'obat_id' => $obat->id,
                'jumlah' => $jumlah,
            ]);
        }

        return $this->cartResponse('Obat ditambahkan ke keranjang');
    }

    public function updateCart(Request $request, $id)
    {
        $request->validate([
            'jumlah' => 'required|integer|min:1',
        ]);

        $item = KeranjangPenjualan::forUser()->findOrFail($id);
        $obat = Obat::findOrFail($item->obat_id);
        $jumlah = (int) $request->jumlah;

        $error = $obat->saleValidationMessage($jumlah);
        if ($error) {
            return $this->cartResponse($error, false);
        }

        $item->update(['jumlah' => $jumlah]);

        return $this->cartResponse('Jumlah diperbarui');
    }

    public function destroy($id)
    {
        KeranjangPenjualan::forUser()->findOrFail($id)->delete();

        if (request()->expectsJson()) {
            return $this->cartResponse('Item dihapus');
        }

        return redirect()->back()->with('success', 'Item dihapus dari keranjang');
    }

    public function clearCart()
    {
        KeranjangPenjualan::forUser()->delete();

        if (request()->expectsJson()) {
            return $this->cartResponse('Keranjang dikosongkan');
        }

        return redirect()->back()->with('success', 'Keranjang dikosongkan');
    }

    public function checkout(Request $request)
    {
        $request->validate([
            'bayar' => 'required|integer|min:0',
        ]);

        $keranjang = $this->getCart();

        if ($keranjang->isEmpty()) {
            return $this->cartResponse('Keranjang masih kosong', false);
        }

        $total = $this->cartTotal($keranjang);
        $bayar = (int) $request->bayar;

        if ($bayar < $total) {
            return $this->cartResponse('Uang pembayaran kurang', false);
        }

        try {
            $penjualan = DB::transaction(function () use ($keranjang, $total, $bayar) {
                $penjualan = Penjualan::create([
                    'no_transaksi' => 'TRX' . now()->format('YmdHis'),
                    'tanggal' => now(),
                    'total' => $total,
                    'bayar' => $bayar,
                    'kembalian' => $bayar - $total,
                    'user_id' => auth()->id(),
                    'metode_bayar' => 'tunai',
                    'status' => 'paid',
                ]);

                foreach ($keranjang as $item) {
                    $obat = Obat::lockForUpdate()->findOrFail($item->obat_id);

                    $obat->assertSellable($item->jumlah);

                    $detail = DetailPenjualan::create([
                        'penjualan_id' => $penjualan->id,
                        'obat_id' => $obat->id,
                        'jumlah' => $item->jumlah,
                        'harga' => $obat->harga_jual,
                        'subtotal' => $obat->subtotalForQuantity($item->jumlah),
                    ]);

                    $this->recordBatchSale($detail, $obat, $item->jumlah);
                }

                KeranjangPenjualan::forUser()->delete();

                return $penjualan;
            });
        } catch (\RuntimeException $e) {
            return $this->cartResponse($e->getMessage(), false);
        }

        return response()->json([
            'success' => true,
            'message' => 'Pembayaran tunai berhasil',
            'penjualan_id' => $penjualan->id,
            'print_url' => route('penjualan.cetak', $penjualan->id),
            'kembalian' => $penjualan->kembalian,
            'html' => view('kasir.pos._cart', ['keranjang' => collect()])->render(),
            'total' => 0,
            'item_count' => 0,
        ]);
    }

    public function checkoutQris(Request $request, MidtransService $midtrans)
    {
        $keranjang = $this->getCart();

        if ($keranjang->isEmpty()) {
            return response()->json(['success' => false, 'message' => 'Keranjang masih kosong'], 422);
        }

        foreach ($keranjang as $item) {
            $obat = Obat::find($item->obat_id);
            if (! $obat) {
                return response()->json(['success' => false, 'message' => 'Obat tidak ditemukan'], 422);
            }
            $error = $obat->saleValidationMessage($item->jumlah);
            if ($error) {
                return response()->json(['success' => false, 'message' => $error], 422);
            }
        }

        $total = $this->cartTotal($keranjang);
        $orderId = 'TRX-' . auth()->id() . '-' . time();

        $penjualan = Penjualan::create([
            'no_transaksi' => 'TRX' . now()->format('YmdHis'),
            'tanggal' => now(),
            'total' => $total,
            'bayar' => 0,
            'kembalian' => 0,
            'user_id' => auth()->id(),
            'metode_bayar' => 'qris',
            'status' => 'pending',
            'midtrans_order_id' => $orderId,
        ]);

        foreach ($keranjang as $item) {
            $obat = Obat::findOrFail($item->obat_id);
            DetailPenjualan::create([
                'penjualan_id' => $penjualan->id,
                'obat_id' => $obat->id,
                'jumlah' => $item->jumlah,
                'harga' => $obat->harga_jual,
                'subtotal' => $obat->harga_jual * $item->jumlah,
            ]);
        }

        try {
            $charge = $midtrans->createQrisCharge($penjualan);
        } catch (\Exception $e) {
            $penjualan->update(['status' => 'cancelled']);
            DetailPenjualan::where('penjualan_id', $penjualan->id)->delete();
            $penjualan->delete();

            return response()->json([
                'success' => false,
                'message' => 'Gagal membuat QRIS: ' . $e->getMessage(),
            ], 500);
        }

        $penjualan->update([
            'midtrans_transaction_id' => $charge['transaction_id'] ?? null,
        ]);

        KeranjangPenjualan::forUser()->delete();

        $qrString = $midtrans->extractQrString($charge);

        return response()->json([
            'success' => true,
            'penjualan_id' => $penjualan->id,
            'qr_string' => $qrString,
            'total' => $total,
            'status_url' => route('penjualan.status', $penjualan->id),
        ]);
    }

    public function qrisStatus($id)
    {
        $penjualan = Penjualan::where('user_id', auth()->id())->findOrFail($id);

        if ($penjualan->isPending() && $penjualan->midtrans_order_id && config('midtrans.server_key')) {
            try {
                $status = Transaction::status($penjualan->midtrans_order_id);
                $transactionStatus = is_object($status) ? ($status->transaction_status ?? null) : ($status['transaction_status'] ?? null);

                if (in_array($transactionStatus, ['capture', 'settlement'])) {
                    $txId = is_object($status) ? ($status->transaction_id ?? null) : ($status['transaction_id'] ?? null);
                    $this->finalizeQrisPayment($penjualan, $txId);
                    $penjualan->refresh();
                } elseif (in_array($transactionStatus, ['expire', 'cancel', 'deny'])) {
                    $penjualan->update(['status' => 'cancelled']);
                    $penjualan->refresh();
                }
            } catch (\Exception $e) {
                // polling fallback: rely on webhook
            }
        }

        return response()->json([
            'status' => $penjualan->status,
            'print_url' => $penjualan->isPaid() ? route('penjualan.cetak', $penjualan->id) : null,
        ]);
    }

    public function cancelQris($id)
    {
        $penjualan = Penjualan::where('user_id', auth()->id())
            ->where('status', 'pending')
            ->findOrFail($id);

        $penjualan->update(['status' => 'cancelled']);
        DetailPenjualan::where('penjualan_id', $penjualan->id)->delete();

        return response()->json(['success' => true, 'message' => 'Pembayaran QRIS dibatalkan']);
    }

    public function midtransNotification(Request $request, MidtransService $midtrans)
    {
        try {
            $notification = $midtrans->parseNotification();
        } catch (\Exception $e) {
            return response()->json(['message' => 'Invalid notification'], 400);
        }

        $orderId = $notification->order_id ?? null;
        $transactionStatus = $notification->transaction_status ?? null;
        $fraudStatus = $notification->fraud_status ?? null;

        $penjualan = Penjualan::where('midtrans_order_id', $orderId)
            ->orWhere('midtrans_transaction_id', $notification->transaction_id ?? null)
            ->first();

        if (! $penjualan || ! $penjualan->isPending()) {
            return response()->json(['message' => 'OK']);
        }

        $success = in_array($transactionStatus, ['capture', 'settlement'])
            || ($transactionStatus === 'pending' && ($notification->payment_type ?? '') === 'qris' && $fraudStatus === 'accept');

        if ($transactionStatus === 'expire' || $transactionStatus === 'cancel' || $transactionStatus === 'deny') {
            $penjualan->update(['status' => 'cancelled']);
            return response()->json(['message' => 'Cancelled']);
        }

        if ($success || $transactionStatus === 'settlement') {
            $this->finalizeQrisPayment($penjualan, $notification->transaction_id ?? null);
        }

        return response()->json(['message' => 'OK']);
    }

    public function cetak($id)
    {
        $penjualan = Penjualan::with([
            'detail.obat',
            'detail.batchAllocations.stokBatch',
            'user',
        ])->findOrFail($id);

        return view('kasir.data_penjualan.cetak', compact('penjualan'));
    }

    public function riwayat(Request $request)
    {
        $bulan = $request->bulan ?? now()->month;
        $tahun = $request->tahun ?? now()->year;

        $query = Penjualan::with(['detail.obat', 'detail.batchAllocations.stokBatch']);

        if ($bulan) {
            $query->whereMonth('tanggal', $bulan);
        }

        if ($tahun) {
            $query->whereYear('tanggal', $tahun);
        }

        $riwayat = $query->orderByDesc('tanggal')->get();

        return view('kasir.data_riwayat.index', compact('riwayat', 'bulan', 'tahun'));
    }

    public function detailModal($id)
    {
        $penjualan = Penjualan::with(['detail.obat', 'detail.batchAllocations.stokBatch'])->findOrFail($id);

        return view('kasir.data_riwayat.detail_modal', compact('penjualan'));
    }

    public function cetakRiwayat($id)
    {
        $penjualan = Penjualan::with(['detail.obat', 'detail.batchAllocations.stokBatch'])->findOrFail($id);

        return view('kasir.data_riwayat.cetak', compact('penjualan'));
    }

    public function exportExcel(Request $request)
    {
        $bulan = $request->bulan ?? now()->month;
        $tahun = $request->tahun ?? now()->year;

        return Excel::download(
            new RiwayatPenjualanExport($bulan, $tahun),
            'riwayat_penjualan.xlsx'
        );
    }

    public function exportPDF(Request $request)
    {
        $bulan = $request->bulan ?? now()->month;
        $tahun = $request->tahun ?? now()->year;

        $query = Penjualan::with(['detail.obat', 'detail.batchAllocations.stokBatch']);

        if ($bulan) {
            $query->whereMonth('tanggal', $bulan);
        }

        if ($tahun) {
            $query->whereYear('tanggal', $tahun);
        }

        $riwayat = $query->get();

        $pdf = Pdf::loadView('kasir.data_riwayat.pdf', compact('riwayat'));

        return $pdf->download('riwayat_penjualan.pdf');
    }

    protected function getCart()
    {
        return KeranjangPenjualan::forUser()
            ->with('obat')
            ->orderBy('created_at')
            ->get();
    }

    protected function cartTotal($keranjang): int
    {
        return $keranjang->sum(fn ($item) => $item->obat
            ? $item->obat->subtotalForQuantity($item->jumlah)
            : 0);
    }

    protected function cartResponse(string $message, bool $success = true)
    {
        $keranjang = $this->getCart();
        $total = $this->cartTotal($keranjang);

        return response()->json([
            'success' => $success,
            'message' => $message,
            'html' => view('kasir.pos._cart', compact('keranjang'))->render(),
            'total' => $total,
            'item_count' => $keranjang->sum('jumlah'),
        ], $success ? 200 : 422);
    }

    protected function finalizeQrisPayment(Penjualan $penjualan, ?string $transactionId = null): void
    {
        if ($penjualan->isPaid()) {
            return;
        }

        DB::transaction(function () use ($penjualan, $transactionId) {
            $penjualan = Penjualan::lockForUpdate()->find($penjualan->id);

            if ($penjualan->isPaid()) {
                return;
            }

            $penjualan->load('detail');

            foreach ($penjualan->detail as $detail) {
                $obat = Obat::lockForUpdate()->findOrFail($detail->obat_id);
                $this->recordBatchSale($detail, $obat, $detail->jumlah);
            }

            $penjualan->update([
                'status' => 'paid',
                'bayar' => $penjualan->total,
                'kembalian' => 0,
                'midtrans_transaction_id' => $transactionId ?? $penjualan->midtrans_transaction_id,
            ]);

            if ($penjualan->user_id) {
                KeranjangPenjualan::forUser($penjualan->user_id)->delete();
            }
        });
    }

    protected function recordBatchSale(DetailPenjualan $detail, Obat $obat, int $quantity): void
    {
        $allocations = $obat->decreaseStockFefo($quantity);

        foreach ($allocations as $row) {
            DetailPenjualanBatch::create([
                'detail_penjualan_id' => $detail->id,
                'stok_batch_id' => $row['stok_batch_id'],
                'jumlah' => $row['jumlah'],
            ]);
        }
    }

    protected function getTopObat(int $limit)
    {
        return DetailPenjualan::select('obat_id', DB::raw('SUM(jumlah) as total_terjual'))
            ->whereMonth('created_at', Carbon::now()->month)
            ->whereYear('created_at', Carbon::now()->year)
            ->groupBy('obat_id')
            ->orderByDesc('total_terjual')
            ->with(['obat' => fn ($q) => $q->sellable()])
            ->limit($limit)
            ->get()
            ->pluck('obat')
            ->filter();
    }
}
