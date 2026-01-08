<style>
* {
	margin: 0;
	padding: 0;
	box-sizing: border-box;
}

body {
	font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
	background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
	min-height: 100vh;
	display: flex;
	justify-content: center;
	align-items: center;
	padding: 20px;
}

.chat-container {
	width: 100%;
	max-width: 500px;
	background: white;
	border-radius: 20px;
	box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
	overflow: hidden;
	display: flex;
	flex-direction: column;
	height: 600px;
}

.chat-header {
	background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
	color: white;
	padding: 20px;
	text-align: center;
	font-weight: 600;
	font-size: 18px;
}

.chat-messages {
	flex: 1;
	padding: 20px;
	overflow-y: auto;
	display: flex;
	flex-direction: column;
	gap: 15px;
}

.message {
	display: flex;
	align-items: flex-end;
	gap: 10px;
	opacity: 0;
	animation: fadeIn 0.4s ease forwards;
}

@keyframes fadeIn {
	from {
		opacity: 0;
		transform: translateY(10px);
	}

	to {
		opacity: 1;
		transform: translateY(0);
	}
}

.message.sent {
	flex-direction: row-reverse;
}

.avatar {
	width: 35px;
	height: 35px;
	border-radius: 50%;
	background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
	display: flex;
	align-items: center;
	justify-content: center;
	color: white;
	font-weight: bold;
	font-size: 14px;
	flex-shrink: 0;
}

.message.sent .avatar {
	background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
}

.bubble {
	max-width: 70%;
	padding: 12px 16px;
	border-radius: 18px;
	word-wrap: break-word;
}

.message.received .bubble {
	background: #f0f0f0;
	color: #333;
	border-bottom-left-radius: 4px;
}

.message.sent .bubble {
	background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
	color: white;
	border-bottom-right-radius: 4px;
}

.typing-indicator {
	display: flex;
	gap: 4px;
	padding: 12px 16px;
	background: #f0f0f0;
	border-radius: 18px;
	width: fit-content;
	border-bottom-left-radius: 4px;
}

.typing-indicator span {
	width: 8px;
	height: 8px;
	border-radius: 50%;
	background: #999;
	animation: typing 1.4s infinite;
}

.typing-indicator span:nth-child(2) {
	animation-delay: 0.2s;
}

.typing-indicator span:nth-child(3) {
	animation-delay: 0.4s;
}

@keyframes typing {

	0%,
	60%,
	100% {
		transform: translateY(0);
	}

	30% {
		transform: translateY(-10px);
	}
}

.invitation-card {
	background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
	color: white;
	padding: 20px;
	border-radius: 15px;
	text-align: center;
	margin-top: 10px;
}

.invitation-card h2 {
	font-size: 24px;
	margin-bottom: 15px;
}

.invitation-card .detail {
	margin: 10px 0;
	font-size: 16px;
}

.invitation-card .detail strong {
	display: block;
	font-size: 14px;
	opacity: 0.9;
	margin-bottom: 3px;
}

.response-buttons {
	display: flex;
	gap: 15px;
	margin-top: 20px;
	padding: 20px;
	justify-content: center;
}

.btn {
	flex: 1;
	padding: 14px 24px;
	border: none;
	border-radius: 12px;
	font-size: 16px;
	font-weight: 600;
	cursor: pointer;
	transition: all 0.3s ease;
	max-width: 200px;
}

