<?php

namespace App\Filament\Resources;

use Filament\Tables;
use App\Models\Pergi;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Tables\Actions\Action;
use Illuminate\Support\Facades\Auth;
use Filament\Forms\Components\Select;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\ImageColumn;
use Filament\Forms\Components\FileUpload;
use App\Filament\Resources\PergiResource\Pages;

class PergiResource extends Resource
{
    protected static ?string $model = Pergi::class;
    protected static ?string $slug = 'Pergi';
    protected static ?string $navigationIcon = 'heroicon-o-arrow-up';
    protected static ?string $navigationGroup = 'Operator';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('pegawai_id')
                    ->label('NIP')
                    ->relationship(
                        name: 'pegawai',
                        titleAttribute: 'nip_baru',
                        modifyQueryUsing: fn($query) => $query->where(function ($q) {
                            $search = request('search');
                            if ($search) {
                                $q->where('nip_lama', 'like', "%{$search}%")
                                    ->orWhere('nip_baru', 'like', "%{$search}%")
                                    ->orWhere('nama', 'like', "%{$search}%");
                            }
                        }),
                    )
                    ->getOptionLabelFromRecordUsing(fn($record) => "{$record->nip_baru}")
                    ->searchable()
                    ->reactive()
                    ->afterStateUpdated(
                        fn($state, callable $set) =>
                        $set('nama', \App\Models\Pegawai::find($state)?->nama)
                    )
                    ->afterStateUpdated(
                        fn($state, callable $set) =>
                        $set('unit_kerja', \App\Models\Pegawai::find($state)?->unit_kerja)
                    )
                    ->required(),

                TextInput::make('nama')
                    ->label('Nama Lengkap')
                    ->readOnly()
                    ->afterStateHydrated(function ($set, $record) {
                        if ($record?->pegawai) {
                            $set('nama', $record->pegawai->nama);
                        }
                    }),

                TextInput::make('unit_kerja')
                    ->label('Unit Kerja')
                    ->readOnly()
                    ->afterStateHydrated(function ($set, $record) {
                        if ($record?->pegawai) {
                            $set('unit_kerja', $record->pegawai->unit_kerja);
                        }
                    }),

                TextInput::make('jumlah_koper')
                    ->label('Jumlah Koper')
                    ->numeric()
                    ->reactive()
                    ->required(),

                FileUpload::make('foto_koper')
                    ->label('Foto Koper')
                    ->image()
                    ->multiple()
                    ->directory('koper/pergi')
                    ->preserveFilenames(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')->rowIndex()->label('No'),
                TextColumn::make('pegawai.nip_baru')->label('NIP'),
                TextColumn::make('pegawai.nama')->label('Nama'),
                TextColumn::make('pegawai.jabatan')->label('Jabatan'),
                TextColumn::make('pegawai.unit_kerja')->label('Unit Kerja'),
                TextColumn::make('jumlah_koper')->label('Jumlah Koper'),
                TextColumn::make('created_at')->label('Dibuat')->dateTime(),
                ImageColumn::make('barcode')
                    ->label('Barcode')
                    ->disk('public')
                    ->getStateUsing(fn($record) => 'barcode/pergi/' . $record->barcode)
                    ->size('150'),
                TextColumn::make('status')
                    ->label('Status')
                    ->icon(fn($record) => match ((int) $record->status) {
                        0 => 'heroicon-o-clock',
                        1 => 'heroicon-o-check-circle',
                    })
                    ->getStateUsing(fn($record) => match ((int) $record->status) {
                        0 => 'Sedang Diproses',
                        1 => 'Sudah Diambil',
                    })
                    ->color(fn($record) => match ((int) $record->status) {
                        0 => 'primary',
                        1 => 'success',
                    })
            ])
            ->filters([
                //
            ])
            ->actions([
                Action::make('cetak')
                    ->icon('heroicon-m-printer')
                    ->color('success')
                    ->label('Cetak')
                    ->url(fn($record) => route('barcode.print.pergi', $record->id))
                    ->openUrlInNewTab(),
                Tables\Actions\DeleteAction::make(),

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

        return $user?->hasAnyRole(['Operator', 'SuperAdmin']);
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
            'index' => Pages\ListPergis::route('/'),
            'create' => Pages\CreatePergi::route('/create'),
            'edit' => Pages\EditPergi::route('/{record}/edit'),
        ];
    }
}
