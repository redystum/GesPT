<?php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\User;

class AuditLogResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $causer = $this->causer;
        $causerLabel = 'System';
        if ($causer instanceof User) {
            $roles = $causer->roles->pluck('nome')->implode(', ');
            $causerLabel = $causer->name . ($roles ? " ($roles)" : '');
        }

        $time = $this->created_at->setTimezone('Europe/Lisbon')->format('H:i j/n/Y');
        $lang = $request->header('Accept-Language') === 'pt' ? 'pt' : 'en';

        $sentence = '';
        $details = null;

        $logName = $this->log_name;
        $event = $this->event;

        if ($logName === 'login_failed') {
            $reason = $this->getExtraProperty('reason');
            $email = $this->getExtraProperty('email');
            $userName = $this->getExtraProperty('user_name');

            if ($reason === 'unauthorized_domain') {
                if ($lang === 'pt') {
                    $sentence = "$email tentou iniciar sessão mas falhou porque não tem permissões às $time";
                } else {
                    $sentence = "$email tryed login witch failed because dont have permitions at $time";
                }
            } elseif ($reason === 'blocked') {
                if ($lang === 'pt') {
                    $sentence = "$userName tentou iniciar sessão mas falhou porque está bloqueado às $time";
                } else {
                    $sentence = "$userName tryed to login but failed beacuse its bloqued at $time";
                }
            } else {
                if ($lang === 'pt') {
                    $sentence = "$email tentou iniciar sessão mas falhou devido a credenciais inválidas às $time";
                } else {
                    $sentence = "$email tryed to login but failed because of invalid credentials at $time";
                }
            }
        } elseif ($logName === 'user_roles') {
            $target = $this->getExtraProperty('target_name');
            $role = $this->getExtraProperty('role_name');
            $action = $this->getExtraProperty('action');

            if ($action === 'added') {
                if ($lang === 'pt') {
                    $sentence = "A role $role foi adicionada a $target por $causerLabel às $time";
                } else {
                    $sentence = "$role role was added to $target by $causerLabel at $time";
                }
            } else {
                if ($lang === 'pt') {
                    $sentence = "A role $role foi removida de $target por $causerLabel às $time";
                } else {
                    $sentence = "$role role was removed from $target by $causerLabel at $time";
                }
            }
        } elseif ($logName === 'user_permissions') {
            $target = $this->getExtraProperty('target_name');
            $permissions = $this->getExtraProperty('permissions') ?? [];
            $action = $this->getExtraProperty('action');
            $count = count($permissions);

            if ($action === 'added') {
                if ($lang === 'pt') {
                    $permWord = $count === 1 ? "permissão" : "permissões";
                    $sentence = "$count $permWord adicionada(s) a $target por $causerLabel às $time";
                } else {
                    $permWord = $count === 1 ? "permission" : "permissions";
                    $sentence = "$count $permWord added to $target by $causerLabel at $time";
                }
            } else {
                if ($lang === 'pt') {
                    $permWord = $count === 1 ? "permissão" : "permissões";
                    $sentence = "$count $permWord removida(s) de $target por $causerLabel às $time";
                } else {
                    $permWord = $count === 1 ? "permission" : "permissions";
                    $sentence = "$count $permWord removed from $target by $causerLabel at $time";
                }
            }
            $details = $permissions;
        } elseif ($this->subject_type === 'App\Models\Uc') {
            $ucName = $this->getExtraProperty('attributes.nome') ?? $this->getExtraProperty('old.nome') ?? 'UC';
            $ucId = $this->subject_id;
            $frontendUrl = env('FRONTEND_URL', 'http://localhost:8080');
            $ucLink = "[$ucName]($frontendUrl/uc/$ucId)";

            if ($event === 'created') {
                if ($lang === 'pt') {
                    $sentence = "o utilizador $causerLabel criou a UC $ucLink às $time";
                } else {
                    $sentence = "$causerLabel created the UC $ucLink at $time";
                }
            } elseif ($event === 'updated') {
                if ($lang === 'pt') {
                    $sentence = "o utilizador $causerLabel atualizou a UC $ucLink às $time";
                } else {
                    $sentence = "$causerLabel updated the UC $ucLink at $time";
                }
            } elseif ($event === 'deleted') {
                if ($lang === 'pt') {
                    $sentence = "o utilizador $causerLabel eliminou a UC $ucName às $time";
                } else {
                    $sentence = "$causerLabel deleted the UC $ucName at $time";
                }
            }
        } elseif ($this->subject_type === 'App\Models\Curso') {
            $cursoName = $this->getExtraProperty('attributes.nome') ?? $this->getExtraProperty('old.nome') ?? 'Curso';
            if ($event === 'created') {
                if ($lang === 'pt') {
                    $sentence = "o utilizador $causerLabel criou o Curso $cursoName às $time";
                } else {
                    $sentence = "$causerLabel created the Course $cursoName at $time";
                }
            } elseif ($event === 'updated') {
                if ($lang === 'pt') {
                    $sentence = "o utilizador $causerLabel atualizou o Curso $cursoName às $time";
                } else {
                    $sentence = "$causerLabel updated the Course $cursoName at $time";
                }
            } elseif ($event === 'deleted') {
                if ($lang === 'pt') {
                    $sentence = "o utilizador $causerLabel eliminou o Curso $cursoName às $time";
                } else {
                    $sentence = "$causerLabel deleted the Course $cursoName at $time";
                }
            }
        } elseif ($this->subject_type === 'App\Models\PlanoEstudo') {
            $plano = $this->subject ?? \App\Models\PlanoEstudo::withTrashed()->find($this->subject_id);
            $planoName = 'Plano de Estudos';
            if ($plano) {
                $planoName = 'Plano de Estudos de ' . ($plano->curso?->sigla ?? 'Curso') . ' (' . $plano->ano . ')';
            }
            if ($event === 'created') {
                if ($lang === 'pt') {
                    $sentence = "o utilizador $causerLabel criou o $planoName às $time";
                } else {
                    $sentence = "$causerLabel created the $planoName at $time";
                }
            } elseif ($event === 'updated') {
                if ($lang === 'pt') {
                    $sentence = "o utilizador $causerLabel atualizou o $planoName às $time";
                } else {
                    $sentence = "$causerLabel updated the $planoName at $time";
                }
            } elseif ($event === 'deleted') {
                if ($lang === 'pt') {
                    $sentence = "o utilizador $causerLabel eliminou o $planoName às $time";
                } else {
                    $sentence = "$causerLabel deleted the $planoName at $time";
                }
            }
        } else {
            $subjectLabel = str_replace('App\\Models\\', '', $this->subject_type ?? '');
            if ($lang === 'pt') {
                $sentence = "Ação '$event' no modelo '$subjectLabel' por $causerLabel às $time";
            } else {
                $sentence = "Action '$event' on model '$subjectLabel' by $causerLabel at $time";
            }
        }

        return [
            'id' => $this->id,
            'log_name' => $logName,
            'event' => $event,
            'sentence' => $sentence,
            'details' => $details,
            'properties' => $this->properties,
            'created_at' => $this->created_at->toIso8601String(),
        ];
    }
}
