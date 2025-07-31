<?php
require_once '../core/auth.php';
require_login(); // Memastikan user sudah login

$current_user = get_logged_in_user();
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Monitoring ZI Aceh</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="../../public/assets/css/dashboard.css">
</head>

<body>
    <header>
        <div class="header-content">

            <!-- KIRI: Logo + Judul (digabung) -->
            <div class="header-left">
                <!-- Logo BPS -->
                <div class="logo-container">
                    <img src="../../public/assets/img/LOGO BPS.png" alt="" style="width: 55px;">
                </div>

                <!-- Icon + Judul -->
                <div class="title-section">
                    <div>
                        <div class="header-title">MONITORING ZI ACEH</div>
                        <div class="header-subtitle">Sistem Monitoring Zona Integritas</div>
                    </div>
                </div>
            </div>

            <!-- KANAN: Button Area -->
            <div class="header-right">
                <div class="header-buttons-container">
                    <!-- Admin Button -->
                    <a href="admin.php" class="admin-btn">
                        <i class="fas fa-user-shield"></i> Admin
                    </a>

                    <!-- User Info Dropdown -->
                    <div class="user-info" onclick="toggleDropdown()">
                        <div class="user-details">
                            <span class="user-role">
                                <?php echo $current_user['role'] === 'admin' ? 'Username akun' : 'Pengguna'; ?>
                            </span>
                        </div>
                        <i class="fas fa-chevron-down dropdown-arrow"></i>

                        <!-- Dropdown Menu -->
                        <div class="dropdown-menu" id="dropdownMenu">
                            <a href="#" class="dropdown-item" onclick="openProfile()">
                                <i class="fas fa-user"></i>
                                <span>Profil</span>
                            </a>
                            <a href="#" class="dropdown-item" onclick="openUpdatePassword()">
                                <i class="fas fa-key"></i>
                                <span>Update Password</span>
                            </a>
                            <a href="../core/logout.php" class="dropdown-item logout"
                                onclick="event.stopPropagation();">
                                <i class="fas fa-sign-out-alt"></i>
                                <span>Logout</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <div class="dropdown-overlay" id="dropdownOverlay" onclick="closeDropdown()"></div>
    <div class="container">
        <!-- Statistics Cards -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-tasks"></i>
                </div>
                <div class="stat-number" id="totalTasks">0</div>
                <div class="stat-label">Total Kegiatan</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-clock"></i>
                </div>
                <div class="stat-number" id="ongoingTasks">0</div>
                <div class="stat-label">Sedang Berjalan</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div class="stat-number" id="completedTasks">0</div>
                <div class="stat-label">Selesai</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-percentage"></i>
                </div>
                <div class="stat-number" id="avgProgress">0%</div>
                <div class="stat-label">Rata-rata Progress</div>
            </div>
        </div>

        <!-- KOMPONEN FILTER -->
        <div class="filter-container">
            <div class="filter-header">
                <i class="fas fa-filter"></i>
                <h3>Filter & Pencarian Data</h3>
            </div>

            <div class="filter-grid">
                <!-- Search Input -->
                <div class="filter-group">
                    <label>
                        <i class="fas fa-search input-icon"></i>
                        Pencarian
                    </label>
                    <input type="text" class="search-input" id="searchInput"
                        placeholder="Cari berdasarkan rencana kerja, output, PJK..." autocomplete="off">
                </div>

                <!-- Kabupaten Filter -->
                <div class="filter-group">
                    <label>
                        <i class="fas fa-map-marker-alt input-icon"></i>
                        Kabupaten
                    </label>
                    <select class="filter-select" id="kabupatenFilter">
                        <option value="">Semua Kabupaten</option>
                        <option value="aceh-besar">Aceh Besar</option>
                        <option value="aceh-barat">Aceh Barat</option>
                        <option value="aceh-timur">Aceh Timur</option>
                        <option value="aceh-selatan">Aceh Selatan</option>
                        <option value="aceh-utara">Aceh Utara</option>
                        <option value="banda-aceh">Banda Aceh</option>
                        <option value="langsa">Langsa</option>
                        <option value="lhokseumawe">Lhokseumawe</option>
                        <option value="sabang">Sabang</option>
                        <option value="subulussalam">Subulussalam</option>
                    </select>
                </div>

                <!-- Tahun Filter -->
                <div class="filter-group">
                    <label>
                        <i class="fas fa-calendar-alt input-icon"></i>
                        Tahun
                    </label>
                    <select class="filter-select" id="tahunFilter">
                        <option value="">Semua Tahun</option>
                        <option value="2024">2024</option>
                        <option value="2025">2025</option>
                        <option value="2026">2026</option>
                    </select>
                </div>

                <!-- Filter Actions -->
                <div class="filter-actions">
                    <button class="btn-filter" onclick="applyFilters()">
                        <i class="fas fa-search"></i>
                        <span>Filter</span>
                    </button>
                    <button class="btn-reset" onclick="resetFilters()">
                        <i class="fas fa-undo"></i>
                    </button>
                </div>
            </div>

            <!-- Active Filters Display -->
            <div class="active-filters" id="activeFilters">
                <div class="active-filters-label">Filter Aktif:</div>
                <div class="filter-tags" id="filterTags"></div>
            </div>
        </div>

        <div class="top-bar">
            <div class="page-title">Data Monitoring</div>
            <button class="btn" onclick="openModal()">
                <i class="fas fa-plus-circle"></i> Tambah Data
            </button>
        </div>

        <div class="table-container">
            <div class="table-header">
                <i class="fas fa-table"></i>
                Data Kegiatan Zona Integritas
            </div>

            <!-- Results Info -->
            <div class="results-info" id="resultsInfo" style="display: none;">
                <div class="results-count">
                    Menampilkan <strong id="resultCount">0</strong> dari <strong id="totalCount">0</strong> data
                </div>
                <div class="results-actions">
                    <button class="btn-export" onclick="exportFilteredData()">
                        <i class="fas fa-download"></i>
                        Export
                    </button>
                </div>
            </div>


            <div class="table-wrapper">
                <table>
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Rencana Kerja</th>
                            <th>Rencana Aksi</th>
                            <th>Output</th>
                            <th>PJK</th>
                            <th>Target Bulan</th>
                            <th>Link Bukti</th>
                            <th>Progress</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="dashboardTable">
                        <tr>
                            <td colspan="9" class="loading">
                                <div class="spinner"></div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Modal Pop-up -->
    <div class="modal" id="formModal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Input Data Monitoring</h3>
                <span class="close" onclick="closeModal()">&times;</span>
            </div>

            <div class="modal-body">
                <form id="inputForm">
                    <input type="hidden" name="action" value="add">
                    <input type="hidden" name="id" value="">
                    <input type="hidden" name="sub_deskripsi" value="">

                    <div class="form-group">
                        <label><i class="fas fa-key input-icon"></i> ID Deskripsi (Opsional)</label>
                        <input type="text" name="id_deskripsi" placeholder="Contoh: A.1.I.i.a">
                    </div>

                    <div class="form-group">
                        <label><i class="fas fa-clipboard-list input-icon"></i> Rencana Kerja</label>
                        <input type="text" name="rencanaKerja" placeholder="Masukkan rencana kerja..." required>
                    </div>

                    <div class="form-group">
                        <label><i class="fas fa-tasks input-icon"></i> Rencana Aksi</label>
                        <textarea name="rencanaAksi" placeholder="Masukkan rencana aksi..." required
                            rows="3"></textarea>
                    </div>

                    <div class="form-group">
                        <label><i class="fas fa-bullseye input-icon"></i> Output</label>
                        <input type="text" name="output" placeholder="Masukkan output yang diharapkan..." required>
                    </div>

                    <div class="form-group">
                        <label><i class="fas fa-user-tie input-icon"></i> PJK (Penanggung Jawab Kegiatan)</label>
                        <input type="text" name="pjk" placeholder="Masukkan nama PJK..." required>
                    </div>

                    <div class="form-group">
                        <label><i class="fas fa-calendar-alt input-icon"></i> Target Bulan</label>
                        <input type="month" name="target_bulan" required>
                    </div>

                    <div class="form-group">
                        <label><i class="fas fa-link input-icon"></i> Link Bukti</label>
                        <input type="url" name="bukti_link" placeholder="https://example.com/bukti">
                    </div>

                    <div class="form-group">
                        <label><i class="fas fa-chart-line input-icon"></i> Progress (%)</label>
                        <input type="number" name="progress" min="0" max="100" value="0" placeholder="0-100" required>
                    </div>
                </form>
            </div>

            <div class="form-actions">
                <button type="button" class="btn-cancel" onclick="closeModal()">
                    <i class="fas fa-times"></i> Batal
                </button>
                <button type="submit" form="inputForm" class="btn-submit">
                    <i class="fas fa-paper-plane"></i> Simpan Data
                </button>
            </div>
        </div>
    </div>
</body>
<script src="../../public/assets/js/dashboard.js"></script>

</html>