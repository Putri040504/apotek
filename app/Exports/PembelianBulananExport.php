<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;

class PembelianBulananExport implements FromCollection, WithHeadings, WithMapping, WithEvents
{
    private $data;
    private $bulan;
    private $no = 1;

    public function __construct($data,$bulan)
    {
        $this->data = $data;
        $this->bulan = $bulan;
    }

    public function collection()
    {
        return new Collection($this->data);
    }

    public function headings(): array
    {
        return [
            ['APOTEK ZEMA'],
            ['Laporan Pembelian Obat Bulan '.$this->bulan],
            [],
            ['No','Kode Transaksi','Tanggal','Supplier','Jumlah Item','Total']
        ];
    }

    public function map($row): array
    {
        return [
            $this->no++,
            $row->kode_transaksi,
            $row->tanggal_transaksi,
            $row->supplier,
            $row->jumlah_item,
            $row->total_harga
        ];
    }

    public function registerEvents(): array
    {
        return [

            AfterSheet::class => function(AfterSheet $event){

                // merge judul
                $event->sheet->mergeCells('A1:F1');
                $event->sheet->mergeCells('A2:F2');

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
                $event->sheet->getStyle('A4:F4')
                ->getFont()
                ->setBold(true);

                // auto width
                foreach(range('A','F') as $column){
                    $event->sheet
                    ->getColumnDimension($column)
                    ->setAutoSize(true);
                }

            }

        ];
    }
}