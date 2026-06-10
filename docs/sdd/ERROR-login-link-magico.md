# Erros Encontrados: Login Link Mágico

Este documento tem como objetivo registrar os bugs e ajustes identificados durante a execução manual e homologação da feature de Login via Link Mágico.

## Lista de Erros

### 1. Class "Filament\Schemas\Components\Placeholder" not found
- **Sintoma:** Ao acessar a rota GET `/login`, a aplicação apresentou "Internal Server Error" com a exception `Class "Filament\Schemas\Components\Placeholder" not found`.
- **Causa:** No Filament v5, o formulário agora recebe um `Filament\Schemas\Schema` em vez de `Filament\Forms\Form`, mas os componentes visuais (`Placeholder`, `TextInput`, etc.) continuam dentro do namespace `Filament\Forms\Components`. Anteriormente, ambos foram alterados incorretamente para `Filament\Schemas\Components\...` durante as refatorações.
- **Solução:** O namespace dos componentes na página `App\Filament\Pages\Auth\Login` foi corrigido retornando-os para `use Filament\Forms\Components\Placeholder;` etc.
- **Status:** Resolvido ✅

### 2. Correção de Depreciação: Componente Placeholder e Ajuste de Margem
- **Sintoma:** O uso do componente `Placeholder` no formulário em `App\Filament\Pages\Auth\Login` gerava um aviso de depreciação indicando que o mesmo deveria ser substituído por `TextEntry` com o método `state()`. Além disso, o usuário solicitou um espaçamento maior entre o logotipo e a badge da "Gerência de Ensino".
- **Causa:** O componente `Placeholder` foi descontinuado nas atualizações do Filament e o design original não previa uma margem superior extra.
- **Solução:** A importação `use Filament\Forms\Components\Placeholder` foi alterada para `use Filament\Infolists\Components\TextEntry`. Todas as instâncias de `Placeholder::make()` foram substituídas por `TextEntry::make()` e seus métodos `->content()` substituídos por `->state()`. Foi adicionada a classe Tailwind `mt-6` na div da primeira badge para aplicar o espaçamento solicitado em relação à logo.
- **Status:** Resolvido ✅

### 3. Correção de Alerta de IDE (Intelephense): Undefined type e Método não reconhecido
- **Sintoma:** O editor de código (IDE) estava reportando o aviso "Undefined type" para as classes `SendMagicLinkAction` (em Login.php) e `MagicLinkNotification` (em SendMagicLinkAction.php), além de falhar em reconhecer a chamada de `execute()`.
- **Causa:** Erros de "Undefined type" nestes casos geralmente ocorrem quando a extensão PHP Intelephense (ou similar no VS Code) fica com o cache dessincronizado após a criação de novos arquivos, falhando ao indexar as classes corretamente, apesar de namespace e arquivos estarem 100% íntegros. Adicionalmente, o falso-positivo na chamada de `execute()` ocorria devido ao uso do helper genérico `app()`.
- **Solução (Código):** A instância do `app(SendMagicLinkAction::class)` foi alterada para instanciação direta `(new SendMagicLinkAction())->execute($email)` para resolver o falso-positivo do método. 
- **Solução (IDE):** Como a classe `MagicLinkNotification` está perfeitamente implementada, esse aviso nela é puramente um "glitch" da IDE. Para corrigir definitivamente no seu VS Code, deve-se pressionar `Ctrl + Shift + P` e executar o comando **`Intelephense: Index Workspace`** (ou reiniciar a janela do VS Code).
- **Status:** Resolvido ✅
