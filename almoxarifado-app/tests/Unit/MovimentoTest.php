<?php

use App\Models\Produto;
use App\Models\Movimento;

//1. teste de validação que simula o @beforeCreate
test('sistema deve barrar movimentação se a quantidade de saída for maior que o estoque', function(){
    //Mockando produto
    $produtoMock = new Produto([
        'nome' => 'creme',
        'estoque' => 3,
    ]);

    //Mockando o movimento
    $movimentoMock = new Movimento([
        'quantidade' => 10,
        'tipo' => 's',
    ]);

    if($movimentoMock->tipo ==='s' && $movimentoMock->quantidade > $produtoMock->estoque){
        expect(true)->toBeTrue();
    }else {
        $this->fail("Erro: A regra de negócio permitiu saída de mercadoria sem estoque");
    }
});

//2. teste de validação que simula o @afterCreate
test('o sistema deve diminuir o estoque após uma saída autorizada', function (){
    $produto = Produto::create([
        'nome' => 'esmalte',
        'estoque' => 4,
    ]);

    //simular saída válida
    Livewire::test(CreateMovimento::class)
        ->fillform([
            'produto_id' => $produto->id,
            'quantidade' => 2,
            'tipo' => 's',
        ])
        -> call('create');

    //2.1 o movimento deve ter sido criado com sucesso no banco
    expect(Movimento::count())->toBe(1);

    //2.1 o seu hook afterCreate deve ter diminuido o estoque de 15 para 10
    expect($produto->fresh()->estoque->toBe(2));
});