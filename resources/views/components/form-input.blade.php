@props([
    'name',
    'label' => null,
    'value' => null,
    'type' => 'text',
    'disabled' => false,
    'readonly' => false,
    'placeholder' => ''
])

@if ($label)
    <label for="{{ $name }}" class="form-label">
        {{ $label }}
    </label>
@endif

@php
    $value = $type === 'password' || $type === 'file'
        ? null
        : old($name, $value ?? '');
@endphp

<input
    type="{{ $type }}"
    id="{{ $name }}"
    name="{{ $name }}"
    value="{{ $value }}"
    @disabled($disabled)
    @readonly($readonly)
    {{ $attributes->merge(['class' => 'form-control' . ($errors->has($name) ? ' is-invalid' : '')]) }}
>

@error($name)
    <div class="invalid-feedback">{{ $message }}</div>
@enderror
