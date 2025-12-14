<select name="{{ $name }}" {{ $attributes->merge(['class' => 'form-select']) }}>
    <option> Select One </option>
    @foreach ($options as $value => $label)
        <option value="{{ $value }}" @selected($selected == $value)>
            {{ $label }}
        </option>
    @endforeach
</select>
