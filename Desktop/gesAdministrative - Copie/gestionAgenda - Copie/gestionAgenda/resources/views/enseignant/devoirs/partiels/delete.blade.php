<td>

    <form action="{{ route('devoirs.destroy', $devoir->id) }}" method="POST" style="display:inline-block;" onsubmit="return confirm('Confirmer la suppression ?')">
        @csrf
        @method('DELETE')
        <button type="submit" class="btn btn-primary btn-sm" style="background-color: #007bff; border-color: #007bff;">Supprimer</button>
    </form>
</td>
