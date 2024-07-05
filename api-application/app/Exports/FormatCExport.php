<?php

namespace App\Exports;

use App\Models\FormatCModel;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class FormatCExport implements FromCollection, WithHeadings, WithEvents, WithCustomStartCell, WithStyles
{
    protected $tahun;
    public function __construct($tahun = null)
    {
        $this->tahun = $tahun;
    }
    public function headings(): array
    {
        /**
         * Menyusun baris judul Anda
         * 
         */
        return [
            'Nama Ayah',
            'Nama Ibu',
            'Nama Bayi',
            'L/P',
            'Tanggal Lahir',
            'Tanggal Meninggal Bayi',
            'Tanggal Meninggal Ibu',
            'Keterangan',
        ];
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        /**
         * Membuat query dasar yang akan
         * dijadikan query utama
         * 
         */
        $query = FormatCModel::select(
            'orang_tua.nama_ayah',
            'orang_tua.nama_ibu',
            'bayi.nama as nama_bayi',
            'bayi.jenis_kelamin',
            'bayi.tanggal_lahir',
            'bayi.tanggal_meninggal as tanggal_meninggal_bayi',
            'orang_tua.tanggal_meninggal_ibu',
            'format_a.keterangan',
        )
            ->join('bayi', 'bayi.id', 'format_a.id_bayi')
            ->join('orang_tua', 'orang_tua.id', 'bayi.id_orang_tua')
            ->orderByDesc('format_a.created_at');

        /**
         * Memeriksa apakah user meminta
         * data berdasarkan tahun
         * 
         */
        if (!empty($this->tahun)) {
            $query = $query->whereYear('bayi.tanggal_lahir', '=', $this->tahun);
        }

        /**
         * Mengambil data dari query
         * 
         */
        $formatA = $query->get();

        /**
         * Mengembalikan data format a untuk
         * dicetak sebagai file excel
         * 
         */
        return $formatA;
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                // Menggabungkan kolom A sampai C pada baris 1
                $event->sheet->getDelegate()->mergeCells('A1:H3');

                // Menambahkan judul ke tengah-tengah sel yang digabungkan
                $event->sheet->setCellValue('A1', 'Catatan ibu hamil, kelahiran, kematian bayi' . chr(10) . 'dan kematian ibu hamil, melahirkan atau nifas' . chr(10) . 'Januari - Desember');
                $event->sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            },
        ];
    }

    public function styles(Worksheet $sheet)
    {
        /**
         * Mengatur "wrap text" atau "teks berjalan
         * otomatis" untuk kolom yang membutuhkan
         * 
         */
        $sheet->getStyle(1)->getAlignment()->setWrapText(true);
        $sheet->getColumnDimension('A')->setWidth(26);
        $sheet->getColumnDimension('B')->setWidth(26);
        $sheet->getColumnDimension('C')->setWidth(26);
        $sheet->getColumnDimension('D')->setWidth(3);
        $sheet->getColumnDimension('E')->setWidth(15);
        $sheet->getColumnDimension('F')->setWidth(20);
        $sheet->getColumnDimension('G')->setWidth(20);
        $sheet->getColumnDimension('H')->setWidth(50);
        return [
            1 => ['font' => ['bold' => true]],
            4 => ['font' => ['bold' => true]],
        ];
    }

    public function startCell(): string
    {
        return 'A4';
    }
}
