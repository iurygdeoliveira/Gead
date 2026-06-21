<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AcademicTerm extends Model
{
    protected $fillable = ['name', 'start_date', 'end_date'];

    public function courseClasses(): HasMany
    {
        return $this->hasMany(CourseClass::class);
    }
}
