<div class="d-flex justify-content-around align-items-center bg-primary w-100 gap-2" style="height: 96px;">
    <a class="nav-link text-white" href="/">
        Dashboard
    </a>

    <a class="nav-link text-white" href="/products">
        Productos
    </a>


@if(Auth::check() && Auth::user()->role === 'admin')
    <a class="nav-link text-white" href="/admin">
        Admin
    </a>
    @endif

        @auth
   <div class="nav-link text-white">
        <form method="POST" action="/logout" class="d-inline">
            @csrf
            <button type="submit" class="btn btn-outline-light btn-sm">Cerrar sesión</button>
        </form>
    </div>
       
    @else
        <a class="nav-link text-white" href="/login">Acceder</a>
        <a class="nav-link text-white" href="/register">Registrarse</a>
    @endauth
</div>
