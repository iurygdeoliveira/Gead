<?php

namespace App\Filament\Pages\Auth;

use App\Actions\Auth\SendMagicLinkAction;
use Filament\Actions\Action;
use Filament\Auth\Http\Responses\Contracts\LoginResponse;
use Filament\Auth\Pages\Login as BaseAuthLogin;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\View;
use Filament\Schemas\Schema;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Validation\ValidationException;

class Login extends BaseAuthLogin
{
    public bool $magicLinkSent = false;

    public function getHeading(): string|Htmlable
    {
        return '';
    }

    public function sendMagicLink(): void
    {
        if ($this->magicLinkSent) {
            return;
        }

        $email = $this->data['magic_link_email'] ?? null;

        if (! $email) {
            $this->addError('data.magic_link_email', 'O e-mail institucional é obrigatório.');

            return;
        }

        try {
            (new SendMagicLinkAction)->execute($email);

            $this->magicLinkSent = true;
            $this->data['magic_link_email'] = null; // Limpa o campo
        } catch (ValidationException $e) {
            $this->addError('data.magic_link_email', $e->getMessage());
        } catch (\Exception $e) {
            $this->addError('data.magic_link_email', 'Falha ao enviar e-mail. Verifique o servidor SMTP ou Resend: '.$e->getMessage());
        }
    }

    public function authenticate(): ?LoginResponse
    {
        $magicEmail = $this->data['magic_link_email'] ?? null;
        $password = $this->data['password'] ?? null;

        if (! empty($magicEmail) && empty($password)) {
            $this->sendMagicLink();

            return null;
        }

        return parent::authenticate();
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

                View::make('filament.auth.divider')
                    ->columnSpanFull()
                    ->extraAttributes(['class' => 'flex w-full justify-center items-center']),

                View::make('filament.auth.alunos-badge')
                    ->columnSpanFull()
                    ->extraAttributes(['class' => 'flex w-full justify-center items-center']),

                TextInput::make('magic_link_email')
                    ->label('E-mail Institucional')
                    ->email()
                    ->placeholder('digitar o email aqui'),

                Actions::make([
                    Action::make('sendMagicLink')
                        ->label(fn () => $this->magicLinkSent ? 'Link mágico enviado! Verifique seu e-mail.' : 'Enviar link de acesso ao e-mail')
                        ->action('sendMagicLink')
                        ->extraAttributes(fn () => $this->magicLinkSent ? ['class' => 'w-full pointer-events-none'] : ['class' => 'w-full']),
                ])->fullWidth(),
            ])
            ->statePath('data');
    }
}
