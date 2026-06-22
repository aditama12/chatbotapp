<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Chat;
use App\Models\AdminReply;
use Illuminate\Support\Facades\Auth;

class AdminChatController extends Controller
{
    /**
     * Ambil daftar chat yang menunggu admin
     */
    public function getEscalatedChats()
    {
        $chats = Chat::escalated()
            ->pendingAdmin()
            ->with('user')
            ->orderBy('updated_at', 'desc') // 👈 UBAH INI JADI updated_at
            ->get()
            ->map(function ($chat) {
                return [
                    'id' => $chat->id,
                    'user_id' => $chat->user_id,
                    'user_name' => $chat->user->name ?? 'Unknown User',
                    'user_email' => $chat->user->email ?? '',
                    'message' => $chat->message,
                    'bot_reply' => $chat->reply,
                    'escalation_reason' => $chat->escalation_reason,
                    // 👈 UBAH INI biar indikator jam di sidebar juga ikut update
                    'escalated_at' => $chat->updated_at, 
                    'status' => $chat->admin_status,
                    'unread' => true
                ];
            });

        return response()->json([
            'success' => true,
            'data' => $chats
        ]);
    }

    /**
     * Ambil detail chat tertentu dengan histori lengkap
     */
    public function getChatDetail($chatId)
    {
        $chat = Chat::with('user', 'admin', 'adminReplies.admin')
            ->findOrFail($chatId);

        // Ambil semua admin replies
        $adminReplies = $chat->adminReplies()
            ->with('admin')
            ->orderBy('created_at', 'asc')
            ->get()
            ->map(function ($reply) {
                return [
                    'id' => $reply->id,
                    'type' => 'admin',
                    'message' => $reply->message,
                    'admin_name' => $reply->admin->name ?? 'Admin',
                    'created_at' => $reply->created_at
                ];
            });

        // Parse user follow-up messages (JSON format)
        $userFollowUps = [];
        if ($chat->user_follow_up) {
            try {
                $followUpData = json_decode($chat->user_follow_up, true);
                if (is_array($followUpData)) {
                    foreach ($followUpData as $followUp) {
                        $userFollowUps[] = [
                            'type' => 'user_follow_up',
                            'message' => $followUp['message'] ?? '',
                            'created_at' => $followUp['created_at'] ?? now()
                        ];
                    }
                }
            } catch (\Exception $e) {
                // Silent fallback
            }
        }

        // Merge dan sort by created_at
        $allMessages = array_merge($adminReplies->toArray(), $userFollowUps);
        usort($allMessages, function($a, $b) {
            return strtotime($a['created_at']) - strtotime($b['created_at']);
        });

        return response()->json([
            'success' => true,
            'data' => [
                'chat' => [
                    'id' => $chat->id,
                    'user_name' => $chat->user->name,
                    'user_email' => $chat->user->email,
                    'message' => $chat->message,
                    'bot_reply' => $chat->reply,
                    'status' => $chat->admin_status,
                    'escalation_reason' => $chat->escalation_reason,
                    'escalated_at' => $chat->escalated_at,
                    'resolved_at' => $chat->resolved_at
                ],
                'admin_replies' => $adminReplies,
                'user_follow_ups' => $userFollowUps,
                'all_messages' => $allMessages
            ]
        ]);
    }

    /**
     * Admin mengirim reply ke user (bisa berkali-kali)
     */
    /**
     * Admin mengirim reply ke user (bisa berkali-kali)
     */
    public function sendAdminReply(Request $request, $chatId)
    {
        $request->validate([
            'message' => 'required|string|max:2000'
        ]);

        $chat = Chat::findOrFail($chatId);

        // FIX: Kasih fallback (?? 1) biar gak crash masuk DB
        $adminId = Auth::id() ?? 1;

        // FIX: Pake null-safe operator (?->) biar aman dari error 500
        $adminName = Auth::user()?->name ?? 'Admin Disdukcapil'; 

        // Buat admin reply baru
        $adminReply = AdminReply::create([
            'chat_id' => $chatId,
            'admin_id' => $adminId,
            'message' => $request->message
        ]);

        $chat->touch(); // Biar urutan chat di sidebar naik ke atas

        return response()->json([
            'success' => true,
            'message' => 'Reply sent successfully',
            'data' => [
                'id' => $adminReply->id,
                'message' => $adminReply->message,
                'admin_name' => $adminName,
                'created_at' => $adminReply->created_at
            ]
        ]);
    }

