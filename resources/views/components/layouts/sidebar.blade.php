<nav class="navbar bg-body-tertiary fixed-top" dir="{{ $isRtl ? 'rtl' : 'ltr' }}">
    <div class="container-fluid">

        <button class="navbar-toggler" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasNavbar"
                aria-controls="offcanvasNavbar" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <a class="navbar-brand" href="#">{{ $title ?? __('messages.default_site_title') }}</a>

        <livewire:settings.language-switcher/>

        <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasNavbar" aria-labelledby="offcanvasNavbarLabel">
            <div class="offcanvas-header">
                <h5 class="offcanvas-title"
                    id="offcanvasNavbarLabel">{{ $title ?? __('messages.default_site_title') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
            </div>
            <div class="offcanvas-body">
                <ul class="navbar-nav justify-content-end flex-grow-1 pe-3">

                    <li class="nav-item">
                        <a class="nav-link active" aria-current="page"
                           href="{{ url('/dashboard') }}">{{__('messages.dashboard_menu')}}</a>
                    </li>

                    @auth

                        <li class="nav-item">
                            <a class="nav-link active" aria-current="page"
                               href="{{ url('users') }}">{{__('messages.users_menu')}}</a>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link active" aria-current="page"
                               href="{{ url('projects') }}">{{__('messages.projects_menu')}}</a>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link active" aria-current="page"
                               href="{{ url('tasks') }}">{{__('messages.tasks_menu')}}</a>
                        </li>


                        <livewire:auth.logout-button/>

                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown"
                               aria-expanded="false">
                                Dropdown
                            </a>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="#">Action</a></li>
                                <li><a class="dropdown-item" href="#">Another action</a></li>
                                <li>
                                    <hr class="dropdown-divider">
                                </li>
                                <li><a class="dropdown-item" href="#">Something else here</a></li>
                            </ul>
                        </li>
                    @else
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('login') }}">{{__('messages.login_menu')}}</a>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('register') }}">{{__('messages.register_menu')}}</a>
                        </li>

                    @endauth

                </ul>
                <form class="d-flex mt-3" role="search">
                    <input class="form-control me-2" type="search"
                           placeholder="{{__('messages.general_search_button')}}"
                           aria-label="{{__('general_search_button')}}"/>
                    <button class="btn btn-outline-success"
                            type="submit">{{__('messages.general_search_button')}}</button>
                </form>
            </div>
        </div>
    </div>
</nav>
