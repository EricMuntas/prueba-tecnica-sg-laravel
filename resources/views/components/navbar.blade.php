<nav class="site-navbar">
    <a class="brand" href="/">🛍️ <span>Crear</span>Pedidos</a>

    <a class="nav-link" href="/products">Productos</a>

    @if(Auth::check() && Auth::user()->role === 'admin')
        <a class="nav-link" href="/admin">Panel de Administración</a>
    @endif

    @auth
        <div class="nav-user">
            <span>👤 {{ Auth::user()->name }}</span>
            @if(Auth::user()->role === 'admin')
                <span class="badge bg-warning text-dark">Admin</span>
            @endif
        </div>
        <form method="POST" action="/logout">
            @csrf
            <button type="submit" class="btn-nav-logout" style="color: white;">Salir</button>
        </form>
    @else
        <a class="nav-link" href="/login">Acceder</a>
        <a class="nav-link" href="/register">Registrarse</a>
    @endauth
</nav>
