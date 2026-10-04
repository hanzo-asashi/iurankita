<?php

declare(strict_types=1);

namespace App\Filament\Resources\Users\Schemas;

use App\Enums\UserRole;
use App\Filament\Resources\Users\Pages\CreateUser;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

final class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                Section::make('Informasi Pengguna')
                    ->description('Detail akun dan hak akses pengguna sistem IuranKita.')
                    ->columnSpanFull()
                    ->schema([
                        Grid::make(2)->schema([
                            TextInput::make('name')
                                ->label('Nama Lengkap')
                                ->maxLength(255)
                                ->required(),
                            TextInput::make('email')
                                ->label('Email')
                                ->maxLength(255)
                                ->unique(ignoreRecord: true)
                                ->email()
                                ->required(),
                        ]),
                        Grid::make(2)->schema([
                            Select::make('role')
                                ->label('Peran')
                                ->options(UserRole::class)
                                ->default(UserRole::Admin)
                                ->native(false)
                                ->required(),
                            TextInput::make('phone')
                                ->label('Nomor Telepon / WhatsApp')
                                ->tel()
                                ->maxLength(20),
                        ]),
                        TextInput::make('password')
                            ->label('Kata Sandi')
                            ->password()
                            ->required(fn ($livewire): bool => $livewire instanceof CreateUser)
                            ->revealable(filament()->arePasswordsRevealable())
                            ->rule(Password::default())
                            ->autocomplete('new-password')
                            ->dehydrated(fn ($state): bool => filled($state))
                            ->dehydrateStateUsing(fn ($state): string => Hash::make($state)),
                    ]),
            ]);
    }
}
