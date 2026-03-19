<?php

namespace App\Filament\User\Pages;

use Filament\Forms;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Pages\Page;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Filament\Notifications\Notification;
use App\Models\User;

class Profile extends Page implements HasForms
{
    use InteractsWithForms;

    protected string $view = 'filament.user.pages.profile';
    protected static bool $shouldRegisterNavigation = false;
    public ?array $data = [];

    public function mount(): void
    {
        $user = Auth::user();
        if (!$user) return;

        $address = $user->address;

        $this->form->fill([
            'name' => $user->name,
            'email' => $user->email,
            'phone_no' => $user->phone_no,
            'gender' => $user->gender,
            'address' => [
                'name' => $address?->name,
                'phone_no' => $address?->phone_no,
                'email' => $address?->email,
                'province' => $address?->province,
                'district' => $address?->district,
                'address_line1' => $address?->address_line1,
                'address_line2' => $address?->address_line2,
                'nearest_landmark' => $address?->nearest_landmark,
            ],
        ]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->statePath('data')
            ->components([

                //PROFILE SECTION
                Section::make('Profile Information')
                    ->description('Update your basic account details')
                    ->icon('heroicon-o-user-circle')
                    ->columns(['sm' => 1, 'md' => 2])
                    ->components([
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('Your full name'),

                        Forms\Components\TextInput::make('email')
                            ->email()
                            ->required()
                            ->maxLength(255)
                            ->unique(User::class, 'email', ignorable: Auth::user())
                            ->placeholder('example@email.com'),

                        Forms\Components\TextInput::make('phone_no')
                            ->tel()
                            ->maxLength(20)
                            ->unique(User::class, 'phone_no', ignorable: Auth::user())
                            ->placeholder('+977 98XXXXXXXX'),

                        Forms\Components\Select::make('gender')
                            ->options([
                                'male' => 'Male',
                                'female' => 'Female',
                                'other' => 'Other',
                            ])
                            ->native(false)
                            ->placeholder('Select your gender'),
                    ]),

                //ADDRESS SECTION
                Section::make('Address Information')
                    ->description('Manage your delivery address')
                    ->icon('heroicon-o-map-pin')
                    ->columns(['sm' => 1, 'md' => 2])
                    ->components([
                        Forms\Components\TextInput::make('address.name')
                            ->label('Receiver Name')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('John Doe'),

                        Forms\Components\TextInput::make('address.phone_no')
                            ->label('Phone Number')
                            ->required()
                            ->maxLength(20)
                            ->placeholder('+977 98XXXXXXXX'),

                        Forms\Components\TextInput::make('address.email')
                            ->email()
                            ->maxLength(255)
                            ->placeholder('Receiver email (optional)'),

                        Forms\Components\TextInput::make('address.province')
                            ->required()
                            ->maxLength(100)
                            ->placeholder('Province'),

                        Forms\Components\TextInput::make('address.district')
                            ->required()
                            ->maxLength(100)
                            ->placeholder('District'),

                        Forms\Components\TextInput::make('address.address_line1')
                            ->label('Street Address')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('Street name, House no.'),

                        Forms\Components\TextInput::make('address.address_line2')
                            ->label('Address Line 2')
                            ->maxLength(255)
                            ->placeholder('Optional'),

                        Forms\Components\TextInput::make('address.nearest_landmark')
                            ->label('Nearest Landmark')
                            ->maxLength(255)
                            ->placeholder('e.g., Near City Park'),
                    ]),

                //PASSWORD SECTION
                Section::make('Security Settings')
                    ->description('Change your password securely')
                    ->icon('heroicon-o-lock-closed')
                    ->collapsible()
                    ->collapsed(true)
                    ->columns(2)
                    ->components([
                        Forms\Components\TextInput::make('current_password')
                            ->password()
                            ->revealable()
                            ->label('Current Password')
                            ->dehydrated(false)
                            ->placeholder('Enter current password')
                            ->rule(function () {
                                return function ($attribute, $value, $fail) {
                                    if (filled($value) && !Hash::check($value, Auth::user()->password)) {
                                        $fail('Current password is incorrect');
                                    }
                                };
                            }),

                        Forms\Components\TextInput::make('password')
                            ->password()
                            ->revealable()
                            ->label('New Password')
                            ->dehydrated(fn($state) => filled($state))
                            ->minLength(8)
                            ->placeholder('At least 8 characters')
                            ->rule(function ($get) {
                                return function ($attribute, $value, $fail) use ($get) {
                                    if (filled($get('current_password')) && blank($value)) {
                                        $fail('New password is required when current password is entered.');
                                    }
                                };
                            }),

                        Forms\Components\TextInput::make('password_confirmation')
                            ->password()
                            ->revealable()
                            ->label('Confirm New Password')
                            ->dehydrated(false)
                            ->placeholder('Re-enter new password')
                            ->rule(function ($get) {
                                return function ($attribute, $value, $fail) use ($get) {
                                    if (filled($get('password')) && $value !== $get('password')) {
                                        $fail('Passwords do not match.');
                                    }
                                };
                            }),
                    ]),
            ]);
    }

    public function save(): void
    {
        $user = Auth::user();
        if (!$user) return;

        $data = $this->form->getState();

        $userData = $data;
        unset($userData['address']);

        if (!empty($data['password'])) {
            $userData['password'] = Hash::make($data['password']);
        } else {
            unset($userData['password']);
        }

        $user->update($userData);

        $addressData = $data['address'] ?? [];

        if (!empty($addressData)) {
            $user->address()->updateOrCreate(
                ['user_id' => $user->id],
                $addressData
            );
        }

        Notification::make()
            ->title('Profile updated successfully')
            ->success()
            ->send();
    }
}
