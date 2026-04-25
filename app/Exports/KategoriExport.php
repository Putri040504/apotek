<?php

namespace App\Exports;

use App\Models\Kategori;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;

class KategoriExport implements FromCollection, WithHeadings, WithMapping, WithEvents
{
    private $no = 1;

    public function collection()
    {
        return Kategori::all();
    }

    public function headings(): array
    {
        return [
            ['APOTEK ZEMA'],
            ['Data Kategori Obat'],
            [],
            ['No', 'Kode Kategori', 'Nama Kategori']
        ];
    }

    public function map($kategori): array
    {
        return [
            $this->no++,
            $kategori->kode_kategori,
            $kategori->nama_kategori
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {

                // merge judul
                $event->sheet->mergeCells('A1:C1');
                $event->sheet->mergeCells('A2:C2');

                // center text
                $event->sheet->getStyle('A1:A2')->getAlignment()->setHorizontal('center');

                // bold judul
                $event->sheet->getStyle('A1:A2')->getFont()->setBold(true)->setSize(14);

                // bold header tabel
                $event->sheet->getStyle('A4:C4')->getFont()->setBold(true);

                // auto width
                foreach(range('A','C') as $column){
                    $event->sheet->getColumnDimension($column)->setAutoSize(true);
                }

            }
        ];
    }
}