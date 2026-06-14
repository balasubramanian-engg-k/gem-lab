@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto p-2 h-screen flex flex-col">
    <iframe class="hidden w-full h-0 border rounded mt-4" id="exportFrame"></iframe>
    <!-- Header -->
     <!-- Search Bar -->
    <!-- Import Modal -->
   <div id="importModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden">
    <div class="bg-white p-6 rounded shadow-lg w-1/2">
        <h2 class="text-lg font-bold mb-4">Import CSV</h2>

        <!-- File Upload -->
        <input type="file" id="csvFile" accept=".csv" class="mb-2">

        <!-- Sample Template Download -->
        <div class="mb-4">
            <a href="{{ asset('templates/sample-import-template.csv') }}" 
               download 
               class="text-blue-600 hover:underline text-sm font-medium flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" 
                     class="h-4 w-4 mr-1 text-blue-600" 
                     fill="none" 
                     viewBox="0 0 24 24" 
                     stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M4 16v2a2 2 0 002 2h12a2 2 0 002-2v-2M7 10l5 5m0 0l5-5m-5 5V4" />
                </svg>
                Download Sample Template
            </a>
        </div>

        <!-- Failed Rows Table -->
        <div id="failedRowsContainer" class="hidden">
            <h3 class="font-semibold mb-2 text-red-600">Failed Rows</h3>
            <table class="w-full border border-gray-300 text-sm">
                <thead class="bg-gray-200">
                    <tr>
                        <th class="border px-2 py-1">Row No</th>
                        <th class="border px-2 py-1">Error</th>
                    </tr>
                </thead>
                <tbody id="failedRowsBody"></tbody>
            </table>
        </div>

        <!-- Buttons -->
        <div class="flex justify-end space-x-2 mt-4">
            <button id="closeModal" class="bg-gray-500 text-white px-3 py-1 rounded">Close</button>
            <button id="uploadBtn" class="bg-green-600 text-white px-3 py-1 rounded">Upload</button>
        </div>
    </div>
</div>
    <div class="flex justify-between items-center mb-1 space-x-2">
        <h1 class="text-1xl font-bold text-gray-800">Gems Listing</h1>
        <div class="flex">
            Selected: <span class="font-bold px-1" id="selectedCount">0</span>
        </div>
         <div class="flex  space-x-2">
             <form method="GET" action="{{ route('gems.index') }}" class="flex items-center gap-2">
            <input 
                type="text" 
                name="search" 
                value="{{ request('search') }}" 
                placeholder="Search gems..."
                class="w-full md:w-full px-2 py-1 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none"
            >
            <button 
                type="submit" 
                class="px-2 py-1 bg-blue-600 text-white text-sm rounded-lg shadow hover:bg-blue-700 transition">
                Search
            </button>
        </form>
        @if (Auth::check())
            <button id="exportBtn" 
                    class="px-2 py-1 bg-blue-600 text-white text-sm rounded-lg shadow hover:bg-blue-700 transition">
                    Export Selected
                </button>
                <!-- Export Button -->
                <a href="{{ route('save.downloadZip') }}" 
                class="px-2 py-1 bg-blue-600 text-white text-sm rounded-lg shadow hover:bg-blue-700 transition">
                    Export latest backup
                </a>
                <!-- Import Button -->
                <button id="importButton" 
                        class="bg-blue-600 text-white px-2  text-sm py-1 rounded hover:bg-blue-700">
                    Import CSV
                </button>
        @endif
        </div>
        <a href="{{ url('gem-admin/gems/create') }}"
        class="px-2 py-1 text-sm bg-blue-600 text-white rounded-lg shadow hover:bg-blue-700 hover:scale-105 transition">
            Add Gem
        </a>
    </div>
    <div id="certificateModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50">
        <div class="relative max-w-lg w-full bg-white rounded-lg shadow-xl overflow-hidden max-h-[80vh] overflow-y-auto">
        <!-- Close Button -->
            <button 
                onclick="closeCardModal()" 
                class="absolute top-1 right-1 z-10 bg-white hover:bg-gray-100 text-gray-600 hover:text-gray-800 rounded-full p-1 shadow-md transition-colors duration-200"
                >
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
            <div class="certificate-content pt-6"></div>
        </div>
    </div>
    <div id="viewModal" class="fixed inset-0 hidden bg-black bg-opacity-50 items-center justify-center z-50">
  <div class="bg-white rounded-lg p-5 max-w-md w-full shadow-lg relative">
    <!-- Close Button -->
    <button onclick="closeModal()" 
            class="absolute top-0 right-3 text-gray-500 hover:text-red-500 text-xl font-bold">&times;</button>



    <!-- Gem Details -->
    <div id="gemDetails" class="text-sm space-y-2 p-4 bg-white rounded-lg shadow border border-gray-200">
        <p class="justify-between"><strong class="text-gray-700">Summary No:</strong> <span id="summary_no" class="text-gray-500 font-medium"></span></p>
        <p class="justify-between"><strong class="text-gray-700">Description:</strong> <span id="description" class="text-gray-500 font-medium"></span></p>
        <p class="justify-between"><strong class="text-gray-700">Grade/Clarity:</strong> <span id="clarity" class="text-gray-500 font-medium"></span></p>
        <p class="justify-between"><strong class="text-gray-700">Gross Weight/Hardness:</strong> <span id="gross_weight" class="text-gray-500 font-medium"></span></p>
        <p class="justify-between"><strong class="text-gray-700">Stone Weight:</strong> <span id="diamond_weight" class="text-gray-500 font-medium"></span></p>
        <p class="justify-between"><strong class="text-gray-700">Conclusion:</strong> <span id="comments" class="text-gray-500 font-medium"></span></p>
        <div class="mt-3">
            <strong class="text-gray-700 block mb-1">Image:</strong>
            <img id="image" src="" alt="Gem Image" class="w-40 h-auto rounded shadow border border-gray-300" />
        </div>
    </div>


    <!-- Close Button -->
  </div>
