// Sample user data
let users = [
    { id: 1, username: 'admin', namaLengkap: 'Administrator Sistem', email: 'admin@bps.go.id', role: 1, createdAt: '2024-01-15' },
    { id: 2, username: 'user1', namaLengkap: 'Petugas Monitoring', email: 'user1@bps.go.id', role: 2, createdAt: '2024-01-16' },
    { id: 3, username: 'user2', namaLengkap: 'Staff Zona Integritas', email: 'user2@bps.go.id', role: 2, createdAt: '2024-01-17' },
    { id: 4, username: 'supervisor', namaLengkap: 'Supervisor Tim', email: 'supervisor@bps.go.id', role: 1, createdAt: '2024-01-18' },
    { id: 5, username: 'operator', namaLengkap: 'Operator Sistem', email: 'operator@bps.go.id', role: 2, createdAt: '2024-01-19' }
];

let currentEditId = null;
let nextId = 6;

// Initialize page
document.addEventListener('DOMContentLoaded', function () {
    updateTable();
    updateStats();
    initializeSearchAndFilter();
});

// Initialize search and filter functionality
function initializeSearchAndFilter() {
    const searchInput = document.getElementById('searchInput');
    const roleFilter = document.getElementById('roleFilter');

    if (searchInput) {
        searchInput.addEventListener('input', function() {
            applyFilters();
        });
    }

    if (roleFilter) {
        roleFilter.addEventListener('change', function() {
            applyFilters();
        });
    }
}

// Apply combined search and filter
function applyFilters() {
    const searchTerm = document.getElementById('searchInput').value.toLowerCase();
    const roleFilter = document.getElementById('roleFilter').value;

    let filteredUsers = users;

    // Apply search filter
    if (searchTerm) {
        filteredUsers = filteredUsers.filter(user =>
            user.username.toLowerCase().includes(searchTerm) ||
            user.namaLengkap.toLowerCase().includes(searchTerm) ||
            user.email.toLowerCase().includes(searchTerm)
        );
    }

    // Apply role filter
    if (roleFilter !== 'all') {
        filteredUsers = filteredUsers.filter(user => user.role === parseInt(roleFilter));
    }

    updateTableWithData(filteredUsers);
}

// Open modal function
function openModal(action, userId = null) {
    const modal = document.getElementById('userModal');
    const modalTitle = document.getElementById('modalTitle');
    const form = document.getElementById('userForm');
    const passwordHint = document.getElementById('passwordHint');
    const passwordField = document.getElementById('password');

    if (action === 'add') {
        modalTitle.textContent = 'Tambah Pengguna Baru';
        form.reset();
        passwordHint.style.display = 'none';
        passwordField.required = true;
        currentEditId = null;
    } else if (action === 'edit' && userId) {
        modalTitle.textContent = 'Edit Pengguna';
        loadUserData(userId);
        passwordHint.style.display = 'inline';
        passwordField.required = false;
        currentEditId = userId;
    }

    modal.style.display = 'block';
    document.body.style.overflow = 'hidden';

    //scroll ke atas saat modal dibuka
    setTimeout(() => {
        const modalBody = document.querySelector('.modal-body');
        if (modalBody) {
            modalBody.scrollTop = 0;
        }
    }, 100);

    // Tambah smooth animation saat modal muncul
    setTimeout(() => {
        modal.style.opacity = '1';
    }, 10);
}

// Close modal function
function closeModal() {
    const modal = document.getElementById('userModal');
    modal.style.display = 'none';
    document.body.style.overflow = 'auto';
    currentEditId = null;
}

// Load user data for editing
function loadUserData(userId) {
    const user = users.find(u => u.id === userId);
    if (user) {
        document.getElementById('userId').value = user.id;
        document.getElementById('username').value = user.username;
        document.getElementById('namaLengkap').value = user.namaLengkap;
        document.getElementById('email').value = user.email;
        document.getElementById('role').value = user.role;
        document.getElementById('password').value = '';
    }
}

