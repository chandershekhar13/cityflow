
<nav class="bg-slate-900 border-b border-slate-800 shadow-lg">

    <div class="max-w-7xl mx-auto px-6">

        <div class="flex justify-between h-16 items-center">

            <!-- Logo -->

            <div class="flex items-center space-x-8">

                <a
                    href="/dashboard"
                    class="text-2xl font-bold text-cyan-400"
                >
                    CityFlow
                </a>

                <!-- Navigation Links -->

                <div class="hidden md:flex items-center space-x-6">

                    <a
                        href="/dashboard"
                        class="text-slate-300 hover:text-cyan-400 transition duration-200"
                    >
                        Dashboard
                    </a>

                    <a
                        href="/map"
                        class="text-slate-300 hover:text-cyan-400 transition duration-200"
                    >
                        City Map
                    </a>

                </div>

            </div>

            <!-- Right Side -->

            <div class="flex items-center space-x-4">

                <span class="text-slate-400 text-sm">
                    {{ Auth::user()->name }}
                </span>

                <form
                    method="POST"
                    action="{{ route('logout') }}"
                >
                    @csrf

                    <button
                        type="submit"
                        class="bg-cyan-500 hover:bg-cyan-400 text-slate-900 font-semibold px-4 py-2 rounded-xl transition duration-200"
                    >
                        Logout
                    </button>

                </form>

            </div>

        </div>

    </div>

</nav>
