// Dashboard JavaScript dengan integrasi PHP
document.addEventListener('DOMContentLoaded', function () {
    loadDashboardData();
    updateStatistics();

    // Setup form submission
    document.getElementById('inputForm').addEventListener('submit', handleFormSubmit);
});

// Load data dari server dengan debug
function loadDashboardData() {
    console.log('🔄 Loading dashboard data...'); // Debug log

    fetch('/Web-Jazirah-main/app/controllers/get_monitoring_data.php')
        .then(response => {
            console.log('📡 Response status:', response.status); // Debug log
            console.log('📡 Response headers:', response.headers); // Debug log

            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            return response.text(); // Ubah ke text dulu untuk debug
        })
        .then(text => {
            console.log('📄 Raw response:', text); // Debug: lihat response mentah

            try {
                const data = JSON.parse(text);
                console.log('✅ Parsed JSON:', data); // Debug: lihat JSON yang sudah diparsing

                if (data.success) {
                    displayTableData(data.data);
                    updateStatistics();
                } else {
                    showError('Gagal memuat data: ' + data.message);
                }
            } catch (parseError) {
                console.error('❌ JSON Parse Error:', parseError);
                console.error('❌ Raw text that failed to parse:', text);
                showError('Response bukan JSON valid: ' + parseError.message);
            }
        })
        .catch(error => {
            console.error('❌ Fetch Error:', error);
            showError('Terjadi kesalahan saat memuat data: ' + error.message);
        });
}

// TAMBAHKAN JAVASCRIPT INI ke dalam dashboard.js atau di dalam <script> tag

// Dropdown functionality
function toggleDropdown() {
    const userInfo = document.querySelector('.user-info');
    const dropdownMenu = document.getElementById('dropdownMenu');
    const overlay = document.getElementById('dropdownOverlay');

    userInfo.classList.toggle('active');
    dropdownMenu.classList.toggle('show');
    overlay.classList.toggle('show');
}

function closeDropdown() {
    const userInfo = document.querySelector('.user-info');
    const dropdownMenu = document.getElementById('dropdownMenu');
    const overlay = document.getElementById('dropdownOverlay');

    userInfo.classList.remove('active');
    dropdownMenu.classList.remove('show');
    overlay.classList.remove('show');
}

// Menu item functions
function openProfile() {
    closeDropdown();
    // Ganti dengan URL atau logika sesuai kebutuhan
    window.location.href = 'profile.php';
}

function openUpdatePassword() {
    closeDropdown();
    // Ganti dengan URL atau logika sesuai kebutuhan
    window.location.href = 'update_password.php';
}

// Close dropdown when clicking outside
document.addEventListener('click', function (event) {
    const userInfo = document.querySelector('.user-info');
    const dropdownMenu = document.getElementById('dropdownMenu');

    if (!userInfo.contains(event.target)) {
        closeDropdown();
    }
});

// Prevent dropdown from closing when clicking inside the dropdown menu
document.addEventListener('DOMContentLoaded', function () {
    const dropdownMenu = document.getElementById('dropdownMenu');
    if (dropdownMenu) {
        dropdownMenu.addEventListener('click', function (event) {
            event.stopPropagation();
        });
    }
});

// Display data di tabel
function displayTableData(data) {
    const tbody = document.getElementById('dashboardTable');

    if (data.length === 0) {
        tbody.innerHTML = `
            <tr>
                <td colspan="9" class="empty-state">
                    <i class="fas fa-inbox"></i>
                    <h3>Belum ada data</h3>
                    <p>Klik "Tambah Data" untuk menambah data monitoring baru</p>
                </td>
            </tr>
        `;
        return;
    }

    tbody.innerHTML = data.map((item, index) => `
        <tr>
            <td>${item.id_deskripsi}.${item.sub_deskripsi}</td>
            <td>${item.rencana_kerja}</td>
            <td>${item.rencana_aksi}</td>
            <td>${item.output}</td>
            <td>${item.pjk}</td>
            <td>${formatDate(item.target_bulan)}</td>
            <td>
                ${item.link ? // Ubah dari 'bukti_link' ke 'link'
            `<a href="${item.link}" target="_blank" class="link-btn">
                        <i class="fas fa-external-link-alt"></i> Lihat
                    </a>` :
            '<span class="text-muted">-</span>'
        }
            </td>
            <td>
                <div class="progress-container">
                    <div class="progress-bar" style="width: ${item.progress}%">
                        <div class="progress-text">${item.progress}%</div>
                    </div>
                </div>
            </td>
            <td>
                <button class="btn-aksi" onclick="editData(${item.id_pendukung})" title="Edit">
                    <i class="fas fa-edit"></i>
                </button>
                <button class="btn-aksi" onclick="deleteData(${item.id_pendukung})" title="Hapus">
                    <i class="fas fa-trash"></i>
                </button>
            </td>
        </tr>
    `).join('');
}

