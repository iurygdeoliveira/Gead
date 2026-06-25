<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Relatório de Alunos Pendentes</title>
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
        .class-section {
            margin-bottom: 30px;
        }
        .class-header {
            background-color: #f2f2f2;
            padding: 8px;
            font-weight: bold;
            border: 1px solid #000;
            margin-bottom: 0;
            border-bottom: none;
        }
        .student-table {
            width: 100%;
            border-collapse: collapse;
        }
        .student-table th, .student-table td {
            border: 1px solid #000;
            padding: 6px 8px;
            text-align: left;
        }
        .student-table th {
            background-color: #e9e9e9;
        }
        .text-center {
            text-align: center !important;
        }
        .footer {
            margin-top: 50px;
            text-align: center;
            font-size: 11px;
            color: #666;
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
        <h2>RELATÓRIO DE ALUNOS PENDENTES DE AVALIAÇÃO DOCENTE</h2>
    </div>

    <div class="info-section">
        <p>Docente: {{ $data['teacher_name'] }}</p>
        <p>SIAPE: {{ $data['teacher_siape'] }}</p>
        <br>
    </div>

    @forelse($data['classes'] as $class)
        <div class="class-section">
            <div class="class-header">
                Turma: {{ $class['class_code'] }} - {{ $class['class_name'] }}<br>
                Curso: {{ $class['course_name'] }}<br>
                Disciplina: {{ $class['discipline_name'] }}
            </div>
            <table class="student-table">
                <thead>
                    <tr>
                        <th style="width: 50px;" class="text-center">Nº</th>
                        <th>Nome do Aluno</th>
                        <th style="width: 150px;">Matrícula</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($class['students'] as $index => $student)
                        <tr>
                            <td class="text-center">{{ $index + 1 }}</td>
                            <td>{{ $student->name }}</td>
                            <td>{{ $student->registration_number ?? 'Não informada' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @empty
        <p>Não há alunos pendentes de avaliação para este professor nas disciplinas cadastradas.</p>
    @endforelse

    <div class="footer">
        <p>Gerado em {{ \Carbon\Carbon::now()->translatedFormat('d \d\e F \d\e Y \à\s H:i') }}</p>
    </div>

</body>
</html>
