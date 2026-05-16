<?php

namespace App\Exports;

use App\Models\Obat;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;

class ObatExport implements FromCollection, WithHeadings, WithMapping, WithEvents
{
    private $no = 1;

    public function collection()
    {
        return Obat::with(['kategori', 'stokBatches' => fn ($q) => $q->hasStock()->orderFefo()])->get();
    }

    public function headings(): array
    {
        return [
            ['APOTEK ZEMA'],
            ['Data Obat'],
            [],
            [
                'No',
                'Kode Obat',
                'Nama Obat',
                'Kategori',
                'Tanggal EXP',
                'Stok',
                'Harga Beli',
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
            $obat->kategori->nama_kategori,
            $obat->earliestExpiryBatch()?->tanggal_exp?->format('Y-m-d') ?? '-',
            $obat->stok,
            'Rp ' . number_format($obat->harga_beli,0,',','.'),
            'Rp ' . number_format($obat->harga_jual,0,',','.')
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {

                // merge judul
                $event->sheet->mergeCells('A1:H1');
                $event->sheet->mergeCells('A2:H2');

                // center text
                $event->sheet->getStyle('A1:A2')
                    ->getAlignment()
                    ->setHorizontal('center');

                // bold judul
                $event->sheet->getStyle('A1:A2')
                    ->getFont()
                    ->setBold(true)
                    ->setSize(14);

                // bold header tabel
                $event->sheet->getStyle('A4:H4')
                    ->getFont()
                    ->setBold(true);

                // auto width kolom
                foreach(range('A','H') as $column){
                    $event->sheet->getColumnDimension($column)->setAutoSize(true);
                }

            }
        ];
    }
}