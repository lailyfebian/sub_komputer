@extends('layouts.generic')

@section('head')
    @if ($page->title ?? '')
        <title>{{ $page->title ?: $keyword->name ?? 'Les Privat SD, Bimbel SD, Guru Privat SD' }}
        </title>
        <link rel="canonical" href="{{ url()->current() }}" />
        <meta name="description"
            content="{{ $page->meta_description ?: $keyword->name ?: 'Les Privat SD yang membantu anak memahami materi pelajaran, mengerjakan tugas sekolah, dan meningkatkan prestasi belajar melalui pendampingan guru privat.' }}" />
        <meta name="keywords" content="{{ $keyword->name ?: '' }} {{ $region->name ?? '' }}">
        <meta property="og:locale" content="id" />
        <meta property="og:type" content="article" />
        <meta property="og:title" content="{{ $page->title ?: $keyword->name ?: '' }}" />
        <meta property="og:description"
            content="{{ $page->meta_description ?: $keyword->name ?: 'Les Privat SD yang membantu anak memahami materi pelajaran, mengerjakan tugas sekolah, dan meningkatkan prestasi belajar melalui pendampingan guru privat.' }}" />
        <meta property="og:site_name" content="{{ $keyword->name ?? '' }}" />
        <meta property="og:image" content="{{ asset('images/image-header-homepage.webp') }}" />
        <meta property="og:image:width" content="1280" />
        <meta property="og:image:height" content="720" />
        <meta property="og:image:type" content="image/webp" />
        <meta name="twitter:card" content="summary_large_image">
        <meta name="twitter:title" content="{{ $page->title ?? ($keyword->name ?? '') }}">
        <meta name="twitter:description"
            content="{{ $page->meta_description ?: $keyword->name ?: 'Les Privat SD yang membantu anak memahami materi pelajaran, mengerjakan tugas sekolah, dan meningkatkan prestasi belajar melalui pendampingan guru privat.' }}">
        <meta name="twitter:image" content="{{ asset('images/image-header-homepage.webp') }}">
        <meta name="twitter:url" content="{{ url()->current() }}">
    @else
        <title>{{ $page->title ?: $keyword->name ?? 'Les Privat SD, Bimbel SD, Guru Privat SD' }}
            di
            {{ $region->name ?? '' }}</title>
        <link rel="canonical" href="{{ url()->current() }}" />
        <meta name="description"
            content="{{ $page->meta_description ?: $keyword->name ?: 'Les Privat SD yang membantu anak memahami materi pelajaran, mengerjakan tugas sekolah, dan meningkatkan prestasi belajar melalui pendampingan guru privat.' }} di {{ $region->name ?? '' }}" />
        <meta name="keywords" content="{{ $keyword->name ?? '' }} di {{ $region->name ?? '' }}">
        <meta property="og:locale" content="id" />
        <meta property="og:type" content="article" />
        <meta property="og:title" content="{{ $page->title ?: $keyword->name ?: '' }} di {{ $region->name ?? '' }}" />
        <meta property="og:description"
            content="{{ $page->meta_description ?: $keyword->name ?: 'Les Privat SD yang membantu anak memahami materi pelajaran, mengerjakan tugas sekolah, dan meningkatkan prestasi belajar melalui pendampingan guru privat.' }} di {{ $region->name ?? '' }}" />
        <meta property="og:site_name" content="{{ $keyword->name ?? '' }}" />
        <meta property="og:image" content="{{ asset('images/image-header-homepage.webp') }}" />
        <meta property="og:image:width" content="1280" />
        <meta property="og:image:height" content="720" />
        <meta property="og:image:type" content="image/webp" />
        <meta name="twitter:card" content="summary_large_image">
        <meta name="twitter:title" content="{{ $page->title ?? ($keyword->name ?? '') }} di {{ $region->name ?? '' }}">
        <meta name="twitter:description"
            content="{{ $page->meta_description ?: $keyword->name ?: 'Les Privat SD yang membantu anak memahami materi pelajaran, mengerjakan tugas sekolah, dan meningkatkan prestasi belajar melalui pendampingan guru privat.' }} di {{ $region->name ?? '' }}">
        <meta name="twitter:image" content="{{ asset('images/image-header-homepage.webp') }}">
        <meta name="twitter:url" content="{{ url()->current() }}">
    @endif
