<form method="post" enctype="multipart/form-data" action="{{ route('update', $contact->id) }}">
    @csrf
    Name:<input type="text" name="name" value="{{ $contact->name }}">
    <br>
    Phone:<input type="text" name="phone" value="{{ $contact->phone }}">
    <br>
    Address:<input type="text" name="address" value="{{ $contact->address }}">
    <br>
    Email:<input type="text" name="email" value="{{ $contact->email }}">
    <br>
    <button>Store</button>
</form>
