<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CourseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $csvPath = base_path('docs/dados de seed/alunos.csv');

        if (! file_exists($csvPath)) {
            $this->command->error("Arquivo CSV não encontrado: {$csvPath}");
            return;
        }

        $team = \App\Models\Team::where('cnpj', '03.131.702/0001-33')->first();
        if (! $team) {
            $this->command->error("Campus Araguaína não encontrado (CNPJ: 03.131.702/0001-33)");
            return;
        }

        $file = fopen($csvPath, 'r');
        $isHeader = true;

        while (($row = fgetcsv($file, 1000, ',')) !== false) {
            if ($isHeader) {
                $isHeader = false;
                continue;
            }

            $cursoStr = $row[3] ?? null;

            if (empty($cursoStr) || $cursoStr === '-') {
                continue;
            }

            $parts = explode(' - ', $cursoStr, 2);
            $code = trim($parts[0]);
            $name = isset($parts[1]) ? trim($parts[1]) : $code;
            $name = trim(str_replace('(Campus Araguaína)', '', $name));

            \App\Models\Course::updateOrCreate(
                ['code' => $code, 'team_id' => $team->id],
                ['name' => $name]
            );
        }

        fclose($file);
    }
}
