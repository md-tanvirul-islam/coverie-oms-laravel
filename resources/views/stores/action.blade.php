<div class="d-flex gap-2">

    <a href="{{ route('stores.show', $model->id) }}" class="btn btn-secondary btn-sm d-flex align-items-center gap-1">
        <i class="bi bi-eye"></i>
    </a>

    <a href="{{ route('stores.edit', $model->id) }}" class="btn btn-warning btn-sm d-flex align-items-center gap-1">
        <i class="bi bi-pencil-square"></i>
    </a>

    <form action="{{ route('stores.destroy', $model->id) }}" method="POST"
        onsubmit="return confirm('Delete this Store?')">
        @csrf
        @method('DELETE')
        <button class="btn btn-danger btn-sm d-flex align-items-center gap-1">
            <i class="bi bi-trash"></i>
        </button>
    </form>

</div>
