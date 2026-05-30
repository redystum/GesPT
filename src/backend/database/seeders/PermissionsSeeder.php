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
            ['id' => 1, 'nome' => 'Cursos', 'name' => 'Courses'],
            ['id' => 2, 'nome' => 'UCs', 'name' => 'UCs'],
            ['id' => 3, 'nome' => 'Planos de estudo', 'name' => 'Study plans'],
            ['id' => 4, 'nome' => 'Utilizadores', 'name' => 'Users'],
            ['id' => 5, 'nome' => 'Roles', 'name' => 'Roles'],
            ['id' => 6, 'nome' => 'Configurações de sistema', 'name' => 'System settings'],
            ['id' => 7, 'nome' => 'Outras', 'name' => 'Others'],

            // TODO: ao adicionar estudantes e opções...
//            ['id' => -1, 'nome' => 'Estudantes e Opções', 'name' => 'Students and Options'],
        ];

        $policyResources = [
            'Curso' => [
                'slug' => 'cursos', 'id_categoria' => 1, 'singular' => 'Curso', 'plural' => 'Cursos',
                'abilities' => ['viewAny', 'view', 'create', 'update', 'delete']
            ],

            'Uc' => [
                'slug' => 'ucs', 'id_categoria' => 2, 'singular' => 'Unidade Curricular', 'plural' => 'Unidades Curriculares',
                'abilities' => ['viewAny', 'view', 'create', 'update', 'delete']
            ],

            'PlanoEstudo' => [
                'slug' => 'planos_estudo', 'id_categoria' => 3, 'singular' => 'Plano de Estudo', 'plural' => 'Planos de Estudo',
                'abilities' => ['viewAny', 'view', 'create', 'update', 'delete']
            ],

            'User' => [
                'slug' => 'users', 'id_categoria' => 4, 'singular' => 'Utilizador', 'plural' => 'Utilizadores',
                'abilities' => [
                    'viewAny', 'create', 'update', 'delete',
                    ['action' => 'block', 'nome' => 'Bloquear Utilizador', 'descricao' => 'Permite bloquear um utilizador', 'required' => ['view_any']]
                ]
            ],

            'UserRole' => [
                'slug' => 'user_roles', 'id_categoria' => 4, 'singular' => 'Role ao Utilizador', 'plural' => 'Roles ao Utilizador',
                'abilities' => ['add', 'remove']
            ],

            'UserPermissoes' => [
                'slug' => 'user_permissoes', 'id_categoria' => 4, 'singular' => 'Permissão ao Utilizador', 'plural' => 'Permissões ao Utilizador',
                'abilities' => ['add', 'remove']
            ],

            'Role' => [
                'slug' => 'roles', 'id_categoria' => 5, 'singular' => 'Role', 'plural' => 'Roles',
                'abilities' => ['viewAny', 'create', 'update', 'delete']
            ],

            'LoginDominios' => [
                'slug' => 'login_dominios', 'id_categoria' => 6, 'singular' => 'Domínio de Login', 'plural' => 'Domínios de Login',
                'abilities' => ['viewAny', 'create', 'update', 'delete']
            ],

            // TODO: ao adicionar estudantes e opções...
//            'Opcoes' => [
//                'slug' => 'opcoes', 'id_categoria' => 6, 'singular' => 'Opção', 'plural' => 'Opções',
//                'abilities' => ['viewAny', 'view', 'create', 'update', 'delete']
//            ],
        ];

        $abilities = [
            'viewAny' => ['action' => 'view_any', 'nome' => 'Ver lista de :plural', 'descricao' => 'Permite consultar a lista de :plural'],
            'view' => ['action' => 'view', 'nome' => 'Ver :singular', 'descricao' => 'Permite consultar :singular individuais', 'required' => ['view_any']],
            'create' => ['action' => 'create', 'nome' => 'Criar :singular', 'descricao' => 'Permite criar :singular', 'required' => ['view_any']],
            'update' => ['action' => 'update', 'nome' => 'Editar :singular', 'descricao' => 'Permite editar :singular', 'required' => ['view_any', 'view']],
            'delete' => ['action' => 'delete', 'nome' => 'Eliminar :singular', 'descricao' => 'Permite eliminar :singular', 'required' => ['view_any']],
            //'forceDelete' => ['action' => 'force_delete', 'nome' => 'Eliminar permanentemente :singular', 'descricao' => 'Permite eliminar permanentemente :singular'],

            'add' => ['action' => 'add', 'nome' => 'Adicionar :singular', 'descricao' => 'Permite adicionar :singular', 'required' => ['view_any']],
            'remove' => ['action' => 'remove', 'nome' => 'Remover :singular', 'descricao' => 'Permite remover :singular', 'required' => ['view_any']],
        ];

        $permissoes = [];
        foreach ($policyResources as $resource) {
            // Use specified abilities if provided, otherwise use all default abilities
            $resourceAbilities = $resource['abilities'] ?? array_keys($abilities);

            foreach ($resourceAbilities as $abilityDefinition) {
                $ability = null;

                if (is_string($abilityDefinition) && isset($abilities[$abilityDefinition])) {
                    // Ability defined by string key (e.g. 'view', 'create')
                    $ability = $abilities[$abilityDefinition];
                } elseif (is_array($abilityDefinition)) {
                    // Custom ability defined directly in the resource
                    $ability = $abilityDefinition;
                }

                if ($ability) {
                    $required = [];

                    foreach ($ability['required'] ?? [] as $req) {
                        $required[] = $resource['slug'] . '.' . $req;
                    }

                    $permissoes[] = [
                        'id_categoria' => $resource['id_categoria'],
                        'nome' => str_replace(
                            [':singular', ':plural'],
                            [$resource['singular'], $resource['plural']],
                            $ability['nome']
                        ),
                        'permissao' => $resource['slug'] . '.' . $ability['action'],
                        'descricao' => str_replace(
                            [':singular', ':plural'],
                            [$resource['singular'], $resource['plural']],
                            $ability['descricao']
                        ),
                        'required' => $required
                    ];
                }
            }
        }

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
                    'id_categoria' => $p['id_categoria'],
                    'required_roles' => $p['required'] ?? null
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
        foreach (Permissoes::all() as $p) {
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
