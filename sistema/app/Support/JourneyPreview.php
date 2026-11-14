<?php

namespace App\Support;

/** Cenário fictício da T04. Não é cadastro, seed de produção ou autorização. */
class JourneyPreview
{
    public static function data(): array
    {
        return [
            'family' => [
                'actor' => 'Bruno Exemplo',
                'children' => [
                    ['id' => 'lucas', 'name' => 'Lucas Exemplo', 'shortName' => 'Lucas', 'initial' => 'L', 'classroom' => 'Ponte · Caminhos', 'level' => 3],
                    ['id' => 'lia', 'name' => 'Lia Exemplo', 'shortName' => 'Lia', 'initial' => 'L', 'classroom' => 'Jardim · Sementes', 'level' => 1],
                ],
            ],
            'notice' => ['id' => 'encontro-17', 'version' => 1, 'title' => 'Nosso encontro de terça foi cancelado.'],
            'attendance' => [
                'actor' => 'Ana Exemplo',
                'classroom' => 'Jardim · Sementes',
                'sessions' => [
                    ['id' => 'aula-24', 'label' => '24 de novembro · Aula realizada', 'cancelled' => false],
                    ['id' => 'aula-17', 'label' => '17 de novembro · Encontro cancelado', 'cancelled' => true],
                ],
                'students' => [
                    ['id' => 'lia', 'name' => 'Lia Exemplo', 'initial' => 'L'],
                    ['id' => 'mateus', 'name' => 'Mateus Exemplo', 'initial' => 'M'],
                    ['id' => 'sofia', 'name' => 'Sofia Exemplo', 'initial' => 'S'],
                ],
            ],
            'order' => [
                'id' => 'PED-001', 'actor' => 'Eva Exemplo', 'requester' => 'Bruno Exemplo',
                'beneficiary' => 'Lia Exemplo', 'product' => 'Apostila · Módulo 1 · Nível 1',
                'quantity' => 2, 'unit' => 'Núcleo Jardim',
            ],
        ];
    }
}
