# Relatório do Projecto VUTIVI

## Capa

**Projecto:** VUTIVI  
**Tema:** Plataforma de gestão de biblioteca física e digital  
**Tecnologia principal:** Laravel  
**Idioma:** Português de Moçambique  
**Natureza do documento:** Relatório técnico e expositivo do sistema desenvolvido

---

## 1. Introdução

O presente relatório descreve o projecto VUTIVI, uma aplicação web desenvolvida para apoiar a gestão de bibliotecas físicas e digitais. A plataforma foi concebida para organizar recursos, controlar empréstimos, gerir acessos digitais, acompanhar prazos e melhorar a experiência de utilizadores que procuram, partilham e utilizam materiais de estudo.

O nome VUTIVI remete ao conceito de conhecimento. A aplicação procura transformar a biblioteca num ambiente mais acessível, rastreável e colaborativo, onde cada recurso possui informação clara, estado actualizado e ligação directa ao seu dono e aos seus utilizadores.

---

## 2. Contextualização do Problema

Em muitas instituições de ensino, bibliotecas comunitárias e centros de formação, a gestão de livros, documentos, apostilas, vídeos e ficheiros digitais ainda depende de processos manuais ou dispersos. Essa realidade cria dificuldades na localização de recursos, no controlo de empréstimos, na identificação de materiais indisponíveis e no acompanhamento de devoluções.

Entre os principais problemas identificados destacam-se:

- Falta de um catálogo digital centralizado.
- Dificuldade em controlar prazos de empréstimo.
- Ausência de notificações para devoluções e extensões.
- Pouca rastreabilidade sobre quem possui determinado recurso.
- Falta de distinção clara entre recursos físicos e digitais.
- Processo manual para gerir pedidos, aprovações e devoluções.

A VUTIVI surge como resposta a esse cenário, oferecendo uma solução integrada para organização, pesquisa, empréstimo e acompanhamento de recursos.

---

## 3. Objectivos do Projecto

### 3.1 Objectivo Geral

Desenvolver uma plataforma web para gestão de biblioteca física e digital, permitindo o registo, pesquisa, empréstimo, devolução, notificação e acompanhamento de recursos de forma organizada e segura.

### 3.2 Objectivos Específicos

- Permitir o cadastro e autenticação de utilizadores.
- Permitir o registo de recursos físicos e digitais.
- Disponibilizar uma biblioteca com pesquisa, filtros e paginação.
- Controlar pedidos de empréstimo, devoluções e histórico.
- Permitir pedidos de extensão de prazo.
- Notificar donos de recursos e utilizadores sobre decisões relevantes.
- Garantir sincronização entre estado do empréstimo e disponibilidade do recurso.
- Validar dados de entrada e proteger rotas sensíveis.
- Criar uma interface responsiva, moderna e simples de utilizar.

---

## 4. Metodologia de Desenvolvimento

O desenvolvimento seguiu uma abordagem incremental, com separação clara entre regras de negócio, interface, persistência e validação. A aplicação foi estruturada com base no padrão MVC do Laravel, complementado por classes de acção para isolar operações específicas.

As principais práticas aplicadas foram:

- Modelação de entidades com migrations e relacionamentos Eloquent.
- Criação de controllers para coordenar fluxos HTTP.
- Utilização de Form Requests para validação.
- Separação de regras críticas em actions.
- Implementação de views Blade com componentes reutilizáveis.
- Criação de testes automatizados para proteger comportamentos essenciais.
- Uso de feedback visual por toasts, badges, estados e notificações.

---

## 5. Descrição da Aplicação VUTIVI

A VUTIVI é uma plataforma híbrida de biblioteca. Ela permite gerir tanto recursos físicos, como livros e materiais impressos, quanto recursos digitais, como PDFs, vídeos, áudios, documentos e apresentações.

O sistema organiza cada recurso com dados comuns, como título, descrição, dono, estado, capa e tipo. Depois, adiciona informações específicas conforme o recurso seja físico ou digital.

