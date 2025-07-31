<form action="/register-siswa" method="POST">
    @csrf
    <input type="text" name="username" placeholder="Username">
    <input type="email" name="email" placeholder="Email">
    <input type="password" name="password" placeholder="Password">
    <input type="password" name="password_confirmation" placeholder="Konfirmasi Password">
    <input type="text" name="nama_lengkap" placeholder="Nama Lengkap">
    <button type="submit">Register</button>
</form>
