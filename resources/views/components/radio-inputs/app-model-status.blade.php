@foreach ($options as $option => $value)
    <div class="form-check">
        <input class="form-check-input" type="radio" name="{{ $name }}" value="{{$value}}" id="{{ $name }}-{{ $value }}" @checked($checked == $value)>
        <label class="form-check-label" for="{{ $name }}-{{ $value }}">
            {{ $option }}
        </label>
    </div>
@endforeach
