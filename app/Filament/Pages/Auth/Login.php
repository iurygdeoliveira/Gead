<?php

namespace App\Filament\Pages\Auth;

use Filament\Actions\Action;
use Filament\Auth\Http\Responses\Contracts\LoginResponse;
use Filament\Auth\Pages\Login as BaseAuthLogin;
use Filament\Facades\Filament;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\View;
use Filament\Schemas\Schema;
use Filament\Support\Colors\Color;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Validation\ValidationException;

class Login extends BaseAuthLogin
{
    public function getHeading(): string|Htmlable
    {
        return '';
    }

    public function authenticate(): ?LoginResponse
    {
        $response = parent::authenticate();

        $user = Filament::auth()->user();

        // Bloqueio de professores que não são gerentes nem TAEs
        if ($user && $user->teacher()->exists() && $user->email !== 'walmir.sousa@ifto.edu.br') {
            Filament::auth()->logout();

            throw ValidationException::withMessages([
                'data.email' => 'O acesso para professores ainda não está liberado.',
            ]);
        }

        return $response;
    }

    protected function getFormActions(): array
    {
        return [];
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                View::make('filament.auth.gerencia-badge')
                    ->columnSpanFull()
                    ->extraAttributes(['class' => 'flex w-full justify-center items-center']),

                $this->getEmailFormComponent(),
                $this->getPasswordFormComponent(),
                $this->getRememberFormComponent(),

                Actions::make([
                    Action::make('authenticate')
                        ->label(__('filament-panels::auth/pages/login.form.actions.authenticate.label'))
                        ->submit('authenticate')
                        ->extraAttributes(['class' => 'w-full']),
                ])->fullWidth(),

                View::make('filament.auth.alunos-badge')
                    ->columnSpanFull()
                    ->extraAttributes(['class' => 'flex w-full justify-center items-center']),

                Actions::make([
                    Action::make('googleLogin')
                        ->icon('icon-google')
                        ->label('Logar com email institucional')
                        ->url(route('google.redirect'))
                        ->color(Color::hex('#fef2f2'))
                        ->extraAttributes(['class' => 'w-full']),
                ])->fullWidth(),
            ])
            ->statePath('data');
    }
}