    /**
     * Admin menandai chat sebagai selesai
     */
    public function resolveChat(Request $request, $chatId)
    {
        $chat = Chat::findOrFail($chatId);

        $chat->update([
            'admin_status' => 'resolved',
            'admin_id' => Auth::id() ?? 1, // FIX: Kasih fallback juga di sini ya
            'resolved_at' => now()
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Chat marked as resolved',
            'data' => $chat
        ]);
    }

    /**
     * User menambah follow-up message pada escalated chat
     */
    public function addFollowUpMessage(Request $request, $chatId)
    {
        $request->validate([
            'message' => 'required|string|max:2000'
        ]);

        $chat = Chat::findOrFail($chatId);

        // Allow both user dan guest untuk add follow-up

        // Store follow-up message as JSON array with timestamp
        $followUpData = [];
        if ($chat->user_follow_up) {
            try {
                $followUpData = json_decode($chat->user_follow_up, true) ?? [];
            } catch (\Exception $e) {
                // Fallback untuk format lama
                $followUpData = [];
            }
        }

        // Add new follow-up message
        $followUpData[] = [
            'message' => $request->message,
            'created_at' => now()->toIso8601String()
        ];

        $chat->user_follow_up = json_encode($followUpData);
        $chat->save();

        return response()->json([
            'success' => true,
            'message' => 'Follow-up message sent',
            'data' => $followUpData
        ]);
    }

    /**
     * Ambil semua chat user (untuk melihat histori)
     */
    public function getUserChats($userId)
    {
        $chats = Chat::where('user_id', $userId)
            ->with('admin', 'adminReplies')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $chats
        ]);
    }

    /**
     * Mengambil data statistik untuk Dashboard Admin
     */
    /**
     * Mengambil data statistik untuk Dashboard Admin
     */
    public function getDashboardStats()
    {
        // Hitung total metrik dari database, pastikan HANYA ngitung yang beneran di-escalate
        $totalChat = Chat::whereNotNull('escalated_at')->count();
        
        // 👉 FIX: Tambahin whereNotNull('escalated_at') biar chat dummy/error zaman dulu ga keikut!
        $pendingChat = Chat::where('admin_status', 'pending')
                           ->whereNotNull('escalated_at')
                           ->count();
                           
        $resolvedChat = Chat::where('admin_status', 'resolved')
                            ->whereNotNull('escalated_at')
                            ->count();

        // Ambil 5 chat terbaru yang dialihkan ke admin untuk tabel
        $recentChats = Chat::with('user')
            ->whereNotNull('escalated_at')
            ->orderBy('updated_at', 'desc')
            ->take(5)
            ->get()
            ->map(function ($chat) {
                return [
                    'id' => $chat->id,
                    'user' => $chat->user->name ?? 'User Anonim',
                    'message' => $chat->message,
                    'time' => $chat->updated_at, 
                    'status' => $chat->admin_status, 
                ];
            });

        return response()->json([
            'success' => true,
            'data' => [
                'metrics' => [
                    'total' => $totalChat,
                    'pending' => $pendingChat,
                    'resolved' => $resolvedChat,
                ],
                'recent_chats' => $recentChats
            ]
        ]);
    }

    /**
     * Mengambil semua riwayat obrolan (Selesai maupun belum)
     */
    public function getHistory()
    {
        $chats = Chat::with('user')
            ->orderBy('created_at', 'desc') // Urutkan dari yang paling baru
            ->get()
            ->map(function ($chat) {
                return [
                    'id' => $chat->id,
                    'user_name' => $chat->user->name ?? 'User Anonim',
                    'user_email' => $chat->user->email ?? '-',
                    'message' => $chat->message,
                    'bot_reply' => $chat->reply,
                    'status' => $chat->admin_status, 
                    'escalated_at' => $chat->escalated_at,
                    'created_at' => $chat->created_at,
                ];
            });

        return response()->json([
            'success' => true,
            'data' => $chats
        ]);
    }
}
