<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasUuids;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'external_id',
        'name',
        'email',
        'token',
        'roles',
        'permissions'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'remember_token',
    ];

    protected $casts = [
    'roles' => 'array',
    'permissions' => 'array',
  ];


  protected function normalizedRoles(): array
  {
    $roles = $this->roles ?? [];
    if (!is_array($roles)) $roles = [];
    return array_values(array_unique(array_map(
      fn($r) => strtolower(trim((string)$r)),
      $roles
    )));
  }

  public function hasRole(string $role): bool
  {
    $role = strtolower(trim($role));
    return in_array($role, $this->normalizedRoles(), true);
  }

  /** $roles bisa "admin|editor" atau array */
  public function hasAnyRole(array|string $roles): bool
  {
    $required = is_array($roles) ? $roles : explode('|', $roles);
    $required = array_filter(array_map(fn($r) => strtolower(trim($r)), $required));
    if (empty($required)) return false;

    $own = $this->normalizedRoles();
    return count(array_intersect($required, $own)) > 0;
  }

  public function hasAllRoles(array|string $roles): bool
  {
    $required = is_array($roles) ? $roles : explode('|', $roles);
    $required = array_filter(array_map(fn($r) => strtolower(trim($r)), $required));
    if (empty($required)) return false;

    $own = $this->normalizedRoles();
    foreach ($required as $r) {
      if (!in_array($r, $own, true)) return false;
    }
    return true;
  }

  /** Untuk Blade directive */
  public function matchesRoles(array|string $roles, string $mode = 'any'): bool
  {
    return $mode === 'all'
      ? $this->hasAllRoles($roles)
      : $this->hasAnyRole($roles);
  }

}
