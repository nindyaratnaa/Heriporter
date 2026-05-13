<template>
  <div>
    <div v-if="loading" style="text-align:center; color:#888; padding:48px;">
      Memuat dashboard...
    </div>

    <div v-else>
      <!-- House Banner -->
      <div v-if="houseConfig[user.house]"
        style="background:rgba(200,169,110,0.06); border:1px solid rgba(200,169,110,0.25); padding:16px 20px; margin-bottom:20px; display:flex; align-items:center; justify-content:space-between;">
        <div style="display:flex; align-items:center; gap:14px;">
          <img :src="'/images/' + houseConfig[user.house].img" :alt="user.house" style="width:44px; height:44px; object-fit:contain;">
          <div>
            <div style="color:var(--candle); font-size:15px; font-weight:bold; letter-spacing:2px;">{{ user.house?.toUpperCase() }}</div>
            <div style="color:var(--parchment-dim); font-size:11px; margin-top:2px;">{{ houseConfig[user.house].desc }}</div>
          </div>
        </div>
        <div v-if="user.wand_id" style="text-align:right;">
          <div style="color:var(--parchment-dim); font-size:10px; letter-spacing:1px;">TONGKAT SIHIR</div>
          <div style="color:var(--copper); font-size:12px; margin-top:2px;">🪄 Terdaftar</div>
        </div>
      </div>

      <!-- Title -->
      <div class="page-title">⚗️ DASHBOARD</div>
      <div class="page-sub">Selamat datang, {{ user.name }}!</div>

      <!-- XP Bar -->
      <div class="card" style="padding:20px; margin-bottom:24px;">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:8px;">
          <span style="color:var(--copper); font-size:13px; font-weight:bold;">⭐ LEVEL {{ user.level }}</span>
          <span style="color:var(--parchment-dim); font-size:12px;">{{ user.xp }} / {{ user.max_xp }} XP</span>
        </div>
        <div class="xp-bar">
          <div class="xp-fill" :style="'width:' + xpPercent + '%'"></div>
        </div>
      </div>

      <!-- Stats -->
      <div style="display:grid; grid-template-columns:repeat(4,1fr); gap:16px; margin-bottom:24px;">
        <div class="card" style="padding:20px; text-align:center;">
          <div style="font-size:28px; margin-bottom:6px;">🧪</div>
          <div style="color:var(--candle); font-size:28px; font-weight:bold;">{{ stats.total }}</div>
          <div style="color:var(--parchment-dim); font-size:11px; margin-top:4px;">TOTAL RAMUAN</div>
        </div>
        <div class="card-gold" style="padding:20px; text-align:center;">
          <div style="font-size:28px; margin-bottom:6px;">⏳</div>
          <div style="color:var(--copper); font-size:28px; font-weight:bold;">{{ stats.pending }}</div>
          <div style="color:var(--parchment-dim); font-size:11px; margin-top:4px;">PENDING</div>
        </div>
        <div class="card" style="padding:20px; text-align:center;">
          <div style="font-size:28px; margin-bottom:6px;">✅</div>
          <div style="color:#7cfc00; font-size:28px; font-weight:bold;">{{ stats.approved }}</div>
          <div style="color:var(--parchment-dim); font-size:11px; margin-top:4px;">APPROVED</div>
        </div>
        <div class="card" style="padding:20px; text-align:center;">
          <div style="font-size:28px; margin-bottom:6px;">❌</div>
          <div style="color:#e07060; font-size:28px; font-weight:bold;">{{ stats.rejected }}</div>
          <div style="color:var(--parchment-dim); font-size:11px; margin-top:4px;">REJECTED</div>
        </div>
      </div>

      <!-- Quick Actions -->
      <div style="display:grid; grid-template-columns:repeat(3,1fr); gap:16px; margin-bottom:24px;">
        <a href="/student/potions/create" class="card" style="padding:20px; text-align:center; text-decoration:none; display:block;">
          <div style="font-size:32px; margin-bottom:8px;">⚗️</div>
          <div style="color:var(--copper); font-weight:bold; font-size:13px;">RACIK RAMUAN</div>
          <div style="color:var(--parchment-dim); font-size:11px; margin-top:4px;">Buat ramuan baru</div>
        </a>
        <a href="/student/inventory" class="card" style="padding:20px; text-align:center; text-decoration:none; display:block;">
          <div style="font-size:32px; margin-bottom:8px;">📦</div>
          <div style="color:var(--copper); font-weight:bold; font-size:13px;">INVENTORI</div>
          <div style="color:var(--parchment-dim); font-size:11px; margin-top:4px;">Ramuan approved</div>
        </a>
        <a href="/student/rapor" class="card" style="padding:20px; text-align:center; text-decoration:none; display:block;">
          <div style="font-size:32px; margin-bottom:8px;">📜</div>
          <div style="color:var(--copper); font-weight:bold; font-size:13px;">RAPORT</div>
          <div style="color:var(--parchment-dim); font-size:11px; margin-top:4px;">Lihat nilai</div>
        </a>
      </div>

      <!-- Recent Submissions -->
      <div class="card" style="padding:0; overflow:hidden;">
        <div style="padding:14px 20px; border-bottom:1px solid var(--stone-border);">
          <span style="color:var(--copper); font-weight:bold; font-size:13px; letter-spacing:1px;">📋 RAMUAN TERBARU</span>
        </div>
        <div v-if="recent.length === 0" style="padding:32px; text-align:center; color:var(--parchment-dim);">
          <div style="font-size:32px; margin-bottom:8px;">🧪</div>
          Belum ada ramuan. <a href="/student/potions/create" style="color:var(--copper);">Buat sekarang!</a>
        </div>
        <div v-for="p in recent" :key="p.id"
          style="padding:14px 20px; border-bottom:1px solid var(--stone-border); display:flex; justify-content:space-between; align-items:center;">
          <div>
            <div style="font-weight:bold; font-size:13px; color:var(--parchment);">{{ p.name }}</div>
            <div style="color:var(--parchment-dim); font-size:11px; margin-top:2px;">{{ formatDate(p.created_at) }}</div>
          </div>
          <div style="display:flex; align-items:center; gap:12px;">
            <span :class="'badge-' + p.status">{{ p.status?.toUpperCase() }}</span>
            <a :href="'/student/potions/' + p.id" class="btn-outline" style="padding:4px 12px; font-size:11px;">Detail</a>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios';

