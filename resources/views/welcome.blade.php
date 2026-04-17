<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MonitorDashboard - Web Monitoring System</title>
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Google Fonts: Inter -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Alpine JS for simple interactions -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
        body { font-family: 'Inter', sans-serif; background-color: #f8fafc; }
        .glass { background: rgba(255, 255, 255, 0.7); backdrop-filter: blur(10px); border: 1px solid rgba(255, 255, 255, 0.2); }
        .sidebar-item-active { background: #3b82f6; color: white; box-shadow: 0 4px 6px -1px rgba(59, 130, 246, 0.2); }
        .transition-all { transition: all 0.3s ease; }
        [x-cloak] { display: none !important; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
        .animate-fadeIn { animation: fadeIn 0.4s ease forwards; }
    </style>
</head>
<body class="bg-gray-50 text-slate-900" x-data="app()">
    
    <!-- Sidebar -->
    <div class="fixed left-0 top-0 h-full w-64 bg-slate-900 text-slate-300 shadow-2xl transition-all z-50 overflow-y-auto hidden md:block">
        <div class="p-6 border-b border-slate-800 flex items-center space-x-3">
            <div class="w-8 h-8 bg-blue-500 rounded-lg flex items-center justify-center text-white">
                <i class="fas fa-microchip"></i>
            </div>
            <span class="text-xl font-bold text-white tracking-tight">MonitorHub</span>
        </div>
        
        <nav class="p-4 space-y-2">
            <button @click="navigate('dashboard')" :class="activePage === 'dashboard' ? 'sidebar-item-active text-white' : 'hover:bg-slate-800 hover:text-white'" class="w-full flex items-center space-x-3 p-3 rounded-xl transition-all">
                <i class="fas fa-chart-line w-6"></i>
                <span class="font-medium">Dashboard</span>
            </button>
            <button @click="navigate('monitors')" :class="activePage === 'monitors' ? 'sidebar-item-active text-white' : 'hover:bg-slate-800 hover:text-white'" class="w-full flex items-center space-x-3 p-3 rounded-xl transition-all">
                <i class="fas fa-desktop w-6"></i>
                <span class="font-medium">Websites</span>
            </button>
            <button @click="navigate('logs')" :class="activePage === 'logs' ? 'sidebar-item-active text-white' : 'hover:bg-slate-800 hover:text-white'" class="w-full flex items-center space-x-3 p-3 rounded-xl transition-all">
                <i class="fas fa-list-ul w-6"></i>
                <span class="font-medium">Activity Logs</span>
            </button>
        </nav>
        
        <div class="absolute bottom-6 left-6 right-6">
            <div class="p-4 bg-slate-800 rounded-2xl border border-slate-700">
                <p class="text-xs text-slate-500 mb-1">System Status</p>
                <div class="flex items-center space-x-2">
                    <div class="w-2 h-2 bg-emerald-500 rounded-full animate-pulse"></div>
                    <span class="text-sm font-semibold text-white">Engine Online</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="md:ml-64 min-h-screen relative">
        <!-- Top Nav -->
        <header class="h-16 glass sticky top-0 z-40 px-8 flex items-center justify-between shadow-sm border-b border-slate-100">
            <h2 class="text-lg font-bold text-slate-800" x-text="pageTitle"></h2>
            <div class="flex items-center space-x-4">
                <div class="text-right hidden sm:block">
                    <p class="text-xs text-slate-500 font-medium">Last Sync</p>
                    <p class="text-sm font-semibold text-slate-700" x-text="lastSyncTime"></p>
                </div>
                <button @click="refreshData()" class="p-2 text-slate-400 hover:text-blue-500 transition-colors">
                    <i class="fas fa-sync-alt" :class="loading ? 'animate-spin' : ''"></i>
                </button>
            </div>
        </header>

        <main class="p-8 max-w-7xl mx-auto">
            
            <!-- Dashboard Home -->
            <template x-if="activePage === 'dashboard'">
                <div class="space-y-8 animate-fadeIn">
                    <!-- Summary Cards -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                        <div class="bg-white p-6 rounded-3xl shadow-sm border border-slate-100 flex items-center space-x-4">
                            <div class="w-12 h-12 bg-blue-50 rounded-2xl flex items-center justify-center text-blue-500 text-lg">
                                <i class="fas fa-globe"></i>
                            </div>
                            <div>
                                <p class="text-sm text-slate-500 font-medium">Total</p>
                                <p class="text-2xl font-bold" x-text="stats.total"></p>
                            </div>
                        </div>
                        <div class="bg-white p-6 rounded-3xl shadow-sm border border-slate-100 flex items-center space-x-4">
                            <div class="w-12 h-12 bg-emerald-50 rounded-2xl flex items-center justify-center text-emerald-500 text-lg">
                                <i class="fas fa-arrow-up"></i>
                            </div>
                            <div>
                                <p class="text-sm text-slate-500 font-medium">Healthy</p>
                                <p class="text-2xl font-bold text-emerald-600" x-text="stats.up"></p>
                            </div>
                        </div>
                        <div class="bg-white p-6 rounded-3xl shadow-sm border border-slate-100 flex items-center space-x-4">
                            <div class="w-12 h-12 bg-rose-50 rounded-2xl flex items-center justify-center text-rose-500 text-lg">
                                <i class="fas fa-triangle-exclamation"></i>
                            </div>
                            <div>
                                <p class="text-sm text-slate-500 font-medium">Down</p>
                                <p class="text-2xl font-bold text-rose-600" x-text="stats.down"></p>
                            </div>
                        </div>
                        <div class="bg-white p-6 rounded-3xl shadow-sm border border-slate-100 flex items-center space-x-4">
                            <div class="w-12 h-12 bg-amber-50 rounded-2xl flex items-center justify-center text-amber-500 text-lg">
                                <i class="fas fa-bolt"></i>
                            </div>
                            <div>
                                <p class="text-sm text-slate-500 font-medium">Avg Resp</p>
                                <p class="text-2xl font-bold" x-text="stats.avgResponse + 'ms'"></p>
                            </div>
                        </div>
                    </div>

                    <!-- Dashboard Detail -->
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                        <!-- Left: Status Feed -->
                        <div class="lg:col-span-2 space-y-4">
                            <div class="flex items-center justify-between mb-2">
                                <h3 class="text-lg font-bold text-slate-800">Operational Overview</h3>
                                <span class="text-xs bg-emerald-100 text-emerald-700 font-bold px-2 py-1 rounded-full uppercase tracking-wider">Live</span>
                            </div>
                            
                            <div class="grid gap-4">
                                <template x-for="monitor in monitors" :key="monitor.id">
                                    <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-100 hover:border-blue-200 transition-all flex items-center justify-between group cursor-pointer" @click="navigate('monitors')">
                                        <div class="flex items-center space-x-4">
                                            <div :class="monitor.status === 'UP' ? 'bg-emerald-500' : 'bg-rose-500'" class="w-3 h-3 rounded-full shadow-lg"></div>
                                            <div>
                                                <h4 class="font-bold text-slate-800 group-hover:text-blue-600 transition-colors" x-text="monitor.name"></h4>
                                                <p class="text-xs text-slate-400 font-mono" x-text="monitor.url"></p>
                                            </div>
                                        </div>
                                        <div class="text-right">
                                            <p class="font-bold text-slate-700" x-text="monitor.response_time + 'ms'"></p>
                                            <p class="text-[10px] text-slate-400 uppercase tracking-widest font-bold" x-text="formatDate(monitor.checked_at)"></p>
                                        </div>
                                    </div>
                                </template>
                                <template x-if="monitors.length === 0">
                                    <div class="text-center py-12 bg-slate-50 rounded-3xl border-2 border-dashed border-slate-200">
                                        <i class="fas fa-magnifying-glass text-4xl text-slate-300 mb-4"></i>
                                        <p class="text-slate-500 font-medium">No monitors found. Add one to start tracking!</p>
                                    </div>
                                </template>
                            </div>
                        </div>

                        <!-- Right: Recent Logs -->
                        <div class="space-y-4">
                            <h3 class="text-lg font-bold text-slate-800">Recent Incidents</h3>
                            <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden divide-y divide-slate-50">
                                <template x-for="log in logs.slice(0, 10)" :key="log.id">
                                    <div class="p-4 flex items-start space-x-3">
                                        <div :class="log.status === 'UP' ? 'text-emerald-500' : 'text-rose-500'" class="mt-1">
                                            <i :class="log.status === 'UP' ? 'fas fa-check-circle' : 'fas fa-times-circle'"></i>
                                        </div>
                                        <div>
                                            <p class="text-sm font-semibold text-slate-800" x-text="log.monitor_name + ' is ' + log.status"></p>
                                            <p class="text-[10px] text-slate-400 font-medium" x-text="formatDate(log.checked_at)"></p>
                                            <p x-if="log.error_message" class="text-[10px] text-rose-400 mt-1" x-text="log.error_message"></p>
                                        </div>
                                    </div>
                                </template>
                                <template x-if="logs.length === 0">
                                    <div class="p-8 text-center text-slate-400 text-sm italic">
                                        No check history available.
                                    </div>
                                </template>
                            </div>
                        </div>
                    </div>
                </div>
            </template>

            <!-- Websites Page -->
            <template x-if="activePage === 'monitors'">
                <div class="space-y-6 animate-fadeIn">
                    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                        <div>
                            <h3 class="text-2xl font-bold text-slate-800">Your Monitoring Targets</h3>
                            <p class="text-slate-500">Manage all websites being monitored by the engine.</p>
                        </div>
                        <button @click="showAddModal = true" class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2.5 rounded-xl font-bold shadow-lg shadow-blue-200 flex items-center space-x-2 transition-all active:scale-95">
                            <i class="fas fa-plus"></i>
                            <span>Add New Target</span>
                        </button>
                    </div>

                    <div class="bg-white rounded-[2rem] shadow-sm border border-slate-100 overflow-hidden overflow-x-auto">
                        <table class="w-full text-left whitespace-nowrap">
                            <thead class="bg-slate-50 border-b border-slate-100">
                                <tr>
                                    <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Website</th>
                                    <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider text-center">Interval</th>
                                    <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider text-center">Current Status</th>
                                    <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider text-right">Performance</th>
                                    <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-50">
                                <template x-for="m in monitors" :key="m.id">
                                    <tr class="hover:bg-blue-50/30 transition-colors group">
                                        <td class="px-6 py-4">
                                            <div class="flex flex-col">
                                                <span class="font-bold text-slate-800" x-text="m.name"></span>
                                                <span class="text-xs text-slate-400 font-mono" x-text="m.url"></span>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 text-center">
                                            <span class="bg-slate-100 text-slate-600 px-3 py-1 rounded-lg text-xs font-bold" x-text="m.interval + 'm'"></span>
                                        </td>
                                        <td class="px-6 py-4 text-center">
                                            <template x-if="m.status === 'UP'">
                                                <span class="inline-flex items-center space-x-1.5 text-emerald-600 bg-emerald-50 px-3 py-1 rounded-full text-xs font-bold border border-emerald-100">
                                                    <span class="w-1.5 h-1.5 bg-emerald-500 rounded-full"></span>
                                                    <span>OPERATIONAL</span>
                                                </span>
                                            </template>
                                            <template x-if="m.status !== 'UP'">
                                                <span class="inline-flex items-center space-x-1.5 text-rose-600 bg-rose-50 px-3 py-1 rounded-full text-xs font-bold border border-rose-100">
                                                    <span class="w-1.5 h-1.5 bg-rose-500 rounded-full animate-pulse"></span>
                                                    <span>OUTAGE</span>
                                                </span>
                                            </template>
                                        </td>
                                        <td class="px-6 py-4 text-right">
                                            <span class="font-mono text-slate-700 font-bold" x-text="m.response_time + ' ms'"></span>
                                        </td>
                                        <td class="px-6 py-4 text-center">
                                            <div class="flex items-center justify-center space-x-2">
                                                <button @click="openEditModal(m)" class="p-2 text-blue-500 hover:bg-blue-50 rounded-lg transition-colors" title="Edit">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <button @click="deleteMonitor(m.id)" class="p-2 text-rose-500 hover:bg-rose-50 rounded-lg transition-colors" title="Delete">
                                                    <i class="fas fa-trash-alt"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>
                </div>
            </template>

            <!-- Logs Page -->
            <template x-if="activePage === 'logs'">
                <div class="space-y-6 animate-fadeIn">
                    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                        <div>
                            <h3 class="text-2xl font-bold text-slate-800">Detailed Activity Stream</h3>
                            <p class="text-slate-500">View and manage the full history of monitoring checks.</p>
                        </div>
                        <div class="flex items-center space-x-3">
                            <a href="/api/logs/export" class="bg-blue-50 text-blue-600 hover:bg-blue-100 px-4 py-2 rounded-xl font-bold text-sm transition-all border border-blue-100 flex items-center space-x-2">
                                <i class="fas fa-file-csv"></i>
                                <span>Export CSV</span>
                            </a>
                            <button @click="clearLogs()" class="bg-rose-50 text-rose-600 hover:bg-rose-100 px-4 py-2 rounded-xl font-bold text-sm transition-all border border-rose-100 flex items-center space-x-2">
                                <i class="fas fa-trash-sweep"></i>
                                <span>Clear History</span>
                            </button>
                        </div>
                    </div>

                    <div class="bg-white rounded-[2rem] shadow-sm border border-slate-100 overflow-hidden overflow-x-auto">
                        <table class="w-full text-left whitespace-nowrap">
                            <thead class="bg-slate-50 border-b border-slate-100">
                                <tr>
                                    <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase">Timestamp</th>
                                    <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase text-center">Website Name</th>
                                    <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase text-center">Status</th>
                                    <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase text-center">Response</th>
                                    <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase text-center">Code</th>
                                    <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase">Message</th>
                                    <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-50">
                                <template x-for="log in logs" :key="log.id">
                                    <tr class="hover:bg-slate-50/50">
                                        <td class="px-6 py-4 text-sm font-medium text-slate-600" x-text="formatDate(log.checked_at)"></td>
                                        <td class="px-6 py-4 text-center text-sm font-bold text-slate-700" x-text="log.monitor_name"></td>
                                        <td class="px-6 py-4 text-center">
                                            <span :class="log.status === 'UP' ? 'text-emerald-600 bg-emerald-50' : 'text-rose-600 bg-rose-50'" class="px-2 py-0.5 rounded text-[10px] font-bold" x-text="log.status"></span>
                                        </td>
                                        <td class="px-6 py-4 text-center text-sm font-mono" x-text="log.response_time + 'ms'"></td>
                                        <td class="px-6 py-4 text-center">
                                            <span class="text-xs font-bold text-slate-500" x-text="log.status_code || '-'"></span>
                                        </td>
                                        <td class="px-6 py-4 text-sm text-slate-500 truncate max-w-[200px]" :title="log.error_message" x-text="log.error_message || 'OK'"></td>
                                        <td class="px-6 py-4 text-center">
                                            <button @click="deleteLog(log.id)" class="p-2 text-rose-500 hover:bg-rose-50 rounded-lg transition-colors" title="Delete Log Entry">
                                                <i class="fas fa-trash-alt text-xs"></i>
                                            </button>
                                        </td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>
                </div>
            </template>
        </main>
    </div>

    <!-- Add Monitor Modal -->
    <div x-show="showAddModal" class="fixed inset-0 z-[100] overflow-y-auto" x-cloak x-transition>
        <div class="flex items-center justify-center min-h-screen p-4">
            <div @click="showAddModal = false" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm"></div>
            
            <div class="relative bg-white rounded-[2.5rem] w-full max-w-md p-10 shadow-2xl">
                <h3 class="text-2xl font-bold text-slate-800 mb-2">Monitor Site</h3>
                <p class="text-slate-500 text-sm mb-8">Enter the details below to start tracking uptime.</p>
                
                <form @submit.prevent="addMonitor()" class="space-y-6">
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">Display Name</label>
                        <input x-model="newTarget.name" type="text" placeholder="e.g. My Portfolio" class="w-full px-5 py-3 rounded-2xl bg-slate-50 border border-slate-200 focus:ring-4 focus:ring-blue-100 focus:border-blue-500 outline-none transition-all font-medium transition-all" required>
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">Target URL</label>
                        <input x-model="newTarget.url" type="text" placeholder="https://example.com" class="w-full px-5 py-3 rounded-2xl bg-slate-50 border border-slate-200 focus:ring-4 focus:ring-blue-100 focus:border-blue-500 outline-none transition-all font-medium transition-all" required>
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">Check Interval (Minutes)</label>
                        <select x-model="newTarget.interval" class="w-full px-5 py-3 rounded-2xl bg-slate-50 border border-slate-200 focus:ring-4 focus:ring-blue-100 focus:border-blue-500 outline-none transition-all font-bold transition-all">
                            <option value="1">Every Minute</option>
                            <option value="5">Every 5 Minutes</option>
                            <option value="15">Every 15 Minutes</option>
                            <option value="60">Hourly</option>
                        </select>
                    </div>
                    <button type="submit" :disabled="loading" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-4 rounded-2xl shadow-xl shadow-blue-200 transition-all active:scale-95 flex items-center justify-center space-x-2">
                        <template x-if="!loading">
                            <span>Initialize Monitoring</span>
                        </template>
                        <template x-if="loading">
                            <i class="fas fa-circle-notch animate-spin"></i>
                        </template>
                    </button>
                    <button type="button" @click="showAddModal = false" class="w-full text-slate-400 text-sm font-bold py-2">Cancel</button>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Monitor Modal -->
    <div x-show="showEditModal" class="fixed inset-0 z-[100] overflow-y-auto" x-cloak x-transition>
        <div class="flex items-center justify-center min-h-screen p-4">
            <div @click="showEditModal = false" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm"></div>
            
            <div class="relative bg-white rounded-[2.5rem] w-full max-w-md p-10 shadow-2xl">
                <h3 class="text-2xl font-bold text-slate-800 mb-2">Edit Monitor</h3>
                <p class="text-slate-500 text-sm mb-8">Update the monitoring configuration for this target.</p>
                
                <form @submit.prevent="updateMonitor()" class="space-y-6">
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">Display Name</label>
                        <input x-model="editTarget.name" type="text" placeholder="e.g. My Portfolio" class="w-full px-5 py-3 rounded-2xl bg-slate-50 border border-slate-200 focus:ring-4 focus:ring-blue-100 focus:border-blue-500 outline-none transition-all font-medium transition-all" required>
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">Target URL</label>
                        <input x-model="editTarget.url" type="text" placeholder="https://example.com" class="w-full px-5 py-3 rounded-2xl bg-slate-50 border border-slate-200 focus:ring-4 focus:ring-blue-100 focus:border-blue-500 outline-none transition-all font-medium transition-all" required>
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">Check Interval (Minutes)</label>
                        <select x-model="editTarget.interval" class="w-full px-5 py-3 rounded-2xl bg-slate-50 border border-slate-200 focus:ring-4 focus:ring-blue-100 focus:border-blue-500 outline-none transition-all font-bold transition-all">
                            <option value="1">Every Minute</option>
                            <option value="5">Every 5 Minutes</option>
                            <option value="15">Every 15 Minutes</option>
                            <option value="60">Hourly</option>
                        </select>
                    </div>
                    <button type="submit" :disabled="loading" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-4 rounded-2xl shadow-xl shadow-blue-200 transition-all active:scale-95 flex items-center justify-center space-x-2">
                        <template x-if="!loading">
                            <span>Save Changes</span>
                        </template>
                        <template x-if="loading">
                            <i class="fas fa-circle-notch animate-spin"></i>
                        </template>
                    </button>
                    <button type="button" @click="showEditModal = false" class="w-full text-slate-400 text-sm font-bold py-2">Cancel</button>
                </form>
            </div>
        </div>
    </div>

    <!-- Core App Script -->
    <script>
        function app() {
            return {
                activePage: 'dashboard',
                pageTitle: 'Engine Dashboard',
                lastSyncTime: 'Initialing...',
                loading: false,
                monitors: [],
                logs: [],
                stats: { total: 0, up: 0, down: 0, avgResponse: 0 },
                showAddModal: false,
                showEditModal: false,
                newTarget: { name: '', url: '', interval: 5 },
                editTarget: { id: null, name: '', url: '', interval: 5 },

                async init() {
                    const savedPage = localStorage.getItem('lastPage');
                    if (savedPage) this.navigate(savedPage);
                    await this.refreshData();
                    
                    // Auto refresh loop (10s)
                    setInterval(() => this.refreshData(), 10000);
                },

                navigate(page) {
                    this.activePage = page;
                    localStorage.setItem('lastPage', page);
                    switch(page) {
                        case 'dashboard': this.pageTitle = 'Engine Dashboard'; break;
                        case 'monitors': this.pageTitle = 'Monitor Targets'; break;
                        case 'logs': this.pageTitle = 'Incident History'; break;
                    }
                },

                async refreshData() {
                    this.loading = true;
                    try {
                        const [monitorsRes, logsRes] = await Promise.all([
                            fetch('api/monitors'),
                            fetch('api/logs')
                        ]);
                        
                        const monitorsData = await monitorsRes.json();
                        const logsData = await logsRes.json();

                        this.monitors = monitorsData.data || [];
                        this.logs = logsData.data || [];
                        
                        this.calculateStats();
                        this.lastSyncTime = new Date().toLocaleTimeString();
                    } catch (e) {
                        console.error("Fetch failed", e);
                        this.lastSyncTime = "Error Syncing";
                    } finally {
                        this.loading = false;
                    }
                },

                calculateStats() {
                    if (this.monitors.length === 0) {
                        this.stats = { total: 0, up: 0, down: 0, avgResponse: 0 };
                        return;
                    }
                    this.stats.total = this.monitors.length;
                    const upCount = this.monitors.filter(m => m.status === 'UP').length;
                    this.stats.up = upCount;
                    this.stats.down = this.monitors.length - upCount;
                    
                    const totalResp = this.monitors.reduce((acc, m) => acc + (parseFloat(m.response_time) || 0), 0);
                    this.stats.avgResponse = Math.round(totalResp / this.monitors.length);
                },

                async addMonitor() {
                    if (!this.newTarget.name || !this.newTarget.url) return alert("Please fill all fields");
                    
                    this.loading = true;
                    try {
                        const res = await fetch('api/monitors', {
                            method: 'POST',
                            headers: { 
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify(this.newTarget)
                        });
                        
                        if (res.ok) {
                            this.newTarget = { name: '', url: '', interval: 5 };
                            this.showAddModal = false;
                            await this.refreshData();
                            this.navigate('monitors');
                        } else {
                            const err = await res.json();
                            let msg = err.message || "Failed to add target";
                            if (err.errors) {
                                msg = Object.values(err.errors).flat().join('\n');
                            }
                            alert("Error: " + msg);
                        }
                    } catch (e) {
                        alert("Network error occurred");
                    } finally {
                        this.loading = false;
                    }
                },

                formatDate(dateString) {
                    if (!dateString) return 'Never';
                    const date = new Date(dateString);
                    return date.toLocaleString();
                },

                openEditModal(monitor) {
                    this.editTarget = {
                        id: monitor.id,
                        name: monitor.name,
                        url: monitor.url,
                        interval: monitor.interval.includes('menit') ? parseInt(monitor.interval) : monitor.interval
                    };
                    this.showEditModal = true;
                },

                async updateMonitor() {
                    if (!this.editTarget.name || !this.editTarget.url) return alert("Please fill all fields");
                    
                    this.loading = true;
                    try {
                        const res = await fetch(`api/monitors/${this.editTarget.id}`, {
                            method: 'PUT',
                            headers: { 
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify(this.editTarget)
                        });
                        
                        if (res.ok) {
                            this.showEditModal = false;
                            await this.refreshData();
                        } else {
                            const err = await res.json();
                            let msg = err.error || err.message || "Failed to update target";
                            if (err.errors) {
                                msg = Object.values(err.errors).flat().join('\n');
                            }
                            alert("Error: " + msg);
                        }
                    } catch (e) {
                        alert("Network error occurred");
                    } finally {
                        this.loading = false;
                    }
                },

                async deleteMonitor(id) {
                    if (!confirm('Are you sure you want to delete this monitoring target? This action cannot be undone.')) return;
                    
                    this.loading = true;
                    try {
                        const res = await fetch(`api/monitors/${id}`, {
                            method: 'DELETE',
                            headers: { 
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json'
                            }
                        });
                        
                        if (res.ok) {
                            await this.refreshData();
                        } else {
                            const err = await res.json();
                            alert("Error: " + (err.error || "Failed to delete target"));
                        }
                    } catch (e) {
                        alert("Network error occurred");
                    } finally {
                        this.loading = false;
                    }
                },

                async deleteLog(id) {
                    if (!confirm('Are you sure you want to delete this log entry?')) return;
                    
                    this.loading = true;
                    try {
                        const res = await fetch(`api/logs/${id}`, {
                            method: 'DELETE',
                            headers: { 
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json'
                            }
                        });
                        
                        if (res.ok) {
                            await this.refreshData();
                        } else {
                            const err = await res.json();
                            alert("Error: " + (err.message || "Failed to delete log"));
                        }
                    } catch (e) {
                        alert("Network error occurred");
                    } finally {
                        this.loading = false;
                    }
                },

                async clearLogs() {
                    const confirm1 = confirm('WARNING: You are about to DELETE ALL activity history.');
                    if (!confirm1) return;
                    const confirm2 = confirm('Are you REALLY sure? This action cannot be undone.');
                    if (!confirm2) return;
                    
                    this.loading = true;
                    try {
                        const res = await fetch(`api/logs/clear`, {
                            method: 'DELETE',
                            headers: { 
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json'
                            }
                        });
                        
                        if (res.ok) {
                            await this.refreshData();
                            alert("History cleared successfully");
                        } else {
                            const err = await res.json();
                            alert("Error: " + (err.message || "Failed to clear history"));
                        }
                    } catch (e) {
                        alert("Network error occurred");
                    } finally {
                        this.loading = false;
                    }
                }
            }
        }
    </script>
</body>
</html>
