<?php

namespace App\Exports;

use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;

class PenjualanJenisObatExport implements FromCollection, WithHeadings, WithMapping, WithEvents
{

    private $no = 1;
    protected $bulan;
    protected $tahun;
    protected $obat;

    public function __construct($bulan,$tahun,$obat)
    {
        $this->bulan = $bulan;
        $this->tahun = $tahun;
        $this->obat  = $obat;
    }

    public function collection()
    {

        return DB::table('detail_penjualan')
        ->join('penjualan','detail_penjualan.penjualan_id','=','penjualan.id')
        ->join('obats','detail_penjualan.obat_id','=','obats.id')
        ->whereMonth('penjualan.tanggal',$this->bulan)
        ->whereYear('penjualan.tanggal',$this->tahun)
        ->where('obats.id',$this->obat)
        ->select(
            'penjualan.no_transaksi',
            'penjualan.tanggal',
            'detail_penjualan.harga',
            'detail_penjualan.jumlah',
            DB::raw('(detail_penjualan.harga * detail_penjualan.jumlah) as total_penjualan')
        )
        ->get();

    }

    public function headings(): array
    {
        return [
            ['APOTEK ZEMA'],
            ['Laporan Penjualan Berdasarkan Jenis Obat'],
            ['Periode : '.$this->bulan.' / '.$this->tahun],
            [],
            [
                'No',
                'No Transaksi',
                'Tanggal',
                'Harga',
                'Jumlah',
                'Total Penjualan'
            ]
        ];
    }

    public function map($data): array
    {
        return [
            $this->no++,
            $data->no_transaksi,
            date('d-m-Y',strtotime($data->tanggal)),
            'Rp '.number_format($data->harga,0,',','.'),
            $data->jumlah,
            'Rp '.number_format($data->total_penjualan,0,',','.')
        ];
    }

    public function registerEvents(): array
    {
        return [

            AfterSheet::class => function(AfterSheet $event){

                $event->sheet->mergeCells('A1:F1');
                $event->sheet->mergeCells('A2:F2');
                $event->sheet->mergeCells('A3:F3');

                $event->sheet->getStyle('A1:A3')
                    ->getAlignment()
                    ->setHorizontal('center');

                $event->sheet->getStyle('A1:A3')
                    ->getFont()
                    ->setBold(true)
                    ->setSize(14);

                $event->sheet->getStyle('A5:F5')
                    ->getFont()
                    ->setBold(true);

                foreach(range('A','F') as $column){
                    $event->sheet->getColumnDimension($column)->setAutoSize(true);
                }

            }

        ];
    }

}