@extends('layouts.generic')

@section('head')
    <title>{{ $article->title }}</title>
    <meta name="description" content="{{ $article->title }}" />
    <meta name="robots" content="index, follow, max-snippet:-1, max-video-preview:-1, max-image-preview:large" />
    <link rel="canonical" href="/blogs" />
    <meta property="og:locale" content="id" />
    <meta property="og:type" content="article" />
    <meta property="og:title" content="{{ $article->title }}" />
    <meta property="og:description" content="{{ $article->title }}" />
    <meta property="og:url" content="/blogs" />
    <meta property="og:site_name" content="Bimbel Commander" />
    <meta property="og:image:width" content="626" />
    <meta property="og:image:height" content="626" />
    <meta property="og:image:alt" content="Blogs" />
    <meta property="og:image:type" content="image/webp" />
    <meta name="twitter:card" content="summary_large_image" />
    <meta name="twitter:title" content="{{ $article->title }}" />
    <meta name="twitter:description" content="{{ $article->title }}" />
    @if ($article->image)
        <meta name="twitter:image" content="{{ asset('storage/' . $article->image) ?? '' }}" />
        <meta property="og:image" content="{{ asset('storage/' . $article->image) ?? '' }}" />
        <meta property="og:image:secure_url" content="{{ asset('storage/' . $article->image) ?? '' }}" />
    @endif
@endsection

@section('content')
    <div class="container" style="margin-top: 100px">
        @if ($article->image)
            <img src="{{ asset('storage/' . $article->image) }}" alt="{{ $article->title }}" class="d-block m-auto">
        @endif
        <p class="text-secondary" style="font-size: 12px">
            Dipublikasikan: {{ $article->created_at->format('d M Y') }}
        </p>
        <h1 class="text-center">{{ $article->title }}</h1>
        <div class="prose max-w-none" style="text-justify">
            @foreach ($article->data as $block)
                @if ($block['type'] === 'content')
                    {!! $block['content'] !!}
                @endif
            @endforeach
        </div>
    </div>
@endsection
