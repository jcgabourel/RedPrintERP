@props(['label', 'name', 'value' => '', 'placeholder' => '', 'required' => false, 'error' => null, 'rows' => 3])

<div class="form-group">
    @if($label)
        <label for="{{ $name }}" class="form-label">
            {{ $label }}
            @if($required)
                <span class="text-red-500">*</span>
            @endif
        </label>
    @endif
    
    <textarea
        name="{{ $name }}"
        id="{{ $name }}"
        rows="{{ $rows }}"
        placeholder="{{ $placeholder }}"
        {{ $required ? 'required' : '' }}
        {{ $attributes->merge(['class' => 'form-textarea ' . ($error ? 'border-red-500' : '')]) }}
    >{{ old($name, $value) }}</textarea>
    
    @if($error)
        <p class="form-error">{{ $error }}</p>
    @endif
</div>