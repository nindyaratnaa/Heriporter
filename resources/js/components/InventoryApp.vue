<template>
  <div>
    <!-- Loading -->
    <div v-if="loading" style="text-align:center; color:#888; padding:48px;">
      Memuat inventori...
    </div>

    <!-- Kosong -->
    <div v-else-if="inventory.length === 0" class="card" style="padding:48px; text-align:center;">
      <div style="font-size:48px; margin-bottom:12px;">📦</div>
      <div class="gold" style="font-size:16px; font-weight:bold; margin-bottom:8px;">Inventori Kosong</div>
      <div style="color:#888; font-size:13px; margin-bottom:20px;">Belum ada ramuan yang disetujui guru.</div>
      <a href="/student/potions/create" class="btn-gold">+ BUAT RAMUAN</a>
    </div>

    <!-- List Inventory -->
    <div v-else style="display:grid; grid-template-columns:repeat(auto-fill, minmax(260px,1fr)); gap:16px;">
      <div
        v-for="potion in inventory"
        :key="potion.id"
        class="card"
        style="padding:0; overflow:hidden; border-color:rgba(39,174,96,0.4);"
      >
        <div style="padding:16px; border-bottom:1px solid rgba(39,174,96,0.2);">
          <div style="display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:8px;">
            <span style="font-size:28px;">🧪</span>
            <span class="badge-approved">APPROVED</span>
          </div>
          <div style="font-weight:bold; font-size:14px; margin-bottom:4px;">{{ potion.name }}</div>
          <div style="color:#888; font-size:12px;">{{ potion.description?.substring(0, 70) }}{{ potion.description?.length > 70 ? '...' : '' }}</div>
          <div v-if="potion.rating" style="margin-top:8px; color:var(--gold); font-size:12px;">
            ⭐ Rating: {{ potion.rating }}/10
          </div>
          <div v-if="potion.guru_comment" style="margin-top:4px; color:#888; font-size:11px; font-style:italic;">
            {{ potion.guru_comment }}
          </div>
        </div>
        <div style="padding:10px 16px; background:rgba(0,0,0,0.2); display:flex; justify-content:space-between; align-items:center;">
          <span style="color:#888; font-size:11px;">{{ formatDate(potion.created_at) }}</span>
          <button
            @click="hapus(potion.id)"
            class="btn-danger"
            style="padding:4px 10px; font-size:11px;"
          >
            🗑 Hapus
          </button>
        </div>
      </div>
    </div>

    <!-- Pesan sukses/error -->
    <div v-if="message" style="margin-top:16px; padding:12px; text-align:center; border-radius:4px;"
      :style="isError ? 'background:rgba(192,57,43,0.15); color:#e07060; border:1px solid rgba(192,57,43,0.3);' : 'background:rgba(39,174,96,0.15); color:#27ae60; border:1px solid rgba(39,174,96,0.3);'">
      {{ message }}
    </div>
  </div>
</template>

<script>
import axios from 'axios';

export default {
  name: 'InventoryApp',
  data() {
    return {
      inventory: [],
      loading: true,
      message: '',
      isError: false,
    };
  },
  mounted() {
    this.fetchInventory();
  },
  methods: {
    async fetchInventory() {
      try {
        const res = await axios.get('/student/inventory/data', {
          headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
          }
        });
        this.inventory = res.data;
      } catch (err) {
        this.message = 'Gagal memuat inventori.';
        this.isError = true;
      } finally {
        this.loading = false;
      }
    },

    async hapus(id) {
      if (!confirm('Hapus ramuan dari inventori?')) return;
      try {
        const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        await axios.delete(`/student/inventory/${id}`, {
          headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': token
          }
        });
        this.inventory = this.inventory.filter(p => p.id !== id);
        this.message = 'Ramuan berhasil dihapus dari inventori.';
        this.isError = false;
      } catch (err) {
        this.message = 'Gagal menghapus ramuan.';
        this.isError = true;
      }
    },

    formatDate(dateStr) {
      if (!dateStr) return '';
      const d = new Date(dateStr);
      return d.toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric' });
    }
  }
};
</script>