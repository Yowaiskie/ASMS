<div class="h-[80vh] flex flex-col items-center justify-center text-center animate-fade-in-up">
    <div class="w-32 h-32 bg-red-50 text-red-500 rounded-full flex items-center justify-center mb-8 shadow-xl shadow-red-100 ring-8 ring-red-50">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
        </svg>
    </div>
    
    <h1 class="text-6xl font-black text-slate-800 mb-2">403</h1>
    <h2 class="text-2xl font-bold text-slate-700 mb-4">Access Forbidden</h2>
    
    <p class="text-slate-500 max-w-md mx-auto mb-8 leading-relaxed">
        Oops! You don't have permission to access this page. This area is restricted to authorized personnel only.
    </p>
    
    <div class="flex gap-3 justify-center">
        <a href="<?= URLROOT ?>/dashboard" class="px-8 py-3 bg-slate-800 text-white font-bold rounded-2xl hover:bg-slate-900 transition-all shadow-lg shadow-slate-200">
            Back to Dashboard
        </a>
        <button onclick="history.back()" class="px-8 py-3 bg-white border border-slate-200 text-slate-600 font-bold rounded-2xl hover:bg-slate-50 transition-all shadow-sm">
            Go Back
        </button>
    </div>
</div>