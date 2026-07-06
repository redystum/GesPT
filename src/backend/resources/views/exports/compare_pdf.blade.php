<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>{{ $isEnglish ? 'Study Plan Comparison' : 'Comparação de Planos de Estudo' }}</title>
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 10px;
            color: #333333;
            margin: 0;
            padding: 0;
        }
        
        .header-container {
            margin-bottom: 15px;
            border-bottom: 2px solid #1e3a8a;
            padding-bottom: 8px;
        }

        .title {
            font-size: 18px;
            font-weight: bold;
            color: #1e3a8a;
            margin: 0 0 5px 0;
        }

        .meta-info {
            font-size: 11px;
            color: #4b5563;
            margin-bottom: 4px;
        }

        .meta-label {
            font-weight: bold;
            color: #1f2937;
        }

        .plans-summary {
            margin-bottom: 12px;
            font-size: 11px;
            width: 100%;
            border-collapse: collapse;
        }

        .plans-summary td {
            padding: 6px 10px;
            border: 1px solid #e5e7eb;
        }

        .plan-initial-header {
            background-color: #dbeafe;
            color: #1e40af;
            font-weight: bold;
            width: 50%;
        }

        .plan-target-header {
            background-color: #e0e7ff;
            color: #3730a3;
            font-weight: bold;
            width: 50%;
        }

        .compare-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 5px;
        }

        .compare-table th {
            font-size: 9px;
            font-weight: bold;
            text-transform: uppercase;
            padding: 6px 4px;
            border: 1px solid #d1d5db;
        }

        .th-initial {
            background-color: #3b82f6;
            color: #ffffff;
        }

        .th-target {
            background-color: #6366f1;
            color: #ffffff;
        }

        .compare-table td {
            padding: 6px 5px;
            border: 1px solid #e5e7eb;
            vertical-align: middle;
            font-size: 9px;
        }

        /* Group background alternation */
        .pair-even {
            background-color: #f9fafb;
        }

        .pair-odd {
            background-color: #ffffff;
        }

        /* Thicker vertical border to split initial/target plans */
        .border-split {
            border-right: 2px solid #9ca3af !important;
        }

        .text-center {
            text-align: center;
        }

        .text-italic {
            font-style: italic;
        }

        .text-muted {
            color: #9ca3af;
        }

        .text-danger {
            color: #ef4444;
        }

        .footer {
            position: fixed;
            bottom: -30px;
            left: 0px;
            right: 0px;
            height: 30px;
            text-align: center;
            font-size: 8px;
            color: #9ca3af;
            border-top: 1px solid #e5e7eb;
            padding-top: 5px;
        }
    </style>
