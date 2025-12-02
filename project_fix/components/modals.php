<?php /* expects $user_id possibly defined by including page */ ?>

<!-- Add Modal -->
<div id="addModal" class="fixed inset-0 bg-black bg-opacity-40 hidden items-center justify-center z-50">
  <div class="bg-white rounded-lg w-full max-w-lg p-6">
    <div class="flex justify-between items-center mb-4">
      <h2 class="text-xl font-semibold">Tambah Deadline Baru</h2>
      <button id="closeAdd" class="text-gray-600 text-xl">✕</button>
    </div>

    <form action="add_deadline.php" method="POST">
      <input type="hidden" name="user_id" value="<?= htmlspecialchars($user_id ?? '') ?>">
      <div class="mb-3">
        <label class="block text-sm font-medium text-gray-700">Judul</label>
        <input name="title" required class="w-full border rounded px-3 py-2" placeholder="Judul tugas / proyek">
      </div>
      <div class="mb-3">
        <label class="block text-sm font-medium text-gray-700">Deskripsi</label>
        <textarea name="description" rows="3" class="w-full border rounded px-3 py-2"></textarea>
      </div>
      <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 mb-3">
        <div>
          <label class="block text-sm font-medium text-gray-700">Tanggal</label>
          <input type="date" name="due_date" required class="w-full border rounded px-3 py-2">
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700">Prioritas</label>
          <select name="priority" class="w-full border rounded px-3 py-2">
            <option value="sedang">Sedang</option>
            <option value="tinggi">Tinggi</option>
            <option value="rendah">Rendah</option>
          </select>
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700">Kategori</label>
          <input name="category" class="w-full border rounded px-3 py-2">
        </div>
      </div>

      <div class="flex justify-end gap-3">
        <button type="button" id="cancelAdd" class="px-4 py-2 rounded border">Batal</button>
        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">Simpan</button>
      </div>
    </form>
  </div>
</div>

<!-- Edit Modal -->
<div id="editModal" class="fixed inset-0 bg-black bg-opacity-40 hidden items-center justify-center z-50">
  <div class="bg-white rounded-lg w-full max-w-lg p-6">
    <div class="flex justify-between items-center mb-4">
      <h2 class="text-xl font-semibold">Edit Deadline</h2>
      <button id="closeEdit" class="text-gray-600 text-xl">✕</button>
    </div>

    <form id="editForm" action="edit_deadline.php" method="POST">
      <input type="hidden" name="id" id="edit-id">
      <div class="mb-3">
        <label class="block text-sm font-medium text-gray-700">Judul</label>
        <input type="text" id="edit-title" name="title" class="w-full border rounded px-3 py-2">
      </div>
      <div class="mb-3">
        <label class="block text-sm font-medium text-gray-700">Deskripsi</label>
        <textarea id="edit-description" name="description" rows="3" class="w-full border rounded px-3 py-2"></textarea>
      </div>
      <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 mb-3">
        <div>
          <label class="block text-sm font-medium text-gray-700">Tanggal</label>
          <input type="date" id="edit-due_date" name="due_date" class="w-full border rounded px-3 py-2">
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700">Prioritas</label>
          <select id="edit-priority" name="priority" class="w-full border rounded px-3 py-2">
            <option value="tinggi">Tinggi</option>
            <option value="sedang">Sedang</option>
            <option value="rendah">Rendah</option>
          </select>
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700">Kategori</label>
          <input type="text" id="edit-category" name="category" class="w-full border rounded px-3 py-2">
        </div>
      </div>

      <div class="flex justify-end gap-3">
        <button type="button" id="cancelEdit" class="px-4 py-2 rounded border">Batal</button>
        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">Simpan</button>
      </div>
    </form>
  </div>
</div>
