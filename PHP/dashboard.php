<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
define('SITE_NAME', 'SKYREC');

error_log("Sessão iniciada. admin_logged_in: " . (isset($_SESSION['admin_logged_in']) ? 'true' : 'false'));

if (!isset($_SESSION['admin_logged_in']) || !$_SESSION['admin_logged_in']) {
    error_log("Redirecionando para index.php - Usuário não autenticado");
    header('Location: index.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="pt-BR" id="htmlElement">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard admin - <?= SITE_NAME ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">
    <script>
       
        (function() {
            const savedTheme = localStorage.getItem('theme') || 'dark';
            document.documentElement.setAttribute('data-theme', savedTheme);
            document.documentElement.classList.add(savedTheme);
        })();
    </script>
    <style>
        :root {
            --sidebar-bg: #ffffff;
            --sidebar-text: #374151;
            --main-bg: #f9fafb;
            --card-bg: #ffffff;
            --text-primary: #111827;
            --text-secondary: #6b7280;
            --border-color: #e5e7eb;
            --table-header-bg: #f9fafb;
            --client-panel-bg: #f3f4f6;
            --input-bg: #ffffff;
            --input-text: #111827;
        }

        [data-theme="dark"] {
            --sidebar-bg: #1f2937;
            --sidebar-text: #d1d5db;
            --main-bg: #111827;
            --card-bg: #1f2937;
            --text-primary: #f9fafb;
            --text-secondary: #9ca3af;
            --border-color: #374151;
            --table-header-bg: #374151;
            --client-panel-bg: #374151;
            --input-bg: #374151;
            --input-text: #f9fafb;
        }

        html, body {
            height: 100%;
            margin: 0;
            background-color: var(--main-bg);
            color: var(--text-primary);
            transition: all 0.3s ease;
            overflow: hidden;
        }

        .main-container {
            display: flex;
            flex-direction: column;
            height: 100vh;
            width: 100vw;
            overflow: hidden;
        }

        .content-container {
            flex: 1;
            display: flex;
            width: 100%;
            overflow: hidden;
        }

        .sidebar {
            background-color: var(--sidebar-bg);
            color: var(--sidebar-text);
            border-right: 1px solid var(--border-color);
            transition: all 0.3s ease;
            width: 260px;
            flex-shrink: 0;
            transform: translateX(0);
        }

        .sidebar.hidden {
            transform: translateX(-100%);
        }

        .card {
            background-color: var(--card-bg);
            border: 1px solid var(--border-color);
            border-radius: 12px;
            transition: all 0.3s ease;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        }

        .text-primary {
            color: var(--text-primary);
        }

        .text-secondary {
            color: var(--text-secondary);
        }

        .border-color {
            border-color: var(--border-color);
        }

        .client-panel {
            background-color: var(--client-panel-bg);
            transition: all 0.3s ease;
        }

        .table-header {
            background-color: var(--table-header-bg);
            transition: all 0.3s ease;
        }

        input, select, textarea {
            background-color: var(--input-bg);
            color: var(--input-text);
            border: 1px solid var(--border-color);
            border-radius: 6px;
            transition: all 0.3s ease;
        }

        input:focus, select:focus, textarea:focus {
            border-color: #4f46e5;
            outline: none;
            box-shadow: 0 0 0 2px rgba(79, 70, 229, 0.2);
        }

        .profile-menu {
            display: none;
            position: absolute;
            right: 0;
            top: 100%;
            background-color: var(--card-bg);
            border: 1px solid var(--border-color);
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            z-index: 100;
            min-width: 200px;
            overflow: hidden;
        }

        .profile-menu.show {
            display: block;
        }

        .profile-menu-item {
            padding: 10px 16px;
            cursor: pointer;
            transition: background-color 0.2s;
            color: var(--text-primary);
        }

        .profile-menu-item:hover {
            background-color: var(--border-color);
        }

        .profile-picture {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            object-fit: cover;
            cursor: pointer;
        }

        .profile-container {
            position: relative;
        }

        .card-hover:hover {
            transform: translateY(-4px);
            box-shadow: 0 6px 16px rgba(0, 0, 0, 0.1);
        }

        .active-menu-item {
            background-color: rgba(79, 70, 229, 0.1);
            border-left: 4px solid #4f46e5;
        }

        .badge-expired, .badge-danger {
            background-color: rgba(239, 68, 68, 0.1);
            color: #ef4444;
        }

        .badge-active, .badge-success {
            background-color: rgba(16, 185, 129, 0.1);
            color: #10b981;
        }

        .badge-warning {
            background-color: rgba(245, 158, 11, 0.1);
            color: #f59e0b;
        }

        .notification-container {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 9999;
        }

        .theme-toggle-button {
            transition: all 0.3s ease;
        }

        .theme-toggle-button:hover {
            transform: scale(1.1);
        }

        .menu-item:hover {
            background-color: #f3f4f6;
        }

        [data-theme="dark"] .menu-item:hover {
            background-color: #4b5563;
        }

        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.5);
            z-index: 1000;
            align-items: center;
            justify-content: center;
        }

        .modal.active {
            display: flex;
        }

        .modal-content {
            background: var(--card-bg);
            color: var(--text-primary);
            padding: 20px;
            border-radius: 12px;
            max-width: 400px;
            width: 90%;
            text-align: center;
            border: 1px solid var(--border-color);
        }

        .modal-buttons {
            display: flex;
            justify-content: center;
            gap: 12px;
            margin-top: 20px;
        }

        .modal-button {
            padding: 10px 20px;
            border: none;
            border-radius: 6px;
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .modal-button.confirm {
            background: #ef4444;
            color: #ffffff;
        }

        .modal-button.confirm:hover {
            background: #dc2626;
        }

        .modal-button.cancel {
            background: var(--border-color);
            color: var(--text-primary);
        }

        .modal-button.cancel:hover {
            background: #d1d5db;
        }

        .control-button button, .control-form button {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 8px 14px;
            border: none;
            border-radius: 6px;
            font-size: 13px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .btn-primary {
            background: #4f46e5;
            color: #ffffff;
        }

        .btn-primary:hover {
            background: #4338ca;
        }

        .btn-success {
            background: #10b981;
            color: #ffffff;
        }

        .btn-success:hover {
            background: #059669;
        }

        .btn-danger {
            background: #ef4444;
            color: #ffffff;
        }

        .btn-danger:hover {
            background: #dc2626;
        }

        .btn-warning {
            background: #f59e0b;
            color: #ffffff;
        }

        .btn-warning:hover {
            background: #d97706;
        }

        .table-container {
            max-height: 400px;
            overflow-y: auto;
            overflow-x: hidden;
            position: relative;
            width: 100%;
        }

        .table-container table {
            width: 100%;
            table-layout: auto;
        }

        .table-container th, .table-container td {
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            max-width: 150px;
        }

        .table-container th:nth-child(1), .table-container td:nth-child(1) { /* Username */
            max-width: 100px;
        }

        .table-container th:nth-child(2), .table-container td:nth-child(2) { /* Criado em */
            max-width: 120px;
        }

        .table-container th:nth-child(3), .table-container td:nth-child(3) { /* Expira em */
            max-width: 120px;
        }

        .table-container th:nth-child(4), .table-container td:nth-child(4) { /* Duração Restante */
            max-width: 100px;
        }

        .table-container th:nth-child(5), .table-container td:nth-child(5) { /* Max Devices */
            max-width: 80px;
        }

        .table-container th:nth-child(6), .table-container td:nth-child(6) { /* HWID */
            max-width: 100px;
        }

        .table-container th:nth-child(7), .table-container td:nth-child(7) { /* Banido */
            max-width: 80px;
        }

        .table-container th:nth-child(8), .table-container td:nth-child(8) { /* Ações */
            max-width: 80px;
        }

        .lifetime-indicator {
            margin-top: 12px;
            padding: 8px;
            background: #4f46e5;
            color: #ffffff;
            border-radius: 6px;
            font-size: 13px;
            text-align: center;
        }

        .status-card {
            transition: all 0.3s ease;
            border: 1px solid var(--border-color);
            border-radius: 12px;
        }

        [data-theme="dark"] .status-card {
            background-color: rgba(31, 41, 55, 0.7);
        }

        .badge {
            padding: 6px 12px;
            border-radius: 16px;
            font-size: 13px;
            font-weight: 500;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .badge-success {
            background-color: rgba(16, 185, 129, 0.1);
            color: #10b981;
        }

        .badge-danger {
            background-color: rgba(239, 68, 68, 0.1);
            color: #ef4444;
        }

        .badge-warning {
            background-color: rgba(245, 158, 11, 0.1);
            color: #f59e0b;
        }

        .badge-info {
            background-color: rgba(59, 130, 246, 0.1);
            color: #3b82f6;
        }

        .header-container {
            width: 100%;
            background-color: var(--card-bg);
            border-bottom: 1px solid var(--border-color);
            z-index: 10;
        }

        header {
            width: 100%;
            max-width: none;
            margin: 0;
            padding-left: 260px; 
            transition: padding-left 0.3s ease;
        }

        .sidebar.hidden + .main-content header {
            padding-left: 0;
        }

        .main-content {
            flex: 1;
            display: flex;
            flex-direction: column;
            width: calc(100% - 260px);
            transition: width 0.3s ease;
        }

        .sidebar.hidden + .main-content {
            width: 100%;
        }

        @media (max-width: 768px) {
            .sidebar {
                width: 200px;
                position: fixed;
                height: 100%;
                z-index: 1000;
            }

            .main-content {
                width: 100%;
            }

            header {
                padding-left: 0;
            }

            .content-container {
                flex-direction: row;
            }

            .grid-cols-4 {
                grid-template-columns: repeat(1, minmax(0, 1fr));
            }

            .grid-cols-2 {
                grid-template-columns: 1fr;
            }

            .card {
                padding: 16px;
            }

            .control-button button {
                padding: 8px 12px;
                font-size: 12px;
            }

            .table-container {
                max-height: 300px;
            }

            .table-container th, .table-container td {
                max-width: 100px;
            }

            .table-container th:nth-child(1), .table-container td:nth-child(1) {
                max-width: 80px;
            }

            .table-container th:nth-child(6), .table-container td:nth-child(6) {
                max-width: 80px;
            }
        }

        @media (max-width: 640px) {
            .sidebar {
                transform: translateX(-100%);
            }

            .sidebar.active {
                transform: translateX(0);
            }

            .main-content {
                padding: 16px;
            }

            .profile-menu {
                right: 16px;
                min-width: 160px;
            }

            .table-container th, .table-container td {
                max-width: 80px;
            }

            .table-container th:nth-child(1), .table-container td:nth-child(1) {
                max-width: 60px;
            }

            .table-container th:nth-child(6), .table-container td:nth-child(6) {
                max-width: 60px;
            }
        }

      
        .chat-container {
            display: flex;
            flex-direction: column;
            height: 500px;
            border: 1px solid var(--border-color);
            border-radius: 8px;
            overflow: hidden;
        }

        .chat-header {
            background-color: var(--card-bg);
            padding: 12px;
            border-bottom: 1px solid var(--border-color);
            font-weight: medium;
        }

        .chat-messages {
            flex: 1;
            padding: 12px;
            overflow-y: auto;
            background-color: var(--main-bg);
        }

        .chat-message {
            margin-bottom: 12px;
            display: flex;
            flex-direction: column;
        }

        .chat-message.client {
            align-items: flex-end;
        }

        .chat-message.admin {
            align-items: flex-start;
        }

        .chat-message .message-bubble {
            max-width: 70%;
            padding: 8px 12px;
            border-radius: 8px;
            font-size: 14px;
        }

        .chat-message.client .message-bubble {
            background-color: #4f46e5;
            color: white;
        }

        .chat-message.admin .message-bubble {
            background-color: var(--border-color);
            color: var(--text-primary);
        }

        .chat-message .message-time {
            font-size: 12px;
            color: var(--text-secondary);
            margin-top: 4px;
        }

        .chat-input {
            display: flex;
            padding: 12px;
            background-color: var(--card-bg);
            border-top: 1px solid var(--border-color);
        }

        .chat-input input {
            flex: 1;
            padding: 8px;
            border-radius: 8px;
            border: 1px solid var(--border-color);
            margin-right: 8px;
        }

        .chat-input button {
            padding: 8px 16px;
            background-color: #4f46e5;
            color: white;
            border-radius: 8px;
            border: none;
        }

        .chat-input button:hover {
            background-color: #4338ca;
        }
    </style>
</head>
<body class="transition-colors duration-300">
    <div class="main-container">
        <div class="header-container">
            <header class="card py-4 px-6 flex items-center justify-between">
                <div class="flex items-center">
                    <button id="sidebarToggle" class="mr-4 text-secondary hover:text-indigo-700">
                        <i class="fas fa-bars"></i>
                    </button>
                    <h1 class="text-xl font-bold text-indigo-700">Painel do Administrador</h1>
                </div>
                
                <div class="flex items-center space-x-4">
                    <button id="themeToggle" class="theme-toggle-button text-secondary hover:text-indigo-700">
                        <i class="fas fa-moon"></i>
                    </button>
                    <div class="border-l h-8 border-color hidden sm:block"></div>
                    <div class="relative">
                        <button class="text-secondary hover:text-indigo-700">
                            <i class="fas fa-bell text-xl"></i>
                        </button>
                    </div>
                    <div class="border-l h-8 border-color hidden sm:block"></div>
                    <div class="profile-container">
                        <img 
                            src="https://via.placeholder.com/40" 
                            class="profile-picture"
                            id="profilePicture"
                            onclick="toggleProfileMenu()"
                            alt="Foto do perfil"
                        >
                        <span class="ml-2 font-medium hidden md:inline"><?= htmlspecialchars($_SESSION['admin_username'] ?? 'Admin') ?></span>
                        
                        <div class="profile-menu" id="profileMenu">
                            <div class="profile-menu-item" onclick="showModal()">
                                <i class="fas fa-sign-out-alt mr-2"></i> Sair
                            </div>
                        </div>
                    </div>
                </div>
            </header>
        </div>
        
        <div class="content-container">
        
            <div class="sidebar" id="sidebar">
                <div class="p-4 border-b border-color">
                    <div class="flex items-center">
                        <div class="w-10 h-10 rounded-full bg-indigo-700 flex items-center justify-center text-white font-bold">
                            <i class="fas fa-shield-alt"></i>
                        </div>
                        <h1 class="ml-3 font-bold text-lg">SKYREC</h1>
                    </div>
                </div>
                
                <div class="p-4">
                    <div class="flex items-center space-x-3 p-3 client-panel rounded-lg">
                        <div class="relative">
                            <div class="w-10 h-10 rounded-full bg-indigo-700 flex items-center justify-center text-white font-bold">
                                <?= strtoupper(substr($_SESSION['admin_username'] ?? 'Admin', 0, 1)) ?>
                            </div>
                            <span class="absolute bottom-0 right-0 w-3 h-3 bg-green-500 rounded-full border-2 border-white"></span>
                        </div>
                        <div>
                            <p class="font-medium"><?= htmlspecialchars($_SESSION['admin_username'] ?? 'Admin') ?></p>
                            <p class="text-xs text-secondary">Administrador</p>
                        </div>
                    </div>
                </div>
                
                <nav class="mt-2">
                    <div class="px-2">
                        <p class="text-xs font-semibold text-secondary uppercase tracking-wider mb-2 px-3">Menu Principal</p>
                        <a href="#" class="menu-item flex items-center px-3 py-2 rounded-lg active-menu-item" onclick="showSection('info'); loadUserInfo()">
                            <i class="fas fa-tachometer-alt mr-3 text-indigo-700"></i>
                            <span>Dashboard</span>
                        </a>
                        <a href="#" class="menu-item flex items-center px-3 py-2 rounded-lg" onclick="showSection('generate')">
                            <i class="fas fa-user-plus mr-3 text-indigo-700"></i>
                            <span>Gerar Usuário</span>
                        </a>
                        <a href="#" class="menu-item flex items-center px-3 py-2 rounded-lg" onclick="showSection('manage')">
                            <i class="fas fa-users-cog mr-3 text-indigo-700"></i>
                            <span>Gerenciar Usuário</span>
                        </a>
                        <a href="#" class="menu-item flex items-center px-3 py-2 rounded-lg" onclick="showSection('key'); loadKeys()">
                            <i class="fas fa-key mr-3 text-indigo-700"></i>
                            <span>Gerar Chave</span>
                        </a>
                        <a href="#" class="menu-item flex items-center px-3 py-2 rounded-lg" onclick="showSection('support')">
                            <i class="fas fa-headset mr-3 text-indigo-700"></i>
                            <span>Suporte</span>
                        </a>
                        <a href="dll_management.php" class="menu-item flex items-center px-3 py-2 rounded-lg">
                            <i class="fas fa-cogs mr-3 text-indigo-700"></i>
                            <span>Gerenciar DLL</span>
                        </a>
                    </div>
                </nav>
                
                <div class="absolute bottom-0 w-full p-4 border-t border-color">
                    <a href="#" class="flex items-center hover:text-indigo-700" onclick="showModal(); return false;">
                        <i class="fas fa-sign-out-alt mr-3"></i>
                        <span>Sair</span>
                    </a>
                </div>
            </div>
            
           
            <div class="main-content flex-1 flex flex-col">
                <main class="p-6 flex-1 overflow-y-auto">
                    <div class="card rounded-xl shadow-sm p-6 mb-6" id="server-controls-section">
                        <h3 class="font-medium text-lg mb-4 text-indigo-700">
                            <i class="fas fa-server mr-2"></i> Gerenciamento do Servidor
                        </h3>
                        
                      
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                            <div class="status-card p-4">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p class="text-sm font-medium text-secondary">Status do Servidor</p>
                                        <p class="text-lg font-semibold mt-1">
                                            <span id="server-status" class="badge badge-success">Carregando...</span>
                                        </p>
                                    </div>
                                    <i class="fas fa-server text-xl text-green-500"></i>
                                </div>
                            </div>
                            
                            <div class="status-card p-4">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p class="text-sm font-medium text-secondary">Status de Logins</p>
                                        <p class="text-lg font-semibold mt-1">
                                            <span id="login-status" class="badge badge-success">Carregando...</span>
                                        </p>
                                    </div>
                                    <i class="fas fa-sign-in-alt text-xl text-blue-500"></i>
                                </div>
                            </div>
                        </div>
                        
                        <div class="tabs flex border-b border-color mb-4">
                            <div class="tab px-4 py-2 cursor-pointer text-sm font-medium text-secondary active" data-tab="access">Acesso</div>
                            <div class="tab px-4 py-2 cursor-pointer text-sm font-medium text-secondary" data-tab="subscriptions">Assinaturas</div>
                        </div>
                        
                        <div class="tab-content active" id="access">
                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                                <button class="btn-warning flex items-center justify-center gap-2 py-2 text-sm" onclick="serverAction('pause_all')">
                                    <i class="fas fa-pause"></i> Pausar Logins
                                </button>
                                <button class="btn-success flex items-center justify-center gap-2 py-2 text-sm" onclick="serverAction('unpause_all')">
                                    <i class="fas fa-play"></i> Despausar Logins
                                </button>
                                <button class="btn-danger flex items-center justify-center gap-2 py-2 text-sm" id="cheat-toggle-btn" onclick="toggleCheat()">
                                    <i class="fas fa-power-off"></i> <span id="cheat-toggle-text">Desligar servidor</span>
                                </button>
                            </div>
                        </div>
                        
                        <div class="tab-content" id="subscriptions">
                            <div class="grid grid-cols-1 gap-4 mb-4">
                                <div class="flex flex-col sm:flex-row gap-2">
                                    <select id="duration-type" class="flex-1 px-3 py-2 rounded-lg border border-color text-sm">
                                        <option value="days">Dias</option>
                                        <option value="lifetime">Lifetime</option>
                                    </select>
                                    <input type="number" id="server-duration" placeholder="Duração" min="1" class="flex-1 px-3 py-2 rounded-lg border border-color text-sm">
                                    <button class="btn-primary flex items-center justify-center gap-2 px-4 py-2 text-sm" onclick="serverAction('add_days_all')">
                                        <i class="fas fa-calendar-plus"></i> Adicionar
                                    </button>
                                </div>
                                
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                                    <button class="btn-danger flex items-center justify-center gap-2 py-2 text-sm" onclick="if(confirm('Tem certeza que deseja deletar todos os usuários expirados?')) serverAction('delete_expired_users')">
                                        <i class="fas fa-trash"></i> Deletar Expirados
                                    </button>
                                    <button class="btn-warning flex items-center justify-center gap-2 py-2 text-sm" onclick="if(confirm('Tem certeza que deseja resetar todos os HWIDs?')) serverAction('reset_all_hwids')">
                                        <i class="fas fa-undo"></i> Resetar HWIDs
                                    </button>
                                </div>
                            </div>
                            <div class="lifetime-indicator hidden" id="lifetimeIndicator">
                                <i class="fas fa-infinity"></i> Lifetime Ativado - Validade Máxima (999 dias)
                            </div>
                        </div>
                        
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mt-6">
                            <div class="card rounded-xl p-4 card-hover">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p class="text-sm text-secondary">Total de Clientes</p>
                                        <p class="text-xl font-bold mt-1" id="total-clients">Carregando...</p>
                                    </div>
                                    <div class="bg-blue-50 p-2 rounded-lg">
                                        <i class="fas fa-users text-blue-600 text-lg"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="card rounded-xl p-4 card-hover">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p class="text-sm text-secondary">Total de Logins Gerados</p>
                                        <p class="text-xl font-bold mt-1" id="total-logins">Carregando...</p>
                                    </div>
                                    <div class="bg-green-50 p-2 rounded-lg">
                                        <i class="fas fa-sign-in-alt text-green-600 text-lg"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="card rounded-xl p-4 card-hover">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p class="text-sm text-secondary">Total Banidos</p>
                                        <p class="text-xl font-bold mt-1" id="total-banned">Carregando...</p>
                                    </div>
                                    <div class="bg-red-50 p-2 rounded-lg">
                                        <i class="fas fa-ban text-red-600 text-lg"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="card rounded-xl p-4 card-hover">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p class="text-sm text-secondary">Total Expirados</p>
                                        <p class="text-xl font-bold mt-1" id="total-expired">Carregando...</p>
                                    </div>
                                    <div class="bg-yellow-50 p-2 rounded-lg">
                                        <i class="fas fa-clock text-yellow-600 text-lg"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card rounded-xl shadow-sm overflow-hidden" id="info-section">
                        <div class="p-4 border-b border-color flex items-center justify-between">
                            <h3 class="font-medium text-lg text-primary">Informações dos Usuários</h3>
                            <div class="relative">
                                <i class="fas fa-magnifying-glass absolute left-3 top-1/2 transform -translate-y-1/2 text-secondary"></i>
                                <input type="text" id="userSearchInput" placeholder="Pesquisar username" class="pl-10 pr-4 py-2 rounded-lg border border-color focus:outline-none focus:ring-2 focus:ring-indigo-700 text-sm w-full sm:w-64">
                            </div>
                        </div>
                        <div class="table-container">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="table-header">
                                    <tr>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-secondary uppercase tracking-wider">Username</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-secondary uppercase tracking-wider">Criado em</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-secondary uppercase tracking-wider">Expira em</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-secondary uppercase tracking-wider">Duração</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-secondary uppercase tracking-wider">Devices</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-secondary uppercase tracking-wider">HWID</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-secondary uppercase tracking-wider">Banido</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-secondary uppercase tracking-wider">Ações</th>
                                    </tr>
                                </thead>
                                <tbody id="user-info-body" class="divide-y divide-gray-200"></tbody>
                            </table>
                        </div>
                    </div>

                    <div class="card rounded-xl shadow-sm p-6" id="generate-section" style="display: none;">
                        <h3 class="font-medium text-lg mb-4 text-indigo-700">
                            <i class="fas fa-user-plus mr-2"></i> Gerar Usuário
                        </h3>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-secondary mb-1">Username</label>
                                <input type="text" id="gen-username" placeholder="Username" class="w-full px-3 py-2 rounded-lg border border-color text-sm">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-secondary mb-1">Password</label>
                                <input type="text" id="gen-password" placeholder="Password" class="w-full px-3 py-2 rounded-lg border border-color text-sm">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-secondary mb-1">Duração</label>
                                <div class="flex gap-2">
                                    <select id="gen-duration-type" class="w-1/2 px-3 py-2 rounded-lg border border-color text-sm">
                                        <option value="seconds">Segundos</option>
                                        <option value="hours">Horas</option>
                                        <option value="days">Dias</option>
                                        <option value="weeks">Semanas</option>
                                        <option value="months">Meses</option>
                                        <option value="lifetime">Lifetime</option>
                                    </select>
                                    <input type="number" id="gen-duration" placeholder="Duração" min="1" class="w-1/2 px-3 py-2 rounded-lg border border-color text-sm">
                                </div>
                                <div class="lifetime-indicator hidden" id="gen-lifetime-indicator">
                                    <i class="fas fa-infinity"></i> Lifetime Ativado - Validade Máxima (999 dias)
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-secondary mb-1">Max Devices</label>
                                <input type="number" id="gen-devices" placeholder="Max Devices" min="1" class="w-full px-3 py-2 rounded-lg border border-color text-sm">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-secondary mb-1">Produto</label>
                                <select id="gen-product" class="w-full px-3 py-2 rounded-lg border border-color text-sm">
                                    <option value="1">Seu produto 1</option>
                                    <option value="2">VSeu produto 2</option>
                                </select>
                            </div>
                        </div>
                        <button onclick="generateUser()" class="mt-4 px-4 py-2 bg-indigo-700 text-white rounded-lg hover:bg-indigo-800 flex items-center text-sm">
                            <i class="fas fa-user-plus mr-2"></i> Gerar Usuário
                        </button>
                    </div>

                    <div class="card rounded-xl shadow-sm p-6" id="manage-section" style="display: none;">
                        <h3 class="font-medium text-lg mb-4 text-indigo-700">
                            <i class="fas fa-users-cog mr-2"></i> Gerenciar Usuário
                        </h3>
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-secondary mb-1">Username</label>
                            <input type="text" id="manage-username" placeholder="Username" oninput="toggleButtons()" class="w-full px-3 py-2 rounded-lg border border-color text-sm">
                        </div>
                        <div id="manage-actions" style="display: none;">
                            <h4 class="font-medium text-sm mb-2 text-primary">Ações Disponíveis</h4>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div class="flex gap-2">
                                    <button onclick="manageUser('ban')" class="btn-danger flex-1 py-2 text-sm">Banir</button>
                                    <button onclick="manageUser('unban')" class="btn-success flex-1 py-2 text-sm">Desbanir</button>
                                    <button onclick="if(confirm('Tem certeza que deseja deletar este usuário?')) manageUser('delete')" class="btn-danger flex-1 py-2 text-sm">Deletar</button>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-secondary mb-1">Novo Username</label>
                                    <div class="flex gap-2">
                                        <input type="text" id="new-username" placeholder="Novo Username" class="flex-1 px-3 py-2 rounded-lg border border-color text-sm">
                                        <button onclick="manageUser('change_username')" class="btn-primary px-4 py-2 text-sm">Alterar Username</button>
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-secondary mb-1">Nova Senha</label>
                                    <div class="flex gap-2">
                                        <input type="text" id="new-password" placeholder="Nova Senha" class="flex-1 px-3 py-2 rounded-lg border border-color text-sm">
                                        <button onclick="manageUser('change_password')" class="btn-primary px-4 py-2 text-sm">Alterar Senha</button>
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-secondary mb-1">Adicionar Duração</label>
                                    <div class="flex gap-2">
                                        <select id="manage-duration-type" class="w-1/3 px-3 py-2 rounded-lg border border-color text-sm">
                                            <option value="seconds">Segundos</option>
                                            <option value="hours">Horas</option>
                                            <option value="days">Dias</option>
                                            <option value="weeks">Semanas</option>
                                            <option value="months">Meses</option>
                                            <option value="lifetime">Lifetime</option>
                                        </select>
                                        <input type="number" id="days-add" placeholder="Duração a adicionar" min="1" class="w-1/3 px-3 py-2 rounded-lg border border-color text-sm">
                                        <button onclick="manageUser('add_duration')" class="btn-primary w-1/3 py-2 text-sm">Adicionar Duração</button>
                                    </div>
                                    <div class="lifetime-indicator hidden" id="manage-lifetime-indicator">
                                        <i class="fas fa-infinity"></i> Lifetime Ativado - Validade Máxima (999 dias)
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-secondary mb-1">Remover Duração</label>
                                    <div class="flex gap-2">
                                        <select id="manage-remove-duration-type" class="w-1/3 px-3 py-2 rounded-lg border border-color text-sm">
                                            <option value="seconds">Segundos</option>
                                            <option value="hours">Horas</option>
                                            <option value="days">Dias</option>
                                            <option value="weeks">Semanas</option>
                                            <option value="months">Meses</option>
                                        </select>
                                        <input type="number" id="days-remove" placeholder="Duração a remover" min="1" class="w-1/3 px-3 py-2 rounded-lg border border-color text-sm">
                                        <button onclick="manageUser('remove_duration')" class="btn-danger w-1/3 py-2 text-sm">Remover Duração</button>
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-secondary mb-1">Adicionar Devices</label>
                                    <div class="flex gap-2">
                                        <input type="number" id="devices-add" placeholder="Devices a adicionar" min="1" class="flex-1 px-3 py-2 rounded-lg border border-color text-sm">
                                        <button onclick="manageUser('add_devices')" class="btn-primary px-4 py-2 text-sm">Adicionar Devices</button>
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-secondary mb-1">Remover Devices</label>
                                    <div class="flex gap-2">
                                        <input type="number" id="devices-remove" placeholder="Devices a remover" min="1" class="flex-1 px-3 py-2 rounded-lg border border-color text-sm">
                                        <button onclick="manageUser('remove_devices')" class="btn-danger px-4 py-2 text-sm">Remover Devices</button>
                                    </div>
                                </div>
                                <div>
                                    <button onclick="manageUser('reset_hwid')" class="btn-primary w-full py-2 text-sm">Resetar HWID</button>
                                </div>
                            </div>
                        </div>
                    </div>
 
                    <div class="card rounded-xl shadow-sm p-6" id="key-section" style="display: none;">
                        <h3 class="font-medium text-lg mb-4 text-indigo-700">
                            <i class="fas fa-key mr-2"></i> Gerar Chave
                        </h3>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-secondary mb-1">Duração</label>
                                <div class="flex gap-2">
                                    <select id="key-duration-type" class="w-1/2 px-3 py-2 rounded-lg border border-color text-sm">
                                        <option value="seconds">Segundos</option>
                                        <option value="hours">Horas</option>
                                        <option value="days">Dias</option>
                                        <option value="weeks">Semanas</option>
                                        <option value="months">Meses</option>
                                        <option value="lifetime">Lifetime</option>
                                    </select>
                                    <input type="number" id="key-duration" placeholder="Duração" min="1" class="w-1/2 px-3 py-2 rounded-lg border border-color text-sm">
                                </div>
                                <div class="lifetime-indicator hidden" id="key-lifetime-indicator">
                                    <i class="fas fa-infinity"></i> Lifetime Ativado - Validade Máxima (999 dias)
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-secondary mb-1">Máximo de Usos</label>
                                <input type="number" id="key-uses" placeholder="Máximo de Usos" min="1" class="w-full px-3 py-2 rounded-lg border border-color text-sm">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-secondary mb-1">Tipo de Chave</label>
                                <select id="key-type" class="w-full px-3 py-2 rounded-lg border border-color text-sm">
                                    <option value="standard">Padrão</option>
                                    <option value="premium">Premium</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-secondary mb-1">Produto</label>
                                <select id="key-product" class="w-full px-3 py-2 rounded-lg border border-color text-sm">
                                    <option value="1">Seu produto 1</option>
                                    <option value="2">Seu produto 2</option>
                                </select>
                            </div>
                        </div>
                        <button onclick="generateKey()" class="mt-4 px-4 py-2 bg-indigo-700 text-white rounded-lg hover:bg-indigo-800 flex items-center text-sm">
                            <i class="fas fa-key mr-2"></i> Gerar Key
                        </button>
                        <div class="mt-4 relative">
                            <label class="block text-sm font-medium text-secondary mb-1">Key Gerada</label>
                            <input type="text" id="generated-key" placeholder="Chave gerada aparecerá aqui" readonly class="w-full px-3 py-2 rounded-lg border border-color text-sm">
                            <button onclick="copyKey()" style="display: none;" id="copy-key-btn" class="absolute right-2 top-8 px-3 py-1 bg-indigo-700 text-white rounded-lg hover:bg-indigo-800 text-sm">
                                <i class="fas fa-copy"></i> Copiar
                            </button>
                        </div>
                        <div class="mt-6">
                            <h4 class="font-medium text-lg text-primary mb-4">Últimas Keys Geradas</h4>
                            <div class="table-container">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="table-header">
                                        <tr>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-secondary uppercase tracking-wider">Key</th>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-secondary uppercase tracking-wider">Criada em</th>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-secondary uppercase tracking-wider">Resgatada</th>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-secondary uppercase tracking-wider">Usuário que Resgatou</th>
                                        </tr>
                                    </thead>
                                    <tbody id="keys-body" class="divide-y divide-gray-200"></tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="card rounded-xl shadow-sm p-6" id="support-section" style="display: none;">
                        <div class="card rounded-xl shadow-sm p-6 mb-8">
                            <div class="flex items-center justify-between mb-6">
                                <h2 class="text-2xl font-bold text-primary">
                                    <i class="fas fa-headset mr-2 text-indigo-700"></i> Suporte
                                </h2>
                            </div>
                            <h3 class="font-medium text-lg mb-4 text-indigo-700">
                                <i class="fas fa-ticket-alt mr-2"></i> Tickets de Suporte
                            </h3>
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="table-header">
                                        <tr>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-secondary uppercase tracking-wider">Ticket ID</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-secondary uppercase tracking-wider">Cliente</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-secondary uppercase tracking-wider">Status</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-secondary uppercase tracking-wider">Criado em</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-secondary uppercase tracking-wider">Ações</th>
                                        </tr>
                                    </thead>
                                    <tbody id="tickets-table" class="divide-y divide-gray-200">
                                        <?php
                                        require_once 'db.php';
                                        $stmt = $pdo->query('
                                            SELECT t.id, t.status, t.created_at, c.username 
                                            FROM support_tickets t 
                                            JOIN clients c ON t.client_id = c.id 
                                            ORDER BY t.created_at DESC
                                        ');
                                        while ($ticket = $stmt->fetch(PDO::FETCH_ASSOC)):
                                        ?>
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-primary"><?= $ticket['id'] ?></td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-primary"><?= htmlspecialchars($ticket['username']) ?></td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full <?= $ticket['status'] === 'open' ? 'badge-warning' : ($ticket['status'] === 'in_progress' ? 'badge-active' : 'badge-expired') ?>">
                                                    <?= ucfirst($ticket['status'] === 'open' ? 'Aberto' : ($ticket['status'] === 'in_progress' ? 'Em andamento' : 'Fechado')) ?>
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-secondary"><?= (new DateTime($ticket['created_at']))->format('d/m/Y H:i') ?></td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                                <button onclick="openChat(<?= $ticket['id'] ?>)" class="text-indigo-700 hover:text-indigo-900 mr-3" title="Abrir Chat">
                                                    <i class="fas fa-comments"></i>
                                                </button>
                                                <?php if ($ticket['status'] !== 'closed'): ?>
                                                <button onclick="closeTicket(<?= $ticket['id'] ?>)" class="text-red-600 hover:text-red-800" title="Fechar Ticket">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                        <?php endwhile; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div id="chat-section" class="card rounded-xl shadow-sm p-6" style="display: none;">
                            <h3 class="font-medium text-lg mb-4 text-indigo-700">
                                <i class="fas fa-comments mr-2"></i> Chat do Ticket #<span id="chat-ticket-id"></span>
                            </h3>
                            <div class="chat-container">
                                <div class="chat-header">Conversa com o Cliente</div>
                                <div class="chat-messages" id="chat-messages"></div>
                                <div class="chat-input">
                                    <input type="text" id="chat-input" placeholder="Digite sua mensagem..." onkeypress="if(event.key === 'Enter') sendMessage()">
                                    <button onclick="sendMessage()" class="px-4 py-2">Enviar</button>
                                </div>
                            </div>
                            <button onclick="closeChat()" class="mt-4 px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 flex items-center text-sm">
                                <i class="fas fa-times mr-2"></i> Fechar Chat
                            </button>
                        </div>
                    </div>

                    <div class="card rounded-xl shadow-sm p-6" id="manage-panel" style="display: none; position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%); z-index: 1000; max-width: 90%;">
                        <h3 class="font-medium text-lg mb-4 text-indigo-700">
                            Gerenciar <span id="manage-panel-username"></span>
                        </h3>
                        <button onclick="closeManagePanel()" class="absolute top-4 right-4 bg-red-500 text-white px-2 py-1 rounded-lg text-sm">X</button>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div class="flex gap-2">
                                <button onclick="manageUserFromPanel('ban')" class="btn-danger flex-1 py-2 text-sm">Banir</button>
                                <button onclick="manageUserFromPanel('unban')" class="btn-success flex-1 py-2 text-sm">Desbanir</button>
                                <button onclick="if(confirm('Tem certeza que deseja deletar este usuário?')) manageUserFromPanel('delete')" class="btn-danger flex-1 py-2 text-sm">Deletar</button>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-secondary mb-1">Novo Username</label>
                                <div class="flex gap-2">
                                    <input type="text" id="panel-new-username" placeholder="Novo Username" class="flex-1 px-3 py-2 rounded-lg border border-color text-sm">
                                    <button onclick="manageUserFromPanel('change_username')" class="btn-primary px-4 py-2 text-sm">Alterar Username</button>
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-secondary mb-1">Nova Senha</label>
                                <div class="flex gap-2">
                                    <input type="text" id="panel-new-password" placeholder="Nova Senha" class="flex-1 px-3 py-2 rounded-lg border border-color text-sm">
                                    <button onclick="manageUserFromPanel('change_password')" class="btn-primary px-4 py-2 text-sm">Alterar Senha</button>
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-secondary mb-1">Adicionar Duração</label>
                                <div class="flex gap-2">
                                    <select id="panel-duration-type" class="w-1/3 px-3 py-2 rounded-lg border border-color text-sm">
                                        <option value="seconds">Segundos</option>
                                        <option value="hours">Horas</option>
                                        <option value="days">Dias</option>
                                        <option value="weeks">Semanas</option>
                                        <option value="months">Meses</option>
                                        <option value="lifetime">Lifetime</option>
                                    </select>
                                    <input type="number" id="panel-days-add" placeholder="Duração a adicionar" min="1" class="w-1/3 px-3 py-2 rounded-lg border border-color text-sm">
                                    <button onclick="manageUserFromPanel('add_duration')" class="btn-primary w-1/3 py-2 text-sm">Adicionar Duração</button>
                                </div>
                                <div class="lifetime-indicator hidden" id="panel-lifetime-indicator">
                                    <i class="fas fa-infinity"></i> Lifetime Ativado - Validade Máxima (999 dias)
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-secondary mb-1">Remover Duração</label>
                                <div class="flex gap-2">
                                    <select id="panel-remove-duration-type" class="w-1/3 px-3 py-2 rounded-lg border border-color text-sm">
                                        <option value="seconds">Segundos</option>
                                        <option value="hours">Horas</option>
                                        <option value="days">Dias</option>
                                        <option value="weeks">Semanas</option>
                                        <option value="months">Meses</option>
                                    </select>
                                    <input type="number" id="panel-days-remove" placeholder="Duração a remover" min="1" class="w-1/3 px-3 py-2 rounded-lg border border-color text-sm">
                                    <button onclick="manageUserFromPanel('remove_duration')" class="btn-danger w-1/3 py-2 text-sm">Remover Duração</button>
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-secondary mb-1">Adicionar Devices</label>
                                <div class="flex gap-2">
                                    <input type="number" id="panel-devices-add" placeholder="Devices a adicionar" min="1" class="flex-1 px-3 py-2 rounded-lg border border-color text-sm">
                                    <button onclick="manageUserFromPanel('add_devices')" class="btn-primary px-4 py-2 text-sm">Adicionar Devices</button>
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-secondary mb-1">Remover Devices</label>
                                <div class="flex gap-2">
                                    <input type="number" id="panel-devices-remove" placeholder="Devices a remover" min="1" class="flex-1 px-3 py-2 rounded-lg border border-color text-sm">
                                    <button onclick="manageUserFromPanel('remove_devices')" class="btn-danger px-4 py-2 text-sm">Remover Devices</button>
                                </div>
                            </div>
                            <div>
                                <button onclick="manageUserFromPanel('reset_hwid')" class="btn-primary w-full py-2 text-sm">Resetar HWID</button>
                            </div>
                        </div>
                    </div>
                </main>
            </div>
        </div>
    </div>

    <div class="notification-container" id="notificationContainer"></div>

    <div class="modal" id="logoutModal">
        <div class="modal-content">
            <h3 class="text-lg font-medium text-primary">Confirmar Logout</h3>
            <p class="text-secondary">Tem certeza que deseja sair?</p>
            <div class="modal-buttons">
                <button class="modal-button confirm" onclick="logout()" data-notify="Logging out">Sair</button>
                <button class="modal-button cancel" onclick="hideModal()">Cancelar</button>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/toastify-js"></script>
    <script>
      
        console.log('Sessão admin:', <?php echo json_encode($_SESSION); ?>);

        
        const htmlElement = document.getElementById('htmlElement');
        const themeToggle = document.getElementById('themeToggle');
        const themeIcon = themeToggle.querySelector('i');

        themeToggle.addEventListener('click', () => {
            const currentTheme = htmlElement.getAttribute('data-theme');
            const newTheme = currentTheme === 'light' ? 'dark' : 'light';
            
            htmlElement.setAttribute('data-theme', newTheme);
            htmlElement.classList.remove('light', 'dark');
            htmlElement.classList.add(newTheme);
            
            localStorage.setItem('theme', newTheme);
            updateThemeIcon(newTheme);
        });

        function updateThemeIcon(theme) {
            themeIcon.classList.remove('fa-moon', 'fa-sun');
            themeIcon.classList.add(theme === 'light' ? 'fa-moon' : 'fa-sun');
        }

        
        const sidebarToggle = document.getElementById('sidebarToggle');
        const sidebar = document.getElementById('sidebar');
        sidebarToggle.addEventListener('click', () => {
            sidebar.classList.toggle('hidden');
            sidebar.classList.toggle('active');
        });

        
        document.addEventListener('click', (event) => {
            if (!sidebar.contains(event.target) && !sidebarToggle.contains(event.target) && sidebar.classList.contains('active')) {
                sidebar.classList.remove('active');
                sidebar.classList.add('hidden');
            }
        });

        function toggleProfileMenu() {
            document.getElementById('profileMenu').classList.toggle('show');
        }

        document.addEventListener('click', function(event) {
            const profileMenu = document.getElementById('profileMenu');
            const profilePicture = document.getElementById('profilePicture');
            
            if (!profileMenu.contains(event.target) && event.target !== profilePicture) {
                profileMenu.classList.remove('show');
            }
        });

        let currentUsername = '';

        function showNotification(message, type = 'info') {
            Toastify({
                text: message,
                duration: 3000,
                gravity: "top",
                position: "right",
                backgroundColor: type === 'success' ? "#10B981" : type === 'error' ? "#EF4444" : "#4f46e5",
                stopOnFocus: true,
            }).showToast();
        }

        window.addEventListener('load', () => {
            showNotification('Bem-vindo ao Painel de Gerenciamento!', 'success');
            const savedTheme = localStorage.getItem('theme') || 'light';
            document.documentElement.setAttribute('data-theme', savedTheme);
            updateThemeIcon(savedTheme);
            loadUserInfo();
            setupDurationSelectors();
            
            const urlParams = new URLSearchParams(window.location.search);
            if (urlParams.get('page') === 'support') {
                showSection('support');
            }
        });

        document.querySelectorAll('.tab').forEach(tab => {
            tab.addEventListener('click', () => {
                document.querySelectorAll('.tab').forEach(t => t.classList.remove('active'));
                document.querySelectorAll('.tab-content').forEach(c => c.classList.remove('active'));
                tab.classList.add('active');
                document.getElementById(tab.dataset.tab).classList.add('active');
            });
        });

        function showModal() {
            document.getElementById('logoutModal').classList.add('active');
            showNotification('Confirmando logout', 'warning');
        }

        function hideModal() {
            document.getElementById('logoutModal').classList.remove('active');
        }

        function showSection(section) {
            const sections = ['generate', 'manage', 'info', 'key', 'support'];
            sections.forEach(s => {
                const element = document.getElementById(`${s}-section`);
                if (element) {
                    element.style.display = s === section ? 'block' : 'none';
                }
            });
            document.getElementById('server-controls-section').style.display = section === 'info' ? 'block' : 'none';

            document.querySelectorAll('.menu-item').forEach(link => {
                link.classList.remove('active-menu-item');
                if (link.getAttribute('onclick') && link.getAttribute('onclick').includes(section)) {
                    link.classList.add('active-menu-item');
                }
            });

            if (sidebar.classList.contains('active')) {
                sidebar.classList.remove('active');
                sidebar.classList.add('hidden');
            }
        }

        function toggleButtons() {
            const username = document.getElementById('manage-username').value;
           

 document.getElementById('manage-actions').style.display = username ? 'block' : 'none';
        }

        function showToast(message, success = true) {
            showNotification(message, success ? 'success' : 'error');
        }

        function setupDurationSelectors() {
            const selectors = [
                'gen-duration-type',
                'key-duration-type',
                'manage-duration-type',
                'panel-duration-type',
                'duration-type'
            ];

            selectors.forEach(selector => {
                const element = document.getElementById(selector);
                if (element) {
                    element.addEventListener('change', function() {
                        const durationInput = document.getElementById(selector.replace('-type', ''));
                        const lifetimeIndicator = document.getElementById(selector.replace('duration-type', 'lifetime-indicator') || 'lifetimeIndicator');
                        if (this.value === 'lifetime') {
                            durationInput.value = 999;
                            durationInput.disabled = true;
                            lifetimeIndicator.style.display = 'block';
                        } else {
                            durationInput.disabled = false;
                            lifetimeIndicator.style.display = 'none';
                            durationInput.value = '';
                        }
                    });
                }
            });
        }

        async function logout() {
            const minLoadingTime = new Promise(resolve => setTimeout(resolve, 1000));
            try {
                const response = await fetch('logout.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' }
                });
                const [data] = await Promise.all([response.json(), minLoadingTime]);
                showToast(data.message, data.success);
                if (data.success) {
                    setTimeout(() => {
                        window.location.href = 'index.php';
                    }, 1000);
                }
            } catch (error) {
                showToast('Erro ao fazer logout: ' + error.message, false);
                console.error('Erro detalhado:', error);
            }
        }

        async function generateUser() {
            const username = document.getElementById('gen-username').value.trim();
            const password = document.getElementById('gen-password').value.trim();
            const duration = document.getElementById('gen-duration').value;
            const durationType = document.getElementById('gen-duration-type').value;
            const max_devices = document.getElementById('gen-devices').value;
            const product_id = document.getElementById('gen-product').value;

            if (!username) {
                showToast('O campo Username é obrigatório!', false);
                return;
            }
            if (!password) {
                showToast('O campo Password é obrigatório!', false);
                return;
            }
            if (!duration && durationType !== 'lifetime') {
                showToast('O campo Duração é obrigatório!', false);
                return;
            }
            if (!max_devices) {
                showToast('O campo Max Devices é obrigatório!', false);
                return;
            }
            if (!product_id) {
                showToast('Selecione um produto!', false);
                return;
            }
 
            const durationValue = durationType === 'lifetime' ? 999 : parseInt(duration);
            if (isNaN(durationValue) || durationValue <= 0) {
                showToast('Duração deve ser um número maior que 0!', false);
                return;
            }
            const maxDevicesValue = parseInt(max_devices);
            if (isNaN(maxDevicesValue) || maxDevicesValue <= 0) {
                showToast('Max Devices deve ser um número maior que 0!', false);
                return;
            }

            const minLoadingTime = new Promise(resolve => setTimeout(resolve, 1000));

            try {
                const response = await fetch('generate_user.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ username, password, duration: durationValue, durationType, max_devices: maxDevicesValue, product_id })
                });
                if (!response.ok) {
                    throw new Error(`HTTP error: ${response.status}`);
                }
                const [data] = await Promise.all([response.json(), minLoadingTime]);
                console.log('Resposta do generate_user:', data);
                showToast(data.message, data.success);
                if (data.success) {
                    clearForm('gen');
                    loadUserInfo();
                }
            } catch (error) {
                showToast('Erro ao conectar ao servidor: ' + error.message, false);
                console.error('Erro detalhado:', error);
            }
        }

        async function generateKey() {
            const duration = document.getElementById('key-duration').value;
            const durationType = document.getElementById('key-duration-type').value;
            const max_uses = document.getElementById('key-uses').value;
            const key_type = document.getElementById('key-type').value;
            const product_id = document.getElementById('key-product').value;

            if (!duration && durationType !== 'lifetime') {
                showToast('Duração é obrigatória!', false);
                return;
            }
            if (!max_uses) {
                showToast('Máximo de usos é obrigatório!', false);
                return;
            }
            if (!product_id) {
                showToast('Selecione um produto!', false);
                return;
            }

            const durationValue = durationType === 'lifetime' ? 999 : parseInt(duration);
            if (isNaN(durationValue) || durationValue <= 0) {
                showToast('Duração deve ser maior que 0!', false);
                return;
            }
            if (parseInt(max_uses) <= 0) {
                showToast('Máximo de usos deve ser maior que 0!', false);
                return;
            }

            const minLoadingTime = new Promise(resolve => setTimeout(resolve, 1000));

            try {
                const controller = new AbortController();
                const timeoutId = setTimeout(() => controller.abort(), 10000);
                const response = await fetch('generate_key.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ duration: durationValue, max_uses, key_type, product_id }),
                    signal: controller.signal
                });
                clearTimeout(timeoutId);
                if (!response.ok) {
                    throw new Error(`HTTP error: ${response.status}`);
                }
                const [data] = await Promise.all([response.json(), minLoadingTime]);
                console.log('Resposta do generate_key:', data);
                showToast(data.message, data.success);
                if (data.success) {
                    document.getElementById('generated-key').value = data.key;
                    document.getElementById('copy-key-btn').style.display = 'inline-block';
                    document.getElementById('key-duration').value = '';
                    document.getElementById('key-uses').value = '';
                    document.getElementById('key-type').value = 'standard';
                    document.getElementById('key-product').value = '1';
                    await loadKeys();
                }
            } catch (error) {
                showToast('Erro ao gerar chave: ' + error.message, false);
                console.error('Erro detalhado:', error);
            }
        }

        async function loadKeys() {
            try {
                const response = await fetch('get_keys.php', {
                    method: 'GET',
                    headers: { 'Content-Type': 'application/json' }
                });
                if (!response.ok) {
                    throw new Error(`Erro HTTP: ${response.status}`);
                }
                const data = await response.json();
                console.log('Resposta do get_keys:', data);
                if (!data.success) {
                    showToast(data.message, false);
                    return;
                }
                const tbody = document.getElementById('keys-body');
                tbody.innerHTML = '';
                data.keys.forEach(key => {
                    const row = document.createElement('tr');
                    row.innerHTML = `
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-primary">${key.key_code}</td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-secondary">${key.created_at}</td>
                        <td class="px-4 py-3 whitespace-nowrap"><span class="badge badge-${key.is_redeemed === 'Sim' ? 'success' : 'danger'}">${key.is_redeemed}</span></td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-secondary">${key.redeemed_by_username || 'N/A'}</td>
                    `;
                    tbody.appendChild(row);
                });
            } catch (error) {
                showToast('Erro ao carregar chaves: ' + error.message, false);
                console.error('Erro detalhado:', error);
            }
        }


        function copyKey() {
            const keyInput = document.getElementById('generated-key');
            keyInput.select();
            document.execCommand('copy');
            showToast('Chave copiada para a área de transferência!', true);
        }

        async function manageUser(action) {
            const username = document.getElementById('manage-username').value;
            if (!username) {
                showToast('Digite um username!', false);
                return;
            }
            await manageUserAction(username, action, 'manage');
        }

        async function manageUserFromPanel(action) {
            await manageUserAction(currentUsername, action, 'panel');
            loadUserInfo();
        }

        async function manageUserAction(username, action, section) {
            let body = { action, username };
            const prefix = section === 'panel' ? 'panel-' : '';
            switch (action) {
                case 'change_username':
                    body.value = document.getElementById(`${prefix}new-username`).value;
                    if (!body.value) return showToast('Digite um novo username!', false);
                    break;
                case 'change_password':
                    body.value = document.getElementById(`${prefix}new-password`).value;
                    if (!body.value) return showToast('Digite uma nova senha!', false);
                    break;
                case 'add_duration':
                    body.value = document.getElementById(`${prefix}days-add`).value;
                    body.durationType = document.getElementById(`${prefix}duration-type`).value;
                    if (!body.value && body.durationType !== 'lifetime') return showToast('Digite a quantidade de duração!', false);
                    body.value = body.durationType === 'lifetime' ? 999 : parseInt(body.value);
                    if (isNaN(body.value) || body.value <= 0) return showToast('Duração deve ser maior que 0!', false);
                    break;
                case 'remove_duration':
                    body.value = -Math.abs(document.getElementById(`${prefix}days-remove`).value);
                    body.durationType = document.getElementById(`${prefix}remove-duration-type`).value;
                    if (!body.value) return showToast('Digite a quantidade de duração!', false);
                    break;
                case 'add_devices':
                    body.value = document.getElementById(`${prefix}devices-add`).value;
                    if (!body.value) return showToast('Digite a quantidade de devices!', false);
                    break;
                case 'remove_devices':
                    body.value = -Math.abs(document.getElementById(`${prefix}devices-remove`).value);
                    if (!body.value) return showToast('Digite a quantidade de devices!', false);
                    break;
                case 'delete':
                    body.value = null;
                    break;
            }

            const minLoadingTime = new Promise(resolve => setTimeout(resolve, 1000));

            try {
                const response = await fetch('manage_user.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(body)
                });
                if (!response.ok) {
                    throw new Error(`HTTP error: ${response.status}`);
                }
                const [data] = await Promise.all([response.json(), minLoadingTime]);
                console.log('Resposta do manage_user:', data);
                showToast(data.message, data.success);
                if (data.success) {
                    clearForm(section);
                    if (section === 'panel') closeManagePanel();
                    loadUserInfo();
                }
            } catch (error) {
                showToast('Erro ao gerenciar usuário: ' + error.message, false);
                console.error('Erro detalhado:', error);
            }
        }

        function clearForm(section) {
            const prefix = section === 'panel' ? 'panel-' : '';
            if (section === 'gen') {
                document.getElementById('gen-username').value = '';
                document.getElementById('gen-password').value = '';
                document.getElementById('gen-duration').value = '';
                document.getElementById('gen-devices').value = '';
                document.getElementById('gen-duration-type').value = 'days';
                document.getElementById('gen-lifetime-indicator').style.display = 'none';
                document.getElementById('gen-duration').disabled = false;
            } else {
                document.getElementById(`${prefix}new-username`).value = '';
                document.getElementById(`${prefix}new-password`).value = '';
                document.getElementById(`${prefix}days-add`).value = '';
                document.getElementById(`${prefix}days-remove`).value = '';
                document.getElementById(`${prefix}devices-add`).value = '';
                document.getElementById(`${prefix}devices-remove`).value = '';
                if (section === 'manage' || section === 'panel') {
                    document.getElementById(`${prefix}duration-type`).value = 'days';
                    document.getElementById(`${prefix}remove-duration-type`).value = 'days';
                    document.getElementById(`${prefix}lifetime-indicator`).style.display = 'none';
                    document.getElementById(`${prefix}days-add`).disabled = false;
                }
            }
        }

        function showManagePanel(username) {
            currentUsername = username;
            document.getElementById('manage-panel-username').textContent = username;
            document.getElementById('manage-panel').style.display = 'block';
            clearForm('panel');
        }

        function closeManagePanel() {
            document.getElementById('manage-panel').style.display = 'none';
            currentUsername = '';
        }

        async function loadUserInfo() {
            try {
                const response = await fetch('get_users.php', {
                    method: 'GET',
                    headers: { 'Content-Type': 'application/json' }
                });
                if (!response.ok) {
                    throw new Error(`Erro HTTP: ${response.status}`);
                }
                const users = await response.json();
                console.log('Resposta do get_users:', users);
                if (!Array.isArray(users)) {
                    showToast('Erro ao carregar usuários: ' + (users.message || 'Dados inválidos'), false);
                    return;
                }

                const totalClients = users.length;
                document.getElementById('total-clients').textContent = totalClients;
                const totalLogins = users.length;
                document.getElementById('total-logins').textContent = totalLogins;
                const totalBanned = users.filter(user => user.banned).length;
                document.getElementById('total-banned').textContent = totalBanned;
                const now = new Date();
                const totalExpired = users.filter(user => new Date(user.expires_at) < now).length;
                document.getElementById('total-expired').textContent = totalExpired;

                const tbody = document.getElementById('user-info-body');
                tbody.innerHTML = '';
                users.forEach(user => {
                    const createdAt = new Date(user.created_at);
                    const expiresAt = new Date(user.expires_at);
                    const durationLeft = calculateDurationLeft(expiresAt, now);
                    const row = document.createElement('tr');
                    row.innerHTML = `
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-primary">${user.username}</td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-secondary">${createdAt.toLocaleString('pt-BR')}</td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-secondary">${expiresAt.toLocaleString('pt-BR')}</td>
                        <td class="px-4 py-3 whitespace-nowrap"><span class="badge badge-${durationLeft === 'Expirado' ? 'danger' : 'success'}">${durationLeft}</span></td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-secondary">${user.max_devices}</td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-secondary">${user.hwid || 'Nenhum'}</td>
                        <td class="px-4 py-3 whitespace-nowrap"><span class="badge badge-${user.banned ? 'danger' : 'success'}">${user.banned ? 'Sim' : 'Não'}</span></td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm font-medium">
                            <a href="#" class="text-indigo-700 hover:text-indigo-900 mr-3" onclick="showManagePanel('${user.username}')">
                                <i class="fas fa-edit"></i>
                            </a>
                            <a href="#" class="text-red-600 hover:text-red-800" onclick="if(confirm('Tem certeza que deseja deletar este usuário?')) manageUserAction('${user.username}', 'delete', 'manage')">
                                <i class="fas fa-trash"></i>
                            </a>
                        </td>
                    `;
                    tbody.appendChild(row);
                });

               
                const searchInput = document.getElementById('userSearchInput');
                searchInput.addEventListener('input', () => {
                    const searchTerm = searchInput.value.toLowerCase();
                    const rows = tbody.querySelectorAll('tr');
                    rows.forEach(row => {
                        const username = row.cells[0].textContent.toLowerCase();
                        row.style.display = username.includes(searchTerm) ? '' : 'none';
                    });
                });

                await updateServerStatus();
            } catch (error) {
                showToast('Erro ao carregar informações: ' + error.message, false);
                console.error('Erro detalhado:', error);
            }
        }

        function calculateDurationLeft(expiresAt, now) {
            const diffMs = expiresAt - now;
            if (diffMs < 0) return 'Expirado';
            const seconds = Math.floor(diffMs / 1000);
            const minutes = Math.floor(seconds / 60);
            const hours = Math.floor(minutes / 60);
            const days = Math.floor(hours / 24);
            const weeks = Math.floor(days / 7);
            const months = Math.floor(days / 30);

            if (months >= 1) return `${months} ${months === 1 ? 'mês' : 'meses'}`;
            if (weeks >= 1) return `${weeks} ${weeks === 1 ? 'semana' : 'semanas'}`;
            if (days >= 1) return `${days} ${days === 1 ? 'dia' : 'dias'}`;
            if (hours >= 1) return `${hours} ${hours === 1 ? 'hora' : 'horas'}`;
            return `${seconds} ${seconds === 1 ? 'segundo' : 'segundos'}`;
        }

        async function serverAction(action) {
            let body = { action };
            let message = 'Processando ação do servidor...';
            if (action === 'add_days_all') {
                const duration = document.getElementById('server-duration').value;
                const durationType = document.getElementById('duration-type').value;
                if (!duration && durationType !== 'lifetime') {
                    showToast('Duração é obrigatória!', false);
                    return;
                }
                body.days = durationType === 'lifetime' ? 999 : parseInt(duration);
                if (isNaN(body.days) || body.days <= 0) {
                    showToast('Duração deve ser maior que 0!', false);
                    return;
                }
                message = 'Adicionando duração a todos os usuários...';
            } else if (action === 'delete_expired_users') {
                message = 'Deletando usuários expirados...';
            } else if (action === 'pause_all') {
                message = 'Pausando logins...';
            } else if (action === 'unpause_all') {
                message = 'Despausando logins...';
            } else if (action === 'reset_all_hwids') {
                message = 'Resetando HWIDs...';
            }

            const minLoadingTime = new Promise(resolve => setTimeout(resolve, 1000));

            try {
                const response = await fetch('server_management.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(body)
                });
                if (!response.ok) {
                    throw new Error(`HTTP error: ${response.status}`);
                }
                const [data] = await Promise.all([response.json(), minLoadingTime]);
                console.log('Resposta do server_management:', data);
                showToast(data.message, data.success);
                if (data.success) {
                    loadUserInfo();
                    if (action === 'add_days_all') {
                        document.getElementById('server-duration').value = '';
                    }
                }
            } catch (error) {
                showToast('Erro ao executar ação do servidor: ' + error.message, false);
                console.error('Erro detalhado:', error);
            }
        }

        async function toggleCheat() {
            const button = document.getElementById('cheat-toggle-btn');
            const text = document.getElementById('cheat-toggle-text');
            const isOn = text.textContent.includes('Ligar');
            const action = isOn ? 'unpause_cheat' : 'pause_cheat';
            const message = isOn ? 'Ligando servidor...' : 'Desligando servidor...';

            const minLoadingTime = new Promise(resolve => setTimeout(resolve, 1000));

            try {
                const response = await fetch('server_management.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ action })
                });
                if (!response.ok) {
                    throw new Error(`HTTP error: ${response.status}`);
                }
                const [data] = await Promise.all([response.json(), minLoadingTime]);
                console.log('Resposta do server_management (toggleCheat):', data);
                showToast(data.message, data.success);
                if (data.success) {
                    text.textContent = isOn ? 'Desligar servidor' : 'Ligar servidor';
                    button.classList.toggle('btn-danger', !isOn);
                    button.classList.toggle('btn-success', isOn);
                    await updateServerStatus();
                }
            } catch (error) {
                showToast('Erro ao alternar servidor: ' + error.message, false);
                console.error('Erro detalhado:', error);
            }
        }


        async function updateServerStatus() {
            try {
                const response = await fetch('server_status.php', {
                    method: 'GET',
                    headers: { 'Content-Type': 'application/json' }
                });
                if (!response.ok) {
                    throw new Error(`HTTP error: ${response.status}`);
                }
                const data = await response.json();
                console.log('Resposta do server_status:', data);
                if (data.success) {
                    const serverStatus = document.getElementById('server-status');
                    const loginStatus = document.getElementById('login-status');
                    serverStatus.textContent = data.server_status ? 'Online' : 'Offline';
                    serverStatus.className = `badge badge-${data.server_status ? 'success' : 'danger'}`;
                    loginStatus.textContent = data.login_status ? 'Habilitado' : 'Desabilitado';
                    loginStatus.className = `badge badge-${data.login_status ? 'success' : 'danger'}`;
                    const cheatToggleText = document.getElementById('cheat-toggle-text');
                    const cheatToggleBtn = document.getElementById('cheat-toggle-btn');
                    cheatToggleText.textContent = data.server_status ? 'Desligar servidor' : 'Ligar servidor';
                    cheatToggleBtn.classList.remove('btn-danger', 'btn-success');
                    cheatToggleBtn.classList.add(data.server_status ? 'btn-danger' : 'btn-success');
                } else {
                    showToast('Erro ao verificar status: ' + (data.message || 'Falha no servidor'), false);
                }
            } catch (error) {
                showToast('Erro ao verificar status do servidor: ' + error.message, false);
                console.error('Erro detalhado:', error);
            }
        }

        function showManagePanel(username) {
            currentUsername = username;
            document.getElementById('manage-panel-username').textContent = username;
            document.getElementById('manage-panel').style.display = 'block';
            clearForm('panel');
        }

        function closeManagePanel() {
            document.getElementById('manage-panel').style.display = 'none';
            currentUsername = '';
        }

        document.getElementById('userSearchInput').addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            const rows = document.querySelectorAll('#user-info-body tr');
            rows.forEach(row => {
                const username = row.cells[0].textContent.toLowerCase();
                row.style.display = username.includes(searchTerm) ? '' : 'none';
            });
        });
        
   let currentTicketId = null;