@endsection

@php
    $tutorQualityIcons = ['1.webp', '2.webp', '3.webp', '4.webp'];

    $serviceFeaturesIcons = [
        'tutor-pilihan.webp',
        'laporan-belajar.webp',
        'belajar-sesuai-kebutuhan.webp',
        'easy-payment.webp',
        'cs-fast-respon.webp',
        'keberhasilan-siswa.webp',
        'hemat-waktu.webp',
        'hemat-tenaga.webp',
        'hemat-biaya.webp',
    ];

    $benefitIcons = [
        '<svg viewBox="0 0 24 24" aria-hidden="true">
            <path d="M3 5.5C6.5 4.5 9.5 5.3 12 7v13
                C9.5 18.3 6.5 17.5 3 18.5z" />
            <path d="M21 5.5C17.5 4.5 14.5 5.3 12 7v13
                C14.5 18.3 17.5 17.5 21 18.5z" />
            <path d="M6 9l3 .5M6 12l3 .5M15 9l3-.5M15 12l3-.5" />
        </svg>',
        '<svg viewBox="0 0 24 24" aria-hidden="true">
            <circle cx="12" cy="12" r="9" />
            <circle cx="12" cy="12" r="5.5" />
            <circle cx="12" cy="12" r="2" />
            <path d="M15.5 8.5L21 3M16 3h5v5" />
        </svg>',
        '<svg viewBox="0 0 24 24" aria-hidden="true">
            <path d="M12 2.8l2.8 5.7 6.3.9-4.6 4.5
                1.1 6.3-5.6-3-5.6 3 1.1-6.3
                -4.6-4.5 6.3-.9z" />
        </svg>',
        '<svg viewBox="0 0 24 24" aria-hidden="true">
            <path d="M6 3h8l5 5v13H6z" />
            <path d="M14 3v5h5M9 12h7M9 15h7M9 18h4" />
        </svg>',
    ];

    $testimonialIcons = ['2.webp', '1.webp', '3.webp', '5.webp', '4.webp', '6.webp'];

@endphp

