<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ParticipantResource\Pages;
use App\Filament\Resources\ParticipantResource\RelationManagers;
use App\Models\Participant;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Filters\QueryBuilder;
use Filament\Tables\Filters\QueryBuilder\Constraints\Constraint;
use Filament\Tables\Filters\QueryBuilder\Constraints\DateConstraint;
use Filament\Tables\Filters\QueryBuilder\Constraints\Operators\Operator;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ParticipantResource extends Resource
{
    protected static ?string $model = Participant::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $pluralModelLabel = 'Participantes';
    protected static ?string $modelLabel = 'Participante';


    public static function form(Form $form): Form
    {
        return $form;
    }

    public static function table(Table $table): Table
    {

        return $table
            ->modifyQueryUsing(fn (Builder $query) => $query
                ->whereBetween('dateBirth',[Carbon::make('1970-01-01'), Carbon::make('1983-12-31')]))
            ->poll(10)
            ->deferLoading()
            ->defaultSort('created_at')
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->copyable()
                    ->label('NOME')
                    ->searchable(),
                Tables\Columns\TextColumn::make('dateBirth')
                    ->date('d/m/Y')
                    ->label('DATA DE NASCIMENTO')
                    ->searchable(),
                Tables\Columns\TextColumn::make('cpf')
                    ->label('CPF')
                    ->copyable()
                    ->searchable(),

                Tables\Columns\SelectColumn::make('pix')
                    ->options([
                        '0' => 'Não',
                        '1' => 'Sim'
                    ])->label('Possui chave'),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Cadastro')
                    ->dateTime('d-m-Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                QueryBuilder::make()
                    ->constraints([
                        DateConstraint::make('dateBirth')
                        ->label('Periódo Nascimento')


                    ]),


            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageParticipants::route('/'),
        ];
    }
}
