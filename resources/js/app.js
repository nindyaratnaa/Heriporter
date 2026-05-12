import { createApp } from 'vue';
import InventoryApp from './components/InventoryApp.vue';
import DashboardApp from './components/DashboardApp.vue';
import ProfileApp   from './components/ProfileApp.vue';

const inventoryEl = document.getElementById('inventory-app');
if (inventoryEl) createApp(InventoryApp).mount('#inventory-app');

const dashboardEl = document.getElementById('dashboard-app');
if (dashboardEl) createApp(DashboardApp).mount('#dashboard-app');

const profileEl = document.getElementById('profile-app');
if (profileEl) createApp(ProfileApp).mount('#profile-app');