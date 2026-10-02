@props(['name', 'label' => '', 'required' => false, 'id' => null, 'value' => null])

<div class="mb-3">
    @if ($label)
        <label for="{{ $name }}" class="form-label">{{ $label }}</label>
    @endif

    <textarea name="{{ $name }}" id="{{ $id }}"
        class="form-control @error($name) is-invalid @enderror" {{ $required ? 'required' : '' }} {{ $attributes }}>{{ old($name, $value) }}</textarea>

    @error($name)
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>