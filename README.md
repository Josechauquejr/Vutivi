# Documentação Técnica Completa da Plataforma VUTIVI

![Logo](./public/img/png/logo_bb.png)

## 1. Visão Geral do Sistema

### 1.1 Objetivo da Plataforma

A VUTIVI é uma plataforma de biblioteca digital e física concebida para organizar, disponibilizar, gerir e partilhar conhecimento em ambientes académicos, institucionais e colaborativos. O nome vem do xitsonga, em que “Vutivi” significa “conhecimento”, e esse conceito orienta tanto a experiência visual como a arquitetura funcional do sistema.

O objetivo central é transformar a gestão de recursos numa experiência clara, pesquisável e participativa. Em vez de tratar livros, ficheiros, documentos, vídeos e materiais de estudo como registos isolados, a plataforma organiza esses recursos como parte de um ecossistema de aprendizagem. Utilizadores podem descobrir materiais, publicar recursos, guardar favoritos, acompanhar empréstimos, receber alertas e participar no ciclo de circulação do conhecimento.

### 1.2 Conceito da Biblioteca

A biblioteca da VUTIVI é híbrida. Ela suporta recursos físicos, como livros, equipamentos e apostilas, e recursos digitais, como PDFs, documentos, apresentações, vídeos, áudios e arquivos. Essa separação é importante porque cada tipo possui regras próprias:

- Recursos físicos exigem localização, condição, disponibilidade, termos de empréstimo, prazo de devolução e eventual aprovação.
- Recursos digitais exigem ficheiro, modo de acesso, permissões de download ou visualização e janela de acesso.

Apesar dessas diferenças, ambos partilham uma raiz comum: título, descrição, dono, estado, disponibilidade, favoritos, slug amigável, capa e presença no catálogo.

### 1.3 Problema Resolvido

Bibliotecas pequenas e médias frequentemente enfrentam problemas de dispersão de informação, controlo manual de empréstimos, ausência de rastreabilidade, dificuldade de pesquisa, pouca visibilidade de prazos e falta de experiência moderna para utilizadores. A VUTIVI resolve esses problemas ao centralizar:

- Cadastro e gestão de recursos.
- Pesquisa e filtragem.
- Acompanhamento de empréstimos.
- Alertas de devolução.
- Favoritos.
- Uploads de capas e ficheiros.
- Partilha de links amigáveis.
- Interface responsiva.
- Feedback visual em tempo real.

### 1.4 Público Alvo

A plataforma foi desenhada para instituições de ensino, centros de formação, bibliotecas comunitárias, equipas académicas, departamentos internos e grupos que precisam gerir conhecimento partilhado. Os utilizadores principais são:

- Leitores e estudantes que procuram recursos.
- Donos de recursos que publicam e gerem materiais.
- Responsáveis por empréstimos.
- Administradores ou gestores de biblioteca.
- Comunidades académicas que partilham materiais de estudo.

### 1.5 Funcionalidades Principais

As funcionalidades principais incluem autenticação, cadastro de utilizadores, upload de foto de perfil, cadastro de recursos físicos e digitais, upload de capa, upload de ficheiro digital, pesquisa, filtros, favoritos, contadores, empréstimos, termos e condições, extensões de prazo, notificações, breadcrumbs, slugs amigáveis, toasts, dark mode e responsividade.

### 1.6 Fluxo Geral da Aplicação

O fluxo típico começa na homepage, onde o utilizador entende o propósito da VUTIVI e acessa a biblioteca. Na biblioteca, ele pesquisa recursos por texto, tipo, formato, estado e popularidade. Ao abrir um recurso, visualiza detalhes, disponibilidade, dono, formato, capa e ações. Se for recurso físico, pode aceitar termos e solicitar empréstimo. Se for digital, pode visualizar ou baixar conforme permissão. O sistema fornece feedback através de toasts, badges, contadores e estados visuais.

## 2. Arquitetura Geral

### 2.1 Estrutura Frontend e Backend

A VUTIVI utiliza Laravel como backend e Blade como camada de renderização do frontend. O frontend é estilizado com Tailwind CSS, compilado via Vite, e possui JavaScript progressivo para microinterações, previews, toasts, favoritos sem refresh, contadores animados e comportamento de dropdowns.

O backend é responsável por autenticação, validação, persistência, relacionamentos, rotas, regras de empréstimo, uploads, slugs, paginação e respostas JSON para interações dinâmicas. O frontend consome essas respostas quando uma ação pode ser atualizada sem reload.

### 2.2 Separação de Responsabilidades

O sistema separa responsabilidades em camadas:

