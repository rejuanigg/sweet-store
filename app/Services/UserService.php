<?php
namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserService
{
    public function store(array $data)
    {
        $data['role']='employed';

        $data['password'] = Hash::make($data['password']);

        $user = User::create($data);

        return $user;
    }
    public function changePassword(User $user, string $newPassword)
    {
        $user->update([
            'password' => Hash::make($newPassword)
        ]);
        return $user;
    }

    public function update(User $user, array $data)
    {
        $user->update($data);

        return $user;
    }

    public function destroy(User $user)
    {
        $user->delete();
    }
}
?>
