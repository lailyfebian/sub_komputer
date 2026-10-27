<div class="header-desktop">
<div class="container d-lg-flex d-none align-items-center justify-content-between pt-3 pb-2">
    <div class="navbar-brand d-flex">
        <img src="{{ URL::to('images/logo-pn-web.webp') }}" width="180" alt="Logo Privat Nusantara" />
    </div>
    <div class="header" id="">
        <div class="d-flex">
          <a class="nav-link active" aria-current="page" href="{{ url('/') }}">Beranda</a>
          <a class="nav-link" href="{{ url('/#produk') }}">Produk</a>
          <a class="nav-link" href="{{ url('/#kontak-kami') }}">Kontak Kami</a>
        </div>
    </div>
</div>
</div>

<div class="d-lg-none d-block header-mobile">
    <div class="menu px-4">
        <div class="d-flex justify-content-between">
            <div class="icon">
                <a class="nav-link active" aria-current="page" href="{{ url('/') }}">
                    <img src="{{ asset('images/icon/mobile/home.webp') }}" width="20px" alt="Home Mobile">
                </a>
                <p class="text-center fw-bold">Home</p>
            </div>
            <div class="icon">
                <a class="nav-link active" aria-current="page" href="{{ url('/#produk') }}">
                    <img src="{{ asset('images/icon/mobile/product.webp') }}" width="20px" alt="Produk Mobile">
                </a>
                <p class="text-center fw-bold">Produk</p>
            </div>
            <div class="icon">
                <a class="nav-link active" aria-current="page" href="{{ url('/#kontak-kami') }}">
                    <img src="{{ asset('images/icon/mobile/contact.webp') }}" width="20px" alt="Kontak Kami Mobile">
                </a>
                <p class="text-center fw-bold">Kontak Kami</p>
            </div>
        </div>
    </div>
</div>