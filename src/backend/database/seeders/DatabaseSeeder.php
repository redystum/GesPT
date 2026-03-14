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
        // ── Users ────────────────────────────────────────────────────────
        User::factory()->create([
            'name'  => 'Admin',
            'email' => 'admin@gespt.local',
        ]);
        User::factory(9)->create();

        // ── Roles & Permissions ──────────────────────────────────────────
        $permissoes = Permissoes::factory(10)->create();
        $roles      = Role::factory(3)->create();

        foreach ($roles as $role) {
            $permissoes->random(rand(2, 5))->each(
                fn ($p) => RolePermissoes::factory()->create([
                    'id_role'      => $role->id,
                    'id_permissao' => $p->id,
                ])
            );
        }

        User::all()->each(function ($user) use ($permissoes) {
            $permissoes->random(rand(1, 3))->each(
                fn ($p) => UserPermissoes::factory()->create([
                    'id_user'      => $user->id,
                    'id_permiscao' => $p->id,
                ])
            );
        });

        // ── Academic structure ───────────────────────────────────────────
        $cursos = Curso::factory(4)->create();

        $ucs = $cursos->flatMap(
            fn ($curso) => Uc::factory(6)->create(['id_curso' => $curso->id])
        );

        $planos = $cursos->flatMap(
            fn ($curso) => PlanoEstudo::factory(2)->create(['id_curso' => $curso->id])
        );

        $planos->each(function ($plano) use ($ucs) {
            $ucs->random(rand(4, 8))->each(
                fn ($uc) => PlanoCadeira::factory()->create([
                    'id_plano' => $plano->id,
                    'id_uc'    => $uc->id,
                ])
            );
        });

        // ── Students ─────────────────────────────────────────────────────
        $estudantes = $planos->flatMap(
            fn ($plano) => Estudante::factory(5)->create(['plano_de_estudo' => $plano->id])
        );

        // ── Optional UC choices ──────────────────────────────────────────
        if ($ucs->count() >= 2) {
            $opcoes = collect();
            for ($i = 0; $i < 10; $i++) {
                [$ucA, $ucB] = $ucs->random(2)->values();
                $opcoes->push(Opcoes::factory()->create([
                    'id_uc_opcional' => $ucA->id,
                    'id_uc'          => $ucB->id,
                ]));
            }

            $estudantes->random(min(10, $estudantes->count()))->each(
                fn ($est) => OpcoesEstudante::factory()->create([
                    'id_estudante' => $est->id,
                    'id_opcao'     => $opcoes->random()->id,
                ])
            );
        }

        // ── Equivalencias ────────────────────────────────────────────────
        $rules = $planos->flatMap(
            fn ($plano) => EquivalenciaRule::factory(2)->create(['id_plano' => $plano->id])
        );

        $rules->each(function ($rule) use ($ucs) {
            foreach (['Original', 'Final'] as $lado) {
                Equivalencia::factory()->create([
                    'id_rule' => $rule->id,
                    'id_uc'   => $ucs->random()->id,
                    'lado'    => $lado,
                    'custom'  => null,
                ]);
            }
        });
    }
}
