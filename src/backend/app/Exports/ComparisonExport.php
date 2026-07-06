<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class ComparisonExport implements FromArray, WithTitle, WithStyles, WithColumnWidths
{
    private $data;
    private $curso;
    private $planoInicial;
    private $planoFinal;
    private $isEnglish;

    public function __construct(array $data, $curso, $planoInicial, $planoFinal, bool $isEnglish = false)
    {
        $this->data = $data;
        $this->curso = $curso;
        $this->planoInicial = $planoInicial;
        $this->planoFinal = $planoFinal;
        $this->isEnglish = $isEnglish;
    }

    public function title(): string
    {
        return $this->isEnglish ? 'Study Plan Comparison' : 'Comparação de Planos';
    }

    private function formatUcData($ucData): array
    {
        if (!$ucData || !isset($ucData['uc'])) {
            return ['', '', '', '', ''];
        }

        $uc = $ucData['uc'];
        $nome = $this->isEnglish ? ($uc['name'] ?? $uc['nome']) : $uc['nome'];

        if (!empty($ucData['opcao_escolhida'])) {
            $opcaoNome = $this->isEnglish ? ($ucData['opcao_escolhida']['name'] ?? $ucData['opcao_escolhida']['nome']) : $ucData['opcao_escolhida']['nome'];
            $nome .= ' -> ' . $opcaoNome;
        }

        $semesterVal = (int) $uc['semestre'];
        $semesterText = match($semesterVal) {
            1 => $this->isEnglish ? '1st Sem.' : '1º Sem.',
            2 => $this->isEnglish ? '2nd Sem.' : '2º Sem.',
            3 => $this->isEnglish ? 'Both' : 'Ambos',
            default => '-'
        };

        return [
            $nome,
            $uc['sigla'],
            $uc['ects'],
            $semesterText,
            $ucData['observacao'] ?? ''
        ];
    }

    public function array(): array
    {
        $courseName = $this->isEnglish ? ($this->curso->name ?? $this->curso->nome) : $this->curso->nome;
        
        $rows = [
            [$this->isEnglish ? 'Curricular Plan Comparison' : 'Comparação de Planos de Estudo'],
            [
                ($this->isEnglish ? 'Course: ' : 'Curso: ') . $courseName . 
                ' (' . ($this->isEnglish ? 'Acronym: ' : 'Sigla: ') . $this->curso->sigla . 
                ', ' . ($this->isEnglish ? 'Code: ' : 'Código: ') . $this->curso->codigo . ')'
            ],
            [
                $this->isEnglish ? 'Initial Plan: ' : 'Plano Inicial: ',
                $this->planoInicial->ano,
                $this->planoInicial->ects_sum . ' ECTS',
                '',
                '',
                $this->isEnglish ? 'Target Plan: ' : 'Plano de Destino: ',
                $this->planoFinal ? $this->planoFinal->ano : ($this->isEnglish ? 'Discontinued' : 'Descontinuado'),
                $this->planoFinal ? ($this->planoFinal->ects_sum . ' ECTS') : '',
                '',
                ''
            ],
            [
                $this->isEnglish ? 'UC Name (Initial)' : 'Nome da UC (Inicial)',
                $this->isEnglish ? 'Acronym' : 'Sigla',
                'ECTS',
                $this->isEnglish ? 'Semester' : 'Semestre',
                $this->isEnglish ? 'Observations' : 'Observações',
                $this->isEnglish ? 'UC Name (Target)' : 'Nome da UC (Destino)',
                $this->isEnglish ? 'Acronym' : 'Sigla',
                'ECTS',
                $this->isEnglish ? 'Semester' : 'Semestre',
                $this->isEnglish ? 'Observations' : 'Observações'
            ]
        ];

        foreach ($this->data as $pair) {
            $inicialList = $pair['inicial'] ?? [];
            $finalList = $pair['final'] ?? [];

            if (empty($inicialList) && empty($finalList)) {
                continue;
            }

            $max = max(count($inicialList), count($finalList));
            for ($i = 0; $i < $max; $i++) {
                $initUc = $inicialList[$i] ?? null;
                $finalUc = $finalList[$i] ?? null;

                // Clear fields for subsequent rows of a merged column to avoid duplicate data warnings
                if (count($inicialList) > 1 && count($finalList) === 1 && $i > 0) {
                    $finalUc = null;
                }
                if (count($finalList) > 1 && count($inicialList) === 1 && $i > 0) {
                    $initUc = null;
                }

                $initRow = $this->formatUcData($initUc);
                $finalRow = $this->formatUcData($finalUc);

                if ($i === 0) {
                    if (!$initUc && count($inicialList) === 0) {
                        $initRow[0] = $this->isEnglish ? 'No prior equivalent' : 'Sem antecedente';
                    }
                    if (!$finalUc && count($finalList) === 0 && $this->planoFinal) {
                        $finalRow[0] = $this->isEnglish ? 'Discontinued unit' : 'UC descontinuada';
                    }
                }

                $rows[] = array_merge($initRow, $finalRow);
            }
        }

        return $rows;
    }

    public function columnWidths(): array
    {
        return [
            'A' => 32,
            'B' => 12,
            'C' => 8,
            'D' => 12,
            'E' => 20,
            'F' => 32,
            'G' => 12,
            'H' => 8,
            'I' => 12,
            'J' => 20,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // Merge title rows
        $sheet->mergeCells('A1:J1');
        $sheet->mergeCells('A2:J2');

        // Style title block (Deep Indigo/Blue)
        $sheet->getStyle('A1:J1')->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 16,
                'color' => ['rgb' => 'FFFFFF'],
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'color' => ['rgb' => '1E3A8A'], // Deep Blue
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ]);

        $sheet->getStyle('A2:J2')->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 12,
                'color' => ['rgb' => '1E3A8A'],
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'color' => ['rgb' => 'EFF6FF'], // Light blue accent
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ]);

        // Style Plan details (Row 3)
        $sheet->getStyle('A3:E3')->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['rgb' => '1E40AF'],
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'color' => ['rgb' => 'DBEAFE'], // Blue 100
            ],
            'alignment' => [
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ]);

        $sheet->getStyle('F3:J3')->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['rgb' => '3730A3'],
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'color' => ['rgb' => 'E0E7FF'], // Indigo 100
            ],
            'alignment' => [
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ]);

        // Style Table Column Headers (Row 4)
        $sheet->getStyle('A4:E4')->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['rgb' => '1E3A8A'],
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'color' => ['rgb' => '93C5FD'], // Blue 300
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ]);

        $sheet->getStyle('F4:J4')->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['rgb' => '312E81'],
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'color' => ['rgb' => 'A5B4FC'], // Indigo 300
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ]);

        // Row Heights
        $sheet->getRowDimension(1)->setRowHeight(36);
        $sheet->getRowDimension(2)->setRowHeight(26);
        $sheet->getRowDimension(3)->setRowHeight(24);
        $sheet->getRowDimension(4)->setRowHeight(24);

        // Border configuration
        $thinBorder = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => 'E5E7EB'],
                ],
            ],
        ];

        // Format comparison rows
        $currentRow = 5;
        $isPairEven = false;
        
        foreach ($this->data as $pair) {
            $inicialList = $pair['inicial'] ?? [];
            $finalList = $pair['final'] ?? [];

            if (empty($inicialList) && empty($finalList)) {
                continue;
            }

            $max = max(count($inicialList), count($finalList));
            
            // Background fill for striping pairs
            $bgColor = $isPairEven ? 'F9FAFB' : 'FFFFFF'; // Off-white and White
            
            $startRow = $currentRow;

            for ($i = 0; $i < $max; $i++) {
                $initUc = $inicialList[$i] ?? null;
                $finalUc = $finalList[$i] ?? null;

                $sheet->getRowDimension($currentRow)->setRowHeight(20);

                // Apply styling to initial and final columns
                $sheet->getStyle("A{$currentRow}:J{$currentRow}")->applyFromArray($thinBorder);
                
                // Set cell alignments
                $sheet->getStyle("A{$currentRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
                $sheet->getStyle("B{$currentRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle("C{$currentRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle("D{$currentRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle("E{$currentRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
                
                $sheet->getStyle("F{$currentRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
                $sheet->getStyle("G{$currentRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle("H{$currentRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle("I{$currentRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle("J{$currentRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);

                // Fill background
                $sheet->getStyle("A{$currentRow}:J{$currentRow}")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB($bgColor);

                // Specific color tints for special text labels
                if ($i === 0) {
                    if (!$initUc && count($inicialList) === 0) {
                        $sheet->getStyle("A{$currentRow}")->getFont()->setItalic(true)->getColor()->setRGB('9CA3AF');
                    }
                    if (!$finalUc && count($finalList) === 0 && $this->planoFinal) {
                        $sheet->getStyle("F{$currentRow}")->getFont()->setItalic(true)->getColor()->setRGB('EF4444');
                    }
                }

                $currentRow++;
            }

            // Perform merges if equivalence has multiple elements on one side and a single on the other
            if ($max > 1) {
                $endRow = $currentRow - 1;
                if (count($inicialList) > 1 && count($finalList) === 1) {
                    $sheet->mergeCells("F{$startRow}:F{$endRow}");
                    $sheet->mergeCells("G{$startRow}:G{$endRow}");
                    $sheet->mergeCells("H{$startRow}:H{$endRow}");
                    $sheet->mergeCells("I{$startRow}:I{$endRow}");
                    $sheet->mergeCells("J{$startRow}:J{$endRow}");
                    $sheet->getStyle("F{$startRow}:J{$endRow}")->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
                } else if (count($finalList) > 1 && count($inicialList) === 1) {
                    $sheet->mergeCells("A{$startRow}:A{$endRow}");
                    $sheet->mergeCells("B{$startRow}:B{$endRow}");
                    $sheet->mergeCells("C{$startRow}:C{$endRow}");
                    $sheet->mergeCells("D{$startRow}:D{$endRow}");
                    $sheet->mergeCells("E{$startRow}:E{$endRow}");
                    $sheet->getStyle("A{$startRow}:E{$endRow}")->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
                }
            }

            $isPairEven = !$isPairEven;
        }

        // Add a vertical divider in the middle
        for ($r = 3; $r < $currentRow; $r++) {
            $sheet->getStyle("E{$r}")->getBorders()->getRight()->setBorderStyle(Border::BORDER_MEDIUM)->getColor()->setRGB('9CA3AF');
        }

        return [];
    }
}
