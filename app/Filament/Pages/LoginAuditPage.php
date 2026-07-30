<?php

declare(strict_types=1);

namespace App\Filament\Pages;

use App\Enums\RoleType;
use App\Models\User;
use App\Traits\Filament\HasConfigurableNavigationSort;
use Filament\Facades\Filament;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use OwenIt\Auditing\Models\Audit;

class LoginAuditPage extends Page implements HasTable
{
    use HasConfigurableNavigationSort;
    use InteractsWithTable;

    public string|int|null $selectedUserIdentifier = null;

    public ?string $search = '';

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedShieldCheck;

    protected static ?string $slug = 'logins';

    protected string $view = 'filament.pages.login-audit-page';

    protected static ?string $navigationLabel = 'Logins';

    protected static ?string $title = 'Auditoria de Logins';

    protected static string|\UnitEnum|null $navigationGroup = 'Administração';

    protected static ?int $navigationSort = 10;

    public function table(Table $table): Table
    {
        return $table
            ->query(
                fn () => Audit::query() // @phpstan-ignore-line
                    ->whereIn('event', ['login', 'logout', 'failed_login'])
                    ->when($this->selectedUserIdentifier, function ($query): void {
                        if (is_numeric($this->selectedUserIdentifier)) {
                            $query->where('user_id', $this->selectedUserIdentifier);
                        } else {
                            $query->whereNull('user_id')
                                ->where('new_values', 'like', "%{$this->selectedUserIdentifier}%");
                        }
                    })
                    ->with(['user.student.enrollments.course', 'user.student.enrollments.classEnrollments.courseClass'])
                    ->latest()
            )
            ->columns([
                TextColumn::make('user.name')
                    ->label('Usuário')
                    ->searchable(isIndividual: true, isGlobal: false)
                    ->getStateUsing(fn ($record) => $record->user?->name ?? 'Usuário não encontrado'), // @phpstan-ignore-line

                TextColumn::make('user.email')
                    ->label('E-mail')
                    ->searchable(
                        query: fn (Builder $query, string $search): Builder => $query->whereHas('user', function (\Illuminate\Contracts\Database\Query\Builder $q) use ($search): void {
                            $q->where('email', 'like', "%{$search}%");
                        })->orWhere('new_values', 'like', "%{$search}%"),
                        isIndividual: true,
                        isGlobal: false
                    )
                    ->getStateUsing(fn ($record) => $record->user?->email ?? ($record->new_values['email'] ?? '-')), // @phpstan-ignore-line

                TextColumn::make('course')
                    ->label('Curso')
                    ->getStateUsing(fn ($record) => $record->user?->student?->enrollments?->map(fn ($e) => $e->course?->name)->filter()->unique()->implode(', ') ?: '-'),

                TextColumn::make('courseClass')
                    ->label('Turma')
                    ->getStateUsing(fn ($record) => $record->user?->student?->enrollments?->flatMap(fn ($e) => $e->classEnrollments)->map(fn ($ce) => $ce->courseClass?->name)->filter()->unique()->implode(', ') ?: '-'),

                TextColumn::make('event')
                    ->label('Evento')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'login' => 'success',
                        'logout' => 'info',
                        'failed_login' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'login' => 'Login',
                        'logout' => 'Logout',
                        'failed_login' => 'Falha no Login',
                        default => $state,
                    }),

                TextColumn::make('created_at')
                    ->label('Data/Hora')
                    ->dateTime('d/m/Y H:i:s')
                    ->sortable(),
            ])
            ->filters([])
            ->defaultSort('created_at', 'desc');
    }

    public function selectUser(string|int|null $identifier): void
    {
        $this->selectedUserIdentifier = $identifier;
        $this->resetTable();
    }

    public function getSelectedUser(): ?User
    {
        if (! $this->selectedUserIdentifier) {
            return null;
        }

        if (is_numeric($this->selectedUserIdentifier)) {
            return User::find($this->selectedUserIdentifier);
        }

        $fakeUser = new User;
        $fakeUser->name = 'Usuário Não Encontrado';
        $fakeUser->email = $this->selectedUserIdentifier;

        return $fakeUser;
    }

    /**
     * @return Collection<int, User>
     */
    public function getUsersWithAudits(): Collection
    {
        $users = User::query()
            ->whereHas('audits', fn ($query) => $query->whereIn('event', ['login', 'logout', 'failed_login']))
            ->when($this->search, function ($query): void {
                $query->where(function (Builder $q): void {
                    $search = "%{$this->search}%";
                    $q->whereRaw('unaccent(name) ILIKE unaccent(?)', [$search])
                        ->orWhereRaw('unaccent(email) ILIKE unaccent(?)', [$search])
                        ->orWhereHas('student.enrollments.course', function (Builder $q2) use ($search): void {
                            $q2->whereRaw('unaccent(name) ILIKE unaccent(?)', [$search]);
                        })
                        ->orWhereHas('student.enrollments.classEnrollments.courseClass', function (Builder $q2) use ($search): void {
                            $q2->whereRaw('unaccent(name) ILIKE unaccent(?)', [$search])
                                ->orWhereRaw('unaccent(code) ILIKE unaccent(?)', [$search]);
                        });
                });
            })
            ->orderBy('name')
            ->get();

        $users->each(function ($user): void {
            $user->setAttribute('identifier', $user->id);
        });

        $unknownEmails = Audit::query()
            ->whereIn('event', ['failed_login'])
            ->whereNull('user_id')
            ->when($this->search, function ($query): void {
                $query->where('new_values', 'like', "%{$this->search}%");
            })
            ->select('new_values')
            ->get()
            ->pluck('new_values.email')
            ->filter()
            ->unique();

        foreach ($unknownEmails as $email) {
            $fakeUser = new User;
            $fakeUser->name = 'Usuário Não Encontrado';
            $fakeUser->email = $email;
            $fakeUser->setAttribute('identifier', $email);
            $users->push($fakeUser);
        }

        return $users;
    }

    #[\Override]
    public static function canAccess(): bool
    {
        /** @var User|null $user */
        $user = Filament::auth()->user();

        if (! $user) {
            return false;
        }
        if ($user->hasRole(RoleType::ADMIN->value)) {
            return true;
        }
        if ($user->hasRole(RoleType::MANAGER->value)) {
            return true;
        }

        return $user->hasRole(RoleType::TAE->value);
    }
}
