<?php

namespace App\Exports;

use App\Models\Curso;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;


class PlanoEstudoTemplateExport implements FromArray, WithTitle, WithStyles, WithColumnWidths
{
    public function __construct(private Curso $curso)
    {
    }
    public function array(): array
    {
        return [
        ['Plano de Estudo - Template de Importação'],
        ['Curso'],
        ['', 'Nome', 'Sigla', 'Código', 'Ano do plano de estudo'],
        ['', $this->curso->nome, $this->curso->sigla, $this->curso->codigo, ''],
        ['Unidades Curriculares'],
        ['Nome em português', 'Nome em inglês', 'Sigla', 'ECTS', 'Horas', 'Semestre'],
        ['', '', '', '', '', ''],
    ];
    }

    public function title(): string
    {
        return 'Plano de Estudo';
    }

    public function columnWidths(): array
    {
        return [
            'A' => 28,
            'B' => 32,
            'C' => 16,
            'D' => 14,
            'E' => 18,
            'F' => 14,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->mergeCells('A1:F1');
        $sheet->mergeCells('A2:F2');
        $sheet->mergeCells('A5:F5');

        $sheet->getStyle('A1:F1')->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 18,
                'color' => ['rgb' => 'FFFFFF'],
            ],
            'fill' => [
                'fillType' => 'solid',
                'color' => ['rgb' => '1F2937'],
            ],
            'alignment' => [
                'horizontal' => 'center',
                'vertical' => 'center',
            ],
        ]);

        $sectionStyle = [
            'font' => [
                'bold' => true,
                'size' => 16,
                'color' => ['rgb' => '000000'],
            ],
            'fill' => [
                'fillType' => 'solid',
                'color' => ['rgb' => '93B7D7'],
            ],
            'alignment' => [
                'horizontal' => 'center',
                'vertical' => 'center',
            ],
        ];

        $sheet->getStyle('A2:F2')->applyFromArray($sectionStyle);
        $sheet->getStyle('A5:F5')->applyFromArray($sectionStyle);

        $headerStyle = [
            'font' => [
                'bold' => true,
                'color' => ['rgb' => '000000'],
            ],
            'fill' => [
                'fillType' => 'solid',
                'color' => ['rgb' => 'F4BE8D'],
            ],
            'alignment' => [
                'horizontal' => 'center',
                'vertical' => 'center',
            ],
        ];

        $sheet->getStyle('A3:F3')->applyFromArray($headerStyle);
        $sheet->getStyle('A6:F6')->applyFromArray($headerStyle);

        $sheet->getStyle('A1:F40')->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => 'thin',
                    'color' => ['rgb' => 'D1D5DB'],
                ],
            ],
        ]);

        $sheet->getStyle('A1:F40')->applyFromArray([
            'alignment' => [
                'vertical' => 'center',
            ],
        ]);

        $sheet->getRowDimension(1)->setRowHeight(32);
        $sheet->getRowDimension(2)->setRowHeight(28);
        $sheet->getRowDimension(5)->setRowHeight(28);

        $sheet->freezePane('A7');

        return [];
    }
}
