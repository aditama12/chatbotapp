<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Faq;

class FaqController extends Controller
{
    // Menampilkan halaman Frontend Kelola FAQ
    public function index()
    {
        $faqs = Faq::orderBy('created_at', 'desc')->get();
        return view('faq', compact('faqs'));
    }

    // Menyimpan data FAQ baru ke Database
    public function store(Request $request)
    {
        $request->validate([
            'question' => 'required|string|max:255',
            'answer' => 'required|string', 
        ]);

        Faq::create($request->only(['question', 'answer']));
        return redirect()->route('faq.index')->with('success', 'Jawaban Bot berhasil ditambahkan!');
    }

    // Memperbarui data FAQ yang sudah ada
    public function update(Request $request, $id)
    {
        $request->validate([
            'question' => 'required|string|max:255',
            'answer' => 'required|string',
        ]);

        $faq = Faq::findOrFail($id);
        $faq->update($request->only(['question', 'answer']));
        return redirect()->route('faq.index')->with('success', 'Jawaban Bot berhasil diperbarui!');
    }

    // Menghapus data FAQ
    public function destroy($id)
    {
        $faq = Faq::findOrFail($id);
        $faq->delete();
        return redirect()->route('faq.index')->with('success', 'Jawaban Bot berhasil dihapus!');
    }
}