</div>
    <!-- Table -->
    <div class="overflow-x-auto  bg-white rounded-lg shadow flex-1 overflow-y-auto">
        <table class="min-w-full border border-gray-200">
            <thead class="bg-gray-100 border-b border-gray-200">
                <tr>
                    <th class="px-4 py-2 text-center"></th>
                    <th class="px-4 py-2 text-left text-sm font-semibold text-gray-700">Summary No</th>
                    <th class="px-4 py-2 text-left text-sm font-semibold text-gray-700">Description</th>
                    <th class="px-4 py-2 text-left text-sm font-semibold text-gray-700">Gross Weight/Hardness</th>
                    <th class="px-4 py-2 text-left text-sm font-semibold text-gray-700">Stone Weight</th>
                    <th class="px-4 py-2 text-left text-sm font-semibold text-gray-700">Conclusion</th>
                    <th class="px-4 py-2 text-left text-sm font-semibold text-gray-700">Created By</th>
                    <th class="px-4 py-2 text-left text-sm font-semibold text-gray-700">Date created</th>
                    <th class="px-4 py-2 text-center text-sm font-semibold text-gray-700">Actions</th>
                </tr>
            </thead>
            <tbody class="overflow-y-auto">
                @forelse($gems as $gem)
                <tr class="border-b hover:bg-gray-50">
                    <td class="px-4 py-2 text-center">
                    <input type="checkbox" data-generated="{{ $gem->certificate_generated }}" name="selected_gems[]" value="{{ $gem->summary_no }}" class="gem-checkbox form-checkbox h-4 w-4 text-blue-600">
                </td>
                    <td class="px-4 py-2 text-sm text-gray-700">{{ $gem->summary_no }}</td>
                    <td class="px-4 py-2 w-60 text-sm text-gray-700">{{ $gem->description }}</td>
                    <td class="px-4 py-2 text-sm text-gray-700">{{ trim($gem->gross_weight) }}{{ ($gem->weight_type ?? 'gross_weight') == 'hardness' ? '' : ' Gms' }}</td>
                    <td class="px-4 py-2 text-sm text-gray-700">{{ trim($gem->diamond_weight) }} Cts</td>
                    <td class="px-4 py-2 text-sm text-gray-700">{{ $gem->comment }}</td>
                    <td class="px-4 py-2 text-sm text-gray-700">{{ $gem->creator ? $gem->creator->name : 'Admin' }}</td>
                    <td class="px-4 py-2 text-sm text-gray-700">{{ $gem->created_at ? \Carbon\Carbon::parse($gem->created_at)->format('Y-m-d') : '-' }}</td>
                    <td class="px-4 py-2 text-center space-x-2">
                        <!-- View -->
                        @if (Auth::check() && Auth::user()->is_admin)
                            <button onclick="viewCard('{{ $gem->summary_no }}')" title="View">
                                <img src="{{ asset('icons/view-card.svg') }}" class="w-5 h-5 inline-block hover:scale-110 transition">
                            </button>
                            <button onclick="viewGem('{{ $gem->id }}')" title="View">
                                <img src="{{ asset('icons/view.svg') }}" class="w-5 h-5 inline-block hover:scale-110 transition">
                            </button>
                        @endif
                        <!-- Edit -->
                        <a href="{{ route('gems.edit', $gem->id) }}" title="Edit">
                            <img src="{{ asset('icons/edit.svg') }}" class="w-5 h-5 inline-block hover:scale-110 transition">
                        </a>

                        <!-- Delete -->
                        @if (Auth::check() && Auth::user()->is_admin)
                            <form action="{{ route('gems.destroy', $gem->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Are you sure?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" title="Delete">
                                    <img src="{{ asset('icons/delete.svg') }}" class="w-5 h-5 inline-block hover:scale-110 transition">
                                </button>
                            </form>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="9" class="px-4 py-6 text-center text-gray-500">No gems found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="mt-4">
        {{ $gems->links() }}
    </div>
