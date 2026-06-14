@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto p-6 bg-white rounded-lg shadow-md mt-10">
    <h1 class="text-2xl font-bold mb-6 text-gray-800">Edit Gem</h1>

    @if ($errors->any())
        <div class="mb-4 p-4 bg-red-100 text-red-700 rounded">
            <ul class="list-disc pl-5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('gems.update', old('id', $gem->id)) }}" method="POST" enctype="multipart/form-data" class="space-y-5">
        @csrf
        @method('PUT')
        <div>
            <label class="block font-medium text-gray-700 mb-2">Type</label>
            <div class="flex items-center space-x-6">
               <label class="inline-flex items-center">
                    <input 
                        type="radio" 
                        name="type" 
                        id="type_gem" 
                        value="One Silver Ring Studded with Gemstone"
                        {{ $gem->description == 'One Silver Ring Studded with Gemstone' ? 'checked' : '' }}
                        class="text-blue-600 focus:ring-blue-500 border-gray-300">
                    <span class="ml-2 text-gray-700">One Silver Ring Studded with Gemstone</span>
                </label>

                <label class="inline-flex items-center">
                    <input type="radio" name="type" id="type_other" value="Others"
                        {{ $gem->description !== 'One Silver Ring Studded with Gemstone' ? 'checked' : '' }}
                        class="text-blue-600 focus:ring-blue-500 border-gray-300">
                    <span class="ml-2 text-gray-700">Others</span>
                </label>
            </div>
        </div>
        <div>
            <label for="description" class="block font-medium text-gray-700">Description</label>
            <textarea name="description" id="description" maxlength="115" rows="3" oninput="updateCharCount(this)" 
                      class="mt-1 block w-full border border-gray-300 rounded-lg shadow-sm p-2 focus:ring-blue-500 focus:border-blue-500">{{ old('description', $gem->description) }}</textarea>
            <p id="descCounter" class="text-sm text-gray-500 mt-1">0 / 115 characters</p>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 relative">
            <div>
                <label class="block font-medium text-gray-700">Grade/Clarity</label>
                <div class="flex items-center space-x-4 mt-2">
                    <label class="inline-flex items-center">
                        <input type="radio" name="clarity_type" value="AA" class="clarity-radio text-blue-600" {{ old('clarity_type', $gem->clarity == 'AA' ? 'AA' : 'Others') == 'AA' ? 'checked' : '' }}>
                        <span class="ml-2 text-gray-700">AA</span>
                    </label>
                    <label class="inline-flex items-center">
                        <input type="radio" name="clarity_type" value="Others" class="clarity-radio text-blue-600" {{ old('clarity_type', $gem->clarity == 'AA' ? 'AA' : 'Others') == 'Others' ? 'checked' : '' }}>
                        <span class="ml-2 text-gray-700">Others</span>
                    </label>
                </div>
                <div id="clarity_other_div" class="mt-2 {{ old('clarity_type', $gem->clarity == 'AA' ? 'AA' : 'Others') == 'Others' ? '' : 'hidden' }}">
                    <label for="clarity" class="sr-only">Other Clarity</label>
                    <input type="text" name="clarity" id="clarity"
                           value="{{ old('clarity', $gem->clarity == 'AA' ? '' : $gem->clarity) }}"
                           class="block w-full border border-gray-300 rounded-lg shadow-sm p-2">
                </div>
            </div>
             <div>
                <label class="block font-medium text-gray-700 mb-2">Weight Type</label>
                <div class="flex items-center space-x-4 mb-2">
                    <label class="inline-flex items-center">
                        <input type="radio" name="weight_type" id="weight_type_gross" value="gross_weight" class="weight-type-radio text-blue-600" 
                               {{ old('weight_type', $gem->weight_type ?? 'gross_weight') == 'gross_weight' ? 'checked' : '' }}>
                        <span class="ml-2 text-gray-700">Gross Weight</span>
                    </label>
                    <label class="inline-flex items-center">
                        <input type="radio" name="weight_type" id="weight_type_hardness" value="hardness" class="weight-type-radio text-blue-600"
                               {{ old('weight_type', $gem->weight_type ?? 'gross_weight') == 'hardness' ? 'checked' : '' }}>
                        <span class="ml-2 text-gray-700">Hardness</span>
                    </label>
                </div>
                <div id="gross_weight_div" class="{{ old('weight_type', $gem->weight_type ?? 'gross_weight') == 'hardness' ? 'hidden' : '' }}">
                <label for="gross_weight" class="block font-medium text-gray-700">Gross Weight</label>
                    <div class="relative mt-1">
                <input required type="text" name="gross_weight" id="gross_weight"
                               value="{{ old('gross_weight', $gem->weight_type == 'hardness' ? '' : $gem->gross_weight) }}"
                               class="block w-full border border-gray-300 rounded-lg shadow-sm p-2 pr-12">
                        <span class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-500 select-none pointer-events-none">Gms</span>
                    </div>
                </div>
                <div id="hardness_div" class="{{ old('weight_type', $gem->weight_type ?? 'gross_weight') == 'hardness' ? '' : 'hidden' }}">
                    <label for="hardness" class="block font-medium text-gray-700">Hardness</label>
                    <input type="text" name="hardness" id="hardness"
                           value="{{ old('hardness', $gem->weight_type == 'hardness' ? $gem->gross_weight : '') }}"
                       class="mt-1 block w-full border border-gray-300 rounded-lg shadow-sm p-2">
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-1 gap-4 relative">
       
            <div>
                <label for="diamond_weight" class="block font-medium text-gray-700">Stone Weight</label>
                <input required type="text" name="diamond_weight" id="diamond_weight"
                       value="{{ old('diamond_weight', $gem->diamond_weight) }}"
                       class="mt-1 block w-full border border-gray-300 rounded-lg shadow-sm p-2">
                <span class="absolute inset-y-0 right-0 flex items-center pr-3 mt-7 text-gray-500 select-none pointer-events-none">Cts</span>
            </div>
        </div>
        <div>
            <label for="image" class="block font-medium text-gray-700">Gem Image</label>
            <input type="file" name="image" id="image"
                   class="mt-1 block w-full text-gray-700">
            @if ($gem->image)
                <div class="mt-3">
                    <img src="{{ $gem->image }}" alt="Gem Image" class="rounded-lg shadow-md max-h-40">
                </div>
            @endif
        </div>
        @php
            $stones = [
                'RUBY',
                'PEARL',
                'CORAL',
                'NATURAL EMERALD',
                'NATURAL WHITE ROCK STONE',
                'YELLOW SAPPHIRE',
                'BLUE SAPPHIRE',
                'NATURAL GOMED - HESSONITE STONE',
                'NATURAL CATSEYE - CHRYSOBERYL',
                'NATURAL GARNET STONE',
                'NATURAL AMETHYST STONE',
                'NATURAL AQUAMARINE STONE',
                'NATURAL AVENTURINE STONE',
                'NATURAL IDOCRAISE/PERIDOT STONE',
                'NATURAL OPAL STONE',
                'NATURAL MOONSTONE',
                'NATURAL CITRINE STONE',
                'NATURAL TORQUOISE STONE',
                'NATURAL THULITE STONE',
                'NATURAL LABRODORITE STONE',
                'NATURAL YELLOW STONE',
                'NATURAL LAPIS BLUE STONE',
                'NATURAL MADRAS SANDSTONE',
                'OTHERS',
            ];

            // Check if the comment matches one of the stones (excluding 'OTHERS')
            $matchesStone = in_array($gem->comment, array_diff($stones, ['OTHERS']));
        @endphp

        <div>
            <label class="block font-medium text-gray-700 mb-2">Comment Type (Select Gemstone)</label>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
                @foreach ($stones as $stone)
                    <label class="inline-flex items-center">
                        <input 
                            type="radio" 
                            name="comment_type" 
                            value="{{ $stone }}"
                            class="gemstone-radio text-blue-600 focus:ring-blue-500 border-gray-300"
                            {{-- Check this radio if the comment matches OR default to OTHERS --}}
                            {{ ($stone === $gem->comment || ($stone === 'OTHERS' && !$matchesStone)) ? 'checked' : '' }}>
                        <span class="ml-2 text-gray-700">{{ $stone }}</span>
                    </label>
                @endforeach
            </div>
        </div>
        <div>
            <label for="description" class="block font-medium text-gray-700">Comments</label>
            <textarea required name="comment" maxlength="60" oninput="updateCommentCharacterCount(this)"  id="comment" rows="3"
                      class="mt-1 block w-full border border-gray-300 rounded-lg shadow-sm p-2 focus:ring-blue-500 focus:border-blue-500">{{ old('comment', $gem->comment) }}</textarea>
            <p id="commentCounter" class="text-sm text-gray-500 mt-1">0 / 60 characters</p>
        </div>

        <div class="flex justify-end space-x-3">
            <a href="{{ route('gems.index') }}"
               class="px-4 py-2 bg-gray-300 text-gray-800 rounded-lg hover:bg-gray-400">Cancel</a>
            <button type="submit"
                    class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">Update</button>
        </div>
    </form>
