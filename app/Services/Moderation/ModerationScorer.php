<?php

namespace App\Services\Moderation;

use App\Models\BlacklistHash;
use App\Models\BlacklistWord;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class ModerationScorer
{
    public const KEYWORDS = [
        'explosivo' => 'critical',
        'bomba' => 'critical',
        'arma' => 'high',
        'droga' => 'high',
        'quimico' => 'medium',
        'veneno' => 'critical',
        'malware' => 'high',
        'proibido' => 'critical',
    ];

    public function analyze(string $title, ?string $text = null, ?string $hash = null): array
    {
        $normalizedHash = Str::lower(trim((string) $hash));
        $content = Str::lower($title.' '.($text ?? '').' '.$normalizedHash);
        $score = (crc32($content) % 26) + 8;
        $detected = [];

        foreach ($this->keywordEntries() as $word => $metadata) {
            if (str_contains($content, $word)) {
                $severity = $metadata['severity'];
                $detected[] = [
                    'word' => $word,
                    'severity' => $severity,
                    'category' => $metadata['category'],
                ];

                $score += match ($severity) {
                    'critical' => 42,
                    'high' => 28,
                    'medium' => 16,
                    default => 8,
                };
            }
        }

        foreach ($this->blacklistHashes() as $blacklistedHash) {
            if ($normalizedHash !== '' && hash_equals($blacklistedHash, $normalizedHash)) {
                $detected[] = [
                    'word' => $blacklistedHash,
                    'severity' => 'critical',
                    'category' => 'Hash rejeitado',
                ];
                $score = max($score + 70, 95);
                break;
            }

            if ($blacklistedHash !== '' && str_contains($content, $blacklistedHash)) {
                $detected[] = [
                    'word' => $blacklistedHash,
                    'severity' => 'critical',
                    'category' => 'Hash rejeitado',
                ];
                $score += 52;
                break;
            }
        }

        $score = min(100, $score);
        $status = $this->statusFor($score);

        return [
            'score' => $score,
            'status' => $status,
            'reason' => $this->reasonFor($score),
            'detected' => $detected,
        ];
    }

    public function statusFor(int $score): string
    {
        return $score <= 30 ? 'approved' : ($score >= 80 ? 'rejected' : 'review');
    }

    public function reasonFor(int $score): string
    {
        if ($score <= 30) {
            return 'Nenhuma ameaca detectada';
        }

        if ($score >= 80) {
            return 'Risco critico detectado pelo pipeline automatico';
        }

        return 'Conteudo suspeito requer revisao humana';
    }

    private function keywordEntries(): array
    {
        $entries = collect(self::KEYWORDS)
            ->map(fn (string $severity) => [
                'severity' => $severity,
                'category' => 'Regra padrao',
            ])
            ->all();

        $columns = ['word'];
        $hasCategory = Schema::hasColumn('blacklist_words', 'category');
        $hasSeverity = Schema::hasColumn('blacklist_words', 'severity');

        if ($hasCategory) {
            $columns[] = 'category';
        }

        if ($hasSeverity) {
            $columns[] = 'severity';
        }

        BlacklistWord::get($columns)->each(function (BlacklistWord $word) use (&$entries, $hasCategory, $hasSeverity) {
            $normalized = Str::lower(trim($word->word));

            if ($normalized === '') {
                return;
            }

            $entries[$normalized] = [
                'severity' => $hasSeverity ? ($word->severity ?: 'high') : 'high',
                'category' => $hasCategory ? ($word->category ?: 'Palavra perigosa') : 'Palavra perigosa',
            ];
        });

        return $entries;
    }

    private function blacklistHashes(): array
    {
        return BlacklistHash::pluck('hash')
            ->map(fn ($hash) => Str::lower(trim((string) $hash)))
            ->filter()
            ->values()
            ->all();
    }
}
