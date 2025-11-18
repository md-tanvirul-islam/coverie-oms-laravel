<a href="{{ route('users.edit', $row->id) }}" 
   class="btn btn-sm btn-warning">Edit</a>

<form action="{{ route('users.destroy', $row->id) }}"
      method="POST"
      class="d-inline">
    @csrf
    @method('DELETE')
    <button class="btn btn-sm btn-danger" onclick="return confirm('Delete?')">
        Delete
    </button>
</form>
