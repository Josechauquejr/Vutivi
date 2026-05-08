<?php

namespace Database\Seeders;

use App\Models\Resource;
use App\Models\User;
use App\Models\PhysicalResource;
use App\Models\DigitalResource;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ResourceSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Popula a base de dados com recursos fictícios.
     */
    public function run(): void
    {
        // Criar alguns usuários adicionais se não existirem
        $users = User::all();
        if ($users->isEmpty()) {
            $users = collect([
                User::factory()->create(['name' => 'João Silva', 'username' => 'joao.silva']),
                User::factory()->create(['name' => 'Maria Santos', 'username' => 'maria.santos']),
                User::factory()->create(['name' => 'Pedro Oliveira', 'username' => 'pedro.oliveira']),
            ]);
        }

        // Recursos digitais
        $digitalResources = [
            [
                'title' => 'Relatório Anual 2024',
                'description' => 'Documento completo com os resultados financeiros, estratégias e metas atingidas ao longo de 2024.',
                'type' => 'digital',
                'status' => 'available',
                'quantity_available' => 1,
                'owner_id' => $users->random()->id,
                'digital' => [
                    'file_path' => 'reports/annual_2024.pdf',
                    'access_type' => 'download',
                    'access_days' => 7,
                ]
            ],
            [
                'title' => 'Manual de Utilizador',
                'description' => 'Guia completo de utilização do sistema, com exemplos práticos e resolução de problemas comuns.',
                'type' => 'digital',
                'status' => 'available',
                'quantity_available' => 1,
                'owner_id' => $users->random()->id,
                'digital' => [
                    'file_path' => 'manuals/user_guide.docx',
                    'access_type' => 'download',
                    'access_days' => 14,
                ]
            ],
            [
                'title' => 'Apresentação Q3',
                'description' => 'Slides com os resultados trimestrais de vendas, análise de mercado e projecções para o Q4 2024.',
                'type' => 'digital',
                'status' => 'reserved',
                'quantity_available' => 0,
                'owner_id' => $users->random()->id,
                'digital' => [
                    'file_path' => 'presentations/q3_results.pptx',
                    'access_type' => 'view',
                    'access_days' => 5,
                ]
            ],
            [
                'title' => 'Tutorial de Formação',
                'description' => 'Vídeo de formação sobre as melhores práticas de gestão de projectos e metodologias ágeis.',
                'type' => 'digital',
                'status' => 'active',
                'quantity_available' => 0,
                'owner_id' => $users->random()->id,
                'digital' => [
                    'file_path' => 'videos/training_tutorial.mp4',
                    'access_type' => 'view',
                    'access_days' => 3,
                ]
            ],
            [
                'title' => 'Base de Dados Clientes',
                'description' => 'Planilha com todos os dados dos clientes activos, histórico de compras e contactos actualizados.',
                'type' => 'digital',
                'status' => 'available',
                'quantity_available' => 1,
                'owner_id' => $users->random()->id,
                'digital' => [
                    'file_path' => 'data/client_database.xlsx',
                    'access_type' => 'download',
                    'access_days' => 10,
                ]
            ],
            [
                'title' => 'Pacote de Instalação v3.0',
                'description' => 'Arquivo comprimido com todos os ficheiros necessários para instalação do sistema na versão 3.0.',
                'type' => 'digital',
                'status' => 'reserved',
                'quantity_available' => 0,
                'owner_id' => $users->random()->id,
                'digital' => [
                    'file_path' => 'software/installer_v3.0.zip',
                    'access_type' => 'download',
                    'access_days' => 30,
                ]
            ],
        ];

        // Recursos físicos
        $physicalResources = [
            [
                'title' => 'Projetor Multimédia',
                'description' => 'Projetor Full HD com 4000 lumens, ideal para apresentações e treinamentos.',
                'type' => 'physical',
                'status' => 'available',
                'quantity_available' => 2,
                'owner_id' => $users->random()->id,
                'physical' => [
                    'location' => 'Sala de Reuniões A',
                    'max_loan_days' => 7,
                    'condition' => 'Excelente',
                ]
            ],
            [
                'title' => 'Laptop Dell XPS 13',
                'description' => 'Laptop ultrabook com processador Intel i7, 16GB RAM e SSD 512GB.',
                'type' => 'physical',
                'status' => 'available',
                'quantity_available' => 1,
                'owner_id' => $users->random()->id,
                'physical' => [
                    'location' => 'Escritório 204',
                    'max_loan_days' => 14,
                    'condition' => 'Bom',
                ]
            ],
            [
                'title' => 'Câmera DSLR Canon',
                'description' => 'Câmera profissional com lente 18-55mm, ideal para fotografias e vídeos.',
                'type' => 'physical',
                'status' => 'reserved',
                'quantity_available' => 0,
                'owner_id' => $users->random()->id,
                'physical' => [
                    'location' => 'Armário de Equipamentos',
                    'max_loan_days' => 5,
                    'condition' => 'Excelente',
                ]
            ],
            [
                'title' => 'Microfone sem fio',
                'description' => 'Microfone profissional sem fio com alcance de 100 metros.',
                'type' => 'physical',
                'status' => 'available',
                'quantity_available' => 3,
                'owner_id' => $users->random()->id,
                'physical' => [
                    'location' => 'Sala de Equipamentos',
                    'max_loan_days' => 3,
                    'condition' => 'Bom',
                ]
            ],
        ];

        // Criar recursos digitais
        foreach ($digitalResources as $resourceData) {
            $digitalData = $resourceData['digital'];
            unset($resourceData['digital']);

            $resource = Resource::create($resourceData);
            DigitalResource::create(array_merge($digitalData, ['resource_id' => $resource->id]));
        }

        // Criar recursos físicos
        foreach ($physicalResources as $resourceData) {
            $physicalData = $resourceData['physical'];
            unset($resourceData['physical']);

            $resource = Resource::create($resourceData);
            PhysicalResource::create(array_merge($physicalData, ['resource_id' => $resource->id]));
        }
    }
}