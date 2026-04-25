<?php

namespace App\Exports;

use App\Models\Obat;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;

class LaporanObatExport implements FromCollection, WithHeadings, WithMapping, WithEvents
{

    private $no = 1;
    protected $bulan;
    protected $tahun;

    public function __construct($bulan,$tahun)
    {
        $this->bulan = $bulan;
        $this->tahun = $tahun;
    }

    public function collection()
    {

        $query = Obat::with('kategori');

        if($this->bulan){
            $query->whereMonth('created_at',$this->bulan);
        }

        if($this->tahun){
            $query->whereYear('created_at',$this->tahun);
        }

        return $query->get();
    }

    public function headings(): array
    {
        return [
            ['APOTEK ZEMA'],
            ['Laporan Data Obat'],
            [],
            [
                'No',
                'Kode Obat',
                'Nama Obat',
                'Kategori',
                'Stok',
                'Harga Jual'
            ]
        ];
    }

    public function map($obat): array
    {
        return [
            $this->no++,
            $obat->kode_obat,
            $obat->nama_obat,
            $obat->kategori->nama_kategori ?? '-',
            $obat->stok,
            'Rp ' . number_format($obat->harga_jual,0,',','.')
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {

                // merge judul
                $event->sheet->mergeCells('A1:F1');
                $event->sheet->mergeCells('A2:F2');

                // center text judul
                $event->sheet->getStyle('A1:A2')
                    ->getAlignment()
                    ->setHorizontal('center');

                // bold judul
                $event->sheet->getStyle('A1:A2')
                    ->getFont()
                    ->setBold(true)
                    ->setSize(14);

                // bold header tabel
                $event->sheet->getStyle('A4:F4')
                    ->getFont()
                    ->setBold(true);

                // auto width
                foreach(range('A','F') as $column){
                    $event->sheet->getColumnDimension($column)->setAutoSize(true);
                }

            }
        ];
    }

}