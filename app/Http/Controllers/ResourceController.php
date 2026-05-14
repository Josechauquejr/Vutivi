<?php

namespace App\Http\Controllers;

use App\Models\Resource;

/**
 * Expoe o catalogo compartilhado de recursos sem misturar a navegacao com o CRUD dos subtipos.
 */
class ResourceController extends Controller
{
    /**
     * Lista os recursos disponiveis no catalogo compartilhado.
     */
    public function index()
    {
        return view('index');
    }

    /**
     * Exibe a página biblioteca com recursos fictícios.
     */
    public function library()
    {
        $resources = Resource::with(['owner', 'physicalResource', 'digitalResource'])
            ->latest()
            ->paginate(10);

        return view('library', compact('resources'));
    }

    /**
     * Exibe a página Meus recursos com recursos do usuário.
     */
    public function mine()
    {
        return view('home', [
            'pageTitle' => 'Meus Recursos',
            'resources' => $this->mineResources(),
        ]);
    }

    /**
     * Exibe a página Favoritos com recursos escolhidos.
     */
    public function favorites()
    {
        return view('home', [
            'pageTitle' => 'Favoritos',
            'resources' => $this->favoriteResources(),
        ]);
    }

    /**
     * Exibe a página Categorias com recursos variados.
     */
    public function categories()
    {
        return view('home', [
            'pageTitle' => 'Categorias',
            'resources' => $this->categoryResources(),
        ]);
    }

    /**
     * Exibe os detalhes completos de um recurso do catalogo.
     */
    public function destroy(int $id)
    {
        $resource = Resource::findOrFail($id);
        $resource->delete();

        return redirect()->route('resources.index')->with('success', 'Recurso excluido com sucesso.');
    }

    private function libraryResources(): array
    {
        return [
            $this->makeResource([
                'title' => 'Relatório Anual 2024',
                'description' => 'Documento completo com os resultados financeiros e metas do ano.',
                'dataType' => 'pdf',
                'iconClass' => 'pdf',
                'ext' => 'PDF',
                'status' => 'Disponível',
                'statusClass' => 'disponivel',
                'metaText1' => 'Dono: Equipa Financeira',
                'metaText2' => '4.2 MB',
            ]),
            $this->makeResource([
                'title' => 'Manual de Utilizador',
                'description' => 'Guia prático com exemplos para usar a plataforma.',
                'dataType' => 'doc',
                'iconClass' => 'doc',
                'ext' => 'DOCX',
                'status' => 'Disponível',
                'statusClass' => 'disponivel',
                'metaText1' => 'Dono: Equipa de Produto',
                'metaText2' => '1.8 MB',
            ]),
            $this->makeResource([
                'title' => 'Apresentação Q3',
                'description' => 'Slides com resultados e planos estratégicos para o próximo trimestre.',
                'dataType' => 'pptx',
                'iconClass' => 'pptx',
                'ext' => 'PPTX',
                'status' => 'Emprestado',
                'statusClass' => 'emprestado',
                'metaText1' => 'Dono: Marketing',
                'metaText2' => '9.7 MB',
            ]),
        ];
    }

    private function mineResources(): array
    {
        return [
            $this->makeResource([
                'title' => 'Projeto Celular',
                'description' => 'Protótipo de design para o próximo app de RH.',
                'dataType' => 'pdf',
                'iconClass' => 'pdf',
                'ext' => 'PDF',
                'status' => 'Disponível',
                'statusClass' => 'disponivel',
                'metaText1' => 'Dono: Você',
                'metaText2' => '78 páginas',
                'dataOwner' => 'my',
            ]),
            $this->makeResource([
                'title' => 'Checklist de Onboarding',
                'description' => 'Lista de verificação para novos colaboradores e procedimentos internos.',
                'dataType' => 'xlsx',
                'iconClass' => 'xlsx',
                'ext' => 'XLSX',
                'status' => 'Disponível',
                'statusClass' => 'disponivel',
                'metaText1' => 'Dono: Você',
                'metaText2' => '2.1 MB',
                'dataOwner' => 'my',
            ]),
        ];
    }

    private function favoriteResources(): array
    {
        return [
            $this->makeResource([
                'title' => 'Guia de Apresentações',
                'description' => 'Conjunto de templates e boas práticas para apresentações internas.',
                'dataType' => 'pptx',
                'iconClass' => 'pptx',
                'ext' => 'PPTX',
                'status' => 'Disponível',
                'statusClass' => 'disponivel',
                'metaText1' => 'Dono: Design',
                'metaText2' => '24 slides',
            ]),
            $this->makeResource([
                'title' => 'Tutorial de Formação',
                'description' => 'Vídeo de formação sobre metodologias ágeis e comunicação em equipe.',
                'dataType' => 'video',
                'iconClass' => 'video',
                'ext' => 'MP4',
                'status' => 'Em Uso',
                'statusClass' => 'em-uso',
                'metaText1' => 'Dono: RH',
                'metaText2' => '2h 15min',
            ]),
        ];
    }

    private function categoryResources(): array
    {
        return [
            $this->makeResource([
                'title' => 'Template Contrato',
                'description' => 'Modelo de contrato de prestação de serviços.',
                'dataType' => 'doc',
                'iconClass' => 'doc',
                'ext' => 'DOCX',
                'status' => 'Disponível',
                'statusClass' => 'disponivel',
                'metaText1' => 'Dono: Jurídico',
                'metaText2' => '1.2 MB',
            ]),
            $this->makeResource([
                'title' => 'Relatório Analítico',
                'description' => 'Relatório com métricas de uso e engajamento do sistema.',
                'dataType' => 'pdf',
                'iconClass' => 'pdf',
                'ext' => 'PDF',
                'status' => 'Disponível',
                'statusClass' => 'disponivel',
                'metaText1' => 'Dono: Análise de Dados',
                'metaText2' => '3.8 MB',
            ]),
            $this->makeResource([
                'title' => 'Pacote de Instalação',
                'description' => 'Arquivo ZIP com materiais do projeto e documentação técnica.',
                'dataType' => 'zip',
                'iconClass' => 'zip',
                'ext' => 'ZIP',
                'status' => 'Emprestado',
                'statusClass' => 'emprestado',
                'metaText1' => 'Dono: Infra',
                'metaText2' => '256 MB',
            ]),
        ];
    }

    private function makeResource(array $data): array
    {
        static $nextId = 101;

        return array_merge([
            'id' => $nextId++,
            'title' => 'Recurso Genérico',
            'description' => 'Descrição do recurso.',
            'dataType' => 'doc',
            'iconClass' => 'doc',
            'ext' => 'DOCX',
            'status' => 'Disponível',
            'statusClass' => 'disponivel',
            'metaText1' => 'Dono: Equipa',
            'metaText2' => '1.0 MB',
            'dataOwner' => 'other',
        ], $data);
    }
}