- Models representam entidades persistentes e relacionamentos.
- Controllers coordenam entradas HTTP e respostas.
- Requests validam e normalizam dados.
- Actions encapsulam operações de criação, atualização e regras específicas.
- Blade views renderizam páginas e componentes.
- Components reutilizam navbar, sidebar, breadcrumbs, footer, cards e toasts.
- Migrations definem estrutura do banco.
- Seeders alimentam dados iniciais.
- JavaScript adiciona comportamento progressivo.

### 2.3 Organização Modular

Os recursos são divididos em recurso base, recurso físico e recurso digital. Essa modelagem evita duplicação e mantém regras comuns no modelo `Resource`, enquanto dados específicos ficam em `PhysicalResource` e `DigitalResource`.

Reservas e termos são módulos próprios. Favoritos usam tabela pivot entre utilizadores e recursos. Uploads são armazenados via filesystem público do Laravel.

### 2.4 Fluxo de Dados

O fluxo de dados segue o padrão request-response:

1. O utilizador interage com formulário, link ou botão.
2. A rota direciona para um controller.
3. O request valida os dados.
4. O controller chama action ou model.
5. O banco é atualizado.
6. A resposta retorna uma view, redirect ou JSON.
7. O frontend atualiza a UI, exibindo toast, contador, badge ou novo estado visual.

Para favoritos, o fluxo é otimista: a interface muda imediatamente, a chamada `fetch` confirma no backend e, se falhar, o estado anterior é restaurado.

### 2.5 Estrutura de Navegação

A navegação é composta por navbar fixa, sidebar autenticada, breadcrumbs em páginas internas e footer institucional. A navbar concentra pesquisa, notificações, perfil e acessos rápidos. A sidebar organiza biblioteca, recursos, empréstimos e conta. Breadcrumbs fornecem contexto e reduzem desorientação em páginas profundas.

### 2.6 Arquitetura Escalável

A arquitetura suporta expansão futura para APIs REST, políticas de autorização, notificações persistentes, broadcasting, filas, auditoria e dashboard administrativo. A escolha de models separados para tipos de recurso permite adicionar novos tipos sem reescrever o catálogo.

## 3. Backend Laravel

### 3.1 Estrutura Laravel

O backend segue a estrutura padrão Laravel:

- `app/Models`: entidades do domínio.
- `app/Http/Controllers`: controle HTTP.
- `app/Http/Requests`: validações.
- `app/Actions`: casos de uso e regras específicas.
- `database/migrations`: schema.
- `database/seeders`: dados iniciais.
- `resources/views`: Blade.
- `routes/web.php`: rotas web.
- `resources/js` e `resources/css`: assets.

### 3.2 Controllers

`ResourceController` centraliza catálogo, biblioteca, favoritos, recursos pessoais, recursos emprestados, alertas de prazo, conta, slugs públicos e página sobre. Ele também expõe resposta JSON para favoritos quando a requisição espera JSON.

`PhysicalResourceController` e `DigitalResourceController` gerem CRUD especializado para cada tipo de recurso. Eles continuam compatíveis com rotas antigas por ID, mas a interface pública usa slugs amigáveis.

`ReservationController` gere empréstimos, devoluções, pedidos de extensão, aprovação e recusa de extensão. Ele coordena validação de disponibilidade e sincronização da quantidade disponível.

`ReservationTermsController` controla aceite de termos antes da solicitação de empréstimo físico.

`UserController` gere cadastro, edição de perfil, upload de foto e exclusão de conta.

### 3.3 Models

`Resource` é a raiz do agregado de recursos. Possui título, slug, descrição, capa, tipo, estado, quantidade e dono. Relaciona-se com `PhysicalResource`, `DigitalResource`, `Reservation`, `TermAndCondition` e utilizadores que favoritaram.

`User` representa utilizadores autenticados. Possui nome, username, slug, email, foto de perfil e senha. Relaciona-se com recursos próprios, reservas e favoritos.

`Reservation` representa o ciclo de empréstimo. Inclui datas, estado, aprovação, devolução, extensão, atraso e relações com recurso e utilizador.

### 3.4 Migrations

As migrations criam users, resources, physical_resources, digital_resources, reservations, terms, acceptances, favoritos e campos adicionais como slug, cover_image e profile_photo. A modelagem usa foreign keys para garantir integridade referencial.

### 3.5 Requests e Validações

Requests normalizam campos textuais, validam obrigatoriedade, formatos, uploads e regras de senha. Recursos digitais validam ficheiros permitidos e imagens de capa. Recursos físicos validam localização, condição e prazo. Utilizadores validam nome, username, email, senha e foto de perfil.

