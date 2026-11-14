<?php

namespace App\Support;

use Illuminate\Support\Str;

final class FieldIcon
{
    public static function resolve(string $name, string $type = 'text', string $control = 'input'): ?string
    {
        if ($control === 'input') {
            if (in_array($type, ['hidden', 'checkbox', 'radio', 'range', 'color', 'file', 'button', 'submit', 'reset', 'image'], true)) {
                return null;
            }

            $typeIcon = match ($type) {
                'email' => 'mail',
                'tel' => 'phone',
                'password' => 'lock',
                'search' => 'search',
                'url' => 'link',
                'number' => 'hash',
                'date', 'datetime-local', 'month', 'week' => 'calendar',
                'time' => 'clock',
                default => null,
            };

            if ($typeIcon !== null) {
                return $typeIcon;
            }
        }

        $segments = preg_split('/[.\[\]]+/', $name, -1, PREG_SPLIT_NO_EMPTY);
        $field = Str::snake(Str::ascii(end($segments) ?: $name));
        $field = str_replace('-', '_', $field);
        $field = preg_replace('/_id$/', '', $field);

        return match ($field) {
            'name', 'full_name', 'fullname', 'nome', 'nome_completo', 'responsavel', 'guardian' => 'user',
            'email', 'e_mail' => 'mail',
            'phone', 'telephone', 'telefone', 'celular', 'whatsapp' => 'phone',
            'classroom', 'class', 'turma', 'group', 'grupo', 'nivel', 'level' => 'users',
            'church', 'igreja', 'nucleo', 'regional', 'region', 'unidade' => 'building',
            'address', 'endereco', 'city', 'cidade', 'estado', 'state', 'cep', 'postal_code' => 'pin',
            'birth_date', 'data_nascimento', 'nascimento', 'date', 'data' => 'calendar',
            'note', 'notes', 'observacao', 'observacoes', 'message', 'mensagem', 'description', 'descricao' => 'message',
            'password', 'senha' => 'lock',
            'search', 'busca', 'pesquisa' => 'search',
            'url', 'website', 'site' => 'link',
            'cpf', 'rg', 'document', 'documento', 'quantity', 'quantidade' => 'hash',
            default => match ($control) {
                'select' => 'list',
                'textarea' => 'message',
                default => 'text',
            },
        };
    }
}
