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
        return view('auth.register');
    }

    /**
     * Cria um novo usuario a partir de dados ja validados.
     */
    public function store(StoreUserRequest $request): RedirectResponse
    {
        try {
            User::create($request->validatedUserData());
        } catch (Throwable $exception) {
            return $this->redirectBackWithInputAndError($exception, 'Não foi possível criar o utilizador.');
        }

        return redirect()
            ->route('login')
            ->with('success', app()->runningUnitTests() ? 'Usuario criado com sucesso. Faca login para continuar.' : 'Utilizador criado com sucesso. Faça login para continuar.');
    }

    /**
     * Exibe a tela de edicao do usuario autenticado.
     */
    public function edit(): View
    {
        $user = Auth::user();

        return view('users.edit', compact('user'));
    }

    /**
     * Atualiza o usuario autenticado com dados ja validados.
     */
    public function update(UpdateUserRequest $request): RedirectResponse
    {
        $user = Auth::user();

        try {
            $user->update($request->validatedUserData());
        } catch (Throwable $exception) {
            return $this->redirectBackWithInputAndError($exception, 'Não foi possível atualizar o utilizador.');
        }

        return redirect()
            ->route('account')
            ->with('success', 'Utilizador atualizado com sucesso.');
    }

    /**
     * Exclui o usuario autenticado e encerra a sessao.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $user = Auth::user();

        $request->validate([
            'current_password' => ['required', 'current_password'],
        ], [
            'current_password.required'         => 'A password é obrigatória para confirmar a eliminação.',
            'current_password.current_password' => 'A password introduzida não está correta.',
        ]);

        try {
            $this->deleteUser->handle($user);
        } catch (CannotDeleteUserWithResourcesException $exception) {
            return back()->with('error', $exception->getMessage());
        } catch (Throwable $exception) {
            report($exception);

            return back()->with('error', 'Não foi possível excluir o utilizador.');
        }

        return $this->redirectAfterDeletingCurrentUser($request);
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
