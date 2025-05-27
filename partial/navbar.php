<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Navbar dengan Dropdown</title>
  <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
  <script>
    // Opsional: hilangkan flicker saat load
    document.addEventListener('alpine:init', () => {
      Alpine.store('dropdown', {
        open: false
      });
    });
  </script>
</head>
<body class="bg-gray-100">

  <nav class="bg-[#001F3F] text-white px-6 py-3 h-14 flex items-center fixed top-4 left-1/5 right-4 shadow-lg rounded-xl w-[81%] mx-auto" x-data="{ open: false }">
    
 
    <a href="../asset/Buku Panduan.pdf" target="_blank" class="flex items-center space-x-2 text-white hover:underline mr-6">
      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 0 0 6 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 0 1 6 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 0 1 6-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0 0 18 18a8.967 8.967 0 0 0-6 2.292m0-14.25v14.25" />
      </svg>
      <span>Buku Panduan</span>
    </a>


<a href="../asset/LAPORAN ABSENSI.docx" download class="flex items-center space-x-2 text-white hover:underline ml-6">
  <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
    <path stroke-linecap="round" stroke-linejoin="round" d="M9 17v-6h6v6m2 4H7a2 2 0 01-2-2V5a2 2 0 012-2h5.5l5.5 5.5V19a2 2 0 01-2 2z" />
  </svg>
  <span>Laporan</span>
</a>


    <!-- Profile -->
    <div class="ml-auto relative" @click.outside="open = false">
      <div class="flex items-center space-x-2 cursor-pointer" @click="open = !open">
        <span><?php echo htmlspecialchars($_SESSION['username']); ?></span>
        <img src="../asset/profile.png" alt="Profile" class="w-8 h-8 rounded-full">
        
        <!-- Tanda panah -->
        <span x-show="!open" x-cloak>▼</span>
        <span x-show="open" x-cloak>▲</span>
      </div>

      <!-- Dropdown -->
      <div x-show="open" x-transition x-cloak class="absolute right-0 mt-2 w-40 bg-white rounded-md shadow-lg z-50">
        <a href="../logout.php" class="block px-4 py-2 text-gray-700 hover:bg-gray-200">Logout</a>
      </div>
    </div>
  </nav>

</body>
</html>
