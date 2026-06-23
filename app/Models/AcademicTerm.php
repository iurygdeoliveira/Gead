<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['name', 'start_date', 'end_date'])]
class AcademicTerm extends Model
{
    /**
     * @return HasMany<CourseClass, $this>
     */
    public function courseClasses(): HasMany
    {
        return $this->hasMany(CourseClass::class);
    }
}
