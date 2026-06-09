<?php

namespace App\Filament\Resources\Movimentos\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class MovimentoForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                // TextInput::make('produto_id')
                Select::make("produto_id")
                    ->label('produto')
                    ->relationship(name: 'produto', titleAttribute: 'nome')
                    ->searchable() // permite pesquisar pelo nome
                    ->preload() // carrega a lista automaticamente
                    ->required(),
                TextInput::make('quantidade')
                    ->required()
                    ->numeric(),
                Select::make('tipo')
                    ->options(['e' => 'E', 's' => 'S'])
                    ->required(),
            ]);
    }
}
