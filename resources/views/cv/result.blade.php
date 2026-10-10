@extends('layouts.app')

@section('content')
@php
    $r = $analysis->result;
    $score = (int) $analysis->score;
    $circ = 440;
    $offset = round($circ * (1 - $score / 100));
    [$label, $color] = match (true) {
        $score >= 80 => ['Excellent', '#34d399'],
        $score >= 65 => ['Good', '#60a5fa'],
        $score >= 50 => ['Needs Work', '#fbbf24'],
        default      => ['Weak', '#f87171'],
    };
@endphp

<style>
    .ring { stroke-dasharray: {{ $circ }}; stroke-dashoffset: {{ $circ }}; animation: ring 1.6s .3s ease forwards; }
    @keyframes ring { to { stroke-dashoffset: {{ $offset }}; } }
    .bar { width: 0; animation: bar 1.2s .5s ease forwards; }
    @keyframes bar { to { width: var(--w); } }
</style>

{{-- Score card --}}
<div class="glass rounded-3xl p-8 text-center mb-6 fade-up">
    <p class="text-gray-400 text-sm mb-6">📄 {{ $analysis->original_name }}
        @if($analysis->job_role) • <span class="text-violet-300">{{ $analysis->job_role }}</span> @endif
    </p>

    <div class="relative w-52 h-52 mx-auto mb-4">
        <svg viewBox="0 0 160 160" class="w-full h-full -rotate-90">
            <circle cx="80" cy="80" r="70" fill="none" stroke="rgba(255,255,255,.08)" stroke-width="12"/>
            <circle cx="80" cy="80" r="70" fill="none" stroke="{{ $color }}" stroke-width="12" stroke-linecap="round" class="ring"/>
        </svg>
        <div class="absolute inset-0 flex flex-col items-center justify-center">
            <span id="scoreNum" class="text-6xl font-extrabold" style="color: {{ $color }}">0</span>
            <span class="text-gray-500 text-sm">out of 100</span>
        </div>
    </div>

    <span class="inline-block px-4 py-1 rounded-full text-sm font-bold" style="background: {{ $color }}22; color: {{ $color }}">{{ $label }}</span>
    <p class="text-gray-300 mt-5 max-w-2xl mx-auto leading-relaxed">{{ $r['summary'] ?? '' }}</p>
</div>

{{-- Section scores --}}
<div class="glass rounded-3xl p-6 mb-6 fade-up" style="animation-delay:.1s">
    <h3 class="font-bold text-lg mb-5">📊 Section Scores</h3>
    <div class="grid md:grid-cols-2 gap-x-8 gap-y-5">
        @foreach (($r['section_scores'] ?? []) as $name => $value)
            <div>
                <div class="flex justify-between text-sm mb-2">
                    <span class="capitalize text-gray-300">{{ $name }}</span>
                    <span class="font-bold">{{ (int) $value }}</span>
                </div>
                <div class="w-full h-2.5 rounded-full bg-white/10 overflow-hidden">
                    <div class="bar h-full rounded-full" style="--w: {{ (int) $value }}%; background: linear-gradient(90deg,#7c3aed,#60a5fa)"></div>
                </div>
            </div>
        @endforeach
    </div>
</div>

{{-- Strengths / Weaknesses --}}
<div class="grid md:grid-cols-2 gap-6 mb-6 fade-up" style="animation-delay:.2s">
    <div class="rounded-3xl p-6 bg-emerald-500/10 border border-emerald-500/25">
        <h3 class="font-bold text-emerald-300 mb-4">💪 Strengths</h3>
        <ul class="space-y-3 text-sm text-gray-200">
            @foreach (($r['strengths'] ?? []) as $item)
                <li class="flex gap-2"><span class="text-emerald-400">✔</span><span>{{ $item }}</span></li>
            @endforeach
        </ul>
    </div>
    <div class="rounded-3xl p-6 bg-rose-500/10 border border-rose-500/25">
        <h3 class="font-bold text-rose-300 mb-4">⚠️ Weaknesses</h3>
        <ul class="space-y-3 text-sm text-gray-200">
            @foreach (($r['weaknesses'] ?? []) as $item)
                <li class="flex gap-2"><span class="text-rose-400">✖</span><span>{{ $item }}</span></li>
            @endforeach
        </ul>
    </div>
</div>

{{-- Improvements --}}
<div class="glass rounded-3xl p-6 mb-6 fade-up" style="animation-delay:.3s">
    <h3 class="font-bold text-lg mb-5">💡 How to Improve</h3>
    <div class="space-y-4">
        @foreach (($r['improvements'] ?? []) as $i => $item)
            <div class="flex gap-4 items-start">
                <span class="shrink-0 w-8 h-8 rounded-full grad-btn flex items-center justify-center text-sm font-bold text-white">{{ $i + 1 }}</span>
                <p class="text-gray-300 text-sm leading-relaxed pt-1">{{ $item }}</p>
            </div>
        @endforeach
    </div>
</div>

{{-- Keywords --}}
<div class="glass rounded-3xl p-6 mb-8 fade-up" style="animation-delay:.4s">
    <h3 class="font-bold text-lg mb-4">🔑 Missing Keywords</h3>
    <div class="flex flex-wrap gap-2">
        @foreach (($r['missing_keywords'] ?? []) as $kw)
            <span class="px-3 py-1.5 rounded-full text-sm bg-amber-400/10 border border-amber-400/30 text-amber-300">{{ $kw }}</span>
        @endforeach
    </div>
</div>

{{-- Buttons --}}
<div class="no-print flex flex-col sm:flex-row gap-4">
    <a href="{{ route('cv.create') }}" class="grad-btn flex-1 text-center text-white font-bold py-3.5 rounded-xl">🔄 Analyze another CV</a>
    <button onclick="window.print()" class="glass flex-1 font-bold py-3.5 rounded-xl hover:bg-white/10 transition">🖨️ Save as PDF</button>
</div>

<script>
    // score count-up animation
    (function () {
        const target = {{ $score }};
        const el = document.getElementById('scoreNum');
        let n = 0;
        const t = setInterval(() => {
            n += Math.max(1, Math.round(target / 40));
            if (n >= target) { n = target; clearInterval(t); }
            el.textContent = n;
        }, 35);
    })();
</script>
@endsection