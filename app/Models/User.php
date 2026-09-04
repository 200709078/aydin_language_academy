<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Http\UploadedFile;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Jetstream\HasProfilePhoto;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens;
    use HasFactory;
    use HasProfilePhoto;
    use Notifiable;
    use TwoFactorAuthenticatable;

    /**
     * Store profile photos on the private disk. Jetstream's default trait
     * implementation uses storePublicly(), which is not appropriate here.
     */
    public function updateProfilePhoto(UploadedFile $photo, $storagePath = 'profile-photos'): void
    {
        tap($this->profile_photo_path, function ($previous) use ($photo, $storagePath): void {
            $path = $photo->store($storagePath, [
                'disk' => $this->profilePhotoDisk(),
                'visibility' => 'private',
            ]);

            if ($path === false) {
                throw new \RuntimeException('Profil fotoğrafı saklanamadı.');
            }

            $this->forceFill([
                'profile_photo_path' => $path,
            ])->save();

            if ($previous) {
                Storage::disk($this->profilePhotoDisk())->delete($previous);
            }
        });
    }

    /**
     * Resolve uploaded profile photos through the authenticated controller.
     */
    protected function profilePhotoUrl(): Attribute
    {
        return Attribute::get(function (): string {
            return $this->profile_photo_path
                ? route('profile.photos.show', ['user' => $this->getKey()])
                : $this->defaultProfilePhotoUrl();
        });
    }

    public function placementTests(): HasMany
    {
        return $this->hasMany(PlacementTest::class);
    }

    public function approvedPlacementTests(): HasMany
    {
        return $this->hasMany(PlacementTest::class, 'approved_by');
    }

    public function exerciseAttempts(): HasMany
    {
        return $this->hasMany(ExerciseAttempt::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    public function authoredNews(): HasMany
    {
        return $this->hasMany(News::class, 'author_id');
    }

    public function publishedNews(): HasMany
    {
        return $this->hasMany(News::class, 'published_by');
    }

    public function uploadedMediaAssets(): HasMany
    {
        return $this->hasMany(MediaAsset::class, 'uploaded_by');
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'phone',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array<int, string>
     */
    protected $appends = [
        'profile_photo_url',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
}
