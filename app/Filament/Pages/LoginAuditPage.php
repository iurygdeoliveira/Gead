<?php

declare(strict_types = 1);

namespace App\Filament\Pages;

use App\Enums\RoleType;
use Filament\Facades\Filament;
use Filament\Pages\Page;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use OwenIt\Auditing\Models\Audit;

class LoginAuditPage extends Page implements HasTable
{
    use InteractsWithTable;

    public ?int $selectedUserId = null;

    public ?string $search = '';

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-shield-check';

    protected static ?string $slug = 'logins';

    protected string $view = 'filament.pages.login-audit-page';

    protected static ?string $navigationLabel = 'Logins';

    protected static ?string $title = 'Auditoria de Logins';

    protected static string | \UnitEnum | null $navigationGroup = 'Auditoria';

    protected static ?int $navigationSort = 10;

    public function table(Table $table): Table
    {
        return $table
            ->query(
                fn () => Audit::query()
                    ->whereIn('event', ['login', 'logout', 'failed_login'])
                    ->when($this->selectedUserId, fn ($query) => $query->where('user_id', $this->selectedUserId))
                    ->with('user')
                    ->latest()
            )
            ->columns([
                TextColumn::make('user.name')
                    ->label('Usuário')
                    ->searchable(isIndividual: true, isGlobal: false),

                TextColumn::make('user.email')
                    ->label('E-mail')
                    ->searchable(isIndividual: true, isGlobal: false),

                TextColumn::make('event')
                    ->label('Evento')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'login'        => 'success',
                        'logout'       => 'info',
                        'failed_login' => 'danger',
                        default        => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'login'        => 'Login',
                        'logout'       => 'Logout',
                        'failed_login' => 'Falha no Login',
                        default        => $state,
                    }),

                TextColumn::make('created_at')
                    ->label('Data/Hora')
                    ->dateTime('d/m/Y H:i:s')
                    ->sortable(),
            ])
            ->filters([])
            ->defaultSort('created_at', 'desc');
    }

    public function selectUser(?int $userId): void
    {
        $this->selectedUserId = $userId;
        $this->resetTable();
    }

    /**
     * @return \Illuminate\Support\Collection<int, \App\Models\User>
     */
    public function getUsersWithAudits(): \Illuminate\Support\Collection
    {
        return \App\Models\User::query()
            ->whereHas('audits', fn ($query) => $query->whereIn('event', ['login', 'logout', 'failed_login']))
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('name', 'ilike', "%{$this->search}%")
                        ->orWhere('email', 'ilike', "%{$this->search}%");
                });
            })
            ->orderBy('name')
            ->get();
    }

    public static function canAccess(): bool
    {
        /** @var \App\Models\User|null $user */
        $user = Filament::auth()->user();

        return $user?->hasRole(RoleType::ADMIN->value) ?? false;
    }
}