@section('content')

    @if ($data['hero'] ?? '')
        <div class="header-beranda">
            <div class="container d-lg-flex align-items-lg-center justify-content-between text-center">
                <div class="text-header">
                    <div class="text-center">
                        <h1 class="fw-bold mb-4">
                            {{ $data['hero']['title'] ?? '' }}
                        </h1>
                    </div>
                    <div class="text-white hero-content mb-3" style="font-family: 'Poppins', sans-serif;">
                        {!! $data['hero']['content'] ?? '' !!}</div>
                    <a href="https://wa.privatnusantara.com/?kebutuhan={{ $keyword->name ?: '' }} {{ $region->name ?? '' }}"
                        class="btn fw-bold">
                        Konsultasi Gratis
                    </a>
                </div>
                <div class="image-header">
                    <div>
                        <img src="{{ asset('images/image-header-homepage.webp') }}" id="header-image-beranda" class="d-block m-auto"
                            alt="{{ $data['hero']['title'] ?? '' }}" />
                    </div>
                </div>
            </div>
        </div>
    @endif

    @if ($data['tutor-quality'] ?? '')
        <div class="keunggulan">
            <div class="container">
                <div class="count d-lg-flex justify-content-evenly align-items-center text-center">
                    <div>
                        <span class="angka" id="kepuasan"></span><span>/10</span>
                        <p class="fw-bold">Tingkat Kepuasan Siswa</p>
                    </div>
                    <div>
                        <span class="angka" id="tutor"></span><span>+</span>
                        <p class="fw-bold">Tutor di Privat Nusantara</p>
                    </div>
                    <div>
                        <span class="angka" id="siswa_berhasil"></span><span>%</span>
                        <p class="fw-bold">Siswa Berhasil</p>
                    </div>
                    <div>
                        <span class="angka" id="layanan_kota"></span>
                        <p class="fw-bold">Layanan kota besar di Indonesia!</p>
                    </div>
                </div>
                <div class="list-keunggulan d-lg-flex align-items-center">
                    <div class="ps-lg-3">
                        <h2 class="mt-4 mt-lg-0 text-center fw-bold">{{ $data['tutor-quality']['title'] ?? '' }}</h2>
                        <div class="text-center fs-lg-5 mt-lg-4 mt-2">{!! $data['tutor-quality']['content'] ?? '' !!}</div>
                        <div class="d-grid">
                            @foreach (array_values($data['tutor-quality']['items']) as $i => $item_tutor_quality)
                                <div class="card-section">
                                    @php
                                        $iconIndex = $i % count($tutorQualityIcons);
                                        $iconPath = asset('images/icon/benefit/' . $tutorQualityIcons[$iconIndex]);
                                    @endphp

                                    <img src="{{ $iconPath }}" alt="Icon {{ $item_tutor_quality['title'] ?? '' }}">

                                    <h3 class="mt-3 fw-bold fs-lg-4">
                                        {{ $item_tutor_quality['title'] ?? '' }}
                                    </h3>

                                    <div>{!! $item_tutor_quality['description'] !!}</div>
                                </div>
                            @endforeach

                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    @if ($region ?? '')
        <div class="container py-3">
            <p>Melayani {{ $page->title ?? '' }} di {{ $region->name ?? '' }} meliputi wilayah
                {{ $region->address ?? '' }} & sekitarnya</p>
        </div>
    @endif

    @if ($data['benefit'] ?? '')
        <div class="benefit-section">
            <div class="benefit-section-container">
                <div class="benefit-section-image">
                    <img src="{{ asset('images/benefit-image.webp') }}" alt="{{ $data['benefit']['title'] ?? '' }}"
                        loading="lazy">
                </div>
                <div class="benefit-section-content">
                    <h2>{{ $data['benefit']['title'] ?? '' }}</h2>
                    <div class="benefit-section-description">
                        {!! $data['benefit']['content'] ?? '' !!}
                    </div>
                    <div class="benefit-section-grid">
                        @foreach (array_values($data['benefit']['items']) as $i => $item_benefit)
                            <div class="benefit-item">
                                @php
                                    $iconIndex = $i % count($benefitIcons);
                                @endphp
                                <div class="benefit-item-icon">
                                    {!! $benefitIcons[$iconIndex] ?? '' !!}
                                </div>
                                <div class="benefit-item-content">
                                    <p class="title mb-0">{{ $item_benefit['title'] ?? '' }}</p>
                                    {!! $item_benefit['description'] !!}
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    @endif

    @if ($data['cta'] ?? '')
        <div style="background-color: #f9f9fb;">
            <div class="container py-5 text-center">
                <h2 class="text-center fw-bold">{{ $data['cta']['title'] ?? '' }}</h2>
                <div class="text-start">{!! $data['cta']['content'] ?? '' !!}</div>
            </div>
        </div>
    @endif

    @if ($data['description'] ?? '')
        <div style="background-color: #f1f9f5;">
            <div class="container py-5 text-center">
                <h2 class="text-center fw-bold">{{ $data['description']['title'] ?? '' }}</h2>
                <div class="description-section text-start">{!! $data['description']['content'] ?? '' !!}</div>
            </div>
        </div>
    @endif

    @if ($data['curriculum'] ?? '')
        <div class="kurikulum">
            <div class="container">
                <h2 class="text-center fw-bold">{{ $data['curriculum']['title'] ?? '' }}</h2>
                <p class="text-center mt-3">{!! $data['curriculum']['content'] ?? '' !!}</p>
                <div class="list-kurikulum">
                    <div class="d-grid text-center">
                        @foreach (array_values($data['curriculum']['items'] ?? []) as $i => $item_curriculum)
                            <div class="card-section">
                                <strong>{{ $item_curriculum['title'] }}</strong>
                                @if (!empty($item_curriculum['title']))
                                    <div class="list-unstyled mt-3 text-start">
                                        {!! $item_curriculum['description'] !!}
                                    </div>
                                @endif

                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    @endif

    @if ($data['description2'] ?? '')
        <div style="background-color: #f1f9f5;">
            <div class="container py-5 text-center">
                <h2 class="text-center fw-bold">{{ $data['description2']['title'] ?? '' }}</h2>
                <div class="description-section text-start">{!! $data['description2']['content'] ?? '' !!}</div>
            </div>
        </div>
    @endif

    @if ($data['cta2'] ?? '')
        <div class="cta-page">
            <div class="container d-lg-flex d-block align-items-center">
                <div class="text-cta text-center">
                    <h2 class="fs-lg-2 fw-bold">{{ $data['cta2']['title'] ?? '' }}</h2>
                    <div class="mt-4">{!! $data['cta2']['content'] ?? '' !!}</div>
                    @if ($region)
                        <p>Layanan kami mencakup area {{ $region->alamat }}</p>
                    @endif
                    <div class="button-cta mt-lg-4">
                        <a href="https://wa.privatnusantara.com/?kebutuhan={{ $keyword->name ?: '' }} {{ $region->name ?? '' }}"
                            class="btn text-white">
                            Konsultasi Sekarang
                        </a>
                    </div>
                </div>
                <div class="image-cta">
                    <img src="{{ asset('images/cta-pn.webp') }}" alt="{{ $data['cta2']['title'] ?? '' }}">
                </div>
            </div>
        </div>
    @endif

    @if ($data['product'] ?? '')
        <div class="list-produk text-center">
            <div class="container">
                <h2 class="text-center fw-bold mb-lg-3">{{ $data['product']['title'] ?? '' }}</h2>
                {!! $data['product']['content'] ?? '' !!}
                <div class="card-wrap">
                    <div class="card-section">
                        <img src="{{ asset('images/produk/versi-baru/1.webp') }}" alt="Les Privat Calistung">
                        <div class="button-produk">
                            <div class="button text-center">
                                <a href="https://wa.privatnusantara.com/?kebutuhan=Les Privat Calistung"
                                    class="btn d-inline-block m-auto" aria-label="Les Privat Calistung">
                                    Konsultasi Calistung
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="card-section">
                        <img src="{{ asset('images/produk/versi-baru/2.webp') }}" alt="Les Privat SD">
                        <div class="button-produk">
                            <div class="button text-center">
                                <a href="https://wa.privatnusantara.com/?kebutuhan=Les Privat SD"
                                    class="btn d-inline-block m-auto" aria-label="Les Privat SD">
                                    Konsultasi SD
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="card-section">
                        <img src="{{ asset('images/produk/versi-baru/3.webp') }}" alt="Les Privat SMP">
                        <div class="button-produk">
                            <div class="button text-center">
                                <a href="https://wa.privatnusantara.com/?kebutuhan=Les Privat SMP"
                                    class="btn d-inline-block m-auto" aria-label="Les Privat SMP">
                                    Konsultasi SMP
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="card-section">
                        <img src="{{ asset('images/produk/versi-baru/4.webp') }}" alt="Les Privat SMA">
                        <div class="button-produk">
                            <div class="button text-center">
                                <a href="https://wa.privatnusantara.com/?kebutuhan=Les Privat SMA"
                                    class="btn d-inline-block m-auto" aria-label="Les Privat SMA">
                                    Konsultasi SMA
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="card-section">
                        <img src="{{ asset('images/produk/versi-baru/5.webp') }}" alt="Les Privat TKA">
                        <div class="button-produk">
                            <div class="button text-center">
                                <a href="https://wa.privatnusantara.com/?kebutuhan=Les Privat TKA"
                                    class="btn d-inline-block m-auto" aria-label="Les Privat TKA">
                                    Konsultasi TKA
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="card-section">
                        <img src="{{ asset('images/produk/versi-baru/6.webp') }}" alt="Les Privat OSN / IMO">
                        <div class="button-produk">
                            <div class="button text-center">
                                <a href="https://wa.privatnusantara.com/?kebutuhan=Les Privat OSN / IMO"
                                    class="btn d-inline-block m-auto" aria-label="Les Privat OSN / IMO">
                                    Konsultasi OSN / IMO
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <a href="https://privatnusantara.com/produk" class="btn mt-lg-4 text-white" id="button-lihat-produk">
                Lihat Semua Produk
            </a>
        </div>
    @endif

    @if ($data['description3'] ?? '')
        <div style="background-color: #f1f9f5;">
            <div class="container py-5 text-center">
                <h2 class="text-center fw-bold">{{ $data['description3']['title'] ?? '' }}</h2>
                <div class="description-section text-start">{!! $data['description3']['content'] ?? '' !!}</div>
            </div>
        </div>
    @endif
    
    @if ($data['testimonial'] ?? '')
        <div class="testimoni">
            <div class="container mt-lg-5 mt-4 mb-lg-4 mb-0">
                <h2 class="text-center mt-lg-4 pt-3">{{ $data['testimonial']['title'] ?? '' }}</h2>
                <div class="text-center">{!! $data['testimonial']['content'] ?? '' !!}</div>
                <div class="carousel-container">
                    <button class="carousel-arrow prev">&#10094;</button>

                    <div class="carousel-wrapper">
                        <div class="carousel-track">

                            @foreach (array_values($data['testimonial']['items']) as $i => $item)
                                @php
                                    $iconIndex = $i % count($testimonialIcons);
                                    $iconPath = asset('images/testimoni/' . $testimonialIcons[$iconIndex]);
                                @endphp

                                <div class="testimonial">
                                    <p>{!! $item['description'] !!}</p>
                                    <div class="testimonial-footer">
                                        <img src="{{ $iconPath }}" alt="Testimoni {{ $item['title'] ?? '' }}"
                                            class="testimonial-image">
                                        <p>- {{ $item['title'] ?? '' }}</p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    <button class="carousel-arrow next">&#10095;</button>
                    <div class="carousel-dots"></div>
                </div>
            </div>
        </div>
    @endif

    <div class="list-kota my-lg-5 my-4">
        <div class="container">
            @if ($data['coverage-area'] ?? '')
                <h2 class="text-center fw-bold">{{ $data['coverage-area']['title'] ?? '' }}</h2>
            @else
                <h2 class="text-center fw-bold">Layanan Les Privat SD di Berbagai Kota</h2>
            @endif
            {!! $data['coverage-area']['content'] ?? '' !!}
            <div class="col-12 mt-4">
                <div class="d-grid">

                    @php
                        $keywordSlug = $keyword->slug ?? '';
                        $list = collect();
                        $urlPrefix = $keywordSlug;

                        if ($regionType === 'kota') {
                            $list = \App\Models\Region::where('type', 'kota')
                                ->where('id', '!=', $region->id)
                                ->limit(24)
                                ->get();
                            $urlPrefix .= '/kota';
                        } elseif ($regionType === 'kota-besar') {
                            $list = \App\Models\Region::where('type', 'kota-besar')
                                ->where('id', '!=', $region->id)
                                ->limit(24)
                                ->get();
                            $urlPrefix .= '/kota-besar';
                        } elseif ($regionType === 'kecamatan') {
                            if ($region->parent) {
                                $list = \App\Models\Region::where('type', 'kecamatan')
                                    ->where('parent_id', $region->parent_id)
                                    ->where('id', '!=', $region->id)
                                    ->limit(24)
                                    ->get();
                                $urlPrefix .= '/kecamatan';
                            }

                            if ($list->isEmpty()) {
                                $list = \App\Models\Region::where('type', 'kota')->limit(32)->get();
                                $urlPrefix = $keywordSlug . '/kota';
                            }
                        } elseif (in_array($regionType, ['kelurahan', 'desa'])) {
                            $city = $region->parent?->parent;

                            if ($city) {
                                $list = \App\Models\Region::where('type', 'kecamatan')
                                    ->where('parent_id', $city->id)
                                    ->limit(24)
                                    ->get();
                                $urlPrefix .= '/kecamatan';
                            }

                            if ($list->isEmpty()) {
                                $list = \App\Models\Region::where('type', 'kota')->limit(32)->get();
                                $urlPrefix = $keywordSlug . '/kota';
                            }
                        } else {
                            $list = \App\Models\Region::where('type', 'kota')->limit(32)->get();
                            $urlPrefix .= '/kota';
                        }

                        $list = $list->take(24);
                    @endphp


                    @if ($list->count())
                        @foreach ($list as $item)
                            <p>
                                <a class="text-decoration-none d-block m-auto text-start"
                                    href="{{ url($urlPrefix . '/' . $item->slug . '/terbaik') }}">
                                    {{ $item->name ?? 'Nama tidak ditemukan' }}
                                </a>
                            </p>
                        @endforeach
                    @else
                        <p>Data tidak ditemukan</p>
                    @endif

                </div>
            </div>
        </div>
    </div>

    @if ($specificAddress ?? '')
        <div class="container py-5">
            @php
                $specificAddress = $region->specificAddresses->where('category', 'sd_negeri')->first();
            @endphp

            <h3 class="text-center mt-lg-4 pt-3 fw-bold">{{ $data['school-address']['title'] ?? '' }}</h3>
            @foreach ($specificAddress->schoolAddresses as $school)
                <ul class="mt-3">
                    <li>{{ $school->address }}</li>
                </ul>
            @endforeach
        </div>
    @endif

    @if ($data['faq'] ?? '')
        <div class="faq-section">
            <div class="faq-section-container">

                <div class="faq-section-header">
                    <h2>{{ $data['faq']['title'] ?? 'Pertanyaan yang Sering Diajukan' }}</h2>
                    <p>{!! $data['faq']['content'] ?? 'Temukan jawaban seputar layanan les privat, proses belajar, tutor, dan pilihan jadwal untuk anak Anda.' !!}
                    </p>
                </div>

                <div class="faq-section-content">

                    <div class="faq-section-visual">
                        <div class="faq-section-image">
                            <img src="{{ asset('images/faq-image.webp') }}"
                                alt="{{ $data['faq']['title'] ?? 'Pertanyaan yang Sering Diajukan' }}" loading="lazy"
                                decoding="async">
                        </div>
                    </div>

                    <div class="faq-section-list">
                        @foreach (array_values($data['faq']['items']) as $i => $item_faq)
                            <div class="faq-section-item {{ $i === 0 ? 'active' : '' }}">
                                <button class="faq-section-question" type="button"
                                    aria-expanded="{{ $i === 0 ? 'true' : 'false' }}">
                                    <span class="faq-section-number">
                                        {{ str_pad($i + 1, 2, '0', STR_PAD_LEFT) }}
                                    </span>

                                    <span class="faq-section-question-text">
                                        {{ $item_faq['question'] ?? ($item_faq['title'] ?? '') }}
                                    </span>

                                    <span class="faq-section-arrow" aria-hidden="true"></span>
                                </button>

                                <div class="faq-section-answer">
                                    <div class="faq-section-answer-inner">
                                        <p>{!! $item_faq['description'] ?? '' !!}</p>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                </div>
            </div>
        </div>
    @endif

    @if ($data['cta3'] ?? '')
        <div style="background-color: #f9f9fb;">
            <div>
                <div class="cta2 container text-center py-5">
                    <h2 class="text-center fw-bold">{{ $data['cta3']['title'] ?? '' }}</h2>
                    {!! $data['cta3']['content'] ?? '' !!}
                    <a href="https://wa.privatnusantara.com/?kebutuhan={{ $keyword->name ?: '' }} {{ $region->name ?? '' }}"
                        class="btn fw-bold"
                        style="background-color: #045323; color: #fff; border-radius: 25px; padding: 7px 15px;">
                        Mulai Konsultasi Gratis
                    </a>
                </div>
            </div>
        </div>
    @endif
