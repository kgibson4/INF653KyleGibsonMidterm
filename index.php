<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quotes API Dashboard</title>
    <style>
        :root { --primary: #2563eb; --secondary: #64748b; --success: #16a34a; --danger: #dc2626; --dark: #1e293b; }
        body { font-family: 'Segoe UI', system-ui, sans-serif; background: #f8fafc; color: var(--dark); margin: 0; padding: 20px; }
        .container { max-width: 1100px; margin: 0 auto; }
        header { border-bottom: 2px solid #e2e8f0; margin-bottom: 30px; padding-bottom: 10px; display: flex; justify-content: space-between; align-items: center; }
        
        .grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 20px; }
        .card { background: white; padding: 20px; border-radius: 12px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1); border: 1px solid #e2e8f0; }
        .card h2 { margin-top: 0; font-size: 1.25rem; border-bottom: 1px solid #f1f5f9; padding-bottom: 10px; margin-bottom: 15px; }
        
        .form-group { margin-bottom: 12px; }
        label { display: block; font-size: 0.85rem; font-weight: 600; margin-bottom: 4px; color: var(--secondary); }
        input, select, textarea { width: 100%; padding: 8px; border: 1px solid #cbd5e1; border-radius: 6px; box-sizing: border-box; }
        
        .btn-group { display: flex; gap: 8px; margin-top: 15px; flex-wrap: wrap; }
        button { flex: 1; padding: 10px; border-radius: 6px; border: none; font-weight: 600; cursor: pointer; transition: all 0.2s; font-size: 0.8rem; min-width: 80px; }
        .btn-get { background: var(--primary); color: white; }
        .btn-post { background: var(--success); color: white; }
        .btn-put { background: #f59e0b; color: white; }
        .btn-delete { background: var(--danger); color: white; }
        button:hover { opacity: 0.9; transform: translateY(-1px); }

        #console-wrapper { margin-top: 30px; position: sticky; bottom: 20px; }
        #json-viewer { background: #0f172a; color: #38bdf8; padding: 20px; border-radius: 8px; overflow: auto; max-height: 400px; font-family: 'Fira Code', monospace; font-size: 0.9rem; border: 1px solid #334155; }
        .method-badge { padding: 2px 6px; border-radius: 4px; font-size: 0.7rem; text-transform: uppercase; margin-right: 10px; }
    </style>
</head>
<body>

<div class="container">
    <header>
        <h1>Quotes API Dashboard</h1>
        <div id="db-status">
            <?php
            include_once 'config/Database.php';
            $db = (new Database())->connect();
            echo $db ? "🟢 Connected to Postgres" : "🔴 DB Connection Error";
            ?>
        </div>
    </header>

    <div class="grid">
        <div class="card">
            <h2>Authors</h2>
            <div class="form-group">
                <label>Author ID (for GET/PUT/DELETE)</label>
                <input type="number" id="auth-id" placeholder="e.g. 5">
            </div>
            <div class="form-group">
                <label>Author Name (for POST/PUT)</label>
                <input type="text" id="auth-name" placeholder="e.g. John Doe">
            </div>
            <div class="btn-group">
                <button class="btn-get" onclick="apiCall('authors', 'GET', 'auth-id')">READ</button>
                <button class="btn-get" style="background:#6366f1" onclick="apiCall('authors', 'GET')">LIST ALL</button>
                <button class="btn-post" onclick="apiCall('authors', 'POST', null, {author: 'auth-name'})">CREATE</button>
                <button class="btn-put" onclick="apiCall('authors', 'PUT', null, {id: 'auth-id', author: 'auth-name'})">UPDATE</button>
                <button class="btn-delete" onclick="apiCall('authors', 'DELETE', null, {id: 'auth-id'})">DELETE</button>
            </div>
        </div>

        <div class="card">
            <h2>Categories</h2>
            <div class="form-group">
                <label>Category ID</label>
                <input type="number" id="cat-id" placeholder="e.g. 2">
            </div>
            <div class="form-group">
                <label>Category Name</label>
                <input type="text" id="cat-name" placeholder="e.g.Inspiration">
            </div>
            <div class="btn-group">
                <button class="btn-get" onclick="apiCall('categories', 'GET', 'cat-id')">READ</button>
                <button class="btn-get" style="background:#6366f1" onclick="apiCall('categories', 'GET')">LIST ALL</button>
                <button class="btn-post" onclick="apiCall('categories', 'POST', null, {category: 'cat-name'})">CREATE</button>
                <button class="btn-put" onclick="apiCall('categories', 'PUT', null, {id: 'cat-id', category: 'cat-name'})">UPDATE</button>
                <button class="btn-delete" onclick="apiCall('categories', 'DELETE', null, {id: 'cat-id'})">DELETE</button>
            </div>
        </div>

        <div class="card" style="grid-column: span 1;">
            <h2>Quotes</h2>
            <div class="form-group">
                <label>Quote ID</label>
                <input type="number" id="q-id">
            </div>
            <div class="form-group">
                <label>Quote Text</label>
                <textarea id="q-text" rows="2"></textarea>
            </div>
            <div class="form-group">
                <label>Author ID</label>
                <input type="number" id="q-auth-id">
            </div>
            <div class="form-group">
                <label>Category ID</label>
                <input type="number" id="q-cat-id">
            </div>
            <div class="btn-group">
                <button class="btn-get" onclick="apiCall('quotes', 'GET', 'q-id')">READ</button>
                <button class="btn-get" style="background:#6366f1" onclick="apiCall('quotes', 'GET')">LIST ALL</button>
                <button class="btn-get" style="background:#ec4899" onclick="apiCall('quotes', 'GET', null, null, true)">RANDOM</button>
                <button class="btn-post" onclick="apiCall('quotes', 'POST', null, {quote: 'q-text', author_id: 'q-auth-id', category_id: 'q-cat-id'})">CREATE</button>
                <button class="btn-put" onclick="apiCall('quotes', 'PUT', null, {id: 'q-id', quote: 'q-text', author_id: 'q-auth-id', category_id: 'q-cat-id'})">UPDATE</button>
                <button class="btn-delete" onclick="apiCall('quotes', 'DELETE', null, {id: 'q-id'})">DELETE</button>
            </div>
        </div>
    </div>

    <div id="console-wrapper">
        <h3 style="margin-bottom:10px">Server Response Console</h3>
        <pre id="json-viewer">// Results will appear here...</pre>
    </div>
</div>

<script>
async function apiCall(endpoint, method, idField = null, bodyMap = null, isRandom = false) {
    const viewer = document.getElementById('json-viewer');
    let url = `api/${endpoint}/`;
    
    // Handle GET parameters (ID or Random)
    if (method === 'GET') {
        const idVal = idField ? document.getElementById(idField).value : '';
        const params = new URLSearchParams();
        if (idVal) params.append('id', idVal);
        if (isRandom) params.append('random', 'true');
        
        // Add author/cat filters for quotes if fields aren't empty
        if(endpoint === 'quotes' && !idVal) {
            const aId = document.getElementById('q-auth-id').value;
            const cId = document.getElementById('q-cat-id').value;
            if(aId) params.append('author_id', aId);
            if(cId) params.append('category_id', cId);
        }
        
        if (params.toString()) url += `?${params.toString()}`;
    }

    const options = { method };
    
    // Handle POST/PUT/DELETE JSON bodies
    if (['POST', 'PUT', 'DELETE'].includes(method) && bodyMap) {
        const payload = {};
        for (const [key, fieldId] of Object.entries(bodyMap)) {
            payload[key] = document.getElementById(fieldId).value;
        }
        options.headers = { 'Content-Type': 'application/json' };
        options.body = JSON.stringify(payload);
    }

    viewer.textContent = `>> Sending ${method} to ${url}...`;

    try {
        const response = await fetch(url, options);
        const data = await response.json();
        viewer.textContent = JSON.stringify(data, null, 4);
    } catch (error) {
        viewer.textContent = `Error: ${error.message}`;
    }
}
</script>

</body>
</html>