<?php
// No session needed
?>

<!DOCTYPE html>
<html>
<head>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Scientific Calculator</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

<style>

/* 🌙 Default Dark */
body {
    transition: 0.3s;
    background: #0f2027;
    color: white;
}

.calculator {
    max-width: 400px;
    margin: 40px auto;
    padding: 20px;
    border-radius: 20px;
    background: #203a43;
    box-shadow: 0 10px 30px rgba(0,0,0,0.4);
}

.display {
    height: 60px;
    font-size: 22px;
    text-align: right;
    border-radius: 10px;
}

.btn-calc {
    width: 75px;
    height: 55px;
    font-size: 16px;
    border-radius: 12px;
    transition: 0.2s;
}

.btn-calc:hover {
    transform: scale(1.05);
}

/* ☀ Light Mode */
.light {
    background: #f5f7fa;
    color: black;
}

.light .calculator {
    background: white;
    box-shadow: 0 10px 25px rgba(0,0,0,0.1);
}

/* ⚡ Neon Mode */
.neon {
    background: #000;
    color: #0ff;
}

.neon .calculator {
    background: #111;
    box-shadow: 0 0 20px #0ff;
}

.neon .btn-calc {
    background: #000;
    color: #0ff;
    border: 1px solid #0ff;
}

.neon .btn-calc:hover {
    background: #0ff;
    color: #000;
}

/* History Panel */
.history-panel {
    max-width: 400px;
    margin: 20px auto;
    padding: 15px;
    border-radius: 15px;
    background: #203a43;
    max-height: 200px;
    overflow-y: auto;
    border: 2px solid #0ff;
}

.light .history-panel {
    background: #f0f0f0;
    border: 2px solid #007bff;
}

.neon .history-panel {
    background: #111;
    border: 2px solid #0ff;
}

.history-item {
    padding: 8px;
    margin: 5px 0;
    background: #1a2a32;
    border-radius: 8px;
    font-size: 14px;
    cursor: pointer;
    transition: 0.2s;
}

.history-item:hover {
    background: #2a4a52;
    transform: translateX(5px);
}

.light .history-item {
    background: white;
    border: 1px solid #ddd;
}

.light .history-item:hover {
    background: #e9ecef;
}

.neon .history-item {
    background: #000;
    color: #0ff;
    border: 1px solid #0ff;
}

.neon .history-item:hover {
    background: #0ff;
    color: #000;
}

</style>
</head>

<body>

<!-- 🎨 Theme Switch -->
<div class="text-center mt-3">
    <button class="btn btn-secondary btn-sm" onclick="setTheme('dark')">🌙 Dark</button>
    <button class="btn btn-light btn-sm" onclick="setTheme('light')">☀ Light</button>
    <button class="btn btn-info btn-sm" onclick="setTheme('neon')">⚡ Neon</button>
</div>

<div class="calculator text-center">

<h4>🧮 Scientific Calculator</h4>

<input type="text" id="display" class="form-control display mb-3" readonly>

<div class="d-flex flex-wrap gap-2 justify-content-center">

<!-- Numbers -->
<?php
$nums = ['7','8','9','4','5','6','1','2','3','0','.'];
foreach ($nums as $n) {
    echo "<button class='btn btn-light btn-calc' onclick='add(\"$n\")'>$n</button>";
}
?>

<!-- Basic Operators -->
<button class="btn btn-warning btn-calc" onclick="add('+')">+</button>
<button class="btn btn-warning btn-calc" onclick="add('-')">-</button>
<button class="btn btn-warning btn-calc" onclick="add('*')">*</button>
<button class="btn btn-warning btn-calc" onclick="add('/')">/</button>

<!-- Scientific -->
<button class="btn btn-info btn-calc" onclick="add('Math.sin(')">sin</button>
<button class="btn btn-info btn-calc" onclick="add('Math.cos(')">cos</button>
<button class="btn btn-info btn-calc" onclick="add('Math.tan(')">tan</button>