@endsection


@push('scripts')
    <script>
        (function() {

            const track = document.querySelector(".carousel-track");
            if (!track) return;
            let originalCards = Array.from(track.children);

            function getSlidesPerView() {
                if (window.innerWidth <= 600) return 1;
                if (window.innerWidth <= 900) return 2;
                return 3;
            }

            function duplicateUntilFull() {
                const slidesPerView = getSlidesPerView();
                const target = slidesPerView * 2;

                if (originalCards.length < target) {
                    const cloneNeeded = target - originalCards.length;

                    for (let i = 0; i < cloneNeeded; i++) {
                        let clone = originalCards[i % originalCards.length].cloneNode(true);
                        track.appendChild(clone);
                    }
                }
            }

            duplicateUntilFull();

            const cards = Array.from(track.children);
            const prevButton = document.querySelector('.carousel-arrow.prev');
            const nextButton = document.querySelector('.carousel-arrow.next');
            const dotsNav = document.querySelector('.carousel-dots');

            let pageIndex = 0;

            function renderDots() {
                if (!dotsNav) return;
                dotsNav.innerHTML = "";

                const totalPages = Math.ceil(cards.length / getSlidesPerView());

                for (let i = 0; i < totalPages; i++) {
                    const dot = document.createElement("button");
                    if (i === pageIndex) dot.classList.add("active");
                    dot.addEventListener("click", () => moveToPage(i));
                    dotsNav.appendChild(dot);
                }
            }

            function updateCarousel() {
                const wrapper = document.querySelector(".carousel-wrapper");
                if (!wrapper) return;

                const width = wrapper.getBoundingClientRect().width;

                track.style.transform = `translateX(-${pageIndex * width}px)`;

                renderDots();
            }

            function moveToPage(i) {
                const totalPages = Math.ceil(cards.length / getSlidesPerView());
                pageIndex = i;

                if (pageIndex >= totalPages) pageIndex = 0;
                if (pageIndex < 0) pageIndex = totalPages - 1;

                updateCarousel();
            }

            if (nextButton) nextButton.addEventListener("click", () => moveToPage(pageIndex + 1));
            if (prevButton) prevButton.addEventListener("click", () => moveToPage(pageIndex - 1));

            let startX = 0;
            track.addEventListener("touchstart", e => startX = e.touches[0].clientX);
            track.addEventListener("touchend", e => {
                const diff = e.changedTouches[0].clientX - startX;
                if (diff > 50) moveToPage(pageIndex - 1);
                if (diff < -50) moveToPage(pageIndex + 1);
            });

            window.addEventListener("resize", () => {
                pageIndex = 0;
                track.innerHTML = "";
                originalCards.forEach(c => track.appendChild(c.cloneNode(true)));
                duplicateUntilFull();
                updateCarousel();
            });

            updateCarousel();

        })();
    </script>

    <script>
        function animateValue(obj, start, end, duration) {
            if (!obj) return;
            let startTimestamp = null;
            const step = (timestamp) => {
                if (!startTimestamp) startTimestamp = timestamp;
                const progress = Math.min((timestamp - startTimestamp) / duration, 1);
                obj.innerHTML = Math.floor(progress * (end - start) + start);
                if (progress < 1) {
                    window.requestAnimationFrame(step);
                }
            };
            window.requestAnimationFrame(step);
        }

        animateValue(document.getElementById("kepuasan"), 0, 9, 1500);
        animateValue(document.getElementById("tutor"), 0, 5000, 4000);
        animateValue(document.getElementById("siswa_berhasil"), 0, 90, 3000);
        animateValue(document.getElementById("layanan_kota"), 0, 99, 3000);
    </script>

    <script>
        function animateValue(obj, start, end, duration) {
            let startTimestamp = null;
            const step = (timestamp) => {
                if (!startTimestamp) startTimestamp = timestamp;
                const progress = Math.min((timestamp - startTimestamp) / duration, 1);
                obj.innerHTML = Math.floor(progress * (end - start) + start);
                if (progress < 1) {
                    window.requestAnimationFrame(step);
                }
            };
            window.requestAnimationFrame(step);
        }

        const kepuasan = document.getElementById("kepuasan");
        const tutor = document.getElementById("tutor");
        const siswa_berhasil = document.getElementById("siswa_berhasil");
        const layanan_kota = document.getElementById("layanan_kota");
        animateValue(kepuasan, 0, 9, 1500);
        animateValue(tutor, 0, 5000, 4000);
        animateValue(siswa_berhasil, 0, 90, 3000);
        animateValue(layanan_kota, 0, 99, 3000);

        document.querySelectorAll('.faq-section').forEach((section) => {
            const items = Array.from(
                section.querySelectorAll('.faq-section-item')
            );

            items.forEach((item) => {
                const button = item.querySelector('.faq-section-question');
                if (!button) return;
                button.setAttribute(
                    'aria-expanded',
                    item.classList.contains('active') ? 'true' : 'false'
                );

                button.addEventListener('click', () => {
                    const shouldOpen = !item.classList.contains('active');
                    items.forEach((otherItem) => {
                        otherItem.classList.remove('active');
                        const otherButton = otherItem.querySelector(
                            '.faq-section-question'
                        );
                        if (otherButton) {
                            otherButton.setAttribute('aria-expanded', 'false');
                        }
                    });

                    if (shouldOpen) {
                        item.classList.add('active');
                        button.setAttribute('aria-expanded', 'true');
                    }
                });
            });
        });
    </script>
@endpush