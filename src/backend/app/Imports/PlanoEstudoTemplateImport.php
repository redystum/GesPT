<?php

namespace App\Imports;

use App\Models\Curso;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;

class PlanoEstudoTemplateImport implements ToCollection
{
    public array $data = [];

    public function __construct(private Curso $curso)
    {
    }

    public function collection(Collection $rows): void
    {
        $rows = $rows->toArray();

        $anoPlano = $rows[3][4] ?? null; // E4

        $ucs = [];

        foreach (array_slice($rows, 6) as $row) {
            if (empty($row[0]) && empty($row[2])) {
                continue;
            }

            $ucs[] = [
                'nome' => $row[0] ?? '',
                'name' => $row[1] ?? '',
                'sigla' => $row[2] ?? '',
                'ects' => $row[3] ?? null,
                'horas_trabalho_totais' => $row[4] ?? null,
                'semestre' => $row[5] ?? null,
                'ano' => $row[6] ?? null,
                't' => $row[7] ?? 0,
                'tp' => $row[8] ?? 0,
                'pl' => $row[9] ?? 0,
                'tc' => $row[10] ?? 0,
                's' => $row[11] ?? 0,
                'e' => $row[12] ?? 0,
                'ot' => $row[13] ?? 0,
                'o' => $row[14] ?? 0,
                'isNew' => true,
            ];
        }

        $this->data = [
            'course' => [
                'isNew' => false,
                'data' => [
                    'id' => $this->curso->id,
                    'nome' => $this->curso->nome,
                    'name' => $this->curso->name,
                    'sigla' => $this->curso->sigla,
                    'codigo' => $this->curso->codigo,
                ],
            ],
            'plan' => [
                'ano' => $anoPlano,
                'isValid' => true,
            ],
            'ucs' => $ucs,
            'equivalences' => [
                'prevPlanId' => '',
                'equivalences' => [],
            ],
        ];
    }
}
