<?php

namespace App\Kubix\Domains\Identity\User;

use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

class UserRepository
{
    public function findById(int $id): ?User 
    { 
        return User::query()->find($id); 
    }

    public function findByEmail(string $email): ?User 
    { 
        return User::query()->where('email', $email)->first(); 
    }

    public function create(array $attributes): User
    {
        return User::query()->create($attributes);
    }

    public function update(User $user, array $attributes): User
    {
        $user->fill($attributes);
        $user->save();
        return $user;
    }

    public function delete(User $user): bool
    {
        return (bool) $user->delete();
    }

    public function all(): Collection
    {
        return User::query()->get();
    }
}