<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <a href="{{ route('orders.index') }}" class="inline-flex items-center text-xs font-bold text-slate-400 hover:text-white transition mb-2">
                    <svg class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Back to My Orders
                </a>
                <h2 class="font-extrabold text-2xl text-slate-100 tracking-tight">
                    Order {{ $order->order_number }}
                </h2>
            </div>
            
            <div class="flex items-center space-x-3">
                @php
                    $statusColors = [
                        'pending' => 'bg-amber-500/10 border-amber-500/20 text-amber-400',
                        'paid' => 'bg-emerald-500/10 border-emerald-500/20 text-emerald-400',
                        'processing' => 'bg-blue-500/10 border-blue-500/20 text-blue-400',
                        'shipped' => 'bg-purple-500/10 border-purple-500/20 text-purple-400',
                        'completed' => 'bg-indigo-500/10 border-indigo-500/20 text-indigo-400',
                        'cancelled' => 'bg-red-500/10 border-red-500/20 text-red-400',
                    ];
                    $badgeStyle = $statusColors[$order->status] ?? 'bg-slate-500/10 border-slate-500/20 text-slate-400';
                @endphp
                <span class="inline-flex items-center px-3 py-1.5 text-xs font-extrabold tracking-wider uppercase border rounded-xl {{ $badgeStyle }}">
                    {{ $order->status }}
                </span>
            </div>
        </div>
    </x-slot>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 relative z-10">
        <!-- Status Messages -->
        @if(session('success'))
            <div class="mb-6 p-4 bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 rounded-2xl flex items-center space-x-3 shadow-lg shadow-emerald-500/5">
                <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span class="font-bold text-sm">{{ session('success') }}</span>
            </div>
        @endif

        @if(session('error'))
            <div class="mb-6 p-4 bg-red-500/10 border border-red-500/20 text-red-400 rounded-2xl flex items-center space-x-3 shadow-lg shadow-red-500/5">
                <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span class="font-bold text-sm">{{ session('error') }}</span>
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Left 2 Cols: Order Items & Delivery details -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Order Items Card -->
                <div class="bg-[#1E293B]/50 backdrop-blur-md border border-slate-800 rounded-2xl shadow-xl overflow-hidden relative">
                    <div class="absolute top-0 left-0 right-0 h-[2px] bg-gradient-to-r from-purple-500 via-fuchsia-500 to-blue-500"></div>
                    
                    <div class="p-6 border-b border-slate-800 bg-[#1E293B]/30">
                        <h3 class="font-bold text-lg text-slate-100">Ordered Items</h3>
                    </div>

                    <div class="divide-y divide-slate-800 bg-[#1E293B]/10">
                        @foreach($order->items as $item)
                            <div class="p-6 flex items-center justify-between hover:bg-slate-800/20 transition duration-150">
                                <div class="flex items-center space-x-4">
                                    <div class="w-16 h-16 rounded-xl bg-slate-800 overflow-hidden shrink-0 border border-slate-700">
                                        @if($item->product && $item->product->primaryImage)
                                            <img src="{{ $item->product->primaryImage->image_path }}" alt="{{ $item->product->name }}" class="w-full h-full object-cover">
                                        @else
                                            <div class="w-full h-full flex items-center justify-center text-slate-500 font-bold text-xs">N/A</div>
                                        @endif
                                    </div>
                                    <div>
                                        <h4 class="font-extrabold text-slate-200 text-sm hover:text-purple-400 transition">
                                            @if($item->product)
                                                <a href="{{ route('shop.show', $item->product->slug) }}">{{ $item->product->name }}</a>
                                            @else
                                                N/A (Product Deleted)
                                            @endif
                                        </h4>
                                        <p class="text-xs text-slate-400 mt-1 font-semibold">
                                            Rp {{ number_format($item->price, 0, ',', '.') }} &times; {{ $item->quantity }}
                                        </p>
                                    </div>
                                </div>
                                <span class="font-bold text-slate-100">
                                    Rp {{ number_format($item->price * $item->quantity, 0, ',', '.') }}
                                </span>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Delivery Details Card -->
                <div class="bg-[#1E293B]/50 backdrop-blur-md border border-slate-800 rounded-2xl p-6 shadow-xl relative overflow-hidden">
                    <div class="absolute top-0 left-0 right-0 h-[2px] bg-gradient-to-r from-blue-500 to-purple-500"></div>
                    
                    <h3 class="font-bold text-lg text-slate-100 border-b border-slate-800 pb-3 mb-5">Delivery Information</h3>
                    
                    <div class="space-y-6 text-sm">
                        <div>
                            <span class="text-xs text-slate-500 font-extrabold uppercase tracking-wider block">Customer Name</span>
                            <span class="text-slate-200 font-bold mt-1 block">{{ $order->user->name }}</span>
                            <span class="text-slate-400 font-medium text-xs block">{{ $order->user->email }}</span>
                        </div>
                        
                        <div>
                            <span class="text-xs text-slate-500 font-extrabold uppercase tracking-wider block">Shipping Address</span>
                            <p class="text-slate-300 font-semibold mt-1 bg-slate-900/60 p-4 border border-slate-800 rounded-xl leading-relaxed">
                                {{ $order->shipping_address }}
                            </p>
                        </div>

                        @if($order->notes)
                            <div>
                                <span class="text-xs text-slate-500 font-extrabold uppercase tracking-wider block">Order Notes</span>
                                <p class="text-slate-400 italic mt-1 bg-slate-900/40 p-4 border border-slate-800/50 rounded-xl">
                                    "{{ $order->notes }}"
                                </p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Right 1 Col: Billing Summary & Payment/Cancellation Actions -->
            <div class="space-y-6">
                <!-- Order Pricing Breakdown -->
                <div class="bg-[#1E293B]/50 backdrop-blur-md border border-slate-800 rounded-2xl p-6 shadow-xl relative overflow-hidden">
                    <div class="absolute top-0 left-0 right-0 h-[2px] bg-gradient-to-r from-purple-500 to-blue-500"></div>
                    
                    <h3 class="font-bold text-lg text-slate-100 border-b border-slate-800 pb-3 mb-5">Billing Summary</h3>
                    
                    @php
                        $subtotal = 0;
                        foreach($order->items as $item) {
                            $subtotal += $item->price * $item->quantity;
                        }
                    @endphp

                    <div class="space-y-4 text-sm text-slate-300">
                        <div class="flex justify-between">
                            <span>Items Subtotal</span>
                            <span class="font-bold text-slate-100">Rp {{ number_format($subtotal, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span>Shipping Cost</span>
                            <span class="font-bold text-slate-100">Rp {{ number_format($order->shipping_cost, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between pt-4 border-t border-slate-800 text-base font-bold text-slate-100">
                            <span>Grand Total</span>
                            <span class="text-purple-400 text-lg">Rp {{ number_format($order->total_price, 0, ',', '.') }}</span>
                        </div>
                    </div>
                </div>

                {{-- QRIS Payment Box (Only when status is pending) --}}
                @if($order->status === 'pending')
                    <div class="bg-[#1E293B]/50 backdrop-blur-md border border-purple-500/25 rounded-2xl p-6 shadow-xl relative overflow-hidden">
                        <div class="absolute top-0 left-0 right-0 h-[2px] bg-gradient-to-r from-purple-500 via-pink-500 to-blue-500"></div>

                        <h3 class="font-extrabold text-base text-slate-100 flex items-center space-x-2 border-b border-slate-800 pb-3 mb-4">
                            <span class="p-1.5 bg-purple-500/10 border border-purple-500/25 rounded-lg text-purple-400">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z" />
                                </svg>
                            </span>
                            <span>Bayar dengan QRIS</span>
                        </h3>

                        {{-- State: Belum generate QR --}}
                        <div id="qris-initial" class="space-y-4">
                            <div class="p-4 bg-slate-900/60 border border-slate-800 rounded-xl space-y-2">
                                <div class="flex justify-between items-center text-xs">
                                    <span class="text-slate-400 font-semibold">Total Pembayaran</span>
                                    <span class="text-purple-400 font-extrabold text-base">Rp {{ number_format($order->total_price, 0, ',', '.') }}</span>
                                </div>
                                <div class="flex justify-between items-center text-xs">
                                    <span class="text-slate-400 font-semibold">Order ID</span>
                                    <span class="text-slate-200 font-bold font-mono">{{ $order->order_number }}</span>
                                </div>
                                <div class="flex justify-between items-center text-xs">
                                    <span class="text-slate-400 font-semibold">Metode</span>
                                    <span class="text-green-400 font-bold">QRIS (GoPay / OVO / Dana / dll)</span>
                                </div>
                            </div>

                            <button id="btn-generate-qris"
                                class="w-full inline-flex items-center justify-center gap-2 px-6 py-3.5 bg-gradient-to-r from-purple-600 to-blue-600 border border-transparent rounded-xl font-bold text-xs text-white uppercase tracking-widest hover:from-purple-500 hover:to-blue-500 transition duration-150 shadow-lg shadow-purple-500/20 cursor-pointer">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01" />
                                </svg>
                                Tampilkan Kode QRIS
                            </button>

                            <div class="text-slate-500 text-xs font-semibold text-center">
                                🔒 Diterima oleh GoPay, OVO, Dana, LinkAja, ShopeePay & semua bank
                            </div>
                        </div>

                        {{-- State: Loading --}}
                        <div id="qris-loading" class="hidden text-center py-8 space-y-3">
                            <div class="inline-block animate-spin rounded-full h-10 w-10 border-b-2 border-purple-500"></div>
                            <p class="text-slate-400 text-sm font-semibold">Generating QRIS Code...</p>
                        </div>

                        {{-- State: QR Code tampil --}}
                        <div id="qris-display" class="hidden space-y-4">
                            {{-- Info Bar --}}
                            <div class="p-3 bg-slate-900/60 border border-slate-800 rounded-xl flex justify-between items-center text-xs">
                                <span class="text-slate-400 font-semibold">Total</span>
                                <span class="text-purple-400 font-extrabold">Rp {{ number_format($order->total_price, 0, ',', '.') }}</span>
                            </div>

                            {{-- QR Code Container --}}
                            <div class="flex flex-col items-center bg-white rounded-2xl p-4 shadow-inner border-4 border-purple-500/30">
                                {{-- QRIS Logo --}}
                                <div class="flex items-center gap-2 mb-3">
                                    <div class="bg-red-600 text-white font-extrabold text-xs px-2 py-0.5 rounded">QRIS</div>
                                    <span class="text-slate-500 text-xs font-bold">SetupNesia</span>
                                </div>

                                {{-- QR Image --}}
                                <img id="qris-img" src="" alt="QRIS Code" class="w-52 h-52 object-contain rounded-xl">

                                {{-- Merchant Name --}}
                                <p class="text-slate-600 text-xs font-bold mt-2 text-center">SetupNesia Store</p>
                                <p class="text-slate-400 text-xs font-mono">{{ $order->order_number }}</p>
                            </div>

                            {{-- Timer --}}
                            <div class="flex items-center justify-center gap-2 p-3 bg-amber-500/10 border border-amber-500/20 rounded-xl">
                                <svg class="h-4 w-4 text-amber-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <span class="text-amber-400 text-xs font-bold">
                                    QR aktif selama <span id="qris-countdown" class="font-mono text-amber-300">15:00</span>
                                </span>
                            </div>

                            {{-- Instruction --}}
                            <div class="text-center space-y-1">
                                <p class="text-slate-300 text-xs font-bold">Cara Bayar:</p>
                                <p class="text-slate-400 text-xs">1. Buka aplikasi e-wallet atau m-banking</p>
                                <p class="text-slate-400 text-xs">2. Pilih fitur <strong class="text-slate-200">Scan QR / QRIS</strong></p>
                                <p class="text-slate-400 text-xs">3. Arahkan kamera ke QR code di atas</p>
                                <p class="text-slate-400 text-xs">4. Konfirmasi jumlah & selesaikan pembayaran</p>
                            </div>

                            {{-- Confirm Payment Button --}}
                            <form action="{{ route('orders.show', $order->id) }}" method="GET">
                                <button type="button" id="btn-confirm-payment"
                                    class="w-full inline-flex items-center justify-center gap-2 px-6 py-3 bg-emerald-600 hover:bg-emerald-500 border border-transparent rounded-xl font-bold text-xs text-white uppercase tracking-widest transition duration-150 shadow-lg shadow-emerald-500/20 cursor-pointer">
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                    </svg>
                                    Sudah Bayar — Cek Status
                                </button>
                            </form>

                            <p class="text-slate-500 text-xs text-center">
                                Status order akan diupdate otomatis setelah pembayaran dikonfirmasi
                            </p>
                        </div>

                        {{-- State: Error --}}
                        <div id="qris-error" class="hidden p-4 bg-red-500/10 border border-red-500/20 rounded-xl text-center space-y-3">
                            <svg class="h-8 w-8 text-red-400 mx-auto" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <p id="qris-error-msg" class="text-red-400 text-sm font-semibold"></p>
                            <button id="btn-retry-qris" class="text-xs text-purple-400 hover:text-purple-300 font-bold underline cursor-pointer">
                                Coba Lagi
                            </button>
                        </div>
                    </div>

                    <script>
                    (function() {
                        const btnGenerate  = document.getElementById('btn-generate-qris');
                        const btnRetry     = document.getElementById('btn-retry-qris');
                        const btnConfirm   = document.getElementById('btn-confirm-payment');
                        const stateInitial = document.getElementById('qris-initial');
                        const stateLoad    = document.getElementById('qris-loading');
                        const stateDisplay = document.getElementById('qris-display');
                        const stateError   = document.getElementById('qris-error');
                        const qrisImg      = document.getElementById('qris-img');
                        const errorMsg     = document.getElementById('qris-error-msg');
                        const countdown    = document.getElementById('qris-countdown');

                        let timerInterval = null;

                        function showState(state) {
                            [stateInitial, stateLoad, stateDisplay, stateError].forEach(el => el.classList.add('hidden'));
                            state.classList.remove('hidden');
                        }

                        function startCountdown(minutes) {
                            let totalSeconds = minutes * 60;
                            clearInterval(timerInterval);
                            timerInterval = setInterval(() => {
                                const m = Math.floor(totalSeconds / 60);
                                const s = totalSeconds % 60;
                                countdown.textContent = `${String(m).padStart(2, '0')}:${String(s).padStart(2, '0')}`;
                                if (totalSeconds <= 0) {
                                    clearInterval(timerInterval);
                                    countdown.textContent = 'EXPIRED';
                                    countdown.classList.replace('text-amber-300', 'text-red-400');
                                }
                                totalSeconds--;
                            }, 1000);
                        }

                        function generateQris() {
                            showState(stateLoad);

                            fetch('{{ route('payment.qris', $order->id) }}', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                                }
                            })
                            .then(res => {
                                if (!res.ok) return res.json().then(e => { throw new Error(e.message || 'Gagal generate QRIS'); });
                                return res.json();
                            })
                            .then(data => {
                                qrisImg.src = data.qris_image;
                                showState(stateDisplay);
                                startCountdown(15);
                            })
                            .catch(err => {
                                errorMsg.textContent = err.message;
                                showState(stateError);
                            });
                        }

                        btnGenerate.addEventListener('click', generateQris);
                        btnRetry.addEventListener('click', generateQris);

                        btnConfirm.addEventListener('click', () => {
                            window.location.reload();
                        });
                    })();
                    </script>
                @endif

                <!-- Cancellation Form Box -->
                @if(in_array($order->status, ['pending', 'paid']))
                    <div class="bg-[#1E293B]/50 backdrop-blur-md border border-red-500/15 rounded-2xl p-6 shadow-xl relative overflow-hidden">
                        <div class="absolute top-0 left-0 right-0 h-[2px] bg-red-500/35"></div>
                        
                        <h3 class="font-bold text-base text-slate-100 border-b border-slate-800 pb-3 mb-4 flex items-center space-x-2">
                            <span class="p-1 bg-red-500/10 border border-red-500/25 rounded text-red-400">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                </svg>
                            </span>
                            <span>Need to Cancel?</span>
                        </h3>
                        
                        <p class="text-xs text-slate-400 font-semibold mb-5 leading-relaxed">
                            You can cancel this order since it has not been processed. If you've already paid, your funds will be queueing for refund verification.
                        </p>

                        <form action="{{ route('orders.cancel', $order->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to cancel this order? This will restock the inventory immediately.');">
                            @csrf
                            <button type="submit" class="w-full inline-flex items-center justify-center px-4 py-3 bg-red-950/20 hover:bg-red-950/45 text-red-400 border border-red-500/20 rounded-xl font-bold text-xs uppercase tracking-wider transition cursor-pointer">
                                Cancel This Order
                            </button>
                        </form>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
