<?php

namespace App\Exports;

use App\Models\Penjualan;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;

class RiwayatPenjualanExport implements FromCollection, WithHeadings, WithEvents
{

    private $bulan;
    private $tahun;
    private $total = 0;

    public function __construct($bulan,$tahun)
    {
        $this->bulan = $bulan;
        $this->tahun = $tahun;
    }

    public function collection()
    {

        $query = Penjualan::with(['detail.obat', 'detail.batchAllocations.stokBatch']);

        if($this->bulan){
            $query->whereMonth('tanggal',$this->bulan);
        }

        if($this->tahun){
            $query->whereYear('tanggal',$this->tahun);
        }

        $data = $query->get();

        $rows = [];
        $no = 1;

        foreach($data as $p){

            foreach($p->detail as $d){

                $rows[] = [
                    $no++,
                    $p->no_transaksi,
                    \Carbon\Carbon::parse($p->tanggal)->translatedFormat('d F Y'),
                    $d->obat?->nama_obat ?? '-',
                    ($d->batchAllocations->first()?->stokBatch?->tanggal_exp
                        ?? $d->obat?->earliestExpiryBatch()?->tanggal_exp)?->format('d-m-Y') ?? '-',
                    $d->harga,
                    $d->jumlah,
                    $d->subtotal
                ];

                $this->total += $d->subtotal;

            }

        }

        // baris total
        $rows[] = ['', '', '', '', '', '', 'Total Penjualan', $this->total];

        return new Collection($rows);

    }

    public function headings(): array
    {
        return [
            ['APOTEK ZEMA'],
            ['LAPORAN DETAIL PENJUALAN'],
            [],
            ['No','Kode Transaksi','Tanggal','Obat','Expired','Harga','Jumlah','Subtotal']
        ];
    }

    public function registerEvents(): array
    {
        return [

            AfterSheet::class => function(AfterSheet $event){

                // merge judul
                $event->sheet->mergeCells('A1:H1');
                $event->sheet->mergeCells('A2:H2');

                // center judul
                $event->sheet->getStyle('A1:A2')
                ->getAlignment()
                ->setHorizontal('center');

                // bold judul
                $event->sheet->getStyle('A1:A2')
                ->getFont()
                ->setBold(true)
                ->setSize(14);

                // bold header
                $event->sheet->getStyle('A4:H4')
                ->getFont()
                ->setBold(true);

                // auto width
                foreach(range('A','H') as $column){
                    $event->sheet->getColumnDimension($column)->setAutoSize(true);
                }

            }

        ];
    }

}