.btn-accept {
	background: linear-gradient(135deg, #4ade80, #22c55e);
	color: white;
	box-shadow: 0 4px 15px rgba(34, 197, 94, 0.4);
}

.btn-accept:hover {
	background: linear-gradient(135deg, #22c55e, #16a34a);
	transform: translateY(-2px);
	box-shadow: 0 8px 25px rgba(34, 197, 94, 0.5);
}

.btn-decline {
	background: transparent;
	color: #ef4444;
	border: 2px solid #ef4444;
	box-shadow: 0 4px 15px rgba(239, 68, 68, 0.2);
}

.btn-decline:hover {
	background: #ef4444;
	color: white;
	transform: translateY(-2px);
	box-shadow: 0 8px 25px rgba(239, 68, 68, 0.4);
}
</style>


<div class="chat-container">
	<div class="chat-header">
		Party Planner 🎉
	</div>
	<div class="chat-messages" id="chatMessages"></div>
	<div class="response-buttons" id="responseButtons" style="display: none;">
		<button class="btn btn-accept" onclick="acceptInvitation()">✓
			{{__('admin.accept-invitation')}}</button>
		<button class="btn btn-decline" onclick="declineInvitation()">✗
			{{__('admin.refuse-invitation')}}</button>
	</div>
</div>

@php
$invitationHtml = '<div class="invitation-card">';
	$invitationHtml .= '<h2>🎊 ' . __('admin.you-are-invited') . ' 🎊</h2>';
	$invitationHtml .= '<div class="detail"><strong>' . __('admin.event-name') . '</strong>' .
		($invitation->event_name ?? __('admin.event-name')) . '</div>';
	if($invitation->date) {
	$invitationHtml .= '<div class="detail"><strong>' . __('admin.date') . '</strong>' . $invitation->date . '
	</div>';
	}
	if($invitation->time) {
	$invitationHtml .= '<div class="detail"><strong>' . __('admin.time') . '</strong>' .
		\Carbon\Carbon::parse($invitation->time)->format('h:i A') . '</div>';
	}
	if($invitation->address) {
	$invitationHtml .= '<div class="detail"><strong>' . __('admin.address') . '</strong>' .
		nl2br(e($invitation->address)) . '</div>';
	}
	if(isset($host_name) && $host_name) {
	$invitationHtml .= '<div class="detail"><strong>' . __('admin.host-name') . '</strong>' . $host_name . '</div>
	';
	}
	if($invitation->description) {
	$invitationHtml .= '<div class="detail" style="margin-top: 15px; font-size: 14px;">' .
		$invitation->description . '</div>';
	}
	$invitationHtml .= '</div>';
$invitationHtmlJs = addslashes($invitationHtml);
@endphp
<script>
const messages = [{
		type: 'received',
		text: 'Hey! 👋',
		delay: 1000
	},
	{
		type: 'sent',
		text: 'Hi there!',
		delay: 1500
	},
	{
		type: 'received',
		text: 'I have something exciting to share with you...',
		delay: 2000
	},
	{
		type: 'sent',
		text: 'Oh? What is it?',
		delay: 1500
	},
	{
		type: 'received',
		text: 'Are you free next Saturday evening?',
		delay: 2000
	},
	{
		type: 'sent',
		text: 'I think so, why?',
		delay: 1500
	},
	{
		type: 'received',
		text: "Perfect! Because you're invited to...",
		delay: 2500
	},
	{
		type: 'received',
		isInvitation: true,
		html: `{!! $invitationHtmlJs !!}`,
		delay: 2500
	}
];

const chatContainer = document.getElementById('chatMessages');
let currentIndex = 0;

function showTypingIndicator() {
	return new Promise(resolve => {
		const typingDiv = document.createElement('div');
		typingDiv.className = 'message received';
		typingDiv.innerHTML = `
                    <div class="avatar">P</div>
                    <div class="typing-indicator">
                        <span></span>
                        <span></span>
                        <span></span>
                    </div>
                `;
		chatContainer.appendChild(typingDiv);
		chatContainer.scrollTop = chatContainer.scrollHeight;

		setTimeout(() => {
			typingDiv.remove();
			resolve();
		}, 1500);
	});
}

async function displayMessage(msg) {
	if (msg.type === 'received') {
		await showTypingIndicator();
	}

	const messageDiv = document.createElement('div');
	messageDiv.className = `message ${msg.type}`;

	if (msg.isInvitation) {
		messageDiv.innerHTML = `
                    <div class="avatar">P</div>
                    <div class="bubble" style="max-width: 90%; background: transparent; padding: 0;">
                        ${msg.html}
                    </div>
                `;
	} else {
		const avatarLetter = msg.type === 'received' ? 'P' : 'Y';
		messageDiv.innerHTML = `
                    <div class="avatar">${avatarLetter}</div>
                    <div class="bubble">${msg.text}</div>
                `;
	}

	chatContainer.appendChild(messageDiv);
	chatContainer.scrollTop = chatContainer.scrollHeight;
}

async function startChat() {
	for (const msg of messages) {
		await new Promise(resolve => setTimeout(resolve, msg.delay));
		await displayMessage(msg);
	}
	// Show buttons after all messages are displayed
	setTimeout(() => {
		document.getElementById('responseButtons').style.display = 'flex';
	}, 1000);
}

startChat();
</script>