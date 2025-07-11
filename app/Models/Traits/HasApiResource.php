<?php

namespace App\Models\Traits;

use Illuminate\Support\Str;

trait HasApiResource
{
    /**
     * Get the fillable fields
     */
    public function getFillable(): array
    {
        return $this->fillable ?? [];
    }

    /**
     * Get the singular name to work with for api language
     */
    public function getSingularModelName(): string
    {
        if (isset($this->singularModelName)) {
            return $this->singularModelName;
        }

        return Str::headline(str_replace('\\', '', Str::singular(Str::snake(class_basename($this)))));
    }
}
