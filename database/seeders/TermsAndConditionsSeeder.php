<?php

namespace Database\Seeders;

use App\Models\Resource;
use App\Models\TermAndCondition;
use Illuminate\Database\Seeder;

/**
 * Popula termos e condições para recursos físicos.
 */
class TermsAndConditionsSeeder extends Seeder
{
    public function run(): void
    {
        // Buscar um recurso físico para teste
        $resource = Resource::where('type', 'physical')->first();

        if (!$resource) {
            $this->command->info('Nenhum recurso físico encontrado. Criando um para teste...');
            return;
        }

        // Termo de Requisição
        TermAndCondition::firstOrCreate(
            [
                'resource_id' => $resource->id,
                'scope' => 'requisition',
                'version' => 1
            ],
            [
                'title' => 'Termos de Requisição',
                'content' => "Ao solicitar este recurso, você concorda com:

1. Devolver o item até a data especificada
2. Manter o item em bom estado
3. Não emprestar o item a terceiros
4. Notificar a biblioteca se danificar o item
5. Aceitar as responsabilidades legais pela guarda do item",
                'is_active' => true
            ]
        );

        // Termo de Retirada
        TermAndCondition::firstOrCreate(
            [
                'resource_id' => $resource->id,
                'scope' => 'pickup',
                'version' => 1
            ],
            [
                'title' => 'Confirmação de Retirada',
                'content' => "Ao retirar este recurso, você confirma:

1. Receber o item em bom estado conforme descrito
2. Aceitar responsabilidade TOTAL pelo item a partir deste momento
3. Concordar com as regras de empréstimo e multas por atraso
4. Devolver em perfeito estado até a data acordada
5. Comunicar qualquer dano imediatamente à biblioteca",
                'is_active' => true
            ]
        );

        // Termo de Extensão
        TermAndCondition::firstOrCreate(
            [
                'resource_id' => $resource->id,
                'scope' => 'extension',
                'version' => 1
            ],
            [
                'title' => 'Termos de Extensão de Prazo',
                'content' => "Você pode estender o prazo até 2 vezes. Cada extensão adiciona 7 dias.

1. Você continua responsável pelo item durante toda a extensão
2. Extensões futuras podem ser recusadas se o item tiver muita procura
3. Multas por atraso continuam sendo aplicadas após a data estendida
4. Você será notificado se houver restrições para esta extensão",
                'is_active' => true
            ]
        );

        // Termo de Devolução (apenas informativo)
        TermAndCondition::firstOrCreate(
            [
                'resource_id' => $resource->id,
                'scope' => 'return',
                'version' => 1
            ],
            [
                'title' => 'Informações Sobre Devolução',
                'content' => "Lembre-se ao devolver:

1. Devolver no mesmo local onde retirou
2. Se estiver atrasado, multa será aplicada: €0,50 por dia
3. Danos ao item podem resultar em cobrança
4. Guarde o comprovante de devolução
5. Você receberá confirmação por email",
                'is_active' => true
            ]
        );

        $this->command->info("✓ Termos criados com sucesso para: {$resource->title}");
    }
}
