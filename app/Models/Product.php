<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;
use Illuminate\Support\Facades\Log;

class Product extends Model
{
    use HasFactory;
    use HasSlug;
    use SoftDeletes;

    // TODO : Make fillable or guarded as per your requirement

    public function getSlugOptions(): SlugOptions
    {
        Log::info(
            'Generating slug for product: ' . ($this->title ?? 'No title set')
        );
        return SlugOptions::create()
            ->generateSlugsFrom('title')
            ->saveSlugsTo('slug');
    }
}
