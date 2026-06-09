<?php

namespace App\Filament\Resources\Movimentos\Pages;

use App\Filament\Resources\Movimentos\MovimentoResource;
use Filament\Resources\Pages\CreateRecord;
use App\Models\Produto;
use App\Models\Movimento;
use Filament\Notifications\Notification;

class CreateMovimento extends CreateRecord
{

    /**
* Valida o estoque antes da criação de um movimento.
* @param  $dados Recebe os dados do formulário 
* @param int $produto_id Identificador do produto selecionado 
* @param int $quantidade Quantidade informada no movimento de entrada ou saída
* @param string $tipo Tipo do movimento ('e'- entrada e 's' - saída)
* @return void
* @throws \Exception Interrompe a criação do movimento caso o estoque seja insuficiente
*/ 

    protected static string $resource = MovimentoResource::class;

    //Criando nossos hooks

    protected function beforeCreate(): void
    {
        $dados = $this->data;

        // selecionando o produto/qtd e tipo pelo id recebido na lista
        $produto = Produto::find($dados['produto_id']);
        $quantidade = $dados['quantidade'];
        $tipo = $dados['tipo'];

        // verificar se é uma saída e se o estoque é sufuciente
        if ($tipo === 's' && $quantidade > $produto->estoque) {
            // notificar o usuário sobre o estoque insuficiente
            Notification::make()
                ->danger()
                ->title('Estoque insufuciente!')
                ->body("O estoque de '{$produto->nome}' é de apenas '{$produto->estoque}' unidade, mas você tentou retirar {$quantidade}.")
                ->send();

            $this->halt(); // impede a criação do movimento

        }
    }

    /**
 * Atualiza o estoque do produto após a criação de um movimento.
 *
 * @param object $movimento Registro do movimento criado
 * @param string $movimento->tipo Tipo do movimento ('e'- entrada ou 's'- saída)
 * @param int $movimento->quantidade Quantidade movimentada no estoque
 * @param object $produto Produto relacionado ao movimento
 *
 * @return void
 * @throws \Exception Pode ocorrer erro caso o relacionamento com o produto esteja inválido
 */
// hook - remover ou aumentar o estoque
    protected function afterCreate(): void
    {
        $movimento = $this->getRecord(); // registro do movimento criado
        $produto = $movimento->produto; // produto relacionado ao movimento

        if ($movimento->tipo === 'e') {
            // Entrada: Aumentar o estoque
            $produto->increment('estoque', $movimento->quantidade);
        } else {
            // saída: diminuir o estoque
            $produto->decrement('estoque', $movimento->quantidade);
        }
    }
}