</head>
<body>

    <div class="header-container">
        <h1 class="title">{{ $isEnglish ? 'Study Plan Comparison' : 'Comparação de Planos de Estudo' }}</h1>
        <div class="meta-info">
            <span class="meta-label">{{ $isEnglish ? 'Course:' : 'Curso:' }}</span> 
            {{ $isEnglish ? ($curso->name ?? $curso->nome) : $curso->nome }}
            &nbsp;|&nbsp;
            <span class="meta-label">{{ $isEnglish ? 'Acronym:' : 'Sigla:' }}</span> {{ $curso->sigla }}
            &nbsp;|&nbsp;
            <span class="meta-label">{{ $isEnglish ? 'Code:' : 'Código:' }}</span> {{ $curso->codigo }}
        </div>
    </div>

    <table class="plans-summary">
        <tr>
            <td class="plan-initial-header">
                {{ $isEnglish ? 'Initial Plan:' : 'Plano Inicial:' }} {{ $planoInicial->ano }} 
                <span style="font-weight: normal; font-size: 10px; float: right;">
                    {{ $planoInicial->ects_sum }} ECTS
                </span>
            </td>
            <td class="plan-target-header">
                {{ $isEnglish ? 'Target Plan:' : 'Plano de Destino:' }} 
                {{ $planoFinal ? $planoFinal->ano : ($isEnglish ? 'Discontinued' : 'Descontinuado') }}
                @if($planoFinal)
                    <span style="font-weight: normal; font-size: 10px; float: right;">
                        {{ $planoFinal->ects_sum }} ECTS
                    </span>
                @endif
            </td>
        </tr>
    </table>

    <table class="compare-table">
        <thead>
            <tr>
                <th class="th-initial" style="width: 25%;">{{ $isEnglish ? 'UC Name (Initial)' : 'Nome da UC (Inicial)' }}</th>
                <th class="th-initial" style="width: 8%;">{{ $isEnglish ? 'Acronym' : 'Sigla' }}</th>
                <th class="th-initial" style="width: 5%;">ECTS</th>
                <th class="th-initial" style="width: 6%;">{{ $isEnglish ? 'Sem.' : 'Sem.' }}</th>
                <th class="th-initial border-split" style="width: 11%;">{{ $isEnglish ? 'Observations' : 'Observações' }}</th>
                
                <th class="th-target" style="width: 25%;">{{ $isEnglish ? 'UC Name (Target)' : 'Nome da UC (Destino)' }}</th>
                <th class="th-target" style="width: 8%;">{{ $isEnglish ? 'Acronym' : 'Sigla' }}</th>
                <th class="th-target" style="width: 5%;">ECTS</th>
                <th class="th-target" style="width: 6%;">{{ $isEnglish ? 'Sem.' : 'Sem.' }}</th>
                <th class="th-target" style="width: 11%;">{{ $isEnglish ? 'Observations' : 'Observações' }}</th>
            </tr>
        </thead>
        <tbody>
            @php
                $isPairEven = false;
            @endphp
            @foreach($data as $pair)
                @php
                    $inicialList = $pair['inicial'] ?? [];
                    $finalList = $pair['final'] ?? [];
                    $max = max(count($inicialList), count($finalList));
                    if ($max === 0) $max = 1;
                    $rowBgClass = $isPairEven ? 'pair-even' : 'pair-odd';
                    $isPairEven = !$isPairEven;

                    $mergeInitial = (count($finalList) > 1 && count($inicialList) === 1);
                    $mergeFinal = (count($inicialList) > 1 && count($finalList) === 1);
                @endphp
                
                @for($i = 0; $i < $max; $i++)
                    @php
                        $initUc = $inicialList[$i] ?? null;
                        $finalUc = $finalList[$i] ?? null;
                    @endphp
                    <tr class="{{ $rowBgClass }}">
                        <!-- Initial UC Details -->
                        @if($mergeInitial)
                            @if($i === 0)
                                @if($initUc && isset($initUc['uc']))
                                    @php
                                        $uc = $initUc['uc'];
                                        $ucNome = $isEnglish ? ($uc['name'] ?? $uc['nome']) : $uc['nome'];
                                        if (!empty($initUc['opcao_escolhida'])) {
                                            $optName = $isEnglish ? ($initUc['opcao_escolhida']['name'] ?? $initUc['opcao_escolhida']['nome']) : $initUc['opcao_escolhida']['nome'];
                                            $ucNome .= ' &rarr; ' . $optName;
                                        }
                                    @endphp
                                    <td rowspan="{{ $max }}">{{ $ucNome }}</td>
                                    <td rowspan="{{ $max }}" class="text-center">{{ $uc['sigla'] }}</td>
                                    <td rowspan="{{ $max }}" class="text-center">{{ $uc['ects'] }}</td>
                                    <td rowspan="{{ $max }}" class="text-center">
                                        @if($uc['semestre'] == 1) 1º
                                        @elseif($uc['semestre'] == 2) 2º
                                        @elseif($uc['semestre'] == 3) {{ $isEnglish ? 'Both' : 'Ambos' }}
                                        @else - @endif
                                    </td>
                                    <td rowspan="{{ $max }}" class="border-split">{{ $initUc['observacao'] ?? '' }}</td>
                                @else
                                    <td rowspan="{{ $max }}" class="text-italic text-muted">{{ $isEnglish ? 'No prior equivalent' : 'Sem antecedente' }}</td>
                                    <td rowspan="{{ $max }}" class="text-center"></td>
                                    <td rowspan="{{ $max }}" class="text-center"></td>
                                    <td rowspan="{{ $max }}" class="text-center"></td>
                                    <td rowspan="{{ $max }}" class="border-split"></td>
                                @endif
                            @endif
                        @else
                            @if($initUc && isset($initUc['uc']))
                                @php
                                    $uc = $initUc['uc'];
                                    $ucNome = $isEnglish ? ($uc['name'] ?? $uc['nome']) : $uc['nome'];
                                    if (!empty($initUc['opcao_escolhida'])) {
                                        $optName = $isEnglish ? ($initUc['opcao_escolhida']['name'] ?? $initUc['opcao_escolhida']['nome']) : $initUc['opcao_escolhida']['nome'];
                                        $ucNome .= ' &rarr; ' . $optName;
                                    }
                                @endphp
                                <td>{{ $ucNome }}</td>
                                <td class="text-center">{{ $uc['sigla'] }}</td>
                                <td class="text-center">{{ $uc['ects'] }}</td>
                                <td class="text-center">
                                    @if($uc['semestre'] == 1) 1º
                                    @elseif($uc['semestre'] == 2) 2º
                                    @elseif($uc['semestre'] == 3) {{ $isEnglish ? 'Both' : 'Ambos' }}
                                    @else - @endif
                                </td>
                                <td class="border-split">{{ $initUc['observacao'] ?? '' }}</td>
                            @else
                                @if($i === 0)
                                    <td class="text-italic text-muted">{{ $isEnglish ? 'No prior equivalent' : 'Sem antecedente' }}</td>
                                @else
                                    <td></td>
                                @endif
                                <td class="text-center"></td>
                                <td class="text-center"></td>
                                <td class="text-center"></td>
                                <td class="border-split"></td>
                            @endif
                        @endif

                        <!-- Target UC Details -->
                        @if($mergeFinal)
                            @if($i === 0)
                                @if($finalUc && isset($finalUc['uc']))
                                    @php
                                        $uc = $finalUc['uc'];
                                        $ucNome = $isEnglish ? ($uc['name'] ?? $uc['nome']) : $uc['nome'];
                                        if (!empty($finalUc['opcao_escolhida'])) {
                                            $optName = $isEnglish ? ($finalUc['opcao_escolhida']['name'] ?? $finalUc['opcao_escolhida']['nome']) : $finalUc['opcao_escolhida']['nome'];
                                            $ucNome .= ' &rarr; ' . $optName;
                                        }
                                    @endphp
                                    <td rowspan="{{ $max }}">{{ $ucNome }}</td>
                                    <td rowspan="{{ $max }}" class="text-center">{{ $uc['sigla'] }}</td>
                                    <td rowspan="{{ $max }}" class="text-center">{{ $uc['ects'] }}</td>
                                    <td rowspan="{{ $max }}" class="text-center">
                                        @if($uc['semestre'] == 1) 1º
                                        @elseif($uc['semestre'] == 2) 2º
                                        @elseif($uc['semestre'] == 3) {{ $isEnglish ? 'Both' : 'Ambos' }}
                                        @else - @endif
                                    </td>
                                    <td rowspan="{{ $max }}">{{ $finalUc['observacao'] ?? '' }}</td>
                                @else
                                    <td rowspan="{{ $max }}" class="text-italic text-danger">{{ $isEnglish ? 'Discontinued unit' : 'UC descontinuada' }}</td>
                                    <td rowspan="{{ $max }}" class="text-center"></td>
                                    <td rowspan="{{ $max }}" class="text-center"></td>
                                    <td rowspan="{{ $max }}" class="text-center"></td>
                                    <td rowspan="{{ $max }}"></td>
                                @endif
                            @endif
                        @else
                            @if($finalUc && isset($finalUc['uc']))
                                @php
                                    $uc = $finalUc['uc'];
                                    $ucNome = $isEnglish ? ($uc['name'] ?? $uc['nome']) : $uc['nome'];
                                    if (!empty($finalUc['opcao_escolhida'])) {
                                        $optName = $isEnglish ? ($finalUc['opcao_escolhida']['name'] ?? $finalUc['opcao_escolhida']['nome']) : $finalUc['opcao_escolhida']['nome'];
                                        $ucNome .= ' &rarr; ' . $optName;
                                    }
                                @endphp
                                <td>{{ $ucNome }}</td>
                                <td class="text-center">{{ $uc['sigla'] }}</td>
                                <td class="text-center">{{ $uc['ects'] }}</td>
                                <td class="text-center">
                                    @if($uc['semestre'] == 1) 1º
                                    @elseif($uc['semestre'] == 2) 2º
                                    @elseif($uc['semestre'] == 3) {{ $isEnglish ? 'Both' : 'Ambos' }}
                                    @else - @endif
                                </td>
                                <td>{{ $finalUc['observacao'] ?? '' }}</td>
                            @else
                                @if($i === 0 && $planoFinal)
                                    <td class="text-italic text-danger">{{ $isEnglish ? 'Discontinued unit' : 'UC descontinuada' }}</td>
                                @else
                                    <td></td>
                                @endif
                                <td class="text-center"></td>
                                <td class="text-center"></td>
                                <td class="text-center"></td>
                                <td></td>
                            @endif
                        @endif
                    </tr>
                @endfor
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        {{ $isEnglish ? 'GesPT - Curricular Plan Comparison Platform' : 'GesPT - Plataforma de Gestão e Comparação de Planos de Estudo' }}
    </div>

</body>
</html>