let chatPollingInterval = null;
let lastMessageId = 0;

function openChat(ticketId) {
    currentTicketId = ticketId;
    lastMessageId = 0; 
    document.getElementById('chat-ticket-id').textContent = ticketId;
    document.getElementById('chat-section').style.display = 'block';
    document.getElementById('chat-messages').innerHTML = '';
    document.getElementById('chat-input').value = '';
    loadMessages();
    if (chatPollingInterval) clearInterval(chatPollingInterval);
    chatPollingInterval = setInterval(loadMessages, 5000);
}

function closeChat() {
    document.getElementById('chat-section').style.display = 'none';
    currentTicketId = null;
    lastMessageId = 0;
    if (chatPollingInterval) {
        clearInterval(chatPollingInterval);
        chatPollingInterval = null;
    }
}

async function loadMessages() {
    if (!currentTicketId) return;
    try {
        const response = await fetch('get_messages.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ ticket_id: currentTicketId, last_message_id: lastMessageId })
        });
        if (!response.ok) throw new Error(`HTTP error: ${response.status}`);
        const data = await response.json();
        if (!data.success) {
            showToast(data.message, false);
            return;
        }
        const messagesDiv = document.getElementById('chat-messages');
        data.messages.forEach(msg => {
            const messageDiv = document.createElement('div');
            messageDiv.className = `chat-message ${msg.sender_type === 'admin' ? 'admin' : 'client'}`;
            messageDiv.innerHTML = `
                <div class="message-bubble">${msg.message}</div>
                <div class="message-time">${new Date(msg.created_at).toLocaleString('pt-BR')}</div>
            `;
            messagesDiv.appendChild(messageDiv);
            lastMessageId = Math.max(lastMessageId, msg.id); 
        });
        messagesDiv.scrollTop = messagesDiv.scrollHeight;
    } catch (error) {
        showToast('Erro ao carregar mensagens: ' + error.message, false);
        console.error('Erro detalhado:', error);
    }
}

