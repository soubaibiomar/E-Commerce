<?php
/**
 * ZeyTech AI Sales Engineer & Live Merchant Messaging Widget
 */
if (defined('AI_CHAT_WIDGET_INCLUDED')) {
    return;
}
define('AI_CHAT_WIDGET_INCLUDED', true);

$currentPid = intval($_GET['pid'] ?? 0);
?>
<!-- Floating AI Shopping Advisor Button & Modal -->
<style>
@media (max-width: 767px) {
	#ai-chat-widget-root {
		bottom: 16px !important;
		right: 16px !important;
	}
	#aiChatModal {
		position: fixed !important;
		top: 0 !important;
		left: 0 !important;
		right: 0 !important;
		bottom: 0 !important;
		width: 100vw !important;
		height: 100vh !important;
		max-width: 100vw !important;
		max-height: 100vh !important;
		border-radius: 0 !important;
		border: none !important;
		z-index: 100000 !important;
	}
	#aiChatForm {
		padding: 12px 14px calc(12px + env(safe-area-inset-bottom, 0px)) 14px !important;
	}
	#aiChatInput {
		min-height: 44px !important;
		font-size: 14px !important;
	}
	#aiChatSendBtn {
		width: 44px !important;
		height: 44px !important;
	}
	.ai-quick-btn {
		min-height: 36px !important;
		padding: 6px 12px !important;
	}
}
</style>
<div id="ai-chat-widget-root" style="position:fixed; bottom:24px; right:24px; z-index:99999; font-family:'IBM Plex Sans', sans-serif;">
	
	<!-- Trigger Floating Button (Sharp 2px, Gold Border, Navy Surface) -->
	<button id="aiChatTriggerBtn" onclick="toggleAiChat()" type="button" aria-label="Open AI Assistant" style="width:52px; height:52px; border-radius:2px; background:#0c1526; color:#d9b567; border:1px solid #c79a44; box-shadow:0 8px 24px rgba(0,0,0,0.5); cursor:pointer; display:flex; align-items:center; justify-content:center; position:relative; transition:all 0.2s ease; outline:none;">
		<span class="hexagram-mark" id="aiChatIcon" style="width:24px; height:24px;">
			<svg class="hexagram-svg" viewBox="0 0 24 24">
				<polygon points="12,2 22,18 2,18" stroke="#c79a44" fill="none" stroke-width="1.5"/>
				<polygon points="12,22 22,6 2,6" stroke="#d9b567" fill="none" stroke-width="1.5"/>
			</svg>
		</span>
		<span style="position:absolute; top:-2px; right:-2px; width:8px; height:8px; background:#22c55e; border-radius:2px;"></span>
	</button>

	<!-- Chat Window Modal (Sharp 2px, Deep Navy, Hairline Border) -->
	<div id="aiChatModal" style="display:none; position:absolute; bottom:64px; right:0; width:380px; max-width:calc(100vw - 32px); height:520px; max-height:calc(100vh - 120px); background:#0c1526; border-radius:2px; box-shadow:0 20px 40px rgba(0,0,0,0.6); border:1px solid rgba(142,162,191,0.25); overflow:hidden; flex-direction:column;">
		
		<!-- Header -->
		<div style="padding:14px 18px; background:#111d33; color:#f2efe6; display:flex; justify-content:space-between; align-items:center; border-bottom:1px solid rgba(142,162,191,0.18);">
			<div style="display:flex; align-items:center; gap:10px;">
				<span class="hexagram-mark" style="width:20px; height:20px;">
					<svg class="hexagram-svg" viewBox="0 0 24 24">
						<polygon points="12,2 22,18 2,18" stroke="#c79a44" fill="none" stroke-width="1.5"/>
						<polygon points="12,22 22,6 2,6" stroke="#d9b567" fill="none" stroke-width="1.5"/>
					</svg>
				</span>
				<div>
					<div style="font-family:'Fraunces', serif; font-weight:700; font-size:14px; line-height:1.2; color:#f2efe6;">ZeyTech AI Sales Engineer</div>
					<div style="font-family:'Space Mono'; font-size:10px; color:#8ea2bf; display:flex; align-items:center; gap:4px; margin-top:2px;">
						<span style="color:#22c55e;">[ONLINE]</span>
						<span>HUB-A1 &bull; DARIJA / FR / EN</span>
					</div>
				</div>
			</div>
			<button type="button" onclick="toggleAiChat()" aria-label="Close Chat" style="background:transparent; border:none; color:#8ea2bf; font-size:16px; cursor:pointer; padding:4px; font-family:'Space Mono';">
				&times;
			</button>
		</div>

		<!-- Quick Suggestions Strip -->
		<div style="padding:8px 12px; background:#080e1a; border-bottom:1px solid rgba(142,162,191,0.15); display:flex; gap:6px; overflow-x:auto; white-space:nowrap; -webkit-overflow-scrolling:touch;">
			<button type="button" class="ai-quick-btn btn-ghost" onclick="sendAiQuickPrompt('What are the full specifications (Fiche Technique)?')" style="padding:4px 8px; font-size:10px; font-family:'Space Mono';">[FICHE.TECHNIQUE]</button>
			<button type="button" class="ai-quick-btn btn-ghost" onclick="sendAiQuickPrompt('How fast is shipping to Rabat/Casablanca?')" style="padding:4px 8px; font-size:10px; font-family:'Space Mono';">[CTM.TRANSIT]</button>
			<button type="button" class="ai-quick-btn btn-ghost" onclick="sendAiQuickPrompt('شحال الثمن بالدرهم واش كاين فالمخزن؟')" style="padding:4px 8px; font-size:10px; font-family:'Space Mono'; color:#d9b567; border-color:rgba(199,154,68,0.4);">[بالدرهم المغربي]</button>
		</div>

		<!-- Messages Container -->
		<div id="aiChatMessages" style="flex:1; padding:16px; overflow-y:auto; background:#080e1a; display:flex; flex-direction:column; gap:12px;">
			<!-- Intro Message -->
			<div style="align-self:flex-start; max-width:88%; background:#111d33; border:1px solid rgba(142,162,191,0.18); border-radius:2px; padding:10px 14px; font-size:13px; color:#f2efe6; line-height:1.5;">
				<div style="font-family:'Space Mono'; font-size:10px; color:#c79a44; margin-bottom:4px;">[AGENT: SALES_ENGINEER]</div>
				Marhaba! I am your Technical Sales Engineer at Casablanca Central Fulfillment. How can I assist you with specs, regional MAD tariffs, or orders today?
			</div>
		</div>

		<!-- Footer Input Bar -->
		<form id="aiChatForm" onsubmit="handleAiChatSubmit(event)" style="padding:10px 14px; background:#0c1526; border-top:1px solid rgba(142,162,191,0.18); display:flex; gap:8px; align-items:center;">
			<input type="text" id="aiChatInput" placeholder="Message or Darija query..." autocomplete="off" style="flex:1; padding:8px 12px; font-size:13px; font-family:'IBM Plex Sans'; background:#080e1a; border:1px solid rgba(142,162,191,0.25); color:#f2efe6; border-radius:2px; outline:none;" required>
			<button type="submit" id="aiChatSendBtn" aria-label="Send message" class="btn-primary" style="padding:8px 14px; font-size:11px; font-family:'Space Mono'; flex-shrink:0;">
				SEND
			</button>
		</form>
	</div>
