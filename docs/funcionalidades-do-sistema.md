# Funcionalidades Do Sistema

## Visao Geral

Este sistema gerencia usuarios, recursos fisicos, recursos digitais e reservas.
O objetivo principal e permitir que usuarios autenticados cadastrem recursos, consultem o catalogo e controlem emprestimos com regras de disponibilidade e prazo.

## Principios Aplicados

- Validacao de entrada fica em `FormRequest`.
- Persistencia fica em `Actions`.
- Controllers apenas orquestram fluxo HTTP.
- Regras de negocio criticas usam excecoes e validacoes dedicadas.
- Funcoes pequenas fazem uma coisa por vez.

## Arquitetura Das Camadas

### Controllers

Os controllers recebem a requisicao, chamam requests e actions, e devolvem resposta HTTP.

Principais controllers:

- `Auth\LoginController`: autentica e encerra sessao.
- `UserController`: cadastra, edita e exclui usuarios.
- `ResourceController`: expoe o catalogo compartilhado.
- `PhysicalResourceController`: CRUD do subtipo fisico.
- `DigitalResourceController`: CRUD do subtipo digital.
- `ReservationController`: cria, atualiza, devolve e exclui reservas.

### Form Requests

Os requests definem o contrato de entrada e impedem que `store` e `update` validem dados diretamente.

Requests principais:

- `LoginRequest`
- `StoreUserRequest`
- `UpdateUserRequest`
- `StorePhysicalResourceRequest`
- `UpdatePhysicalResourceRequest`
- `StoreDigitalResourceRequest`
- `UpdateDigitalResourceRequest`
- `StoreReservationRequest`
- `UpdateReservationRequest`

### Actions

As actions fazem a persistencia ou uma regra pequena e isolada.

Exemplos:

- `AuthenticateUser`: tenta autenticar.
- `LogoutUser`: encerra a sessao.
- `DeleteUser`: exclui usuario quando o dominio permite.
- `CreatePhysicalResource`: persiste recurso fisico.
- `UpdatePhysicalResource`: atualiza recurso fisico.
- `CreateDigitalResource`: persiste recurso digital.
- `UpdateDigitalResource`: atualiza recurso digital.
- `CreateReservation`: cria reserva.
- `UpdateReservation`: atualiza reserva.
- `DeleteReservation`: remove reserva.
- `ReturnReservation`: marca devolucao.
- `ValidateReservationAgainstResource`: verifica tipo, prazo e disponibilidade.
- `SyncResourceAvailability`: recalcula o status real do recurso.

## Funcionalidades

### 1. Autenticacao

#### Objetivo

Permitir que usuarios entrem e saiam do sistema com seguranca.

#### Rotas

- `GET /login`
- `POST /login`
- `POST /logout`

#### Fluxo De Login

1. `LoginController::create()` exibe a tela de login.
2. `LoginRequest` valida `username` e `password`.
3. `LoginRequest::prepareForValidation()` normaliza o username.
4. `LoginController::store()` chama `AuthenticateUser`.
5. `AuthenticateUser::handle()` tenta autenticar.
6. Em caso de sucesso, a sessao e regenerada.
7. Em caso de falha de credenciais, a excecao `InvalidCredentialsException` e convertida em mensagem amigavel.

#### Fluxo De Logout

1. `LoginController::destroy()` chama `LogoutUser`.
2. `LogoutUser::handle()` faz logout, invalida a sessao e gira o token CSRF.

### 2. Cadastro E Gestao De Usuarios

#### Objetivo

Permitir cadastro de novos usuarios, edicao da conta e exclusao segura.

#### Rotas

- `GET /register`
- `POST /register`
- `GET /users/{user}/edit`
- `PUT /users/{user}`
- `DELETE /users/{user}`

#### Fluxo De Cadastro

1. `UserController::create()` mostra o formulario.
2. `StoreUserRequest` valida nome, username, email e senha.
3. `UserFormRequest::prepareForValidation()` normaliza os campos textuais.
4. `UserController::store()` persiste o usuario com `User::create()`.

