<?php

declare(strict_types=1);

namespace App\Filament\Resources\Households\Tables;

use App\Enums\ConstructionStatus;
use App\Enums\OccupancyStatus;
use App\Models\Household;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

final class HouseholdsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query) => $query->withCount([
                'constructionProjects as active_construction_count' => fn (Builder $q) => $q->where('status', ConstructionStatus::Active),
            ]))
            ->columns([
                TextColumn::make('house_code')
                    ->label('Kode Rumah')
                    ->sortable()
                    ->searchable()
                    ->weight('bold')
                    ->copyable(),
                TextColumn::make('block')
                    ->label('Blok')
                    ->alignCenter()
                    ->sortable(),
                TextColumn::make('house_number')
                    ->label('No.')
                    ->alignCenter()
                    ->sortable(),
                TextColumn::make('head_of_family')
                    ->label('Kepala Keluarga')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('occupancy_status')
                    ->label('Status Hunian')
                    ->badge()
                    ->sortable(),
                TextColumn::make('construction_projects_count')
                    ->label('Total Proyek')
                    ->counts('constructionProjects')
                    ->badge()
                    ->color('gray')
                    ->alignCenter()
                    ->sortable(),
                TextColumn::make('active_construction')
                    ->label('Pembangunan Aktif')
                    ->state(fn (Household $record): string => ($record->active_construction_count ?? 0) > 0 ? "{$record->active_construction_count} Aktif" : '-')
                    ->badge()
                    ->alignCenter()
                    ->color(fn (string $state): string => $state !== '-' ? 'warning' : 'gray'),
                TextColumn::make('phone')
                    ->label('Telepon')
                    ->searchable()
                    ->copyable()
                    ->placeholder('-'),
                IconColumn::make('is_active')
                    ->label('Aktif')
                    ->boolean()
                    ->alignCenter()
                    ->sortable(),
            ])
            ->defaultSort('house_code', 'asc')
            ->emptyStateHeading('Belum Ada Data Rumah')
            ->emptyStateDescription('Tambahkan data rumah warga untuk memulai pendataan dan penagihan iuran.')
            ->filters([
                SelectFilter::make('occupancy_status')
                    ->label('Status Hunian')
                    ->options(OccupancyStatus::class)
                    ->native(false),
                TernaryFilter::make('is_active')
                    ->label('Status Rumah')
                    ->trueLabel('Hanya Rumah Aktif')
                    ->falseLabel('Hanya Rumah Nonaktif')
                    ->native(false),
                Filter::make('has_active_construction')
                    ->label('Memiliki Pembangunan Aktif')
                    ->query(fn (Builder $query): Builder => $query->whereHas(
                        'constructionProjects',
                        fn (Builder $q) => $q->where('status', ConstructionStatus::Active)
                    )),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make()
                    ->disabled(fn (Household $record): bool => $record->invoices()->exists())
                    ->tooltip(fn (Household $record): ?string => $record->invoices()->exists() ? 'Rumah ini memiliki riwayat tagihan dan tidak dapat dihapus.' : null),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->action(function (Collection $records): void {
                            $blocked = $records->filter(fn (Household $household): bool => $household->invoices()->exists());

                            if ($blocked->isNotEmpty()) {
                                Notification::make()
                                    ->title('Penghapusan Dibatalkan')
                                    ->body("{$blocked->count()} rumah yang dipilih memiliki riwayat tagihan/keuangan sehingga tidak dapat dihapus.")
                                    ->danger()
                                    ->send();

                                return;
                            }

                            $records->each->delete();

                            Notification::make()
                                ->title('Berhasil Dihapus')
                                ->body('Data rumah yang dipilih berhasil dihapus.')
                                ->success()
                                ->send();
                        }),
                ]),
            ]);
    }
}
