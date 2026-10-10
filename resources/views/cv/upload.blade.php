@extends('layouts.app')

@section('content')
<div class="text-center mb-10 fade-up">
    <span class="glass inline-block text-xs font-semibold px-4 py-1.5 rounded-full text-violet-300 mb-5">✨ AI Powered Resume Review</span>
    <h1 class="text-4xl md:text-6xl font-extrabold leading-tight tracking-tight">
        Apni CV ko <span class="grad-text">next level</span><br>pe le jao
    </h1>
    <p class="text-gray-400 mt-4 max-w-xl mx-auto">PDF upload karo, 20 second mein score, strengths, weaknesses aur improvements hasil karo.</p>
</div>

<div class="glass rounded-3xl p-6 md:p-8 fade-up" style="animation-delay:.15s">

    @if ($errors->any())
        <div class="bg-red-500/10 border border-red-500/30 text-red-300 p-3 rounded-xl mb-5 text-sm">
            @foreach ($errors->all() as $error) <p>⚠️ {{ $error }}</p> @endforeach
        </div>
    @endif

    <form id="cvForm" action="{{ route('cv.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <label id="dropzone" for="cvInput"
               class="block cursor-pointer border-2 border-dashed border-white/15 hover:border-violet-400 rounded-2xl p-10 text-center transition mb-6">
            <div class="text-5xl mb-3">📄</div>
            <p id="dropText" class="font-semibold">CV yahan drag & drop karo ya click karo</p>
            <p class="text-xs text-gray-500 mt-1">Sirf PDF • Max 5MB</p>
            <input id="cvInput" type="file" name="cv" accept=".pdf" required class="hidden">
        </label>

        <label class="block text-sm font-semibold mb-2 text-gray-300">Target job role <span class="text-gray-500 font-normal">(optional)</span></label>
        <input type="text" name="job_role" value="{{ old('job_role') }}" placeholder="e.g. Backend Developer"
               class="w-full bg-white/5 border border-white/10 focus:border-violet-400 outline-none rounded-xl px-4 py-3 mb-6 text-white placeholder-gray-500">

        <button type="submit" class="grad-btn w-full text-white font-bold py-3.5 rounded-xl text-lg">
            🚀 Analyze My CV
        </button>
    </form>
</div>

<div class="grid grid-cols-3 gap-4 mt-8 text-center text-sm fade-up" style="animation-delay:.3s">
    <button type="button" onclick="openInfo('ats')" class="glass rounded-2xl p-4 hover:bg-white/10 hover:-translate-y-1 hover:border-violet-400/50 transition cursor-pointer">
        <div class="text-2xl mb-1">🎯</div>ATS Score
    </button>
    <button type="button" onclick="openInfo('tips')" class="glass rounded-2xl p-4 hover:bg-white/10 hover:-translate-y-1 hover:border-violet-400/50 transition cursor-pointer">
        <div class="text-2xl mb-1">💡</div>Smart Tips
    </button>
    <button type="button" onclick="openInfo('keywords')" class="glass rounded-2xl p-4 hover:bg-white/10 hover:-translate-y-1 hover:border-violet-400/50 transition cursor-pointer">
        <div class="text-2xl mb-1">🔑</div>Keywords
    </button>
</div>