</div>

<!-- Loading Overlay -->
<div id="loadingOverlay" 
     class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white p-6 rounded-lg shadow-lg flex items-center space-x-3">
        <svg class="animate-spin h-6 w-6 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"></path>
        </svg>
        <span class="text-gray-700 font-medium">Exporting certificates...</span>
    </div>
</div>


<!-- JS for Modal -->
<script>
function openModal() {
  $("#viewModal").removeClass("hidden").addClass("flex");
}

// Hide modal
function closeModal() {
  $("#viewModal").addClass("hidden").removeClass("flex");
}

function closeCardModal() {
    $('#certificateModal').addClass('hidden');
    $('.certificate-content').html('');
}



// Fetch and display gem details
function viewGem(id) {
  $.ajax({
    url: "/gem-admin/gems/" + id, // Laravel route: Route::get('/get-gem/{id}', ...)
    method: "GET",
    dataType: "json",
    success: function(data) {
      // Set image
      $('#summary_no').text(data.summary_no);
        $('#description').text(data.description);
        $('#stone_type').text(data['stone_type']);
        $('#shape').text(data.shape);
        $('#gross_weight').text(data.gross_weight);
        $('#diamond_weight').text(data.diamond_weight);
        $('#color').text(data.color);
        $('#clarity').text(data.clarity);
        $('#finish').text(data.finish);
        $('#comments').text(data.comment);
        $('#image').attr('src', data.image);

      // Show modal
      openModal();
    },
    error: function() {
      alert("Error fetching gem details.");
    }
  });
}

