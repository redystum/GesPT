<?php

namespace Database\Seeders;

use App\Models\Curso;
use App\Models\Equivalencia;
use App\Models\EquivalenciaRule;
use App\Models\Estudante;
use App\Models\Opcoes;
use App\Models\OpcoesEstudante;
use App\Models\Permissoes;
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


        // TODO: aqui é suposto ter todas as Permições possiveis para o admin as puder usar
        //  ex: can_create_plano, can_create_estudante, can_update_equivalencias, etc...

        $this->call([
            InitialSetupSeeder::class
        ]);

        if (app()->isProduction()) {
            return;
        }


        // TODO: replace insert to updateOrCreate so it does not break the db ex:
        /*
         $permissoes = [
                ['nome' => 'Adicionar Utilizadores', 'permissao' => 'can_create_users', 'descricao' => 'Permite adicionar utilizadores ao sistema'],
                ['nome' => 'Remover Utilizadores', 'permissao' => 'can_delete_users', 'descricao' => 'Permite remover permanentemente utilizadores'],
                // ... add all your other arrays here ...
            ];

            foreach ($permissoes as $p) {
                Permissoes::updateOrCreate(
                    ['permissao' => $p['permissao']], // Unique key to check
                    [
                        'nome' => $p['nome'],
                        'descricao' => $p['descricao']
                    ]
                );
            }
         */
        Permissoes::insert([
            // --- User Management (Internal Control) ---
            ['nome' => 'Adicionar Utilizadores', 'permissao' => 'can_create_users', 'descricao' => 'Permite adicionar utilizadores ao sistema'],
            ['nome' => 'Remover Utilizadores', 'permissao' => 'can_delete_users', 'descricao' => 'Permite remover permanentemente utilizadores'],
            ['nome' => 'Bloquear Utilizadores', 'permissao' => 'can_block_users', 'descricao' => 'Permite suspender o acesso de um utilizador'],
            ['nome' => 'Gerir Cargos', 'permissao' => 'can_manage_roles', 'descricao' => 'Permite Criar, editar e eliminar cargos'],
            ['nome' => 'Atribuir Cargos', 'permissao' => 'can_give_roles', 'descricao' => 'Permite atribuir cargos a utilizadores'],
            ['nome' => 'Atribuir permissões individuais', 'permissao' => 'can_give_permitions', 'descricao' => 'Permite dar permissões específicas a utilizadores'],

            // --- Cursos & UCs ---
            ['nome' => 'Gerir Cursos', 'permissao' => 'can_manage_cursos', 'descricao' => 'Permite criar, editar e eliminar cursos'],
            ['nome' => 'Gerir Unidades Curriculares', 'permissao' => 'can_manage_ucs', 'descricao' => 'Permite gerir as cadeiras (UCs) do sistema'],

            // --- Planos de Estudo ---
            ['nome' => 'Criar Planos de Estudo', 'permissao' => 'can_create_planos', 'descricao' => 'Permite criar novos planos de estudo'],
            ['nome' => 'Editar Planos de Estudo', 'permissao' => 'can_edit_planos', 'descricao' => 'Permite alterar a estrutura de planos existentes'],
            ['nome' => 'Visualizar Planos', 'permissao' => 'can_view_planos', 'descricao' => 'Permite consultar a lista de planos e cadeiras'],

            // --- Estudantes & Opções ---
            ['nome' => 'Gerir Estudantes', 'permissao' => 'can_manage_estudantes', 'descricao' => 'Permite associar estudantes a planos'],
            ['nome' => 'Validar Opções', 'permissao' => 'can_validate_options', 'descricao' => 'Permite validar as escolhas de opcionais dos alunos'],

            // --- Equivalências ---
            ['nome' => 'Criar Equivalências', 'permissao' => 'can_create_equivalencias', 'descricao' => 'Permite criar regras de equivalência entre planos'],
            ['nome' => 'Editar Equivalências', 'permissao' => 'can_edit_equivalencias', 'descricao' => 'Permite alterar regras de equivalência'],
            ['nome' => 'Eliminar Equivalências', 'permissao' => 'can_delete_equivalencias', 'descricao' => 'Permite remover regras de equivalência'],

            // --- Configurações de Sistema ---
            ['nome' => 'Visualizar Logs', 'permissao' => 'can_view_logs', 'descricao' => 'Permite ver o histórico de alterações no sistema'],
        ]);


        // ----- Sample data -----

        Curso::insert([
            ['nome' => 'Engenharia Informática', 'sigla' => 'EI', 'codigo' => '9119', 'name' => 'Computer Engineering'],
            ['nome' => 'Gestão', 'sigla' => 'GT', 'codigo' => '9120', 'name' => 'Management'],
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
            ['id' => 1, 'nome' => 'Programação I', 'sigla' => 'P1', 'ects' => 6, 'horas' => 60, 'id_curso' => 1, 'name' => 'Programming I', 'semestre' => 1],
            ['id' => 2, 'nome' => 'Programação II', 'sigla' => 'P2', 'ects' => 6, 'horas' => 60, 'id_curso' => 1, 'name' => 'Programming II', 'semestre' => 1],
            ['id' => 3, 'nome' => 'Programação Avançada', 'sigla' => 'PA', 'ects' => 6, 'horas' => 60, 'id_curso' => 1, 'name' => 'Advanced Programming', 'semestre' => 1],
            ['id' => 4, 'nome' => 'Sistemas de Informação', 'sigla' => 'SI', 'ects' => 6, 'horas' => 60, 'id_curso' => 1, 'name' => 'Information Systems', 'semestre' => 1],
            ['id' => 5, 'nome' => 'Matemática A', 'sigla' => 'MATA', 'ects' => 6, 'horas' => 60, 'id_curso' => 1, 'name' => 'Mathematics A', 'semestre' => 1],
            ['id' => 6, 'nome' => 'Matemática B', 'sigla' => 'MATB', 'ects' => 6, 'horas' => 60, 'id_curso' => 1, 'name' => 'Mathematics B', 'semestre' => 1],

            ['id' => 7, 'nome' => 'Gestão de Projetos', 'sigla' => 'GP', 'ects' => 6, 'horas' => 60, 'id_curso' => 2, 'name' => 'Project Management', 'semestre' => 1],
            ['id' => 8, 'nome' => 'Marketing', 'sigla' => 'MK', 'ects' => 6, 'horas' => 60, 'id_curso' => 2, 'name' => 'Marketing', 'semestre' => 1],

            ['id' => 9, 'nome' => 'Opcão 1', 'sigla' => 'OP1', 'ects' => 6, 'horas' => 60, 'id_curso' => 2, 'name' => 'Option 1', 'semestre' => 2],
            ['id' => 10, 'nome' => 'Opcão 2', 'sigla' => 'OP2', 'ects' => 6, 'horas' => 60, 'id_curso' => 2, 'name' => 'Option 2', 'semestre' => 2],
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
            ['id' => 5, 'id_plano' => 1, 'id_uc' => 5], // MATA // Este pode ser eliminado, mas ao existir vai fazer ligação direta e adicionado ás equivalências com as outras
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
            ['id_rule' => 1, 'id_plano_uc' => 1, 'lado' => 'Original', 'observacao' => "aaaaaa"], // p1 plano1
            ['id_rule' => 1, 'id_plano_uc' => 2, 'lado' => 'Original', 'observacao' => null], // p2 plano1
            ['id_rule' => 1, 'id_plano_uc' => 9, 'lado' => 'Final', 'observacao' => 'alguma observação importante aqui'], // PA plano2

            // SI = SI + matA
            ['id_rule' => 2, 'id_plano_uc' => 4, 'lado' => 'Original', 'observacao' => null], // SI plano1
            ['id_rule' => 2, 'id_plano_uc' => 10, 'lado' => 'Final', 'observacao' => null], // SI plano2
            ['id_rule' => 2, 'id_plano_uc' => 11, 'lado' => 'Final', 'observacao' => 'Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. At vero eos et accusam et justo duo dolores et ea rebum. Stet clita kasd gubergren, no sea takimata'], // MATA plano2

            // MATB = MATA
            ['id_rule' => 3, 'id_plano_uc' => 6, 'lado' => 'Original', 'observacao' => null], // MATB plano1
            ['id_rule' => 3, 'id_plano_uc' => 11, 'lado' => 'Final', 'observacao' => null], // MATA plano2

            // GP = OP1
            ['id_rule' => 5, 'id_plano_uc' => 12, 'lado' => 'Original', 'observacao' => null], // GP plano3
            ['id_rule' => 5, 'id_plano_uc' => 14, 'lado' => 'Final', 'observacao' => 'alguma observação importante aqui'], // OP1 plano4

            // MK = OP2
            ['id_rule' => 6, 'id_plano_uc' => 13, 'lado' => 'Original', 'observacao' => null], // MK plano3
            ['id_rule' => 6, 'id_plano_uc' => 15, 'lado' => 'Final', 'observacao' => null], // OP2 plano4
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