{{-- Info popup --}}
<div id="infoModal" class="hidden fixed inset-0 z-40 items-center justify-center p-4 bg-black/70 backdrop-blur-sm" onclick="if(event.target===this) closeInfo()">
    <div class="glass bg-[#0d0d1c] rounded-3xl p-6 md:p-8 w-full max-w-2xl max-h-[85vh] overflow-y-auto relative">
        <button type="button" onclick="closeInfo()" class="absolute top-4 right-4 w-9 h-9 rounded-full bg-white/10 hover:bg-white/20 text-lg">✕</button>

        {{-- ATS --}}
        <div id="panel-ats" class="hidden">
            <h3 class="text-2xl font-extrabold mb-1">🎯 ATS Score kya hota hai?</h3>
            <p class="text-gray-400 text-sm mb-5">ATS (Applicant Tracking System) wo software hai jo companies CV ko pehle khud scan karti hain, insaan baad mein dekhta hai.</p>

            <h4 class="font-semibold mb-2 text-violet-300">ATS ye cheezein check karta hai</h4>
            <ul class="space-y-2 text-sm text-gray-300 mb-5">
                <li>✔ Job role se match karte keywords</li>
                <li>✔ Simple, clean formatting (tables aur images se bacho)</li>
                <li>✔ Standard sections: Education, Experience, Skills, Projects</li>
                <li>✔ Text wali PDF (scanned image wali nahi)</li>
                <li>✔ Contact info: email, phone, LinkedIn</li>
            </ul>

            <h4 class="font-semibold mb-3 text-violet-300">Score ka matlab</h4>
            <div class="space-y-2 text-sm">
                <div class="flex items-center gap-3"><span class="w-20 text-center py-1 rounded-full bg-emerald-400/15 text-emerald-300 font-bold">80-100</span><span class="text-gray-300">Excellent, shortlist hone ke chances zyada</span></div>
                <div class="flex items-center gap-3"><span class="w-20 text-center py-1 rounded-full bg-blue-400/15 text-blue-300 font-bold">65-79</span><span class="text-gray-300">Good, thori improvement se top pe</span></div>
                <div class="flex items-center gap-3"><span class="w-20 text-center py-1 rounded-full bg-amber-400/15 text-amber-300 font-bold">50-64</span><span class="text-gray-300">Needs work, keywords aur projects badhao</span></div>
                <div class="flex items-center gap-3"><span class="w-20 text-center py-1 rounded-full bg-rose-400/15 text-rose-300 font-bold">0-49</span><span class="text-gray-300">Weak, CV dobara structure karo</span></div>
            </div>
        </div>

        {{-- Tips --}}
        <div id="panel-tips" class="hidden">
            <h3 class="text-2xl font-extrabold mb-1">💡 Smart CV Tips</h3>
            <p class="text-gray-400 text-sm mb-5">Ye 8 baatein CV ko foran behtar bana deti hain.</p>
            <div class="space-y-3">
                <div class="flex gap-3"><span class="shrink-0 w-7 h-7 rounded-full grad-btn flex items-center justify-center text-xs font-bold text-white">1</span><p class="text-sm text-gray-300"><b class="text-white">CV 1 page rakho</b> agar experience 3 saal se kam hai.</p></div>
                <div class="flex gap-3"><span class="shrink-0 w-7 h-7 rounded-full grad-btn flex items-center justify-center text-xs font-bold text-white">2</span><p class="text-sm text-gray-300"><b class="text-white">Numbers likho:</b> "API banayi" ki jagah "API banayi jo 500+ users handle karti hai".</p></div>
                <div class="flex gap-3"><span class="shrink-0 w-7 h-7 rounded-full grad-btn flex items-center justify-center text-xs font-bold text-white">3</span><p class="text-sm text-gray-300"><b class="text-white">Action verbs use karo:</b> Built, Designed, Optimized, Implemented.</p></div>
                <div class="flex gap-3"><span class="shrink-0 w-7 h-7 rounded-full grad-btn flex items-center justify-center text-xs font-bold text-white">4</span><p class="text-sm text-gray-300"><b class="text-white">Projects ke saath links do:</b> GitHub aur live demo ka link zaroor.</p></div>
                <div class="flex gap-3"><span class="shrink-0 w-7 h-7 rounded-full grad-btn flex items-center justify-center text-xs font-bold text-white">5</span><p class="text-sm text-gray-300"><b class="text-white">Har job ke liye CV tweak karo:</b> job description ke keywords shamil karo.</p></div>
                <div class="flex gap-3"><span class="shrink-0 w-7 h-7 rounded-full grad-btn flex items-center justify-center text-xs font-bold text-white">6</span><p class="text-sm text-gray-300"><b class="text-white">Simple font aur layout:</b> 1 font, 10-12pt, bohot colors ya graphics nahi.</p></div>
                <div class="flex gap-3"><span class="shrink-0 w-7 h-7 rounded-full grad-btn flex items-center justify-center text-xs font-bold text-white">7</span><p class="text-sm text-gray-300"><b class="text-white">Professional email:</b> naam wali email use karo, nickname wali nahi.</p></div>
                <div class="flex gap-3"><span class="shrink-0 w-7 h-7 rounded-full grad-btn flex items-center justify-center text-xs font-bold text-white">8</span><p class="text-sm text-gray-300"><b class="text-white">Spelling check karo:</b> ek galti bhi impression kharab kar deti hai.</p></div>
            </div>
        </div>

        {{-- Keywords --}}
        <div id="panel-keywords" class="hidden">
            <h3 class="text-2xl font-extrabold mb-1">🔑 Role ke hisaab se Keywords</h3>
            <p class="text-gray-400 text-sm mb-4">Role pe click karo, uske keywords dikhenge aur wo role form mein khud bhar jayega.</p>
            <div id="roleButtons" class="flex flex-wrap gap-2 mb-5"></div>
            <div id="kwBox" class="hidden">
                <h4 class="font-semibold mb-3 text-violet-300">CV mein ye keywords hone chahiye:</h4>
                <div id="kwList" class="flex flex-wrap gap-2"></div>
                <p class="text-xs text-gray-500 mt-4">⚠️ Sirf wahi keywords likho jo tumhe sach mein aate hain.</p>
            </div>
        </div>
    </div>
</div>

