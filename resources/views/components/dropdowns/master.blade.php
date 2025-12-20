@props(['name', 'options' => [], 'selected' => null])

<select name="{{ $name }}" id="select2"
    {{ $attributes->class(['form-select', 'is-invalid' => $errors->has($name), 'select2']) }}>
    <option value="">Select One</option>

    @foreach ($options as $value => $label)
        <option value="{{ $value }}" @selected(is_array($selected) ? in_array($value, old($name, $selected)) : old($name, $selected) == $value)>
            {{ $label }}
        </option>
    @endforeach
</select>
