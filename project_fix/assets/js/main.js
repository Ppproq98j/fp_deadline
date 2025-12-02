// assets/js/main.js
document.addEventListener('DOMContentLoaded', function () {

    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('overlay');
    const openBtn = document.getElementById('openSidebar') || document.getElementById('menuBtn');
    const closeBtn = document.getElementById('closeSidebar');

    const safe = el => el !== null && el !== undefined;

    function openSidebar() {
        if (!safe(sidebar) || !safe(overlay)) return;
        sidebar.classList.add('show');
        overlay.classList.add('show');
    }
    function closeSidebar() {
        if (!safe(sidebar) || !safe(overlay)) return;
        sidebar.classList.remove('show');
        overlay.classList.remove('show');
    }

    if (safe(openBtn)) openBtn.addEventListener('click', (e) => { e.preventDefault(); openSidebar(); });
    if (safe(closeBtn)) closeBtn.addEventListener('click', (e) => { e.preventDefault(); closeSidebar(); });
    if (safe(overlay)) overlay.addEventListener('click', closeSidebar);
    document.addEventListener('keydown', (e) => { if (e.key === 'Escape') closeSidebar(); });

    // Add modal
    const addModal = document.getElementById('addModal');
    const openAddBtn = document.getElementById('openAddModal');
    const closeAddBtn = document.getElementById('closeAdd');
    const cancelAddBtn = document.getElementById('cancelAdd');

    if (safe(openAddBtn)) openAddBtn.addEventListener('click', () => {
        addModal.classList.remove('hidden'); addModal.classList.add('flex');
    });
    if (safe(closeAddBtn)) closeAddBtn.addEventListener('click', () => {
        addModal.classList.add('hidden'); addModal.classList.remove('flex');
    });
    if (safe(cancelAddBtn)) cancelAddBtn.addEventListener('click', () => {
        addModal.classList.add('hidden'); addModal.classList.remove('flex');
    });

    // Edit modal handlers
    const editModal = document.getElementById('editModal');
    const closeEditBtn = document.getElementById('closeEdit');
    const cancelEditBtn = document.getElementById('cancelEdit');
    if (safe(closeEditBtn)) closeEditBtn.addEventListener('click', () => { editModal.classList.add('hidden'); editModal.classList.remove('flex'); });
    if (safe(cancelEditBtn)) cancelEditBtn.addEventListener('click', () => { editModal.classList.add('hidden'); editModal.classList.remove('flex'); });

    // openEditModal global function
    window.openEditModal = function (id, data) {
        try { if (typeof data === 'string') data = JSON.parse(data); } catch (e) { }
        const byId = (n) => document.getElementById(n);
        if (byId('edit-id')) byId('edit-id').value = id;
        if (byId('edit-title')) byId('edit-title').value = data.title || '';
        if (byId('edit-description')) byId('edit-description').value = data.description || '';
        if (byId('edit-due_date')) byId('edit-due_date').value = data.due_date || '';
        if (byId('edit-priority')) byId('edit-priority').value = data.priority || 'sedang';
        if (byId('edit-category')) byId('edit-category').value = data.category || '';
        if (editModal) { editModal.classList.remove('hidden'); editModal.classList.add('flex'); }
    };

    // Test notif (demo)
    const testNotifBtn = document.getElementById('testNotif');
    if (safe(testNotifBtn)) testNotifBtn.addEventListener('click', function () {
        if (confirm('Tambahkan notifikasi sample ke database? (OK untuk ya)')) {
            if (window.location) window.location.href = 'add_notification.php';
        }
    });

    // Export CSV (client)
    const exportBtn = document.getElementById('exportBtn');
    if (safe(exportBtn)) exportBtn.addEventListener('click', function () {
        const rows = [['Title', 'Due Date', 'Priority', 'Category', 'Status']];
        document.querySelectorAll('.space-y-4 > div').forEach(item => {
            const titleEl = item.querySelector('h3');
            if (!titleEl) return;
            const title = titleEl.innerText.trim();
            let due = '';
            const s = item.querySelectorAll('span, p');
            s.forEach(el => { if (el.innerText.startsWith('Due:')) due = el.innerText.replace('Due:', '').trim(); });
            rows.push([title, due, '', '', '']);
        });
        const csv = rows.map(r => r.map(v => '"' + String(v).replace(/"/g, '""') + '"').join(',')).join('\n');
        const blob = new Blob([csv], { type: 'text/csv' });
        const url = URL.createObjectURL(blob);
        const a = document.createElement('a'); a.href = url; a.download = 'deadlines_export.csv';
        document.body.appendChild(a); a.click(); a.remove(); URL.revokeObjectURL(url);
    });

});
