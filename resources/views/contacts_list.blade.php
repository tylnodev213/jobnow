<button onclick="location.href='{{ route('create') }}'">Add contact</button>
<table border="1" cellpadding="0">
    <thead>
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Phone</th>
            <th>Address</th>
            <th>Email</th>
            <th colspan="2">Action</th>
        </tr>
    </thead>
    <tbody>
        @foreach($contacts as $contact)
            <tr>
                <td>{{ $contact->id }}</td>
                <td>{{ $contact->name }}</td>
                <td>{{ $contact->phone }}</td>
                <td>{{ $contact->address }}</td>
                <td>{{ $contact->email }}</td>
                <td>
                    <button onclick="location.href='{{ route('edit', $contact->id) }}'">
                        Edit
                    </button>
                </td>
                <td>
                    <form method="post" action="{{ route('destroy', $contact->id) }}">
                        @csrf
                        <button onclick="return confirm('Are you sure delete {{ $contact->name }}?');">Delete</button>
                    </form>
                </td>
            </tr>
        @endforeach
    </tbody>
</table>