// Handle form submission
function handleSubmit(event) {
    event.preventDefault();

    const submitBtn = document.getElementById('submitBtn');
    const originalText = submitBtn.innerHTML;

    // Show loading state
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Menyimpan...';

    // Get form data
    const formData = new FormData(event.target);
    const userData = {
        username: formData.get('username'),
        namaLengkap: formData.get('namaLengkap'),
        email: formData.get('email'),
        password: formData.get('password'),
        role: parseInt(formData.get('role'))
    };

    // Validate required fields
    if (!userData.username || !userData.namaLengkap || !userData.email || !userData.role) {
        showNotification('Mohon lengkapi semua field yang wajib diisi!', 'error');
        submitBtn.disabled = false;
        submitBtn.innerHTML = originalText;
        return;
    }

    // Check for duplicate username (exclude current user when editing)
    const duplicateUser = users.find(u => 
        u.username === userData.username && u.id !== currentEditId
    );
    if (duplicateUser) {
        showNotification('Username sudah digunakan!', 'error');
        submitBtn.disabled = false;
        submitBtn.innerHTML = originalText;
        return;
    }

    // Check for duplicate email (exclude current user when editing)
    const duplicateEmail = users.find(u => 
        u.email === userData.email && u.id !== currentEditId
    );
    if (duplicateEmail) {
        showNotification('Email sudah digunakan!', 'error');
        submitBtn.disabled = false;
        submitBtn.innerHTML = originalText;
        return;
    }

    // Simulate API call delay
    setTimeout(() => {
        if (currentEditId) {
            // Update existing user
            const userIndex = users.findIndex(u => u.id === currentEditId);
            if (userIndex !== -1) {
                users[userIndex] = {
                    ...users[userIndex],
                    ...userData,
                    id: currentEditId
                };
                // Only update password if provided
                if (!userData.password) {
                    delete users[userIndex].password;
                }
                showNotification('User berhasil diupdate!', 'success');
            }
        } else {
            // Add new user
            const newUser = {
                ...userData,
                id: nextId++,
                createdAt: new Date().toISOString().split('T')[0]
            };
            users.push(newUser);
            showNotification('User berhasil ditambahkan!', 'success');
        }

        // Reset button and close modal
        submitBtn.disabled = false;
        submitBtn.innerHTML = originalText;
        closeModal();
        updateTable();
        updateStats();
    }, 1000);
}

// Delete user function
function deleteUser(userId) {
    const user = users.find(u => u.id === userId);
    if (!user) return;

    if (confirm(`Apakah Anda yakin ingin menghapus user "${user.username}"?`)) {
        const userIndex = users.findIndex(u => u.id === userId);
        if (userIndex !== -1) {
            users.splice(userIndex, 1);
            showNotification('User berhasil dihapus!', 'success');
            updateTable();
            updateStats();
        }
    }
}

// Update table function
function updateTable() {
    const tableBody = document.getElementById('userTableBody');
    if (!tableBody) return;

    tableBody.innerHTML = '';

    users.forEach(user => {
        const row = document.createElement('tr');
        row.innerHTML = `
            <td>${user.id}</td>
            <td>
                <div class="user-cell">
                    <div class="user-avatar-small">
                        ${user.username.charAt(0).toUpperCase()}
                    </div>
                    <div class="user-info-cell">
                        <div class="username">${user.username}</div>
                        ${user.id === 1 ? '<div class="current-user">Current User</div>' : ''}
                    </div>
                </div>
            </td>
            <td>${user.namaLengkap}</td>
            <td>${user.email}</td>
            <td>
                <span class="badge ${user.role === 1 ? 'badge-admin' : 'badge-user'}">
                    ${user.role === 1 ? 'Administrator' : 'User'}
                </span>
            </td>
            <td>${formatDate(user.createdAt)}</td>
            <td>
                <div class="action-buttons">
                    <button onclick="openModal('edit', ${user.id})" class="btn-action btn-edit" title="Edit User">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button onclick="deleteUser(${user.id})" class="btn-action btn-delete" title="Hapus User" ${user.id === 1 ? 'disabled' : ''}>
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </td>
        `;
        tableBody.appendChild(row);
    });
}

// Update table with specific data
function updateTableWithData(userData) {
    const tableBody = document.getElementById('usersTableBody');
    if (!tableBody) return;

    tableBody.innerHTML = '';

    userData.forEach(user => {
        const row = document.createElement('tr');
        row.innerHTML = `
            <td>${user.id}</td>
            <td>
                <div class="user-cell">
                    <div class="user-avatar-small">
                        ${user.username.charAt(0).toUpperCase()}
                    </div>
                    <div class="user-info-cell">
                        <div class="username">${user.username}</div>
                        ${user.id === 1 ? '<div class="current-user">Current User</div>' : ''}
                    </div>
                </div>
            </td>
            <td>${user.namaLengkap}</td>
            <td>${user.email}</td>
            <td>
                <span class="badge ${user.role === 1 ? 'badge-admin' : 'badge-user'}">
                    ${user.role === 1 ? 'Administrator' : 'User'}
                </span>
            </td>
            <td>${formatDate(user.createdAt)}</td>
            <td>
                <div class="action-buttons">
                    <button onclick="openModal('edit', ${user.id})" class="btn-action btn-edit" title="Edit User">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button onclick="deleteUser(${user.id})" class="btn-action btn-delete" title="Hapus User" ${user.id === 1 ? 'disabled' : ''}>
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </td>
        `;
        tableBody.appendChild(row);
    });
}

