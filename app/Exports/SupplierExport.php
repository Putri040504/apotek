<?php

namespace App\Exports;

use App\Models\Supplier;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;

class SupplierExport implements FromCollection, WithHeadings, WithMapping, WithEvents
{
    private $no = 1;

    public function collection()
    {
        return Supplier::with('obat')->get();
    }

    public function headings(): array
    {
        return [
            ['APOTEK ZEMA'],
            ['Data Supplier'],
            [],
            ['No','Kode Supplier','Nama Supplier','Nama Obat','Alamat','Email','No Telp']
        ];
    }

    public function map($s): array
    {
        return [
            $this->no++,
            $s->kode_supplier,
            $s->nama_supplier,
            $s->obat->nama_obat ?? '-',
            $s->alamat,
            $s->email,
            $s->no_telp
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function($event){

                $event->sheet->mergeCells('A1:G1');
                $event->sheet->mergeCells('A2:G2');

                $event->sheet->getStyle('A1:A2')
                ->getAlignment()->setHorizontal('center');

                $event->sheet->getStyle('A1:A2')
                ->getFont()->setBold(true)->setSize(14);

                $event->sheet->getStyle('A4:G4')
                ->getFont()->setBold(true);

                foreach(range('A','G') as $column){
                    $event->sheet->getColumnDimension($column)->setAutoSize(true);
                }

            }
        ];
    }
}