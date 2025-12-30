<div class="d-flex gap-2">

    <a href="{{ route('expenses.edit', $model->id) }}"
       class="btn btn-warning btn-sm d-flex align-items-center gap-1">
        <i class="bi bi-pencil-square"></i>
    </a>

    <form action="{{ route('expenses.destroy', $model->id) }}"
          method="POST"
          onsubmit="return confirm('Delete this Expense?')">
        @csrf
        @method('DELETE')
        <button class="btn btn-danger btn-sm d-flex align-items-center gap-1">
            <i class="bi bi-trash"></i>
        </button>
    </form>

</div>
