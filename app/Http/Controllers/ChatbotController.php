<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\Chat;
use Illuminate\Support\Facades\Auth;

class ChatbotController extends Controller
{
    public function send(Request $request)
    {
        $request->validate([
            'message' => 'required|string|max:2000',
            'user_id' => 'nullable|integer'
        ]);

        $userId = $request->user_id ?? Auth::id() ?? 1;

        // --- BARU: CEK BATAS 5 PERTANYAAN ---
        // Hitung berapa kali user sudah bertanya (asumsikan 1 baris chat = 1 pertanyaan)
        $chatCount = Chat::where('user_id', $userId)
                         ->where('created_at', '>=', today()) // Bisa disesuaikan: per hari
                         ->count();

        // Kalau sudah >= 5, paksa escalation jadi TRUE
        $isLimitReached = $chatCount >= 5;
        // ------------------------------------

        // [KODE API DOCSBOT TETAP SAMA]
        $teamId = env('DOCSBOT_TEAM_ID');
        $botId = env('DOCSBOT_BOT_ID');
        $apiKey = env('DOCSBOT_API_KEY');

        $response = Http::withToken($apiKey)
            ->timeout(30)
            ->post("https://api.docsbot.ai/teams/{$teamId}/bots/{$botId}/chat", [
                'question' => $request->message,
                'full_source' => false
            ]);

        $responseData = $response->json();
        $answer = $responseData['answer'] ?? 'Maaf, saya sedang mengalami gangguan.';
        $confidence = $responseData['confidence'] ?? null;
        
        // Cek escalation dari AI OR dari limit kita
        $needsEscalation = $this->checkIfNeedsEscalation($answer, $confidence) || $isLimitReached;

        // Simpan ke database
        $chat = Chat::create([
            'user_id' => $userId,
            'message' => $request->message,
            'reply' => $isLimitReached ? 'Mohon maaf, jatah 5 pertanyaan AI Anda untuk hari ini sudah habis. Saya alihkan ke Admin untuk bantuan lebih lanjut.' : $answer,
            'admin_status' => $needsEscalation ? 'pending' : 'resolved',
            'escalated_at' => $needsEscalation ? now() : null,
            'escalation_reason' => $isLimitReached ? 'Limit reached (5 questions)' : ($needsEscalation ? 'Low confidence' : null)
        ]);

        return response()->json([
            'status' => $response->status(),
            'answer' => $chat->reply, // Kirim jawaban yang mungkin sudah di-override oleh pesan limit
            'needs_escalation' => $needsEscalation,
            'chat_id' => $chat->id,
        ]);
    }

    /**
     * Deteksi apakah pertanyaan perlu dialihkan ke admin
     */
    /**
     * Deteksi apakah pertanyaan perlu dialihkan ke admin
     */
    private function checkIfNeedsEscalation($answer, $confidence = null)
    {
        // 1. Cek dari jawaban AI dulu (Ini cara paling akurat)
        $escalationKeywords = [
            'maaf, tidak bisa menjawab',
            'maaf, tidak ada',
            'tidak menemukan',
            'tidak memiliki informasi',
            'tidak dapat',
            'error',
            'i don\'t have'
        ];

        $answer_lower = strtolower($answer);
        foreach ($escalationKeywords as $keyword) {
            if (strpos($answer_lower, $keyword) !== false) {
                return true; // Kalau ada kata-kata menyerah, langsung alihkan!
            }
        }

        // 2. Cek skor confidence (HANYA JIKA DocsBot benar-benar ngirim nilainya)
        // Kalau $confidence itu null, pengecekan ini bakal dilewati
        if ($confidence !== null && $confidence < 0.6) {
            return true;
        }

        // Kalau lolos dua-duanya, berarti AI pintar dan aman!
        return false;
    }
}