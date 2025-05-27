<style>
  [x-cloak] { display: none !important; }
</style>

<div class="bg-[#001F3F] text-white w-64 min-h-screen fixed top-0 left-0 p-5 shadow-lg z-50 flex flex-col justify-between">
  <div class="text-center mb-6">
    <img src="../asset/wihope.png" alt="Logo Sekolah" class="w-24 mx-auto">
  </div>

  <ul class="space-y-4 flex-grow">

  <li>
      <a href="?page=isi_dasboard" class="flex items-center space-x-3 px-4 py-2 rounded-lg hover:bg-[#003366] transition-all duration-300">
   
        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 12l2-2m0 0l7-7 7 7m-9 2v8m4-8h4a2 2 0 012 2v6a2 2 0 01-2 2h-4m-4 0H5a2 2 0 01-2-2v-6a2 2 0 012-2h4" />
        </svg>
        <span>Dashboard</span>
      </a>
    </li>

    <li x-data="{ open: false }">
      <button @click="open = !open" class="block w-full px-4 py-2 text-left rounded-lg hover:bg-[#003366] focus:outline-none flex justify-between items-center">
        <div class="flex items-center gap-2">
   
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6 w-5 h-5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v6m3-3H9m12 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
          </svg>
          input
        </div>
        <span x-show="!open" x-cloak>▼</span>
        <span x-show="open" x-cloak>▲</span>
      </button>
      <ul x-show="open" x-transition class="mt-2 space-y-2" x-cloak>
        <li><a href="?page=input_siswa" class="block px-4 py-2 rounded-lg hover:bg-[#003366]">data siswa</a></li>
        <li><a href="?page=input_user" class="block px-4 py-2 rounded-lg hover:bg-[#003366]">data user</a></li>
        <li><a href="?page=input_jurusan" class="block px-4 py-2 rounded-lg hover:bg-[#003366]">data jurusan</a></li>
        <li><a href="?page=input_kelas" class="block px-4 py-2 rounded-lg hover:bg-[#003366]">data kelas</a></li>
        <li><a href="?page=jadwal" class="block px-4 py-2 rounded-lg hover:bg-[#003366]">data jadwal</a></li>
        <li><a href="?page=input_mapel" class="block px-4 py-2 rounded-lg hover:bg-[#003366]">data mapel</a></li>
            </ul>
    </li>

    <li x-data="{ open: false }">
      <button @click="open = !open" class="block w-full px-4 py-2 text-left rounded-lg hover:bg-[#003366] focus:outline-none flex justify-between items-center">
        <div class="flex items-center gap-2">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
  <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 6.375c0 2.278-3.694 4.125-8.25 4.125S3.75 8.653 3.75 6.375m16.5 0c0-2.278-3.694-4.125-8.25-4.125S3.75 4.097 3.75 6.375m16.5 0v11.25c0 2.278-3.694 4.125-8.25 4.125s-8.25-1.847-8.25-4.125V6.375m16.5 0v3.75m-16.5-3.75v3.75m16.5 0v3.75C20.25 16.153 16.556 18 12 18s-8.25-1.847-8.25-4.125v-3.75m16.5 0c0 2.278-3.694 4.125-8.25 4.125s-8.25-1.847-8.25-4.125" />
</svg>

          tabel
        </div>
        <span x-show="!open" x-cloak>▼</span>
        <span x-show="open" x-cloak>▲</span>
      </button>
      <ul x-show="open" x-transition class="mt-2 space-y-2" x-cloak>
        <li><a href="?page=tampil_siswa" class="block px-4 py-2 rounded-lg hover:bg-[#003366]">tabel siswa</a></li>
        <li><a href="?page=tampil_user" class="block px-4 py-2 rounded-lg hover:bg-[#003366]">tabel user</a></li>
        <li><a href="?page=tampil_kelas" class="block px-4 py-2 rounded-lg hover:bg-[#003366]">tabel kelas</a></li>
        <li><a href="?page=tampil_jurusan" class="block px-4 py-2 rounded-lg hover:bg-[#003366]">tabel jurusan</a></li>
        <li><a href="?page=tampil_jadwal" class="block px-4 py-2 rounded-lg hover:bg-[#003366]">tabel jadwal</a></li>
      </ul>
    </li>

    <li>
      <a href="?page=input_absensi" class="block px-4 py-2 rounded-lg hover:bg-[#003366] transition-all duration-300 flex items-center gap-2">

        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" d="M4.26 10.147a60.438 60.438 0 0 0-.491 6.347A48.62 48.62 0 0 1 12 20.904a48.62 48.62 0 0 1 8.232-4.41 60.46 60.46 0 0 0-.491-6.347m-15.482 0a50.636 50.636 0 0 0-2.658-.813A59.906 59.906 0 0 1 12 3.493a59.903 59.903 0 0 1 10.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.717 50.717 0 0 1 12 13.489a50.702 50.702 0 0 1 7.74-3.342M6.75 15a.75.75 0 1 0 0-1.5.75.75 0 0 0 0 1.5Zm0 0v-3.675A55.378 55.378 0 0 1 12 8.443m-7.007 11.55A5.981 5.981 0 0 0 6.75 15.75v-1.5" />
        </svg>
        Absensi
      </a>
    </li>

    <li>
      <a href="?page=rekap" class="block px-4 py-2 rounded-lg hover:bg-[#003366] transition-all duration-300 flex items-center gap-2">

        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" d="M4.26 10.147a60.438 60.438 0 0 0-.491 6.347A48.62 48.62 0 0 1 12 20.904a48.62 48.62 0 0 1 8.232-4.41 60.46 60.46 0 0 0-.491-6.347m-15.482 0a50.636 50.636 0 0 0-2.658-.813A59.906 59.906 0 0 1 12 3.493a59.903 59.903 0 0 1 10.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.717 50.717 0 0 1 12 13.489a50.702 50.702 0 0 1 7.74-3.342M6.75 15a.75.75 0 1 0 0-1.5.75.75 0 0 0 0 1.5Zm0 0v-3.675A55.378 55.378 0 0 1 12 8.443m-7.007 11.55A5.981 5.981 0 0 0 6.75 15.75v-1.5" />
        </svg>
        laporan
      </a>
    </li>

    <li>
      <a href="?page=scan" class="block px-4 py-2 rounded-lg hover:bg-[#003366] transition-all duration-300 flex items-center gap-2">
      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
  <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 2.25h3m-3.75 3h15a2.25 2.25 0 0 0 2.25-2.25V6.75A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25v10.5A2.25 2.25 0 0 0 4.5 19.5Z" />
</svg>

        scan kartu
      </a>
    </li>



  </ul>
</div>
