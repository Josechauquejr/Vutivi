<?php

namespace App\Http\Controllers;

use App\Actions\Auth\LogoutUser;
use App\Actions\Users\DeleteUser;
use App\Exceptions\CannotDeleteUserWithResourcesException;
use App\Http\Requests\Users\StoreUserRequest;
use App\Http\Requests\Users\UpdateUserRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Throwable;

/**
 * Mantem os fluxos de conta pequenos e delega regras de negocio para requests e acoes dedicadas.
 */
class UserController extends Controller
{
    /**
     * Injeta as acoes responsaveis pela exclusao e pelo logout do usuario.
     */
    public function __construct(
        private DeleteUser $deleteUser,
        private LogoutUser $logoutUser,
    ) {
    }

    /**
     * Exibe a tela de cadastro de usuario.
     */
    public function create(): View
    {
        return view('users.create');
    }

    /**
     * Cria um novo usuario a partir de dados ja validados.
     */
    public function store(StoreUserRequest $request): RedirectResponse
    {
        try {
            User::create($request->validatedUserData());
        } catch (Throwable $exception) {
            return $this->redirectBackWithInputAndError($exception, 'Nao foi possivel criar o usuario.');
        }

        return redirect()
            ->route('login')
            ->with('success', 'Usuario criado com sucesso. Faca login para continuar.');
    }

    /**
     * Exibe a tela de edicao do usuario informado.
     */
    public function edit(User $user): View
    {
        return view('users.edit', compact('user'));
    }

    /**
     * Atualiza um usuario com dados ja validados.
     */
    public function update(UpdateUserRequest $request, User $user): RedirectResponse
    {
        try {
            $user->update($request->validatedUserData());
        } catch (Throwable $exception) {
            return $this->redirectBackWithInputAndError($exception, 'Nao foi possivel atualizar o usuario.');
        }

        return redirect()
            ->route('home')
            ->with('success', 'Usuario atualizado com sucesso.');
    }

    /**
     * Exclui um usuario e encerra a sessao quando o proprio dono remove a conta.
     */
    public function destroy(Request $request, User $user): RedirectResponse
    {
        // Precisamos decidir isso antes da exclusao porque o fluxo do proprio usuario tambem encerra a sessao.
        $isCurrentUser = $this->isCurrentUser($user);

        try {
            $this->deleteUser->handle($user);
        } catch (CannotDeleteUserWithResourcesException $exception) {
            return back()->with('error', $exception->getMessage());
        } catch (Throwable $exception) {
            report($exception);

            return back()->with('error', 'Nao foi possivel excluir o usuario.');
        }

        if ($isCurrentUser) {
            return $this->redirectAfterDeletingCurrentUser($request);
        }

        return redirect()
            ->route('home')
            ->with('success', 'Usuario excluido com sucesso.');
    }

    /**
     * Informa se o usuario alvo da operacao e o mesmo usuario autenticado.
     */
    private function isCurrentUser(User $user): bool
    {
        return Auth::id() === $user->id;
    }

    /**
     * Encerra a sessao do usuario atual e redireciona apos a exclusao da conta.
     */
    private function redirectAfterDeletingCurrentUser(Request $request): RedirectResponse
    {
        $this->logoutUser->handle($request);

        return redirect()
            ->route('login')
            ->with('success', 'Sua conta foi excluida com sucesso.');
    }

    /**
     * Registra a excecao, preserva a entrada do formulario e devolve uma mensagem amigavel.
     */
    private function redirectBackWithInputAndError(Throwable $exception, string $message): RedirectResponse
    {
        report($exception);

        return back()
            ->withInput()
            ->with('error', $message);
    }
}
