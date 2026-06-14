<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Gemstone ID Card</title>

    {{-- Tailwind CDN --}}
    <script src="https://cdn.tailwindcss.com"></script>

    {{-- html2canvas --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js?v=1"></script>

    {{-- Google Fonts --}}
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&display=swap" rel="stylesheet">

    {{-- Favicon with cache busting --}}
    <link rel="icon" href="{{ asset('favicon.png') }}?v={{ filemtime(public_path('favicon.png')) }}" type="image/png">

    <style>
        body {
            font-family: 'Montserrat', sans-serif;
        }
        .right-adjust { right: -10px !important; }
        @media print {
            #downloadBtn { display: none !important; }
        }
        .bg-img {
            /* background-image: url('{{ asset('images/GHC_Certificate_watermark.png') }}?v={{ filemtime(public_path('images/GHC_Certificate_watermark.png')) }}');
            background-repeat: no-repeat;
            background-size: cover;
            background-position: center -130px; */
        }
        .bg-img-footer {
            background-image: url('{{ asset('images/GHC_Certificate_footer.png') }}?v={{ filemtime(public_path('images/GHC_Certificate_footer.png')) }}');
            background-repeat: no-repeat;
            background-size: cover;
        }
        #certificate-container {
            width: 1004px;
            height: 650px;
            box-sizing: border-box;
            overflow: hidden;
        }
        .gem-stone {
            width: 200px;
            height: 200px;
            max-width: 200px;
        }
        .bar-img {
            width: 200px;
            height: 20px;
        }
        @font-face {
            font-family: 'Alaska';
            src: url("{{ asset('fonts/Alaska-Regular.ttf') }}") format('truetype');
            font-weight: normal;
            font-style: normal;
        }
        .font-alaska {
            font-family: 'Alaska', sans-serif;
        }
        /* .font-medium {
            font-size: 0.6rem !important;
        } */
        .img-size {
            max-height: 3.5 rem;
        }
        .header-adjustment {
            position: relative;
            top: -10px;
        }
    </style>
</head>
<body class="bg-gray-200 flex justify-center min-h-screen">
<div id="loader" class="fixed inset-0 bg-[rgb(24_21_21_/_70%)] flex items-center justify-center z-50 hidden">
    <div class="animate-spin rounded-full h-16 w-16 border-4 border-blue-500 border-t-transparent"></div>
</div>
<div id="certificate-container" class="relative flex flex-col items-center">

    {{-- Certificate --}}
    <div id="certificate" class="bg-white w-[700px]  shadow-lg overflow-hidden rounded-xl">
        {{-- Header --}}
        <div class="text-white flex items-center justify-between px-4 py-2">
            <div class="flex items-center gap-2 py-1">
                <a href="{{ route('dashboard') }}">
                    <img src="{{ asset('images/id_logo.png') }}?v={{ filemtime(public_path('images/id_logo.png')) }}"
                         alt="Logo" class="shadow-md max-h-20">
                </a>
                <div class="w-px h-8 bg-white"></div>
            </div>
            <div class="flex items-center max-h-20">
                {!! QrCode::size(80)->generate(url('/search_certificate/' . $gem->summary_no)) !!}
            </div>
        </div>

        {{-- Title --}}
        <div class="px-4">
            <div class="flex items-center justify-center mb-1 mt-1 header-adjustment">
                <div class="flex-1 border-t-2 border-[#C6B893]"></div>
                <span class="px-4 font-bold text-sm text-[#6E5C4D]">IDENTIFICATION REPORT</span>
                <div class="flex-1 border-t-2 border-[#C6B893]"></div>
            </div>

            {{-- Body --}}
            <div class="bg-img">
                <div class="flex pb-4">
                    <div class="w-full text-sm space-y-2">
                        @php
                            $gemDetails = [
                                'SUMMARY NO' => $gem->summary_no,
                                'DESCRIPTION'        => $gem->description,
                            ];
                        @endphp
                        @foreach ($gemDetails as $label => $value)
                            <div class="flex">
                                <span class="w-48 font-medium">{{ $label }}</span>
                                <span class="w-5 font-medium text-left">:</span>
                                {{-- If label is DESCRIPTION, take full width --}}
                                @if ($label === 'DESCRIPTION')
                                    <span class="flex-1 font-medium">{{ $value }}</span>
                                @else
                                    <span class="w-50 font-medium">{{ $value }}</span>
                                @endif
                            </div>
                        @endforeach
                    </div>

                    
                </div>
                <div class="flex">
                    <div class="w-full text-sm space-y-2">
                        @php
                            $gemDetails = [
                                'GRADE / CLARITY'    => $gem->clarity ?? 'N/A',
                                'GROSS WEIGHT'       => $gem->gross_weight ?? 'N/A',
                                'STONE WEIGHT'       => $gem->diamond_weight ?? 'N/A'
                            ];
                        @endphp
                        @foreach ($gemDetails as $label => $value)
                            <div class="flex">
                                <span class="w-48 font-medium">{{ $label }}</span>
                                <span class="w-5 font-medium text-left">:</span>
                                {{-- If label is DESCRIPTION, take full width --}}
                                @if ($label === 'DESCRIPTION')
                                    <span class="flex-1 font-medium">{{ $value }}</span>
                                @else
                                    <span class="w-50 font-medium">{{ $value }}</span>
                                @endif
                            </div>
                        @endforeach
                    </div>

                    {{-- Gem Image --}}
                    <div class="w-1/4 flex flex-col items-end translate-y-1/8">
                        <img src="{{ $gem->image ?? 'https://via.placeholder.com/100' }}" 
                            alt="Gemstone" class="mb-2 border w-25 h-25 object-cover gem-stone">
                    </div>

                    {{-- Barcode --}}
                    <div class="w-1/6 flex flex-col items-center relative">
                       <div class="absolute top-[-15%] right-0 transform -translate-y-1/8 text-[12px] font-bold text-[#000000]">
    <span class="block -rotate-90 whitespace-nowrap origin-bottom-right">Terms & Conditions Apply</span>
</div>
                    </div>
                </div>
                <div class="flex pb-4">
                    <div class="w-full text-sm space-y-2">
                        @php
                            $gemDetails = [
                                'CONCLUSION'  => $gem->comment ?? 'N/A',
                            ];
                        @endphp
                        @foreach ($gemDetails as $label => $value)
                            <div class="flex">
                                <span class="w-48 font-semibold">{{ $label }}</span>
                                <span class="w-5 font-semibold text-left">:</span>
                                    <span class="w-50 font-semibold">{{ $value }}</span>
                            </div>
                        @endforeach
                    </div>

                    
                </div>
            </div>
        </div>

       <div class="flex items-center justify-center">
            <span class="px-2 text-xs font-medium text-[#6E5C4D] justify-center"> <img class="bar-img" src="data:image/png;base64,{{ $barcode }}" alt="Barcode" class="w-50">
            </span>
        </div>
        <div class="mx-2 border-t-4 py-0.5  bg-[#C6B893] border-[#C6B893]"></div>
        <div class="py-1"></div>
    </div>
    <div class="flex pb-4 space-x-4">                       
        {{-- Download Button --}}
        <button data-summaryno="{{ $gem->summary_no }}" id="downloadBtn"
                class="mt-2 bg-[#2270e5] text-white px-4 py-2 text-sm rounded shadow hover:bg-[#5a3e29] transition">
            ⬇ Download
        </button>

        {{-- Print Button --}}
        <button id="printPngBtn"
                class="mt-2 bg-[#2270e5] text-white px-4 py-2 text-sm rounded shadow hover:bg-[#5a3e29] transition">
            🖨 Print PNG
        </button>
    </div>                      
</div>

<script>
document.getElementById('downloadBtn').addEventListener('click', function (event) {
    const targetWidth = 1004;
    const targetHeight = 650;
    // document.querySelector(".header-text").classList.add('header-adjustment');
    document.getElementById("loader").classList.remove("hidden");
    html2canvas(document.querySelector("#certificate"), {
        scale: 2,
        backgroundColor: null
    }).then(capturedCanvas => {
        const finalCanvas = document.createElement('canvas');
        finalCanvas.width = targetWidth;
        finalCanvas.height = targetHeight;
        const ctx = finalCanvas.getContext('2d');

        ctx.fillStyle = "#fff";
        ctx.fillRect(0, 0, targetWidth, targetHeight);

        ctx.drawImage(
            capturedCanvas,
            0, 0, capturedCanvas.width, capturedCanvas.height,
            0, 0, targetWidth, targetHeight
        );

        const link = document.createElement('a');
        link.download = event.target.getAttribute('data-summaryno') + '.png';
        link.href = finalCanvas.toDataURL('image/png');
        link.click();
        // document.querySelector(".header-text").classList.remove('header-adjustment');
        document.getElementById("loader").classList.add("hidden");
    });
});
document.getElementById('printPngBtn').addEventListener('click', function () {
    const targetWidth = 1004;
    const targetHeight = 650;
   
    // document.querySelector(".header-text").classList.add('header-adjustment');
    document.getElementById("loader").classList.remove("hidden");
    html2canvas(document.querySelector("#certificate"), {
        scale: 2,
        backgroundColor: null
    }).then(capturedCanvas => {
        // Create final canvas with exact size
        const finalCanvas = document.createElement('canvas');
        finalCanvas.width = targetWidth;
        finalCanvas.height = targetHeight;
        const ctx = finalCanvas.getContext('2d');

        ctx.fillStyle = "#fff";
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

        // Convert to PNG
        const imgData = finalCanvas.toDataURL("image/png");

        // Open in new tab for printing
        const win = window.open("");
        win.document.write('<img src="' + imgData + '" onload="window.print();window.close()">');
        win.document.close();
        // document.querySelector(".header-text").classList.remove('header-adjustment');
        document.getElementById("loader").classList.add("hidden");
    });
});
</script>

</body>
</html>
