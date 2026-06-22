<?php

declare(strict_types=1);

namespace App\Filament\Pages;

use App\Models\User;
use Filament\Actions\Action;
use Filament\Facades\Filament;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Pages\Page;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Hash;

/**
 * @property Form $form
 */
class ChangePassword extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-lock-closed';

    protected string $view = 'filament.pages.change-password';

    protected static bool $shouldRegisterNavigation = false;

    public ?array $data = [];

    public function mount(): void
    {
        /** @var User|null $user */
        $user = Filament::auth()->user();

        if (! $user || ! $user->must_change_password) {
            $panelId = Filament::getCurrentPanel()?->getId() ?? 'manager';
            $tenant = Filament::getTenant();
            redirect()->route("filament.{$panelId}.pages.dashboard", $tenant ? ['tenant' => $tenant] : []);

            return;
        }

        $this->form->fill();
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make('Alteração de Senha Obrigatória')
                    ->description('Por segurança, você deve alterar sua senha antes de continuar.')
                    ->schema([
                        TextInput::make('password')
                            ->label('Nova Senha')
                            ->password()
                            ->revealable()
                            ->required()
                            ->confirmed()
                            ->minLength(8),
                        TextInput::make('password_confirmation')
                            ->label('Confirmar Nova Senha')
                            ->password()
                            ->revealable()
                            ->required(),
                    ]),
            ])
            ->statePath('data');
    }

    public function updatePassword(): void
    {
        $data = $this->form->getState();

        /** @var User|null $user */
        $user = User::query()->find(Filament::auth()->id());

        if (! $user) {
            redirect()->route('filament.auth.auth.login');

            return;
        }

        $user->update([
            'password' => Hash::make($data['password']),
            'must_change_password' => false,
        ]);

        Filament::auth()->setUser($user);
        Filament::auth()->logout();

        session()->invalidate();
        session()->regenerateToken();

        session()->flash('password_changed', 'Senha alterada com sucesso! Faça login com sua nova senha.');

        redirect()->route('filament.auth.auth.login');
    }

    protected function getFormActions(): array
    {
        return [
            Action::make('save')
                ->label('Alterar Senha')
                ->submit('updatePassword'),
        ];
    }
}