// Update statistik dengan debug
function updateStatistics() {
    console.log('📊 Loading statistics...');

    fetch('/Web-Jazirah-main/app/controllers/get_statistics.php')
        .then(response => {
            console.log('📊 Statistics response status:', response.status);

            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            return response.text();
        })
        .then(text => {
            console.log('📊 Statistics raw response:', text);

            try {
                const data = JSON.parse(text);
                console.log('📊 Statistics parsed:', data);

                if (data.success) {
                    document.getElementById('totalTasks').textContent = data.stats.total;
                    document.getElementById('ongoingTasks').textContent = data.stats.ongoing;
                    document.getElementById('completedTasks').textContent = data.stats.completed;
                    document.getElementById('avgProgress').textContent = data.stats.avgProgress + '%';
                } else {
                    console.error('❌ Statistics error:', data.message);
                }
            } catch (parseError) {
                console.error('❌ Statistics JSON Parse Error:', parseError);
                console.error('❌ Statistics raw text:', text);
            }
        })
        .catch(error => {
            console.error('❌ Statistics Error:', error);
        });
}
// Handle form submission
function handleFormSubmit(e) {
    e.preventDefault();

    const formData = new FormData(e.target);
    const action = formData.get('action');

    const submitBtn = document.querySelector('.btn-submit');
    const originalText = submitBtn.innerHTML;

    // Show loading
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Menyimpan...';
    submitBtn.disabled = true;

    const endpoint = action === 'edit'
        ? '../../app/controllers/update_monitoring_data.php'
        : '../../app/controllers/add_monitoring_data.php';

    const idInput = formData.get('id_deskripsi');

    if (idInput && idInput.includes('.')) {
        const parts = idInput.trim().split('.');
        if (parts.length >= 5) {
            const sub = parts.pop(); // Ambil bagian terakhir sebagai sub_deskripsi
            const id_deskripsi = parts.join('.');

            formData.set('id_deskripsi', id_deskripsi);
            formData.set('sub_deskripsi', sub);
        } else {
            showError("Format ID deskripsi tidak valid. Contoh: A.1.I.i.a");
            return; // Stop submit
        }
    }

    fetch(endpoint, {
        method: 'POST',
        body: formData
    })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showSuccess(data.message);
                closeModal();
                loadDashboardData();
                document.getElementById('inputForm').reset();
            } else {
                showError(data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showError('Terjadi kesalahan saat menyimpan data');
        })
        .finally(() => {
            submitBtn.innerHTML = originalText;
            submitBtn.disabled = false;
        });
}

