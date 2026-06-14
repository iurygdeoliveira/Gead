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
        $team = \App\Models\Team::where('cnpj', '03.131.702/0001-33')->first();
        if (! $team) {
            $team = \App\Models\Team::create([
                'name' => 'Campus Araguaína',
                'slug' => 'campus-araguaina',
                'cnpj' => '03.131.702/0001-33',
                'is_active' => true,
                'is_personal' => false,
            ]);
        }

        $csvFiles = glob(database_path('seeders/dados de seed/*/Alunos*.csv'));

        if (empty($csvFiles)) {
            $this->command->error("Nenhum arquivo de alunos encontrado nos subdiretórios de seeders/dados de seed");
            return;
        }

        foreach ($csvFiles as $csvPath) {
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

                // Force Analises Clinicas code to be 195 as commented by the user
                if ($code === '210') {
                    $code = '195';
                }

                \App\Models\Course::updateOrCreate(
                    ['code' => $code, 'team_id' => $team->id],
                    ['name' => $name]
                );
            }

            fclose($file);
        }
    }
}
