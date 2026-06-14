<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GHC GEM STONE HALLMARK CENTRE</title>

    {{-- Favicon with cache busting --}}
    <link rel="icon" href="{{ asset('favicon.png') }}?v={{ filemtime(public_path('favicon.png')) }}" type="image/png">

    {{-- Bootstrap (CDN is already cached by browser/CDN) --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    {{-- Google Fonts (handled by Google cache) --}}
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&display=swap" rel="stylesheet">

    <style>
        body {
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            font-family: 'Montserrat', sans-serif;
        }
        main {
            flex: 1;
        }
        .search-box {
            max-width: 500px;
            margin: 20px auto 10px; /* Increased top margin */
        }
        #certificateFrame {
            width: 100%;
            height: 500px;
            border: none;
        }
        .bg-theme {
            background-color: #816242!important;
        }
        .btn-search {
            background-color: #816242!important;
            border: 0 !important;
        }
    </style>
</head>
<body>

    <!-- Header -->
    <header class="bg-theme text-white py-3">
        <div class="container d-flex align-items-center">
            <img src="{{ asset('images/id_logo.png') }}?v={{ filemtime(public_path('images/id_logo.png')) }}" alt="Logo" height="40" class="me-2">
            <div>
                <h6 class="m-0">GEM STONE HALLMARK CENTRE</h6>
                <small class="text-white-50">GEMMOLOGICAL REPORT</small>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main>
        <div class="search-box p-2 bg-light shadow rounded">
            <h5 class="mb-1">Certificate Search</h5>
            <div class="input-group">
                <input type="text" id="certificate_no" name="certificate_no" class="form-control" placeholder="Enter Certificate Number" required>
                <button id="searchBtn" class="btn btn-primary btn-search" type="submit">Search</button>
            </div>
            <div id="message" class="mt-3"></div>
        </div>
    </main>

    <!-- Modal -->
    <div class="modal fade" id="certificateModal" tabindex="-1" aria-labelledby="certificateModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-fullscreen modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="certificateModalLabel">Identification Report</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-0">
                    <iframe id="certificateFrame" style="width:100%; height:80vh; border:none;"></iframe>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-theme text-white text-center py-2">
        <small>&copy; {{ date('Y') }} GHC GEM STONE HALLMARK CENTRE. All rights reserved.</small>
    </footer>

    {{-- jQuery with cache busting (if local) --}}
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    {{-- Bootstrap JS (CDN cached) --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    {{-- Custom JS with cache busting --}}
    <script src="{{ asset('js/certificate-search.js') }}?v={{ filemtime(public_path('js/certificate-search.js')) }}"></script>

    <script>
        // Pass Laravel routes to JS
        const CERTIFICATE_SEARCH_URL = "{{ url('certificate') }}";
        const CERTIFICATE_URL = "{{ url('search_certificate') }}";
    </script>
</body>
</html>
