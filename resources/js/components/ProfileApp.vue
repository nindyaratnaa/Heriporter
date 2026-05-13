<template>
  <div>
    <div v-if="loading" style="text-align:center; color:#888; padding:48px;">
      Memuat profil...
    </div>

    <div v-else>
      <div class="page-title">👤 PROFIL SAYA</div>
      <div class="page-sub">Informasi akun, house, dan tongkat sihir</div>

      <div style="display:grid; grid-template-columns:300px 1fr; gap:20px; max-width:900px;">

        <!-- LEFT -->
        <div>
          <!-- Avatar Card -->
          <div class="card" style="padding:24px; text-align:center; margin-bottom:16px;">
            <div style="margin-bottom:16px;">
              <img :src="user.photo ? '/' + user.photo : '/images/dummy profile.jpg'" alt="Avatar"
                style="width:100px; height:100px; border-radius:50%; object-fit:cover; border:2px solid var(--copper); display:block; margin:0 auto;">
            </div>
            <div style="color:var(--candle); font-size:16px; font-weight:bold;">{{ user.name }}</div>
            <div style="color:var(--parchment-dim); font-size:12px; margin-top:4px;">Level {{ user.level }} Student</div>

            <div v-if="houseConfig[user.house]"
              style="display:inline-flex; align-items:center; gap:6px; background:rgba(200,169,110,0.08); border:1px solid rgba(200,169,110,0.25); padding:4px 12px; font-size:11px; margin-top:8px; color:var(--copper); letter-spacing:1px;">
              <img :src="'/images/' + houseConfig[user.house].img" style="width:16px; height:16px; object-fit:contain;">
              {{ user.house }}
            </div>

            <!-- Upload Foto -->
            <form method="POST" :action="'/student/profile/photo'" enctype="multipart/form-data" style="margin-top:16px;" ref="photoForm">
              <input type="hidden" name="_token" :value="csrfToken">
              <label for="photo"
                style="display:block; background:rgba(0,0,0,0.3); border:1px dashed rgba(200,169,110,0.3); padding:10px; cursor:pointer; font-size:11px; color:var(--parchment-dim);">
                📷 Klik untuk ganti foto
                <input type="file" id="photo" name="photo" accept="image/*" style="display:none;" @change="$refs.photoForm.submit()">
              </label>
            </form>
          </div>

          <!-- Info Card -->
          <div class="card" style="padding:20px;">
            <div style="margin-bottom:12px;">
              <div style="color:var(--parchment-dim); font-size:10px; letter-spacing:1px;">EMAIL</div>
              <div style="color:var(--copper); font-size:12px; margin-top:2px; word-break:break-all;">{{ user.email }}</div>
            </div>
            <div style="margin-bottom:12px;">
              <div style="color:var(--parchment-dim); font-size:10px; letter-spacing:1px;">BERGABUNG</div>
              <div style="color:var(--copper); font-size:12px; margin-top:2px;">{{ formatDate(user.created_at) }}</div>
            </div>
            <div>
              <div style="color:var(--parchment-dim); font-size:10px; letter-spacing:1px; margin-bottom:6px;">XP — LEVEL {{ user.level }}</div>
              <div class="xp-bar">
                <div class="xp-fill" :style="'width:' + xpPercent + '%'"></div>
              </div>
              <div style="display:flex; justify-content:space-between; margin-top:4px; font-size:11px; color:var(--parchment-dim);">
                <span>{{ user.xp }} XP</span>
                <span>{{ user.max_xp }} XP</span>
              </div>
            </div>
          </div>
        </div>

        <!-- RIGHT -->
        <div>
          <!-- House -->
          <div v-if="houseConfig[user.house]" class="card" style="padding:20px; margin-bottom:16px;">
            <div style="color:var(--parchment-dim); font-size:10px; font-weight:bold; letter-spacing:2px; margin-bottom:14px;">🏰 HOUSE</div>
            <div style="display:flex; align-items:center; gap:20px;">
              <img :src="'/images/' + houseConfig[user.house].img" :alt="user.house"
                style="width:72px; height:72px; object-fit:contain; filter:drop-shadow(0 0 10px rgba(200,169,110,0.3));">
              <div>
                <div style="color:var(--candle); font-size:20px; font-weight:bold; letter-spacing:3px;">{{ user.house?.toUpperCase() }}</div>
                <div style="color:var(--parchment-dim); font-size:12px; margin-top:6px;">{{ houseConfig[user.house].desc }}</div>
              </div>
            </div>
          </div>

          <!-- Wand -->
          <div v-if="wand" class="card" style="padding:20px; margin-bottom:16px;">
            <div style="color:var(--parchment-dim); font-size:10px; font-weight:bold; letter-spacing:2px; margin-bottom:14px;">🪄 TONGKAT SIHIR — OLLIVANDERS</div>
            <div style="display:flex; align-items:center; gap:20px; margin-bottom:14px;">
              <img :src="'/images/' + wand.gambar" :alt="wand.nama"
                style="width:60px; height:80px; object-fit:contain; filter:drop-shadow(0 0 8px rgba(200,169,110,0.4));">
              <div>
                <div style="color:var(--candle); font-size:14px; font-weight:bold;">{{ wand.nama }}</div>
                <div style="color:var(--parchment-dim); font-size:11px; margin-top:4px; font-style:italic; line-height:1.5;">{{ wand.deskripsi }}</div>
              </div>
            </div>
            <div style="display:grid; grid-template-columns:1fr 1fr; gap:8px;">
              <div v-for="[label, val] in wandDetails" :key="label"
                style="background:rgba(0,0,0,0.3); padding:10px; border:1px solid var(--stone-border);">
                <div style="color:var(--parchment-dim); font-size:10px; letter-spacing:1px;">{{ label }}</div>
                <div style="color:var(--parchment); font-size:12px; margin-top:2px;">{{ val }}</div>
              </div>
            </div>
          </div>

          <!-- Stats -->
          <div style="display:grid; grid-template-columns:repeat(4,1fr); gap:10px; margin-bottom:12px;">
            <div class="card" style="padding:14px; text-align:center;">
              <div style="font-size:20px; margin-bottom:4px;">🧪</div>
              <div style="color:var(--candle); font-size:22px; font-weight:bold;">{{ stats.total }}</div>
              <div style="color:var(--parchment-dim); font-size:10px;">TOTAL</div>
            </div>
            <div class="card" style="padding:14px; text-align:center;">
              <div style="font-size:20px; margin-bottom:4px;">✅</div>
              <div style="color:#7cfc00; font-size:22px; font-weight:bold;">{{ stats.approved }}</div>
              <div style="color:var(--parchment-dim); font-size:10px;">APPROVED</div>
            </div>
            <div class="card" style="padding:14px; text-align:center;">
              <div style="font-size:20px; margin-bottom:4px;">⏳</div>
              <div style="color:var(--copper); font-size:22px; font-weight:bold;">{{ stats.pending }}</div>
              <div style="color:var(--parchment-dim); font-size:10px;">PENDING</div>
            </div>
            <div class="card" style="padding:14px; text-align:center;">
              <div style="font-size:20px; margin-bottom:4px;">❌</div>
              <div style="color:#e07060; font-size:22px; font-weight:bold;">{{ stats.rejected }}</div>
              <div style="color:var(--parchment-dim); font-size:10px;">REJECTED</div>
            </div>
          </div>

          <div v-if="stats.total > 0" class="card" style="padding:14px; text-align:center;">
            <div style="color:var(--parchment-dim); font-size:10px; letter-spacing:1px; margin-bottom:4px;">SUCCESS RATE</div>
            <div style="color:var(--candle); font-size:28px; font-weight:bold;">{{ successRate }}%</div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios';

export default {
  name: 'ProfileApp',
  data() {
    return {
      user: {},
      wand: null,
      stats: { total: 0, approved: 0, pending: 0, rejected: 0 },
      loading: true,
      csrfToken: document.querySelector('meta[name="csrf-token"]')?.getAttribute('content'),
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
    },
    successRate() {
      if (!this.stats.total) return 0;
      return Math.round((this.stats.approved / this.stats.total) * 100);
    },
    wandDetails() {
      if (!this.wand) return [];
      return [
        ['BAHAN KAYU', this.wand.bahan_kayu],
        ['BAHAN INTI', this.wand.bahan_inti],
        ['PANJANG', this.wand.panjang],
        ['FLEKSIBILITAS', this.wand.fleksibilitas],
      ];
    }
  },
  mounted() {
    this.fetchProfile();
  },
  methods: {
    async fetchProfile() {
      try {
        const res = await axios.get('/student/profile/data', {
          headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
        });
        this.user  = res.data.user;
        this.wand  = res.data.wand;
        this.stats = res.data.stats;
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