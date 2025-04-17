<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Institution extends Model
{
    use HasFactory;
    use HasUuids;

    protected $fillable = [
        'logo_path',
        'name',
        'status',
        'type',
    ];

    protected $hidden = [
        'status',
        'logo_path',
    ];

    protected $casts = [
        'status' => 'boolean',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array<int, string>
     */
    protected $appends = [
        'logo_url',
    ];

    /**
     * Get the URL to the photo.
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    public function logoUrl(): Attribute
    {
        return Attribute::get(function (): string {
            return Storage::disk('public')->url('banks-images/' . $this->logo_path);
        });
    }
}
