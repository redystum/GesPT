<?php

namespace Database\Seeders;

use App\Models\Curso;
use App\Models\Equivalencia;
use App\Models\EquivalenciaRule;
use App\Models\Estudante;
use App\Models\Opcoes;
use App\Models\OpcoesEstudante;
use App\Models\Permissoes;
use App\Models\PlanoCadeira;
use App\Models\PlanoEstudo;
use App\Models\Role;
use App\Models\RolePermissoes;
use App\Models\Uc;
use App\Models\User;
use App\Models\UserPermissoes;
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
            ['ano' => '2015/16', 'id_curso' => 1,],
            ['ano' => '2026/27', 'id_curso' => 1,],
            ['ano' => '2015/16', 'id_curso' => 2,],
            ['ano' => '2026/27', 'id_curso' => 2,],
        ]);

        Estudante::insert([
            ['email' => 'joao@mail.pt', 'plano_estudo_id' => 1,],
            ['email' => 'pedro@mail.pt', 'plano_estudo_id' => 1,],
            ['email' => 'afonso@mail.pt', 'plano_estudo_id' => 3,],
        ]);

        Uc::insert([
            ['id' => 1, 'nome' => 'Programação I', 'sigla' => 'P1', 'ects' => 6, 'horas' => 60, 'id_curso' => 1,],
            ['id' => 2, 'nome' => 'Programação II', 'sigla' => 'P2', 'ects' => 6, 'horas' => 60, 'id_curso' => 1,],
            ['id' => 3, 'nome' => 'Programação Avançada', 'sigla' => 'PA', 'ects' => 6, 'horas' => 60, 'id_curso' => 1,],
            ['id' => 4, 'nome' => 'Sistemas de Informação', 'sigla' => 'SI', 'ects' => 6, 'horas' => 60, 'id_curso' => 1,],
            ['id' => 5, 'nome' => 'Matemática A', 'sigla' => 'MATA', 'ects' => 6, 'horas' => 60, 'id_curso' => 1,],
            ['id' => 6, 'nome' => 'Matemática B', 'sigla' => 'MATB', 'ects' => 6, 'horas' => 60, 'id_curso' => 1,],

            ['id' => 7, 'nome' => 'Gestão de Projetos', 'sigla' => 'GP', 'ects' => 6, 'horas' => 60, 'id_curso' => 2,],
            ['id' => 8, 'nome' => 'Marketing', 'sigla' => 'MK', 'ects' => 6, 'horas' => 60, 'id_curso' => 2,],

            ['id' => 9, 'nome' => 'Opcão 1', 'sigla' => 'OP1', 'ects' => 6, 'horas' => 60, 'id_curso' => 2,],
            ['id' => 10, 'nome' => 'Opcão 2', 'sigla' => 'OP2', 'ects' => 6, 'horas' => 60, 'id_curso' => 2,],
        ]);

        EquivalenciaRule::insert([
            ['id_plano' => 1],
            ['id_plano' => 1],
            ['id_plano' => 1],
            ['id_plano' => 1],
            ['id_plano' => 3],
            ['id_plano' => 3],
        ]);

        Equivalencia::insert([
            // p1 + p2 = PA
            ['id_rule' => 1, 'id_uc' => 1, 'lado' => "Original"], // p1
            ['id_rule' => 1, 'id_uc' => 2, 'lado' => "Original"], // p2
            ['id_rule' => 1, 'id_uc' => 3, 'lado' => "Final"], // PA

            // pa = p1 + p2 + matA
            ['id_rule' => 2, 'id_uc' => 3, 'lado' => "Original"], // PA
            ['id_rule' => 2, 'id_uc' => 1, 'lado' => "Final"], // p1
            ['id_rule' => 2, 'id_uc' => 2, 'lado' => "Final"], // p2
            ['id_rule' => 2, 'id_uc' => 5, 'lado' => "Final"], // matA

            // p2 = p1 + p2
            ['id_rule' => 3, 'id_uc' => 2, 'lado' => "Original"], // p2
            ['id_rule' => 3, 'id_uc' => 1, 'lado' => "Final"], // p1
            ['id_rule' => 3, 'id_uc' => 2, 'lado' => "Final"], // p2

            // SI = p1
            ['id_rule' => 4, 'id_uc' => 4, 'lado' => "Original"], // SI
            ['id_rule' => 4, 'id_uc' => 1, 'lado' => "Final"], // p1

            // GP = OP1
            ['id_rule' => 5, 'id_uc' => 7, 'lado' => "Original"], // GP
            ['id_rule' => 5, 'id_uc' => 9, 'lado' => "Final"], // OP1

            // MK = OP2
            ['id_rule' => 6, 'id_uc' => 8, 'lado' => "Original"], // MK
            ['id_rule' => 6, 'id_uc' => 10, 'lado' => "Final"], // OP2
        ]);

        Opcoes::insert([
            // op1
            ['id' => 1, 'id_uc_opcional' => 9, 'id_uc' => 1],
            ['id' => 2, 'id_uc_opcional' => 9, 'id_uc' => 2],
            ['id' => 3, 'id_uc_opcional' => 9, 'id_uc' => 3],

            // op2
            ['id' => 4, 'id_uc_opcional' => 10, 'id_uc' => 3],
            ['id' => 5, 'id_uc_opcional' => 10, 'id_uc' => 4],
            ['id' => 6, 'id_uc_opcional' => 10, 'id_uc' => 5],
            ['id' => 7, 'id_uc_opcional' => 10, 'id_uc' => 6],
        ]);

        OpcoesEstudante::insert([
            ['id_estudante' => 3, 'id_opcao' => 3],
            ['id_estudante' => 3, 'id_opcao' => 6],
        ]);

        PlanoCadeira::insert([
            ['id_plano' => 1, 'id_uc' => 1],
            ['id_plano' => 1, 'id_uc' => 2],
            ['id_plano' => 1, 'id_uc' => 3],
            ['id_plano' => 1, 'id_uc' => 4],
            ['id_plano' => 1, 'id_uc' => 5],
            ['id_plano' => 1, 'id_uc' => 6],

            ['id_plano' => 2, 'id_uc' => 1],
            ['id_plano' => 2, 'id_uc' => 2],
            ['id_plano' => 2, 'id_uc' => 3],
            ['id_plano' => 2, 'id_uc' => 4],
            ['id_plano' => 2, 'id_uc' => 5],

            ['id_plano' => 3, 'id_uc' => 7],
            ['id_plano' => 3, 'id_uc' => 8],
        ]);

        PlanoCadeira::insert([
            ['id_plano' => 4, 'id_uc' => 9, 'opcional' => true],
            ['id_plano' => 4, 'id_uc' => 10, 'opcional' => true],
        ]);

    }
}
