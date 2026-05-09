<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Permissoes;
use App\Models\Role;
use App\Models\RolePermissoes;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            ['id' => 1, 'nome' => 'Utilizadores', 'name' => 'Users'],
            ['id' => 2, 'nome' => 'Cursos e UCs', 'name' => 'Courses and UCs'],
            ['id' => 3, 'nome' => 'Planos de estudo', 'name' => 'Study plans'],
            ['id' => 4, 'nome' => 'Estudantes e Opções', 'name' => 'Students and Options'],
            ['id' => 5, 'nome' => 'Equivalências', 'name' => 'Equivalences'],
            ['id' => 6, 'nome' => 'Configurações de sistema', 'name' => 'System settings'],
            ['id' => 7, 'nome' => 'Outras', 'name' => 'Others'],
        ];

        $permissoes = [
            // --- User Management (Internal Control) ---
            ['id_categoria' => 1, 'nome' => 'Adicionar Utilizadores', 'permissao' => 'can_create_users', 'descricao' => 'Permite adicionar utilizadores ao sistema'],
            ['id_categoria' => 1, 'nome' => 'Remover Utilizadores', 'permissao' => 'can_delete_users', 'descricao' => 'Permite remover permanentemente utilizadores'],
            ['id_categoria' => 1, 'nome' => 'Bloquear Utilizadores', 'permissao' => 'can_block_users', 'descricao' => 'Permite suspender o acesso de um utilizador'],
            ['id_categoria' => 1, 'nome' => 'Gerir Cargos', 'permissao' => 'can_manage_roles', 'descricao' => 'Permite Criar, editar e eliminar cargos'],
            ['id_categoria' => 1, 'nome' => 'Atribuir Cargos', 'permissao' => 'can_give_roles', 'descricao' => 'Permite atribuir cargos a utilizadores'],
            ['id_categoria' => 1, 'nome' => 'Atribuir permissões individuais', 'permissao' => 'can_give_permitions', 'descricao' => 'Permite dar permissões específicas a utilizadores'],

            // --- Cursos & UCs ---
            ['id_categoria' => 2, 'nome' => 'Gerir Cursos', 'permissao' => 'can_manage_cursos', 'descricao' => 'Permite criar, editar e eliminar cursos'],
            ['id_categoria' => 2, 'nome' => 'Gerir Unidades Curriculares', 'permissao' => 'can_manage_ucs', 'descricao' => 'Permite gerir as cadeiras (UCs) do sistema'],

            // --- Planos de Estudo ---
            ['id_categoria' => 3, 'nome' => 'Criar Planos de Estudo', 'permissao' => 'can_create_planos', 'descricao' => 'Permite criar novos planos de estudo'],
            ['id_categoria' => 3, 'nome' => 'Editar Planos de Estudo', 'permissao' => 'can_edit_planos', 'descricao' => 'Permite alterar a estrutura de planos existentes'],
            ['id_categoria' => 3, 'nome' => 'Visualizar Planos', 'permissao' => 'can_view_planos', 'descricao' => 'Permite consultar a lista de planos e cadeiras'],

            // --- Estudantes & Opções ---
            ['id_categoria' => 4, 'nome' => 'Gerir Estudantes', 'permissao' => 'can_manage_estudantes', 'descricao' => 'Permite associar estudantes a planos'],
            ['id_categoria' => 4, 'nome' => 'Validar Opções', 'permissao' => 'can_validate_options', 'descricao' => 'Permite validar as escolhas de opcionais dos alunos'],

            // --- Equivalências ---
            ['id_categoria' => 5, 'nome' => 'Criar Equivalências', 'permissao' => 'can_create_equivalencias', 'descricao' => 'Permite criar regras de equivalência entre planos'],
            ['id_categoria' => 5, 'nome' => 'Editar Equivalências', 'permissao' => 'can_edit_equivalencias', 'descricao' => 'Permite alterar regras de equivalência'],
            ['id_categoria' => 5, 'nome' => 'Eliminar Equivalências', 'permissao' => 'can_delete_equivalencias', 'descricao' => 'Permite remover regras de equivalência'],

            // --- Configurações de Sistema ---
            ['id_categoria' => 6, 'nome' => 'Visualizar Logs', 'permissao' => 'can_view_logs', 'descricao' => 'Permite ver o histórico de alterações no sistema'],
        ];

        $roles = [
            ['id' => 1, 'nome' => 'Administrador', 'descricao' => 'Acesso total ao sistema.'],
        ];

        foreach ($categories as $c) {
            Category::updateOrCreate(
                ['id' => $c['id']],
                [
                    'id' => $c['id'],
                    'nome' => $c['nome'],
                    'name' => $c['name']
                ]
            );
        }

        foreach ($permissoes as $p) {
            Permissoes::updateOrCreate(
                ['permissao' => $p['permissao']],
                [
                    'nome' => $p['nome'],
                    'permissao' => $p['permissao'],
                    'descricao' => $p['descricao'],
                    'id_categoria' => $p['id_categoria']
                ]
            );
        }

        foreach ($roles as $r) {
            Role::updateOrCreate(
                ['id' => $r['id']],
                [
                    'id' => $r['id'],
                    'nome' => $r['nome'],
                    'descricao' => $r['descricao']
                ]
            );
        }

        // give all permissions to admin
        foreach(Permissoes::all() as $p){
            RolePermissoes::updateOrCreate(
                ['id_role' => 1, 'id_permissao' => $p->id],
                [
                    'id_role' => 1,
                    'id_permissao' => $p->id
                ]
            );
        }
    }
}
