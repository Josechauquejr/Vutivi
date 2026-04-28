## Visão geral

O Vutivi é uma plataforma web de biblioteca universitária digital, desenvolvida para democratizar o acesso ao conhecimento académico. Permite que alunos, docentes e membros da comunidade universitária carreguem, partilhem e descubram obras em formato digital — livros, artigos, dissertações, aulas em vídeo e conteúdo de áudio — num único espaço centralizado, gratuito e acessível.

## O problema que resolve

Em muitas universidades africanas, o acesso a materiais académicos é limitado por custos, distribuição física e falta de plataformas digitais adaptadas ao contexto local. O Vutivi elimina estas barreiras: qualquer estudante com acesso à internet pode encontrar, ler e descarregar obras relevantes para o seu curso, sem depender de livrarias físicas ou de cópias partilhadas de forma informal.

---

## Utilizadores do Sistema

### Al - Aluno

Pesquisa e descarrega obras, guarda na sua biblioteca pessoal, dá gostos, recebe recomendações com base no seu curso e historial de leitura. Pode também carregar os seus próprios trabalhos e partilhá-los com a comunidade.

### Do - Docente

Publica materiais pedagógicos — apontamentos, artigos, bibliografias recomendadas, vídeos de aula. Tem visibilidade de autor com perfil próprio e estatísticas de consulta das suas obras.

### Vi - Visitante

Pode navegar no catálogo e consultar metadados (título, autor, sinopse, resumo). Para descarregar ou guardar obras necessita de criar conta.

### Ad - Administrador

Gere utilizadores, aprova ou rejeita obras, consulta estatísticas da plataforma e assegura a qualidade do conteúdo publicado.

---

## Funcionalidades principais

### Catálogo e Pesquisa

Pesquisa full-text com filtros por curso, tipo de obra, autor, intervalo de anos e letra inicial. Resultados ordenados por relevância, gostos ou data.

### Carregamento de Obras

Upload de ficheiros PDF, MP4 e MP3. O utilizador preenche metadados: título, sinopse, resumo, autores, categoria e tipo de obra.

### Minha biblioteca

Cada utilizador tem uma colecção pessoal onde guarda as obras que quer ler mais tarde, com etiquetas personalizáveis.

### Gostos e Popularidade

Sistema de gostos por obra. A contagem é pública e serve como indicador de qualidade e relevância para outros utilizadores.

### Pagina da obra

Cada obra tem uma página dedicada com sinopse, resumo, perfil do autor, tipo, categoria, ano, contagem de gostos e de transferências.

### Recomendações

Sugestões automáticas baseadas no curso do utilizador e nas obras mais populares na sua área académica.

### Visualização Inline

PDFs podem ser lidos directamente no navegador sem descarregar. Vídeos e áudios têm player integrado na página da obra.

### Perfil de autor

Página dedicada a cada autor com biografia, lista de obras publicadas na plataforma e estatísticas de consulta.

---

## **Tipos de conteúdo suportados**

**PDF - Livros PDF - Artigos PDF - Dissertações MP4 — Aulas em vídeo MP4 -Documentários  MP3 - Podcasts académicos   MP3 - Audiolivros**

---

## Plano de Desenvolvimento

---

## **Tecnologia**

| **Tecnologia** | **Função**                |
| -------------- | ------------------------- |
| **Laravel 11** | Framework principal (PHP) |
| **Vue 3**      | Interface com Inertia.js  |
| **PostGres**   | Base de dados relacional  |
| **Tailwind**   | Estilização CSS           |

**Ambiente de desenvolvimento**

O Vutivi é desenvolvido localmente com Laragon (Windows) ou Laravel Herd (Mac), sem custos de servidor nem de serviços externos. Toda a stack é open-source e gratuita — incluindo o armazenamento de ficheiros (disco local), a base de dados (MySQL incluído no Laragon) e o motor de pesquisa (Laravel Scout com driver de base de dados).

---

## Escalabilidade Futura

Quando o Vutivi crescer além do ambiente local, a arquitectura está preparada para adicionar: análise automática de vírus nos ficheiros carregados (ClamAV), moderação de conteúdo explícito (modelos de IA open-source), gestão de direitos de autor e licenças, notificações em tempo real, armazenamento em nuvem (MinIO self-hosted ou S3) e motor de pesquisa avançado (Meilisearch). Tudo isto sem alterar a estrutura de base do projecto — apenas novos módulos adicionados por cima.
