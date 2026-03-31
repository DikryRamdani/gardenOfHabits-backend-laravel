<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>🌱 Garden Debug Panel</title>
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Segoe UI', sans-serif;
            background: #1a1a2e;
            color: #eee;
            min-height: 100vh;
        }

        header {
            background: #16213e;
            padding: 16px 24px;
            display: flex;
            align-items: center;
            gap: 12px;
            border-bottom: 2px solid #0f3460;
            position: sticky;
            top: 0;
            z-index: 100;
        }

        header h1 {
            font-size: 1.4rem;
            color: #4ecca3;
        }

        #token-badge {
            margin-left: auto;
            font-size: 0.75rem;
            padding: 4px 12px;
            border-radius: 20px;
            background: #0f3460;
            color: #aaa;
        }

        #token-badge.active {
            background: #1a5c3a;
            color: #4ecca3;
        }

        .layout {
            display: flex;
            gap: 0;
        }

        nav {
            width: 200px;
            min-height: calc(100vh - 57px);
            background: #16213e;
            padding: 12px 0;
            position: sticky;
            top: 57px;
            overflow-y: auto;
            flex-shrink: 0;
        }

        nav a {
            display: block;
            padding: 10px 20px;
            color: #aaa;
            text-decoration: none;
            font-size: 0.85rem;
            border-left: 3px solid transparent;
            transition: all .2s;
        }

        nav a:hover,
        nav a.active {
            color: #4ecca3;
            border-color: #4ecca3;
            background: #0f3460;
        }

        nav .nav-section {
            padding: 12px 20px 4px;
            font-size: 0.7rem;
            color: #555;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        main {
            flex: 1;
            padding: 24px;
            overflow: auto;
        }

        .section {
            display: none;
        }

        .section.active {
            display: block;
        }

        .card {
            background: #16213e;
            border: 1px solid #0f3460;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 16px;
        }

        .card h3 {
            color: #4ecca3;
            margin-bottom: 14px;
            font-size: 1rem;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 16px;
        }

        label {
            display: block;
            font-size: 0.8rem;
            color: #aaa;
            margin-bottom: 4px;
        }

        input,
        select,
        textarea {
            width: 100%;
            padding: 8px 12px;
            background: #0f3460;
            border: 1px solid #1a4080;
            border-radius: 8px;
            color: #eee;
            font-size: 0.85rem;
            margin-bottom: 10px;
            outline: none;
        }

        input:focus,
        select:focus,
        textarea:focus {
            border-color: #4ecca3;
        }

        textarea {
            resize: vertical;
            min-height: 60px;
        }

        button {
            padding: 8px 20px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
            font-size: 0.85rem;
            transition: all .2s;
        }

        .btn-primary {
            background: #4ecca3;
            color: #1a1a2e;
        }

        .btn-primary:hover {
            background: #38b08a;
        }

        .btn-danger {
            background: #e74c3c;
            color: #fff;
        }

        .btn-danger:hover {
            background: #c0392b;
        }

        .btn-warn {
            background: #f39c12;
            color: #1a1a2e;
        }

        .btn-warn:hover {
            background: #d68910;
        }

        .response {
            margin-top: 12px;
            background: #0a0f1e;
            border: 1px solid #1a4080;
            border-radius: 8px;
            padding: 12px;
        }

        .response pre {
            font-size: 0.78rem;
            white-space: pre-wrap;
            word-break: break-all;
            color: #7ec8e3;
        }

        .response.ok pre {
            color: #4ecca3;
        }

        .response.err pre {
            color: #e74c3c;
        }

        .badge {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 10px;
            font-size: 0.7rem;
            font-weight: 700;
        }

        .badge-get {
            background: #1a5c3a;
            color: #4ecca3;
        }

        .badge-post {
            background: #1a3a5c;
            color: #4e9cca;
        }

        .badge-patch {
            background: #4a3a1a;
            color: #ca9c4e;
        }

        .badge-delete {
            background: #5c1a1a;
            color: #ca4e4e;
        }

        #pomodoro-timer {
            font-size: 3rem;
            font-weight: 900;
            color: #4ecca3;
            text-align: center;
            padding: 20px;
            letter-spacing: 4px;
        }

        #timer-bar {
            height: 8px;
            background: #0f3460;
            border-radius: 4px;
            margin: 8px 0 16px;
        }

        #timer-fill {
            height: 100%;
            background: #4ecca3;
            border-radius: 4px;
            width: 100%;
            transition: width 1s linear;
        }

        .task-item {
            background: #0f3460;
            border-radius: 8px;
            padding: 12px 16px;
            margin-bottom: 8px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .task-item .task-title {
            flex: 1;
        }

        .task-item .task-badge {
            font-size: 0.7rem;
            padding: 2px 8px;
            border-radius: 10px;
        }

        .task-easy {
            background: #1a5c3a;
            color: #4ecca3;
        }

        .task-medium {
            background: #4a3a1a;
            color: #ca9c4e;
        }

        .task-hard {
            background: #5c1a1a;
            color: #ca4e4e;
        }

        .task-done {
            opacity: 0.5;
            text-decoration: line-through;
        }

        .streak-display {
            text-align: center;
        }

        .streak-display .streak-num {
            font-size: 3rem;
            font-weight: 900;
            color: #f39c12;
        }

        .streak-display .streak-label {
            color: #aaa;
            font-size: 0.85rem;
        }
    </style>
</head>

<body>

    <header>
        <span style="font-size:1.5rem">🌱</span>
        <h1>Garden of Habits — Debug Panel</h1>
        <div id="token-badge">No Token</div>
    </header>

    <div class="layout">
        <nav>
            <div class="nav-section">Auth</div>
            <a href="#" onclick="show('auth-register')">Register</a>
            <a href="#" onclick="show('auth-login')">Login</a>
            <a href="#" onclick="show('auth-logout')">Logout</a>
            <a href="#" onclick="show('auth-refresh')">Refresh Token</a>

            <div class="nav-section">User</div>
            <a href="#" onclick="show('user-me')">Get Me</a>

            <div class="nav-section">Garden</div>
            <a href="#" onclick="show('garden-show')">My Garden</a>

            <div class="nav-section">Tasks</div>
            <a href="#" onclick="show('task-list')">List Tasks</a>
            <a href="#" onclick="show('task-create')">Create Task</a>
            <a href="#" onclick="show('task-complete')">Complete Task</a>
            <a href="#" onclick="show('task-update')">Update Task</a>
            <a href="#" onclick="show('task-delete')">Delete Task</a>

            <div class="nav-section">Pomodoro</div>
            <a href="#" onclick="show('pomo-start')">Start Session</a>
            <a href="#" onclick="show('pomo-finish')">Finish Session</a>
            <a href="#" onclick="show('pomo-status')">Session Status</a>
        </nav>

        <main>

            {{-- AUTH: REGISTER --}}
            <div id="auth-register" class="section active">
                <div class="card">
                    <h3><span class="badge badge-post">POST</span> /api/register</h3>
                    <div class="grid">
                        <div>
                            <label>Username</label><input id="reg-username" placeholder="johndoe">
                            <label>Email</label><input id="reg-email" type="email" placeholder="john@example.com">
                            <label>Password</label><input id="reg-password" type="password" placeholder="password123">
                            <label>Confirm Password</label><input id="reg-confirm" type="password"
                                placeholder="password123">
                            <button class="btn-primary" onclick="register()">Register</button>
                        </div>
                        <div class="response" id="reg-res">
                            <pre>Response will appear here...</pre>
                        </div>
                    </div>
                </div>
            </div>

            {{-- AUTH: LOGIN --}}
            <div id="auth-login" class="section">
                <div class="card">
                    <h3><span class="badge badge-post">POST</span> /api/login</h3>
                    <div class="grid">
                        <div>
                            <label>Username</label><input id="login-username" type="username" placeholder="johndoe">
                            <label>Password</label><input id="login-password" type="password" placeholder="password123">
                            <button class="btn-primary" onclick="login()">Login</button>
                        </div>
                        <div class="response" id="login-res">
                            <pre>Response will appear here...</pre>
                        </div>
                    </div>
                </div>
            </div>

            {{-- AUTH: LOGOUT --}}
            <div id="auth-logout" class="section">
                <div class="card">
                    <h3><span class="badge badge-post">POST</span> /api/logout</h3>
                    <p style="color:#aaa;margin-bottom:14px;font-size:0.85rem">Membutuhkan token aktif.</p>
                    <button class="btn-danger" onclick="logout()">Logout</button>
                    <div class="response" id="logout-res" style="margin-top:12px">
                        <pre>Response will appear here...</pre>
                    </div>
                </div>
            </div>

            {{-- AUTH: REFRESH --}}
            <div id="auth-refresh" class="section">
                <div class="card">
                    <h3><span class="badge badge-post">POST</span> /api/refresh</h3>
                    <div class="grid">
                        <div>
                            <label>Refresh Token</label>
                            <textarea id="refresh-token"
                                placeholder="Paste refresh_token disini (otomatis terisi setelah login)"></textarea>
                            <button class="btn-primary" onclick="refreshToken()">Refresh</button>
                        </div>
                        <div class="response" id="refresh-res">
                            <pre>Response will appear here...</pre>
                        </div>
                    </div>
                </div>
            </div>

            {{-- USER: ME --}}
            <div id="user-me" class="section">
                <div class="card">
                    <h3><span class="badge badge-get">GET</span> /api/user</h3>
                    <button class="btn-primary" onclick="getUser()">Get My Profile</button>
                    <div class="response" id="user-res" style="margin-top:12px">
                        <pre>Response will appear here...</pre>
                    </div>
                </div>
            </div>

            {{-- GARDEN --}}
            <div id="garden-show" class="section">
                <div class="card">
                    <h3><span class="badge badge-get">GET</span> /api/garden</h3>
                    <button class="btn-primary" onclick="getGarden()">Get My Garden</button>
                    <div class="response" id="garden-res" style="margin-top:12px">
                        <pre>Response will appear here...</pre>
                    </div>
                </div>
            </div>

            {{-- TASK: LIST --}}
            <div id="task-list" class="section">
                <div class="card">
                    <h3><span class="badge badge-get">GET</span> /api/tasks</h3>
                    <button class="btn-primary" onclick="getTasks()">Load Tasks</button>
                    <div id="task-list-items" style="margin-top:14px"></div>
                    <div class="response" id="task-list-res" style="margin-top:12px">
                        <pre>Response will appear here...</pre>
                    </div>
                </div>
            </div>

            {{-- TASK: CREATE --}}
            <div id="task-create" class="section">
                <div class="card">
                    <h3><span class="badge badge-post">POST</span> /api/tasks</h3>
                    <div class="grid">
                        <div>
                            <label>Title</label><input id="task-title" placeholder="Belajar Laravel">
                            <label>Description</label><textarea id="task-desc" placeholder="Optional"></textarea>
                            <label>Difficulty</label>
                            <select id="task-diff">
                                <option value="easy">Easy (+10 EXP, +5 HP)</option>
                                <option value="medium">Medium (+20 EXP, +10 HP)</option>
                                <option value="hard">Hard (+30 EXP, +20 HP)</option>
                            </select>
                            <button class="btn-primary" onclick="createTask()">Create Task</button>
                        </div>
                        <div class="response" id="task-create-res">
                            <pre>Response will appear here...</pre>
                        </div>
                    </div>
                </div>
            </div>

            {{-- TASK: COMPLETE --}}
            <div id="task-complete" class="section">
                <div class="card">
                    <h3><span class="badge badge-patch">PATCH</span> /api/tasks/{id}/complete</h3>
                    <div class="grid">
                        <div>
                            <label>Task ID</label><input id="complete-id" type="number" placeholder="1">
                            <button class="btn-warn" onclick="completeTask()">✅ Mark Complete</button>
                        </div>
                        <div class="response" id="task-complete-res">
                            <pre>Response will appear here...</pre>
                        </div>
                    </div>
                </div>
            </div>

            {{-- TASK: UPDATE --}}
            <div id="task-update" class="section">
                <div class="card">
                    <h3><span class="badge badge-patch">PATCH</span> /api/tasks/{id}</h3>
                    <div class="grid">
                        <div>
                            <label>Task ID</label><input id="update-id" type="number" placeholder="1">
                            <label>Title</label><input id="update-title" placeholder="New title">
                            <label>Description</label><textarea id="update-desc"
                                placeholder="New description"></textarea>
                            <button class="btn-primary" onclick="updateTask()">Update Task</button>
                        </div>
                        <div class="response" id="task-update-res">
                            <pre>Response will appear here...</pre>
                        </div>
                    </div>
                </div>
            </div>

            {{-- TASK: DELETE --}}
            <div id="task-delete" class="section">
                <div class="card">
                    <h3><span class="badge badge-delete">DELETE</span> /api/tasks/{id}</h3>
                    <div class="grid">
                        <div>
                            <label>Task ID</label><input id="delete-id" type="number" placeholder="1">
                            <button class="btn-danger" onclick="deleteTask()">🗑️ Delete Task</button>
                        </div>
                        <div class="response" id="task-delete-res">
                            <pre>Response will appear here...</pre>
                        </div>
                    </div>
                </div>
            </div>

            {{-- POMODORO: START --}}
            <div id="pomo-start" class="section">
                <div class="card">
                    <h3><span class="badge badge-post">POST</span> /api/pomodoro/start</h3>
                    <p style="color:#aaa;font-size:0.85rem;margin-bottom:14px">Durasi: 25 menit. Timer akan berjalan
                        otomatis setelah start.</p>
                    <div id="pomodoro-timer">25:00</div>
                    <div id="timer-bar">
                        <div id="timer-fill"></div>
                    </div>
                    <div style="text-align:center;margin-bottom:16px">
                        <button class="btn-primary" onclick="startPomodoro()" id="pomo-start-btn">▶ Start
                            Pomodoro</button>
                    </div>
                    <div class="response" id="pomo-start-res">
                        <pre>Response will appear here...</pre>
                    </div>
                </div>
            </div>

            {{-- POMODORO: FINISH --}}
            <div id="pomo-finish" class="section">
                <div class="card">
                    <h3><span class="badge badge-post">POST</span> /api/pomodoro/finish</h3>
                    <p style="color:#aaa;font-size:0.85rem;margin-bottom:14px">Tandai sesi selesai. Streak akan
                        bertambah.</p>
                    <button class="btn-primary" onclick="finishPomodoro()">🏁 Finish Session</button>
                    <div class="response" id="pomo-finish-res" style="margin-top:12px">
                        <pre>Response will appear here...</pre>
                    </div>
                </div>
            </div>

            {{-- POMODORO: STATUS --}}
            <div id="pomo-status" class="section">
                <div class="card">
                    <h3><span class="badge badge-get">GET</span> /api/pomodoro/status</h3>
                    <button class="btn-primary" onclick="pomodoroStatus()">Check Status</button>
                    <div class="response" id="pomo-status-res" style="margin-top:12px">
                        <pre>Response will appear here...</pre>
                    </div>
                </div>
            </div>

        </main>
    </div>

    <script>
        @verbatim
            const BASE = 'http://127.0.0.1:8000/api';
            let token = localStorage.getItem('access_token') || '';
            let refreshTok = localStorage.getItem('refresh_token') || '';
            let timerInterval = null;
            let timerSeconds = 25 * 60;

            function updateBadge() {
                const badge = document.getElementById('token-badge');
                if (token) {
                    badge.textContent = '🔑 Token Active';
                    badge.classList.add('active');
                } else {
                    badge.textContent = 'No Token';
                    badge.classList.remove('active');
                }
            }
            updateBadge();

            function show(id) {
                document.querySelectorAll('.section').forEach(s => s.classList.remove('active'));
                document.querySelectorAll('nav a').forEach(a => a.classList.remove('active'));
                document.getElementById(id).classList.add('active');
                event.target.classList.add('active');
            }

            function res(id, data, ok) {
                const el = document.getElementById(id);
                el.className = 'response ' + (ok ? 'ok' : 'err');
                el.innerHTML = '<pre>' + JSON.stringify(data, null, 2) + '</pre>';
            }

            async function api(method, endpoint, body) {
                const headers = { 'Content-Type': 'application/json', 'Accept': 'application/json' };
                if (token) headers['Authorization'] = 'Bearer ' + token;
                const opts = { method, headers };
                if (body) opts.body = JSON.stringify(body);
                const r = await fetch(BASE + endpoint, opts);
                const data = await r.json();
                return { ok: r.ok, status: r.status, data };
            }

            async function register() {
                const r = await api('POST', '/register', {
                    username: document.getElementById('reg-username').value,
                    email: document.getElementById('reg-email').value,
                    password: document.getElementById('reg-password').value,
                    password_confirmation: document.getElementById('reg-confirm').value,
                });
                if (r.ok && r.data?.data) {
                    token = r.data.data.access_token;
                    refreshTok = r.data.data.refresh_token;
                    localStorage.setItem('access_token', token);
                    localStorage.setItem('refresh_token', refreshTok);
                    document.getElementById('refresh-token').value = refreshTok;
                    updateBadge();
                }
                res('reg-res', r.data, r.ok);
            }

            async function login() {
                const r = await api('POST', '/login', {
                    username: document.getElementById('login-username').value,
                    password: document.getElementById('login-password').value,
                });
                if (r.ok && r.data?.data) {
                    token = r.data.data.access_token;
                    refreshTok = r.data.data.refresh_token;
                    localStorage.setItem('access_token', token);
                    localStorage.setItem('refresh_token', refreshTok);
                    document.getElementById('refresh-token').value = refreshTok;
                    updateBadge();
                }
                res('login-res', r.data, r.ok);
            }

            async function logout() {
                const r = await api('POST', '/logout');
                // Always clear tokens locally so users aren't trapped
                token = ''; refreshTok = '';
                localStorage.removeItem('access_token');
                localStorage.removeItem('refresh_token');
                updateBadge();
                res('logout-res', r.data, r.ok);
            }

            async function refreshToken() {
                const rt = document.getElementById('refresh-token').value || refreshTok;
                const r = await api('POST', '/refresh', { refresh_token: rt });
                if (r.ok && r.data?.data) {
                    token = r.data.data.access_token;
                    refreshTok = r.data.data.refresh_token;
                    localStorage.setItem('access_token', token);
                    localStorage.setItem('refresh_token', refreshTok);
                    document.getElementById('refresh-token').value = refreshTok;
                    updateBadge();
                }
                res('refresh-res', r.data, r.ok);
            }

            async function getUser() {
                const r = await api('GET', '/user');
                res('user-res', r.data, r.ok);
            }

            async function getGarden() {
                const r = await api('GET', '/garden');
                res('garden-res', r.data, r.ok);
            }

            async function getTasks() {
                const r = await api('GET', '/tasks');
                res('task-list-res', r.data, r.ok);
                if (r.ok && r.data?.data?.task) {
                    const tasks = r.data.data.task;
                    const el = document.getElementById('task-list-items');
                    if (!tasks.length) { el.innerHTML = '<p style="color:#aaa;font-size:0.85rem">Belum ada task.</p>'; return; }
                    el.innerHTML = tasks.map(t => `
                                                            <div class="task-item ${t.is_completed ? 'task-done' : ''}">
                                                                <span class="task-badge task-${t.difficulty}">${t.difficulty}</span>
                                                                <span class="task-title">${t.title}</span>
                                                                <small style="color:#aaa">ID: ${t.id}</small>
                                                                ${!t.is_completed ? `<button class="btn-warn" style="padding:4px 10px;font-size:0.75rem" onclick="quickComplete(${t.id})">✅</button>` : ''}
                                                            </div>`).join('');
                }
            }

            async function quickComplete(id) {
                const r = await api('PATCH', '/tasks/' + id + '/complete');
                res('task-list-res', r.data, r.ok);
                getTasks();
            }

            async function createTask() {
                const r = await api('POST', '/tasks', {
                    title: document.getElementById('task-title').value,
                    description: document.getElementById('task-desc').value,
                    difficulty: document.getElementById('task-diff').value,
                });
                res('task-create-res', r.data, r.ok);
            }

            async function completeTask() {
                const id = document.getElementById('complete-id').value;
                const r = await api('PATCH', '/tasks/' + id + '/complete');
                res('task-complete-res', r.data, r.ok);
            }

            async function updateTask() {
                const id = document.getElementById('update-id').value;
                const r = await api('PATCH', '/tasks/' + id, {
                    title: document.getElementById('update-title').value,
                    description: document.getElementById('update-desc').value,
                });
                res('task-update-res', r.data, r.ok);
            }

            async function deleteTask() {
                const id = document.getElementById('delete-id').value;
                const r = await api('DELETE', '/tasks/' + id);
                res('task-delete-res', r.data, r.ok);
            }

            // --- POMODORO ---
            function formatTime(secs) {
                const m = String(Math.floor(secs / 60)).padStart(2, '0');
                const s = String(secs % 60).padStart(2, '0');
                return m + ':' + s;
            }

            async function startPomodoro() {
                const r = await api('POST', '/pomodoro/start');
                res('pomo-start-res', r.data, r.ok);
                if (r.ok) {
                    timerSeconds = 25 * 60;
                    clearInterval(timerInterval);
                    const btn = document.getElementById('pomo-start-btn');
                    btn.disabled = true;
                    btn.textContent = '⏳ Running...';
                    timerInterval = setInterval(() => {
                        timerSeconds--;
                        document.getElementById('pomodoro-timer').textContent = formatTime(timerSeconds);
                        document.getElementById('timer-fill').style.width = (timerSeconds / (25 * 60) * 100) + '%';

                        if (timerSeconds <= 0) {
                            clearInterval(timerInterval);
                            btn.disabled = false;
                            btn.textContent = '▶ Start Pomodoro';
                            autoFinish();
                        }
                    }, 1000);
                }
            }

            async function autoFinish() {
                const r = await api('POST', '/pomodoro/finish');
                document.getElementById('pomodoro-timer').textContent = '✅ Done!';
                res('pomo-start-res', { auto_finished: true, ...r.data }, r.ok);
            }

            async function finishPomodoro() {
                const r = await api('POST', '/pomodoro/finish');
                res('pomo-finish-res', r.data, r.ok);
                if (timerInterval) { clearInterval(timerInterval); timerInterval = null; }
                // Reset timer UI
                timerSeconds = 25 * 60;
                document.getElementById('pomodoro-timer').textContent = '25:00';
                document.getElementById('timer-fill').style.width = '100%';
                const btn = document.getElementById('pomo-start-btn');
                if (btn) { btn.disabled = false; btn.textContent = '▶ Start Pomodoro'; }
            }

            async function pomodoroStatus() {
                const r = await api('GET', '/pomodoro/status');
                res('pomo-status-res', r.data, r.ok);
            }

            // Restore refresh token dari localStorage
            if (refreshTok) document.getElementById('refresh-token').value = refreshTok;
        @endverbatim
    </script>
</body>

</html>