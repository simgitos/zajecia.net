@props(['name', 'label' => '', 'options' => [], 'required' => false, 'selected' => null])

<div class="mb-3">
    @if($label)
        <label for="{{ $name }}" class="form-label">{{ $label }}</label>
    @endif
    <select
        name="{{ $name }}"
        id="{{ $name }}"
        class="form-select @error($name) is-invalid @enderror"
        {{ $required ? 'required' : '' }}
    >
        @foreach($options as $value => $text)
            <option value="{{ $value }}"
                @if(old($name, $selected) == $value) selected @endif>
                {{ $text }}
            </option>
        @endforeach
    </select>
    @error($name)
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>