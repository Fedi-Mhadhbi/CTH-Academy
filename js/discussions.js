// Discussion System JavaScript
let currentCourseId = 1; // This will be set dynamically when viewing a course
let currentDiscussionId = null;

// Initialize discussions when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    // Check if discussion section exists
    if (document.getElementById('courseDiscussion')) {
        initializeDiscussions();
    }
});

function initializeDiscussions() {
    const container = document.getElementById('courseDiscussion');
    
    // Inject the discussion HTML
    container.innerHTML = `
        <div class="discussion-section" id="discussionSection">
            <div class="discussion-header">
                <h2>💬 Course Discussion</h2>
                <button class="ask-question-btn" onclick="toggleQuestionForm()">Ask a Question</button>
            </div>

            <div class="question-form" id="questionForm">
                <h3 style="margin-bottom: 15px;">Ask Your Question</h3>
                <textarea id="questionInput" placeholder="Type your question here..."></textarea>
                <div class="form-buttons">
                    <button class="submit-btn" onclick="postQuestion()">Post Question</button>
                    <button class="cancel-btn" onclick="toggleQuestionForm()">Cancel</button>
                </div>
            </div>

            <div id="discussionsList">
                <div class="no-discussions">
                    <div class="no-discussions-icon">💭</div>
                    <p>Loading discussions...</p>
                </div>
            </div>
        </div>

        <div class="reply-modal" id="replyModal">
            <div class="reply-modal-content">
                <div class="modal-header">
                    <h3>Discussion Replies</h3>
                    <button class="close-modal" onclick="closeReplyModal()">×</button>
                </div>
                
                <div class="original-question" id="modalQuestion"></div>
                <div id="repliesList"></div>
                
                <div class="question-form active" style="margin-top: 25px;">
                    <h4 style="margin-bottom: 15px;">Your Reply</h4>
                    <textarea id="replyInput" placeholder="Type your reply..."></textarea>
                    <div class="form-buttons">
                        <button class="submit-btn" onclick="postReply()">Post Reply</button>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    // Load discussions
    loadDiscussions();
}

function toggleQuestionForm() {
    const form = document.getElementById('questionForm');
    form.classList.toggle('active');
    if (form.classList.contains('active')) {
        document.getElementById('questionInput').focus();
    }
}

async function loadDiscussions() {
    try {
        const res = await fetch(`backend/discussions/get_discussions.php?course_id=${currentCourseId}`);
        const data = await res.json();
        
        const container = document.getElementById('discussionsList');
        
        if (data.status === 'error') {
            container.innerHTML = `<div class="no-discussions"><p>Error: ${data.message}</p></div>`;
            return;
        }
        
        if (data.discussions.length === 0) {
            container.innerHTML = `
                <div class="no-discussions">
                    <div class="no-discussions-icon">💭</div>
                    <p>No questions yet. Be the first to ask!</p>
                </div>`;
            return;
        }
        
        let html = '';
        data.discussions.forEach(d => {
            const initials = (d.prenom.charAt(0) + d.nom.charAt(0)).toUpperCase();
            const answeredClass = d.is_answered == 1 ? 'answered' : '';
            const answeredBadge = d.is_answered == 1 ? '<span class="answered-badge">✓ Answered</span>' : '';
            
            html += `
                <div class="discussion-item ${answeredClass}">
                    <div class="discussion-meta">
                        <div class="author-info">
                            <div class="author-avatar">${initials}</div>
                            <div>
                                <div class="author-name">${d.prenom} ${d.nom}</div>
                                <div class="discussion-time">${d.created_at}</div>
                            </div>
                        </div>
                        ${answeredBadge}
                    </div>
                    <div class="question-text">${d.question}</div>
                    <div class="discussion-footer">
                        <button onclick="viewReplies(${d.id})">
                            💬 <span class="reply-count">${d.reply_count} ${d.reply_count == 1 ? 'reply' : 'replies'}</span>
                        </button>
                    </div>
                </div>`;
        });
        
        container.innerHTML = html;
        
    } catch (err) {
        console.error(err);
        document.getElementById('discussionsList').innerHTML = 
            '<div class="no-discussions"><p>Failed to load discussions</p></div>';
    }
}

async function postQuestion() {
    const question = document.getElementById('questionInput').value.trim();
    
    if (!question) {
        alert('Please enter a question');
        return;
    }
    
    try {
        const res = await fetch('backend/discussions/post_question.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify({
                course_id: currentCourseId,
                question: question
            })
        });
        
        const data = await res.json();
        
        if (data.status === 'success') {
            document.getElementById('questionInput').value = '';
            toggleQuestionForm();
            loadDiscussions();
            alert('✅ Question posted successfully!');
        } else {
            alert('❌ ' + data.message);
        }
    } catch (err) {
        console.error(err);
        alert('Failed to post question');
    }
}

async function viewReplies(discussionId) {
    currentDiscussionId = discussionId;
    
    try {
        const res = await fetch(`backend/discussions/get_replies.php?discussion_id=${discussionId}`);
        const data = await res.json();
        
        if (data.status === 'error') {
            alert('Error: ' + data.message);
            return;
        }
        
        const questionHtml = `
            <strong>${data.discussion.prenom} ${data.discussion.nom} asked:</strong>
            <p style="margin-top: 10px;">${data.discussion.question}</p>
        `;
        document.getElementById('modalQuestion').innerHTML = questionHtml;
        
        const repliesContainer = document.getElementById('repliesList');
        if (data.replies.length === 0) {
            repliesContainer.innerHTML = '<p style="text-align:center;color:#999;">No replies yet. Be the first to answer!</p>';
        } else {
            let html = '';
            data.replies.forEach(r => {
                const teacherBadge = r.is_teacher_reply == 1 ? '<span class="teacher-badge">TEACHER</span>' : '';
                const teacherClass = r.is_teacher_reply == 1 ? 'teacher-reply' : '';
                const initials = (r.prenom.charAt(0) + r.nom.charAt(0)).toUpperCase();
                
                html += `
                    <div class="reply-item ${teacherClass}">
                        <div class="author-info">
                            <div class="author-avatar" style="width:30px;height:30px;font-size:14px;">${initials}</div>
                            <div>
                                <strong>${r.prenom} ${r.nom}</strong> ${teacherBadge}
                                <div class="discussion-time">${r.created_at}</div>
                            </div>
                        </div>
                        <div class="reply-text">${r.reply}</div>
                        <button class="helpful-btn">👍 Helpful (${r.helpful_count})</button>
                    </div>`;
            });
            repliesContainer.innerHTML = html;
        }
        
        document.getElementById('replyModal').classList.add('active');
        
    } catch (err) {
        console.error(err);
        alert('Failed to load replies');
    }
}

function closeReplyModal() {
    document.getElementById('replyModal').classList.remove('active');
    document.getElementById('replyInput').value = '';
}

async function postReply() {
    const reply = document.getElementById('replyInput').value.trim();
    
    if (!reply) {
        alert('Please enter a reply');
        return;
    }
    
    try {
        const res = await fetch('backend/discussions/post_reply.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify({
                discussion_id: currentDiscussionId,
                reply: reply
            })
        });
        
        const data = await res.json();
        
        if (data.status === 'success') {
            document.getElementById('replyInput').value = '';
            viewReplies(currentDiscussionId);
            loadDiscussions();
            alert('✅ Reply posted successfully!');
        } else {
            alert('❌ ' + data.message);
        }
    } catch (err) {
        console.error(err);
        alert('Failed to post reply');
    }
}