// Update statistics
function updateStats() {
    const totalUsers = users.length;
    const adminCount = users.filter(u => u.role === 1).length;
    const userCount = users.filter(u => u.role === 2).length;
    
    // Calculate today's users (for demo, using recent users)
    const today = new Date().toISOString().split('T')[0];
    const recentDays = 7; // Show users from last 7 days as "recent"
    const recentDate = new Date();
    recentDate.setDate(recentDate.getDate() - recentDays);
    const recentDateString = recentDate.toISOString().split('T')[0];
    
    const todayUsers = users.filter(u => u.createdAt >= recentDateString).length;

    // Update all stat elements
    const totalUsersElements = document.querySelectorAll('#totalUsers');
    const totalAdminsElements = document.querySelectorAll('#totalAdmins');
    const totalRegularUsersElements = document.querySelectorAll('#totalRegularUsers');
    const todayUsersElements = document.querySelectorAll('#todayUsers');

    totalUsersElements.forEach(el => el.textContent = totalUsers);
    totalAdminsElements.forEach(el => el.textContent = adminCount);
    totalRegularUsersElements.forEach(el => el.textContent = userCount);
    todayUsersElements.forEach(el => el.textContent = todayUsers);
}

// Format date function
function formatDate(dateString) {
    const date = new Date(dateString);
    return date.toLocaleDateString('id-ID', {
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    });
}

// Show notification function
function showNotification(message, type = 'info') {
    // Remove existing notifications
    const existingNotifications = document.querySelectorAll('.notification');
    existingNotifications.forEach(notification => notification.remove());

    const notification = document.createElement('div');
    notification.className = `notification notification-${type}`;
    notification.style.cssText = `
        position: fixed;
        top: 2rem;
        right: 2rem;
        z-index: 9999;
        background: ${type === 'success' ? '#10b981' : type === 'error' ? '#ef4444' : '#3b82f6'};
        color: white;
        padding: 1rem 1.5rem;
        border-radius: 8px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.3);
        display: flex;
        align-items: center;
        gap: 0.75rem;
        font-weight: 500;
        animation: slideInRight 0.5s ease;
        max-width: 400px;
    `;

    notification.innerHTML = `
        <i class="fas ${type === 'success' ? 'fa-check-circle' : type === 'error' ? 'fa-exclamation-circle' : 'fa-info-circle'}"></i>
        <span>${message}</span>
        <button type="button" onclick="this.parentElement.remove()" style="
            background: none;
            border: none;
            color: white;
            font-size: 1.25rem;
            cursor: pointer;
            padding: 0;
            margin-left: auto;
        ">×</button>
    `;

    // Add keyframe animation
    if (!document.getElementById('notification-styles')) {
        const style = document.createElement('style');
        style.id = 'notification-styles';
        style.textContent = `
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
    }

    document.body.appendChild(notification);

    // Auto remove after 4 seconds
    setTimeout(() => {
        if (notification.parentElement) {
            notification.style.animation = 'slideInRight 0.5s ease reverse';
            setTimeout(() => notification.remove(), 500);
        }
    }, 4000);
}

// Export users to CSV
function exportToCSV() {
    const csvContent = "data:text/csv;charset=utf-8,"
        + "ID,Username,Nama Lengkap,Email,Role,Tanggal Dibuat\n"
        + users.map(user =>
            `${user.id},"${user.username}","${user.namaLengkap}","${user.email}","${user.role === 1 ? 'Administrator' : 'User'}","${formatDate(user.createdAt)}"`
        ).join("\n");

    const encodedUri = encodeURI(csvContent);
    const link = document.createElement("a");
    link.setAttribute("href", encodedUri);
    link.setAttribute("download", `users_data_${new Date().toISOString().split('T')[0]}.csv`);
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
    
    showNotification('Data berhasil diekspor ke CSV!', 'success');
}

// Go to dashboard function (placeholder)
function goToDashboard() {
    // In a real application, this would redirect to the dashboard
    showNotification('Redirecting to dashboard...', 'info');
    window.location.href = '/Web-Jazirah-main/app/views/dashboard.php';
}

// Handle logout function (placeholder)
function handleLogout() {
    if (confirm('Apakah Anda yakin ingin logout?')) {
        showNotification('Logging out...', 'info');
        // In a real application, this would handle logout
        window.location.href = '/Web-Jazirah-main/index.php';
    }
}

// Close modal when clicking outside
window.onclick = function (event) {
    const modal = document.getElementById('userModal');
    if (event.target === modal) {
        closeModal();
    }
}

// Handle escape key to close modal
document.addEventListener('keydown', function (event) {
    if (event.key === 'Escape') {
        closeModal();
    }
});

// Refresh table data (for future API integration)
function refreshData() {
    showNotification('Refreshing data...', 'info');
    // In a real application, this would fetch fresh data from API
    updateTable();
    updateStats();
    setTimeout(() => {
        showNotification('Data refreshed successfully!', 'success');
    }, 1000);
}

//Handle keyboard untuk modal
document.addEventListener('keydown', function(e) {
    const modal = document.getElementById('formModal');
    if (modal.style.display === 'block') {
        // Tekan ESC untuk tutup modal
        if (e.key === 'Escape') {
            closeModal();
        }
    }
});