#### Fluxo De Atualizacao

1. `UserController::edit()` carrega o usuario.
2. `UpdateUserRequest` valida os dados e ignora o proprio registro nas regras de unicidade.
3. `UserFormRequest::validatedUserData()` remove a senha vazia para significar "manter a atual".
4. `UserController::update()` atualiza o registro.

#### Fluxo De Exclusao

1. `UserController::destroy()` identifica se o usuario alvo e o usuario autenticado.
2. `DeleteUser::handle()` impede a exclusao quando ainda existem recursos sob sua posse.
3. Se a exclusao for do proprio usuario autenticado, `LogoutUser` tambem encerra a sessao.

### 3. Catalogo Compartilhado De Recursos

#### Objetivo

Exibir um ponto unico de consulta para recursos disponiveis, independentemente do subtipo.

#### Rotas

- `GET /resources`
- `GET /resources/{resource}`
- `DELETE /resources/{resource}`

#### Comportamento

- `ResourceController::index()` lista apenas recursos com status `available`.
- `ResourceController::show()` exibe detalhes do recurso, dono, subtipo e reservas.
- `ResourceController::destroy()` remove o recurso do catalogo.

### 4. Recursos Fisicos

#### Objetivo

Gerenciar itens fisicos com informacoes de localizacao, prazo maximo de emprestimo e estado.

#### Rotas

- `GET /physical-resources`
- `GET /physical-resources/create`
- `POST /physical-resources`
- `GET /physical-resources/{id}`
- `GET /physical-resources/{id}/edit`
- `PUT /physical-resources/{id}`
- `DELETE /physical-resources/{id}`

#### Validacao

- `StorePhysicalResourceRequest` e `UpdatePhysicalResourceRequest` validam:
  - `title`
  - `description`
  - `status`
  - `quantity_available`
  - `location`
  - `max_loan_days`
  - `condition`

#### Armazenamento

1. `PhysicalResourceController::store()` recebe dados ja validados.
2. `CreatePhysicalResource::handle()` cria o recurso base e os detalhes fisicos dentro de uma transacao.

#### Atualizacao

1. `PhysicalResourceController::update()` recebe dados ja validados.
2. `UpdatePhysicalResource::handle()` atualiza a tabela `resources` e a tabela `physical_resources` na mesma transacao.

### 5. Recursos Digitais

#### Objetivo

Gerenciar itens digitais com caminho de ficheiro, tipo de acesso e duracao de acesso.

#### Rotas

- `GET /digital-resources`
- `GET /digital-resources/create`
- `POST /digital-resources`
- `GET /digital-resources/{id}`
- `GET /digital-resources/{id}/edit`
- `PUT /digital-resources/{id}`
- `DELETE /digital-resources/{id}`

#### Validacao

- `StoreDigitalResourceRequest` e `UpdateDigitalResourceRequest` validam:
  - `title`
  - `description`
  - `status`
  - `quantity_available`
  - `file_path`
  - `access_type`
  - `access_days`

#### Armazenamento

1. `DigitalResourceController::store()` recebe dados ja validados.
2. `CreateDigitalResource::handle()` cria o recurso base e os detalhes digitais dentro de uma transacao.

#### Atualizacao

1. `DigitalResourceController::update()` recebe dados ja validados.
2. `UpdateDigitalResource::handle()` atualiza a tabela `resources` e a tabela `digital_resources` na mesma transacao.

### 6. Reservas

#### Objetivo

Controlar emprestimos e devolucoes garantindo tipo correto, prazo permitido e disponibilidade real.

#### Rotas

- `GET /reservations`
- `GET /reservations/create`
- `POST /reservations`
- `GET /reservations/{id}`
- `GET /reservations/{id}/edit`
- `PUT /reservations/{id}`
- `DELETE /reservations/{id}`
- `PATCH /reservations/{id}/return`

#### Validacao Basica

`StoreReservationRequest` e `UpdateReservationRequest` validam:

- `resource_id`
- `user_id`
- `type`
- `start_date`
- `end_date`

#### Validacao De Negocio

