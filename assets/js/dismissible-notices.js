document.addEventListener('DOMContentLoaded', function () {
    const dismissedNotices = PDN_Data.dismissed_notices || [];

    const notices = document.querySelectorAll('.notice');

    notices.forEach((notice) => {
        let noticeId = notice.id;

        if (!noticeId) {
            const noticeContent = notice.textContent || notice.innerText;
            noticeId = 'pdn-' + btoa(noticeContent.trim());
            notice.id = noticeId;
        }

        if (dismissedNotices.includes(noticeId)) {
            notice.style.display = 'none';
            return;
        }

        if (!notice.classList.contains('is-dismissible')) {
            notice.classList.add('is-dismissible');
            const button = document.createElement('button');
            button.type = 'button';
            button.classList.add('notice-dismiss');
            button.innerHTML = '<span class="screen-reader-text">Dismiss this notice.</span>';
            notice.appendChild(button);

            button.addEventListener('click', () => {
                notice.style.display = 'none';

                fetch(PDN_Data.ajax_url, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `action=pdn_dismiss_notice&nonce=${encodeURIComponent(PDN_Data.nonce)}&notice_id=${encodeURIComponent(noticeId)}`,
                })
                    .then((response) => response.json())
                    .then((data) => {
                        if (!data.success) {
                            console.error('Failed to persist dismissal:', data.data);
                        }
                    });
            });
        }
    });
});
