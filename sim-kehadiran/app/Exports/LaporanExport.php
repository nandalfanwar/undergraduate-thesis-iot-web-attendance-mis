<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use Carbon\Carbon;

class LaporanExport implements FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize, WithCustomStartCell
{
    protected $laporan;
    protected $role; // 'kepala-sekolah', 'staf-kedisiplinan', 'guru'
    protected $filterInfo;

    public function __construct(Collection $laporan, string $role = 'kepala-sekolah', array $filterInfo = [])
    {
        $this->laporan = $laporan;
        $this->role = $role;
        $this->filterInfo = $filterInfo;
    }

    /**
     * Tentukan baris pertama tabel data (Memberikan ruang untuk Judul & Informasi Filter di atas)
     */
    public function startCell(): string
    {
        return 'A6';
    }

    /**
     * Collection Data Presensi
     */
    public function collection()
    {
        return $this->laporan;
    }

    /**
     * Header Kolom Tabel berdasarkan Role
     */
    public function headings(): array
    {
        if ($this->role === 'guru') {
            return [
                'No',
                'Tanggal',
                'Waktu Masuk',
                'Waktu Keluar',
                'Status Kehadiran',
                'Keterangan / Catatan',
            ];
        }

        // Untuk Kepala Sekolah & Staf Kedisiplinan
        return [
            'No',
            'NUPTK / ID',
            'Nama Guru / Tendik',
            'Tanggal',
            'Waktu Masuk',
            'Waktu Keluar',
            'Status Kehadiran',
            'Keterangan / Catatan',
        ];
    }

    /**
     * Mapping Isi Data tiap Baris
     */
    public function map($row): array
    {
        static $no = 0;
        $no++;

        $tanggal = $row->tanggal ? Carbon::parse($row->tanggal)->translatedFormat('d F Y') : '-';
        $waktuMasuk = $row->waktu_masuk ? Carbon::parse($row->waktu_masuk)->format('H:i:s') : '-';
        $waktuKeluar = $row->waktu_keluar ? Carbon::parse($row->waktu_keluar)->format('H:i:s') : '-';
        $status = $row->statusKehadiran->status_kehadiran_label ?? '-';
        $keterangan = $row->keterangan ?? '-';

        if ($this->role === 'guru') {
            return [
                $no,
                $tanggal,
                $waktuMasuk,
                $waktuKeluar,
                $status,
                $keterangan,
            ];
        }

        return [
            $no,
            $row->user->nuptk ?? '-',
            $row->user->nama ?? '-',
            $tanggal,
            $waktuMasuk,
            $waktuKeluar,
            $status,
            $keterangan,
        ];
    }

    /**
     * Styling Header Laporan, Filter Info, & Border Tabel
     */
    public function styles(Worksheet $sheet)
    {
        $lastColumn = $this->role === 'guru' ? 'F' : 'H';
        $totalRows = $this->laporan->count() + 6; // Baris terakhir data

        // 1. Judul Laporan Header Top
        $sheet->mergeCells("A1:{$lastColumn}1");
        $sheet->setCellValue('A1', 'LAPORAN REKAPITULASI PRESENSI GURU & TENDIK');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // 2. Sub-Judul / Nama Sekolah
        $sheet->mergeCells("A2:{$lastColumn}2");
        $sheet->setCellValue('A2', 'SDIT DAARUL FATAA');
        $sheet->getStyle('A2')->getFont()->setBold(true)->setSize(11);
        $sheet->getStyle('A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // 3. Information Metadata Filter (Baris 3 & 4)
        $bulanText = $this->filterInfo['bulan'] ?? 'Semua Bulan';
        $tahunText = $this->filterInfo['tahun'] ?? 'Semua Tahun';
        $guruText  = $this->filterInfo['guru'] ?? 'Semua Guru / Tendik';

        $sheet->setCellValue('A4', "Periode: {$bulanText} {$tahunText} | Filter: {$guruText}");
        $sheet->getStyle('A4')->getFont()->setItalic(true)->setSize(10);

        // 4. Styling Header Tabel (Baris 6)
        $sheet->getStyle("A6:{$lastColumn}6")->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '198754'], // Warna Hijau Success (Bootstrap)
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ]);

        // 5. Border Seluruh Tabel
        $sheet->getStyle("A6:{$lastColumn}{$totalRows}")->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => 'D3D3D3'],
                ],
            ],
        ]);

        // 6. Ratatengah Kolom No, Tanggal, Jam, & Status
        $sheet->getStyle("A7:A{$totalRows}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        
        if ($this->role === 'guru') {
            $sheet->getStyle("B7:E{$totalRows}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        } else {
            $sheet->getStyle("B7:B{$totalRows}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("D7:G{$totalRows}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        }

        return [];
    }
}