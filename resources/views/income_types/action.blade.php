<div class="d-flex gap-2">

    <a href="{{ route('income_types.edit', $model->id) }}" class="btn btn-warning btn-sm d-flex align-items-center gap-1">
        <i class="bi bi-pencil-square"></i>
    </a>

    <form action="{{ route('income_types.destroy', $model->id) }}" method="POST"
        onsubmit="return confirm('Delete this Income Type?')">
        @csrf
        @method('DELETE')
        <button class="btn btn-danger btn-sm d-flex align-items-center gap-1">
            <i class="bi bi-trash"></i>
        </button>
    </form>

</div>
