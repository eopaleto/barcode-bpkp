<?php

namespace App;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;

trait ReadOnlyForSuperAdmin
{
    /**
     * Helper untuk mendapatkan user login dengan type hint User
     *
     * @return User|null
     */
    protected function getAuthUser(): ?User
    {
        /** @var User|null $user */
        $user = Auth::user();
        return $user;
    }

    public static function canCreate(): bool
    {
        $user = Auth::user();
        return !($user && $user->hasRole('SuperAdmin'));
    }

    public function canEdit(Model $record): bool
    {
        $user = $this->getAuthUser();
        return !($user && $user->hasRole('SuperAdmin'));
    }

    public function canDelete(Model $record): bool
    {
        $user = $this->getAuthUser();
        return !($user && $user->hasRole('SuperAdmin'));
    }

    public function canForceDelete(Model $record): bool
    {
        $user = $this->getAuthUser();
        return !($user && $user->hasRole('SuperAdmin'));
    }

    public function canRestore(Model $record): bool
    {
        $user = $this->getAuthUser();
        return !($user && $user->hasRole('SuperAdmin'));
    }
}
