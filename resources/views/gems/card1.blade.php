<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Gemstone ID Card</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="icon" href="{{ asset('favicon.png') }}" type="image/png">
    <style>
        body {
            font-family: 'Montserrat', sans-serif;
        }
        .right-adjust {
            right: -47px;
        }
        @media print {
            #downloadBtn {
                display: none !important;
            }
        }
        .bg-img {
            background-image: url('/images/GHC_Certificate_watermark.png');
            background-repeat: no-repeat;
            background-size: cover;
            background-position: center -130px; /* 20px down from the top */
        }
        .bg-img-footer {
            background-image: url('/images/GHC_Certificate_footer.png');
            background-repeat: no-repeat;
            background-size: cover;
        }
        #certificate-container {
            width: 1004px;
            height: 650px;
            box-sizing: border-box; /* Keeps padding inside the fixed size */
            overflow: hidden; /* Prevents content from spilling out */
        }
    </style>
</head>
<body class="bg-gray-200 flex  justify-center min-h-screen">

<div id="certificate-container" class="relative flex flex-col items-center">
    <!-- Download Button -->
    <!-- <button data-summaryno = "{{ $gem->summary_no }}" id="downloadBtn"
        class=" -top-10 right-0 bg-[#745840] text-white px-3 py-1 text-xs rounded shadow hover:bg-[#5a3e29] transition">
        ⬇ Download     </button>
         -->
    <div id="certificate" class="bg-white w-[700px]  shadow-lg overflow-hidden">
        <!-- Header -->
        <div class="bg-[#745840] text-white flex items-center justify-between px-3 py-2">
    <!-- Logo & Title -->
        <div class="flex items-center gap-2">
            <a href="{{ route('dashboard') }}">
                <img src="{{ asset('images/logo.png') }}" alt="Logo" class="shadow-md max-h-10">
            </a>

            <!-- Vertical Line -->
            <div class="w-px h-8 bg-white"></div>

            <!-- Title Text -->
            <div class="leading-tight font-bold text-[15px] pl-8 text-center">
                GEMSTONE HALLMARK CENTRE<br>
                GEMMOLOGICAL REPORT
            </div>
        </div>

    <!-- QR Code -->
        <div class="flex items-center">
            {!! QrCode::size(50)->generate(url('/gems/summary/' . $gem->summary_no)) !!}
        </div>
    </div>
        <!-- Certificate Body -->
        <div class="px-2 ">
        <div class="flex items-center justify-center mb-2">
                <div class="flex-1 border-t border-gray-400"></div>
                <span class="px-4 font-semibold text-lg">CERTIFICATE OF AUTHENTICITY</span>
                <div class="flex-1 border-t border-gray-400"></div>
        </div>

            <div class="flex bg-img pb-4">
                <!-- Left Info -->
                <div class="w-full text-sm space-y-2">
    @php
        $gemDetails = [
            'CERTIFICATE NUMBER' => $gem->summary_no,
            'DESCRIPTION'        => $gem->description,
            'STONE TYPE'         => $gem->stone_type ?? 'N/A',
            'STONE COLOUR'       => $gem->color ?? 'N/A',
            'SHAPE / CUT'        => $gem->shape ?? 'N/A',
            'GRADE / CLARITY'    => $gem->clarity ?? 'N/A',
            'GROSS WEIGHT'       => $gem->gross_weight ?? 'N/A',
            'STONE WEIGHT'       => $gem->diamond_weight ?? 'N/A',
            'FINISH'             => $gem->finish ?? 'N/A',
        ];
    @endphp

    @foreach ($gemDetails as $label => $value)
        <div class="flex">
            <span class="w-60 font-semibold">{{ $label }}:</span>
            <span class="w-60">{{ $value }}</span>
        </div>
    @endforeach
</div>

                <!-- Right Image -->
                <div class="w-1/4 flex flex-col items-end translate-y-1/4">
                    <img src="{{ $gem->image ?? 'https://via.placeholder.com/100' }}" 
                         alt="Gemstone" class="mb-2 border w-25 h-25 object-cover">
                </div>

                <!-- Barcode -->
                <div class="w-1/6 flex flex-col items-center relative">
                    <div class="transform rotate-90 absolute top-1/2 -translate-y-1/2 right-adjust">
                        <img src="data:image/png;base64,{{ $barcode }}" alt="Barcode" class="w-24">
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Footer -->
        <div class="text-white text-center py-3 text-[9px] top-1/2 bg-img-footer">
            CERTIFYING BRILLIANCE, ENSURING TRUST
        </div>
        
    </div>
    <button data-summaryno="{{ $gem->summary_no }}" id="downloadBtn"
            class="mt-2 bg-[#2270e5] text-white px-4 py-2 text-sm rounded shadow hover:bg-[#5a3e29] transition">
            ⬇ Download
        </button>
</div>

<script>
document.getElementById('downloadBtn').addEventListener('click', function (event) {
    const targetWidth = 1004;  // final image width
    const targetHeight = 650;  // final image height

    html2canvas(document.querySelector("#certificate"), {
        scale: 2, // high resolution capture
        backgroundColor: null
    }).then(capturedCanvas => {
        // Create final canvas
        const finalCanvas = document.createElement('canvas');
        finalCanvas.width = targetWidth;
        finalCanvas.height = targetHeight;
        const ctx = finalCanvas.getContext('2d');

        // Optional: Fill with white background
        ctx.fillStyle = "#fff";
        ctx.fillRect(0, 0, targetWidth, targetHeight);

        // Draw certificate stretched exactly to target size
        ctx.drawImage(
            capturedCanvas,
            0, 0, capturedCanvas.width, capturedCanvas.height, // source
            0, 0, targetWidth, targetHeight // destination
        );

        // Download PNG
        const link = document.createElement('a');
        link.download = event.target.getAttribute('data-summaryno') + '.png';
        link.href = finalCanvas.toDataURL('image/png');
        link.click();
    });
});

</script>

</body>
</html>
