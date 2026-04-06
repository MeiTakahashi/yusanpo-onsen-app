import './bootstrap';

import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();

// いいねボタン（全ページ共通）
document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.like-btn').forEach(btn => {
        btn.addEventListener('click', function () {
            const onsenId = this.dataset.onsenId;
            fetch(`/onsens/${onsenId}/like`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json',
                },
            })
            .then(res => {
                if (res.status === 401) {
                    alert('ログインしてください');
                    return;
                }
                return res.json();
            })
            .then(data => {
                if (!data) return;
                this.querySelector('.heart').textContent = data.liked ? '❤️' : '🤍';
            });
        });
    });
});