### 3.6 Actions

Actions encapsulam operações como criar/atualizar recursos físicos e digitais, criar reservas, atualizar reservas, devolver reservas e sincronizar disponibilidade. Isso reduz complexidade nos controllers e facilita testes.

### 3.7 Autenticação e Guards

A autenticação usa o sistema padrão do Laravel, com sessões e `Auth::attempt`. Rotas sensíveis ficam protegidas por middleware `auth`. Rotas de login e cadastro ficam sob middleware `guest`.

### 3.8 Relacionamentos

Os relacionamentos principais são:

- User hasMany Resource.
- User hasMany Reservation.
- User belongsToMany Resource via favoritos.
- Resource belongsTo User.
- Resource hasOne PhysicalResource.
- Resource hasOne DigitalResource.
- Resource hasMany Reservation.
- Reservation belongsTo Resource.
- Reservation belongsTo User.

### 3.9 Uploads e Storage

Uploads usam o disco público do Laravel. Capas são guardadas em `resource-covers`, fotos de perfil em `profile-photos` e ficheiros digitais em `digital-resources`. O frontend oferece preview em tempo real, enquanto o backend valida tipo e tamanho.

### 3.10 Permissões e Autorizações

Edição e remoção de recursos exigem que o utilizador autenticado seja dono do recurso. Edição de perfil exige que o utilizador seja o próprio dono da conta. Extensões só podem ser solicitadas pelo utilizador do empréstimo e aprovadas ou negadas pelo dono do recurso.

### 3.11 Rotas

O sistema mantém rotas CRUD tradicionais e adiciona rotas amigáveis:

- `/library`
- `/sobre`
- `/recurso/{slug}`
- `/resources/create`
- `/reservations/{id}/request-extension`
- `/reservations/{id}/approve-extension`
- `/reservations/{id}/deny-extension`

### 3.12 Favoritos

Favoritos usam tabela pivot. Ao alternar favorito, o backend retorna JSON com estado atual, contador e mensagem. O frontend atualiza o ícone e contador sem recarregar a página.

### 3.13 Empréstimos e Extensões

Empréstimos possuem estados como pendente, aprovado, em uso, extensão pendente, estendido, devolvido e cancelado. O sistema calcula dias restantes e atraso visualmente. Extensões podem ser solicitadas e depois aprovadas ou negadas pelo dono do recurso.

### 3.14 Slugs

Slugs tornam URLs legíveis e melhoram SEO. Eles são gerados automaticamente a partir do título do recurso ou username/nome do utilizador. A geração evita colisões adicionando sufixos numéricos.

## 4. Frontend

### 4.1 Arquitetura Visual

O frontend usa Blade, Tailwind CSS e componentes reutilizáveis. A identidade visual combina Inter para interface e Literata para títulos, reforçando leitura, organização e elegância institucional.

### 4.2 Componentes

Os principais componentes são navbar, sidebar, footer, breadcrumbs, flash toasts, resource card e resource detail. Esses componentes reduzem repetição e centralizam padrões visuais.

### 4.3 Navbar

A navbar contém pesquisa, notificações, acessos rápidos e perfil. O nome do utilizador é curto e acompanhado de avatar à direita. Notificações mostram contador, painel agrupado e estado visual de não lidas.

### 4.4 Sidebar

A sidebar organiza subtabs com ícones semânticos, hover states, estados ativos e animações suaves de expansão. Ela funciona como navegação principal em desktop.

### 4.5 Formulários

Formulários usam inputs sólidos, labels claras, helper texts, ícones, foco visível e grids responsivos. Uploads possuem drag and drop e preview.

### 4.6 Gestão de Estado no Frontend

O estado é progressivo e leve. Favoritos e downloads usam JavaScript para atualização instantânea. Toasts são criados dinamicamente. Contadores são atualizados no DOM. O backend continua sendo fonte de verdade.

### 4.7 UX e UI

A interface prioriza leitura, hierarquia, contexto e feedback. Cards usam badges, métricas e ações compactas. Empréstimos usam status, datas, indicadores e alertas suaves.

### 4.8 Animações

As animações são discretas: fade, slide, scale, pulse leve, reveal on scroll e transições de hover. Há respeito por `prefers-reduced-motion`.

### 4.9 Responsividade

Layouts usam grids adaptativos, menus colapsáveis, inputs largos em mobile e reorganização de ações prioritárias. A experiência mobile evita overflow e mantém botões tocáveis.

### 4.10 Pesquisa e Paginação

A pesquisa suporta sugestões visuais, tags, categorias e painel com scroll. A paginação mostra página atual, total, anterior e próxima.

