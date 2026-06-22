<?php

namespace App\Http\Controllers;

use App\Models\Chat;
use App\Models\Faq;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function adminDashboard()
    {
        $totalChats = Chat::count();
        $totalFaqs = Faq::count();
        $totalUsers = \App\Models\User::where('role', 'user')->count();

        return view('admin-dashboard', compact('totalChats', 'totalFaqs', 'totalUsers'));
    }
}
