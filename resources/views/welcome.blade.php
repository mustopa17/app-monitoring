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
        .custom-scrollbar::-webkit-scrollbar { width: 5px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: #f1f5f9; border-radius: 10px; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
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
            <button @click="navigate('monitors')" :class="activePage === 'monitors' ? 'sidebar-item-active text-white' : 'hover:bg-slate-800 hover:text-white'" class="w-full flex items-center space-x-3 p-3 rounded-xl transition-all" x-show="hasPermission('monitor.view')">
                <i class="fas fa-desktop w-6"></i>
                <span class="font-medium">Websites</span>
            </button>
            <button @click="navigate('logs')" :class="activePage === 'logs' ? 'sidebar-item-active text-white' : 'hover:bg-slate-800 hover:text-white'" class="w-full flex items-center space-x-3 p-3 rounded-xl transition-all" x-show="hasPermission('log.view')">
                <i class="fas fa-list-ul w-6"></i>
                <span class="font-medium">Activity Logs</span>
            </button>
            <button x-show="hasPermission('user.view')" @click="navigate('users')" :class="activePage === 'users' ? 'sidebar-item-active text-white' : 'hover:bg-slate-800 hover:text-white'" class="w-full flex items-center space-x-3 p-3 rounded-xl transition-all">
                <i class="fas fa-users w-6"></i>
                <span class="font-medium">User Management</span>
            </button>
            <button x-show="hasPermission('role.view')"
                    @click="navigate('roles')"
                    :class="activePage === 'roles' ? 'sidebar-item-active text-white' : 'hover:bg-slate-800 hover:text-white'"
                    class="w-full flex items-center space-x-3 p-3 rounded-xl transition-all">
                <i class="fas fa-shield-halved w-6"></i>
                <span class="font-medium">Role Management</span>
            </button>
        </nav>

        
        <div class="absolute bottom-6 left-6 right-6">
            <div class="flex items-center space-x-3 p-4 bg-slate-800/50 rounded-2xl border border-slate-700/50 backdrop-blur-sm">
                <div class="w-10 h-10 rounded-full bg-gradient-to-tr from-blue-500 to-indigo-600 flex items-center justify-center text-white font-bold shadow-lg border-2 border-slate-700" x-text="profile.name.charAt(0).toUpperCase()">
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-bold text-white truncate" x-text="profile.name"></p>
                    <p class="text-[10px] text-blue-400 font-bold uppercase tracking-wider" x-text="profile.role.replace('_', ' ')"></p>
                </div>
                <button @click="logout()" class="p-2 text-slate-500 hover:text-rose-500 transition-all hover:bg-rose-500/10 rounded-xl" title="Logout">
                    <i class="fas fa-sign-out-alt"></i>
                </button>
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
                        <button x-show="hasPermission('monitor.create')"
                                @click="showAddModal = true" class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2.5 rounded-xl font-bold shadow-lg shadow-blue-200 flex items-center space-x-2 transition-all active:scale-95">
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
                                    <th x-show="hasPermission('monitor.edit') || hasPermission('monitor.delete')" class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider text-center">Actions</th>
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
                                                <button x-show="hasPermission('monitor.edit')" @click="openEditModal(m)" class="p-2 text-blue-500 hover:bg-blue-50 rounded-lg transition-colors" title="Edit">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <button x-show="hasPermission('monitor.delete')" @click="deleteMonitor(m.id)" class="p-2 text-rose-500 hover:bg-rose-50 rounded-lg transition-colors" title="Delete">
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
                            <button x-show="hasPermission('log.export')" @click="exportLogs()" class="bg-blue-50 text-blue-600 hover:bg-blue-100 px-4 py-2 rounded-xl font-bold text-sm transition-all border border-blue-100 flex items-center space-x-2">
                                <i class="fas fa-file-csv"></i>
                                <span>Export CSV</span>
                            </button>
                            <button x-show="hasPermission('log.clear')" @click="clearLogs()" class="bg-rose-50 text-rose-600 hover:bg-rose-100 px-4 py-2 rounded-xl font-bold text-sm transition-all border border-rose-100 flex items-center space-x-2">
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
                                    <th x-show="hasPermission('log.clear')" class="px-6 py-4 text-xs font-bold text-slate-500 uppercase text-center">Actions</th>
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
                                        <td x-show="hasPermission('log.clear')" class="px-6 py-4 text-center">
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

            <!-- User Management Page -->
            <template x-if="activePage === 'users'">
                <div class="space-y-8 animate-fadeIn">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-2xl font-bold text-slate-800">User Management</h3>
                            <p class="text-slate-500">Manage system users and their roles</p>
                        </div>
                        <button x-show="hasPermission('user.create')" @click="showUserModal = true" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-2xl font-bold shadow-lg shadow-blue-200 transition-all active:scale-95 flex items-center space-x-2">
                            <i class="fas fa-plus"></i>
                            <span>Tambah User</span>
                        </button>
                    </div>

                    <div class="bg-white rounded-[2.5rem] shadow-sm border border-slate-100 overflow-hidden">
                        <table class="w-full text-left">
                            <thead class="bg-slate-50 border-b border-slate-100">
                                <tr>
                                    <th class="px-6 py-4 text-xs font-bold text-slate-400 uppercase tracking-widest">Nama</th>
                                    <th class="px-6 py-4 text-xs font-bold text-slate-400 uppercase tracking-widest">Email</th>
                                    <th class="px-6 py-4 text-xs font-bold text-slate-400 uppercase tracking-widest text-center">Role</th>
                                    <th class="px-6 py-4 text-xs font-bold text-slate-400 uppercase tracking-widest text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-50">
                                <template x-for="user in users" :key="user.id">
                                    <tr class="hover:bg-slate-50 transition-colors">
                                        <td class="px-6 py-4">
                                            <div class="flex items-center space-x-3">
                                                <div class="w-8 h-8 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center font-bold text-xs" x-text="user.name.charAt(0).toUpperCase()"></div>
                                                <span class="font-bold text-slate-700" x-text="user.name"></span>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 text-slate-500 text-sm" x-text="user.email"></td>
                                        <td class="px-6 py-4 text-center">
                                            <span :class="user.role === 'super_admin' ? 'bg-rose-100 text-rose-600' : 'bg-blue-100 text-blue-600'" 
                                                  class="px-3 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider" 
                                                  x-text="user.role.replace('_', ' ')"></span>
                                        </td>
                                        <td class="px-6 py-4 text-center space-x-2">
                                            <button x-show="hasPermission('user.edit')" @click="openEditUserModal(user)" class="p-2 text-blue-500 hover:bg-blue-50 rounded-lg transition-colors" title="Edit User">
                                                <i class="fas fa-edit text-xs"></i>
                                            </button>
                                            <button x-show="hasPermission('user.delete')" @click="deleteUser(user.id)" class="p-2 text-rose-500 hover:bg-rose-50 rounded-lg transition-colors" title="Hapus User">
                                                <i class="fas fa-trash-alt text-xs"></i>
                                            </button>
                                        </td>
                                    </tr>
                                </template>
                                <template x-if="users.length === 0">
                                    <tr>
                                        <td colspan="4" class="px-6 py-12 text-center text-slate-400 italic">No users found.</td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>
                </div>
            </template>

            <!-- Role Management Page -->
            <template x-if="activePage === 'roles'">
                <div class="space-y-8 animate-fadeIn">
                    <!-- Header -->
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-2xl font-bold text-slate-800">Role Management</h3>
                            <p class="text-slate-500">Kelola role dan permission yang tersedia di sistem</p>
                        </div>
                        <button x-show="hasPermission('role.create')" @click="showAddRoleModal = true"
                                class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-2xl font-bold shadow-lg shadow-blue-200 transition-all active:scale-95 flex items-center space-x-2">
                            <i class="fas fa-plus"></i>
                            <span>Tambah Role</span>
                        </button>
                    </div>

                    <!-- Tabel Role -->
                    <div class="bg-white rounded-[2.5rem] shadow-sm border border-slate-100 overflow-hidden">
                        <table class="w-full text-left">
                            <thead class="bg-slate-50 border-b border-slate-100">
                                <tr>
                                    <th class="px-6 py-4 text-xs font-bold text-slate-400 uppercase tracking-widest">Nama Role</th>
                                    <th class="px-6 py-4 text-xs font-bold text-slate-400 uppercase tracking-widest">Deskripsi</th>
                                    <th class="px-6 py-4 text-xs font-bold text-slate-400 uppercase tracking-widest text-center">Jumlah User</th>
                                    <th class="px-6 py-4 text-xs font-bold text-slate-400 uppercase tracking-widest">Permission</th>
                                    <th class="px-6 py-4 text-xs font-bold text-slate-400 uppercase tracking-widest text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-50">
                                <template x-for="role in roles" :key="role.id">
                                    <tr class="hover:bg-slate-50 transition-colors">
                                        <td class="px-6 py-4">
                                            <span class="font-bold uppercase tracking-wider"
                                                  :class="role.name === 'super_admin' ? 'text-rose-600' : role.name === 'admin' ? 'text-blue-600' : 'text-slate-600'"
                                                  x-text="role.name.replace('_', ' ')">
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 text-slate-500 text-sm" x-text="role.description"></td>
                                        <td class="px-6 py-4 text-center">
                                            <span class="bg-slate-100 text-slate-600 px-3 py-1 rounded-full text-xs font-bold"
                                                  x-text="role.user_count + ' user'">
                                            </span>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="flex flex-wrap gap-1">
                                                <template x-for="perm in role.permissions.slice(0, 3)" :key="perm">
                                                    <span class="bg-blue-50 text-blue-600 px-2 py-0.5 rounded text-[10px] font-bold" x-text="perm.replace(/_/g, ' ')"></span>
                                                </template>
                                                <template x-if="role.permissions.length > 3">
                                                    <span class="bg-slate-100 text-slate-500 px-2 py-0.5 rounded text-[10px] font-bold" x-text="'+' + (role.permissions.length - 3) + ' more'"></span>
                                                </template>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 text-center space-x-2">
                                            <button x-show="hasPermission('role.edit')" @click="openEditRoleModal(role)"
                                                    class="p-2 text-blue-500 hover:bg-blue-50 rounded-lg transition-colors"
                                                    title="Edit Role">
                                                <i class="fas fa-edit text-xs"></i>
                                            </button>
                                            <button x-show="hasPermission('role.delete')" @click="deleteRole(role)"
                                                    :disabled="role.name === 'super_admin'"
                                                    :class="role.name === 'super_admin' ? 'opacity-30 cursor-not-allowed text-slate-400' : 'hover:bg-rose-50 text-rose-500'"
                                                    class="p-2 rounded-lg transition-colors"
                                                    title="Hapus Role">
                                                <i class="fas fa-trash-alt text-xs"></i>
                                            </button>
                                        </td>
                                    </tr>
                                </template>
                                <template x-if="roles.length === 0">
                                    <tr>
                                        <td colspan="5" class="px-6 py-12 text-center text-slate-400 italic">Tidak ada role tersedia.</td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>
                </div>
            </template>


        </main>
    </div>

    <!-- Monitor Modals -->
    <div x-show="showAddModal" class="fixed inset-0 z-[100] overflow-y-auto" x-cloak x-transition>
        <div class="flex items-center justify-center min-h-screen p-4">
            <div @click="showAddModal = false" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm"></div>
            <div class="relative bg-white rounded-[2.5rem] w-full max-w-md p-10 shadow-2xl">
                <h3 class="text-2xl font-bold text-slate-800 mb-2">Add New Target</h3>
                <form @submit.prevent="addMonitor()" class="space-y-4">
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-1">Friendly Name</label>
                        <input x-model="newTarget.name" type="text" class="w-full px-5 py-3 rounded-2xl bg-slate-50 border border-slate-200 outline-none" placeholder="My Awesome Website" required>
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-1">Target URL</label>
                        <input x-model="newTarget.url" type="text" class="w-full px-5 py-3 rounded-2xl bg-slate-50 border border-slate-200 outline-none" placeholder="google.com" required>
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-1">Check Interval (Minutes)</label>
                        <input x-model="newTarget.interval" type="number" class="w-full px-5 py-3 rounded-2xl bg-slate-50 border border-slate-200 outline-none" min="1" required>
                    </div>
                    <button type="submit" class="w-full bg-blue-600 text-white font-bold py-4 rounded-2xl shadow-lg">Start Monitoring</button>
                    <button type="button" @click="showAddModal = false" class="w-full text-slate-400 text-sm font-bold">Cancel</button>
                </form>
            </div>
        </div>
    </div>

    <div x-show="showEditModal" class="fixed inset-0 z-[100] overflow-y-auto" x-cloak x-transition>
        <div class="flex items-center justify-center min-h-screen p-4">
            <div @click="showEditModal = false" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm"></div>
            <div class="relative bg-white rounded-[2.5rem] w-full max-w-md p-10 shadow-2xl">
                <h3 class="text-2xl font-bold text-slate-800 mb-2">Edit Target</h3>
                <form @submit.prevent="updateMonitor()" class="space-y-4">
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-1">Friendly Name</label>
                        <input x-model="editTarget.name" type="text" class="w-full px-5 py-3 rounded-2xl bg-slate-50 border border-slate-200 outline-none" required>
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-1">Target URL</label>
                        <input x-model="editTarget.url" type="text" class="w-full px-5 py-3 rounded-2xl bg-slate-50 border border-slate-200 outline-none" required>
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-1">Check Interval (Minutes)</label>
                        <input x-model="editTarget.interval" type="number" class="w-full px-5 py-3 rounded-2xl bg-slate-50 border border-slate-200 outline-none" min="1" required>
                    </div>
                    <button type="submit" class="w-full bg-blue-600 text-white font-bold py-4 rounded-2xl shadow-lg">Save Changes</button>
                    <button type="button" @click="showEditModal = false" class="w-full text-slate-400 text-sm font-bold">Cancel</button>
                </form>
            </div>
        </div>
    </div>

    <!-- Role Modals -->
    <div x-show="showAddRoleModal" class="fixed inset-0 z-[100] overflow-y-auto" x-cloak x-transition>
        <div class="flex items-center justify-center min-h-screen p-4">
            <div @click="showAddRoleModal = false" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm"></div>
            <div class="relative bg-white rounded-[2.5rem] w-full max-w-md p-10 shadow-2xl">
                <h3 class="text-2xl font-bold text-slate-800 mb-1">Tambah Role Baru</h3>
                <p class="text-slate-400 text-sm mb-6">Isi detail role dan pilih permission yang sesuai.</p>
                <form @submit.prevent="addRole()" class="space-y-5">
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-1">Nama Role</label>
                        <input x-model="newRole.name" type="text"
                               class="w-full px-5 py-3 rounded-2xl bg-slate-50 border border-slate-200 outline-none"
                               placeholder="contoh: EDITOR, VIEWER" required>
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-1">Deskripsi</label>
                        <input x-model="newRole.description" type="text"
                               class="w-full px-5 py-3 rounded-2xl bg-slate-50 border border-slate-200 outline-none"
                               placeholder="contoh: Bisa lihat dan edit monitor" required>
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-3">Permission</label>
                        <div class="space-y-4 max-h-60 overflow-y-auto pr-2 custom-scrollbar">
                            <template x-for="(perms, category) in groupedPermissions()" :key="category">
                                <div class="space-y-2">
                                    <h4 class="text-[10px] font-bold text-slate-400 uppercase tracking-widest border-b border-slate-100 pb-1 mt-2" x-text="category"></h4>
                                    <div class="grid grid-cols-1 gap-1">
                                        <template x-for="perm in perms" :key="perm.key">
                                            <label class="flex items-center space-x-3 cursor-pointer hover:bg-white p-2 rounded-xl transition-all border border-transparent hover:border-slate-100">
                                                <input type="checkbox" :value="perm.key" x-model="newRole.permissions"
                                                       class="w-4 h-4 accent-blue-600 rounded border-slate-300">
                                                <div class="flex flex-col">
                                                    <span class="text-sm font-bold text-slate-700" x-text="perm.label"></span>
                                                    <span class="text-[10px] text-slate-500 leading-tight" x-text="perm.description"></span>
                                                    <span class="text-[8px] text-slate-400 font-mono mt-0.5" x-text="perm.key"></span>
                                                </div>
                                            </label>
                                        </template>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>
                    <button type="submit" class="w-full bg-blue-600 text-white font-bold py-4 rounded-2xl shadow-lg hover:bg-blue-700 transition-all">Simpan Role</button>
                    <button type="button" @click="showAddRoleModal = false" class="w-full text-slate-400 text-sm font-bold">Batal</button>
                </form>
            </div>
        </div>
    </div>

    <div x-show="showEditRoleModal" class="fixed inset-0 z-[100] overflow-y-auto" x-cloak x-transition>
        <div class="flex items-center justify-center min-h-screen p-4">
            <div @click="showEditRoleModal = false" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm"></div>
            <div class="relative bg-white rounded-[2.5rem] w-full max-w-md p-10 shadow-2xl">
                <h3 class="text-2xl font-bold text-slate-800 mb-1">Edit Role</h3>
                <p class="text-slate-400 text-sm mb-6">Ubah detail atau permission untuk role ini.</p>
                <form @submit.prevent="updateRole()" class="space-y-5">
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-1">Nama Role</label>
                        <input x-model="editRoleObj.name" type="text"
                               class="w-full px-5 py-3 rounded-2xl bg-slate-50 border border-slate-200 outline-none"
                               :disabled="editRoleObj.name === 'super_admin'" required>
                        <p x-show="editRoleObj.name === 'super_admin'" class="text-xs text-rose-400 mt-1 font-medium">
                            Nama role super_admin tidak bisa diubah.
                        </p>
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-1">Deskripsi</label>
                        <input x-model="editRoleObj.description" type="text"
                               class="w-full px-5 py-3 rounded-2xl bg-slate-50 border border-slate-200 outline-none" required>
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-3">Permission</label>
                        <div class="space-y-4 max-h-60 overflow-y-auto pr-2 custom-scrollbar">
                            <template x-for="(perms, category) in groupedPermissions()" :key="category">
                                <div class="space-y-2">
                                    <h4 class="text-[10px] font-bold text-slate-400 uppercase tracking-widest border-b border-slate-100 pb-1 mt-2" x-text="category"></h4>
                                    <div class="grid grid-cols-1 gap-1">
                                        <template x-for="perm in perms" :key="perm.key">
                                            <label class="flex items-center space-x-3 cursor-pointer hover:bg-white p-2 rounded-xl transition-all border border-transparent hover:border-slate-100">
                                                <input type="checkbox" :value="perm.key" x-model="editRoleObj.permissions"
                                                       class="w-4 h-4 accent-blue-600 rounded border-slate-300">
                                                <div class="flex flex-col">
                                                    <span class="text-sm font-bold text-slate-700" x-text="perm.label"></span>
                                                    <span class="text-[10px] text-slate-500 leading-tight" x-text="perm.description"></span>
                                                    <span class="text-[8px] text-slate-400 font-mono mt-0.5" x-text="perm.key"></span>
                                                </div>
                                            </label>
                                        </template>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>
                    <button type="submit" class="w-full bg-blue-600 text-white font-bold py-4 rounded-2xl shadow-lg hover:bg-blue-700 transition-all">Simpan Perubahan</button>
                    <button type="button" @click="showEditRoleModal = false" class="w-full text-slate-400 text-sm font-bold">Batal</button>
                </form>
            </div>
        </div>
    </div>

    <!-- User Modals -->
    <div x-show="showUserModal" class="fixed inset-0 z-[100] overflow-y-auto" x-cloak x-transition>
        <div class="flex items-center justify-center min-h-screen p-4">
            <div @click="showUserModal = false" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm"></div>
            <div class="relative bg-white rounded-[2.5rem] w-full max-w-md p-10 shadow-2xl">
                <h3 class="text-2xl font-bold text-slate-800 mb-2">Tambah User Baru</h3>
                <form @submit.prevent="addUser()" class="space-y-4">
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-1">Nama</label>
                        <input x-model="newUser.name" type="text" class="w-full px-5 py-3 rounded-2xl bg-slate-50 border border-slate-200 outline-none" required>
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-1">Email</label>
                        <input x-model="newUser.email" type="email" class="w-full px-5 py-3 rounded-2xl bg-slate-50 border border-slate-200 outline-none" required>
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-1">Password</label>
                        <input x-model="newUser.password" type="password" class="w-full px-5 py-3 rounded-2xl bg-slate-50 border border-slate-200 outline-none" required>
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-1">Role</label>
                        <select x-model="newUser.role" class="w-full px-5 py-3 rounded-2xl bg-slate-50 border border-slate-200 outline-none font-bold">
                            <template x-for="role in roles" :key="role.id">
                                <option :value="role.name" x-text="role.name.replace('_', ' ').toUpperCase()"></option>
                            </template>
                        </select>
                    </div>
                    <button type="submit" class="w-full bg-blue-600 text-white font-bold py-4 rounded-2xl shadow-lg">Simpan User</button>
                    <button type="button" @click="showUserModal = false" class="w-full text-slate-400 text-sm font-bold">Batal</button>
                </form>
            </div>
        </div>
    </div>

    <div x-show="showEditUserModal" class="fixed inset-0 z-[100] overflow-y-auto" x-cloak x-transition>
        <div class="flex items-center justify-center min-h-screen p-4">
            <div @click="showEditUserModal = false" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm"></div>
            <div class="relative bg-white rounded-[2.5rem] w-full max-w-md p-10 shadow-2xl">
                <h3 class="text-2xl font-bold text-slate-800 mb-2">Edit User</h3>
                <form @submit.prevent="updateUser()" class="space-y-4">
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-1">Nama</label>
                        <input x-model="editUserObj.name" type="text" class="w-full px-5 py-3 rounded-2xl bg-slate-50 border border-slate-200 outline-none" required>
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-1">Email</label>
                        <input x-model="editUserObj.email" type="email" class="w-full px-5 py-3 rounded-2xl bg-slate-50 border border-slate-200 outline-none" required>
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-1">Password (Kosongkan jika tidak ganti)</label>
                        <input x-model="editUserObj.password" type="password" class="w-full px-5 py-3 rounded-2xl bg-slate-50 border border-slate-200 outline-none">
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-1">Role</label>
                        <select x-model="editUserObj.role" class="w-full px-5 py-3 rounded-2xl bg-slate-50 border border-slate-200 outline-none font-bold">
                            <template x-for="role in roles" :key="role.id">
                                <option :value="role.name" x-text="role.name.replace('_', ' ').toUpperCase()"></option>
                            </template>
                        </select>
                    </div>
                    <button type="submit" class="w-full bg-blue-600 text-white font-bold py-4 rounded-2xl shadow-lg">Update User</button>
                    <button type="button" @click="showEditUserModal = false" class="w-full text-slate-400 text-sm font-bold">Batal</button>
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
                users: [],
                stats: { total: 0, up: 0, down: 0, avgResponse: 0 },
                showAddModal: false,
                showEditModal: false,
                showUserModal: false,
                showEditUserModal: false,
                newTarget: { name: '', url: '', interval: 5 },
                editTarget: { id: null, name: '', url: '', interval: 5 },
                newUser: { name: '', email: '', password: '', role: 'user' },
                editUserObj: { id: null, name: '', email: '', password: '', role: 'user' },
                profile: { name: '...', role: '...', permissions: [] },

                // === State untuk Role Management ===
                showAddRoleModal: false,
                showEditRoleModal: false,
                newRole: { name: '', description: '', permissions: [] },
                editRoleObj: { id: null, name: '', description: '', permissions: [] },

                // Data DUMMY role
                roles: [],

                // Daftar semua permission yang tersedia
                allPermissions: [],

                canAccess(roles) {
                    if (!this.profile || !this.profile.role) return false;
                    const userRole = this.profile.role.toLowerCase();
                    if (Array.isArray(roles)) {
                        return roles.map(r => r.toLowerCase()).includes(userRole);
                    }
                    return userRole === roles.toLowerCase();
                },

                hasPermission(permission) {
                    if (!this.profile || !this.profile.permissions) return false;
                    return this.profile.permissions.includes(permission);
                },

                groupedPermissions() {
                    const groups = {};
                    this.allPermissions.forEach(perm => {
                        const category = perm.key.split('.')[0] || 'other';
                        if (!groups[category]) groups[category] = [];
                        groups[category].push(perm);
                    });
                    return groups;
                },



                async init() {
                    const savedPage = localStorage.getItem('lastPage');
                    if (savedPage) this.navigate(savedPage);
                    await this.fetchProfile();
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
                        case 'users': this.pageTitle = 'User Management'; break;
                        case 'roles': this.pageTitle = 'Role Management'; break;
                    }
                },


                async refreshData() {
                    this.loading = true;
                    try {
                        const headers = {
                            'Authorization': `Bearer ${localStorage.getItem('auth_token')}`,
                            'Accept': 'application/json'
                        };

                        // Ambil Monitor & Logs (Bisa diakses semua role)
                        const [monitorsRes, logsRes] = await Promise.all([
                            fetch('/api/monitors', { headers }),
                            fetch('/api/logs', { headers })
                        ]);

                        if (monitorsRes.ok) {
                            const data = await monitorsRes.json();
                            this.monitors = data.data || [];
                        }

                        if (logsRes.ok) {
                            const data = await logsRes.json();
                            this.logs = data.data || [];
                        }

                        // Ambil daftar user & roles jika punya izin
                        // Ambil daftar user jika punya izin
                        if (this.hasPermission('user.view')) {
                            const usersRes = await fetch('/api/users', { headers });
                            if (usersRes.ok) {
                                const data = await usersRes.json();
                                this.users = data.data || [];
                            }
                        }

                        // Ambil daftar roles jika punya izin
                        if (this.hasPermission('role.view')) {
                            // Fetch roles
                            const rolesRes = await fetch('/api/roles', { headers });
                            if (rolesRes.ok) {
                                const data = await rolesRes.json();
                                this.roles = data.data || [];
                            }

                            // Fetch permissions
                            const permRes = await fetch('/api/permissions', { headers });
                            if (permRes.ok) {
                                const data = await permRes.json();
                                this.allPermissions = data.data || [];
                            }
                        }
                        
                        this.calculateStats();
                        this.lastSyncTime = new Date().toLocaleTimeString();
                    } catch (e) {
                        console.error("Fetch failed", e);
                        this.lastSyncTime = "Error Syncing";
                    } finally {
                        this.loading = false;
                    }
                },

                async fetchProfile() {
                    try {
                        const res = await fetch('/api/user', {
                            headers: {
                                'Authorization': `Bearer ${localStorage.getItem('auth_token')}`,
                                'Accept': 'application/json'
                            }
                        });
                        if (res.ok) {
                            this.profile = await res.json();
                        } else if (res.status === 401) {
                            window.location.href = '/login';
                        }
                    } catch (e) {
                        console.error("Profile fetch failed", e);
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
                        const res = await fetch('/api/monitors', {
                            method: 'POST',
                            headers: { 
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Authorization': `Bearer ${localStorage.getItem('auth_token')}`,
                                'Accept': 'application/json'
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
                        const res = await fetch(`/api/monitors/${this.editTarget.id}`, {
                            method: 'PUT',
                            headers: { 
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json',
                                'Authorization': `Bearer ${localStorage.getItem('auth_token')}`
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
                        const res = await fetch(`/api/monitors/${id}`, {
                            method: 'DELETE',
                            headers: { 
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json',
                                'Authorization': `Bearer ${localStorage.getItem('auth_token')}`
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
                        const res = await fetch(`/api/logs/${id}`, {
                            method: 'DELETE',
                            headers: { 
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json',
                                'Authorization': `Bearer ${localStorage.getItem('auth_token')}`
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
                        const res = await fetch(`/api/logs/clear`, {
                            method: 'DELETE',
                            headers: { 
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json',
                                'Authorization': `Bearer ${localStorage.getItem('auth_token')}`
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
                },

                async exportLogs() {
                    this.loading = true;
                    try {
                        const res = await fetch('/api/logs/export', {
                            headers: { 
                                'Authorization': `Bearer ${localStorage.getItem('auth_token')}`,
                                'Accept': 'text/csv'
                            }
                        });
                        
                        if (res.ok) {
                            const blob = await res.blob();
                            const url = window.URL.createObjectURL(blob);
                            const a = document.createElement('a');
                            a.href = url;
                            a.download = `monitor_logs_${new Date().getTime()}.csv`;
                            document.body.appendChild(a);
                            a.click();
                            window.URL.revokeObjectURL(url);
                        } else {
                            alert("Export failed: Unauthorised or server error");
                        }
                    } catch (e) {
                        alert("Network error during export");
                    } finally {
                        this.loading = false;
                    }
                },

                async addUser() {
                    this.loading = true;
                    try {
                        const res = await fetch('/api/users', {
                            method: 'POST',
                            headers: { 
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json',
                                'Authorization': `Bearer ${localStorage.getItem('auth_token')}`
                            },
                            body: JSON.stringify(this.newUser)
                        });
                        
                        if (res.ok) {
                            this.showUserModal = false;
                            this.newUser = { name: '', email: '', password: '', role: 'user' };
                            await this.refreshData();
                        } else {
                            const err = await res.json();
                            alert("Error: " + (err.error || "Failed to add user"));
                        }
                    } catch (e) {
                        alert("Network error");
                    } finally {
                        this.loading = false;
                    }
                },

                openEditUserModal(user) {
                    this.editUserObj = { ...user, password: '' };
                    this.showEditUserModal = true;
                },

                async updateUser() {
                    this.loading = true;
                    try {
                        const res = await fetch(`/api/users/${this.editUserObj.id}`, {
                            method: 'PUT',
                            headers: { 
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json',
                                'Authorization': `Bearer ${localStorage.getItem('auth_token')}`
                            },
                            body: JSON.stringify(this.editUserObj)
                        });
                        
                        if (res.ok) {
                            this.showEditUserModal = false;
                            await this.refreshData();
                        } else {
                            const err = await res.json();
                            alert("Error: " + (err.error || "Failed to update user"));
                        }
                    } catch (e) {
                        alert("Network error");
                    } finally {
                        this.loading = false;
                    }
                },

                async deleteUser(id) {
                    if (!confirm('Hapus user ini?')) return;
                    this.loading = true;
                    try {
                        const res = await fetch(`/api/users/${id}`, {
                            method: 'DELETE',
                            headers: { 
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json',
                                'Authorization': `Bearer ${localStorage.getItem('auth_token')}`
                            }
                        });
                        if (res.ok) await this.refreshData();
                    } catch (e) {
                        alert("Network error");
                    } finally {
                        this.loading = false;
                    }
                },


                // ===== Role Management Functions =====

                async addRole() {
                    this.loading = true;
                    try {
                        const res = await fetch('/api/roles', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'Authorization': `Bearer ${localStorage.getItem('auth_token')}`,
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify(this.newRole)
                        });
                        const data = await res.json();
                        if (res.ok) {
                            this.roles.push(data.data);
                            this.newRole = { name: '', description: '', permissions: [] };
                            this.showAddRoleModal = false;
                            alert('Role berhasil ditambahkan!');
                        } else {
                            alert('Gagal: ' + (data.message || JSON.stringify(data.errors)));
                        }
                    } catch (e) {
                        alert('Network error saat menambah role');
                    } finally {
                        this.loading = false;
                    }
                },

                openEditRoleModal(role) {
                    this.editRoleObj = {
                        id: role.id,
                        name: role.name,
                        description: role.description,
                        permissions: [...role.permissions]
                    };
                    this.showEditRoleModal = true;
                },

                async updateRole() {
                    this.loading = true;
                    try {
                        const res = await fetch(`/api/roles/${this.editRoleObj.id}`, {
                            method: 'PUT',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'Authorization': `Bearer ${localStorage.getItem('auth_token')}`,
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify(this.editRoleObj)
                        });
                        const data = await res.json();
                        if (res.ok) {
                            const index = this.roles.findIndex(r => r.id === this.editRoleObj.id);
                            if (index !== -1) this.roles[index] = data.data;
                            this.showEditRoleModal = false;
                            alert('Role berhasil diperbarui!');
                        } else {
                            alert('Gagal: ' + (data.message || data.error));
                        }
                    } catch (e) {
                        alert('Network error saat update role');
                    } finally {
                        this.loading = false;
                    }
                },

                async deleteRole(role) {
                    if (role.name === 'super_admin') {
                        alert('Role super_admin tidak bisa dihapus karena bersifat sistem.');
                        return;
                    }
                    if (role.user_count > 0) {
                        alert(`Role "${role.name}" tidak bisa dihapus karena masih digunakan oleh ${role.user_count} user.`);
                        return;
                    }
                    const confirmed = confirm(`Apakah yakin ingin menghapus role "${role.name}"?\n\nAksi ini tidak bisa dibatalkan.`);
                    if (!confirmed) return;

                    this.loading = true;
                    try {
                        const res = await fetch(`/api/roles/${role.id}`, {
                            method: 'DELETE',
                            headers: {
                                'Accept': 'application/json',
                                'Authorization': `Bearer ${localStorage.getItem('auth_token')}`,
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            }
                        });
                        const data = await res.json();
                        if (res.ok) {
                            this.roles = this.roles.filter(r => r.id !== role.id);
                            alert('Role berhasil dihapus.');
                        } else {
                            alert('Gagal: ' + (data.error || data.message));
                        }
                    } catch (e) {
                        alert('Network error saat hapus role');
                    } finally {
                        this.loading = false;
                    }
                },


                async logout() {
                    if (!confirm('Are you sure you want to logout?')) return;
                    
                    this.loading = true;
                    try {
                        await fetch('/api/logout', {
                            method: 'POST',
                            headers: { 
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Authorization': `Bearer ${localStorage.getItem('auth_token')}`
                            }
                        });
                    } catch (e) {
                        console.error("Logout API failed, clearing local storage anyway");
                    } finally {
                        localStorage.removeItem('auth_token');
                        window.location.href = '/login';
                    }
                }
            }
        }

    </script>
</body>
</html>