</div>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const typeGem = document.getElementById('type_gem');
        const typeOther = document.getElementById('type_other');
        const descBox = document.getElementById('description');

        // Update description when "One Silver Ring Studded with Gemstone" is clicked
        typeGem.addEventListener('click', function () {
            descBox.value = 'One Silver Ring Studded with Gemstone';
            updateCharCount(descBox);
        });

        // Clear description when "Others" is clicked
        typeOther.addEventListener('click', function () {
            descBox.value = "{{ $gem->description }}";
            updateCharCount(descBox);
        });

        const radios = document.querySelectorAll('.gemstone-radio');
        const commentBox = document.getElementById('comment');

        radios.forEach(radio => {
            radio.addEventListener('click', function () {
                if (this.value.toUpperCase() === 'OTHERS') {
                    commentBox.value = '';
                    commentBox.removeAttribute('readonly');
                } else {
                    commentBox.value = this.value;
                    commentBox.setAttribute('readonly', true);
                }
                updateCommentCharacterCount(commentBox);
            });
        });

        // Logic for Grade/Clarity
        const clarityRadios = document.querySelectorAll('.clarity-radio');
        const clarityOtherDiv = document.getElementById('clarity_other_div');
        const clarityInput = document.getElementById('clarity');

        clarityRadios.forEach(radio => {
            radio.addEventListener('change', function() {
                if (this.value === 'Others') {
                    clarityOtherDiv.classList.remove('hidden');
                    clarityInput.setAttribute('required', 'required');
                } else {
                    clarityOtherDiv.classList.add('hidden');
                    clarityInput.removeAttribute('required');
                    clarityInput.value = ''; // Clear the input when switching to AA
                }
            });
        });

        // Set initial state for clarity required attribute
        const initialClarityType = document.querySelector('.clarity-radio:checked').value;
        if (initialClarityType === 'Others') {
            clarityInput.setAttribute('required', 'required');
        } else {
            clarityInput.removeAttribute('required');
        }

        // Weight Type radio button handler
        const weightTypeRadios = document.querySelectorAll('.weight-type-radio');
        const grossWeightDiv = document.getElementById('gross_weight_div');
        const hardnessDiv = document.getElementById('hardness_div');
        const grossWeightInput = document.getElementById('gross_weight');
        const hardnessInput = document.getElementById('hardness');

        function toggleWeightFields() {
            const selectedWeightType = document.querySelector('.weight-type-radio:checked');
            if (!selectedWeightType) return;
            
            const value = selectedWeightType.value;
            
            if (value === 'gross_weight') {
                grossWeightDiv.classList.remove('hidden');
                hardnessDiv.classList.add('hidden');
                grossWeightInput.setAttribute('required', 'required');
                hardnessInput.removeAttribute('required');
            } else if (value === 'hardness') {
                grossWeightDiv.classList.add('hidden');
                hardnessDiv.classList.remove('hidden');
                grossWeightInput.removeAttribute('required');
                hardnessInput.setAttribute('required', 'required');
            }
        }

        weightTypeRadios.forEach(radio => {
            radio.addEventListener('change', toggleWeightFields);
        });

        // Initialize on page load
        toggleWeightFields();
    });
    function updateCharCount(el) {
        const counter = document.getElementById('descCounter');
        counter.textContent = el.value.length + " / 115 characters";
    }
    function updateCommentCharacterCount(el) {
        const counter = document.getElementById('commentCounter');
        counter.textContent = el.value.length + " / 60 characters";
    }
</script>
@endsection
