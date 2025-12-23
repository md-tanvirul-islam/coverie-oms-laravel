@auth
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
        <div class="container-fluid">
            <a class="navbar-brand" href="{{ url('/') }}">
                {{ config('app.name', 'SSM OMS') }}
            </a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarSupportedContent">

                {{-- LEFT --}}
                <ul class="navbar-nav me-auto">

                    {{-- ADMIN ONLY --}}
                    @role(\App\Enums\SystemDefinedRole::ADMIN)
                        <li class="nav-item">
                            <a href="{{ route('teams.index') }}"
                                class="nav-link {{ request()->routeIs('teams.*') ? 'active' : '' }}">
                                Teams
                            </a>
                        </li>
                    @else
                        {{-- Dashboard --}}
                        <li class="nav-item">
                            <a href="{{ route('dashboard') }}"
                                class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                                Dashboard
                            </a>
                        </li>

                        {{-- Stores --}}
                        @can(\App\Enums\SystemPermission::STORE_READ->value)
                            <li class="nav-item">
                                <a href="{{ route('stores.index') }}"
                                    class="nav-link {{ request()->routeIs('stores.*') ? 'active' : '' }}">
                                    Stores
                                </a>
                            </li>
                        @endcan

                        {{-- Users --}}
                        @can(\App\Enums\SystemPermission::USER_READ->value)
                            <li class="nav-item">
                                <a href="{{ route('users.index') }}"
                                    class="nav-link {{ request()->routeIs('users.*') ? 'active' : '' }}">
                                    Users
                                </a>
                            </li>
                        @endcan

                        {{-- Roles --}}
                        @can(\App\Enums\SystemPermission::ROLE_READ->value)
                            <li class="nav-item">
                                <a href="{{ route('roles.index') }}"
                                    class="nav-link {{ request()->routeIs('roles.*') ? 'active' : '' }}">
                                    Roles
                                </a>
                            </li>
                        @endcan

                        {{-- Employees --}}
                        @can(\App\Enums\SystemPermission::EMPLOYEE_READ->value)
                            <li class="nav-item">
                                <a href="{{ route('employees.index') }}"
                                    class="nav-link {{ request()->routeIs('employees.*') ? 'active' : '' }}">
                                    Employees
                                </a>
                            </li>
                        @endcan

                        {{-- Orders --}}
                        @can(\App\Enums\SystemPermission::ORDER_READ->value)
                            <li class="nav-item">
                                <a href="{{ route('orders.index') }}"
                                    class="nav-link {{ request()->routeIs('orders.*') ? 'active' : '' }}">
                                    Orders
                                </a>
                            </li>
                        @endcan

                        {{-- Courier Paid Invoices --}}
                        @can(\App\Enums\SystemPermission::COURIER_PAID_INVOICE_READ->value)
                            <li class="nav-item">
                                <a href="{{ route('courier_paid_invoices.index') }}"
                                    class="nav-link {{ request()->routeIs('courier_paid_invoices.*') ? 'active' : '' }}">
                                    Courier Paid Invoices
                                </a>
                            </li>
                        @endcan

                        {{-- REPORTS --}}
                        @canany([\App\Enums\SystemPermission::REPORT_EMPLOYEE_COMMISSION->value,
                            \App\Enums\SystemPermission::REPORT_EXPENSE->value, \App\Enums\SystemPermission::REPORT_INCOME->value])
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle {{ request()->routeIs('reports.*') ? 'active' : '' }}"
                                    href="#" data-bs-toggle="dropdown">
                                    Reports
                                </a>

                                <ul class="dropdown-menu">

                                    @can(\App\Enums\SystemPermission::REPORT_EMPLOYEE_COMMISSION->value)
                                        <li>
                                            <a class="dropdown-item {{ request()->routeIs('reports.employee_commission.*') ? 'active' : '' }}"
                                                href="{{ route('reports.employee_commission.daily') }}">
                                                Employee Commission Report
                                            </a>
                                        </li>
                                    @endcan

                                    @can(\App\Enums\SystemPermission::REPORT_EXPENSE->value)
                                        <li>
                                            <a class="dropdown-item" href="{{ route('reports.expense') }}">
                                                Expense Report
                                            </a>
                                        </li>
                                    @endcan

                                    @can(\App\Enums\SystemPermission::REPORT_INCOME->value)
                                        <li>
                                            <a class="dropdown-item" href="{{ route('reports.income') }}">
                                                Income Report
                                            </a>
                                        </li>
                                    @endcan

                                </ul>
                            </li>
                        @endcanany
                    @endrole
                </ul>

                {{-- RIGHT --}}
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">
                            {{ auth()->user()->name }}
                        </a>

                        <ul class="dropdown-menu dropdown-menu-end">
                            <li>
                                <form action="{{ route('logout') }}" method="POST">
                                    @csrf
                                    <button class="dropdown-item text-danger">
                                        Logout
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </li>
                </ul>

            </div>
        </div>
    </nav>
@endauth
