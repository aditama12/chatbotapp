@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900">
                <div class="mb-6">
                    <h1 class="text-3xl font-bold text-gray-800">Dashboard Admin</h1>
                    <p class="text-gray-500 mt-1">Kelola chatbot dan knowledge base</p>
                </div>

                <!-- Stats Grid -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                    <div class="bg-gradient-to-br from-blue-50 to-blue-100 p-6 rounded-lg border border-blue-200">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-blue-600 text-sm font-medium">Total Chat</p>
                                <p class="text-3xl font-bold text-blue-900">{{ $totalChats }}</p>
                            </div>
                            <div class="text-4xl text-blue-300">💬</div>
                        </div>
                    </div>

                    <div class="bg-gradient-to-br from-green-50 to-green-100 p-6 rounded-lg border border-green-200">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-green-600 text-sm font-medium">Total FAQ</p>
                                <p class="text-3xl font-bold text-green-900">{{ $totalFaqs }}</p>
                            </div>
                            <div class="text-4xl text-green-300">📚</div>
                        </div>
                    </div>

                    <div class="bg-gradient-to-br from-purple-50 to-purple-100 p-6 rounded-lg border border-purple-200">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-purple-600 text-sm font-medium">Total User</p>
                                <p class="text-3xl font-bold text-purple-900">{{ $totalUsers }}</p>
                            </div>
                            <div class="text-4xl text-purple-300">👥</div>
                        </div>
                    </div>
                </div>

                <!-- Admin Menu -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <a href="{{ route('faq.index') }}" class="block p-6 bg-white border border-gray-200 rounded-lg hover:shadow-lg transition-shadow">
                        <div class="flex items-center gap-4">
                            <div class="text-3xl">📖</div>
                            <div>
                                <h3 class="font-semibold text-gray-800">Kelola FAQ</h3>
                                <p class="text-gray-500 text-sm">Tambah, edit, atau hapus FAQ</p>
                            </div>
                        </div>
                    </a>

                    <a href="{{ route('profile.edit') }}" class="block p-6 bg-white border border-gray-200 rounded-lg hover:shadow-lg transition-shadow">
                        <div class="flex items-center gap-4">
                            <div class="text-3xl">⚙️</div>
                            <div>
                                <h3 class="font-semibold text-gray-800">Profil</h3>
                                <p class="text-gray-500 text-sm">Edit profil dan pengaturan</p>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