</div>

<script>
var currentAiProductId = <?php echo $currentPid; ?>;
var ztChatSessionId = localStorage.getItem('zt_chat_sess') || ('sess_' + Math.random().toString(36).slice(2, 10));
localStorage.setItem('zt_chat_sess', ztChatSessionId);
var lastRenderedMsgId = 0;

function toggleAiChat() {
	var modal = document.getElementById('aiChatModal');
	if (modal.style.display === 'none' || modal.style.display === '') {
		modal.style.display = 'flex';
		document.getElementById('aiChatInput').focus();
		pollStaffMessages();
	} else {
		modal.style.display = 'none';
	}
}

function sendAiQuickPrompt(text) {
	document.getElementById('aiChatInput').value = text;
	document.getElementById('aiChatForm').dispatchEvent(new Event('submit'));
}

function appendAiMessage(sender, text, senderName) {
	var container = document.getElementById('aiChatMessages');
	var msgDiv = document.createElement('div');
	
	if (sender === 'user' || sender === 'CUSTOMER') {
		msgDiv.style.cssText = 'align-self:flex-end; max-width:88%; background:rgba(199,154,68,0.15); border:1px solid rgba(199,154,68,0.35); color:#f2efe6; border-radius:2px; padding:10px 14px; font-size:13px; word-break:break-word;';
		msgDiv.textContent = text;
	} else if (sender === 'STAFF') {
		msgDiv.style.cssText = 'align-self:flex-start; max-width:88%; background:#111d33; border:1px solid #22c55e; border-radius:2px; padding:10px 14px; font-size:13px; color:#f2efe6; word-break:break-word;';
		var nameTag = senderName ? `<div style="font-family:'Space Mono'; font-size:10px; font-weight:700; color:#22c55e; margin-bottom:4px;">[SUPPORT: ${senderName}]</div>` : '';
		msgDiv.innerHTML = nameTag + text.replace(/\n/g, '<br>');
	} else {
		msgDiv.style.cssText = 'align-self:flex-start; max-width:88%; background:#111d33; border:1px solid rgba(142,162,191,0.18); border-radius:2px; padding:10px 14px; font-size:13px; color:#f2efe6; word-break:break-word; line-height:1.5;';
		var formatted = text
			.replace(/\*\*(.*?)\*\*/g, '<strong style="color:#d9b567;">$1</strong>')
			.replace(/\n/g, '<br>');
		msgDiv.innerHTML = formatted;
	}

	container.appendChild(msgDiv);
	container.scrollTop = container.scrollHeight;
	return msgDiv;
}