Recursos físicos possuem localização, condição, quantidade disponível, prazo máximo de empréstimo, possibilidade de aprovação e extensão. Recursos digitais possuem ficheiro, tipo de acesso e modo de disponibilização, podendo ser visualizados ou descarregados conforme a configuração.

---

## 6. Funcionalidades Implementadas

### 6.1 Autenticação e Conta

O sistema permite que utilizadores criem conta, iniciem sessão e terminem sessão. As rotas privadas são protegidas por autenticação e o utilizador possui uma área de conta com estatísticas de recursos, empréstimos realizados e recursos emprestados.

### 6.2 Biblioteca

A biblioteca apresenta recursos em cards com capa, tipo, dono, estado, métricas e acções. O utilizador pode pesquisar e filtrar por tipo, formato, estado e critérios de popularidade.

### 6.3 Gestão de Recursos

Utilizadores autenticados podem adicionar recursos físicos ou digitais. O dono do recurso pode editar, remover e acompanhar os seus materiais.

### 6.4 Recursos Físicos

Os recursos físicos suportam empréstimo, localização, condição, prazo máximo, quantidade disponível e termos de utilização.

### 6.5 Recursos Digitais

Os recursos digitais suportam ficheiros, modo de visualização, download e informação sobre janela de acesso.

### 6.6 Empréstimos

O fluxo de empréstimo permite aceitar termos, criar reserva, aprovar quando necessário, acompanhar estado, devolver e manter histórico.

### 6.7 Extensão de Prazo

O cliente pode solicitar extensão de prazo. O dono do recurso pode aprovar ou recusar o pedido. A decisão fica registada e o cliente é notificado.

### 6.8 Notificações

A plataforma apresenta notificações sobre prazos próximos, pedidos de extensão, aprovações, recusas e recursos favoritos disponíveis.

### 6.9 Favoritos

O utilizador pode marcar recursos como favoritos. O sistema actualiza o estado visual e o contador de favoritos.

---

## 7. Arquitectura do Sistema

A aplicação foi construída com Laravel, utilizando o padrão MVC e Blade para renderização das páginas.

### 7.1 Camadas Principais

- **Models:** representam entidades como User, Resource e Reservation.
- **Controllers:** coordenam pedidos HTTP e respostas.
- **Requests:** validam dados submetidos por formulários.
- **Actions:** encapsulam regras de negócio reutilizáveis.
- **Views Blade:** apresentam a interface ao utilizador.
- **Migrations:** definem a estrutura da base de dados.
- **Tests:** verificam o comportamento esperado do sistema.

### 7.2 Separação de Responsabilidades

A separação de responsabilidades torna o sistema mais legível, testável e extensível. Por exemplo, a devolução de uma reserva é tratada por uma action própria, enquanto o controller apenas coordena a chamada e a resposta ao utilizador.

---

## 8. Modelos e Base de Dados

As entidades principais do sistema são:

### 8.1 User

Representa o utilizador autenticado. Pode possuir recursos, realizar reservas e marcar favoritos.

### 8.2 Resource

Representa a raiz de qualquer recurso. Guarda título, descrição, tipo, estado, quantidade disponível, capa, slug e dono.

### 8.3 PhysicalResource

Guarda dados específicos de recursos físicos, como localização, condição, prazo máximo de empréstimo, aprovação e número máximo de extensões.

### 8.4 DigitalResource

Guarda dados específicos de recursos digitais, como caminho do ficheiro, tipo de acesso e dias de acesso.

### 8.5 Reservation

Representa o ciclo de empréstimo. Guarda utilizador, recurso, datas, estado, aprovação, devolução, pedido de extensão e decisão sobre extensão.

### 8.6 TermAndCondition e TermAcceptance

Registam os termos aplicáveis ao uso de recursos e a aceitação desses termos pelo utilizador.

### 8.7 Resource User Favorites

Tabela pivot que liga utilizadores aos recursos favoritos.

---

## 9. Diagramas Explicativos

### 9.1 Casos de Uso

Actores principais:

- Utilizador
- Dono do recurso
- Sistema

Casos de uso:

