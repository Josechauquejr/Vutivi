<?php

namespace App\Http\Controllers;

use App\Models\Fine;
use App\Models\Reservation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $isManager = $user->canManageLoans();

        $myActiveCount   = Reservation::where('user_id', $user->id)->whereNull('returned_at')->whereIn('status', Reservation::COPY_HOLDING_STATUSES)->count();
        $myOverdueCount  = Reservation::where('user_id', $user->id)->whereNull('returned_at')->whereIn('status', [Reservation::STATUS_IN_USE, Reservation::STATUS_EXTENDED])->whereDate('end_date', '<', now()->toDateString())->count();
        $myHistoryCount  = Reservation::where('user_id', $user->id)->count();
        $myFinesCount    = Fine::where('user_id', $user->id)->count();

        $allActiveCount  = $isManager ? Reservation::whereNull('returned_at')->whereIn('status', Reservation::COPY_HOLDING_STATUSES)->count() : null;
        $allOverdueCount = $isManager ? Reservation::whereNull('returned_at')->whereIn('status', [Reservation::STATUS_IN_USE, Reservation::STATUS_EXTENDED])->whereDate('end_date', '<', now()->toDateString())->count() : null;
        $allHistoryCount = $isManager ? Reservation::count() : null;
        $allFinesCount   = $isManager ? Fine::count() : null;

        return view('reports.index', compact(
            'isManager',
            'myActiveCount', 'myOverdueCount', 'myHistoryCount', 'myFinesCount',
            'allActiveCount', 'allOverdueCount', 'allHistoryCount', 'allFinesCount',
        ));
    }

    public function activeLoans(Request $request): StreamedResponse
    {
        abort_unless(Auth::user()->canManageLoans() || $request->boolean('mine'), 403);

        $query = Reservation::with(['resource:id,title,type', 'user:id,name,email'])
            ->whereNull('returned_at')
            ->whereIn('status', Reservation::COPY_HOLDING_STATUSES)
            ->when($request->boolean('mine'), fn ($q) => $q->whereHas('resource', fn ($r) => $r->where('owner_id', Auth::id())))
            ->orderBy('end_date');

        return $this->streamCsv('emprestimos_ativos', [
            ['ID', 'Recurso', 'Tipo', 'Utilizador', 'Email', 'Status', 'Início', 'Devolução'],
        ], $query->cursor()->map(fn ($r) => [
            $r->id,
            $r->resource?->title,
            $r->resource?->type,
            $r->user?->name,
            $r->user?->email,
            $r->status,
            $r->start_date?->format('d/m/Y'),
            $r->end_date?->format('d/m/Y'),
        ]));
    }

    public function overdueLoans(Request $request): StreamedResponse
    {
        abort_unless(Auth::user()->canManageLoans() || $request->boolean('mine'), 403);

        $query = Reservation::with(['resource:id,title', 'user:id,name,email'])
            ->whereNull('returned_at')
            ->whereIn('status', [Reservation::STATUS_IN_USE, Reservation::STATUS_EXTENDED])
            ->whereDate('end_date', '<', now()->toDateString())
            ->when($request->boolean('mine'), fn ($q) => $q->whereHas('resource', fn ($r) => $r->where('owner_id', Auth::id())))
            ->orderBy('end_date');

        return $this->streamCsv('emprestimos_atrasados', [
            ['ID', 'Recurso', 'Utilizador', 'Email', 'Deveria devolver em', 'Dias em atraso'],
        ], $query->cursor()->map(fn ($r) => [
            $r->id,
            $r->resource?->title,
            $r->user?->name,
            $r->user?->email,
            $r->end_date?->format('d/m/Y'),
            $r->end_date ? now()->diffInDays($r->end_date) : '',
        ]));
    }

    public function loanHistory(Request $request): StreamedResponse
    {
        abort_unless(Auth::user()->canManageLoans() || $request->boolean('mine'), 403);

        $query = Reservation::with(['resource:id,title,type', 'user:id,name,email'])
            ->when($request->boolean('mine'), fn ($q) => $q->where('user_id', Auth::id()))
            ->orderByDesc('created_at');

        return $this->streamCsv('historico_emprestimos', [
            ['ID', 'Recurso', 'Tipo', 'Utilizador', 'Status', 'Início', 'Devolução prevista', 'Devolvido em'],
        ], $query->cursor()->map(fn ($r) => [
            $r->id,
            $r->resource?->title,
            $r->resource?->type,
            $r->user?->name,
            $r->status,
            $r->start_date?->format('d/m/Y'),
            $r->end_date?->format('d/m/Y'),
            $r->actual_return_date?->format('d/m/Y') ?? '',
        ]));
    }

    public function fines(Request $request): StreamedResponse
    {
        abort_unless(Auth::user()->canManageLoans() || $request->boolean('mine'), 403);

        $query = Fine::with(['user:id,name,email', 'reservation.resource:id,title'])
            ->when($request->boolean('mine'), fn ($q) => $q->where('user_id', Auth::id()))
            ->orderByDesc('created_at');

        return $this->streamCsv('multas', [
            ['ID', 'Utilizador', 'Email', 'Recurso', 'Dias em atraso', 'Valor/dia', 'Total', 'Pago em'],
        ], $query->cursor()->map(fn ($f) => [
            $f->id,
            $f->user?->name,
            $f->user?->email,
            $f->reservation?->resource?->title,
            $f->days_overdue,
            number_format($f->rate_per_day, 2),
            number_format($f->total, 2),
            $f->paid_at?->format('d/m/Y') ?? 'Pendente',
        ]));
    }

    private function streamCsv(string $filename, array $headers, iterable $rows): StreamedResponse
    {
        return response()->streamDownload(function () use ($headers, $rows) {
            $handle = fopen('php://output', 'w');
            fprintf($handle, chr(0xEF) . chr(0xBB) . chr(0xBF)); // UTF-8 BOM for Excel

            foreach ($headers as $row) {
                fputcsv($handle, $row, ';');
            }

            foreach ($rows as $row) {
                fputcsv($handle, $row, ';');
            }

            fclose($handle);
        }, $filename . '_' . now()->format('Ymd_His') . '.csv', [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }
}