### 4.11 Dark Mode

O dark mode usa superfícies sólidas, contraste confortável e reduz blur excessivo em inputs. O objetivo é leitura prolongada sem fadiga visual.

### 4.12 Acessibilidade

O sistema usa labels, `sr-only`, foco visível, contrastes adequados, botões com tamanho mínimo e textos descritivos. Ícones são acompanhados de texto quando a ação não é universal.

## 5. Base de Dados

### 5.1 Modelagem

A modelagem separa entidades comuns e específicas. `resources` concentra dados universais. `physical_resources` e `digital_resources` guardam detalhes próprios. `reservations` registra o ciclo de empréstimo. `resource_user_favorites` modela relação muitos-para-muitos.

### 5.2 Relacionamentos e Integridade

Foreign keys garantem que recursos pertençam a utilizadores, reservas pertençam a recursos e utilizadores, e favoritos não existam sem ambos. Deletes em cascata removem dados dependentes quando apropriado.

### 5.3 Índices

Slugs são únicos. Favoritos possuem unique composto para impedir duplicação. Termos usam índices por recurso, escopo e estado.

### 5.4 Performance

Consultas usam eager loading para evitar N+1 em listas. Contadores usam `withCount`. Paginação limita volume de dados por página.

## 6. Experiência do Utilizador

### 6.1 Decisões de UX

A UX busca reduzir esforço cognitivo. Ações primárias ficam visíveis, ações secundárias vão para menus contextuais. Estados importantes, como atraso e prazo próximo, ganham destaque visual.

### 6.2 Design System

O sistema usa radius moderado, sombras suaves, tons institucionais, laranja como cor de ação e variações neutras para leitura. Cards são usados para entidades, não para decorar seções inteiras.

### 6.3 Microinterações

Favoritos pulsam, contadores mudam imediatamente, toasts confirmam ações, upload mostra preview e botões respondem a hover/active.

### 6.4 Experiência Mobile e Desktop

No desktop, a sidebar acelera navegação. No mobile, a navbar reorganiza ações e evita quebra visual. Formulários ficam em uma coluna quando necessário.

## 7. Sistema de Empréstimos

O fluxo começa com a visualização de um recurso físico. O utilizador aceita termos e solicita empréstimo. Dependendo da configuração, o pedido pode ser aprovado automaticamente ou ficar pendente. O sistema registra início, prazo, estado, aceite de termos e aprovação.

Durante o empréstimo, o utilizador vê dias restantes, data de entrega, data de devolução, estado e histórico. Se precisar de mais tempo, solicita extensão. O dono do recurso pode aceitar ou negar. Ao devolver, o sistema marca `returned_at` e sincroniza disponibilidade.

Estados visuais indicam pendente, aprovado, atrasado, devolvido e extensão pendente. Empréstimos próximos do vencimento são destacados com tons de alerta suaves.

## 8. Sistema de Recursos

Recursos físicos possuem localização, condição e prazo. Recursos digitais possuem ficheiro, tipo de acesso e dias de acesso. Ambos possuem capa, descrição, estado, dono, favoritos, métricas, slug e partilha por link.

Filtros permitem navegar por tipo, formato, estado, quantidade por página e popularidade. Favoritos funcionam sem refresh e downloads exibem feedback imediato.

## 9. Segurança

O sistema protege rotas autenticadas, valida entradas, restringe edição por dono, valida uploads, usa CSRF em formulários e fetch, usa hash automático de senha e evita exposição desnecessária de identificadores técnicos na navegação pública por meio de slugs.

Uploads são limitados por tipo e tamanho. A autorização de extensão impede que outro utilizador aceite ou negue pedidos de recursos que não possui.

## 10. Responsividade

A responsividade usa breakpoints Tailwind, grids fluidos, largura máxima por contexto e ações reorganizadas. Cards, formulários, breadcrumbs e menus foram desenhados para manter legibilidade em telas pequenas.

## 11. Animações e Interações

O motion design é silencioso e contextual. Toasts entram com slide/fade/scale. Cards elevam levemente. Favoritos usam scale. Skeletons comunicam carregamento. Reveals conectam seções da homepage. O objetivo é transmitir modernidade sem distrair da leitura.

## 12. Conclusão

A VUTIVI combina biblioteca física e digital, gestão de empréstimos, experiência visual moderna e arquitetura Laravel extensível. A plataforma já possui base para evoluir para notificações persistentes, APIs, broadcasting em tempo real, auditoria, dashboard administrativo, analytics e recomendações inteligentes. Seu diferencial está em tratar conhecimento como fluxo vivo entre pessoas, recursos e aprendizagem.
