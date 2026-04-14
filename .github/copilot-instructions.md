# Copilot Instructions for PilihanKita

## Project Overview
**PilihanKita** is a CodeIgniter 4-based voting/election platform built for managing candidates and voter selections across categories. It features role-based access control (admin/user), candidate management, and voter tracking.

## Architecture & Key Components

### Core Structure (CodeIgniter 4)
- **Framework**: CodeIgniter 4 (PHP 8.1+)
- **Database**: MySQL (`pilihan_kita` database defined in `app/Config/Database.php`)
- **Database Driver**: MySQLi
- **Public folder**: `public/` (web root - always point server here, NOT project root)

### Role-Based Modules
1. **Admin** (`app/Controllers/Admin/`)
   - Dashboard: `Admin\Dashboard` - overview and statistics
   - Kategori: `Admin\Kategori` - manage election categories (store, toggle status, delete)
   - Calon: `Admin\Calon` - manage candidates (CRUD operations, photo uploads to `writable/uploads/calon/`)
   - Pemilih: `Admin\Pemilih` - manage voters (create, store, delete via POST endpoints)

2. **User** (voter-facing)
   - Access: `Auth::pemilihan` - voting interface with candidate selection
   - Tracks: `sudah_memilih` boolean in `users` table
   - Votes stored in `suara` table (user_id, calon_id relationship)

3. **Auth** (`app/Controllers/Auth`)
   - Login: Username/password with `password_verify()` (password hashing required)
   - Session-based: Sets session vars (id, username, nama, role, logged_in)
   - Roles: 'admin' or 'user'
   - Logout: `Auth::logout`
   - Password Reset: `forgotPassword()` / `processForgotPassword()`

### Authentication & Authorization
- **Filters** (route protection):
  - `F_admin` (AdminFilter): Requires `logged_in=true` AND `role='admin'`
  - `F_user` (UserFilter): Requires `logged_in=true` AND `role='user'`
  - `F_general` (GeneralFilter): For public/semi-public routes
- **Route Grouping**: Admin routes in `$routes->group('admin', ['filter' => 'F_admin'])`
- **Session-based**: No JWT/token auth; relies on CodeIgniter sessions

### Data Models
Located in `app/Models/`:
- **UserModel**: `users` table (admin_id, username, password, nama, identifier, role, sudah_memilih, created_at) - uses timestamps
- **CalonModel**: `calon` table (nama_calon, wakil_calon, visi, misi, foto, kategori_id, admin_id)
- **KategoriModel**: Categories for elections
- **PemilihModel**: Voter tracking
- **SuaraModel**: `suara` table (user_id, calon_id) - tracks voter choices/votes

All models extend `CodeIgniter\Model` with `$useTimestamps = false` (no timestamps) except UserModel which uses `created_at`.

### Views & Frontend
- **Layout**: Uses AdminLTE CSS framework (`public/assets/adminlte/`)
- **Bootstrap**: Bootstrap Grid system (`public/css/bootstrap-grid.css`)
- **Location**: `app/Views/` with subdirectories (admin/, auth/, user/, layout/, errors/)
- **Template syntax**: PHP with `<?= ?>` for output

## Critical Development Workflows

### Setup & Run
```bash
# Install dependencies
composer install

# Configure database in app/Config/Database.php or .env
# Database: pilihan_kita, User: root (no password default)

# Run locally
php spark serve  # Default: http://localhost:8080

# Clear cache
php spark cache:clear
```

### Testing
```bash
# Requires PHPUnit configured in phpunit.xml.dist
composer install  # Installs phpunit/phpunit ^10.5.16 || ^11.2

# Run tests (from root)
./vendor/bin/phpunit  # or: php -d xdebug.mode=coverage vendor/bin/phpunit

# Test database config in app/Config/Database.php under 'tests' group
# Views excluded from coverage (app/Views/, Routes.php)
```

### File Upload Pattern
- Calon photos: POST to `Admin\Calon::save()` → stored in `writable/uploads/calon/`
- Retrieval: `UploadController::showCalon($filename)` → serves file with correct MIME type
- Path construction: `WRITEPATH . 'uploads/calon/' . $filename`

### Common Patterns

**Model Queries**:
```php
$userModel = new \App\Models\UserModel();
$user = $userModel->where('username', $username)->first();  // Returns array or null
$userModel->insert($data);  // Insert with $allowedFields validation
```

**Session Usage**:
```php
$session = session();
$session->set(['id' => 1, 'logged_in' => true]);  // Set multiple
if (!$session->get('logged_in')) { /* redirect */ }
```

**Redirects with Messages**:
```php
return redirect()->to('path')->with('error', 'Message');  // Flash message
return redirect()->back();  // Preserve form data on validation errors
```

**View Rendering**:
```php
return view('path/file', $data);  // Data passed as $variables in view
```

## Project-Specific Conventions

1. **Naming**: Controllers use namespace `App\Controllers\Admin\ClassName` for admin sections
2. **Password Handling**: Always use `password_hash()` and `password_verify()` (no plain text)
3. **Filter Aliases**: Prefix custom filters with `F_` (AdminFilter → `F_admin`)
4. **Database Timestamps**: UserModel uses `created_at` only (no `updated_at`)
5. **File Paths**: Use framework constants (`WRITEPATH`, `APPPATH`, `PUBPATH`)
6. **Identifier Field**: Users table has `identifier` field (purpose unclear - preserve in migrations)

## Integration Points

- **Email**: `App\Config\Email.php` configured but not implemented in visible code
- **Cache**: Framework-standard caching in `app/Cache/` and `system/Cache/`
- **Validation**: Framework's validation engine (referenced in routes, filters)
- **CLI Commands**: Spark commands available (migrations, cache clear, serve)

## Common Pitfalls to Avoid

- Don't store files in `public/` directly - use `writable/uploads/`
- Session checks: Always verify both `logged_in` AND `role` in filters
- Controller inheritance: All controllers should extend `BaseController`, not `Controller`
- Routes: Admin routes MUST have `['filter' => 'F_admin']` group wrapper
- **Timestamps**: Only UserModel uses timestamps (created_at). Other models don't have auto timestamps.
- **Suara Model**: Stores vote relationships with user_id and calon_id - ensure data integrity when voting
