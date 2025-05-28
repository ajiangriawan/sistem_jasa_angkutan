<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>PT Balink Sakti Synergy - Jasa Angkutan Batubara</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdn.jsdelivr.net/npm/flowbite@3.1.2/dist/flowbite.min.css" rel="stylesheet" />
    <script>
        document.documentElement.classList.add('js')
    </script>

</head>

<body>
    <nav class="bg-white dark:bg-gray-800 fixed w-full z-20 top-0 start-0 border-b border-gray-200 dark:border-gray-600">
        <div class="max-w-screen-xl flex flex-wrap items-center justify-between mx-auto p-4">
            <a href="/" class="flex items-center space-x-3 rtl:space-x-reverse">
                <img src="{{ asset('images/logo.png') }}" class="h-8" alt="Bss Logo">
                <span class="self-center text-2xl font-semibold whitespace-nowrap dark:text-white">PT BSS</span>
            </a>
            <div class="flex md:order-2 space-x-3 md:space-x-0 rtl:space-x-reverse">
                <a href="/dashboard" type="button" class="text-white bg-orange-700 hover:bg-orange-800 focus:ring-4 focus:outline-none focus:ring-orange-300 font-medium rounded-lg text-sm px-4 py-2 text-center dark:bg-orange-600 dark:hover:bg-orange-700 dark:focus:ring-orange-800">
                    Dashboard
                </a>
                <button data-collapse-toggle="navbar-sticky" type="button" class="inline-flex items-center p-2 w-10 h-10 justify-center text-sm text-gray-500 rounded-lg md:hidden hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-gray-200 dark:text-gray-400 dark:hover:bg-gray-700 dark:focus:ring-gray-600" aria-controls="navbar-sticky" aria-expanded="false">
                    <span class="sr-only">Open main menu</span>
                    <svg class="w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 17 14">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M1 1h15M1 7h15M1 13h15" />
                    </svg>
                </button>
            </div>
            <div class="items-center justify-between hidden w-full md:flex md:w-auto md:order-1" id="navbar-sticky">
                <ul class="flex flex-col p-4 md:p-0 mt-4 font-medium border border-gray-100 rounded-lg bg-gray-50 md:space-x-8 rtl:space-x-reverse md:flex-row md:mt-0 md:border-0 md:bg-white dark:bg-gray-800 md:dark:bg-gray-800 dark:border-gray-700">
                    <li>
                        <a href="#beranda" class="block py-2 px-3 text-white bg-orange-700 rounded-sm md:bg-transparent md:text-orange-700 md:p-0 md:dark:text-orange-500" aria-current="page">
                            Beranda
                        </a>
                    </li>
                    <li>
                        <a href="#layanan" class="block py-2 px-3 text-gray-900 rounded-sm hover:bg-gray-100 md:hover:bg-transparent md:hover:text-orange-700 md:p-0 md:dark:hover:text-orange-500 dark:text-white dark:hover:bg-gray-700 dark:hover:text-white md:dark:hover:bg-transparent dark:border-gray-700">
                            Layanan
                        </a>
                    </li>
                    <li>
                        <a href="#tentang" class="block py-2 px-3 text-gray-900 rounded-sm hover:bg-gray-100 md:hover:bg-transparent md:hover:text-orange-700 md:p-0 md:dark:hover:text-orange-500 dark:text-white dark:hover:bg-gray-700 dark:hover:text-white md:dark:hover:bg-transparent dark:border-gray-700">
                            Tentang
                        </a>
                    </li>
                    <li>
                        <a href="#kontak" class="block py-2 px-3 text-gray-900 rounded-sm hover:bg-gray-100 md:hover:bg-transparent md:hover:text-orange-700 md:p-0 md:dark:hover:text-orange-500 dark:text-white dark:hover:bg-gray-700 dark:hover:text-white md:dark:hover:bg-transparent dark:border-gray-700">
                            Kontak
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <section id="beranda" class="h-screen bg-cover bg-center bg-no-repeat bg-[url('{{ asset('images/hero1.png') }}')] bg-gray-700 bg-blend-multiply mt-16">
        <div class="px-4 mx-auto max-w-screen-xl text-center py-24 lg:py-56 delay-[200ms] duration-[400ms] taos:translate-y-[-100%] taos:opacity-0" data-taos-offset="500"">
            <h1 class=" mb-4 text-4xl font-extrabold tracking-tight leading-none text-white md:text-5xl lg:text-6xl">PT Balink Sakti Synergy</h1>
            <p class="mb-8 text-lg font-normal text-gray-300 lg:text-xl sm:px-16 lg:px-48">Sistem Informasi Jasa Angkutan Batu Bara solusi terpercacya dan profesional untuk pengangkutan batubara dengan armada khusus dari PT Balink Sakti Synergy. </p>
            <div class="flex flex-col space-y-4 sm:flex-row sm:justify-center sm:space-y-0">
                <a href="https://wa.me/+6287813233775" class="inline-flex justify-center items-center py-3 px-5 text-base font-medium text-center text-white rounded-lg bg-orange-700 hover:bg-orange-800 focus:ring-4 focus:ring-orange-300 dark:focus:ring-orange-900">
                    Hubungi Kami
                    <svg class="w-3.5 h-3.5 ms-2 rtl:rotate-180" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 10">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M1 5h12m0 0L9 1m4 4L9 9" />
                    </svg>
                </a>
                <a href="#" class="inline-flex justify-center hover:text-gray-900 items-center py-3 px-5 sm:ms-4 text-base font-medium text-center text-white rounded-lg border border-white hover:bg-gray-100 focus:ring-4 focus:ring-gray-400">
                    Layanan
                </a>
            </div>
        </div>
    </section>
    <main class="container mx-auto px-6 mb-8 max-w-7xl relative z-10">
        <section id="layanan" class="mt-2 px-8 py-14 max-w-6xl mx-auto">
            <h3 class="text-3xl font-bold mb-10 text-center glow-text">Layanan Kami</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-12 text-black">
                <div class="p-6 rounded-xl shadow-lg bg-orange-300 hover:bg-white hover:border-4 hover:border-orange-300 cursor-pointer transition delay-[200ms] duration-[600ms] taos:translate-y-[-100%] taos:opacity-0" data-taos-offset="500">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="black" class="w-14 h-14 mb-5 text-orange-400 mx-auto">
                        <path d="M3.375 4.5C2.339 4.5 1.5 5.34 1.5 6.375V13.5h12V6.375c0-1.036-.84-1.875-1.875-1.875h-8.25ZM13.5 15h-12v2.625c0 1.035.84 1.875 1.875 1.875h.375a3 3 0 1 1 6 0h3a.75.75 0 0 0 .75-.75V15Z" />
                        <path d="M8.25 19.5a1.5 1.5 0 1 0-3 0 1.5 1.5 0 0 0 3 0ZM15.75 6.75a.75.75 0 0 0-.75.75v11.25c0 .087.015.17.042.248a3 3 0 0 1 5.958.464c.853-.175 1.522-.935 1.464-1.883a18.659 18.659 0 0 0-3.732-10.104 1.837 1.837 0 0 0-1.47-.725H15.75Z" />
                        <path d="M19.5 19.5a1.5 1.5 0 1 0-3 0 1.5 1.5 0 0 0 3 0Z" />
                    </svg>
                    <h4 class="text-xl font-bold mb-2 text-center">Jasa Angkutan Batubara</h4>
                    <p class="text-center">
                        Pengangkutan batubara dengan armada truk khusus dan dukungan logistik profesional.</p>
                </div>
                <div class="p-6 rounded-xl shadow-lg bg-orange-300 hover:bg-white hover:border-4 hover:border-orange-300 cursor-pointer transition delay-[400ms] duration-[600ms] taos:translate-y-[-100%] taos:opacity-0" data-taos-offset="500">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="black" class="w-14 h-14 mb-5 text-orange-400 mx-auto">
                        <path fill-rule="evenodd" d="M12 5.25c1.213 0 2.415.046 3.605.135a3.256 3.256 0 0 1 3.01 3.01c.044.583.077 1.17.1 1.759L17.03 8.47a.75.75 0 1 0-1.06 1.06l3 3a.75.75 0 0 0 1.06 0l3-3a.75.75 0 0 0-1.06-1.06l-1.752 1.751c-.023-.65-.06-1.296-.108-1.939a4.756 4.756 0 0 0-4.392-4.392 49.422 49.422 0 0 0-7.436 0A4.756 4.756 0 0 0 3.89 8.282c-.017.224-.033.447-.046.672a.75.75 0 1 0 1.497.092c.013-.217.028-.434.044-.651a3.256 3.256 0 0 1 3.01-3.01c1.19-.09 2.392-.135 3.605-.135Zm-6.97 6.22a.75.75 0 0 0-1.06 0l-3 3a.75.75 0 1 0 1.06 1.06l1.752-1.751c.023.65.06 1.296.108 1.939a4.756 4.756 0 0 0 4.392 4.392 49.413 49.413 0 0 0 7.436 0 4.756 4.756 0 0 0 4.392-4.392c.017-.223.032-.447.046-.672a.75.75 0 0 0-1.497-.092c-.013.217-.028.434-.044.651a3.256 3.256 0 0 1-3.01 3.01 47.953 47.953 0 0 1-7.21 0 3.256 3.256 0 0 1-3.01-3.01 47.759 47.759 0 0 1-.1-1.759L6.97 15.53a.75.75 0 0 0 1.06-1.06l-3-3Z" clip-rule="evenodd" />
                    </svg>

                    <h4 class="text-xl font-bold mb-2 text-center">Logistik Batubara Terintegrasi</h4>
                    <p class="text-center">
                        Solusi logistik dari tambang hingga pengiriman dengan koordinasi terbaik.</p>
                </div>
                <div class="p-6 rounded-xl shadow-lg bg-orange-300 hover:bg-white hover:border-4 hover:border-orange-300 cursor-pointer transition delay-[600ms] duration-[600ms] taos:translate-y-[-100%] taos:opacity-0" data-taos-offset="500">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="black" class="w-14 h-14 mb-5 text-orange-400 mx-auto">
                        <path fill-rule="evenodd" d="M2.25 5.25a3 3 0 0 1 3-3h13.5a3 3 0 0 1 3 3V15a3 3 0 0 1-3 3h-3v.257c0 .597.237 1.17.659 1.591l.621.622a.75.75 0 0 1-.53 1.28h-9a.75.75 0 0 1-.53-1.28l.621-.622a2.25 2.25 0 0 0 .659-1.59V18h-3a3 3 0 0 1-3-3V5.25Zm1.5 0v7.5a1.5 1.5 0 0 0 1.5 1.5h13.5a1.5 1.5 0 0 0 1.5-1.5v-7.5a1.5 1.5 0 0 0-1.5-1.5H5.25a1.5 1.5 0 0 0-1.5 1.5Z" clip-rule="evenodd" />
                    </svg>

                    <h4 class="text-xl font-bold mb-2 text-center">Monitoring Pengangkutan</h4>
                    <p class="text-center">
                        Pantau status pengangkutan batubara secara real-time dengan sistem kami.</p>
                </div>
            </div>
        </section>
        <section id="tentang" class="bg-gray-800 rounded-xl mt-2 px-8 py-14 max-w-6xl mx-auto text-white text-center delay-[300ms] duration-[600ms] taos:scale-[0.6] taos:opacity-0" data-taos-offset="400">
            <h3 class="text-3xl font-bold mb-6  glow-text">Tentang PT Balink Sakti Synergy</h3>
            <p>
                PT Balink Sakti Synergy adalah perusahaan jasa angkutan batubara dan logistik yang berkomitmen menyediakan layanan profesional dengan armada khusus dan sistem terintegrasi. Kami mengutamakan keamanan, kecepatan, dan kepuasan pelanggan sebagai prioritas utama.
            </p>
        </section>
        <section id="kontak" class="bg-gray-800 rounded-xl mt-2 px-8 py-14 max-w-6xl mx-auto text-white text-center delay-[300ms] duration-[600ms] taos:scale-[0.6] taos:opacity-0" data-taos-offset="400">
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

    </main>
    <footer class="bg-white shadow-sm dark:bg-gray-800">
        <div class="w-full max-w-screen-xl mx-auto p-4 md:py-8">
            <div class="sm:flex sm:items-center sm:justify-center">
                <a href="/" class="flex items-center mb-4 sm:mb-0 space-x-3 rtl:space-x-reverse">
                    <img src="{{ asset('images/logo.png') }}" class="h-8" alt="Bss Logo" />
                    <span class="self-center text-2xl font-semibold whitespace-nowrap dark:text-white">PT Balink Sakti Synergy</span>
                </a>
            </div>
            <hr class="my-6 border-gray-200 sm:mx-auto dark:border-gray-700 lg:my-8" />
            <span class="block text-sm text-gray-500 sm:text-center dark:text-gray-400">© 2025 <a href="/" class="hover:underline">PT Balink Sakti Synergy</a>. All Rights Reserved.</span>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/flowbite@3.1.2/dist/flowbite.min.js"></script>
    <script src="https://unpkg.com/taos@1.0.5/dist/taos.js"></script>
</body>

</html>