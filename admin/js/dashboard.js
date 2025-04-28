// Refresh dashboard data periodically
setInterval(() => {
    fetch('/admin/api/dashboard-stats.php')
        .then(response => response.json())
        .then(data => {
            // Update participant counts
            Object.entries(data.divisionStats).forEach(([division, stats]) => {
                const card = document.querySelector(`[data-division="${division}"]`);
                if (card) {
                    card.querySelector('.total-participants').textContent = stats.total;
                    card.querySelector('.ready-participants').textContent = stats.ready;
                    
                    const startButton = card.querySelector('.btn-primary');
                    if (startButton) {
                        startButton.disabled = stats.ready < 2;
                    }
                }
            });

            // Update active sessions
            const sessionsContainer = document.querySelector('.sessions-list');
            if (data.activeSessions.length === 0) {
                document.querySelector('.active-sessions')?.classList.add('hidden');
            } else {
                document.querySelector('.active-sessions')?.classList.remove('hidden');
                if (sessionsContainer) {
                    sessionsContainer.innerHTML = data.activeSessions
                        .map(session => `
                            <div class="session-item">
                                <span>${session.division} Division</span>
                                <span>Started: ${new Date(session.started_at).toLocaleTimeString()}</span>
                            </div>
                        `).join('');
                }
            }
        })
        .catch(error => console.error('Error updating dashboard:', error));
}, 5000); // Refresh every 5 seconds

// Category filter functionality
document.addEventListener('DOMContentLoaded', function() {
    const categoryFilter = document.getElementById('categoryFilter');
    if (categoryFilter) {
        categoryFilter.addEventListener('change', function() {
            const selectedCategory = this.value;
            const questionRows = document.querySelectorAll('table tr[data-category]');
            
            questionRows.forEach(row => {
                if (selectedCategory === '' || row.dataset.category === selectedCategory) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });
    }
});

// Update questions table with category information
function updateQuestionsTable(data) {
    const row = document.createElement('tr');
    row.dataset.category = question.category_id;
    
    const questionMeta = document.createElement('div');
    questionMeta.className = 'question-meta';
    
    const categorySpan = document.createElement('span');
    categorySpan.className = 'question-category';
    categorySpan.textContent = question.category_name;
    
    const idSpan = document.createElement('span');
    idSpan.className = 'question-id';
    idSpan.textContent = `#${question.id}`;
    
    questionMeta.appendChild(idSpan);
    questionMeta.appendChild(categorySpan);
}