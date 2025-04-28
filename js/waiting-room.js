document.addEventListener('DOMContentLoaded', () => {
    const logoutBtn = document.getElementById('logoutBtn');

    logoutBtn.addEventListener('click', () => {
        window.location.href = '/logout.php';
    });

    // Start periodic updates
    updateParticipantsList();
    setInterval(updateParticipantsList, 3000);
});

async function updateParticipantsList() {
    try {
        const response = await fetch('/api/get-participants.php');
        if (!response.ok) throw new Error('Network response was not ok');
        
        const data = await response.json();
        if (data.error) throw new Error(data.error);

        const participantsList = document.getElementById('participantsList');
        const currentParticipantId = document.body.dataset.participantId;

        if (!participantsList || !currentParticipantId) {
            throw new Error('Required DOM elements not found');
        }

        participantsList.innerHTML = data.participants.map(participant => `
            <li class="px-6 py-4 hover:bg-gray-50 ${participant.id === parseInt(currentParticipantId) ? 'bg-blue-50' : ''}">
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <div class="h-10 w-10 flex-shrink-0">
                            <span class="inline-flex h-10 w-10 items-center justify-center rounded-full bg-primary-100">
                                <span class="text-lg font-medium text-primary-700">${participant.name.charAt(0)}</span>
                            </span>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-900">
                                ${participant.name}
                                ${participant.id === parseInt(currentParticipantId) ? ' (You)' : ''}
                            </p>
                            <p class="text-sm text-gray-500">
                                ${participant.ready ? 'Ready' : 'Not Ready'}
                            </p>
                        </div>
                    </div>
                    ${participant.ready ? 
                        '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">Ready</span>' : 
                        '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">Waiting</span>'
                    }
                </div>
            </li>
        `).join('');

        if (data.quizActive) {
            window.location.href = 'quiz-session.php';
        }

    } catch (error) {
        console.error('Error updating participants:', error);
    }
}