`ValidateReservationAgainstResource::handle()` executa tres responsabilidades de negocio:

1. Garante que o `type` da reserva corresponde ao tipo real do recurso.
2. Garante que a data final respeita o prazo maximo do subtipo.
3. Garante disponibilidade real considerando reservas abertas.

#### Fluxo De Criacao

1. `ReservationController::store()` recebe dados ja validados.
2. O recurso alvo e carregado com seus relacionamentos.
3. `ValidateReservationAgainstResource` valida o negocio.
4. `CreateReservation::handle()` persiste a reserva.
5. `SyncResourceAvailability::handle()` recalcula o status do recurso.

#### Fluxo De Atualizacao

1. `ReservationController::update()` carrega a reserva atual.
2. O recurso anterior e guardado para sincronizacao futura.
3. O novo recurso alvo e carregado.
4. `ValidateReservationAgainstResource` valida a mudanca, ignorando a propria reserva na contagem de disponibilidade.
5. `UpdateReservation::handle()` atualiza a reserva.
6. `SyncResourceAvailability` recalcula o recurso atual.
7. Se a reserva mudou de recurso, o recurso anterior tambem e recalculado.

#### Fluxo De Devolucao

1. `ReservationController::return()` localiza a reserva.
2. `ReturnReservation::handle()` grava `returned_at`.
3. `SyncResourceAvailability::handle()` recalcula a disponibilidade.

#### Fluxo De Exclusao

1. `ReservationController::destroy()` localiza a reserva.
2. `DeleteReservation::handle()` remove o registro.
3. `SyncResourceAvailability::handle()` recalcula o status do recurso.

### 7. Acesso Digital

#### Estado Atual

As classes `DigitalAccess` e `DigitalAccessController` ainda estao em estado de esqueleto inicial.

#### O Que Ja Existe

- Estrutura inicial de modelo.
- Estrutura inicial de controller com `TODO`s claros.

#### O Que Ainda Falta

- Regras de negocio.
- Rotas.
- Views.
- Persistencia real.
- Testes.

## Funcao De Cada Grupo De Funcoes

### Funcoes De Validacao

Responsaveis por conferir contrato e normalizar dados antes da persistencia.

- `prepareForValidation()`: limpa e normaliza entrada.
- `rules()`: define o contrato obrigatorio.
- `resourceData()`, `physicalResourceData()`, `digitalResourceData()`, `reservationData()`: separam conjuntos de dados por responsabilidade.

### Funcoes De Persistencia

Responsaveis apenas por gravar ou atualizar dados.

- `CreatePhysicalResource::handle()`
- `UpdatePhysicalResource::handle()`
- `CreateDigitalResource::handle()`
- `UpdateDigitalResource::handle()`
- `CreateReservation::handle()`
- `UpdateReservation::handle()`
- `DeleteReservation::handle()`
- `ReturnReservation::handle()`

### Funcoes De Regra De Negocio

Responsaveis por garantir integridade do dominio.

- `ValidateReservationAgainstResource::handle()`
- `DeleteUser::handle()`
- `AuthenticateUser::handle()`
- `SyncResourceAvailability::handle()`

## Testes Que Protegem O Sistema

Os testes de feature garantem que os contratos HTTP e de negocio continuem estaveis.

Arquivos principais:

- `tests/Feature/Auth/LoginTest.php`
- `tests/Feature/Users/UserControllerTest.php`
- `tests/Feature/Resources/DigitalResourceCrudTest.php`
- `tests/Feature/Resources/PhysicalResourceCrudTest.php`
- `tests/Feature/Resources/ResourceCrudTest.php`
- `tests/Feature/Resources/ResourceSchemaTest.php`
- `tests/Feature/Reservations/ReservationCrudTest.php`

## Resumo Final

Hoje o sistema esta organizado para que:

- requests validem;
- actions gravem ou apliquem regra de negocio;
- controllers coordenem;
- testes protejam o comportamento esperado.

Esse desenho evita funcoes inchadas e facilita manutencao, leitura e evolucao segura do sistema.