<button class="btn btn-info btn-calc" onclick="add('Math.log10(')">log</button>
<button class="btn btn-info btn-calc" onclick="add('Math.sqrt(')">√</button>
<button class="btn btn-info btn-calc" onclick="add('**')">xʸ</button>

<button class="btn btn-secondary btn-calc" onclick="add('Math.PI')">π</button>
<button class="btn btn-secondary btn-calc" onclick="add('Math.E')">e</button>

<button class="btn btn-secondary btn-calc" onclick="add('(')">(</button>
<button class="btn btn-secondary btn-calc" onclick="add(')')">)</button>

<!-- Special -->
<button class="btn btn-danger btn-calc" onclick="clearDisplay()">C</button>
<button class="btn btn-dark btn-calc" onclick="del()">⌫</button>
<button class="btn btn-warning btn-calc" onclick="toggleHistory()">📋</button>
<button class="btn btn-outline-danger btn-calc" onclick="clearHistory()">🗑 HC</button>

<!-- Equal -->
<button class="btn btn-success btn-calc" onclick="calculate()">=</button>

</div>

<!-- History Section -->
<div id="historyContainer" style="display:none;" class="history-panel">
    <h6 style="text-align: left; margin-bottom: 10px;">📋 History</h6>
    <div id="historyList"></div>
</div>

</div>

<script>

let history = [];

// Load history from localStorage
function loadHistory() {
    const saved = localStorage.getItem("calcHistory");
    history = saved ? JSON.parse(saved) : [];
    displayHistory();
}

// Save history to localStorage
function saveHistory() {
    localStorage.setItem("calcHistory", JSON.stringify(history));
}

// Add to history
function addToHistory(expression, result) {
    history.unshift({
        expression: expression,
        result: result,
        time: new Date().toLocaleTimeString()
    });
    
    if (history.length > 50) {
        history.pop(); // Keep only last 50 items
    }
    
    saveHistory();
    displayHistory();
}

// Display history
function displayHistory() {
    const historyList = document.getElementById("historyList");
    historyList.innerHTML = "";
    
    if (history.length === 0) {
        historyList.innerHTML = "<p style='text-align:center; opacity:0.6;'>No history yet</p>";
        return;
    }
    
    history.forEach((item, index) => {
        const div = document.createElement("div");
        div.className = "history-item";
        div.innerHTML = `<strong>${item.expression}</strong> = <span style="color:#4ade80;">${item.result}</span><br><small style="opacity:0.6;">${item.time}</small>`;
        div.onclick = () => {
            document.getElementById("display").value = item.result;
        };
        historyList.appendChild(div);
    });
}

// Toggle history visibility
function toggleHistory() {
    const container = document.getElementById("historyContainer");
    container.style.display = container.style.display === "none" ? "block" : "none";
}

// Clear history
function clearHistory() {
    if (confirm("Clear all history?")) {
        history = [];
        saveHistory();
        displayHistory();
        document.getElementById("historyContainer").style.display = "none";
    }
}

// Input
function add(val) {
    document.getElementById("display").value += val;
}

// Clear
function clearDisplay() {
    document.getElementById("display").value = "";
}

// Delete
function del() {
    let d = document.getElementById("display");
    d.value = d.value.slice(0, -1);
}

// Calculate
function calculate() {
    let expr = document.getElementById("display").value;

    try {
        let result = eval(expr);
        document.getElementById("display").value = result;
        addToHistory(expr, result);
    } catch {
        document.getElementById("display").value = "Error";
    }
}

// Theme switch
function setTheme(mode) {
    document.body.classList.remove("light", "neon");

    if (mode === "light") {
        document.body.classList.add("light");
    } else if (mode === "neon") {
        document.body.classList.add("neon");
    }
}

// Keyboard support
document.addEventListener("keydown", function(e) {
    const allowed = "0123456789+-*/().";
    if (allowed.includes(e.key)) add(e.key);
    if (e.key === "Enter") calculate();
    if (e.key === "Backspace") del();
    if (e.key === "Escape") clearDisplay();
});

// Load history on page load
window.addEventListener("load", loadHistory);

</script>

</body>
</html>