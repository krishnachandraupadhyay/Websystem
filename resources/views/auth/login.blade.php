<x-guest-layout>
    <div class="glass-card w-full max-w-[500px] sm:max-w-[520px] rounded-[34px] p-8 sm:p-11 relative my-auto">
        <!-- Logo Emblem -->
        <div class="flex justify-center mb-4 sm:mb-5">
            <div class="relative w-20 h-20 sm:w-24 sm:h-24 flex items-center justify-center drop-shadow-sm">
                <svg class="w-full h-full" viewBox="0 0 100 100" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <!-- Outer Hexagon -->
                    <polygon points="50,5 91,27 91,73 50,95 9,73 9,27" stroke="#1c3a62" stroke-width="5" stroke-linejoin="round" fill="none"/>
                    
                    <!-- Globe Grid Lines -->
                    <circle cx="50" cy="50" r="28" stroke="#1c3a62" stroke-width="3" fill="none" opacity="0.95"/>
                    <ellipse cx="50" cy="50" rx="14" ry="28" stroke="#1c3a62" stroke-width="2.5" fill="none" opacity="0.85"/>
                    <line x1="22" y1="50" x2="78" y2="50" stroke="#1c3a62" stroke-width="2.5" opacity="0.85"/>
                    <line x1="26" y1="36" x2="74" y2="36" stroke="#1c3a62" stroke-width="2" stroke-dasharray="2 3" opacity="0.6"/>
                    <line x1="26" y1="64" x2="74" y2="64" stroke="#1c3a62" stroke-width="2" stroke-dasharray="2 3" opacity="0.6"/>
                    
                    <!-- Tech Cyber Compass / Triangle -->
                    <path d="M50 28 L66 69 L50 57 L34 69 Z" stroke="#1c3a62" stroke-width="3.5" stroke-linejoin="round" fill="#1c3a62" fill-opacity="0.18"/>
                    <circle cx="50" cy="50" r="3.5" fill="#1c3a62"/>
                </svg>
            </div>
        </div>

        <!-- Heading -->
        <h1 class="text-center text-xl sm:text-2xl font-extrabold tracking-[0.14em] text-[#1c3a62] mb-5 sm:mb-6 uppercase">
            {{ __('WELCOME BACK') }}
        </h1>

        <!-- Session Status -->
        <x-auth-session-status class="mb-4" :status="session('status')" />

        <form method="POST" action="{{ route('login') }}" class="space-y-4 sm:space-y-5" x-data="{ showPassword: false }">
            @csrf

            <!-- Email Address Field with Clean Floating Notch -->
            <div class="relative pt-1.5">
                <div class="glass-field-container relative rounded-[20px] px-4.5 py-3 sm:py-3.5">
                    <label for="email" class="absolute -top-2.5 left-4.5 bg-white/80 backdrop-blur-md px-2 py-0.5 text-xs font-semibold text-slate-700 rounded-md tracking-wider shadow-xs">
                        Email
                    </label>
                    <div class="flex items-center gap-3">
                        <!-- Mail Icon -->
                        <svg class="w-5 h-5 text-slate-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                        <input 
                            id="email" 
                            type="email" 
                            name="email" 
                            value="{{ old('email') }}" 
                            required 
                            autofocus 
                            autocomplete="username" 
                            placeholder="Email Address"
                            class="glass-input w-full text-slate-900 placeholder:text-slate-500 font-medium text-[15px] leading-relaxed" 
                        />
                    </div>
                </div>
                <x-input-error :messages="$errors->get('email')" class="mt-1 text-xs text-rose-600 font-semibold ml-2" />
            </div>

            <!-- Password Field with Clean Floating Notch & Toggle -->
            <div class="relative pt-1.5">
                <div class="glass-field-container relative rounded-[20px] px-4.5 py-3 sm:py-3.5">
                    <label for="password" class="absolute -top-2.5 left-4.5 bg-white/80 backdrop-blur-md px-2 py-0.5 text-xs font-semibold text-slate-700 rounded-md tracking-wider shadow-xs">
                        Password
                    </label>
                    <div class="flex items-center gap-3">
                        <!-- Lock Icon -->
                        <svg class="w-5 h-5 text-slate-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                        </svg>
                        <input 
                            id="password" 
                            :type="showPassword ? 'text' : 'password'" 
                            name="password" 
                            required 
                            autocomplete="current-password" 
                            placeholder="Password"
                            class="glass-input w-full text-slate-900 placeholder:text-slate-500 font-medium text-[15px] leading-relaxed" 
                        />
                        <!-- Show / Hide Password Eye Icon -->
                        <button type="button" @click="showPassword = !showPassword" class="text-slate-500 hover:text-slate-800 focus:outline-none transition-colors p-1" title="Toggle password visibility">
                            <svg x-show="!showPassword" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18" />
                            </svg>
                            <svg x-show="showPassword" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="display: none;">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                        </button>
                    </div>
                </div>
                <x-input-error :messages="$errors->get('password')" class="mt-1 text-xs text-rose-600 font-semibold ml-2" />
            </div>

            <!-- Remember Me & Forgot Password -->
            <div class="flex items-center justify-between pt-1">
                <label for="remember_me" class="inline-flex items-center cursor-pointer select-none group">
                    <input 
                        id="remember_me" 
                        type="checkbox" 
                        name="remember" 
                        class="w-4 h-4 rounded border-slate-400 text-blue-600 focus:ring-blue-500/40 bg-white/40 cursor-pointer transition-all"
                    >
                    <span class="ms-2.5 text-sm sm:text-[15px] font-medium text-slate-700 group-hover:text-slate-900 transition-colors">
                        {{ __('Remember Me') }}
                    </span>
                </label>

                @if (Route::has('password.request'))
                    <a class="text-sm sm:text-[15px] font-semibold text-[#1d72f2] hover:text-blue-700 hover:underline focus:outline-none transition-colors" href="{{ route('password.request') }}">
                        {{ __('Forgot Password?') }}
                    </a>
                @endif
            </div>

            <!-- Sign In Button -->
            <div class="pt-2">
                <button 
                    type="submit" 
                    class="w-full py-3.5 sm:py-4 px-6 rounded-2xl bg-[#1d72f2] hover:bg-[#1562d9] active:scale-[0.99] text-white font-bold text-base sm:text-lg shadow-xl shadow-blue-600/35 hover:shadow-blue-600/50 transition-all duration-200 flex items-center justify-center tracking-wide"
                >
                    {{ __('Sign In') }}
                </button>
            </div>
        </form>
    </div>
</x-guest-layout>