function handleAiChatSubmit(e) {
	e.preventDefault();
	var input = document.getElementById('aiChatInput');
	var message = input.value.trim();
	if (!message) return;

	appendAiMessage('user', message);
	input.value = '';

	// Sync message to chat_messages database
	fetch('api-chat-send.php', {
		method: 'POST',
		headers: { 'Content-Type': 'application/json' },
		body: JSON.stringify({
			sessionId: ztChatSessionId,
			senderType: 'CUSTOMER',
			senderName: 'Customer',
			message: message,
			channel: 'WEB'
		})
	}).catch(() => {});

	// Add typing indicator
	var typingDiv = appendAiMessage('ai', '<span style="font-family:\'Space Mono\'; color:#8ea2bf; font-size:11px;">[QUERYING CATALOG & TELEMETRY...]</span>');

	fetch('api-chat.php', {
		method: 'POST',
		headers: { 'Content-Type': 'application/json' },
		body: JSON.stringify({
			message: message,
			productId: currentAiProductId,
			channel: 'WEB',
			sessionId: ztChatSessionId
		})
	})
	.then(res => res.json())
	.then(data => {
		var replyText = data.reply || (data.data ? data.data.reply : 'Could not process query.');
		typingDiv.remove();
		appendAiMessage('ai', replyText);

		// Record AI reply in chat_messages table
		fetch('api-chat-send.php', {
			method: 'POST',
			headers: { 'Content-Type': 'application/json' },
			body: JSON.stringify({
				sessionId: ztChatSessionId,
				senderType: 'AI_AGENT',
				senderName: 'ZeyTech AI Agent',
				message: replyText,
				channel: 'WEB'
			})
		}).catch(() => {});
	})
	.catch(err => {
		typingDiv.remove();
		appendAiMessage('ai', '[ERROR: Connection failed. Retrying in background.]');
	});
}

function pollStaffMessages() {
	fetch('api-chat-history.php?sessionId=' + encodeURIComponent(ztChatSessionId))
		.then(res => res.json())
		.then(data => {
			if (data.success && data.messages) {
				data.messages.forEach(m => {
					if (m.id > lastRenderedMsgId) {
						lastRenderedMsgId = m.id;
						if (m.sender_type === 'STAFF') {
							appendAiMessage('STAFF', m.message, m.sender_name);
						}
					}
				});
			}
		})
		.catch(() => {});
}

setInterval(() => {
	var modal = document.getElementById('aiChatModal');
	if (modal && modal.style.display === 'flex') {
		pollStaffMessages();
	}
}, 4000);
</script>
