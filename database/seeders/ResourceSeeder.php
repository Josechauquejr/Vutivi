<?php

namespace Database\Seeders;

use App\Models\DigitalResource;
use App\Models\PhysicalResource;
use App\Models\Resource;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ResourceSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Popula a base de dados com recursos ficticios para a biblioteca.
     */
    public function run(): void
    {
        $owner = User::firstOrCreate(
            ['email' => 'biblioteca@vutivi.test'],
            [
                'name' => 'Equipa Biblioteca',
                'username' => 'equipa.biblioteca',
                'password' => 'password',
            ]
        );

        $resources = [
            [
                'resource' => [
                    'title' => 'Guia do Investidor',
                    'description' => 'Resumo pratico com principios de investimento, perguntas de revisao e checkpoints para acompanhar a leitura.',
                    'type' => 'digital',
                    'status' => 'available',
                    'quantity_available' => 1,
                    'owner_id' => $owner->id,
                ],
                'digital' => [
                    'file_path' => 'biblioteca/guia-do-investidor.pdf',
                    'access_type' => 'download',
                    'access_days' => 18,
                ],
            ],
            [
                'resource' => [
                    'title' => 'Linha do Tempo da Lideranca',
                    'description' => 'Slides com eventos-chave, contexto historico e notas curtas para revisar lideranca e impacto social.',
                    'type' => 'digital',
                    'status' => 'active',
                    'quantity_available' => 1,
                    'owner_id' => $owner->id,
                ],
                'digital' => [
                    'file_path' => 'biblioteca/linha-do-tempo-lideranca.pptx',
                    'access_type' => 'view',
                    'access_days' => 12,
                ],
            ],
            [
                'resource' => [
                    'title' => 'Podcast de Aprendizagem',
                    'description' => 'Audio curto com comentarios de estudo, exemplos e ideias para revisao individual.',
                    'type' => 'digital',
                    'status' => 'available',
                    'quantity_available' => 1,
                    'owner_id' => $owner->id,
                ],
                'digital' => [
                    'file_path' => 'biblioteca/podcast-aprendizagem.mp3',
                    'access_type' => 'view',
                    'access_days' => 7,
                ],
            ],
            [
                'resource' => [
                    'title' => 'Analise da Partida',
                    'description' => 'Video com resumo de jogadas, leitura tatica e anotacoes para estudar decisoes em momentos de pressao.',
                    'type' => 'digital',
                    'status' => 'available',
                    'quantity_available' => 1,
                    'owner_id' => $owner->id,
                ],
                'digital' => [
                    'file_path' => 'biblioteca/analise-da-partida.mp4',
                    'access_type' => 'view',
                    'access_days' => 24,
                ],
            ],
            [
                'resource' => [
                    'title' => 'Arquivo de Legado',
                    'description' => 'Conjunto de notas, referencias e documentos comentados para consultas rapidas sobre lideranca.',
                    'type' => 'digital',
                    'status' => 'reserved',
                    'quantity_available' => 0,
                    'owner_id' => $owner->id,
                ],
                'digital' => [
                    'file_path' => 'biblioteca/arquivo-de-legado.zip',
                    'access_type' => 'download',
                    'access_days' => 21,
                ],
            ],
            [
                'resource' => [
                    'title' => 'Livro Introducao a Programacao',
                    'description' => 'Livro fisico para consulta guiada, exercicios introdutorios e fundamentos de logica de programacao.',
                    'type' => 'physical',
                    'status' => 'available',
                    'quantity_available' => 4,
                    'owner_id' => $owner->id,
                ],
                'physical' => [
                    'location' => 'Estante A1',
                    'max_loan_days' => 14,
                    'condition' => 'Bom',
                ],
            ],
            [
                'resource' => [
                    'title' => 'Atividades de Leitura',
                    'description' => 'Colecao impressa de exercicios, perguntas guiadas e dinamicas simples para leitura em grupo.',
                    'type' => 'physical',
                    'status' => 'available',
                    'quantity_available' => 9,
                    'owner_id' => $owner->id,
                ],
                'physical' => [
                    'location' => 'Sala de Apoio',
                    'max_loan_days' => 7,
                    'condition' => 'Excelente',
                ],
            ],
            [
                'resource' => [
                    'title' => 'Checklist de Trilha',
                    'description' => 'Guia impresso com checklist de equipamentos, seguranca e preparacao fisica para consultas rapidas.',
                    'type' => 'physical',
                    'status' => 'active',
                    'quantity_available' => 3,
                    'owner_id' => $owner->id,
                ],
                'physical' => [
                    'location' => 'Arquivo de Campo',
                    'max_loan_days' => 5,
                    'condition' => 'Novo',
                ],
            ],
            [
                'resource' => [
                    'title' => 'Manual de Utilizador',
                    'description' => 'Documento pratico com exemplos de uso, fluxos principais e orientacoes para resolver problemas comuns.',
                    'type' => 'digital',
                    'status' => 'available',
                    'quantity_available' => 1,
                    'owner_id' => $owner->id,
                ],
                'digital' => [
                    'file_path' => 'biblioteca/manual-de-utilizador.docx',
                    'access_type' => 'download',
                    'access_days' => 14,
                ],
            ],
            [
                'resource' => [
                    'title' => 'Planilha de Inventario',
                    'description' => 'Ficheiro de controlo com lista de materiais, quantidades, categorias e observacoes de manutencao.',
                    'type' => 'digital',
                    'status' => 'available',
                    'quantity_available' => 1,
                    'owner_id' => $owner->id,
                ],
                'digital' => [
                    'file_path' => 'biblioteca/planilha-de-inventario.xlsx',
                    'access_type' => 'download',
                    'access_days' => 10,
                ],
            ],
        ];

        foreach ($resources as $resourceData) {
            $resource = Resource::updateOrCreate(
                ['title' => $resourceData['resource']['title']],
                $resourceData['resource']
            );

            if (isset($resourceData['digital'])) {
                $resource->physicalResource()->delete();
                DigitalResource::updateOrCreate(
                    ['resource_id' => $resource->id],
                    $resourceData['digital'] + ['resource_id' => $resource->id]
                );
            }

            if (isset($resourceData['physical'])) {
                $resource->digitalResource()->delete();
                PhysicalResource::updateOrCreate(
                    ['resource_id' => $resource->id],
                    $resourceData['physical'] + ['resource_id' => $resource->id]
                );
            }
        }
    }
}
