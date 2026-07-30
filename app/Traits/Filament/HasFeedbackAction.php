<?php

declare(strict_types=1);

namespace App\Traits\Filament;

use App\Models\Feedback;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Facades\Filament;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Illuminate\Support\Str;
use Filament\Notifications\Notification;
use App\Traits\Filament\NotificationsTrait;

trait HasFeedbackAction
{
    use NotificationsTrait;

    public function cacheInteractsWithHeaderActions(): void
    {
        parent::cacheInteractsWithHeaderActions();

        $action = $this->getFeedbackAction();
        $this->cacheAction($action);
        $this->cachedHeaderActions[] = $action;
    }

    protected function getFeedbackAction(): Action
    {
        return Action::make('feedback')
            ->label(__('Feedback'))
            ->color('gray')
            ->slideOver()
            ->modalHeading(__('Enviar feedback'))
            ->fillForm(fn (array $arguments): array => [
                'page_url' => $arguments['page_url'] ?? url()->current(),
                'page_title' => $arguments['page_title'] ?? null,
                'message' => '',
            ])
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
                    'team_id' => Filament::getTenant()?->getKey() ?? null,
                    'panel' => filament()->getCurrentPanel()?->getId(),
                ]);

                $this->notifySuccess(__('Feedback enviado com sucesso'));
            });
    }
}
