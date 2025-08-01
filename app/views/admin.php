<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - Monitoring ZI Aceh</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="../../public/assets/css/admin.css">
</head>

<body>
    <!-- Header -->
    <header>
        <div class="header-content">
            <div class="header-left">
                <div class="logo-container">
                    <i class="fas fa-shield-alt"></i>
                </div>
                <div>
                    <div class="header-title">RUANG ADMIN</div>
                    <div class="header-subtitle">Sistem Management Pengguna</div>
                </div>
            </div>

            <div class="user-info">
                <div class="user-avatar">A</div>
                <div class="user-details">
                    <div class="user-name">Administrator</div>
                    <div class="user-role">Super Administrator</div>
                </div>
                <a href="#" class="logout-btn" onclick="handleLogout()">
                    <i class="fas fa-sign-out-alt"></i>
                </a>
            </div>
        </div>
    </header>

    <div class="container">
        <!-- Statistics Grid -->
        <div class="stats-grid fade-in">
            <div class="stat-card">
                <div class="stat-content">
                    <div class="stat-icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="stat-info">
                        <div class="stat-number" id="totalUsers">5</div>
                        <div class="stat-label">Total Pengguna</div>
                    </div>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-content">
                    <div class="stat-icon">
                        <i class="fas fa-crown"></i>
                    </div>
                    <div class="stat-info">
                        <div class="stat-number" id="adminCount">2</div>
                        <div class="stat-label">Administrator</div>
                    </div>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-content">
                    <div class="stat-icon">
                        <i class="fas fa-user"></i>
                    </div>
                    <div class="stat-info">
                        <div class="stat-number" id="userCount">3</div>
                        <div class="stat-label">User Biasa</div>
                    </div>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-content">
                    <div class="stat-icon">
                        <i class="fas fa-calendar-day"></i>
                    </div>
                    <div class="stat-info">
                        <div class="stat-number" id="todayUsers">1</div>
                        <div class="stat-label">User Hari Ini</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Page Header -->
        <div class="page-header fade-in">
            <div class="page-title">
                <i class="fas fa-users-cog"></i>
                Manajemen Pengguna
            </div>
            <div class="page-actions">
                <button class="btn btn-secondary" onclick="goToDashboard()">
                    <i class="fas fa-arrow-left"></i>
                    Kembali ke Dashboard
                </button>
                <button class="btn" onclick="openModal('add')">
                    <i class="fas fa-plus"></i>
                    Tambah Pengguna
                </button>
            </div>
        </div>

        <!-- Filters Container -->
        <div class="filters-container fade-in">
            <div class="filters-content">
                <div class="filter-group" style="flex: 2;">
                    <label class="filter-label">
                        <i class="fas fa-search"></i> Cari Pengguna
                    </label>
                    <input type="text" id="searchInput" class="filter-input"
                        placeholder="Cari berdasarkan username, nama, atau email..." oninput="searchUsers()">
                </div>

                <div class="filter-group">
                    <label class="filter-label">
                        <i class="fas fa-filter"></i> Filter Role
                    </label>
                    <select id="roleFilter" class="filter-input" onchange="filterByRole(this.value)">
                        <option value="all">Semua Role</option>
                        <option value="1">Administrator</option>
                        <option value="2">User</option>
                    </select>
                </div>

                <div>
                    <button class="btn btn-secondary" onclick="exportToCSV()">
                        <i class="fas fa-download"></i>
                        Export CSV
                    </button>
                </div>
            </div>
        </div>

        <!-- Users Table -->
        <div class="table-container fade-in">
            <div class="table-header">
                <i class="fas fa-table"></i>
                <h3>Daftar Pengguna Sistem</h3>
            </div>

            <div class="table-wrapper">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Username</th>
                            <th>Nama Lengkap</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Dibuat</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="userTableBody"></tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Modal Pop-up -->
    <div id="userModal" class="modal">
        <div class="modal-content">
            <!-- Modal Header -->
            <div class="modal-header">
                <div class="modal-header-content">
                    <div class="modal-icon">
                        <i class="fas fa-user-plus" id="modalIconElement"></i>
                    </div>
                    <h3 id="modalTitle">Tambah Pengguna Baru</h3>
                </div>
                <span class="close" onclick="closeModal()">
                    <i class="fas fa-times"></i>
                </span>
            </div>

            <!-- Form -->
            <form id="userForm" onsubmit="handleSubmit(event)">
                <!-- Modal Body -->
                <div class="modal-body">
                    <input type="hidden" id="userId" name="userId">

                    <div class="form-group">
                        <label class="form-label">
                            <i class="fas fa-user"></i>Username
                        </label>
                        <input type="text" id="username" name="username" class="form-input" required
                            placeholder="Masukkan username unik">
                        <div class="form-focus-line"></div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">
                            <i class="fas fa-id-card"></i>Nama Lengkap
                        </label>
                        <input type="text" id="namaLengkap" name="namaLengkap" class="form-input" required
                            placeholder="Masukkan nama lengkap pengguna">
                        <div class="form-focus-line"></div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">
                            <i class="fas fa-envelope"></i>Email Address
                        </label>
                        <input type="email" id="email" name="email" class="form-input" required
                            placeholder="user@example.com">
                        <div class="form-focus-line"></div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">
                            <i class="fas fa-lock"></i>Password
                            <span id="passwordHint" class="password-hint" style="display: none;">(kosongkan jika tidak
                                ingin mengubah)</span>
                        </label>
                        <input type="password" id="password" name="password" class="form-input"
                            placeholder="Minimal 8 karakter">
                        <div class="form-focus-line"></div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">
                            <i class="fas fa-user-tag"></i>Role Pengguna
                        </label>
                        <select id="role" name="role" class="form-select" required>
                            <option value="">-- Pilih Role Pengguna --</option>
                            <option value="1">👑 Administrator</option>
                            <option value="2">👤 User Biasa</option>
                        </select>
                        <div class="form-focus-line"></div>
                    </div>
                </div>

                <!-- Modal Footer -->
                <div class="form-actions">
                    <button type="button" class="btn-cancel" onclick="closeModal()">
                        <i class="fas fa-times"></i>
                        Batal
                    </button>
                    <button type="submit" class="btn-submit" id="submitBtn">
                        <i class="fas fa-save"></i>
                        Simpan Data
                    </button>
                </div>
            </form>
        </div>
    </div>

</body>
<script src="../../public/assets/js/admin.js"></script>

</html>