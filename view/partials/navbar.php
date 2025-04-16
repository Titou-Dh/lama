<nav class="box-shadow-lg" id="mainNav">
    <div class="max-w-screen-xl flex flex-wrap py-2 items-center justify-between mx-auto">
        <a href="/" class="flex items-center space-x-3 rtl:space-x-reverse">
            <img src="../assets/images/logo.png" class="w-24" alt="Lama Logo" />
        </a>
        <div class="flex md:order-2 space-x-3 md:space-x-0 rtl:space-x-reverse mr-2">
            <div class="nav-item">
                <?php
                if (isset($_SESSION['user_id'])) {
                    echo '
            <div class="flex items-center gap-2"><a class="nav-link btn btn-gradient rounded-full px-3 py-2 flex items-center gap-2 hover:text-white" href="./dashboard">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-layout-dashboard-icon lucide-layout-dashboard"><rect width="7" height="9" x="3" y="3" rx="1"/><rect width="7" height="5" x="14" y="3" rx="1"/><rect width="7" height="9" x="14" y="12" rx="1"/><rect width="7" height="5" x="3" y="16" rx="1"/></svg>      
                    Dashboard
                  </a>
                  <button onclick="window.location.href=\'../../config/session.php?action=logout\'" class="btn btn-outline-secondary  rounded-full">Logout</button></div>
                  ';
                } else {
                    echo '<a class="nav-link btn btn-gradient rounded-full px-3 py-2 hover:text-white" href="./auth/sign-in.php">
                    Sign in
                  </a>';
                }
                ?>
            </div>
            <button data-collapse-toggle="navbar-cta" type="button"
                class="inline-flex items-center p-2 w-10 h-10 justify-center text-sm text-gray-500 border-2 border-blue-500 rounded-lg md:hidden focus:outline-none hover:border-blue-600"
                aria-controls="navbar-cta" aria-expanded="false">
                <span class="sr-only">Open main menu</span>
                <svg class="w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 17 14">
                    <path stroke="#2563eb" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M1 1h15M1 7h15M1 13h15" />
                </svg>
            </button>
        </div>
        <div class="items-center justify-between hidden w-full md:flex md:w-auto md:order-1 transition-all duration-300"
            id="navbar-cta">
            <ul
                class="flex flex-col font-medium px-2 py-3 md:p-0 md:space-x-8 rtl:space-x-reverse md:flex-row md:mt-0 md:border-0">
                <li>
                    <a href="#"
                        class="block py-2 px-3 md:p-0 btn-gradient md:bg-transparent text-white md:text-blue-700 rounded-full"
                        aria-current="page">Home</a>
                </li>
                <li>
                    <a href="#"
                        class="block py-2 px-3 md:p-0 text-white rounded-sm md:hover:bg-transparent md:hover:text-blue-700">About</a>
                </li>
                <li>
                    <a href="#"
                        class="block py-2 px-3 md:p-0 text-white rounded-sm md:hover:bg-transparent md:hover:text-blue-700">Services</a>
                </li>
                <li>
                    <a href="#"
                        class="block py-2 px-3 md:p-0 text-white rounded-sm md:hover:bg-transparent md:hover:text-blue-700">Contact</a>
                </li>
            </ul>
        </div>
    </div>
</nav>