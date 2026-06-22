<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Chat;
use App\Models\Faq;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{
    // Menampilkan halaman Chat UI
    public function index()
    {
        // Mengambil histori chat untuk ditampilkan (opsional, bisa dibatasi berdasarkan user_id jika ada)
        $chats = Chat::all();
        return view('chat', compact('chats'));
    }

    // Menangani pesan yang dikirim via AJAX (fetch)
    public function reply(Request $request)
    {
        $request->validate([
            'message' => 'required|string'
        ]);

        $userMessage = $request->message;

        // Mencari jawaban di tabel FAQ berdasarkan kata kunci (LIKE)
        $faq = Faq::where('question', 'LIKE', '%' . $userMessage . '%')->first();

        if ($faq) {
            $botReply = $faq->answer;
        } else {
            // Jika tidak ada di FAQ, fallback ke DocsBot AI API
            $docsbotApiKey = trim(env('DOCSBOT_API_KEY'));
            $teamId = trim(env('DOCSBOT_TEAM_ID'));
            $botId = trim(env('DOCSBOT_BOT_ID'));

            if ($docsbotApiKey && $teamId && $botId) {
                try {
                    $userId = Auth::id();

                    // Ambil 5 riwayat chat terakhir untuk memberikan KONTEKS ingatan ke AI
                    if ($userId) {
                        $histories = Chat::where('user_id', $userId)
                            ->latest()
                            ->take(5)
                            ->get()->reverse();
                    } else {
                        $histories = collect(); // Riwayat kosong untuk guest
                    }

                    $historyArray = [];
                    foreach ($histories as $h) {
                        $historyArray[] = "User: " . $h->message;
                        $historyArray[] = "DocsBot: " . $h->reply;
                    }

                    // Sisipkan konteks riwayat ke dalam pertanyaan agar bot "ingat" percakapan
                    $historyContext = implode("\n", $historyArray);
                    $finalQuestion = $historyContext ? "Riwayat obrolan:\n" . $historyContext . "\n\nPertanyaan baru: " . $userMessage : $userMessage;

                    $response = Http::withHeaders([
                        'Authorization' => 'Bearer ' . $docsbotApiKey,
                        'Content-Type' => 'application/json'
                    ])->withoutVerifying()->post("https://api.docsbot.ai/teams/{$teamId}/bots/{$botId}/ask", [
                        'question' => $finalQuestion,
                        'full_source' => false
                    ]);

                    if ($response->successful()) {
                        $botReply = $response->json('answer') ?? "Maaf, DocsBot tidak memberikan jawaban.";
                    } else {
                        // Kita paksa sistem mengambil pesan mendalam atau body mentah dari DocsBot
                        $errorMsg = $response->json('message') ?? $response->body();
                        $botReply = "Error dari DocsBot: " . $errorMsg;
                    }
                } catch (\Exception $e) {
                    // Menampilkan pesan error asli dari sistem/koneksi
                    $botReply = "Gangguan koneksi/sistem: " . $e->getMessage();
                }
            } else {
                // Fallback default jika konfigurasi .env belum lengkap
                $botReply = "Maaf, saya belum mengerti. Pengaturan DocsBot (API Key, Team ID, Bot ID) belum diatur di server.";
            }
        }

        // Simpan percakapan ke database (berdasarkan model Chat Anda)
        Chat::create([
            'user_id' => Auth::id() ?? 1,
            'message' => $userMessage,
            'reply' => $botReply
        ]);

        // Kembalikan respons dalam format JSON untuk ditampilkan di front-end
        return response()->json([
            'answer' => $botReply
        ]);
    }
}