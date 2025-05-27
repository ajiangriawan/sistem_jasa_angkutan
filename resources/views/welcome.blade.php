<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>PT Balink Sakti Synergy - Jasa Angkutan Batubara</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/alpinejs" defer></script>
    <style>
        /* Base colors and typography */
        body {
            background: linear-gradient(135deg, #222222 0%, #333333 100%);
            color: #F9FAFB;
            font-family: 'Inter', sans-serif;
            overflow-x: hidden;
            position: relative;
            opacity: 0;
            animation: fadeInPage 1.2s ease forwards;
        }

        .full {
            width: 100%;
            text-align: center;
        }

        @keyframes fadeInPage {
            to {
                opacity: 1;
            }
        }

        a,
        button {
            font-family: 'Inter', sans-serif;
        }

        /* Glow and fade slide animations */
        @keyframes fadeSlideUp {
            0% {
                opacity: 0;
                transform: translateY(20px);
            }

            100% {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .animate-fadeSlideUp {
            animation: fadeSlideUp 1s ease forwards;
        }

        .stagger-delay-1 {
            animation-delay: 0.2s;
        }

        .stagger-delay-2 {
            animation-delay: 0.4s;
        }

        .stagger-delay-3 {
            animation-delay: 0.6s;
        }

        .stagger-delay-4 {
            animation-delay: 0.8s;
        }

        /* Glow text - subtle white with faint dark blue shadow */
        .glow-text {
            color: #F9FAFB;
            text-shadow:
                0 0 8px #1e3a8a,
                0 0 12px #1e40af,
                0 0 20px #1e40af;
        }

        .glow-zoom {
            animation: glowZoom 1.2s ease forwards;
            opacity: 0;
            transform: scale(0.9);
        }

        @keyframes glowZoom {
            0% {
                opacity: 0;
                transform: scale(0.9);
                text-shadow: none;
            }

            50% {
                opacity: 1;
                transform: scale(1.05);
                text-shadow:
                    0 0 14px #1e40af,
                    0 0 24px #1e40af,
                    0 0 30px #1e3a8a;
            }

            100% {
                opacity: 1;
                transform: scale(1);
                text-shadow:
                    0 0 9px #1e3a8a,
                    0 0 18px #1e40af;
            }
        }

        /* Button styling with strong bright orange and deep blue accent */
        .btn-primary {
            background-color: #FF5700;
            color: #F9FAFB;
            font-weight: 700;
            padding: 0.75rem 2rem;
            border-radius: 0.75rem;
            box-shadow: 0 8px 20px rgba(255, 87, 0, 0.5);
            transition: all 0.3s ease;
            cursor: pointer;
            border: 2px solid transparent;
            position: relative;
            overflow: hidden;
        }

        .btn-primary:hover {
            background-color: #e04e00;
            box-shadow: 0 15px 30px rgba(224, 78, 0, 0.8);
            transform: translateY(-3px);
            border-color: #1e3a8a;
        }

        /* Button shimmer effect */
        .btn-shimmer::before {
            content: '';
            position: absolute;
            top: 0;
            left: -75%;
            width: 50%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.4), transparent);
            transform: skewX(-20deg);
            transition: left 1.5s linear;
            z-index: 1;
            pointer-events: none;
        }

        .btn-shimmer:hover::before {
            animation: shimmer 1.5s linear infinite;
        }

        @keyframes shimmer {
            0% {
                left: -75%;
            }

            100% {
                left: 125%;
            }
        }

        /* Floating sparks in calm blueish tone for hero */
        .spark {
            position: absolute;
            border-radius: 50%;
            background: rgba(59, 130, 246, 0.4);
            opacity: 0.7;
            animation: floatUp 5s ease-in-out infinite alternate;
            filter: drop-shadow(0 0 6px rgba(29, 78, 216, 0.7));
            z-index: 2;
        }

        .spark1 {
            width: 14px;
            height: 14px;
            left: 12%;
            bottom: 42%;
            animation-delay: 0s;
        }

        .spark2 {
            width: 11px;
            height: 11px;
            left: 28%;
            bottom: 38%;
            animation-delay: 1.5s;
            animation-duration: 6s;
        }

        .spark3 {
            width: 9px;
            height: 9px;
            left: 42%;
            bottom: 47%;
            animation-delay: 0.8s;
            animation-duration: 5.5s;
        }

        .spark4 {
            width: 7px;
            height: 7px;
            left: 65%;
            bottom: 40%;
            animation-delay: 2s;
            animation-duration: 5.2s;
        }

        @keyframes floatUp {
            0% {
                transform: translateY(0) scale(1);
                opacity: 0.7;
            }

            100% {
                transform: translateY(-20px) scale(1.2);
                opacity: 0.3;
            }
        }

        /* Scroll trucks */
        .truck {
            position: fixed;
            bottom: 5.5rem;
            width: 120px;
            /* Ukuran default untuk truk */
            pointer-events: none;
            filter: drop-shadow(0 4px 8px rgba(0, 0, 0, 0.7));
            z-index: 9999;
            transition: transform 0.1s linear;
            opacity: 0;
            animation: truckFadeIn 1.5s ease forwards;
        }

        @keyframes truckFadeIn {
            to {
                opacity: 1;
            }
        }

        /* Kita tidak perlu lagi mengatur 'left' atau 'right' di CSS untuk truk,
           karena akan diatur sepenuhnya oleh JavaScript */

        /* Sections background */
        section.services,
        section.about,
        section.contact {
            background-color: rgba(25, 25, 25, 0.85);
            border-radius: 1rem;
            box-shadow: 0 12px 20px rgba(0, 0, 0, 0.6);
            backdrop-filter: saturate(180%) blur(10px);
            color: #e5e7eb;
            padding-left: 1rem;
            padding-right: 1rem;
        }

        /* Header and nav */
        header {
            background: transparent;
            position: sticky;
            top: 0;
            z-index: 50;
            backdrop-filter: saturate(180%) blur(12px);
            background-color: rgba(25, 25, 25, 0.9);
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.7);
            padding-left: 1rem;
            padding-right: 1rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        nav ul {
            display: flex;
            gap: 1.5rem;
            list-style: none;
            margin: 0;
            padding: 0;
        }

        nav ul li a {
            transition: color 0.3s ease;
            color: #d1d5db;
            font-weight: 600;
            text-decoration: none;
        }

        nav ul li a:hover {
            color: #ff5700;
        }

        /* Form inputs */
        input,
        textarea {
            border-radius: 0.5rem;
            border: 1px solid #444444;
            padding: 0.75rem 1rem;
            font-size: 1rem;
            background-color: #222222;
            color: #f9fafb;
            box-shadow: inset 0 0 6px rgba(255, 87, 0, 0.4);
            transition: box-shadow 0.3s ease, border-color 0.3s ease;
            width: 100%;
        }

        input::placeholder,
        textarea::placeholder {
            color: #d1d5db;
            opacity: 0.7;
        }

        input:focus,
        textarea:focus {
            outline: none;
            box-shadow: 0 0 12px #ff5700;
            border-color: #ff5700;
            background-color: #2c2c2c;
        }

        /* Responsive */
        @media (max-width: 768px) {
            header {
                flex-wrap: wrap;
                justify-content: center;
                gap: 1rem;
            }

            nav ul {
                flex-direction: column;
                gap: 1rem;
                width: 100%;
                align-items: center;
            }

            nav ul li a {
                font-size: 1.1rem;
                font-weight: 700;
            }

            .tengah {
                text-align: center;
            }

            section.services,
            section.about,
            section.contact {
                padding-left: 0.5rem;
                padding-right: 0.5rem;
            }

            main>section:first-child {
                flex-direction: column !important;
                gap: 1.5rem;
            }

            main>section:first-child>div {
                max-width: 100% !important;
            }

            /* Atur lebar truk agar responsif */
            .truck {
                width: 90px !important;
                bottom: 7rem !important;
            }
        }
    </style>
</head>

<body>
    <header class="container mx-auto px-6 py-4">
        <h1 class="text-2xl font-bold tracking-wide cursor-default select-none glow-text tengah">PT Balink Sakti Synergy</h1>
        <div x-data="{ open: false }" class="md:hidden full">
            <button @click="open = !open" class="focus:outline-none">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
                </svg>
            </button>
            <nav class="flex flex-col space-y-2 text-sm" x-show="open" @click.outside="open = false">
                <a href="#beranda" class="hover:text-orange-400">Beranda</a>
                <a href="#services" class="hover:text-orange-400">Layanan</a>
                <a href="#about" class="hover:text-orange-400">Tentang</a>
                <a href="#contact" class="hover:text-orange-400">Kontak</a>
                <a href="/dashboard" class="hover:text-orange-400">Dashboard</a>
            </nav>
        </div>
        <nav class="hidden md:flex">
            <ul>
                <li><a href="#services" class="hover:text-orange-500">Layanan</a></li>
                <li><a href="#about" class="hover:text-orange-500">Tentang</a></li>
                <li><a href="#contact" class="hover:text-orange-500">Kontak</a></li>
                <li><a href="/dashboard" class="hover:text-orange-500">Dashboard</a></li>
            </ul>
        </nav>
    </header>

    <main class="container mx-auto px-6 mt-12 mb-8 max-w-7xl relative z-10">
        <section id="#beranda" class="flex flex-col md:flex-row items-center justify-between min-h-[70vh] gap-12">
            <div class="max-w-lg animate-fadeSlideUp glow-zoom tengah" style="animation-delay: 0.3s; animation-fill-mode: forwards;">
                <h2 class="text-5xl font-extrabold mb-6 glow-text leading-tight">Sistem Informasi <br />Jasa Angkutan Batubara</h2>
                <p class="text-lg mb-8 max-w-md leading-relaxed text-gray-100">
                    Solusi <span class="font-extrabold underline decoration-orange-500/80">terpercaya</span> dan profesional untuk pengangkutan batubara dengan armada khusus dari PT Balink Sakti Synergy.
                </p>
                <a href="#contact" class="btn-primary btn-shimmer">Hubungi Kami</a>
            </div>
            <div class="relative max-w-lg w-full">
                <img src="{{ asset('images/hero.png') }}" alt="Coal Transportation Truck" class="rounded-xl shadow-2xl max-w-full" />
                <div class="spark spark1"></div>
                <div class="spark spark2"></div>
                <div class="spark spark3"></div>
                <div class="spark spark4"></div>
            </div>
        </section>

        <section id="services" class="services mt-24 px-8 py-14 max-w-6xl mx-auto animate-fadeSlideUp">
            <h3 class="text-3xl font-bold mb-10 text-center glow-text">Layanan Kami</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-12 text-gray-300">
                <div class="p-6 rounded-xl shadow-lg bg-gradient-to-tr from-gray-900 via-gray-800 to-gray-900 hover:from-orange-600 hover:to-orange-500 cursor-pointer transition">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-14 h-14 mb-5 text-orange-400 mx-auto" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8 17l4-4 4 4m0-8l-4 4-4-4" />
                    </svg>
                    <h4 class="text-xl font-semibold mb-2 text-center">Jasa Angkutan Batubara</h4>
                    <p class="text-center">Pengangkutan batubara dengan armada truk khusus dan dukungan logistik profesional.</p>
                </div>
                <div class="p-6 rounded-xl shadow-lg bg-gradient-to-tr from-gray-900 via-gray-800 to-gray-900 hover:from-blue-900 hover:to-blue-700 cursor-pointer transition">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-14 h-14 mb-5 text-blue-700 mx-auto" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 10h1l3 9m4-9h6m-10-4h6m6 0h2l4 14H6" />
                    </svg>
                    <h4 class="text-xl font-semibold mb-2 text-center">Logistik Batubara Terintegrasi</h4>
                    <p class="text-center">Solusi logistik dari tambang hingga pengiriman dengan koordinasi terbaik.</p>
                </div>
                <div class="p-6 rounded-xl shadow-lg bg-gradient-to-tr from-gray-900 via-gray-800 to-gray-900 hover:from-orange-600 hover:to-orange-500 cursor-pointer transition">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-14 h-14 mb-5 text-orange-400 mx-auto" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-3-3v6" />
                    </svg>
                    <h4 class="text-xl font-semibold mb-2 text-center">Monitoring Pengangkutan</h4>
                    <p class="text-center">Pantau status pengangkutan batubara secara real-time dengan sistem kami.</p>
                </div>
            </div>
        </section>

        <section id="about" class="about mt-24 px-8 py-14 max-w-4xl mx-auto text-center animate-fadeSlideUp text-gray-300">
            <h3 class="text-3xl font-bold mb-6 text-white glow-text">Tentang PT Balink Sakti Synergy</h3>
            <p>
                PT Balink Sakti Synergy adalah perusahaan jasa angkutan batubara dan logistik yang berkomitmen menyediakan layanan profesional dengan armada khusus dan sistem terintegrasi. Kami mengutamakan keamanan, kecepatan, dan kepuasan pelanggan sebagai prioritas utama.
            </p>
        </section>

        <section id="contact" class="contact mt-24 px-8 py-14 max-w-3xl mx-auto bg-gradient-to-tr from-gray-900 via-gray-800 to-gray-900 rounded-xl shadow-lg animate-fadeSlideUp">
            <h3 class="text-3xl font-bold mb-8 text-white text-center glow-text">Kontak Kami</h3>
            <div class="text-center text-gray-300">
                <p class="mb-4">Untuk pertanyaan atau informasi lebih lanjut, silakan hubungi kami melalui:</p>
                <p class="mb-2">
                    <strong>Email:</strong> <a href="mailto:info@balinksakti.com" class="text-orange-400">info@balinksakti.com</a>
                </p>
                <p>
                    <strong>WhatsApp:</strong> <a href="https://wa.me/+6287813233775" class="text-orange-400">+62878 1323 3775</a>
                </p>
            </div>
        </section>
        <div class="flex flex-row justify-center mt-8">
            
            <img id="truck2" class="truck" src="{{ asset('images/truck2.png') }}" alt="Truck 2" />
            <img class="w-24" src="{{ asset('images/logo.png') }}" alt="Logo" />

        </div>
    </main>

    <footer class="text-center text-gray-400 text-sm py-8 select-none">
        &copy; 2025 PT Balink Sakti Synergy. All rights reserved.
        <p>Aji Angri Awan | Muzayyanah</p>
    </footer>



    <script>
        // Scroll-based horizontal truck animations with varied speeds and directions
        // Scroll-based horizontal truck animations with varied speeds and directions
        const trucks = [{
                el: document.getElementById('truck2'),
                direction: 1, // left to right
                speedMultiplier: 0.7,
                startOffset: -100, // Mulai lebih jauh dari kiri (bisa disesuaikan)
                animationStopPoint: 0.5 // Truk akan mencapai tengah pada 50% scroll
            },
        ];

        function updateTrucksOnScroll() {
            const scrollTop = window.scrollY || window.pageYOffset;
            const docHeight = document.documentElement.scrollHeight - window.innerHeight;
            // Pastikan docHeight tidak nol untuk menghindari pembagian dengan nol
            const scrollProgress = docHeight > 0 ? Math.min(scrollTop / docHeight, 1) : 0;
            const vw = window.innerWidth;

            trucks.forEach(({
                el,
                direction,
                speedMultiplier,
                startOffset,
                animationStopPoint
            }) => {
                const truckWidth = el.offsetWidth; // Dapatkan lebar truk secara dinamis
                const centerPosition = (vw / 2) - (truckWidth / 2); // Posisi tengah layar yang akurat

                let currentX; // Posisi X saat ini dari truk

                // Hitung persentase animasi yang telah dicapai relatif terhadap animationStopPoint
                let animPercentage = Math.min(scrollProgress / animationStopPoint, 1);

                if (direction === 1) { // Truk bergerak dari kiri ke kanan
                    // Targetnya adalah centerPosition dari startOffset
                    const targetX = centerPosition;
                    // Jarak yang harus ditempuh dari startOffset ke centerPosition
                    const totalTravelDistance = targetX - startOffset;
                    currentX = startOffset + (totalTravelDistance * animPercentage * speedMultiplier);

                } else { // Truk bergerak dari kanan ke kiri
                    // Posisi awal dari kanan layar
                    const initialRightOffset = startOffset; // Nilai negatif ini akan menjadi jarak dari tepi kanan
                    const startX = vw - truckWidth - initialRightOffset; // Posisi X awal di luar layar kanan

                    // Targetnya adalah centerPosition dari startX
                    const targetX = centerPosition;
                    // Jarak yang harus ditempuh dari startX ke centerPosition
                    const totalTravelDistance = startX - targetX;

                    currentX = startX - (totalTravelDistance * animPercentage * speedMultiplier);
                }

                el.style.transform = `translateX(${currentX}px)`;
            });
        }

        window.addEventListener('scroll', () => {
            window.requestAnimationFrame(updateTrucksOnScroll);
        });

        window.addEventListener('load', () => {
            updateTrucksOnScroll(); // Panggil saat halaman dimuat
        });

        // Tambahkan event listener untuk merespons perubahan ukuran layar
        window.addEventListener('resize', () => {
            updateTrucksOnScroll(); // Panggil ulang saat ukuran layar berubah
        });
    </script>
</body>

</html>