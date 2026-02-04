const searchInput = document.getElementById('search');
const suggestions = document.getElementById('suggestions');

searchInput.addEventListener('input', function() {
    const term = this.value.trim();

    if (term.length === 0) {
        suggestions.style.display = 'none';
        return;
    }

    fetch('search.php?term=' + encodeURIComponent(term))
        .then(res => res.json())
        .then(data => {
            suggestions.innerHTML = '';
            if (data.length === 0) {
                suggestions.style.display = 'none';
                return;
            }

            data.forEach(item => {
                const div = document.createElement('div');
                div.textContent = item;
                div.addEventListener('click', function() {
                    searchInput.value = item;
                    suggestions.style.display = 'none';
                });
                suggestions.appendChild(div);
            });

            suggestions.style.display = 'block';
        });
});
