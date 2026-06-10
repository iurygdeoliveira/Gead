<?php

namespace App\Actions\Auth;

use App\Models\User;
use App\Models\Team;
use App\Models\MagicLoginToken;
use Illuminate\Support\Facades\Auth;
use App\Models\Membership;

class AuthenticateMagicLinkAction
{
    public function execute(string $token): User
    {
        $hash = hash('sha256', $token);
        
        $magicToken = MagicLoginToken::valid()->where('token', $hash)->firstOrFail();
        $user = User::where('email', $magicToken->email)->firstOrFail();
        
        $magicToken->delete();
        $user->update(['is_approved' => true]);
        $user->assignRole('user');
        
        $team = Team::firstOrCreate(
            ['slug' => 'campus-araguaina'], 
            ['name' => 'Campus Araguaína', 'cnpj' => '12.345.678/0001-00', 'is_active' => true]
        );
        
        if (!$user->teams()->where('teams.id', $team->id)->exists()) {
            Membership::create([
                'team_id' => $team->id, 
                'user_id' => $user->id, 
                'role' => 'member'
            ]);
        }
        
        Auth::login($user);
        
        return $user;
    }
}
