<?php

namespace App\Exports;

use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;

class PembelianJenisExport implements FromCollection, WithHeadings, WithMapping, WithEvents
{
    private $bulan;
    private $obat;
    private $no = 1;

    public function __construct($bulan,$obat)
    {
        $this->bulan = $bulan;
        $this->obat  = $obat;
    }

    public function collection()
    {
        return DB::table('detail_pembelian')
        ->join('pembelian','detail_pembelian.pembelian_id','=','pembelian.id')
        ->join('obats','detail_pembelian.obat_id','=','obats.id')
        ->join('suppliers','pembelian.supplier_id','=','suppliers.id')

        ->select(
            'pembelian.kode_transaksi',
            'pembelian.tanggal',
            'suppliers.nama_supplier',
            'detail_pembelian.harga',
            'detail_pembelian.jumlah',
            'detail_pembelian.subtotal'
        )

        ->whereMonth('pembelian.tanggal',$this->bulan)
        ->where('detail_pembelian.obat_id',$this->obat)

        ->get();
    }

    public function headings(): array
    {
        return [
            ['APOTEK ZEMA'],
            ['Laporan Pembelian Berdasarkan Jenis Obat'],
            [],
            ['No','Kode Transaksi','Tanggal','Supplier','Harga Modal','Jumlah','Total']
        ];
    }

    public function map($row): array
    {
        return [
            $this->no++,
            $row->kode_transaksi,
            $row->tanggal,
            $row->nama_supplier,
            $row->harga,
            $row->jumlah,
            $row->subtotal
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event){

                // merge judul
                $event->sheet->mergeCells('A1:G1');
                $event->sheet->mergeCells('A2:G2');

                // center judul
                $event->sheet->getStyle('A1:A2')
                ->getAlignment()
                ->setHorizontal('center');

                // bold judul
                $event->sheet->getStyle('A1:A2')
                ->getFont()
                ->setBold(true)
                ->setSize(14);

                // bold header tabel
                $event->sheet->getStyle('A4:G4')
                ->getFont()
                ->setBold(true);

                // auto width
                foreach(range('A','G') as $column){
                    $event->sheet
                    ->getColumnDimension($column)
                    ->setAutoSize(true);
                }

            }
        ];
    }
}