<?php

namespace App\Http\Controllers\Auth;

use App\Actions\Auth\AuthenticateUser;
use App\Actions\Auth\LogoutUser;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Exceptions\InvalidCredentialsException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Throwable;

/**
 * Traduz os casos de uso de autenticacao em respostas HTTP e mensagens de feedback.
 */
class LoginController extends Controller
{
    /**
     * Injeta as acoes que executam autenticacao e encerramento de sessao.
     */
    public function __construct(
        private AuthenticateUser $authenticateUser,
        private LogoutUser $logoutUser,
    ) {
    }

    /**
     * Exibe a tela de login.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Autentica o usuario e inicia uma nova sessao.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        try {
            $this->authenticateUser->handle($request->credentials());
        } catch (InvalidCredentialsException $exception) {
            return back()
                ->withInput($request->only('username'))
                ->with('error', $exception->getMessage());
        } catch (Throwable $exception) {
            report($exception);

            return back()
                ->withInput($request->only('username'))
                ->with('error', 'Não foi possível iniciar a sessão.');
        }

        // Regeneramos a sessao apenas apos o login bem-sucedido para reduzir risco de fixacao de sessao.
        $request->session()->regenerate();

        return redirect()->intended('/home');
    }

    /**
     * Encerra a sessao autenticada atual.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $this->logoutUser->handle($request);

        return redirect()->route('index');
    }
}