export default {
  name: 'DashboardApp',
  data() {
    return {
      user: {},
      stats: { total: 0, approved: 0, pending: 0, rejected: 0 },
      recent: [],
      loading: true,
      houseConfig: {
        'Gryffindor': { img: 'gryffindor.png',  desc: 'Keberanian, Keteguhan, Kehormatan' },
        'Ravenclaw':  { img: 'ravenclaw.png',   desc: 'Kecerdasan, Kreativitas, Kebijaksanaan' },
        'Hufflepuff': { img: 'Hufflepuff.png',  desc: 'Kesetiaan, Kesabaran, Kerja Keras' },
        'Slytherin':  { img: 'Slytherin.png',   desc: 'Ambisi, Kecerdikan, Kepemimpinan' },
      }
    };
  },
  computed: {
    xpPercent() {
      if (!this.user.max_xp) return 0;
      return Math.round((this.user.xp / this.user.max_xp) * 100);
    }
  },
  mounted() {
    this.fetchDashboard();
  },
  methods: {
    async fetchDashboard() {
      try {
        const res = await axios.get('/student/dashboard/data', {
          headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
        });
        this.user   = res.data.user;
        this.stats  = res.data.stats;
        this.recent = res.data.recent;
      } catch (err) {
        console.error(err);
      } finally {
        this.loading = false;
      }
    },
    formatDate(dateStr) {
      if (!dateStr) return '';
      return new Date(dateStr).toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric' });
    }
  }
};
</script>