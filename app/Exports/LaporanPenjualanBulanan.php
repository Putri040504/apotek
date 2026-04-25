<?php

namespace App\Exports;

use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;

class LaporanPenjualanBulanan implements FromCollection, WithHeadings, WithMapping, WithEvents
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

        return DB::table('penjualan')
        ->join('detail_penjualan','penjualan.id','=','detail_penjualan.penjualan_id')
        ->select(
            'penjualan.no_transaksi',
            'penjualan.tanggal',
            DB::raw('SUM(detail_penjualan.jumlah) as jumlah_item'),
            'penjualan.total'
        )
        ->whereMonth('penjualan.tanggal',$this->bulan)
        ->whereYear('penjualan.tanggal',$this->tahun)
        ->groupBy(
            'penjualan.id',
            'penjualan.no_transaksi',
            'penjualan.tanggal',
            'penjualan.total'
        )
        ->get();
    }

    public function headings(): array
    {
        return [
            ['APOTEK ZEMA'],
            ['Laporan Penjualan Obat'],
            ['Periode : '.$this->bulan.' / '.$this->tahun],
            [],
            [
                'No',
                'No Transaksi',
                'Tanggal',
                'Jumlah Item',
                'Total'
            ]
        ];
    }

    public function map($data): array
    {
        return [
            $this->no++,
            $data->no_transaksi,
            date('d-m-Y',strtotime($data->tanggal)),
            $data->jumlah_item,
            'Rp '.number_format($data->total,0,',','.')
        ];
    }

    public function registerEvents(): array
    {
        return [

            AfterSheet::class => function(AfterSheet $event){

                $event->sheet->mergeCells('A1:E1');
                $event->sheet->mergeCells('A2:E2');
                $event->sheet->mergeCells('A3:E3');

                $event->sheet->getStyle('A1:A3')
                    ->getAlignment()
                    ->setHorizontal('center');

                $event->sheet->getStyle('A1:A3')
                    ->getFont()
                    ->setBold(true)
                    ->setSize(14);

                $event->sheet->getStyle('A5:E5')
                    ->getFont()
                    ->setBold(true);

                foreach(range('A','E') as $column){
                    $event->sheet->getColumnDimension($column)->setAutoSize(true);
                }

            }

        ];
    }

}