// Edit data
function editData(id) {
    fetch(`../../app/controllers/get_monitoring_data.php?id=${id}`)
        .then(response => response.json())
        .then(data => {
            if (data.success && data.data.length > 0) {
                const item = data.data[0];
                fillFormForEdit(item);
                openModal();
            } else {
                showError('Data tidak ditemukan');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showError('Terjadi kesalahan saat mengambil data');
        });
}

// Fill form for editing
function fillFormForEdit(item) {
    const form = document.getElementById('inputForm');
    form.querySelector('input[name="action"]').value = 'edit';
    form.querySelector('input[name="id"]').value = item.id;
    form.querySelector('input[name="rencanaKerja"]').value = item.rencana_kerja;
    form.querySelector('textarea[name="rencanaAksi"]').value = item.rencana_aksi;
    form.querySelector('input[name="output"]').value = item.output;
    form.querySelector('input[name="pjk"]').value = item.pjk;
    form.querySelector('input[name="target_bulan"]').value = item.target_bulan;
    form.querySelector('input[name="bukti_link"]').value = item.bukti_link || '';
    form.querySelector('input[name="progress"]').value = item.progress;

    // Update modal title
    document.querySelector('.modal-header h3').textContent = 'Edit Data Monitoring';
    document.querySelector('.btn-submit').innerHTML = '<i class="fas fa-save"></i> Update Data';
}

// Delete data
function deleteData(id) {
    if (confirm('Apakah Anda yakin ingin menghapus data ini?')) {
        fetch('../../../app/controllers/delete_monitoring_data.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `id=${id}`
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showSuccess(data.message);
                    loadDashboardData();
                } else {
                    showError(data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showError('Terjadi kesalahan saat menghapus data');
            });
    }
}

// Modal functions
function openModal() {
    const modal = document.getElementById('formModal');
    modal.style.display = 'block';
    document.body.style.overflow = 'hidden';

    // Tambahan baru - Scroll ke atas saat modal di buka
    setTimeout(() => {
        const modalBody = document.getElementById('.modal-body');
        if (modalBody) {
            modalBody.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        }
    }, 100);

    // Tambah smooth animation saat modal muncul
    setTimeout(() => {
        modal.style.opacity = 1;
    }, 10);
}

function closeModal() {
    document.getElementById('formModal').style.display = 'none';
    document.body.style.overflow = 'auto';

    // Reset form
    document.getElementById('inputForm').reset();
    document.querySelector('input[name="action"]').value = 'add';
    document.querySelector('input[name="id"]').value = '';

    // Reset modal title
    document.querySelector('.modal-header h3').textContent = 'Input Data Monitoring';
    document.querySelector('.btn-submit').innerHTML = '<i class="fas fa-paper-plane"></i> Simpan Data';
}

// Close modal when clicking outside
window.onclick = function (event) {
    const modal = document.getElementById('formModal');
    if (event.target === modal) {
        closeModal();
    }
}

// Utility functions
function formatDate(dateString) {
    const options = { year: 'numeric', month: 'long' };
    const date = new Date(dateString + '-01');
    return date.toLocaleDateString('id-ID', options);
}

function showSuccess(message) {
    // Simple success notification
    const notification = document.createElement('div');
    notification.className = 'notification success';
    notification.innerHTML = `
        <i class="fas fa-check-circle"></i>
        <span>${message}</span>
    `;

    document.body.appendChild(notification);

    setTimeout(() => {
        notification.remove();
    }, 3000);
}

function showError(message) {
    // Simple error notification
    const notification = document.createElement('div');
    notification.className = 'notification error';
    notification.innerHTML = `
        <i class="fas fa-exclamation-circle"></i>
        <span>${message}</span>
    `;

    document.body.appendChild(notification);

    setTimeout(() => {
        notification.remove();
    }, 3000);
}

// Add notification styles
const style = document.createElement('style');
style.textContent = `
    .notification {
        position: fixed;
        top: 20px;
        right: 20px;
        padding: 15px 20px;
        border-radius: 8px;
        color: white;
        font-weight: 600;
        z-index: 10000;
        display: flex;
        align-items: center;
        gap: 10px;
        animation: slideInRight 0.3s ease;
    }
    
    .notification.success {
        background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
    }
    
    .notification.error {
        background: linear-gradient(135deg, #dc3545 0%, #fd7e14 100%);
    }
    
    @keyframes slideInRight {
        from {
            transform: translateX(100%);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }
`;
document.head.appendChild(style);

document.addEventListener('keydown', function (e) {
    const modal = document.getElementById('formModal');
    if (modal.style.display === 'block') {
        // tekan esc untuk tutup modal
        if (e.key === 'Escape') {
            closeModal();
        }
    }
});

// JAVASCRIPT FUNCTIONS 

let currentFilters = {
    search: '',
    kabupaten: '',
    tahun: ''
};

let allData = []; // Menyimpan semua data untuk filtering
let filteredData = []; // Data yang telah difilter

// Fungsi untuk apply filters
function applyFilters() {
    const container = document.querySelector('.filter-container');
    container.classList.add('filter-loading');

    // Ambil nilai filter
    currentFilters.search = document.getElementById('searchInput').value.toLowerCase().trim();
    currentFilters.kabupaten = document.getElementById('kabupatenFilter').value;
    currentFilters.tahun = document.getElementById('tahunFilter').value;

    // Simulasi delay (hapus ini jika menggunakan data real)
    setTimeout(() => {
        // Filter data
        filteredData = allData.filter(item => {
            // Search filter
            if (currentFilters.search && !matchesSearch(item, currentFilters.search)) {
                return false;
            }

            // Kabupaten filter (sesuaikan dengan field data Anda)
            if (currentFilters.kabupaten && item.kabupaten !== currentFilters.kabupaten) {
                return false;
            }

            // Tahun filter (dari target_bulan)
            if (currentFilters.tahun) {
                const itemYear = new Date(item.target_bulan).getFullYear().toString();
                if (itemYear !== currentFilters.tahun) {
                    return false;
                }
            }

            return true;
        });

        // Update tampilan
        displayTableData(filteredData);
        updateActiveFilters();
        updateResultsInfo();

        container.classList.remove('filter-loading');
    }, 500);
}

// Fungsi untuk reset filters
function resetFilters() {
    document.getElementById('searchInput').value = '';
    document.getElementById('kabupatenFilter').value = '';
    document.getElementById('tahunFilter').value = '';

    currentFilters = {
        search: '',
        kabupaten: '',
        tahun: ''
    };

    filteredData = [...allData];
    displayTableData(filteredData);
    updateActiveFilters();
    updateResultsInfo();
}

// Fungsi untuk cek apakah item cocok dengan pencarian
function matchesSearch(item, searchTerm) {
    const searchFields = [
        item.rencana_kerja,
        item.rencana_aksi,
        item.output,
        item.pjk
    ];

    return searchFields.some(field =>
        field && field.toLowerCase().includes(searchTerm)
    );
}

// Update active filters display
function updateActiveFilters() {
    const activeFiltersDiv = document.getElementById('activeFilters');
    const filterTagsDiv = document.getElementById('filterTags');

    const activeTags = [];

    if (currentFilters.search) {
        activeTags.push({
            label: `Pencarian: "${currentFilters.search}"`,
            type: 'search'
        });
    }

    if (currentFilters.kabupaten) {
        const select = document.getElementById('kabupatenFilter');
        const selectedText = select.options[select.selectedIndex].text;
        activeTags.push({
            label: `Kabupaten: ${selectedText}`,
            type: 'kabupaten'
        });
    }

    if (currentFilters.tahun) {
        activeTags.push({
            label: `Tahun: ${currentFilters.tahun}`,
            type: 'tahun'
        });
    }

    if (activeTags.length > 0) {
        filterTagsDiv.innerHTML = activeTags.map(tag => `
                    <div class="filter-tag">
                        ${tag.label}
                        <span class="remove-tag" onclick="removeFilter('${tag.type}')">×</span>
                    </div>
                `).join('');
        activeFiltersDiv.classList.add('show');
    } else {
        activeFiltersDiv.classList.remove('show');
    }
}

// Remove specific filter
function removeFilter(type) {
    switch (type) {
        case 'search':
            document.getElementById('searchInput').value = '';
            currentFilters.search = '';
            break;
        case 'kabupaten':
            document.getElementById('kabupatenFilter').value = '';
            currentFilters.kabupaten = '';
            break;
        case 'tahun':
            document.getElementById('tahunFilter').value = '';
            currentFilters.tahun = '';
            break;
    }
    applyFilters();
}

// Update results info
function updateResultsInfo() {
    const resultsInfo = document.getElementById('resultsInfo');
    const resultCount = document.getElementById('resultCount');
    const totalCount = document.getElementById('totalCount');

    if (filteredData.length !== allData.length || allData.length > 0) {
        resultCount.textContent = filteredData.length;
        totalCount.textContent = allData.length;
        resultsInfo.style.display = 'flex';
    } else {
        resultsInfo.style.display = 'none';
    }
}

// Export filtered data
function exportFilteredData() {
    // Implementasi export (CSV, Excel, dll)
    console.log('Exporting filtered data:', filteredData);
    showSuccess('Data berhasil diekspor!');
}

// Real-time search
document.getElementById('searchInput').addEventListener('input', function () {
    // Debounce untuk performa
    clearTimeout(this.searchTimeout);
    this.searchTimeout = setTimeout(() => {
        applyFilters();
    }, 300);
});

// MODIFIKASI FUNGSI YANG SUDAH ADA
// Update loadDashboardData untuk menyimpan data ke allData
const originalLoadDashboardData = loadDashboardData;
loadDashboardData = function () {
    console.log('🔄 Loading dashboard data with filter support...');

    fetch('/Web-Jazirah-main/app/controllers/get_monitoring_data.php')
        .then(response => {
            console.log('📡 Response status:', response.status);
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.text();
        })
        .then(text => {
            console.log('📄 Raw response:', text);
            try {
                const data = JSON.parse(text);
                console.log('✅ Parsed JSON:', data);

                if (data.success) {
                    // Simpan ke allData untuk filtering
                    allData = data.data;
                    filteredData = [...allData];

                    displayTableData(filteredData);
                    updateStatistics();
                    updateResultsInfo();
                } else {
                    showError('Gagal memuat data: ' + data.message);
                }
            } catch (parseError) {
                console.error('❌ JSON Parse Error:', parseError);
                showError('Response bukan JSON valid: ' + parseError.message);
            }
        })
        .catch(error => {
            console.error('❌ Fetch Error:', error);
            showError('Terjadi kesalahan saat memuat data: ' + error.message);
        });
};







