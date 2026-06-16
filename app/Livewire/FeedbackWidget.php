<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Models\Feedback;
use App\Models\User;
use App\Traits\Filament\NotificationsTrait;
use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Facades\Filament;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class FeedbackWidget extends Component implements HasActions, HasSchemas
{
    use InteractsWithActions;
    use InteractsWithSchemas;
    use NotificationsTrait;

    public function feedbackAction(): Action
    {
        return Action::make('feedback')
            ->label(__('Feedback'))
            ->slideOver()
            ->modalHeading(__('Enviar feedback'))
            ->fillForm(function (array $arguments): array {
                return [
                    'page_url' => $arguments['page_url'] ?? url()->current(),
                    'page_title' => $arguments['page_title'] ?? null,
                    'message' => '',
                ];
            })
            ->schema([
                TextInput::make('page_title')
                    ->label(__('Página atual'))
                    ->disabled()
                    ->dehydrated()
                    ->placeholder(__('—')),
                Hidden::make('page_url'),
                Textarea::make('message')
                    ->label(__('Mensagem'))
                    ->required()
                    ->rows(4)
                    ->placeholder(__('Descreva seu feedback ou sugestão...')),
            ])
            ->action(function (array $data): void {
                /** @var User $user */
                $user = Filament::auth()->user();

                Feedback::create([
                    'user_id' => $user->getAuthIdentifier(),
                    'page_url' => $data['page_url'] ?? url()->current(),
                    'page_title' => $data['page_title'] ?? null,
                    'message' => $data['message'],
                    'team_id' => Filament::getTenant()?->id ?? null,
                    'panel' => filament()->getCurrentPanel()?->getId(),
                ]);

                $this->notifySuccess(__('Feedback enviado com sucesso'));
            });
    }

    public function render(): View
    {
        return view('livewire.feedback-widget');
    }
}
