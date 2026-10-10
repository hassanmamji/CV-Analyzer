<?php

namespace App\Services;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use RuntimeException;

class CvAnalyzerService
{
    private const TOTAL_BUDGET = 60; // seconds

    public function analyze(string $cvText, ?string $jobRole = null): array
    {
        set_time_limit(120);
        $deadline = time() + self::TOTAL_BUDGET;

        $raw    = $this->callGemini($cvText, $jobRole, $deadline);
        $result = $this->parseJson($raw);

        if ($result === null) {
            throw new RuntimeException('AI ka jawab samajh nahi aaya. Dobara try karo.');
        }

        return $result;
    }

    private function models(): array
    {
        return array_values(array_unique([
            config('services.gemini.model'),
            'gemini-flash-lite-latest',
        ]));
    }

    private function callGemini(string $cvText, ?string $jobRole, int $deadline): string
    {
        $role = $jobRole ?: 'general';

        $prompt = <<<PROMPT
You are an expert CV reviewer. Analyze the CV below for the target role: {$role}.

Keep it concise: max 4 items per list, each item under 20 words.
Reply with ONLY valid JSON (no markdown) in exactly this format:
{
  "score": 0-100,
  "summary": "2-3 line overall review",
  "strengths": ["..."],
  "weaknesses": ["..."],
  "section_scores": {"formatting": 0-100, "skills": 0-100, "experience": 0-100, "education": 0-100},
  "improvements": ["specific, actionable suggestions"],
  "missing_keywords": ["..."]
}

CV:
{$cvText}
PROMPT;

        $lastStatus  = null;
        $lastMessage = '';

        foreach ($this->models() as $model) {
            $remaining = $deadline - time();
            if ($remaining < 5) {
                break;
            }

            try {
                $response = Http::withHeaders(['x-goog-api-key' => config('services.gemini.key')])
                    ->connectTimeout(10)
                    ->timeout(min(40, $remaining))
                    ->post("https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent", [
                        'contents' => [['parts' => [['text' => $prompt]]]],
                    ]);
            } catch (ConnectionException $e) {
                Log::warning('Gemini slow/connection problem', ['model' => $model, 'msg' => $e->getMessage()]);
                $lastStatus  = 0;
                $lastMessage = 'Connection slow ya band hai.';
                continue;
            }

            if ($response->successful()) {
                $text = $response->json('candidates.0.content.parts.0.text', '');
                if ($text !== '') {
                    return $text;
                }
                continue;
            }

            $lastStatus  = $response->status();
            $lastMessage = (string) $response->json('error.message', '');
            Log::warning('Gemini error', [
                'model'   => $model,
                'status'  => $lastStatus,
                'message' => $lastMessage,
            ]);
        }

        if ($lastStatus === 429) {
            throw new RuntimeException('Aaj ki free AI limit khatam ho gayi. Kal try karo ya nayi API key / doosra provider lagao.');
        }

        if ($lastStatus === 503 || $lastStatus === 0 || $lastStatus === null) {
            throw new RuntimeException('Google ke AI servers abhi slow ya busy hain. 1 minute baad try karo.');
        }

        // Asli wajah screen pe dikha do taake guess na karna paray
        throw new RuntimeException('AI error (code ' . $lastStatus . '): ' . mb_substr($lastMessage, 0, 200));
    }

    private function parseJson(string $raw): ?array
    {
        $clean = trim(preg_replace('/^```(?:json)?|```$/m', '', $raw));
        $data  = json_decode($clean, true);

        return (is_array($data) && isset($data['score'])) ? $data : null;
    }
}