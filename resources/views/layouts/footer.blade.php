<footer class="bg-gradient-to-b from-gray-900 to-gray-950 border-t border-gray-800 mt-auto relative overflow-hidden">
    {{-- Animated background grid pattern --}}
    <div class="absolute inset-0 opacity-5" 
         style="background-image: linear-gradient(#374151 1px, transparent 1px), linear-gradient(90deg, #374151 1px, transparent 1px); background-size: 30px 30px;">
    </div>
    
    {{-- Subtle grey accent line with minimal animation --}}
    <div x-data="{ shimmer: false }" 
         x-init="setInterval(() => shimmer = !shimmer, 3000)"
         class="h-px bg-gradient-to-r from-gray-700 via-gray-600 to-gray-700 relative overflow-hidden">
        <div x-show="shimmer" 
             x-transition:enter="transition ease-in-out duration-1500"
             x-transition:enter-start="opacity-0 -translate-x-full"
             x-transition:enter-end="opacity-20 translate-x-full"
             x-transition:leave="hidden"
             class="absolute inset-0 bg-gradient-to-r from-transparent via-gray-300 to-transparent">
        </div>
    </div>
    
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 relative">
        {{-- Main footer content with subtle glass effect --}}
        <div class="backdrop-blur-sm bg-gray-900/30 rounded-2xl p-6 border border-gray-800 shadow-xl">
            <div class="flex flex-col md:flex-row justify-between items-center gap-6">
                {{-- Logo and copyright with refined grey styling --}}
                <div class="flex items-center space-x-4 group">
                    <a href="{{ route('dashboard') }}" 
                       class="h-10 w-10 rounded-xl bg-gradient-to-br from-gray-700 to-gray-900 flex items-center justify-center shadow-lg hover:shadow-gray-700/30 transition-all duration-300 hover:scale-110 border border-gray-700">
                        <span class="text-lg font-bold text-gray-200 drop-shadow-lg">⚡</span>
                    </a>
                    <div class="flex flex-col">
                        <a href="{{ route('dashboard') }}" 
                           class="text-sm font-bold text-gray-300 hover:text-white transition-all duration-300">
                            EMMS
                        </a>
                        <span class="text-[9px] font-medium text-gray-600 tracking-[0.2em]">ELECTRICAL MAINTENANCE</span>
                    </div>
                    <div class="h-6 w-px bg-gradient-to-b from-transparent via-gray-700 to-transparent mx-2"></div>
                    <span class="text-xs text-gray-500 font-light tracking-wide">© {{ date('Y') }}</span>
                </div>
                
                {{-- Industrial status indicators with grey theme --}}
                <div x-data="{ systemOk: true }" class="flex items-center space-x-8">
                    <div class="flex items-center space-x-3 bg-gray-800/50 px-3 py-1.5 rounded-full border border-gray-700">
                        <span class="relative flex h-2.5 w-2.5">
                            <span x-show="systemOk" 
                                  x-transition:enter="ease-out duration-300"
                                  class="absolute inline-flex h-full w-full rounded-full bg-gray-400 animate-ping opacity-75">
                            </span>
                            <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-gray-500 ring-2 ring-gray-500/20"></span>
                        </span>
                        <span class="text-xs font-semibold text-gray-400 tracking-wider">SYSTEM OK</span>
                    </div>
                    
                    <div class="h-6 w-px bg-gradient-to-b from-transparent via-gray-700 to-transparent"></div>
                    
                    {{-- Links with refined hover effects --}}
                    <div class="flex items-center space-x-5">
                        <a href="{{ route('privacy-policy') }}" 
                           class="text-xs text-gray-500 hover:text-gray-300 hover:scale-105 transition-all duration-300 relative group">
                            <span class="relative z-10">Privacy</span>
                            <span class="absolute inset-x-0 bottom-0 h-px bg-gradient-to-r from-transparent via-gray-400 to-transparent scale-x-0 group-hover:scale-x-100 transition-transform duration-300"></span>
                        </a>
                        <a href="{{ route('terms-of-service') }}" 
                           class="text-xs text-gray-500 hover:text-gray-300 hover:scale-105 transition-all duration-300 relative group">
                            <span class="relative z-10">Terms</span>
                            <span class="absolute inset-x-0 bottom-0 h-px bg-gradient-to-r from-transparent via-gray-400 to-transparent scale-x-0 group-hover:scale-x-100 transition-transform duration-300"></span>
                        </a>
                        <a href="{{ route('contact') }}" 
                           class="text-xs text-gray-500 hover:text-gray-300 hover:scale-105 transition-all duration-300 relative group">
                            <span class="relative z-10">Contact</span>
                            <span class="absolute inset-x-0 bottom-0 h-px bg-gradient-to-r from-transparent via-gray-400 to-transparent scale-x-0 group-hover:scale-x-100 transition-transform duration-300"></span>
                        </a>
                    </div>
                    
                    <div class="h-6 w-px bg-gradient-to-b from-transparent via-gray-700 to-transparent"></div>
                    
                    {{-- Version badge with refined grey styling --}}
                    <div x-data="{ versionHover: false }"
                         @mouseenter="versionHover = true"
                         @mouseleave="versionHover = false"
                         class="text-xs font-mono text-gray-400 border border-gray-700 px-3 py-1.5 rounded-full bg-gray-800/50 shadow-inner transition-all duration-300 flex items-center space-x-1"
                         :class="versionHover ? 'border-gray-500' : ''">
                        <span class="text-[10px] text-gray-600">v</span>
                        <span>1.0.0</span>
                    </div>
                </div>
            </div>
        </div>
        
        {{-- Bottom decorative bar with grey pulsing LEDs 
        <div x-data="{ 
            led1: false, 
            led2: false, 
            led3: false,
            uptime: 99.9
        }" 
        x-init="
            setInterval(() => led1 = !led1, 1000);
            setInterval(() => led2 = !led2, 1300);
            setInterval(() => led3 = !led3, 1600);
        " 
        class="mt-6 flex justify-between items-center">
            <div class="flex items-center space-x-2">
                <span class="w-1 h-1 rounded-full transition-colors duration-300"
                      :class="led1 ? 'bg-gray-500' : 'bg-gray-500/30'"></span>
                <span class="w-1 h-1 rounded-full transition-colors duration-300"
                      :class="led2 ? 'bg-gray-500' : 'bg-gray-500/30'"></span>
                <span class="w-1 h-1 rounded-full transition-colors duration-300"
                      :class="led3 ? 'bg-gray-500' : 'bg-gray-500/30'"></span>
                <span class="text-[9px] text-gray-600 ml-2 font-mono tracking-wider">SYS v2.1</span>
            </div>
            
            <div class="text-[9px] text-gray-600 font-mono tracking-[0.3em] bg-gray-800/30 px-4 py-1.5 rounded-full border border-gray-700">
                ⚡ INDUSTRIAL CONTROL SYSTEM ⚡
            </div>
            
            <div x-data="{ uptimeLed: false }"
                 x-init="setInterval(() => uptimeLed = !uptimeLed, 1200)"
                 class="flex items-center space-x-2">
                <span class="text-[9px] text-gray-600 mr-2 font-mono tracking-wider">
                    UPTIME <span x-text="uptime"></span>%
                </span>
                <span class="w-1 h-1 rounded-full transition-colors duration-300"
                      :class="uptimeLed ? 'bg-gray-500' : 'bg-gray-500/30'"></span>
                <span class="w-1 h-1 rounded-full transition-colors duration-300"
                      :class="uptimeLed ? 'bg-gray-500' : 'bg-gray-500/30'"></span>
                <span class="w-1 h-1 rounded-full transition-colors duration-300"
                      :class="uptimeLed ? 'bg-gray-500' : 'bg-gray-500/30'"></span>
            </div>
        </div>--}}
    </div>
</footer>