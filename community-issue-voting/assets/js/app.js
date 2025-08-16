document.addEventListener('DOMContentLoaded', () => {
  // Voting handlers
  document.body.addEventListener('click', async (ev) => {
    const btn = ev.target.closest('[data-vote]');
    if (!btn) return;
    const issueId = btn.getAttribute('data-issue-id');
    const voteType = btn.getAttribute('data-vote');
    if (!issueId || !voteType) return;

    try {
      const res = await fetch(`${window.BASE_URL || ''}/api/vote.php`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ issue_id: Number(issueId), vote_type: voteType })
      });
      const data = await res.json();
      if (!res.ok || !data.success) throw new Error(data.error || 'Vote failed');

      const container = document.querySelector(`[data-issue-card="${issueId}"]`);
      if (container) {
        const count = container.querySelector('[data-vote-count]');
        if (count) count.textContent = data.score;
      }
    } catch (err) {
      console.error(err);
      alert('Unable to register vote. Please try again.');
    }
  });

  // Comment form (issue detail)
  const commentForm = document.querySelector('#comment-form');
  if (commentForm) {
    commentForm.addEventListener('submit', async (ev) => {
      ev.preventDefault();
      const issueId = commentForm.querySelector('input[name="issue_id"]').value;
      const comment = commentForm.querySelector('textarea[name="comment"]').value.trim();
      if (!comment) return;
      try {
        const res = await fetch(`${window.BASE_URL || ''}/api/comment_create.php`, {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({ issue_id: Number(issueId), comment })
        });
        const data = await res.json();
        if (!res.ok || !data.success) throw new Error(data.error || 'Comment failed');
        const list = document.querySelector('#comments-list');
        if (list) {
          const div = document.createElement('div');
          div.className = 'comment';
          div.innerHTML = `
            <div class="meta">${data.username} · ${data.created_at}</div>
            <div class="text">${escapeHtml(data.comment)}</div>
          `;
          list.prepend(div);
          commentForm.reset();
        }
      } catch (err) {
        console.error(err);
        alert('Unable to post comment.');
      }
    });
  }
});

function escapeHtml(str) {
  return str
    .replaceAll('&', '&amp;')
    .replaceAll('<', '&lt;')
    .replaceAll('>', '&gt;')
    .replaceAll('"', '&quot;')
    .replaceAll("'", '&#039;');
}