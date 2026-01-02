<!--begin::Sidebar-->
<aside class="app-sidebar bg-body-secondary shadow" data-bs-theme="dark">
  <!--begin::Sidebar Brand-->
  <div class="sidebar-brand">
    <!--begin::Brand Link-->
    <a href="<?= base_url('admin/dashboard') ?>" class="brand-link">
      <!--begin::Brand Image-->
      <div class="brand-image opacity-75 shadow">
        🗳
      </div>
      <!--end::Brand Image-->
      <!--begin::Brand Text-->
      <span class="brand-text fw-light">Pilihan Kita</span>
      <!--end::Brand Text-->
    </a>
    <!--end::Brand Link-->
  </div>
  <!--end::Sidebar Brand-->
  <!--begin::Sidebar Wrapper-->
  <div class="sidebar-wrapper">
    <nav class="mt-2">
      <!--begin::Sidebar Menu-->
      <ul
        class="nav sidebar-menu flex-column"
        data-lte-toggle="treeview"
        role="navigation"
        aria-label="Main navigation"
        data-accordion="false"
        id="navigation">
        <li class="nav-item">
          <a href="<?= base_url('admin/dashboard') ?>" class="nav-link">
            <i class="nav-icon bi bi-speedometer"></i>
            <p>Dashboard</p>
          </a>
        </li>
        <li class="nav-item">
          <a href="<?= base_url('admin/kategori') ?>" class="nav-link">
            <i class="nav-icon bi bi-list-check"></i>
            <p>Kategori Pemilihan</p>
          </a>
        </li>
        <li class="nav-item">
          <a href="#" class="nav-link">
            <i class="nav-icon bi bi-gear-fill"></i>
            <p>
              Manajemen
              <i class="nav-arrow bi bi-chevron-right"></i>
            </p>
          </a>
          <ul class="nav nav-treeview">
            <li class="nav-item">
              <a href="<?= base_url('admin/calon') ?>" class="nav-link">
                <i class="nav-icon bi bi-person-badge"></i>
                <p>Calon</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="<?= base_url('admin/pemilih') ?>" class="nav-link">
                <i class="nav-icon bi bi-people-fill"></i>
                <p>Pemilih</p>
              </a>
            </li>
          </ul>
        </li>
        <hr style="margin: 0.5rem 0.6rem 0.5rem 0; border: 0; height: 1px; background: linear-gradient(to right, transparent, rgba(255,255,255,0.45), transparent);">
        <li class="nav-item">
          <a href="<?= base_url('auth/logout') ?>" class="nav-link">
            <i class="nav-icon bi bi-box-arrow-right"></i>
            <p>Sign Out</p>
          </a>
        </li>

      </ul>
      <!--end::Sidebar Menu-->
    </nav>
  </div>
  <!--end::Sidebar Wrapper-->
</aside>
<!--end::Sidebar-->