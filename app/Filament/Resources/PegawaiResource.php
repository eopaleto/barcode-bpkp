<?php

namespace App\Filament\Resources;

use Filament\Tables;
use App\Models\Pegawai;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Illuminate\Support\Facades\Auth;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use App\Filament\Traits\SuperAdminReadOnly;
use App\Filament\Resources\PegawaiResource\Pages;

class PegawaiResource extends Resource
{
    use SuperAdminReadOnly;

    protected static ?string $model = Pegawai::class;
    protected static ?string $navigationIcon = 'heroicon-o-users';
    protected static ?string $slug = 'Pegawai';
    protected static ?string $navigationGroup = 'Admin';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('nip_baru')
                    ->label('NIP')
                    ->dehydrated(false),
                TextInput::make('nama')
                    ->label('Nama Lengkap')
                    ->dehydrated(false),

                TextInput::make('unit_kerja')
                    ->label('Unit Kerja')
                    ->dehydrated(false),

                TextInput::make('jabatan')
                    ->label('Jabatan')
                    ->dehydrated(false),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')->rowIndex()->label('No'),
                TextColumn::make('nip_baru')->label('NIP')->searchable(),
                TextColumn::make('nama')->label('Nama Peserta')->searchable(),
                TextColumn::make('unit_kerja')->label('Unit Kerja')->searchable()
                    ->extraAttributes([
                        'style' => 'white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 500px;',
                    ]),
                TextColumn::make('jabatan')->label('Jabatan')->searchable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
        ->bulkActions(
            auth()->user()->hasRole('SuperAdmin')
                ? []
                : [
                    Tables\Actions\BulkActionGroup::make([
                        Tables\Actions\DeleteBulkAction::make(),
                    ]),
                ]
        );
    }

    public static function canAccess(): bool
    {
        /** @var User|null $user */
        $user = Auth::user();

        return $user?->hasAnyRole(['SuperAdmin', 'Admin']);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPegawais::route('/'),
            'create' => Pages\CreatePegawai::route('/create'),
            'edit' => Pages\EditPegawai::route('/{record}/edit'),
        ];
    }
}
