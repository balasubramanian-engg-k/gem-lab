<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Gemstone ID Card (New)</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js?v=1"></script>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="icon" href="{{ asset('favicon.png') }}?v={{ filemtime(public_path('favicon.png')) }}" type="image/png">

    <style>
        body { font-family: 'Montserrat', sans-serif; }
        @media print { #downloadBtn, #printPngBtn { display: none !important; } }
        #certificate-container { width: 1004px; max-width: 100%; box-sizing: border-box; overflow: hidden; }
        #certificate img { display: block; vertical-align: top; }
    </style>
</head>
<body class="bg-slate-100 flex flex-col items-center min-h-screen py-8">
<div id="loader" class="fixed inset-0 bg-[rgb(24_21_21_/_70%)] flex items-center justify-center z-50 hidden">
    <div class="animate-spin rounded-full h-16 w-16 border-4 border-blue-500 border-t-transparent"></div>
</div>

{{-- Image from public/certificates_new/{summary_no}.png --}}
<div id="certificate-container" class="relative flex flex-col items-center gap-4">
    <div class="w-full max-w-[700px] text-center text-slate-600 text-sm">
        <p class="font-semibold text-slate-800">Certificate lookup</p>
        <p>Summary no: <span class="font-mono">{{ $gem->summary_no }}</span></p>
    </div>

    <div id="certificate" class="bg-white w-[700px] shadow-lg overflow-hidden rounded-xl border border-slate-200">
        @if (! empty($certificateNewImageUrl))
            <img src="{{ $certificateNewImageUrl }}" alt="Certificate {{ $gem->summary_no }}" width="700" class="w-full h-auto">
        @endif
    </div>

    <div class="flex pb-4 space-x-4">
        @if (! empty($certificateNewImageUrl))
            @php
                $certificatePngDownloadUrl = asset('certificates_new/'.$gem->summary_no.'.png');
            @endphp
            <a id="downloadBtn" href="{{ $certificatePngDownloadUrl }}" download="{{ $gem->summary_no }}.png"
               class="mt-2 inline-flex items-center bg-[#2270e5] text-white px-4 py-2 text-sm rounded shadow hover:bg-[#5a3e29] transition">
                ⬇ Download
            </a>
        @else
            <span id="downloadBtn" class="mt-2 inline-flex items-center bg-gray-400 text-white px-4 py-2 text-sm rounded cursor-not-allowed opacity-75"
                  title="Certificate image not found in certificates_new">⬇ Download</span>
        @endif
        <button id="printPngBtn"
                class="mt-2 bg-[#2270e5] text-white px-4 py-2 text-sm rounded shadow hover:bg-[#5a3e29] transition">
            🖨 Print PNG
        </button>
    </div>
</div>

<script>
function toggleHeaderAdjustment(add) {
    const el = document.querySelector('.header-text');
    if (!el) return;
    el.classList.toggle('header-adjustment', add);
}

document.getElementById('printPngBtn').addEventListener('click', function () {
    const targetWidth = 1004;
    const targetHeight = 650;
    toggleHeaderAdjustment(true);
    document.getElementById('loader').classList.remove('hidden');

    html2canvas(document.querySelector('#certificate'), {
        scale: 2,
        backgroundColor: '#ffffff'
    }).then(capturedCanvas => {
        const finalCanvas = document.createElement('canvas');
        finalCanvas.width = targetWidth;
        finalCanvas.height = targetHeight;
        const ctx = finalCanvas.getContext('2d');
        ctx.fillStyle = '#fff';
        ctx.fillRect(0, 0, targetWidth, targetHeight);
        const scale = Math.min(
            targetWidth / capturedCanvas.width,
            targetHeight / capturedCanvas.height
        );
        const offsetX = (targetWidth - capturedCanvas.width * scale) / 2;
        const offsetY = (targetHeight - capturedCanvas.height * scale) / 2;
        ctx.drawImage(
            capturedCanvas,
            offsetX, offsetY,
            capturedCanvas.width * scale,
            capturedCanvas.height * scale
        );
        const imgData = finalCanvas.toDataURL('image/png');
        fetch('/gems/print-certificate', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({
                image: imgData,
                filename: Date.now() + '.png'
            })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success && data.url) {
                const win = window.open('');
                win.document.write('<img src="{{ url('/') }}' + data.url + '" onload="window.print();window.close()">');
                win.document.close();
            } else {
                alert('Error saving certificate for print!');
            }
        })
        .finally(() => {
            toggleHeaderAdjustment(false);
            document.getElementById('loader').classList.add('hidden');
        });
    });
});
</script>
</body>
</html>
