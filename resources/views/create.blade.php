<form method="post" enctype="multipart/form-data">
    @csrf
    Name:<input type="text" name="name">
    <br>
    Phone:<input type="text" name="phone">
    <br>
    Address:<input type="text" name="address">
    <br>
    Email:<input type="text" name="email">
    <br>
    <button>Store</button>
</form>