<script>
    const roleKeywords = {
        'Backend Developer': ['PHP', 'Laravel', 'REST API', 'MySQL', 'PostgreSQL', 'Redis', 'Docker', 'Git', 'Authentication', 'Unit Testing', 'Queues', 'Caching', 'CI/CD'],
        'Frontend Developer': ['HTML', 'CSS', 'JavaScript', 'React', 'Tailwind', 'Responsive Design', 'TypeScript', 'REST API', 'Git', 'Accessibility', 'Vite', 'Performance'],
        'Full Stack Developer': ['Laravel', 'React', 'Node.js', 'REST API', 'MySQL', 'Docker', 'Git', 'Tailwind', 'Authentication', 'Deployment', 'Testing', 'Agile'],
        'Data Analyst': ['SQL', 'Excel', 'Python', 'Power BI', 'Tableau', 'Data Cleaning', 'Pandas', 'Statistics', 'Dashboards', 'Visualization', 'Reporting'],
        'Mobile Developer': ['Flutter', 'Dart', 'React Native', 'Android', 'Firebase', 'REST API', 'State Management', 'Git', 'Play Store', 'UI/UX'],
        'UI/UX Designer': ['Figma', 'Wireframing', 'Prototyping', 'User Research', 'Design System', 'Usability Testing', 'Adobe XD', 'Responsive Design'],
        'Digital Marketer': ['SEO', 'Google Ads', 'Facebook Ads', 'Analytics', 'Content Strategy', 'Email Marketing', 'Social Media', 'Conversion Rate', 'Copywriting']
    };

    const modal = document.getElementById('infoModal');

    function openInfo(name) {
        ['ats', 'tips', 'keywords'].forEach(p => document.getElementById('panel-' + p).classList.add('hidden'));
        document.getElementById('panel-' + name).classList.remove('hidden');
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }

    function closeInfo() {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }

    document.addEventListener('keydown', e => { if (e.key === 'Escape') closeInfo(); });

    // role buttons banao
    const roleBox = document.getElementById('roleButtons');
    Object.keys(roleKeywords).forEach(role => {
        const b = document.createElement('button');
        b.type = 'button';
        b.textContent = role;
        b.className = 'px-4 py-2 rounded-full text-sm bg-white/5 border border-white/15 hover:border-violet-400 hover:bg-violet-500/20 transition';
        b.onclick = () => {
            document.querySelector('input[name="job_role"]').value = role;
            const list = document.getElementById('kwList');
            list.innerHTML = '';
            roleKeywords[role].forEach(k => {
                const s = document.createElement('span');
                s.textContent = k;
                s.className = 'px-3 py-1.5 rounded-full text-sm bg-amber-400/10 border border-amber-400/30 text-amber-300';
                list.appendChild(s);
            });
            document.getElementById('kwBox').classList.remove('hidden');
            roleBox.querySelectorAll('button').forEach(x => x.classList.remove('border-violet-400', 'bg-violet-500/30'));
            b.classList.add('border-violet-400', 'bg-violet-500/30');
        };
        roleBox.appendChild(b);
    });
</script>

{{-- Loading overlay --}}
<div id="loader" class="hidden fixed inset-0 z-50 bg-[#070711]/90 backdrop-blur-md flex-col items-center justify-center text-center">
    <div class="w-20 h-20 rounded-full border-4 border-white/10 border-t-violet-500 animate-spin mb-6"></div>
    <p id="loaderText" class="text-xl font-semibold grad-text">CV padh raha hoon...</p>
    <p class="text-gray-500 text-sm mt-2">10-30 second lag sakte hain, page band mat karna</p>
</div>

<script>
    const input = document.getElementById('cvInput');
    const zone  = document.getElementById('dropzone');
    const text  = document.getElementById('dropText');

    input.addEventListener('change', () => {
        if (input.files[0]) text.textContent = '✅ ' + input.files[0].name;
    });
    ['dragenter','dragover'].forEach(e => zone.addEventListener(e, ev => { ev.preventDefault(); zone.classList.add('border-violet-400','bg-white/5'); }));
    ['dragleave','drop'].forEach(e => zone.addEventListener(e, ev => { ev.preventDefault(); zone.classList.remove('border-violet-400','bg-white/5'); }));
    zone.addEventListener('drop', ev => {
        if (ev.dataTransfer.files.length) { input.files = ev.dataTransfer.files; input.dispatchEvent(new Event('change')); }
    });

    document.getElementById('cvForm').addEventListener('submit', () => {
        const loader = document.getElementById('loader');
        loader.classList.remove('hidden'); loader.classList.add('flex');
        const msgs = ['Reading your CV...', 'Scanning skills...', 'Reviewing experience...', 'Calculating score...', 'Writing improvements...'];        let i = 0;
        setInterval(() => { i = (i + 1) % msgs.length; document.getElementById('loaderText').textContent = msgs[i]; }, 3500);
    });
</script>
@endsection