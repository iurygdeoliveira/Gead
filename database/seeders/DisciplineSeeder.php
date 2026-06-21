<?php

namespace Database\Seeders;

use App\Models\Course;
use App\Models\Discipline;
use App\Models\Team;
use Illuminate\Database\Seeder;

class DisciplineSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $team = Team::where('cnpj', '03.131.702/0001-33')->first();
        if (! $team) {
            $team = Team::create([
                'name' => 'Campus Araguaína',
                'slug' => 'campus-araguaina',
                'cnpj' => '03.131.702/0001-33',
                'is_active' => true,
                'is_personal' => false,
            ]);
        }

        $csvPath = database_path('seeders/dados de seed/diarios_2026.1.csv');

        if (! file_exists($csvPath)) {
            $this->command->error("Arquivo não encontrado: {$csvPath}");
            return;
        }

        $courses = Course::where('team_id', $team->id)->get()->keyBy('code');

        $file = fopen($csvPath, 'r');
        $isHeader = true;

        while (($row = fgetcsv($file, 1000, ',')) !== false) {
            if ($isHeader) {
                $isHeader = false;
                continue;
            }

            $sigla = trim($row[2] ?? '');
            $descricao = trim($row[3] ?? '');
            $courseCode = trim($row[6] ?? '');

            if (empty($sigla) || empty($descricao) || empty($courseCode)) {
                continue;
            }

            $course = $courses->get($courseCode);
            if (!$course) {
                continue;
            }

            Discipline::updateOrCreate(
                [
                    'course_id' => $course->id,
                    'code' => $sigla,
                ],
                [
                    'name' => $descricao,
                    'period' => null, // O período não é mais estritamente necessário para o seed
                ]
            );
        }

        fclose($file);
    }
}
