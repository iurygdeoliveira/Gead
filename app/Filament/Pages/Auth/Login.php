<?php

namespace App\Filament\Pages\Auth;

use App\Enums\AppTeamRole;
use App\Enums\RoleType;
use App\Mail\MagicLinkEmail;
use App\Models\Role;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\User;
use App\Traits\Filament\NotificationsTrait;
use Filament\Actions\Action;
use Filament\Auth\Http\Responses\Contracts\LoginResponse;
use Filament\Auth\Pages\Login as BaseAuthLogin;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class Login extends BaseAuthLogin
{
    use NotificationsTrait;

    public bool $emailSent = false;

    #[\Override]
    public function getHeading(): string|Htmlable
    {
        return '';
    }

    #[\Override]
    public function authenticate(): ?LoginResponse
    {
        $data = $this->form->getState();
        $email = $data['email'] ?? null;

        if (! $email) {
            return null;
        }

        // Validação de domínio e lista branca
        if ($email !== 'iurygdeoliveira@gmail.com' && ! str_ends_with((string) $email, '@ifto.edu.br') && ! str_ends_with((string) $email, '@estudante.ifto.edu.br')) {
            throw ValidationException::withMessages([
                'data.email' => 'Acesso permitido apenas para administradores autorizados ou e-mails institucionais (@ifto.edu.br / @estudante.ifto.edu.br).',
            ]);
        }

        $user = User::where('email', $email)->first();
        $student = Student::where('email', $email)->first();
        $teacher = Teacher::where('email', $email)->first();

        $isTeacher = ($user && $user->teacher()->exists()) || $teacher;

        // Bloqueio de professores durante as avaliações
        if ($isTeacher && $email !== 'walmir.sousa@ifto.edu.br') {
            $this->redirect(route('solicitar-acesso', ['type' => 'teacher']));

            return null;
        }

        // Se o usuário não existir na base, mas houver estudante correspondente
        if (! $user) {
            if ($student) {
                // Cria o usuário automaticamente para o estudante
                $user = User::create([
                    'name' => $student->name,
                    'email' => $student->email,
                    'password' => bcrypt(Str::random(16)),
                    'email_verified_at' => now(),
                    'is_approved' => true,
                ]);
            } else {
                // Se não for nem usuário nem estudante registrado, redireciona
                $this->redirect(route('solicitar-acesso'));

                return null;
            }
        }

        // Auto-reparação/sincronização do estudante com o usuário
        if ($student) {
            if (! $student->user_id) {
                $student->update(['user_id' => $user->id]);
            }

            if ($student->team_id) {
                $role = Role::firstOrCreate([
                    'name' => RoleType::STUDENT->value,
                    'team_id' => $student->team_id,
                    'guard_name' => 'web',
                ]);

                // Atribui o papel no Spatie Permission
                $user->assignRoleInTeam($role, $student->team);

                // Adiciona o usuário ao time (necessário para o FilaTeams e para evitar ERR_TOO_MANY_REDIRECTS)
                if (! $user->belongsToTeam($student->team)) {
                    $user->teams()->attach($student->team_id, ['role' => AppTeamRole::STUDENT->value]);
                }
            }
        }

        // O bloqueio de professores foi movido para o início do método

        // Gera o Magic Link (válido por 15 minutos)
        $url = URL::temporarySignedRoute('magic.login', now()->addMinutes(15), ['user' => $user->uuid]);

        // Envia o e-mail
        try {
            Mail::to($email)->send(new MagicLinkEmail($url));

            // Atualiza a flag para alterar o botão
            $this->emailSent = true;

            // Notifica na tela
            $this->notifySuccess(
                'Link Enviado!',
                'Um link de acesso seguro foi enviado para o seu e-mail. Ele é válido por 15 minutos.'
            );
        } catch (\Throwable $exception) {
            report($exception);

            $this->notifyDanger(
                'Falha no envio!',
                'Não foi possível enviar o e-mail de acesso. Por favor, tente novamente em alguns instantes.'
            );
        }

        // Limpa o formulário (opcional)
        $this->form->fill();

        return null; // Não retorna a response de login padrão
    }

    #[\Override]
    protected function getFormActions(): array
    {
        return [];
    }

    #[\Override]
    protected function getEmailFormComponent(): TextInput
    {
        /** @var TextInput $component */
        $component = parent::getEmailFormComponent();

        return $component
            ->placeholder('Digitar email institucional aqui')
            ->prefixIcon(Heroicon::Envelope);
    }

    #[\Override]
    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                $this->getEmailFormComponent(),
                // Removidos campos de senha e remember me para o Passwordless

                Actions::make([
                    Action::make('authenticate')
                        ->label(fn (): string => $this->emailSent ? 'Acesse seu email institucional' : 'Receber Link de Acesso')
                        ->submit('authenticate')
                        ->extraAttributes(['class' => 'w-full']),
                ])->fullWidth(),

                // Login do Google (Socialite) colocado em standby:
                /*
                Actions::make([
                    Action::make('googleLogin')
                        ->icon('icon-google')
                        ->label('Logar com email institucional')
                        ->url(route('google.redirect'))
                        ->color('gray')
                        ->extraAttributes(['class' => 'w-full']),
                ])->fullWidth(),
                */
            ])
            ->statePath('data');
    }
}