- Pesquisar recurso.
- Consultar detalhes.
- Solicitar empréstimo.
- Devolver recurso.
- Pedir extensão.
- Aprovar ou recusar extensão.
- Gerir recurso.
- Receber notificação.

### 9.2 Relacionamento de Dados

O utilizador possui muitos recursos e muitas reservas. O recurso pertence a um utilizador, pode ter um detalhe físico ou digital, e pode possuir muitas reservas. As reservas ligam utilizadores a recursos durante o ciclo de empréstimo.

### 9.3 Classes Principais

As classes principais do domínio são User, Resource, PhysicalResource, DigitalResource, Reservation, TermAndCondition e TermAcceptance. Cada uma possui responsabilidade específica e relacionamentos que reflectem a lógica da biblioteca.

---

## 10. Segurança e Validações

O sistema implementa mecanismos de segurança e consistência, tais como:

- Protecção de rotas com middleware de autenticação.
- Validação de formulários com Form Requests.
- Protecção CSRF em formulários e chamadas assíncronas.
- Autorização para que apenas o dono edite ou decida sobre o seu recurso.
- Bloqueio de empréstimo do próprio recurso.
- Validação da disponibilidade antes de criar empréstimos.
- Hash automático de senhas.
- Validação de uploads por tipo e tamanho.

Essas medidas reduzem riscos de uso indevido e ajudam a manter a integridade dos dados.

---

## 11. Testes Realizados

Foram executados testes automatizados para verificar funcionalidades essenciais. Entre eles:

- Testes de autenticação.
- Testes de criação e gestão de recursos.
- Testes de recursos físicos.
- Testes de recursos digitais.
- Testes de reservas.
- Testes de devolução.
- Testes de disponibilidade.
- Testes de extensão de prazo.
- Testes de elegibilidade para novo empréstimo após devolução.

No estado actual, a suíte executada apresentou 16 testes aprovados e 118 asserções, indicando que os principais contratos da aplicação estão protegidos.

---

## 12. Resultados Alcançados

O projecto resultou numa aplicação funcional capaz de gerir uma biblioteca híbrida com interface moderna e fluxo completo de empréstimos. Entre os resultados alcançados destacam-se:

- Catálogo centralizado de recursos.
- Gestão distinta de recursos físicos e digitais.
- Fluxo de empréstimo com devolução e histórico.
- Pedidos de extensão com aprovação ou recusa.
- Notificações para utilizadores e donos de recursos.
- Sincronização entre estado da reserva e disponibilidade.
- Interface responsiva com cards, filtros, navbar, sidebar e toasts.
- Base técnica extensível para novas funcionalidades.

---

## 13. Conclusão

A VUTIVI demonstra como uma biblioteca pode ser modernizada através de uma aplicação web organizada, responsiva e orientada a processos reais. A plataforma reduz a dispersão de informação, melhora o controlo de empréstimos e oferece uma experiência mais clara para utilizadores e donos de recursos.

O sistema não se limita ao cadastro de materiais. Ele acompanha o ciclo completo do recurso, desde a publicação até à devolução, incluindo notificações, favoritos, termos e histórico.

---

## 14. Recomendações Futuras

Como evolução futura, recomenda-se:

- Implementar notificações persistentes em base de dados.
- Criar dashboard administrativo com métricas.
- Adicionar relatórios exportáveis.
- Criar API REST para integração com outras plataformas.
- Implementar broadcasting em tempo real.
- Adicionar auditoria de acções sensíveis.
- Criar recomendações de recursos com base em favoritos e histórico.
- Melhorar permissões com policies específicas do Laravel.

---

## 15. Nota Para Conversão em DOCX

Este relatório está estruturado em Markdown para conversão simples em DOCX. Pode ser convertido com uma das seguintes opções:

1. Abrir este ficheiro num editor compatível, como Microsoft Word ou LibreOffice, e guardar como `.docx`.
2. Usar Pandoc:

```bash
pandoc docs/relatorio-vutivi.md -o docs/relatorio-vutivi.docx
```

O conteúdo está organizado com títulos, subtítulos e secções formais para manter uma estrutura adequada em formato de relatório académico.
