<div class="sidebar">

    <div class="logo">

        <h5 class="text-white fw-bold mb-0 text-uppercase" style="font-size: 13px; letter-spacing: 0.5px; line-height: 1.4;">
            Klasifikasi Keparahan Bencana
        </h5>

    </div>

    <ul>

        <li>
            <a href="{{ route('dashboard') }}" class="{{ Request::routeIs('dashboard') ? 'active' : '' }}">
                <i class="fa fa-home"></i>
                Dashboard
            </a>
        </li>

        <li>
            <a href="{{ route('classification.index') }}" class="{{ Request::routeIs('classification.*') ? 'active' : '' }}">
                <i class="fa fa-brain"></i>
                Klasifikasi
            </a>
        </li>

        <li>
            <a href="{{ route('history.index') }}" class="{{ Request::routeIs('history.*') ? 'active' : '' }}">
                <i class="fa fa-clock"></i>
                Riwayat
            </a>
        </li>

    </ul>

</div>