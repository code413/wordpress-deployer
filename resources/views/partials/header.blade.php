<header class="header mb-4 py-0">
    <div class="container">
        <div class="d-flex align-items-center">
            <a class="header-brand" href="{{ url('/') }}">
                Wordpress Deployer
            </a>
            <div class="d-flex order-lg-2 ml-auto">
                @guest
                    <div class="nav-item d-none d-md-flex">
                        <a class="btn btn-sm btn-outline-primary"
                           href="{{ route('login') }}">{{ __('Login') }}</a>
                        @if (true || Route::has('register'))
                            <a class="btn btn-sm ml-2 btn-outline-success" href="">{{ __('Register') }}</a>
                        @endif
                    </div>
                @else
                    <ul class="nav nav-tabs border-0 flex-column flex-lg-row">

                            @if(\App\Models\Profile::count() < 1)
                                <li class="nav-item">
                                    <a class="nav-link" href="{{action('ProfileController@create')}}"><i
                                                class="fe fe-plus-square"></i> Create Profile</a>
                                </li>
                            @else
                                <li class="nav-item">
                                    <a class="nav-link @if(session()->get('active') === 'profile') active @endif" href="{{action('ProfileController@index')}}">Deployments</a>
                                </li>
                            @endif

                        <li class="nav-item dropdown">
                            <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button"
                               data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                {{ Auth::user()->name }}
                            </a>

                            <div class="dropdown-menu dropdown-menu-right dropdown-menu-arrow" x-placement="bottom-end" aria-labelledby="navbarDropdown">
                                <form action="{{ route('logout') }}" method="POST">
                                    <button class="dropdown-item">
                                        <i class="dropdown-icon fe fe-log-out"></i> {{ __('Logout') }}
                                    </button>
                                    @csrf
                                </form>
                            </div>
                        </li>
                    </ul>
                @endguest
            </div>
            <a href="#" class="header-toggler d-lg-none ml-3 ml-lg-0" data-toggle="collapse"
               data-target="#headerMenuCollapse">
                <span class="header-toggler-icon"></span>
            </a>
        </div>
    </div>
</header>

