<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="ज्ञान विकास स्कूल - Gyan Vikas School - Inspiring Minds, Building Futures. Admissions Open 2024-25.">
    <title>ज्ञान विकास स्कूल | GYAN VIKAS SCHOOL</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Devanagari:wght@400;600;700;800;900&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Outfit:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        navy: {
                            800: '#142c4b',
                            900: '#0e233a',
                            950: '#091829',
                        },
                        school: {
                            gold: '#f59e0b',
                            yellow: '#fbb500',
                            teal: '#0d9488',
                            emerald: '#059669',
                            blue: '#1e40af',
                        }
                    },
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'sans-serif'],
                        devanagari: ['"Noto Sans Devanagari"', 'sans-serif'],
                        heading: ['"Outfit"', 'sans-serif'],
                    }
                }
            }
        }
    </script>
    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }
        .font-hindi {
            font-family: 'Noto Sans Devanagari', sans-serif;
        }
        .font-heading {
            font-family: 'Outfit', sans-serif;
        }
        .hero-gradient-overlay {
            background: linear-gradient(180deg, rgba(8, 23, 40, 0.45) 0%, rgba(8, 23, 40, 0.75) 70%, rgba(8, 23, 40, 0.92) 100%);
        }
        .custom-scrollbar::-webkit-scrollbar {
            width: 4px;
            height: 4px;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb {
            background-color: #cbd5e1;
            border-radius: 9999px;
        }
    </style>
</head>
<body class="bg-[#eef2f6] text-slate-800 antialiased" x-data="{ mobileMenuOpen: false, searchOpen: false }">

    <!-- TOP HEADER / NAVBAR -->
    <header class="sticky top-0 z-50 bg-[#0e233a] border-b border-[#1b3b5f] shadow-md">
        <div class="max-w-[1440px] mx-auto px-3 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16 sm:h-20">
                
                <!-- School Brand / Logo -->
                <a href="{{ url('/') }}" class="flex items-center gap-3 group shrink-0">
                    @if(!empty($headerData['logo']) && file_exists(public_path($headerData['logo'])))
                        <img src="{{ asset($headerData['logo']) }}" alt="{{ $headerData['heading'] ?? 'Logo' }}" class="w-11 h-11 sm:w-13 sm:h-13 object-contain rounded-md shadow-xs">
                    @else
                        <!-- Crest / Shield Logo Fallback -->
                        <div class="relative w-11 h-12 sm:w-13 sm:h-14 rounded-md bg-gradient-to-b from-blue-700 via-blue-900 to-indigo-950 border border-amber-400/80 shadow-md flex flex-col items-center justify-center p-1 overflow-hidden">
                            <div class="absolute inset-0 bg-amber-400/10 pointer-events-none"></div>
                            <!-- Shield Emblem SVG -->
                            <svg class="w-6 h-6 text-amber-400" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M12 2L4 5v6.09c0 5.05 3.41 9.76 8 10.91 4.59-1.15 8-5.86 8-10.91V5l-8-3zm0 2.18l6 2.25v4.66c0 4.1-2.67 7.9-6 8.91-3.33-1.01-6-4.81-6-8.91V6.43l6-2.25zM12 6.5a2.5 2.5 0 00-2.5 2.5c0 1.05.65 1.95 1.57 2.31L10 14h4l-1.07-2.69c.92-.36 1.57-1.26 1.57-2.31A2.5 2.5 0 0012 6.5z"/>
                            </svg>
                            <span class="text-[8px] font-black text-amber-300 tracking-widest uppercase mt-0.5">GVS</span>
                        </div>
                    @endif

                    <!-- Names in Hindi & English -->
                    <div class="flex flex-col justify-center">
                        @if(!empty($headerData['subheading']) || !empty($headerData['heading']))
                            @if(!empty($headerData['subheading']))
                                <span class="font-hindi text-amber-400 font-extrabold text-sm sm:text-base leading-tight tracking-wide drop-shadow-xs">{{ $headerData['subheading'] }}</span>
                            @endif
                            <span class="text-white font-extrabold text-xs sm:text-sm tracking-wider uppercase leading-tight font-heading">{{ $headerData['heading'] ?? 'GYAN VIKAS SCHOOL' }}</span>
                        @else
                            <span class="font-hindi text-amber-400 font-extrabold text-sm sm:text-base leading-tight tracking-wide drop-shadow-xs">ज्ञान विकास स्कूल</span>
                            <span class="text-white font-extrabold text-xs sm:text-sm tracking-wider uppercase leading-tight font-heading">GYAN VIKAS SCHOOL</span>
                        @endif
                    </div>
                </a>

                <!-- Desktop Navigation Links -->
                <nav class="hidden xl:flex items-center gap-1 2xl:gap-3 text-xs sm:text-[13px] font-semibold text-slate-200">
                    @if(!empty($headerData['nav_links']))
                        @foreach($headerData['nav_links'] as $i => $link)
                            <a 
                                href="{{ $link['url'] }}" 
                                target="{{ $link['target'] ?? '_self' }}"
                                title="{{ $link['title'] ?? '' }}"
                                class="px-2.5 py-1.5 transition-colors @if($i === 0) text-amber-400 font-bold border-b-2 border-amber-400 @else hover:text-white hover:text-amber-300 @endif"
                            >{{ $link['text'] }}</a>
                        @endforeach
                    @else
                        <a href="{{ url('/') }}" class="px-2.5 py-1.5 text-amber-400 font-bold border-b-2 border-amber-400 transition-colors">Home</a>
                        <a href="#about" class="px-2.5 py-1.5 hover:text-white hover:text-amber-300 transition-colors">About Us</a>
                        <a href="#academics" class="px-2.5 py-1.5 hover:text-white hover:text-amber-300 transition-colors">Academics</a>
                        <a href="#admissions" class="px-2.5 py-1.5 hover:text-white hover:text-amber-300 transition-colors">Admissions</a>
                        <a href="#placement" class="px-2.5 py-1.5 hover:text-white hover:text-amber-300 transition-colors">Placement</a>
                        <a href="#gallery" class="px-2.5 py-1.5 hover:text-white hover:text-amber-300 transition-colors">Gallery</a>
                        <a href="#events" class="px-2.5 py-1.5 hover:text-white hover:text-amber-300 transition-colors">Events</a>
                        <a href="#notices" class="px-2.5 py-1.5 hover:text-white hover:text-amber-300 transition-colors">Notice Board</a>
                        <a href="#contact" class="px-2.5 py-1.5 hover:text-white hover:text-amber-300 transition-colors">Contact Us</a>
                    @endif
                </nav>

                <!-- Right Action Buttons -->
                <div class="flex items-center gap-2 sm:gap-3">
                    @if(!empty($headerData['buttons']))
                        @foreach($headerData['buttons'] as $btn)
                            <a 
                                href="{{ $btn['url'] }}" 
                                target="{{ $btn['target'] ?? '_self' }}"
                                class="inline-flex items-center gap-1.5 bg-[#fbb500] hover:bg-[#e6a500] text-slate-950 font-black text-xs sm:text-xs px-3 sm:px-4 py-2 rounded-lg uppercase tracking-wider shadow-sm hover:shadow-md transition-all duration-200 transform hover:-translate-y-0.5"
                            >
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                                <span>{{ $btn['text'] }}</span>
                            </a>
                        @endforeach
                    @else
                        <!-- PARENT LOGIN BUTTON Fallback -->
                        <a href="{{ route('login') }}" class="inline-flex items-center gap-1.5 bg-[#fbb500] hover:bg-[#e6a500] text-slate-950 font-black text-xs sm:text-xs px-3 sm:px-4 py-2 rounded-lg uppercase tracking-wider shadow-sm hover:shadow-md transition-all duration-200 transform hover:-translate-y-0.5">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                            <span>PARENT LOGIN</span>
                        </a>
                    @endif

                    <!-- Search Button -->
                    <button @click="searchOpen = !searchOpen" class="w-8 h-8 sm:w-9 sm:h-9 rounded-lg bg-slate-800/80 hover:bg-slate-700 text-slate-300 hover:text-white flex items-center justify-center transition-colors cursor-pointer" title="Search">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </button>

                    <!-- Mobile Menu Hamburger -->
                    <button @click="mobileMenuOpen = !mobileMenuOpen" class="xl:hidden p-2 text-slate-300 hover:text-white rounded-lg focus:outline-none">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path x-show="!mobileMenuOpen" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                            <path x-show="mobileMenuOpen" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Search Dropdown Box -->
            <div x-show="searchOpen" x-transition class="py-3 px-2 border-t border-slate-700/60 flex items-center gap-2">
                <input type="text" placeholder="Search notices, faculty, curriculum, admissions..." class="w-full bg-slate-900 text-white placeholder-slate-400 text-xs sm:text-sm px-4 py-2 rounded-lg border border-slate-700 focus:outline-none focus:border-amber-400">
                <button class="bg-amber-400 text-slate-900 px-4 py-2 rounded-lg text-xs font-bold uppercase shrink-0">Search</button>
            </div>
        </div>

        <!-- Mobile Navigation Menu -->
        <div x-show="mobileMenuOpen" x-transition class="xl:hidden bg-[#0a1b2d] border-t border-slate-800 px-4 pt-2 pb-4 space-y-1 text-sm font-semibold">
            @if(!empty($headerData['nav_links']))
                @foreach($headerData['nav_links'] as $i => $link)
                    <a 
                        href="{{ $link['url'] }}" 
                        target="{{ $link['target'] ?? '_self' }}"
                        @click="mobileMenuOpen = false" 
                        class="block px-3 py-2 rounded-md @if($i === 0) text-amber-400 bg-slate-800/60 font-bold @else text-slate-200 hover:bg-slate-800 @endif"
                    >{{ $link['text'] }}</a>
                @endforeach
            @else
                <a href="{{ url('/') }}" class="block px-3 py-2 rounded-md text-amber-400 bg-slate-800/60 font-bold">Home</a>
                <a href="#about" @click="mobileMenuOpen = false" class="block px-3 py-2 rounded-md text-slate-200 hover:bg-slate-800">About Us</a>
                <a href="#academics" @click="mobileMenuOpen = false" class="block px-3 py-2 rounded-md text-slate-200 hover:bg-slate-800">Academics</a>
                <a href="#admissions" @click="mobileMenuOpen = false" class="block px-3 py-2 rounded-md text-slate-200 hover:bg-slate-800">Admissions</a>
                <a href="#placement" @click="mobileMenuOpen = false" class="block px-3 py-2 rounded-md text-slate-200 hover:bg-slate-800">Placement</a>
                <a href="#gallery" @click="mobileMenuOpen = false" class="block px-3 py-2 rounded-md text-slate-200 hover:bg-slate-800">Gallery</a>
                <a href="#events" @click="mobileMenuOpen = false" class="block px-3 py-2 rounded-md text-slate-200 hover:bg-slate-800">Events</a>
                <a href="#notices" @click="mobileMenuOpen = false" class="block px-3 py-2 rounded-md text-slate-200 hover:bg-slate-800">Notice Board</a>
                <a href="#contact" @click="mobileMenuOpen = false" class="block px-3 py-2 rounded-md text-slate-200 hover:bg-slate-800">Contact Us</a>
            @endif
        </div>
    </header>


    <!-- HERO CAROUSEL SECTION -->
    <section 
        class="max-w-[1440px] mx-auto p-3 sm:p-5 lg:p-6"
        x-data="{
            active: 0,
            slides: [
                {
                    image: 'https://images.unsplash.com/photo-1577896851231-70ef18881754?auto=format&fit=crop&w=1600&q=80',
                    welcome: 'WELCOME TO',
                    title: 'GYAN VIKAS SCHOOL',
                    hindiTitle: 'ज्ञान विकास स्कूल में आपका स्वागत है',
                    tagline: 'Inspiring Minds, Building Futures',
                    hindiTagline: '“प्रेरित मन, उज्ज्वल भविष्य”',
                    badgeText: 'ADMISSIONS OPEN 2024-25',
                    badgeHindi: 'प्रवेश 2024-25 प्रारंभ',
                    badgeLink: '#admissions'
                },
                {
                    image: 'https://images.unsplash.com/photo-1509062522246-3755977927d7?auto=format&fit=crop&w=1600&q=80',
                    welcome: 'CENTRE OF EXCELLENCE',
                    title: 'NURTURING FUTURE INNOVATORS',
                    hindiTitle: 'ज्ञान विकास स्कूल - आधुनिक व संस्कारी शिक्षा',
                    tagline: 'Modern Digital Smart Classrooms & High-Tech Science Labs',
                    hindiTagline: '“आधुनिक स्मार्ट कक्षाएं एवं विज्ञान प्रयोगशालाएं”',
                    badgeText: 'EXPLORE ACADEMICS',
                    badgeHindi: 'शिक्षा व्यवस्था जानें',
                    badgeLink: '#academics'
                },
                {
                    image: 'https://images.unsplash.com/photo-1523240795612-9a054b0db644?auto=format&fit=crop&w=1600&q=80',
                    welcome: 'HOLISTIC ENVIRONMENT',
                    title: 'SPORTS, CULTURE & INNOVATION',
                    hindiTitle: 'खेल, संस्कृति एवं समग्र व्यक्तित्व विकास',
                    tagline: 'Robotics Hub, Cultural Drama & Championship Athletic Grounds',
                    hindiTagline: '“रोबोटिक्स, रंगमंच एवं खेलकूद में अग्रणी”',
                    badgeText: 'CAMPUS LIFE & CLUBS',
                    badgeHindi: 'क्लब्स एवं खेल गतिविधियां',
                    badgeLink: '#gallery'
                },
                {
                    image: 'https://images.unsplash.com/photo-1427504494785-3a9ca7044f45?auto=format&fit=crop&w=1600&q=80',
                    welcome: 'PROUD HERITAGE',
                    title: 'OUTSTANDING RESULTS & PLACEMENTS',
                    hindiTitle: '100% बोर्ड परिणाम एवं प्रतिष्ठित संस्थाओं में चयन',
                    tagline: 'Nurturing Moral Character, Academic Distinction & Lifelong Leadership',
                    hindiTagline: '“संस्कार, अनुशासन एवं उत्कृष्ट सफलता का पर्याय”',
                    badgeText: 'VIEW PLACEMENTS',
                    badgeHindi: 'प्लेसमेंट देखें',
                    badgeLink: '#placement'
                }
            ],
            timer: null,
            startAutoPlay() {
                this.timer = setInterval(() => {
                    this.next();
                }, 5000);
            },
            stopAutoPlay() {
                clearInterval(this.timer);
            },
            next() {
                this.active = (this.active + 1) % this.slides.length;
            },
            prev() {
                this.active = (this.active - 1 + this.slides.length) % this.slides.length;
            },
            goTo(i) {
                this.active = i;
            }
        }"
        x-init="startAutoPlay()"
        @mouseenter="stopAutoPlay()"
        @mouseleave="startAutoPlay()"
    >
        <div class="relative rounded-2xl overflow-hidden shadow-2xl border border-slate-700/40 min-h-[350px] sm:min-h-[420px] lg:min-h-[460px] bg-slate-950 flex items-center justify-center">
            
            <!-- Slide Counter Badge -->
            <div class="absolute top-4 right-4 z-20 px-3 py-1 rounded-full bg-slate-950/70 backdrop-blur-md border border-white/20 text-white text-[11px] font-bold tracking-wider font-heading flex items-center gap-1.5 shadow-md">
                <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                <span x-text="(active + 1)">1</span>
                <span class="text-slate-400">/</span>
                <span class="text-slate-400" x-text="slides.length">4</span>
            </div>

            <!-- Slides Loop -->
            <template x-for="(slide, index) in slides" :key="index">
                <div 
                    x-show="active === index"
                    x-transition:enter="transition-all ease-out duration-700"
                    x-transition:enter-start="opacity-0 scale-95"
                    x-transition:enter-end="opacity-100 scale-100"
                    x-transition:leave="transition-all ease-in duration-500 absolute inset-0"
                    x-transition:leave-start="opacity-100 scale-100"
                    x-transition:leave-end="opacity-0 scale-105"
                    class="absolute inset-0 w-full h-full flex items-center justify-center p-6 text-center select-none"
                >
                    <!-- Background Image -->
                    <img 
                        :src="slide.image" 
                        :alt="slide.title" 
                        class="absolute inset-0 w-full h-full object-cover transform hover:scale-105 transition-transform duration-1000"
                    />
                    <!-- Gradient Overlay -->
                    <div class="absolute inset-0 bg-gradient-to-t from-slate-950/95 via-slate-900/70 to-slate-900/45"></div>

                    <!-- Slide Content -->
                    <div class="relative z-10 max-w-2xl mx-auto flex flex-col items-center">
                        <span class="text-xs sm:text-sm font-black text-amber-300 tracking-widest uppercase mb-1 drop-shadow-sm font-heading" x-text="slide.welcome"></span>
                        <h1 class="text-2xl sm:text-3xl lg:text-4xl 2xl:text-5xl font-extrabold text-white tracking-wide uppercase font-heading drop-shadow-md leading-tight" x-text="slide.title"></h1>
                        <h2 class="font-hindi text-base sm:text-xl lg:text-2xl font-bold text-amber-200 mt-1.5 drop-shadow-sm" x-text="slide.hindiTitle"></h2>
                        
                        <p class="text-xs sm:text-sm text-slate-200 mt-2.5 font-medium tracking-wide max-w-xl drop-shadow-xs" x-text="slide.tagline"></p>
                        <p class="font-hindi text-xs sm:text-sm text-slate-300 italic font-medium mt-0.5" x-text="slide.hindiTagline"></p>

                        <!-- Admissions Pill Button -->
                        <div class="mt-6">
                            <a :href="slide.badgeLink" class="inline-flex items-center gap-2 bg-gradient-to-r from-emerald-500 to-teal-600 hover:from-emerald-600 hover:to-teal-700 text-white font-extrabold text-xs sm:text-sm px-6 py-2.5 rounded-full shadow-lg border border-emerald-300/40 transform hover:scale-105 transition-all">
                                <span class="w-2 h-2 rounded-full bg-white animate-ping"></span>
                                <span class="uppercase" x-text="slide.badgeText"></span>
                                <span class="font-hindi text-[11px] sm:text-xs font-semibold pl-1.5 border-l border-emerald-300/40" x-text="slide.badgeHindi"></span>
                            </a>
                        </div>
                    </div>
                </div>
            </template>

            <!-- Navigation Arrow Left -->
            <button 
                type="button" 
                @click="prev()" 
                class="absolute left-3 sm:left-5 top-1/2 -translate-y-1/2 z-20 w-10 h-10 sm:w-12 sm:h-12 rounded-full bg-slate-900/60 hover:bg-amber-400 text-white hover:text-slate-950 backdrop-blur-md border border-white/20 hover:border-amber-400 flex items-center justify-center shadow-lg transition-all duration-200 transform hover:scale-110 cursor-pointer"
                title="Previous Slide"
            >
                <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/></svg>
            </button>

            <!-- Navigation Arrow Right -->
            <button 
                type="button" 
                @click="next()" 
                class="absolute right-3 sm:right-5 top-1/2 -translate-y-1/2 z-20 w-10 h-10 sm:w-12 sm:h-12 rounded-full bg-slate-900/60 hover:bg-amber-400 text-white hover:text-slate-950 backdrop-blur-md border border-white/20 hover:border-amber-400 flex items-center justify-center shadow-lg transition-all duration-200 transform hover:scale-110 cursor-pointer"
                title="Next Slide"
            >
                <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
            </button>

            <!-- Bottom Indicator Dots / Pills -->
            <div class="absolute bottom-4 inset-x-0 z-20 flex items-center justify-center gap-2">
                <template x-for="(slide, index) in slides" :key="index">
                    <button 
                        type="button"
                        @click="goTo(index)" 
                        :class="active === index ? 'w-8 bg-amber-400 shadow-md' : 'w-2.5 bg-white/50 hover:bg-white/80'"
                        class="h-2.5 rounded-full transition-all duration-300 cursor-pointer"
                        :title="'Go to slide ' + (index + 1)"
                    ></button>
                </template>
            </div>

        </div>
    </section>


    <!-- ==================================================== -->
    <!-- FULL SCREEN / FULL WIDTH ABOUT US SECTION -->
    <!-- ==================================================== -->
    <section id="about" class="w-full bg-white border-y border-slate-200/90 py-12 sm:py-16 my-6 shadow-xs">
        <div class="max-w-[1440px] mx-auto px-4 sm:px-6 lg:px-8">
            
            <!-- Section Header -->
            <div class="text-center max-w-3xl mx-auto mb-10">
                <div class="inline-flex items-center gap-2 bg-blue-50 border border-blue-200 text-blue-800 text-xs font-bold px-3.5 py-1.5 rounded-full mb-3 shadow-2xs">
                    <span class="w-2 h-2 rounded-full bg-blue-600"></span>
                    <span class="font-hindi">संस्कारयुक्त आधुनिक शिक्षा</span>
                    <span>•</span>
                    <span class="tracking-wider">25+ YEARS OF EXCELLENCE</span>
                </div>
                <h2 class="text-2xl sm:text-3xl lg:text-4xl font-extrabold text-slate-900 tracking-tight font-heading uppercase">
                    ABOUT GYAN VIKAS SCHOOL
                </h2>
                <h3 class="font-hindi text-base sm:text-xl font-bold text-amber-600 mt-1">
                    ज्ञान विकास स्कूल — प्रेरित मन, सर्वांगीण विकास एवं उज्ज्वल भविष्य
                </h3>
                <p class="text-slate-600 text-xs sm:text-sm mt-3 leading-relaxed">
                    Welcome to Gyan Vikas School. Located in New Delhi, our campus is an inspiring hub of intellectual discovery, academic distinction, and character building where traditional Indian values merge seamlessly with modern international pedagogy.
                </p>
            </div>

            <!-- Two-Column Grid: Left Story & Stats | Right 9-Pillars Grid -->
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-center">
                
                <!-- Left Column: Story, Image Preview & Key Stats (lg:col-span-5) -->
                <div class="lg:col-span-5 space-y-6">
                    <div class="relative rounded-2xl overflow-hidden shadow-lg border border-slate-200 group">
                        <img 
                            src="https://images.unsplash.com/photo-1580582932707-520aed937b7b?auto=format&fit=crop&w=800&q=80" 
                            alt="Gyan Vikas School Campus" 
                            class="w-full h-56 sm:h-64 object-cover transform group-hover:scale-105 transition-transform duration-700"
                        />
                        <div class="absolute inset-0 bg-gradient-to-t from-slate-950/85 via-slate-950/40 to-transparent flex items-end p-5">
                            <div>
                                <span class="bg-amber-400 text-slate-950 text-[10px] font-black uppercase px-2.5 py-1 rounded-md tracking-wider">CBSE Affiliated</span>
                                <h4 class="text-white font-bold text-base sm:text-lg mt-1 font-heading">Inspiring Campus & Future-Ready Learning</h4>
                            </div>
                        </div>
                    </div>

                    <p class="text-slate-600 text-xs sm:text-sm leading-relaxed text-justify">
                        Our institution stands committed to holistic pedagogy, academic distinction, and state-of-the-art facilities that empower students to discover their potential through innovative teaching, character development, and future-ready education. From STEM robotics to athletic arenas, we nurture tomorrow's leaders today.
                    </p>

                    <!-- 4 Metric Cards -->
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 text-center">
                        <div class="bg-slate-50 border border-slate-200/90 rounded-xl p-3 shadow-2xs hover:border-blue-300 transition-colors">
                            <span class="block text-xl sm:text-2xl font-black text-blue-700 font-heading">25+</span>
                            <span class="text-[11px] font-semibold text-slate-600">Years Legacy</span>
                        </div>
                        <div class="bg-slate-50 border border-slate-200/90 rounded-xl p-3 shadow-2xs hover:border-emerald-300 transition-colors">
                            <span class="block text-xl sm:text-2xl font-black text-emerald-600 font-heading">5,000+</span>
                            <span class="text-[11px] font-semibold text-slate-600">Alumni</span>
                        </div>
                        <div class="bg-slate-50 border border-slate-200/90 rounded-xl p-3 shadow-2xs hover:border-amber-300 transition-colors">
                            <span class="block text-xl sm:text-2xl font-black text-amber-500 font-heading">100%</span>
                            <span class="text-[11px] font-semibold text-slate-600">Board Pass</span>
                        </div>
                        <div class="bg-slate-50 border border-slate-200/90 rounded-xl p-3 shadow-2xs hover:border-indigo-300 transition-colors">
                            <span class="block text-xl sm:text-2xl font-black text-indigo-600 font-heading">150+</span>
                            <span class="text-[11px] font-semibold text-slate-600">Expert Faculty</span>
                        </div>
                    </div>

                    <div class="flex items-center gap-3 pt-2">
                        <a href="#academics" class="inline-flex items-center gap-2 bg-[#0070e0] hover:bg-[#005bb5] text-white text-xs sm:text-sm font-bold px-6 py-3 rounded-full shadow-sm hover:shadow-md transition-all">
                            <span>Explore Our Heritage & Mission</span>
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                        </a>
                        <a href="#admissions" class="inline-flex items-center gap-2 bg-amber-100 hover:bg-amber-200 text-amber-900 text-xs sm:text-sm font-bold px-5 py-3 rounded-full border border-amber-300 transition-all">
                            <span>Admissions 2024-25</span>
                        </a>
                    </div>
                </div>

                <!-- Right Column: 9 Circular Feature Badges (Expanded & Spacious) (lg:col-span-7) -->
                <div class="lg:col-span-7">
                    <div class="bg-slate-50/80 border border-slate-200 rounded-2xl p-5 sm:p-7 shadow-xs">
                        <div class="flex items-center justify-between border-b border-slate-200 pb-3 mb-5">
                            <h4 class="font-extrabold text-slate-900 text-sm sm:text-base uppercase tracking-wider font-heading flex items-center gap-2">
                                <span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span>
                                <span>Core Pillars of Excellence</span>
                            </h4>
                            <span class="text-xs text-slate-500 font-medium">9 Institutional Standards</span>
                        </div>

                        <!-- 3x3 Feature Grid -->
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                            
                            <!-- 1. Our Mission -->
                            <div class="flex flex-col items-center text-center p-3.5 rounded-xl bg-white border border-slate-200/80 hover:border-emerald-300 hover:shadow-md transition-all group cursor-pointer">
                                <div class="w-12 h-12 rounded-full bg-emerald-100 text-emerald-700 flex items-center justify-center shadow-xs group-hover:bg-emerald-600 group-hover:text-white transition-all">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                                </div>
                                <span class="text-xs font-bold text-slate-900 mt-2.5 leading-tight">Our Mission</span>
                                <span class="text-[11px] text-slate-500 mt-1 leading-snug">Inspiring purposeful growth & values</span>
                            </div>

                            <!-- 2. Top Academic Excellence -->
                            <div class="flex flex-col items-center text-center p-3.5 rounded-xl bg-white border border-slate-200/80 hover:border-blue-300 hover:shadow-md transition-all group cursor-pointer">
                                <div class="w-12 h-12 rounded-full bg-blue-100 text-blue-700 flex items-center justify-center shadow-xs group-hover:bg-blue-600 group-hover:text-white transition-all">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5zm0 7l-9-5 9-5 9 5-9 5z"/></svg>
                                </div>
                                <span class="text-xs font-bold text-slate-900 mt-2.5 leading-tight">Top Academic Excellence</span>
                                <span class="text-[11px] text-slate-500 mt-1 leading-snug">CBSE curriculum & board toppers</span>
                            </div>

                            <!-- 3. Holistic Development -->
                            <div class="flex flex-col items-center text-center p-3.5 rounded-xl bg-white border border-slate-200/80 hover:border-teal-300 hover:shadow-md transition-all group cursor-pointer">
                                <div class="w-12 h-12 rounded-full bg-teal-100 text-teal-700 flex items-center justify-center shadow-xs group-hover:bg-teal-600 group-hover:text-white transition-all">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/></svg>
                                </div>
                                <span class="text-xs font-bold text-slate-900 mt-2.5 leading-tight">Holistic Development</span>
                                <span class="text-[11px] text-slate-500 mt-1 leading-snug">Emotional intelligence & life skills</span>
                            </div>

                            <!-- 4. Faculty & Building -->
                            <div class="flex flex-col items-center text-center p-3.5 rounded-xl bg-white border border-slate-200/80 hover:border-indigo-300 hover:shadow-md transition-all group cursor-pointer">
                                <div class="w-12 h-12 rounded-full bg-indigo-100 text-indigo-700 flex items-center justify-center shadow-xs group-hover:bg-indigo-600 group-hover:text-white transition-all">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                                </div>
                                <span class="text-xs font-bold text-slate-900 mt-2.5 leading-tight">Faculty & Building</span>
                                <span class="text-[11px] text-slate-500 mt-1 leading-snug">Experienced mentors & green campus</span>
                            </div>

                            <!-- 5. Sports & Activities -->
                            <div class="flex flex-col items-center text-center p-3.5 rounded-xl bg-white border border-slate-200/80 hover:border-amber-300 hover:shadow-md transition-all group cursor-pointer">
                                <div class="w-12 h-12 rounded-full bg-amber-100 text-amber-700 flex items-center justify-center shadow-xs group-hover:bg-amber-600 group-hover:text-white transition-all">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                </div>
                                <span class="text-xs font-bold text-slate-900 mt-2.5 leading-tight">Sports, Activities</span>
                                <span class="text-[11px] text-slate-500 mt-1 leading-snug">Athletics arenas & championship coaching</span>
                            </div>

                            <!-- 6. Modern Infrastructure -->
                            <div class="flex flex-col items-center text-center p-3.5 rounded-xl bg-white border border-slate-200/80 hover:border-cyan-300 hover:shadow-md transition-all group cursor-pointer">
                                <div class="w-12 h-12 rounded-full bg-cyan-100 text-cyan-700 flex items-center justify-center shadow-xs group-hover:bg-cyan-600 group-hover:text-white transition-all">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                </div>
                                <span class="text-xs font-bold text-slate-900 mt-2.5 leading-tight">Modern Infrastructure</span>
                                <span class="text-[11px] text-slate-500 mt-1 leading-snug">Smart boards & high-tech robotics labs</span>
                            </div>

                            <!-- 7. Communication -->
                            <div class="flex flex-col items-center text-center p-3.5 rounded-xl bg-white border border-slate-200/80 hover:border-sky-300 hover:shadow-md transition-all group cursor-pointer">
                                <div class="w-12 h-12 rounded-full bg-sky-100 text-sky-700 flex items-center justify-center shadow-xs group-hover:bg-sky-600 group-hover:text-white transition-all">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                                </div>
                                <span class="text-xs font-bold text-slate-900 mt-2.5 leading-tight">Communication</span>
                                <span class="text-[11px] text-slate-500 mt-1 leading-snug">Bilingual mastery & public speaking</span>
                            </div>

                            <!-- 8. Learning -->
                            <div class="flex flex-col items-center text-center p-3.5 rounded-xl bg-white border border-slate-200/80 hover:border-purple-300 hover:shadow-md transition-all group cursor-pointer">
                                <div class="w-12 h-12 rounded-full bg-purple-100 text-purple-700 flex items-center justify-center shadow-xs group-hover:bg-purple-600 group-hover:text-white transition-all">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                                </div>
                                <span class="text-xs font-bold text-slate-900 mt-2.5 leading-tight">Learning</span>
                                <span class="text-[11px] text-slate-500 mt-1 leading-snug">Inquiry-driven experiential learning</span>
                            </div>

                            <!-- 9. Contact Us -->
                            <div class="flex flex-col items-center text-center p-3.5 rounded-xl bg-white border border-slate-200/80 hover:border-emerald-300 hover:shadow-md transition-all group cursor-pointer">
                                <div class="w-12 h-12 rounded-full bg-emerald-100 text-emerald-700 flex items-center justify-center shadow-xs group-hover:bg-emerald-600 group-hover:text-white transition-all">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                                </div>
                                <span class="text-xs font-bold text-slate-900 mt-2.5 leading-tight">Contact Us</span>
                                <span class="text-[11px] text-slate-500 mt-1 leading-snug">Direct parent-school partnership</span>
                            </div>

                        </div>
                    </div>
                </div>

            </div>

        </div>
    </section>


    <!-- ==================================================== -->
    <!-- FULL SCREEN / FULL WIDTH CAMPUS PLACEMENTS -->
    <!-- ==================================================== -->
    <section id="placement" class="w-full bg-[#f8fafc] border-y border-slate-200/90 py-12 sm:py-16 my-6 shadow-xs">
        <div class="max-w-[1440px] mx-auto px-4 sm:px-6 lg:px-8">
            
            <!-- Section Header -->
            <div class="text-center max-w-3xl mx-auto mb-10">
                <div class="inline-flex items-center gap-2 bg-blue-50 border border-blue-200 text-blue-900 text-xs font-bold px-3.5 py-1.5 rounded-full mb-3 shadow-2xs">
                    <span class="w-2 h-2 rounded-full bg-blue-600 animate-pulse"></span>
                    @if(!empty($placementData['subheading']))
                        <span class="font-hindi">{{ $placementData['subheading'] }}</span>
                    @else
                        <span class="font-hindi">करियर एवं कैंपस प्लेसमेंट</span>
                        <span>•</span>
                        <span class="tracking-wider uppercase">CAMPUS PLACEMENT SUCCESS</span>
                    @endif
                </div>
                <h2 class="text-2xl sm:text-3xl lg:text-4xl font-extrabold text-slate-900 tracking-tight font-heading uppercase">
                    {{ $placementData['heading'] ?? 'CAMPUS PLACEMENTS' }}
                </h2>
                <p class="text-slate-600 text-xs sm:text-sm mt-2 leading-relaxed">
                    {{ $placementData['paragraph'] ?? 'Celebrating our brilliant students who secured dream job opportunities and premier packages at leading multinational corporations.' }}
                </p>
            </div>

            <!-- Key Placement Highlight Counters -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-10">
                <div class="bg-white rounded-xl p-4 border border-slate-200 shadow-2xs text-center hover:border-emerald-300 transition-colors">
                    <span class="text-2xl sm:text-3xl font-black text-emerald-600 font-heading block">₹36.5 LPA</span>
                    <span class="text-xs font-semibold text-slate-600 mt-1 block">Highest Package Offered</span>
                </div>
                <div class="bg-white rounded-xl p-4 border border-slate-200 shadow-2xs text-center hover:border-blue-300 transition-colors">
                    <span class="text-2xl sm:text-3xl font-black text-blue-600 font-heading block">₹12.4 LPA</span>
                    <span class="text-xs font-semibold text-slate-600 mt-1 block">Average Annual Package</span>
                </div>
                <div class="bg-white rounded-xl p-4 border border-slate-200 shadow-2xs text-center hover:border-amber-300 transition-colors">
                    <span class="text-2xl sm:text-3xl font-black text-amber-500 font-heading block">450+</span>
                    <span class="text-xs font-semibold text-slate-600 mt-1 block">Total Campus Offers</span>
                </div>
                <div class="bg-white rounded-xl p-4 border border-slate-200 shadow-2xs text-center hover:border-indigo-300 transition-colors">
                    <span class="text-2xl sm:text-3xl font-black text-indigo-600 font-heading block">100+</span>
                    <span class="text-xs font-semibold text-slate-600 mt-1 block">Corporate Hiring Partners</span>
                </div>
            </div>

            <!-- Student Placement Cards Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-10">
                @if(!empty($placementData['cards']))
                    @foreach($placementData['cards'] as $card)
                        @php
                            $imgSrc = $card['image'] ?? null;
                            $hasValidImg = !empty($imgSrc) && (
                                str_starts_with($imgSrc, 'http://') || 
                                str_starts_with($imgSrc, 'https://') || 
                                file_exists(public_path($imgSrc))
                            );
                            $finalImgUrl = $hasValidImg ? ((str_starts_with($imgSrc, 'http://') || str_starts_with($imgSrc, 'https://')) ? $imgSrc : asset($imgSrc)) : null;
                        @endphp
                        <div class="bg-white rounded-2xl border border-slate-200/90 shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all duration-300 overflow-hidden flex flex-col group">
                            <div class="relative overflow-hidden">
                                @if($finalImgUrl)
                                    <img 
                                        src="{{ $finalImgUrl }}" 
                                        alt="{{ $card['name'] ?? 'Placement' }}" 
                                        class="w-full h-52 object-cover object-top group-hover:scale-105 transition-transform duration-500"
                                    />
                                @else
                                    <div class="w-full h-52 bg-gradient-to-br from-[#0e233a] to-blue-950 flex items-center justify-center text-white">
                                        <div class="w-16 h-16 rounded-full bg-white/10 flex items-center justify-center font-heading font-black text-2xl border border-white/20">
                                            {{ strtoupper(substr($card['name'] ?? 'P', 0, 1)) }}
                                        </div>
                                    </div>
                                @endif
                                <div class="absolute inset-0 bg-gradient-to-t from-slate-950/80 via-transparent to-black/30"></div>
                                
                                @if(!empty($card['company']) || !empty($card['role']))
                                    <!-- Company Badge (Top-Left) -->
                                    <div class="absolute top-3 left-3 bg-white/95 backdrop-blur-md px-2.5 py-1 rounded-full shadow-md flex items-center gap-1.5 border border-slate-200">
                                        <span class="w-2 h-2 rounded-full bg-blue-500"></span>
                                        <span class="text-[11px] font-black text-slate-900 tracking-tight font-heading">{{ $card['company'] ?: $card['role'] }}</span>
                                    </div>
                                @endif

                                @if(!empty($card['package']))
                                    <!-- Package Badge (Top-Right) -->
                                    <div class="absolute top-3 right-3 bg-gradient-to-r from-emerald-600 to-teal-600 text-white text-xs font-black px-3 py-1 rounded-full shadow-md flex items-center gap-1">
                                        <span>{{ $card['package'] }}</span>
                                    </div>
                                @endif

                                <!-- Floating Name overlay at bottom of image -->
                                <div class="absolute bottom-2.5 left-3 right-3 text-white">
                                    <h3 class="text-base font-extrabold font-heading leading-tight drop-shadow-sm">{{ $card['name'] }}</h3>
                                    @if(!empty($card['role']))
                                        <span class="text-[11px] text-amber-300 font-semibold drop-shadow-xs">{{ $card['role'] }}</span>
                                    @endif
                                </div>
                            </div>

                            <div class="p-4 flex-1 flex flex-col justify-between">
                                <div>
                                    @if(!empty($card['package']))
                                        <!-- Package Callout -->
                                        <div class="flex items-center justify-between py-2 px-3 bg-emerald-50/70 rounded-xl border border-emerald-200/70 mb-3">
                                            <span class="text-[11px] font-bold text-slate-600 uppercase tracking-wider">Offered Package</span>
                                            <span class="text-sm font-black text-emerald-700 font-heading">{{ $card['package'] }}</span>
                                        </div>
                                    @endif

                                    @if(!empty($card['description']))
                                        <p class="text-slate-600 text-xs leading-relaxed line-clamp-3 mb-4">
                                            {{ $card['description'] }}
                                        </p>
                                    @endif
                                </div>

                                <!-- Action Button -->
                                <div class="pt-2 border-t border-slate-100 mt-auto">
                                    <a 
                                        href="{{ $card['button_url'] ?? '#contact' }}" 
                                        target="{{ $card['target'] ?? '_self' }}"
                                        class="w-full py-2.5 px-4 rounded-xl bg-gradient-to-r from-[#0e233a] to-[#1b3b5f] hover:from-blue-700 hover:to-indigo-700 text-white font-bold text-xs flex items-center justify-center gap-2 shadow-xs hover:shadow-md transition-all group/btn"
                                    >
                                        <span>{{ $card['button_text'] ?? 'View Placement Story' }}</span>
                                        <svg class="w-3.5 h-3.5 group-hover/btn:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                @else
                <!-- Card 1: Aarav Sharma (Google) -->
                <div class="bg-white rounded-2xl border border-slate-200/90 shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all duration-300 overflow-hidden flex flex-col group">
                    <div class="relative overflow-hidden">
                        <img 
                            src="https://images.unsplash.com/photo-1539571696357-5a69c17a67c6?auto=format&fit=crop&w=600&q=80" 
                            alt="Aarav Sharma" 
                            class="w-full h-52 object-cover object-top group-hover:scale-105 transition-transform duration-500"
                        />
                        <div class="absolute inset-0 bg-gradient-to-t from-slate-950/80 via-transparent to-black/30"></div>
                        
                        <!-- Company Badge (Top-Left) -->
                        <div class="absolute top-3 left-3 bg-white/95 backdrop-blur-md px-2.5 py-1 rounded-full shadow-md flex items-center gap-1.5 border border-slate-200">
                            <span class="w-2 h-2 rounded-full bg-blue-500"></span>
                            <span class="text-[11px] font-black text-slate-900 tracking-tight font-heading">Google</span>
                        </div>

                        <!-- Package Badge (Top-Right) -->
                        <div class="absolute top-3 right-3 bg-gradient-to-r from-emerald-600 to-teal-600 text-white text-xs font-black px-3 py-1 rounded-full shadow-md flex items-center gap-1">
                            <span>₹36.5 LPA</span>
                        </div>

                        <!-- Floating Name overlay at bottom of image -->
                        <div class="absolute bottom-2.5 left-3 right-3 text-white">
                            <h3 class="text-base font-extrabold font-heading leading-tight drop-shadow-sm">Aarav Sharma</h3>
                            <span class="text-[11px] text-amber-300 font-semibold drop-shadow-xs">Software Engineer (SDE-1)</span>
                        </div>
                    </div>

                    <div class="p-4 flex-1 flex flex-col justify-between">
                        <div>
                            <!-- Package Callout -->
                            <div class="flex items-center justify-between py-2 px-3 bg-emerald-50/70 rounded-xl border border-emerald-200/70 mb-3">
                                <span class="text-[11px] font-bold text-slate-600 uppercase tracking-wider">Offered Package</span>
                                <span class="text-sm font-black text-emerald-700 font-heading">₹36.50 LPA</span>
                            </div>

                            <!-- Description -->
                            <p class="text-slate-600 text-xs leading-relaxed line-clamp-3 mb-4">
                                Selected at Google Core Search team. Excelled in advanced data structures, algorithmic design, and campus innovation lab projects.
                            </p>
                        </div>

                        <!-- Action Button -->
                        <div class="pt-2 border-t border-slate-100 mt-auto">
                            <a href="#contact" class="w-full py-2.5 px-4 rounded-xl bg-gradient-to-r from-[#0e233a] to-[#1b3b5f] hover:from-blue-700 hover:to-indigo-700 text-white font-bold text-xs flex items-center justify-center gap-2 shadow-xs hover:shadow-md transition-all group/btn">
                                <span>View Placement Story</span>
                                <svg class="w-3.5 h-3.5 group-hover/btn:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Card 2: Priya Patel (Microsoft) -->
                <div class="bg-white rounded-2xl border border-slate-200/90 shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all duration-300 overflow-hidden flex flex-col group">
                    <div class="relative overflow-hidden">
                        <img 
                            src="https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&w=600&q=80" 
                            alt="Priya Patel" 
                            class="w-full h-52 object-cover object-top group-hover:scale-105 transition-transform duration-500"
                        />
                        <div class="absolute inset-0 bg-gradient-to-t from-slate-950/80 via-transparent to-black/30"></div>
                        
                        <!-- Company Badge (Top-Left) -->
                        <div class="absolute top-3 left-3 bg-white/95 backdrop-blur-md px-2.5 py-1 rounded-full shadow-md flex items-center gap-1.5 border border-slate-200">
                            <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                            <span class="text-[11px] font-black text-slate-900 tracking-tight font-heading">Microsoft</span>
                        </div>

                        <!-- Package Badge (Top-Right) -->
                        <div class="absolute top-3 right-3 bg-gradient-to-r from-emerald-600 to-teal-600 text-white text-xs font-black px-3 py-1 rounded-full shadow-md flex items-center gap-1">
                            <span>₹32.0 LPA</span>
                        </div>

                        <div class="absolute bottom-2.5 left-3 right-3 text-white">
                            <h3 class="text-base font-extrabold font-heading leading-tight drop-shadow-sm">Priya Patel</h3>
                            <span class="text-[11px] text-amber-300 font-semibold drop-shadow-xs">Cloud Solutions Architect</span>
                        </div>
                    </div>

                    <div class="p-4 flex-1 flex flex-col justify-between">
                        <div>
                            <div class="flex items-center justify-between py-2 px-3 bg-emerald-50/70 rounded-xl border border-emerald-200/70 mb-3">
                                <span class="text-[11px] font-bold text-slate-600 uppercase tracking-wider">Offered Package</span>
                                <span class="text-sm font-black text-emerald-700 font-heading">₹32.00 LPA</span>
                            </div>

                            <p class="text-slate-600 text-xs leading-relaxed line-clamp-3 mb-4">
                                Recruited for the Azure Cloud Platform team. Active student leader of the Gyan Vikas Coding Club and Women-in-Tech mentorship drive.
                            </p>
                        </div>

                        <div class="pt-2 border-t border-slate-100 mt-auto">
                            <a href="#contact" class="w-full py-2.5 px-4 rounded-xl bg-gradient-to-r from-[#0e233a] to-[#1b3b5f] hover:from-blue-700 hover:to-indigo-700 text-white font-bold text-xs flex items-center justify-center gap-2 shadow-xs hover:shadow-md transition-all group/btn">
                                <span>View Placement Story</span>
                                <svg class="w-3.5 h-3.5 group-hover/btn:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Card 3: Rohan Verma (Amazon) -->
                <div class="bg-white rounded-2xl border border-slate-200/90 shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all duration-300 overflow-hidden flex flex-col group">
                    <div class="relative overflow-hidden">
                        <img 
                            src="https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?auto=format&fit=crop&w=600&q=80" 
                            alt="Rohan Verma" 
                            class="w-full h-52 object-cover object-top group-hover:scale-105 transition-transform duration-500"
                        />
                        <div class="absolute inset-0 bg-gradient-to-t from-slate-950/80 via-transparent to-black/30"></div>
                        
                        <!-- Company Badge (Top-Left) -->
                        <div class="absolute top-3 left-3 bg-white/95 backdrop-blur-md px-2.5 py-1 rounded-full shadow-md flex items-center gap-1.5 border border-slate-200">
                            <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                            <span class="text-[11px] font-black text-slate-900 tracking-tight font-heading">Amazon AWS</span>
                        </div>

                        <!-- Package Badge (Top-Right) -->
                        <div class="absolute top-3 right-3 bg-gradient-to-r from-emerald-600 to-teal-600 text-white text-xs font-black px-3 py-1 rounded-full shadow-md flex items-center gap-1">
                            <span>₹28.4 LPA</span>
                        </div>

                        <div class="absolute bottom-2.5 left-3 right-3 text-white">
                            <h3 class="text-base font-extrabold font-heading leading-tight drop-shadow-sm">Rohan Verma</h3>
                            <span class="text-[11px] text-amber-300 font-semibold drop-shadow-xs">Backend Systems Engineer</span>
                        </div>
                    </div>

                    <div class="p-4 flex-1 flex flex-col justify-between">
                        <div>
                            <div class="flex items-center justify-between py-2 px-3 bg-emerald-50/70 rounded-xl border border-emerald-200/70 mb-3">
                                <span class="text-[11px] font-bold text-slate-600 uppercase tracking-wider">Offered Package</span>
                                <span class="text-sm font-black text-emerald-700 font-heading">₹28.40 LPA</span>
                            </div>

                            <p class="text-slate-600 text-xs leading-relaxed line-clamp-3 mb-4">
                                Placed in Amazon AWS distributed computing division. Winner of Smart India Hackathon and lead developer for campus portal systems.
                            </p>
                        </div>

                        <div class="pt-2 border-t border-slate-100 mt-auto">
                            <a href="#contact" class="w-full py-2.5 px-4 rounded-xl bg-gradient-to-r from-[#0e233a] to-[#1b3b5f] hover:from-blue-700 hover:to-indigo-700 text-white font-bold text-xs flex items-center justify-center gap-2 shadow-xs hover:shadow-md transition-all group/btn">
                                <span>View Placement Story</span>
                                <svg class="w-3.5 h-3.5 group-hover/btn:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Card 4: Ananya Singh (Goldman Sachs) -->
                <div class="bg-white rounded-2xl border border-slate-200/90 shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all duration-300 overflow-hidden flex flex-col group">
                    <div class="relative overflow-hidden">
                        <img 
                            src="https://images.unsplash.com/photo-1517841905240-472988babdf9?auto=format&fit=crop&w=600&q=80" 
                            alt="Ananya Singh" 
                            class="w-full h-52 object-cover object-top group-hover:scale-105 transition-transform duration-500"
                        />
                        <div class="absolute inset-0 bg-gradient-to-t from-slate-950/80 via-transparent to-black/30"></div>
                        
                        <!-- Company Badge (Top-Left) -->
                        <div class="absolute top-3 left-3 bg-white/95 backdrop-blur-md px-2.5 py-1 rounded-full shadow-md flex items-center gap-1.5 border border-slate-200">
                            <span class="w-2 h-2 rounded-full bg-sky-500"></span>
                            <span class="text-[11px] font-black text-slate-900 tracking-tight font-heading">Goldman Sachs</span>
                        </div>

                        <!-- Package Badge (Top-Right) -->
                        <div class="absolute top-3 right-3 bg-gradient-to-r from-emerald-600 to-teal-600 text-white text-xs font-black px-3 py-1 rounded-full shadow-md flex items-center gap-1">
                            <span>₹25.0 LPA</span>
                        </div>

                        <div class="absolute bottom-2.5 left-3 right-3 text-white">
                            <h3 class="text-base font-extrabold font-heading leading-tight drop-shadow-sm">Ananya Singh</h3>
                            <span class="text-[11px] text-amber-300 font-semibold drop-shadow-xs">Quantitative Analyst</span>
                        </div>
                    </div>

                    <div class="p-4 flex-1 flex flex-col justify-between">
                        <div>
                            <div class="flex items-center justify-between py-2 px-3 bg-emerald-50/70 rounded-xl border border-emerald-200/70 mb-3">
                                <span class="text-[11px] font-bold text-slate-600 uppercase tracking-wider">Offered Package</span>
                                <span class="text-sm font-black text-emerald-700 font-heading">₹25.00 LPA</span>
                            </div>

                            <p class="text-slate-600 text-xs leading-relaxed line-clamp-3 mb-4">
                                Selected as Quantitative Financial Analyst. Specialized in machine learning models and predictive analytics under faculty supervision.
                            </p>
                        </div>

                        <div class="pt-2 border-t border-slate-100 mt-auto">
                            <a href="#contact" class="w-full py-2.5 px-4 rounded-xl bg-gradient-to-r from-[#0e233a] to-[#1b3b5f] hover:from-blue-700 hover:to-indigo-700 text-white font-bold text-xs flex items-center justify-center gap-2 shadow-xs hover:shadow-md transition-all group/btn">
                                <span>View Placement Story</span>
                                <svg class="w-3.5 h-3.5 group-hover/btn:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Card 5: Vikramaditya Roy (Adobe) -->
                <div class="bg-white rounded-2xl border border-slate-200/90 shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all duration-300 overflow-hidden flex flex-col group">
                    <div class="relative overflow-hidden">
                        <img 
                            src="https://images.unsplash.com/photo-1500648767791-00dcc994a43e?auto=format&fit=crop&w=600&q=80" 
                            alt="Vikramaditya Roy" 
                            class="w-full h-52 object-cover object-top group-hover:scale-105 transition-transform duration-500"
                        />
                        <div class="absolute inset-0 bg-gradient-to-t from-slate-950/80 via-transparent to-black/30"></div>
                        
                        <!-- Company Badge (Top-Left) -->
                        <div class="absolute top-3 left-3 bg-white/95 backdrop-blur-md px-2.5 py-1 rounded-full shadow-md flex items-center gap-1.5 border border-slate-200">
                            <span class="w-2 h-2 rounded-full bg-rose-500"></span>
                            <span class="text-[11px] font-black text-slate-900 tracking-tight font-heading">Adobe</span>
                        </div>

                        <!-- Package Badge (Top-Right) -->
                        <div class="absolute top-3 right-3 bg-gradient-to-r from-emerald-600 to-teal-600 text-white text-xs font-black px-3 py-1 rounded-full shadow-md flex items-center gap-1">
                            <span>₹22.5 LPA</span>
                        </div>

                        <div class="absolute bottom-2.5 left-3 right-3 text-white">
                            <h3 class="text-base font-extrabold font-heading leading-tight drop-shadow-sm">Vikramaditya Roy</h3>
                            <span class="text-[11px] text-amber-300 font-semibold drop-shadow-xs">Full Stack Product Engineer</span>
                        </div>
                    </div>

                    <div class="p-4 flex-1 flex flex-col justify-between">
                        <div>
                            <div class="flex items-center justify-between py-2 px-3 bg-emerald-50/70 rounded-xl border border-emerald-200/70 mb-3">
                                <span class="text-[11px] font-bold text-slate-600 uppercase tracking-wider">Offered Package</span>
                                <span class="text-sm font-black text-emerald-700 font-heading">₹22.50 LPA</span>
                            </div>

                            <p class="text-slate-600 text-xs leading-relaxed line-clamp-3 mb-4">
                                Joined Adobe Creative Suite engineering team. Trained extensively in reactive frameworks, microservices, and modern UI engineering.
                            </p>
                        </div>

                        <div class="pt-2 border-t border-slate-100 mt-auto">
                            <a href="#contact" class="w-full py-2.5 px-4 rounded-xl bg-gradient-to-r from-[#0e233a] to-[#1b3b5f] hover:from-blue-700 hover:to-indigo-700 text-white font-bold text-xs flex items-center justify-center gap-2 shadow-xs hover:shadow-md transition-all group/btn">
                                <span>View Placement Story</span>
                                <svg class="w-3.5 h-3.5 group-hover/btn:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Card 6: Neha Kulkarni (Deloitte) -->
                <div class="bg-white rounded-2xl border border-slate-200/90 shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all duration-300 overflow-hidden flex flex-col group">
                    <div class="relative overflow-hidden">
                        <img 
                            src="https://images.unsplash.com/photo-1573496359142-b8d87734a5a2?auto=format&fit=crop&w=600&q=80" 
                            alt="Neha Kulkarni" 
                            class="w-full h-52 object-cover object-top group-hover:scale-105 transition-transform duration-500"
                        />
                        <div class="absolute inset-0 bg-gradient-to-t from-slate-950/80 via-transparent to-black/30"></div>
                        
                        <!-- Company Badge (Top-Left) -->
                        <div class="absolute top-3 left-3 bg-white/95 backdrop-blur-md px-2.5 py-1 rounded-full shadow-md flex items-center gap-1.5 border border-slate-200">
                            <span class="w-2 h-2 rounded-full bg-emerald-600"></span>
                            <span class="text-[11px] font-black text-slate-900 tracking-tight font-heading">Deloitte</span>
                        </div>

                        <!-- Package Badge (Top-Right) -->
                        <div class="absolute top-3 right-3 bg-gradient-to-r from-emerald-600 to-teal-600 text-white text-xs font-black px-3 py-1 rounded-full shadow-md flex items-center gap-1">
                            <span>₹18.0 LPA</span>
                        </div>

                        <div class="absolute bottom-2.5 left-3 right-3 text-white">
                            <h3 class="text-base font-extrabold font-heading leading-tight drop-shadow-sm">Neha Kulkarni</h3>
                            <span class="text-[11px] text-amber-300 font-semibold drop-shadow-xs">Technology Consultant</span>
                        </div>
                    </div>

                    <div class="p-4 flex-1 flex flex-col justify-between">
                        <div>
                            <div class="flex items-center justify-between py-2 px-3 bg-emerald-50/70 rounded-xl border border-emerald-200/70 mb-3">
                                <span class="text-[11px] font-bold text-slate-600 uppercase tracking-wider">Offered Package</span>
                                <span class="text-sm font-black text-emerald-700 font-heading">₹18.00 LPA</span>
                            </div>

                            <p class="text-slate-600 text-xs leading-relaxed line-clamp-3 mb-4">
                                Recruited for Enterprise Strategy & IT Advisory. Led campus cyber consulting projects and represented Gyan Vikas in national case competitions.
                            </p>
                        </div>

                        <div class="pt-2 border-t border-slate-100 mt-auto">
                            <a href="#contact" class="w-full py-2.5 px-4 rounded-xl bg-gradient-to-r from-[#0e233a] to-[#1b3b5f] hover:from-blue-700 hover:to-indigo-700 text-white font-bold text-xs flex items-center justify-center gap-2 shadow-xs hover:shadow-md transition-all group/btn">
                                <span>View Placement Story</span>
                                <svg class="w-3.5 h-3.5 group-hover/btn:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Card 7: Aditya Rathore (Siemens) -->
                <div class="bg-white rounded-2xl border border-slate-200/90 shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all duration-300 overflow-hidden flex flex-col group">
                    <div class="relative overflow-hidden">
                        <img 
                            src="https://images.unsplash.com/photo-1492562080023-ab3db95bfbce?auto=format&fit=crop&w=600&q=80" 
                            alt="Aditya Rathore" 
                            class="w-full h-52 object-cover object-top group-hover:scale-105 transition-transform duration-500"
                        />
                        <div class="absolute inset-0 bg-gradient-to-t from-slate-950/80 via-transparent to-black/30"></div>
                        
                        <!-- Company Badge (Top-Left) -->
                        <div class="absolute top-3 left-3 bg-white/95 backdrop-blur-md px-2.5 py-1 rounded-full shadow-md flex items-center gap-1.5 border border-slate-200">
                            <span class="w-2 h-2 rounded-full bg-cyan-600"></span>
                            <span class="text-[11px] font-black text-slate-900 tracking-tight font-heading">Siemens</span>
                        </div>

                        <!-- Package Badge (Top-Right) -->
                        <div class="absolute top-3 right-3 bg-gradient-to-r from-emerald-600 to-teal-600 text-white text-xs font-black px-3 py-1 rounded-full shadow-md flex items-center gap-1">
                            <span>₹16.5 LPA</span>
                        </div>

                        <div class="absolute bottom-2.5 left-3 right-3 text-white">
                            <h3 class="text-base font-extrabold font-heading leading-tight drop-shadow-sm">Aditya Rathore</h3>
                            <span class="text-[11px] text-amber-300 font-semibold drop-shadow-xs">Robotics & Automation Specialist</span>
                        </div>
                    </div>

                    <div class="p-4 flex-1 flex flex-col justify-between">
                        <div>
                            <div class="flex items-center justify-between py-2 px-3 bg-emerald-50/70 rounded-xl border border-emerald-200/70 mb-3">
                                <span class="text-[11px] font-bold text-slate-600 uppercase tracking-wider">Offered Package</span>
                                <span class="text-sm font-black text-emerald-700 font-heading">₹16.50 LPA</span>
                            </div>

                            <p class="text-slate-600 text-xs leading-relaxed line-clamp-3 mb-4">
                                Selected as Automation Engineer. Built autonomous warehouse inspection robots in Gyan Vikas Robotics Lab with faculty research sponsorship.
                            </p>
                        </div>

                        <div class="pt-2 border-t border-slate-100 mt-auto">
                            <a href="#contact" class="w-full py-2.5 px-4 rounded-xl bg-gradient-to-r from-[#0e233a] to-[#1b3b5f] hover:from-blue-700 hover:to-indigo-700 text-white font-bold text-xs flex items-center justify-center gap-2 shadow-xs hover:shadow-md transition-all group/btn">
                                <span>View Placement Story</span>
                                <svg class="w-3.5 h-3.5 group-hover/btn:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Card 8: Sneha Gupta (Oracle) -->
                <div class="bg-white rounded-2xl border border-slate-200/90 shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all duration-300 overflow-hidden flex flex-col group">
                    <div class="relative overflow-hidden">
                        <img 
                            src="https://images.unsplash.com/photo-1544005313-94ddf0286df2?auto=format&fit=crop&w=600&q=80" 
                            alt="Sneha Gupta" 
                            class="w-full h-52 object-cover object-top group-hover:scale-105 transition-transform duration-500"
                        />
                        <div class="absolute inset-0 bg-gradient-to-t from-slate-950/80 via-transparent to-black/30"></div>
                        
                        <!-- Company Badge (Top-Left) -->
                        <div class="absolute top-3 left-3 bg-white/95 backdrop-blur-md px-2.5 py-1 rounded-full shadow-md flex items-center gap-1.5 border border-slate-200">
                            <span class="w-2 h-2 rounded-full bg-red-600"></span>
                            <span class="text-[11px] font-black text-slate-900 tracking-tight font-heading">Oracle</span>
                        </div>

                        <!-- Package Badge (Top-Right) -->
                        <div class="absolute top-3 right-3 bg-gradient-to-r from-emerald-600 to-teal-600 text-white text-xs font-black px-3 py-1 rounded-full shadow-md flex items-center gap-1">
                            <span>₹15.2 LPA</span>
                        </div>

                        <div class="absolute bottom-2.5 left-3 right-3 text-white">
                            <h3 class="text-base font-extrabold font-heading leading-tight drop-shadow-sm">Sneha Gupta</h3>
                            <span class="text-[11px] text-amber-300 font-semibold drop-shadow-xs">Cloud Database Specialist</span>
                        </div>
                    </div>

                    <div class="p-4 flex-1 flex flex-col justify-between">
                        <div>
                            <div class="flex items-center justify-between py-2 px-3 bg-emerald-50/70 rounded-xl border border-emerald-200/70 mb-3">
                                <span class="text-[11px] font-bold text-slate-600 uppercase tracking-wider">Offered Package</span>
                                <span class="text-sm font-black text-emerald-700 font-heading">₹15.20 LPA</span>
                            </div>

                            <p class="text-slate-600 text-xs leading-relaxed line-clamp-3 mb-4">
                                Placed as Cloud Database Engineer. Cleared industry certifications with distinction through the Gyan Vikas Corporate Training Cell.
                            </p>
                        </div>

                        <div class="pt-2 border-t border-slate-100 mt-auto">
                            <a href="#contact" class="w-full py-2.5 px-4 rounded-xl bg-gradient-to-r from-[#0e233a] to-[#1b3b5f] hover:from-blue-700 hover:to-indigo-700 text-white font-bold text-xs flex items-center justify-center gap-2 shadow-xs hover:shadow-md transition-all group/btn">
                                <span>View Placement Story</span>
                                <svg class="w-3.5 h-3.5 group-hover/btn:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                            </a>
                        </div>
                    </div>
                </div>
                @endif
            </div>

            <!-- Lower Activities & Video Highlights Strip (Full Width) -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                
                <!-- Activity 1 -->
                <div class="relative rounded-xl overflow-hidden group shadow-2xs border border-slate-200">
                    <img 
                        src="https://images.unsplash.com/photo-1485827404703-89b55fcc595e?auto=format&fit=crop&w=500&q=80" 
                        alt="Robotics Workshop" 
                        class="w-full h-28 object-cover group-hover:scale-105 transition-transform duration-300"
                    />
                    <div class="absolute inset-0 bg-gradient-to-t from-slate-950/90 via-slate-900/40 to-transparent flex items-end p-3">
                        <span class="text-xs font-bold text-white leading-tight">Robotics Workshop</span>
                    </div>
                </div>

                <!-- Activity 2 -->
                <div class="relative rounded-xl overflow-hidden group shadow-2xs border border-slate-200">
                    <img 
                        src="https://images.unsplash.com/photo-1516450360452-9312f5e86fc7?auto=format&fit=crop&w=500&q=80" 
                        alt="Drama Club Performance" 
                        class="w-full h-28 object-cover group-hover:scale-105 transition-transform duration-300"
                    />
                    <div class="absolute inset-0 bg-gradient-to-t from-slate-950/90 via-slate-900/40 to-transparent flex items-end p-3">
                        <span class="text-xs font-bold text-white leading-tight">Drama Club Performance</span>
                    </div>
                </div>

                <!-- Activity 3 (Video) -->
                <div class="relative rounded-xl overflow-hidden group shadow-2xs border border-slate-200">
                    <img 
                        src="https://images.unsplash.com/photo-1523240795612-9a054b0db644?auto=format&fit=crop&w=500&q=80" 
                        alt="Drama Video" 
                        class="w-full h-28 object-cover group-hover:scale-105 transition-transform duration-300"
                    />
                    <div class="absolute inset-0 bg-slate-950/40 flex items-center justify-center">
                        <div class="w-9 h-9 rounded-full bg-white/90 text-slate-900 flex items-center justify-center shadow-md group-hover:scale-110 transition-transform">
                            <svg class="w-4 h-4 ml-0.5" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
                        </div>
                    </div>
                    <div class="absolute inset-x-0 bottom-0 bg-gradient-to-t from-slate-950/90 to-transparent p-2 text-center">
                        <span class="text-[11px] font-bold text-white">Drama Club Performance</span>
                    </div>
                </div>

                <!-- Activity 4 (Video) -->
                <div class="relative rounded-xl overflow-hidden group shadow-2xs border border-slate-200">
                    <img 
                        src="https://images.unsplash.com/photo-1511578314322-379afb476865?auto=format&fit=crop&w=500&q=80" 
                        alt="Vibrant Club" 
                        class="w-full h-28 object-cover group-hover:scale-105 transition-transform duration-300"
                    />
                    <div class="absolute inset-0 bg-slate-950/40 flex items-center justify-center">
                        <div class="w-9 h-9 rounded-full bg-white/90 text-slate-900 flex items-center justify-center shadow-md group-hover:scale-110 transition-transform">
                            <svg class="w-4 h-4 ml-0.5" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
                        </div>
                    </div>
                    <div class="absolute inset-x-0 bottom-0 bg-gradient-to-t from-slate-950/90 to-transparent p-2 text-center">
                        <span class="text-[11px] font-bold text-white">Vibrant Club Variety</span>
                    </div>
                </div>

            </div>

        </div>
    </section>


    <!-- ==================================================== -->
    <!-- 2-COLUMN SECTION: MESSAGE & NOTICE BOARD (col-sm-6 / col-md-6) -->
    <!-- ==================================================== -->
    <section class="max-w-[1440px] mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-2 gap-6 items-start">
            
            <!-- COLUMN 1: MESSAGE FROM LEADERSHIP (col-sm-6, col-md-6) -->
            <div class="w-full">
                <section class="bg-white rounded-xl shadow-xs border border-slate-200/90 p-5 sm:p-6 transition-shadow hover:shadow-md h-full">
                    <h3 class="font-black text-slate-900 text-sm sm:text-base tracking-wider uppercase border-b border-slate-100 pb-3 mb-5 font-heading flex items-center justify-between">
                        <span>MESSAGE FROM LEADERSHIP</span>
                        <span class="w-2.5 h-2.5 rounded-full bg-emerald-600"></span>
                    </h3>

                    <!-- Profile 1: Dr. Sarita Sharma (Principal) - Emerald Theme -->
                    <div class="bg-gradient-to-br from-[#059669] to-[#047857] text-white rounded-xl p-4 sm:p-5 shadow-sm mb-5">
                        <div class="flex gap-4 items-start">
                            <img 
                                src="https://images.unsplash.com/photo-1573496359142-b8d87734a5a2?auto=format&fit=crop&w=300&q=80" 
                                alt="Dr. Sarita Sharma - Principal" 
                                class="w-20 h-20 sm:w-24 sm:h-24 rounded-xl object-cover border-2 border-white/80 shadow-md shrink-0"
                            />
                            <div class="min-w-0">
                                <h4 class="font-bold text-white text-base sm:text-lg leading-tight font-heading">Dr. Sarita Sharma</h4>
                                <span class="text-emerald-100 text-xs font-semibold block mb-2">Principal, Gyan Vikas School</span>
                                <p class="text-emerald-50 text-xs sm:text-[13px] leading-relaxed line-clamp-4">
                                    Welcome to Gyan Vikas School. We meet passionate commitment and holistic enrichment. Our institution is dedicated to nurturing curiosity, discipline, and emotional strength in every student, preparing them for an interconnected world.
                                </p>
                            </div>
                        </div>
                        <div class="mt-3 pt-2.5 border-t border-emerald-400/30 flex justify-end">
                            <button class="bg-emerald-900/60 hover:bg-emerald-950 text-emerald-100 hover:text-white text-xs font-bold px-3.5 py-1.5 rounded-md transition-colors">
                                Read Full Message
                            </button>
                        </div>
                    </div>

                    <!-- Profile 2: Mr. Arun Khanna (Director) - Slate/Light Theme -->
                    <div class="bg-slate-50 border border-slate-200/90 rounded-xl p-4 sm:p-5 shadow-2xs">
                        <div class="flex gap-4 items-start">
                            <img 
                                src="https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?auto=format&fit=crop&w=300&q=80" 
                                alt="Mr. Arun Khanna - Director" 
                                class="w-20 h-20 sm:w-24 sm:h-24 rounded-xl object-cover border border-slate-300 shadow-sm shrink-0"
                            />
                            <div class="min-w-0">
                                <h4 class="font-bold text-slate-900 text-base sm:text-lg leading-tight font-heading">Mr. Arun Khanna</h4>
                                <span class="text-slate-500 text-xs font-semibold block mb-2">Director, Gyan Vikas Educational Foundation</span>
                                <p class="text-slate-600 text-xs sm:text-[13px] leading-relaxed line-clamp-4">
                                    Gyan Vikas School stands as a beacon of academic leadership and value-based schooling. We nurture visionaries with global perspectives, cutting-edge technology, and enduring ethical integrity that stand the test of time.
                                </p>
                            </div>
                        </div>
                        <div class="mt-3 pt-2.5 border-t border-slate-200 flex justify-end">
                            <button class="text-blue-700 hover:text-blue-900 text-xs font-bold hover:underline">
                                Read Full Vision →
                            </button>
                        </div>
                    </div>
                </section>
            </div>

            <!-- COLUMN 2: NOTICE BOARD & EVENTS (col-sm-6, col-md-6) -->
            <div class="w-full">
                <section id="notices" class="bg-white rounded-xl shadow-xs border border-slate-200/90 p-5 sm:p-6 transition-shadow hover:shadow-md h-full">
                    <h3 class="font-black text-slate-900 text-sm sm:text-base tracking-wider uppercase border-b border-slate-100 pb-3 mb-5 font-heading flex items-center justify-between">
                        <span>NOTICE BOARD & EVENTS</span>
                        <span class="w-2.5 h-2.5 rounded-full bg-rose-500"></span>
                    </h3>

                    <!-- Events 2x2 Grid -->
                    <div class="grid grid-cols-2 gap-3 mb-5">
                        <div class="relative rounded-lg overflow-hidden group border border-slate-200">
                            <img src="https://images.unsplash.com/photo-1485827404703-89b55fcc595e?auto=format&fit=crop&w=400&q=80" alt="Robotics" class="w-full h-24 object-cover group-hover:scale-105 transition-transform">
                            <div class="absolute inset-0 bg-gradient-to-t from-slate-950/85 to-transparent flex items-end p-2">
                                <span class="text-xs font-bold text-white">Robotics Workshop</span>
                            </div>
                        </div>
                        <div class="relative rounded-lg overflow-hidden group border border-slate-200">
                            <img src="https://images.unsplash.com/photo-1516450360452-9312f5e86fc7?auto=format&fit=crop&w=400&q=80" alt="Drama" class="w-full h-24 object-cover group-hover:scale-105 transition-transform">
                            <div class="absolute inset-0 bg-gradient-to-t from-slate-950/85 to-transparent flex items-end p-2">
                                <span class="text-xs font-bold text-white">Drama Club Performance</span>
                            </div>
                        </div>
                        <div class="relative rounded-lg overflow-hidden group border border-slate-200">
                            <img src="https://images.unsplash.com/photo-1523240795612-9a054b0db644?auto=format&fit=crop&w=400&q=80" alt="Drama" class="w-full h-24 object-cover group-hover:scale-105 transition-transform">
                            <div class="absolute inset-0 bg-gradient-to-t from-slate-950/85 to-transparent flex items-end p-2">
                                <span class="text-xs font-bold text-white">Academic Symposium</span>
                            </div>
                        </div>
                        <div class="relative rounded-lg overflow-hidden group border border-slate-200">
                            <img src="https://images.unsplash.com/photo-1511578314322-379afb476865?auto=format&fit=crop&w=400&q=80" alt="Drama" class="w-full h-24 object-cover group-hover:scale-105 transition-transform">
                            <div class="absolute inset-0 bg-gradient-to-t from-slate-950/85 to-transparent flex items-end p-2">
                                <span class="text-xs font-bold text-white">Cultural Carnival 2024</span>
                            </div>
                        </div>
                    </div>

                    <!-- Latest Notices List Box -->
                    <div class="bg-slate-50 border border-slate-200 rounded-xl p-4 space-y-3.5">
                        <div class="flex items-center justify-between border-b border-slate-200/80 pb-2">
                            <span class="text-xs font-extrabold text-slate-800 uppercase font-heading">Latest Official Circulars</span>
                            <span class="text-[10px] font-semibold text-rose-600 bg-rose-50 px-2.5 py-0.5 rounded-full border border-rose-200/60">Live Updates</span>
                        </div>

                        <!-- Notice 1 -->
                        <div class="space-y-1">
                            <div class="flex items-center justify-between">
                                <span class="text-[10px] font-bold text-blue-700 bg-blue-50 px-2 py-0.5 rounded">Admissions</span>
                                <span class="text-[10px] text-slate-400 font-medium">13 Jan 2024</span>
                            </div>
                            <h5 class="text-xs sm:text-[13px] font-bold text-slate-800 leading-snug hover:text-blue-600 cursor-pointer">
                                Welcome to Gyan Vikas School, Near Rohini, New Delhi — Registration Open for Session 2024-25.
                            </h5>
                        </div>

                        <!-- Notice 2 -->
                        <div class="space-y-1 border-t border-slate-200/60 pt-2.5">
                            <div class="flex items-center justify-between">
                                <span class="text-[10px] font-bold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded">Academic</span>
                                <span class="text-[10px] text-slate-400 font-medium">14 Jan 2024</span>
                            </div>
                            <h5 class="text-xs sm:text-[13px] font-bold text-slate-800 leading-snug hover:text-blue-600 cursor-pointer">
                                Parent Teacher Alumni Club Interaction: Annual Performance Discussion & Career Feedback.
                            </h5>
                        </div>

                        <!-- Notice 3 -->
                        <div class="space-y-1 border-t border-slate-200/60 pt-2.5">
                            <div class="flex items-center justify-between">
                                <span class="text-[10px] font-bold text-amber-700 bg-amber-50 px-2 py-0.5 rounded">Cultural</span>
                                <span class="text-[10px] text-slate-400 font-medium">15 Jan 2024</span>
                            </div>
                            <h5 class="text-xs sm:text-[13px] font-bold text-slate-800 leading-snug hover:text-blue-600 cursor-pointer">
                                Drama Club Performance: The annual theatre showcase is a resounding cultural milestone.
                            </h5>
                        </div>

                        <div class="pt-2 text-right border-t border-slate-200/80">
                            <a href="#notices" class="text-blue-600 font-bold text-xs hover:underline inline-flex items-center gap-1">
                                <span>View Notice Archive</span>
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                            </a>
                        </div>
                    </div>
                </section>
            </div>

        </div>
    </section>


    <!-- ==================================================== -->
    <!-- FULL SCREEN / FULL WIDTH GALLERY & INNOVATION -->
    <!-- ==================================================== -->
    <section 
        id="gallery" 
        class="w-full bg-white border-y border-slate-200/90 py-12 sm:py-16 my-6 shadow-xs"
        x-data="{
            activeFilter: 'all',
            items: [
                {
                    title: 'Collaborative Group Study',
                    category: 'classrooms',
                    image: 'https://images.unsplash.com/photo-1523240795612-9a054b0db644?auto=format&fit=crop&w=600&q=80',
                    tag: 'Academics',
                    isVideo: false
                },
                {
                    title: 'Student Journal & Creative Writing',
                    category: 'classrooms',
                    image: 'https://images.unsplash.com/photo-1427504494785-3a9ca7044f45?auto=format&fit=crop&w=600&q=80',
                    tag: 'Curriculum',
                    isVideo: false
                },
                {
                    title: 'Joyful Primary Classrooms',
                    category: 'classrooms',
                    image: 'https://images.unsplash.com/photo-1577896851231-70ef18881754?auto=format&fit=crop&w=600&q=80',
                    tag: 'Campus Joy',
                    isVideo: false
                },
                {
                    title: 'Advanced Robotics Workshop',
                    category: 'robotics',
                    image: 'https://images.unsplash.com/photo-1485827404703-89b55fcc595e?auto=format&fit=crop&w=600&q=80',
                    tag: 'STEM & Robotics',
                    isVideo: false
                },
                {
                    title: 'Central Digital Knowledge Library',
                    category: 'classrooms',
                    image: 'https://images.unsplash.com/photo-1516321318423-f06f85e504b3?auto=format&fit=crop&w=600&q=80',
                    tag: 'Resource Center',
                    isVideo: false
                },
                {
                    title: 'Chemistry & Molecular Research Lab',
                    category: 'robotics',
                    image: 'https://images.unsplash.com/photo-1532094349884-543bc11b234d?auto=format&fit=crop&w=600&q=80',
                    tag: 'Science Lab',
                    isVideo: true
                },
                {
                    title: 'Annual Drama & Cultural Gala',
                    category: 'cultural',
                    image: 'https://images.unsplash.com/photo-1516450360452-9312f5e86fc7?auto=format&fit=crop&w=600&q=80',
                    tag: 'Performing Arts',
                    isVideo: false
                },
                {
                    title: 'Athletics & Track Championship',
                    category: 'sports',
                    image: 'https://images.unsplash.com/photo-1511578314322-379afb476865?auto=format&fit=crop&w=600&q=80',
                    tag: 'Sports League',
                    isVideo: true
                }
            ],
            filteredItems() {
                if (this.activeFilter === 'all') return this.items;
                return this.items.filter(item => item.category === this.activeFilter);
            }
        }"
    >
        <div class="max-w-[1440px] mx-auto px-4 sm:px-6 lg:px-8">
            
            <!-- Section Header -->
            <div class="text-center max-w-3xl mx-auto mb-8">
                <div class="inline-flex items-center gap-2 bg-cyan-50 border border-cyan-200 text-cyan-900 text-xs font-bold px-3.5 py-1.5 rounded-full mb-3 shadow-2xs">
                    <span class="w-2 h-2 rounded-full bg-cyan-500"></span>
                    <span class="font-hindi">परिसर, नवाचार एवं स्मृतियां</span>
                    <span>•</span>
                    <span class="tracking-wider uppercase">CAMPUS LIFE & INNOVATION</span>
                </div>
                <h2 class="text-2xl sm:text-3xl lg:text-4xl font-extrabold text-slate-900 tracking-tight font-heading uppercase">
                    GALLERY & INNOVATION
                </h2>
                <p class="text-slate-600 text-xs sm:text-sm mt-2 leading-relaxed">
                    Capturing everyday moments of curiosity, discovery, athletics, and cultural milestones across the Gyan Vikas campus.
                </p>
            </div>

            <!-- Filter Buttons Bar -->
            <div class="flex flex-wrap items-center justify-center gap-2 mb-8">
                <button 
                    @click="activeFilter = 'all'" 
                    :class="activeFilter === 'all' ? 'bg-[#0070e0] text-white shadow-sm' : 'bg-slate-100 text-slate-700 hover:bg-slate-200'"
                    class="px-4 py-2 rounded-full text-xs font-bold transition-all cursor-pointer"
                >
                    All Photos (सभी)
                </button>
                <button 
                    @click="activeFilter = 'classrooms'" 
                    :class="activeFilter === 'classrooms' ? 'bg-[#0070e0] text-white shadow-sm' : 'bg-slate-100 text-slate-700 hover:bg-slate-200'"
                    class="px-4 py-2 rounded-full text-xs font-bold transition-all cursor-pointer"
                >
                    Smart Classrooms (कक्षाएं)
                </button>
                <button 
                    @click="activeFilter = 'robotics'" 
                    :class="activeFilter === 'robotics' ? 'bg-[#0070e0] text-white shadow-sm' : 'bg-slate-100 text-slate-700 hover:bg-slate-200'"
                    class="px-4 py-2 rounded-full text-xs font-bold transition-all cursor-pointer"
                >
                    Robotics & Science Labs (प्रयोगशालाएं)
                </button>
                <button 
                    @click="activeFilter = 'cultural'" 
                    :class="activeFilter === 'cultural' ? 'bg-[#0070e0] text-white shadow-sm' : 'bg-slate-100 text-slate-700 hover:bg-slate-200'"
                    class="px-4 py-2 rounded-full text-xs font-bold transition-all cursor-pointer"
                >
                    Cultural & Drama (सांस्कृतिक)
                </button>
                <button 
                    @click="activeFilter = 'sports'" 
                    :class="activeFilter === 'sports' ? 'bg-[#0070e0] text-white shadow-sm' : 'bg-slate-100 text-slate-700 hover:bg-slate-200'"
                    class="px-4 py-2 rounded-full text-xs font-bold transition-all cursor-pointer"
                >
                    Sports & Athletics (खेलकूद)
                </button>
            </div>

            <!-- Dynamic Photos Grid (8 items across 4 columns) -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
                <template x-for="(item, idx) in filteredItems()" :key="idx">
                    <div class="relative rounded-xl overflow-hidden aspect-4/3 group cursor-pointer border border-slate-200 shadow-2xs hover:shadow-md transition-all">
                        <img 
                            :src="item.image" 
                            :alt="item.title" 
                            class="w-full h-full object-cover transform group-hover:scale-110 transition-transform duration-700"
                        />
                        
                        <!-- Video Play Icon Overlay -->
                        <template x-if="item.isVideo">
                            <div class="absolute inset-0 bg-slate-950/35 flex items-center justify-center">
                                <div class="w-10 h-10 rounded-full bg-white/90 text-slate-900 flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform">
                                    <svg class="w-5 h-5 ml-0.5" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
                                </div>
                            </div>
                        </template>

                        <!-- Bottom Gradient Caption -->
                        <div class="absolute inset-0 bg-gradient-to-t from-slate-950/90 via-slate-950/30 to-transparent flex flex-col justify-end p-3.5 opacity-90 group-hover:opacity-100 transition-opacity">
                            <span class="text-[10px] font-bold text-amber-300 uppercase tracking-wider" x-text="item.tag"></span>
                            <h4 class="text-xs sm:text-sm font-bold text-white leading-tight font-heading mt-0.5" x-text="item.title"></h4>
                        </div>
                    </div>
                </template>
            </div>

            <!-- 2 Grand Panoramic Wide Banner Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mb-8">
                
                <!-- Panoramic Card 1: Innovation Labs -->
                <div class="relative rounded-2xl overflow-hidden group shadow-md cursor-pointer border border-slate-200 min-h-[180px] sm:min-h-[200px] flex items-end">
                    <img 
                        src="https://images.unsplash.com/photo-1581092921461-eab62e97a780?auto=format&fit=crop&w=900&q=80" 
                        alt="Student Innovation & Labs" 
                        class="absolute inset-0 w-full h-full object-cover transform group-hover:scale-105 transition-transform duration-700"
                    />
                    <div class="absolute inset-0 bg-gradient-to-t from-slate-950/95 via-slate-950/55 to-slate-950/20"></div>
                    <div class="relative z-10 p-5 sm:p-6 w-full">
                        <span class="text-xs font-black uppercase text-amber-400 tracking-wider">Research & Technology Center</span>
                        <h4 class="text-lg sm:text-xl font-extrabold text-white font-heading mt-1">Student Innovation & High-Tech Science Labs</h4>
                        <p class="text-xs text-slate-200 mt-1 max-w-lg leading-relaxed line-clamp-2">
                            Equipped with 3D printers, robotic automation arms, AI workstations, and university-grade chemical analysis equipment.
                        </p>
                    </div>
                </div>

                <!-- Panoramic Card 2: Vibrant Community -->
                <div class="relative rounded-2xl overflow-hidden group shadow-md cursor-pointer border border-slate-200 min-h-[180px] sm:min-h-[200px] flex items-end">
                    <img 
                        src="https://images.unsplash.com/photo-1529156069898-49953e39b3ac?auto=format&fit=crop&w=900&q=80" 
                        alt="Vibrant Community" 
                        class="absolute inset-0 w-full h-full object-cover transform group-hover:scale-105 transition-transform duration-700"
                    />
                    <div class="absolute inset-0 bg-gradient-to-t from-slate-950/95 via-slate-950/55 to-slate-950/20"></div>
                    <div class="relative z-10 p-5 sm:p-6 w-full">
                        <span class="text-xs font-black uppercase text-emerald-400 tracking-wider">Campus Life & Athletics</span>
                        <h4 class="text-lg sm:text-xl font-extrabold text-white font-heading mt-1">Vibrant Community, Sports & Cultural Spirit</h4>
                        <p class="text-xs text-slate-200 mt-1 max-w-lg leading-relaxed line-clamp-2">
                            Fostering lifelong friendships, championship sportsmanship, dramatic theatre arts, and humanitarian student clubs.
                        </p>
                    </div>
                </div>

            </div>

            <!-- Footer Action Button -->
            <div class="text-center">
                <a href="#gallery" class="inline-flex items-center gap-2 bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs sm:text-sm px-7 py-3.5 rounded-full shadow-md hover:shadow-lg transition-all">
                    <svg class="w-4 h-4 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    <span>View Complete Campus Photo Gallery (300+ Photos)</span>
                </a>
            </div>

        </div>
    </section>


    <!-- FOOTER (MATCHING DEEP NAVY BLUE FOOTER) -->
    <footer id="contact" class="bg-[#0b1f33] text-slate-300 border-t border-[#183654] pt-10 pb-6">
        <div class="max-w-[1440px] mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8 mb-8">
                
                <!-- Col 1: Home & Quick Links -->
                <div>
                    <h4 class="text-white font-extrabold text-sm uppercase tracking-wider mb-4 font-heading border-b border-slate-700/60 pb-1.5">Navigation</h4>
                    <ul class="space-y-2 text-xs">
                        <li><a href="{{ url('/') }}" class="hover:text-amber-400 transition-colors">Home</a></li>
                        <li><a href="#about" class="hover:text-amber-400 transition-colors">About Us</a></li>
                        <li><a href="#academics" class="hover:text-amber-400 transition-colors">Academics</a></li>
                        <li><a href="#admissions" class="hover:text-amber-400 transition-colors">Admissions</a></li>
                        <li><a href="#careers" class="hover:text-amber-400 transition-colors">Careers</a></li>
                        <li><a href="#contact" class="hover:text-amber-400 transition-colors">Contact Us</a></li>
                    </ul>
                </div>

                <!-- Col 2: CONTACT US -->
                <div>
                    <h4 class="text-white font-extrabold text-sm uppercase tracking-wider mb-4 font-heading border-b border-slate-700/60 pb-1.5">CONTACT US</h4>
                    <div class="space-y-2.5 text-xs text-slate-300">
                        <p class="flex items-start gap-2">
                            <svg class="w-4 h-4 text-amber-400 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            <span>Sector 10, Rohini, New Delhi, Delhi, 110085, India</span>
                        </p>
                        <p class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-amber-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                            <span>+91 11 2345 6789 / +91 98765 43210</span>
                        </p>
                        <p class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-amber-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                            <span>info@gyanvikas.edu.in</span>
                        </p>
                    </div>
                </div>

                <!-- Col 3: Quick Links -->
                <div>
                    <h4 class="text-white font-extrabold text-sm uppercase tracking-wider mb-4 font-heading border-b border-slate-700/60 pb-1.5">Quick Links</h4>
                    <ul class="space-y-2 text-xs">
                        <li><a href="#placement" class="hover:text-amber-400 transition-colors">Alumni Network</a></li>
                        <li><a href="#faq" class="hover:text-amber-400 transition-colors">Frequently Asked Questions (FAQ)</a></li>
                        <li><a href="#gallery" class="hover:text-amber-400 transition-colors">Campus Photo Gallery</a></li>
                        <li><a href="#admissions" class="hover:text-amber-400 transition-colors">Fee Structure & Scholarships</a></li>
                        <li><a href="#notices" class="hover:text-amber-400 transition-colors">School Calendar 2024-25</a></li>
                    </ul>
                </div>

                <!-- Col 4: Social Media Icons & Parent Portal -->
                <div>
                    <h4 class="text-white font-extrabold text-sm uppercase tracking-wider mb-4 font-heading border-b border-slate-700/60 pb-1.5">Connect With Us</h4>
                    <div class="flex items-center gap-3 mb-5">
                        <!-- Facebook -->
                        <a href="#" class="w-8 h-8 rounded-full bg-blue-600 text-white flex items-center justify-center hover:opacity-90 transition-opacity" title="Facebook">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M22 12c0-5.523-4.477-10-10-10S2 6.477 2 12c0 4.991 3.657 9.128 8.438 9.878v-6.987h-2.54V12h2.54V9.797c0-2.506 1.492-3.89 3.777-3.89 1.094 0 2.238.195 2.238.195v2.46h-1.26c-1.243 0-1.63.771-1.63 1.562V12h2.773l-.443 2.89h-2.33v6.988C18.343 21.128 22 16.991 22 12z"/></svg>
                        </a>
                        <!-- Twitter/X -->
                        <a href="#" class="w-8 h-8 rounded-full bg-sky-500 text-white flex items-center justify-center hover:opacity-90 transition-opacity" title="Twitter">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M8.29 20.251c7.547 0 11.675-6.253 11.675-11.675 0-.178 0-.355-.012-.53A8.348 8.348 0 0022 5.92a8.19 8.19 0 01-2.357.646 4.118 4.118 0 001.804-2.27 8.224 8.224 0 01-2.605.996 4.107 4.107 0 00-6.993 3.743 11.65 11.65 0 01-8.457-4.287 4.106 4.106 0 001.27 5.477A4.072 4.072 0 012.8 9.713v.052a4.105 4.105 0 003.292 4.022 4.095 4.095 0 01-1.853.07 4.108 4.108 0 003.834 2.85A8.233 8.233 0 012 18.407a11.616 11.616 0 006.29 1.84"/></svg>
                        </a>
                        <!-- YouTube -->
                        <a href="#" class="w-8 h-8 rounded-full bg-red-600 text-white flex items-center justify-center hover:opacity-90 transition-opacity" title="YouTube">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/></svg>
                        </a>
                        <!-- Instagram -->
                        <a href="#" class="w-8 h-8 rounded-full bg-gradient-to-tr from-amber-500 via-rose-500 to-purple-600 text-white flex items-center justify-center hover:opacity-90 transition-opacity" title="Instagram">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/></svg>
                        </a>
                    </div>

                    <!-- Direct Parent Portal Access -->
                    <a href="{{ route('login') }}" class="block text-center bg-amber-400 hover:bg-amber-500 text-slate-950 font-black text-xs px-4 py-2.5 rounded-lg uppercase tracking-wider transition-colors shadow-sm">
                        Access Parent Portal
                    </a>
                </div>

            </div>

            <!-- Bottom Copyright Bar -->
            <div class="border-t border-slate-700/60 pt-6 flex flex-col sm:flex-row items-center justify-between text-xs text-slate-400">
                <p>Copyright © 2024 Gyan Vikas School. All Rights Reserved.</p>
                <p class="mt-2 sm:mt-0 font-medium">Affiliated with CBSE Board, New Delhi</p>
            </div>
        </div>
    </footer>

</body>
</html>
