<?php

namespace Database\Seeders;

use App\Models\Curso;
use App\Models\Equivalencia;
use App\Models\EquivalenciaRule;
use App\Models\Estudante;
use App\Models\Opcoes;
use App\Models\OpcoesEstudante;
use App\Models\PlanoEstudo;
use App\Models\PlanoUc;
use App\Models\Uc;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {

        // ----- Persistent data -----
        User::create([
            'name' => 'Admin',
            'email' => 'admin@gespt.local',
            'password' => bcrypt('admin123'),
        ]);

        // TODO: aqui é suposto ter todas as Permições possiveis para o admin as puder usar
        //  ex: can_create_plano, can_create_estudante, can_update_equivalencias, etc...

        if (app()->isProduction()) {
            return;
        }

        // ----- Sample data -----

        Curso::insert([
            ['nome' => 'Engenharia Informática', 'sigla' => 'EI', 'codigo' => '9119'],
            ['nome' => 'Gestão', 'sigla' => 'GT', 'codigo' => '9120'],
        ]);

        PlanoEstudo::insert([
            ['ano' => '2015/16', 'id_curso' => 1],
            ['ano' => '2026/27', 'id_curso' => 1],
            ['ano' => '2015/16', 'id_curso' => 2],
            ['ano' => '2026/27', 'id_curso' => 2],
        ]);

        Estudante::insert([
            ['email' => 'joao@mail.pt', 'plano_estudo_id' => 1],
            ['email' => 'pedro@mail.pt', 'plano_estudo_id' => 1],
            ['email' => 'afonso@mail.pt', 'plano_estudo_id' => 3],
        ]);

        Uc::insert([
            ['id' => 1, 'nome' => 'Programação I', 'sigla' => 'P1', 'ects' => 6, 'horas' => 60, 'id_curso' => 1],
            ['id' => 2, 'nome' => 'Programação II', 'sigla' => 'P2', 'ects' => 6, 'horas' => 60, 'id_curso' => 1],
            ['id' => 3, 'nome' => 'Programação Avançada', 'sigla' => 'PA', 'ects' => 6, 'horas' => 60, 'id_curso' => 1],
            ['id' => 4, 'nome' => 'Sistemas de Informação', 'sigla' => 'SI', 'ects' => 6, 'horas' => 60, 'id_curso' => 1],
            ['id' => 5, 'nome' => 'Matemática A', 'sigla' => 'MATA', 'ects' => 6, 'horas' => 60, 'id_curso' => 1],
            ['id' => 6, 'nome' => 'Matemática B', 'sigla' => 'MATB', 'ects' => 6, 'horas' => 60, 'id_curso' => 1],

            ['id' => 7, 'nome' => 'Gestão de Projetos', 'sigla' => 'GP', 'ects' => 6, 'horas' => 60, 'id_curso' => 2],
            ['id' => 8, 'nome' => 'Marketing', 'sigla' => 'MK', 'ects' => 6, 'horas' => 60, 'id_curso' => 2],

            ['id' => 9, 'nome' => 'Opcão 1', 'sigla' => 'OP1', 'ects' => 6, 'horas' => 60, 'id_curso' => 2],
            ['id' => 10, 'nome' => 'Opcão 2', 'sigla' => 'OP2', 'ects' => 6, 'horas' => 60, 'id_curso' => 2],
        ]);

        EquivalenciaRule::insert([
            ['id_plano' => 2],
            ['id_plano' => 2],
            ['id_plano' => 2],
            ['id_plano' => 2],
            ['id_plano' => 4],
            ['id_plano' => 4],
        ]);

        PlanoUc::insert([
            ['id' => 1, 'id_plano' => 1, 'id_uc' => 1], // p1
            ['id' => 2, 'id_plano' => 1, 'id_uc' => 2], // p2
            //            ['id' => 3, 'id_plano' => 1, 'id_uc' => 3], // pa
            ['id' => 4, 'id_plano' => 1, 'id_uc' => 4], // SI
            ['id' => 5, 'id_plano' => 1, 'id_uc' => 5], // MATA
            ['id' => 6, 'id_plano' => 1, 'id_uc' => 6], // MATB

            //            ['id' => 7, 'id_plano' => 2, 'id_uc' => 1], // p1
            //            ['id' => 8, 'id_plano' => 2, 'id_uc' => 2], // p2
            ['id' => 8, 'id_plano' => 2, 'id_uc' => 7], // GP
            ['id' => 9, 'id_plano' => 2, 'id_uc' => 3], // pa
            ['id' => 10, 'id_plano' => 2, 'id_uc' => 4], // SI
            ['id' => 11, 'id_plano' => 2, 'id_uc' => 5], // MATA

            ['id' => 12, 'id_plano' => 3, 'id_uc' => 7], // GP
            ['id' => 13, 'id_plano' => 3, 'id_uc' => 8], // MK

            // opcionais
            ['id' => 14, 'id_plano' => 4, 'id_uc' => 9], // OP1
            ['id' => 15, 'id_plano' => 4, 'id_uc' => 10], // OP2


            // direct eq
            ['id' => 16, 'id_plano' => 1, 'id_uc' => 9], // OP1
            ['id' => 17, 'id_plano' => 2, 'id_uc' => 9], // OP2
        ]);

        // ignore this because our scope is only one plan to other and not equivalências de estudante so the Ucs are not repeatable
        if (false) {
            /** @noinspection PhpUnreachableStatementInspection */
            Equivalencia::insert([
                // p1 + p2 = PA
                ['id_rule' => 1, 'id_plano_uc' => 1, 'lado' => 'Original'], // p1 plano1
                ['id_rule' => 1, 'id_plano_uc' => 2, 'lado' => 'Original'], // p2 plano1
                ['id_rule' => 1, 'id_plano_uc' => 9, 'lado' => 'Final'], // PA plano2

                // pa = p1 + p2 + matA
                ['id_rule' => 2, 'id_plano_uc' => 3, 'lado' => 'Original'], // PA plano1
                ['id_rule' => 2, 'id_plano_uc' => 7, 'lado' => 'Final'], // p1 plano2
                ['id_rule' => 2, 'id_plano_uc' => 8, 'lado' => 'Final'], // p2 plano2
                ['id_rule' => 2, 'id_plano_uc' => 11, 'lado' => 'Final'], // matA plano2

                // p2 = p1 + p2
                ['id_rule' => 3, 'id_plano_uc' => 2, 'lado' => 'Original'], // p2 plano1
                ['id_rule' => 3, 'id_plano_uc' => 7, 'lado' => 'Final'], // p1 plano2
                ['id_rule' => 3, 'id_plano_uc' => 8, 'lado' => 'Final'], // p2 plano2

                // SI = p1
                ['id_rule' => 4, 'id_plano_uc' => 4, 'lado' => 'Original'], // SI plano1
                ['id_rule' => 4, 'id_plano_uc' => null, 'lado' => 'Original'], // custom
                ['id_rule' => 4, 'id_plano_uc' => 7, 'lado' => 'Final'], // p1 plano2

                // GP = OP1
                ['id_rule' => 5, 'id_plano_uc' => 12, 'lado' => 'Original'], // GP plano3
                ['id_rule' => 5, 'id_plano_uc' => 14, 'lado' => 'Final'], // OP1 plano4

                // MK = OP2
                ['id_rule' => 6, 'id_plano_uc' => 13, 'lado' => 'Original'], // MK plano3
                ['id_rule' => 6, 'id_plano_uc' => 15, 'lado' => 'Final'], // OP2 plano4
            ]);
        }

        Equivalencia::insert([
            // p1 + p2 = PA
            ['id_rule' => 1, 'id_plano_uc' => 1, 'lado' => 'Original'], // p1 plano1
            ['id_rule' => 1, 'id_plano_uc' => 2, 'lado' => 'Original'], // p2 plano1
            ['id_rule' => 1, 'id_plano_uc' => 9, 'lado' => 'Final'], // PA plano2

            // SI = SI + matA
            ['id_rule' => 2, 'id_plano_uc' => 4, 'lado' => 'Original'], // SI plano1
            ['id_rule' => 2, 'id_plano_uc' => 10, 'lado' => 'Final'], // SI plano2
            ['id_rule' => 2, 'id_plano_uc' => 11, 'lado' => 'Final'], // MATA plano2

            // MATB = MATA
            ['id_rule' => 3, 'id_plano_uc' => 6, 'lado' => 'Original'], // MATB plano1
            ['id_rule' => 3, 'id_plano_uc' => 11, 'lado' => 'Final'], // MATA plano2

            // GP = OP1
            ['id_rule' => 5, 'id_plano_uc' => 12, 'lado' => 'Original'], // GP plano3
            ['id_rule' => 5, 'id_plano_uc' => 14, 'lado' => 'Final'], // OP1 plano4

            // MK = OP2
            ['id_rule' => 6, 'id_plano_uc' => 13, 'lado' => 'Original'], // MK plano3
            ['id_rule' => 6, 'id_plano_uc' => 15, 'lado' => 'Final'], // OP2 plano4
        ]);

        Opcoes::insert([
            // op1
            ['id' => 1, 'id_uc_opcional' => 14, 'id_uc' => 1],
            ['id' => 2, 'id_uc_opcional' => 14, 'id_uc' => 2],
            ['id' => 3, 'id_uc_opcional' => 14, 'id_uc' => 3],

            // op2
            ['id' => 4, 'id_uc_opcional' => 15, 'id_uc' => 3],
            ['id' => 5, 'id_uc_opcional' => 15, 'id_uc' => 4],
            ['id' => 6, 'id_uc_opcional' => 15, 'id_uc' => 5],
            ['id' => 7, 'id_uc_opcional' => 15, 'id_uc' => 6],
        ]);

        OpcoesEstudante::insert([
            ['id_estudante' => 3, 'id_opcao' => 3],
            ['id_estudante' => 3, 'id_opcao' => 6],
        ]);
    }
}