async function sendMessage() {
    if (!currentTicketId) return;
    const messageInput = document.getElementById('chat-input');
    const message = messageInput.value.trim();
    if (!message) {
        showToast('Digite uma mensagem!', false);
        return;
    }
    try {
        const response = await fetch('send_message.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                ticket_id: currentTicketId,
                sender_id: '<?php echo $_SESSION['admin_id']; ?>', 
                sender_type: 'admin',  
                message: message
            })
        });
        if (!response.ok) throw new Error(`HTTP error: ${response.status}`);
        const data = await response.json();
        showToast(data.message, data.success);
        if (data.success) {
            messageInput.value = '';
            loadMessages();
        }
    } catch (error) {
        showToast('Erro ao enviar mensagem: ' + error.message, false);
        console.error('Erro detalhado:', error);
    }
}

async function closeTicket(ticketId) {
    if (!confirm('Tem certeza que deseja fechar este ticket?')) return;
    try {
        const response = await fetch('close_ticket.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ ticket_id: ticketId })
        });
        if (!response.ok) throw new Error(`HTTP error: ${response.status}`);
        const data = await response.json();
        showToast(data.message, data.success);
        if (data.success) {
            location.reload();
        }
    } catch (error) {
        showToast('Erro ao fechar ticket: ' + error.message, false);
        console.error('Erro detalhado:', error);
    }
}
    </script>
</body>
</html>