function viewCard(id) {
    window.open("{{ url('search_certificate') }}/"+id, "_blank");
//   $.ajax({
//     url: "/search_certificate/" + id+'/card', // Laravel route: Route::get('/get-gem/{id}', ...)
//     method: "GET",
//     success: function(data) {
//         $('#certificateModal').removeClass('hidden');
//         $('.certificate-content').html(data);
//     },
//     error: function() {
//       alert("Error fetching gem details.");
//     }
//   });
}
function openImportModal() {
    document.getElementById('importModal').classList.remove('hidden');
    document.getElementById('importModal').classList.add('flex');
}
function closeImportModal() {
    document.getElementById('importModal').classList.add('hidden');
}
$(document).ready(function(){
    $.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});

    $("#importButton").click(function(){
        $('#importModal').fadeIn();
        $('#importModal').css("display", "flex");
        $("#importModal").addClass("flex");
    })
     $("#closeImport").click(function(){
        $('#importModal').fadeOut();
        // $('#importModal').css("display", "none");
        $("#importModal").removeClass("flex");
    })

     $("#closeModal").click(function () {
         $('#importModal').fadeOut();
        // $('#importModal').css("display", "none");
        $("#importModal").removeClass("flex");
    });

    // Upload CSV
    $("#uploadBtn").click(function () {
        let file = $("#csvFile")[0].files[0];
        if (!file) {
            alert("Please select a CSV file");
            return;
        }

        let formData = new FormData();
        formData.append("file", file);
        
        $.ajax({
           url: "/gem-admin/gems/import-csv",
            type: "POST",
            data: formData,
            processData: false,
            contentType: false,
            success: function (response) {
                if (response.failed && response.failed.length > 0) {
                    // Show failed rows
                    let tbody = "";
                    response.failed.forEach(row => {
                        tbody += `<tr>
                            <td class="border px-2 py-1">${row.row_no}</td>
                            <td class="border px-2 py-1">${row.error}</td>
                        </tr>`;
                    });
                    $("#failedRowsBody").html(tbody);
                    $("#failedRowsContainer").removeClass("hidden");
                } else {
                    // All successful
                    $("#importModal").addClass("hidden");
                    alert("✅ All records uploaded successfully!");
                    window.location.reload();
                }
            },
            error: function (xhr) {
                alert("Something went wrong: " + xhr.responseText);
            }
        });
    });
    $('#exportBtn').on('click', function() {
        let selected = [];
        let selectedPost = [];
        $('.gem-checkbox:checked').each(function() {
            selectedPost.push($(this).val());
            selected.push({
                value: $(this).val(),
                generated: $(this).data('generated') // fetches data-generated attribute
            });
        });
        if (selected.length === 0) {
            alert('Please select at least one record to export.');
            return;
        }
        if (selected.length > 10) {
            alert('You can export a maximum of 10 records at a time.');
            return;
        }

        let i = 0;
        let iframe = $('#exportFrame');
        $('#loadingOverlay').removeClass('hidden');
        iframe.removeClass('hidden');
        function loadNextGem() {
            if (i < selected.length) {
                if(selected[i].generated == '0') {
                     let summaryNo = selected[i].value;
                    iframe.attr('src', `{{ env('APP_URL') }}/export_certificate/${summaryNo}`);
                    iframe.off('load').on('load', function() {
                        setTimeout(() => {
                            i++;
                            loadNextGem();
                            }, 2000); 
                        });
                } else {
                    i++;
                    loadNextGem();
                }
            } 
            else {
                // After all gems are processed, trigger ZIP download
                $.ajax({
                    url: '/gems/export-gems',
                    method: 'POST',
                    data: JSON.stringify({ gems: selectedPost }),
                    contentType: 'application/json',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    xhrFields: {
                        responseType: 'blob'
                    },
                    success: function(blob) {
                        let url = window.URL.createObjectURL(blob);
                        let a = document.createElement('a');
                        a.href = url;
                        a.download = 'export.zip';
                        document.body.appendChild(a);
                        a.click();
                        a.remove();
                        window.URL.revokeObjectURL(url);
                        $('#loadingOverlay').addClass('hidden');
                        iframe.addClass('hidden').attr('src', '');
                        window.location.reload();
                    },
                    error: function(err) {
                        console.error('Export error:', err);
                        alert('Something went wrong while exporting.');
                        $('#loadingOverlay').addClass('hidden');
                        iframe.addClass('hidden').attr('src', '');
                        window.location.reload();
                    }
                });
            }
        }

        // Start with the first gem
        loadNextGem();
    });

    function updateCount() {
        let count = $('.gem-checkbox:checked').length;
        $('#selectedCount').text(count);
    }

    // Update count on checkbox change
    $(document).on('change', '.gem-checkbox', function () {
        updateCount();
    });

    
});
</script>
<style>
    body {
        /* overflow: hidden; */
    }
</style>
@endsection
