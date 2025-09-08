@props(['label', 'name', 'type' => 'text', 'value' => '', 'placeholder' => '', 'required' => false, 'error' => null])

<div class="form-group">
    @if($label)
        <label for="{{ $name }}" class="form-label">
            {{ $label }}
            @if($required)
                <span class="text-red-500">*</span>
            @endif
        </label>
    @endif
    
    <input
        type="{{ $type }}"
        name="{{ $name }}"
        id="{{ $name }}"
        value="{{ old($name, $value) }}"
        placeholder="{{ $placeholder }}"
        {{ $required ? 'required' : '' }}
        {{ $attributes->merge(['class' => 'form-input ' . ($error ? 'border-red-500' : '')]) }}
    >
    
    @if($error)
        <p class="form-error">{{ $error }}</p>
    @endif
</div>