<div class="d-flex gap-2">

    <!-- Edit -->
    <a href="{{ route('moderators.edit', $model->id) }}" 
       class="btn btn-sm btn-warning d-flex align-items-center gap-1">
        <i class="bi bi-pencil-square"></i> 
    </a>

    <!-- Delete -->
    <form action="{{ route('moderators.destroy', $model->id) }}"
        method="POST"
        onsubmit="return confirm('Are you sure you want to delete this?')">
        @csrf
        @method('DELETE')
        <button class="btn btn-sm btn-danger d-flex align-items-center gap-1">
            <i class="bi bi-trash"></i>
        </button>
    </form>

</div>
