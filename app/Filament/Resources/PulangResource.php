<?php

namespace App\Filament\Resources;

use Filament\Tables;
use App\Models\Pulang;
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
use Filament\Tables\Actions\CreateAction;
use App\Filament\Traits\SuperAdminReadOnly;
use App\Filament\Resources\PulangResource\Pages;

class PulangResource extends Resource
{
    use SuperAdminReadOnly;
    protected static ?string $model = Pulang::class;
    protected static ?string $navigationIcon = 'heroicon-o-arrow-uturn-left';
    protected static ?string $slug = 'Pulang';
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
                    ->required()
                    ->multiple()
                    ->acceptedFileTypes([
                        'image/png',
                        'image/jpeg',
                        'image/heic',
                    ])
                    ->directory('koper/pulang'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')->rowIndex()->label('No'),
                TextColumn::make('pegawai.nip_baru')->label('NIP'),
                TextColumn::make('pegawai.nama')->label('Nama'),
                TextColumn::make('pegawai.unit_kerja')->label('Unit Kerja'),
                TextColumn::make('jumlah_koper')->label('Jumlah Koper'),
                TextColumn::make('created_at')->label('Dibuat')->dateTime(),
                TextColumn::make('foto_koper')
                    ->label('Foto Koper')
                    ->formatStateUsing(function ($state) {
                        return empty($state) ? 'Tidak ada foto' : 'Lihat Preview';
                    })
                    ->icon('heroicon-o-eye')
                    ->color('success')
                    ->action(
                        Tables\Actions\Action::make('preview')
                            ->label('Lihat Preview')
                            ->icon('heroicon-o-eye')
                            ->color('success')
                            ->modalHeading('Preview Foto Koper')
                            ->modalWidth('4xl')
                            ->modalContent(
                                fn($record) =>
                                view('filament.preview-foto', [
                                    'fotos' => is_array($record->foto_koper)
                                        ? $record->foto_koper
                                        : json_decode($record->foto_koper, true),
                                ])
                            )
                            ->modalSubmitAction(false)
                            ->modalCancelAction(false)
                    ),
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
            ->actions(auth()->user()->hasRole('SuperAdmin') ? [] : [
                Action::make('cetak')
                    ->icon('heroicon-m-printer')
                    ->color('success')
                    ->label('Cetak')
                    ->url(fn($record) => route('barcode.print.pulang', $record->id))
                    ->openUrlInNewTab(),
                Tables\Actions\DeleteAction::make(),

            ])
            ->bulkActions(
                auth()->user()->hasRole('SuperAdmin') ? [] : [
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
            'index' => Pages\ListPulangs::route('/'),
            'create' => Pages\CreatePulang::route('/create'),
            'edit' => Pages\EditPulang::route('/{record}/edit'),
        ];
    }
}
