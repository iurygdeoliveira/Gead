<?php

namespace App\Http\Controllers\Auth;

use App\Models\ConnectedAccount;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

class GoogleLoginController
{
    public function redirect(): RedirectResponse
    {
        return Socialite::driver('google')->redirect();
    }

    public function callback(): RedirectResponse
    {
        try {
            $socialUser = Socialite::driver('google')->user();
        } catch (\Exception $e) {
            return redirect()->route('filament.admin.auth.login')
                ->with('socialite_error', 'Falha ao autenticar com o Google. Tente novamente.');
        }

        // Camada de segurança: Validar domínio
        $email = $socialUser->getEmail();
        if (! str_ends_with($email, '@ifto.edu.br') && ! str_ends_with($email, '@estudante.ifto.edu.br')) {
            return redirect()->route('filament.admin.auth.login')
                ->with('socialite_error', 'Acesso permitido apenas para e-mails institucionais (@ifto.edu.br ou @estudante.ifto.edu.br).');
        }

        $user = User::where('email', $socialUser->getEmail())->first();

        // Se o usuário não existir, bloqueia e manda para a página de solicitar acesso
        if (! $user) {
            return redirect()->route('solicitar-acesso');
        }

        // Se o usuário existir, atualiza ou cria o ConnectedAccount
        $connectedAccount = ConnectedAccount::updateOrCreate(
            [
                'provider' => 'google',
                'provider_user_id' => $socialUser->getId(),
            ],
            [
                'user_id' => $user->id,
                'name' => $socialUser->getName(),
                'email' => $socialUser->getEmail(),
                'avatar' => $socialUser->getAvatar(),
                'token' => $socialUser->token,
                'refresh_token' => $socialUser->refreshToken,
                'expires_at' => property_exists($socialUser, 'expiresIn') ? now()->addSeconds($socialUser->expiresIn) : null,
            ]
        );

        // Bloqueio de professores que não são gerentes
        if ($user->teacher()->exists() && $user->email !== 'walmir.sousa@ifto.edu.br') {
            return redirect()->route('filament.admin.auth.login')
                ->with('socialite_error', 'O acesso para professores ainda não está liberado.');
        }

        Auth::login($user);

        return redirect()->intended('/login');
    }
}
