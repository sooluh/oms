<?php

namespace App\Filament\Pages;

use Filament\Actions\Action;
use Filament\Facades\Filament;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Pages\Concerns\CanUseDatabaseTransactions;
use Filament\Pages\Page;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\EmbeddedSchema;
use Filament\Schemas\Components\Form;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Support\Enums\Alignment;
use Filament\Support\Exceptions\Halt;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use LogicException;
use SensitiveParameter;
use Throwable;

class Profile extends Page
{
    use CanUseDatabaseTransactions;

    protected static ?string $slug = 'profile';

    protected static bool $shouldRegisterNavigation = false;

    protected string $view = 'filament.pages.profile';

    /**
     * @var array<string, mixed> | null
     */
    public ?array $data = [];

    public function mount(): void
    {
        $this->fillForm();
    }

    public function getUser(): Authenticatable&Model
    {
        $user = Filament::auth()->user();

        if (! $user instanceof Model) {
            throw new LogicException('The authenticated user object must be an Eloquent model to allow the profile page to update it.');
        }

        return $user;
    }

    protected function fillForm(): void
    {
        $user = $this->getUser();

        $this->form->fill([
            'name' => $user->name,
            'email' => $user->email,
        ]);
    }

    public function defaultForm(Schema $schema): Schema
    {
        return $schema
            ->model($this->getUser())
            ->statePath('data');
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make('Tabs')
                    ->tabs([
                        Tab::make('Profile')
                            ->schema([
                                TextInput::make('name')
                                    ->label('Name')
                                    ->required()
                                    ->maxLength(255),

                                TextInput::make('email')
                                    ->label('Email')
                                    ->email()
                                    ->required()
                                    ->maxLength(255)
                                    ->unique(ignoreRecord: true),
                            ]),

                        Tab::make('Password')
                            ->schema([
                                TextInput::make('currentPassword')
                                    ->label('Current Password')
                                    ->password()
                                    ->revealable(filament()->arePasswordsRevealable())
                                    ->currentPassword(guard: Filament::getAuthGuard())
                                    ->required(fn (Get $get): bool => filled($get('password')))
                                    ->dehydrated(false),

                                TextInput::make('password')
                                    ->label('New Password')
                                    ->password()
                                    ->revealable(filament()->arePasswordsRevealable())
                                    ->rule(Password::default())
                                    ->same('passwordConfirmation')
                                    ->dehydrated(fn (#[SensitiveParameter] $state): bool => filled($state))
                                    ->dehydrateStateUsing(fn (#[SensitiveParameter] $state): string => Hash::make($state)),

                                TextInput::make('passwordConfirmation')
                                    ->label('Confirm New Password')
                                    ->password()
                                    ->revealable(filament()->arePasswordsRevealable())
                                    ->required(fn (Get $get): bool => filled($get('password')))
                                    ->dehydrated(false),
                            ]),
                    ]),
            ]);
    }

    public function content(Schema $schema): Schema
    {
        return $schema
            ->components([
                Form::make([EmbeddedSchema::make('form')])
                    ->id('form')
                    ->livewireSubmitHandler('save')
                    ->footer([
                        Actions::make($this->getFormActions())
                            ->alignment(Alignment::Start)
                            ->key('form-actions'),
                    ]),
            ]);
    }

    /**
     * @return array<Action>
     */
    protected function getFormActions(): array
    {
        return [
            $this->getSaveFormAction(),
        ];
    }

    protected function getSaveFormAction(): Action
    {
        return Action::make('save')
            ->label('Save Changes')
            ->submit('save')
            ->keyBindings(['mod+s']);
    }

    public function save(): void
    {
        try {
            $this->beginDatabaseTransaction();

            $data = $this->form->getState();
            $user = $this->getUser();
            $passwordChanged = array_key_exists('password', $data);

            if (array_key_exists('email', $data) && $data['email'] !== $user->getAttributeValue('email')) {
                $data['email_verified_at'] = now();
            }

            $user->update($data);

            $this->commitDatabaseTransaction();

            $this->data['password'] = null;
            $this->data['passwordConfirmation'] = null;

            if ($passwordChanged) {
                Filament::auth()->logout();

                session()->invalidate();
                session()->regenerateToken();

                Notification::make()
                    ->success()
                    ->title('Password changed successfully.')
                    ->body('Please log in with your new password.')
                    ->send();

                $this->redirect(Filament::getLoginUrl());

                return;
            }

            Notification::make()
                ->success()
                ->title('Profile updated successfully.')
                ->send();
        } catch (Halt $exception) {
            $this->rollBackDatabaseTransaction();

            return;
        } catch (Throwable $exception) {
            $this->rollBackDatabaseTransaction();

            throw $exception;
        }
    }

    public function getTitle(): string
    {
        return 'Profile';
    }
}
