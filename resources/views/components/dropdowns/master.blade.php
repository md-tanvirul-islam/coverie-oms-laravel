@props(['name', 'options' => [], 'selected' => null])

<select name="{{ $name }}" id="{{ $name }}"
    {{ $attributes->class(['form-select', 'is-invalid' => $errors->has($name), 'select2']) }}>
    @if (!$attributes->get('multiple'))
        <option>Select One</option>
    @endif

    @foreach ($options as $value => $label)
        <option value="{{ $value }}" @selected(is_array($selected) ? in_array($value, old($name, $selected)) : old($name, $selected) == $value)>
            {{ $label }}
        </option>
    @endforeach
</select>
