<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SubjectResource\Pages;
use App\Filament\Resources\SubjectResource\RelationManagers;
use App\Models\Subject;
use App\Models\Parameter;
use App\Models\Teacher;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction;

class SubjectResource extends Resource
{
    protected static ?string $model = Subject::class;
    protected static ?string $navigationGroup = 'Asignaciones';
    protected static ?string $navigationIcon = 'icon-libro';
    protected static ?int $navigationSort = 6;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('teacher_id')
                    ->label('Docente')
                    ->options(
                        Teacher::pluck('full_name', 'id') // Muestra el full_name y guarda el id
                    )
                    ->required(),
                Forms\Components\Select::make('subject')
                    ->label('Materia')
                    ->options(
                        Parameter::where('parameter', 'MATERIA')
                            ->pluck('value', 'value') // Muestra el valor de value y guarda el valor de value
                    )
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('teacher.full_name')
                    ->label('Docente')
                    ->sortable(),
                Tables\Columns\TextColumn::make('subject')
                    ->label('Materia')
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Creado')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Actualizado')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->headerActions([
                ExportBulkAction::make() //exportar en Excel
                    ->label('Exportar')
                    ->color('success')
                    ->icon('heroicon-s-document-arrow-down'), // icono opcional
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    // Método para cargar la consulta
    protected function getTableQuery()
    {
        return Subject::with('Teacher'); // Carga la relación 'teacher'
    }

    //traducir el titulo
    public static function getLabel(): ?string
    {
        return 'Materias';
    }


    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSubjects::route('/'),
            'create' => Pages\CreateSubject::route('/create'),
            'edit' => Pages\EditSubject::route('/{record}/edit'),
        ];
    }
}
