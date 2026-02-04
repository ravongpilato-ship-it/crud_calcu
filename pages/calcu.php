<?php
// Simple calculator page
?>
<!doctype html>
<html lang="en">
<head>
    <button onclick="history.back()">Back</button>

	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width,initial-scale=1">
	<title>Calculator</title>
	<style>
		:root{--bg:#f3f4f6;--panel:#ffffff;--accent:#2563eb;--btn:#e5e7eb}
		html,body{height:100%;margin:0;font-family:Segoe UI,Roboto,Arial}
		.wrap{min-height:100%;display:flex;align-items:center;justify-content:center;background:var(--bg);padding:32px}
		.calc{width:320px;background:var(--panel);box-shadow:0 6px 24px rgba(15,23,42,.08);border-radius:12px;padding:18px}
		.display{height:56px;background:#0f172a;color:#fff;border-radius:8px;padding:8px 12px;font-size:20px;display:flex;align-items:center;justify-content:flex-end;overflow:hidden}
		.keys{display:grid;grid-template-columns:repeat(4,1fr);gap:8px;margin-top:12px}
		button{height:52px;border:0;border-radius:8px;background:var(--btn);font-size:18px;cursor:pointer}
		button.op{background:#111827;color:#fff}
		button.equal{background:var(--accent);color:#fff;grid-column:span 2}
		button.clear{background:#ef4444;color:#fff}
		.small{font-size:14px}
	</style>
</head>
<body>
	<div class="wrap">
		<div class="calc" role="application" aria-label="Calculator">
			<div id="display" class="display" aria-live="polite">0</div>
			<div class="keys">
				<button class="clear" data-action="clear">C</button>
				<button data-action="back">⌫</button>
				<button data-value="%">%</button>
				<button class="op" data-value="/">÷</button>

				<button data-value="7">7</button>
				<button data-value="8">8</button>
				<button data-value="9">9</button>
				<button class="op" data-value="*">×</button>

				<button data-value="4">4</button>
				<button data-value="5">5</button>
				<button data-value="6">6</button>
				<button class="op" data-value="-">−</button>

				<button data-value="1">1</button>
				<button data-value="2">2</button>
				<button data-value="3">3</button>
				<button class="op" data-value="+">+</button>

				<button data-value="0" style="grid-column:span 2">0</button>
				<button data-value=".">.</button>
				<button class="equal" data-action="equals">=</button>
			</div>
		</div>
	</div>

	<script>
		(function(){
			const display = document.getElementById('display');
			let expr = '';

			function updateDisplay() {
				display.textContent = expr === '' ? '0' : expr;
			}

			function append(ch){
				if (ch === '%' ) { // convert percent to /100 when evaluating
					expr += '%';
					return updateDisplay();
				}
				expr += ch;
				updateDisplay();
			}

			function backspace(){ expr = expr.slice(0,-1); updateDisplay(); }
			function clearAll(){ expr = ''; updateDisplay(); }

			function safeEval(s){
				try{
					// Replace user-friendly symbols
					const replaced = s.replace(/×/g,'*').replace(/÷/g,'/').replace(/−/g,'-').replace(/%/g,'/100');
					// Disallow letters
					if (/[a-zA-Z]/.test(replaced)) return 'Error';
					const val = Function('return ('+replaced+')')();
					if (typeof val === 'number' && isFinite(val)) return String(val);
					return 'Error';
				}catch(e){return 'Error'}
			}

			document.querySelector('.keys').addEventListener('click', function(e){
				const btn = e.target.closest('button');
				if (!btn) return;
				const val = btn.dataset.value;
				const action = btn.dataset.action;
				if (action === 'clear') return clearAll();
				if (action === 'back') return backspace();
				if (action === 'equals') { expr = safeEval(expr); return updateDisplay(); }
				if (val) append(val);
			});

			// Keyboard support
			window.addEventListener('keydown', function(e){
				if (e.key === 'Enter' || e.key === '=') { e.preventDefault(); expr = safeEval(expr); updateDisplay(); return; }
				if (e.key === 'Backspace') { backspace(); return; }
				if (e.key === 'Escape') { clearAll(); return; }
				if (/^[0-9.+\-*/%()]$/.test(e.key)) { append(e.key); }
			});

			updateDisplay();
		})();
	</script>
</body>
</html>
