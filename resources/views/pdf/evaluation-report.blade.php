<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Resultado Avaliação Discente</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            color: #333;
            margin: 0;
            padding: 0;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        .header h2 {
            margin: 5px 0;
            font-size: 16px;
        }
        .info-section {
            margin-bottom: 20px;
        }
        .info-section p {
            margin: 5px 0;
            font-weight: bold;
        }
        .evaluation-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .evaluation-table th, .evaluation-table td {
            border: 1px solid #000;
            padding: 8px;
            text-align: left;
        }
        .evaluation-table th {
            background-color: #f2f2f2;
        }
        .text-center {
            text-align: center !important;
        }
        .font-bold {
            font-weight: bold;
        }
        .footer {
            margin-top: 50px;
            text-align: center;
        }
    </style>
</head>
<body>

    <div class="header">
        <img src="{{ $data['logo_src'] }}" style="height: 80px; display: block; margin: 0 auto 10px auto;">
        <h2>MINISTÉRIO DA EDUCAÇÃO</h2>
        <h2>SECRETARIA DE EDUCAÇÃO PROFISSIONAL E TECNOLÓGICA</h2>
        <h2>INSTITUTO FEDERAL DO TOCANTINS CAMPUS ARAGUAÍNA</h2>
        <br>
        <h2>RESULTADO AVALIAÇÃO DISCENTE</h2>
    </div>

    <div class="info-section">
        <p>Docente: {{ $data['teacher_name'] }}</p>
        <p>SIAPE: {{ $data['teacher_siape'] }}</p>
        <p>AVALIAÇÃO DISCENTE {{ $data['period'] }}</p>
        <br>
        <p>Disciplinas:</p>
    </div>

    @foreach($data['disciplines'] as $discipline)
    <table class="evaluation-table">
        <thead>
            <tr>
                <th>{{ $discipline['course_name'] }} - {{ $discipline['discipline_name'] }}</th>
                <th class="text-center" style="width: 80px;">Média</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>O docente apresenta seu plano de ensino (PLANEJAMENTO) no início do semestre ou ano letivo, indicando a ementa, competências e habilidades, recursos didáticos que serão utilizados, formas de avaliações, referências bibliográficas?</td>
                <td class="text-center">{{ number_format($discipline['planning_avg'], 2, ',', '.') }}</td>
            </tr>
            <tr>
                <td>O docente apresenta uma POSTURA adequada ao cargo e responsabilidade que ocupa?</td>
                <td class="text-center">{{ number_format($discipline['posture_avg'], 2, ',', '.') }}</td>
            </tr>
            <tr>
                <td>O docente é ASSÍDUO, ou seja, não falta às aulas e quando falta, apresenta justificativa e promove suas devidas reposições ou anteposições?</td>
                <td class="text-center">{{ number_format($discipline['attendance_avg'], 2, ',', '.') }}</td>
            </tr>
            <tr>
                <td>O docente é PONTUAL, ou seja, não chega atrasado ou libera a turma mais cedo?</td>
                <td class="text-center">{{ number_format($discipline['punctuality_avg'], 2, ',', '.') }}</td>
            </tr>
            <tr>
                <td>O docente na REALIZAÇÃO de suas aulas procura contextualizar os conteúdos trabalhados; domina o conteúdo; utiliza bem os recursos didáticos; possui fala(dicção) clara, coerente e fluente?</td>
                <td class="text-center">{{ number_format($discipline['execution_avg'], 2, ',', '.') }}</td>
            </tr>
            <tr>
                <td>O docente nas AVALIAÇÕES mostra coerência entre o que foi ensinado e o que é exigido do estudante, entrega as avaliações e comenta os resultados, auxilia no processo de recuperação daqueles conteúdos não apreendidos?</td>
                <td class="text-center">{{ number_format($discipline['assessment_avg'], 2, ',', '.') }}</td>
            </tr>

        </tbody>
    </table>
    <div style="margin-top: 10px; margin-bottom: 30px; border-top: 1px solid #ddd; padding-top: 10px;">
        <h3 class="font-bold" style="font-size: 13px;">PONTUAÇÃO DA TURMA (SOMATÓRIA DAS MÉDIAS DIVIDIDO POR DOIS) <span style="float: right;">{{ number_format($discipline['class_score'], 2, ',', '.') }}</span></h3>
    </div>
    @endforeach

    @if(count($data['disciplines']) > 0)
    <div style="margin-top: 20px; border-top: 2px solid #000; padding-top: 10px;">
        <h3 class="font-bold">MÉDIA DA PONTUAÇÃO DAS TURMAS CONSOLIDADO- RESULTADO FINAL <span style="float: right;">{{ number_format($data['consolidated_score'], 2, ',', '.') }}</span></h3>
    </div>
    @else
    <p>O professor não possui avaliações consolidadas para nenhuma turma neste período.</p>
    @endif

    <div class="footer">
        <p>A cidade de Araguaína-TO, {{ \Carbon\Carbon::now()->translatedFormat('d \d\e F \d\e Y') }}</p>
        
        <div style="margin-top: 80px; width: 60%; margin-left: auto; margin-right: auto; border-top: 1px solid #000; text-align: center; padding-top: 5px;">
            <p style="margin: 0; font-weight: bold;">{{ $data['manager_name'] }}</p>
            <p style="margin: 0;">Gerente de Ensino</p>
            <p style="margin: 0;">SIAPE: {{ $data['manager_siape'] }}</p>
        </div>
    </div>

</body>
</html>
