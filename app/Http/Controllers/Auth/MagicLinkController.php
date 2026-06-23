<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MagicLinkController
{
    public function authenticate(Request $request, User $user): RedirectResponse
    {
        // Se a requisição chegou aqui sem exceções, o middleware 'signed' já garantiu
        // que a assinatura é válida e não expirou.

        // Bloqueio extra: garantir que não é um professor não autorizado (caso essa regra do GoogleLoginController aplique aqui)
        if ($user->teacher()->exists() && $user->email !== 'walmir.sousa@ifto.edu.br') {
            return to_route('filament.admin.auth.login')
                ->with('socialite_error', 'O acesso para professores ainda não está liberado.');
        }

        // Fazer o login
        Auth::login($user);

        // Limpar a sessão para segurança (regenerar ID)
        $request->session()->regenerate();

        // Redirecionar para o painel de auth, onde o RedirectToProperPanelMiddleware
        // assumirá a responsabilidade de redirecionar para o painel adequado (manager, tae, etc.)
        return to_route('filament.auth.auth.login');
    }
}
