# TASKS — Login por Link Mágico Integrado (GeaD)

> Status: RASCUNHO | Total: 10 tarefas

## Backlog de Implementação

### Grupo 1: Infraestrutura de Banco de Dados e Models (AC-1, AC-4)

#### **T1.** Migrations: Tokens e CNPJ. (AC-1, AC-4)
- **Status:** ✅ Concluída
- **Justificativa:** É a base relacional definida no PLAN. Precisamos do schema pronto antes de escrever Models e os testes que invocam a base de dados. Consultando `laravel-best-practices`, migrations devem conter as definições de index e unique nativas.
- **Critério de Pronto:** Execução limpa do `php artisan migrate`.
- **Proposta de Implementação:**
... (Código gerado)

#### **T2.** Models: MagicLoginToken e ajustes no Team. (AC-1, AC-4)
- **Status:** ✅ Concluída
- **Justificativa:** Define os casts sugeridos pelo `laravel-best-practices` e o Scope de verificação de expiração para reuso no código.
- **Critério de Pronto:** Models refletindo exatamente a estrutura do DBML com casts atribuídos no método `casts()`.
- **Proposta de Implementação:**
... (Código gerado)

#### **T3.** Pest: Models e Scopes (RED/GREEN). (AC-1, AC-4)
- **Status:** ✅ Concluída (Testes passando no CLI)
- **Justificativa:** Garantir unitariamente o comportamento de exclusão automática temporal do `scopeValid`. Conforme a skill `pest-testing`, usamos sintaxe funcional `it()` e a API do `expect()`.
- **Proposta de Implementação:**
```php
use App\Models\MagicLoginToken;

it('filters expired tokens using scopeValid', function () {
    MagicLoginToken::create(['email' => 'test@ifto.edu.br', 'token' => 'abc', 'expires_at' => now()->subMinute()]);
    MagicLoginToken::create(['email' => 'test2@ifto.edu.br', 'token' => 'def', 'expires_at' => now()->addMinute()]);
    
    expect(MagicLoginToken::valid()->count())->toBe(1);
});
```

### Grupo 2: Disparo e Notificação (AC-1, AC-3)

#### **T4.** Criar Notificação `MagicLinkNotification`. (AC-1)
- **Status:** ✅ Concluída
- **Justificativa:** Classe nativa do Laravel para envio formatado. `laravel-best-practices` indica separar a View do e-mail da lógica transacional da Action e permitir filas no futuro.
- **Critério de Pronto:** Notificação configurada via construtor e array `toMail()`.
- **Proposta de Implementação:**
... (Código gerado)

#### **T5.** Pest: Envio do Link Mágico e Bloqueio Admin (RED). (AC-1, AC-3)
- **Status:** ✅ Concluída (Testes passando no CLI)
- **Justificativa:** Conforme `pest-testing`, vamos mockar o envio usando `Notification::fake()`. Devemos testar se a role admin gera uma Exception.
- **Proposta de Implementação:**
... (Código gerado)

#### **T6.** Action: `SendMagicLinkAction` (GREEN). (AC-1, AC-3)
- **Status:** ✅ Concluída
- **Justificativa:** Consultando `laravel-permission-development`, usamos `$user->hasRole()` para validar a segurança de admins. Delegar pra Action respeita o `laravel-simplifier`.
- **Proposta de Implementação:**
```php
namespace App\Actions\Auth;

use App\Models\User;
use App\Models\MagicLoginToken;
use App\Notifications\Auth\MagicLinkNotification;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class SendMagicLinkAction {
    public function execute(string $email): void {
        $user = User::where('email', $email)->firstOrFail();
        
        if ($user->hasRole('admin')) { 
            throw ValidationException::withMessages(['email' => 'Contas administrativas exigem senha.']);
        }
        
        $plainToken = Str::random(64);
        MagicLoginToken::updateOrCreate(
            ['email' => $email],
            ['token' => hash('sha256', $plainToken), 'expires_at' => now()->addMinutes(15)]
        );
        
        $user->notify(new MagicLinkNotification($plainToken));
    }
}
```

### Grupo 3: Callback e Autenticação (AC-4, AC-5)

#### **T7.** Pest: Autenticação via Hash e Onboarding (RED). (AC-4, AC-5)
- **Status:** ✅ Concluída (Testes passando no CLI)
- **Justificativa:** Testar exceptions no hash de acordo com a sintaxe funcional do `pest-testing`.
- **Proposta de Implementação:**
... (Código gerado)

#### **T8.** Action: `AuthenticateMagicLinkAction` (GREEN). (AC-4)
- **Status:** ✅ Concluída
- **Justificativa:** Mantém rotas limpas. Manipulação correta do array associativo `Membership` de acordo com Filament tenant conventions.
- **Critério de Pronto:** Testes de T7 passando no CLI.
- **Proposta de Implementação:**
... (Código gerado)

#### **T9.** Rota Web e Controller de Callback. (AC-4, AC-5)
- **Status:** ✅ Concluída
- **Justificativa:** Ponto de entrada do link. Uso de `try/catch` para devolver `ValidationException` na view do Filament.
- **Proposta de Implementação:**
... (Código gerado)

### Grupo 4: Interface do Filament (AC-1, AC-2, AC-3, AC-6)

#### **T10.** Override Componente Livewire `Login`. (AC-1, AC-2, AC-6)
- **Status:** ✅ Concluída
- **Justificativa:** `filament-blueprint` exige namespaces estritos (`Filament\Auth\Pages\Login`). Isso garante a herança exata das traits de rate limit nativas.
- **Critério de Pronto:** UI de login com placeholders reativos (toggle mode e tela de sucesso) em Livewire.
- **Proposta de Implementação:**
... (Código gerado e registrado no BasePanelProvider)

---

## Resumo de Rastreabilidade
| AC ID | Tasks | Status |
|-------|-------|--------|
| AC-1  | T1, T2, T3, T4, T5, T6, T10 | 🔲 |
| AC-2  | T10 | 🔲 |
| AC-3  | T5, T6, T10 | 🔲 |
| AC-4  | T1, T2, T3, T7, T8, T9 | 🔲 |
| AC-5  | T7, T8, T9 | 🔲 |
| AC-6  | T10 | 🔲 |

## Checkpoints de Qualidade
- [x] TDD: Toda implementação (🔨) é precedida ou acompanhada por um teste (🧪)?
- [x] Atômico: Cada task é pequena e foca em uma única responsabilidade?
- [x] Rastreável: Todas as tarefas estão amarradas a um AC?
