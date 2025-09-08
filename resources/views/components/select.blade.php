@props(['label', 'name', 'options' => [], 'selected' => '', 'placeholder' => 'Seleccionar...', 'required' => false, 'error' => null])

<div class="form-group">
    @if($label)
        <label for="{{ $name }}" class="form-label">
            {{ $label }}
            @if($required)
                <span class="text-red-500">*</span>
            @endif
        </label>
    @endif
    
    <select
        name="{{ $name }}"
        id="{{ $name }}"
        {{ $required ? 'required' : '' }}
        {{ $attributes->merge(['class' => 'form-select ' . ($error ? 'border-red-500' : '')]) }}
    >
        <option value="">{{ $placeholder }}</option>
        @foreach($options as $value => $text)
            <option value="{{ $value }}" {{ old($name, $selected) == $value ? 'selected' : '' }}>
                {{ $text }}
            </option>
        @endforeach
    </select>
    
    @if($error)
        <p class="form-error">{{ $error }}</p>
    @endif
</div>