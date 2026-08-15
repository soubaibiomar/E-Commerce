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
	
	<!-- Trigger Floating Button -->
	<button id="aiChatTriggerBtn" onclick="toggleAiChat()" type="button" aria-label="Open AI Assistant" style="width:54px; height:54px; border-radius:50%; background:#121e36; color:#d9b45d; border:2px solid #c59b43; box-shadow:0 8px 28px rgba(0,0,0,0.5); cursor:pointer; display:flex; align-items:center; justify-content:center; position:relative; transition:all 0.25s ease; outline:none; padding:4px;">
		<img src="assets/images/logo.jpg" alt="ZeyTech AI" style="width:38px; height:38px; object-fit:contain; border-radius:50%;">
		<span style="position:absolute; top:2px; right:2px; width:10px; height:10px; background:#10b981; border:2px solid #121e36; border-radius:50%;"></span>
	</button>

	<!-- Chat Window Modal -->
	<div id="aiChatModal" style="display:none; position:absolute; bottom:68px; right:0; width:380px; max-width:calc(100vw - 32px); height:520px; max-height:calc(100vh - 120px); background:#121e36; border-radius:8px; box-shadow:0 20px 48px rgba(0,0,0,0.6); border:1px solid rgba(226,232,240,0.15); overflow:hidden; flex-direction:column;">
		
		<!-- Header -->
		<div style="padding:14px 18px; background:#182847; color:#f8fafc; display:flex; justify-content:space-between; align-items:center; border-bottom:1px solid rgba(226,232,240,0.10);">
			<div style="display:flex; align-items:center; gap:10px;">
				<img src="assets/images/logo.jpg" alt="ZeyTech" style="width:32px; height:32px; object-fit:contain; border-radius:4px;">
				<div>
					<div style="font-family:'Fraunces', serif; font-weight:700; font-size:15px; line-height:1.2; color:#ffffff;">ZeyTech AI Advisor</div>
					<div style="font-size:11px; color:#94a3b8; display:flex; align-items:center; gap:6px; margin-top:2px;">
						<span style="display:inline-block; width:6px; height:6px; background:#10b981; border-radius:50%;"></span>
						<span>Online &bull; Darija, Français, English</span>
					</div>
				</div>
			</div>
			<button type="button" onclick="toggleAiChat()" aria-label="Close Chat" style="background:transparent; border:none; color:#94a3b8; font-size:20px; cursor:pointer; padding:4px; line-height:1;">
				&times;
			</button>
		</div>

		<!-- Quick Suggestions Strip -->
		<div style="padding:8px 12px; background:#0b162c; border-bottom:1px solid rgba(226,232,240,0.08); display:flex; gap:6px; overflow-x:auto; white-space:nowrap; -webkit-overflow-scrolling:touch;">
			<button type="button" class="ai-quick-btn btn-ghost" onclick="sendAiQuickPrompt('What are the full specifications (Fiche Technique)?')" style="padding:4px 10px; font-size:11px; border-radius:4px;">Specifications</button>
			<button type="button" class="ai-quick-btn btn-ghost" onclick="sendAiQuickPrompt('How fast is shipping to Rabat/Casablanca?')" style="padding:4px 10px; font-size:11px; border-radius:4px;">Shipping Delivery</button>
			<button type="button" class="ai-quick-btn btn-ghost" onclick="sendAiQuickPrompt('شحال الثمن بالدرهم واش كاين فالمخزن؟')" style="padding:4px 10px; font-size:11px; border-radius:4px; color:#d9b45d; border-color:rgba(197,155,67,0.3);">الثمن بالدرهم</button>
		</div>

		<!-- Messages Container -->
		<div id="aiChatMessages" style="flex:1; padding:16px; overflow-y:auto; background:#0b162c; display:flex; flex-direction:column; gap:12px;">
			<!-- Intro Message -->
			<div style="align-self:flex-start; max-width:88%; background:#182847; border:1px solid rgba(226,232,240,0.10); border-radius:6px; padding:12px 14px; font-size:13px; color:#f8fafc; line-height:1.5;">
				<div style="font-size:11px; font-weight:600; color:#d9b45d; margin-bottom:4px;">ZeyTech Sales Advisor</div>
				Marhaba! Welcome to ZeyTech Casablanca. How can I assist you with product specs, Moroccan Dirham pricing, or order tracking today?
			</div>
		</div>

		<!-- Footer Input Bar -->
		<form id="aiChatForm" onsubmit="handleAiChatSubmit(event)" style="padding:10px 14px; background:#121e36; border-top:1px solid rgba(226,232,240,0.10); display:flex; gap:8px; align-items:center;">
			<input type="text" id="aiChatInput" placeholder="Ask in Darija, French, or English..." autocomplete="off" style="flex:1; padding:9px 12px; font-size:13px; font-family:'IBM Plex Sans'; background:#0b162c; border:1px solid rgba(226,232,240,0.15); color:#f8fafc; border-radius:4px; outline:none;" required>
			<button type="submit" id="aiChatSendBtn" aria-label="Send message" class="btn-primary" style="padding:8px 14px; font-size:12px; border-radius:4px; flex-shrink:0;">
				Send
			</button>
		</form>

	</div>
</div>

<script>
var isAiChatOpen = false;
var aiCurrentPid = <?php echo $currentPid; ?>;

function toggleAiChat() {
	var modal = document.getElementById('aiChatModal');
	isAiChatOpen = !isAiChatOpen;
	if (isAiChatOpen) {
		modal.style.display = 'flex';
		setTimeout(function(){ document.getElementById('aiChatInput').focus(); }, 100);
	} else {
		modal.style.display = 'none';
	}
}

function sendAiQuickPrompt(text) {
	var input = document.getElementById('aiChatInput');
	input.value = text;
	document.getElementById('aiChatForm').dispatchEvent(new Event('submit'));
}

function handleAiChatSubmit(e) {
	e.preventDefault();
	var input = document.getElementById('aiChatInput');
	var msg = input.value.trim();
	if (!msg) return;

	appendUserMessage(msg);
	input.value = '';

	var typingId = appendTypingIndicator();

	fetch('api-chat.php', {
		method: 'POST',
		headers: { 'Content-Type': 'application/json' },
		body: JSON.stringify({ message: msg, product_id: aiCurrentPid })
	})
	.then(function(res){ return res.json(); })
	.then(function(data){
		removeTypingIndicator(typingId);
		if (data.status === 'ok' && data.reply) {
			appendBotMessage(data.reply);
		} else {
			appendBotMessage(data.error || "I'm experiencing a brief connectivity glitch with Casablanca central database. Please try again.");
		}
	})
	.catch(function(err){
		removeTypingIndicator(typingId);
		appendBotMessage("Connection error with AI service. Please verify your connection.");
	});
}

function appendUserMessage(text) {
	var container = document.getElementById('aiChatMessages');
	var div = document.createElement('div');
	div.style.cssText = "align-self:flex-end; max-width:85%; background:#c59b43; color:#0b162c; font-weight:500; border-radius:6px; padding:10px 14px; font-size:13px; line-height:1.4;";
	div.textContent = text;
	container.appendChild(div);
	container.scrollTop = container.scrollHeight;
}

function appendBotMessage(text) {
	var container = document.getElementById('aiChatMessages');
	var div = document.createElement('div');
	div.style.cssText = "align-self:flex-start; max-width:88%; background:#182847; border:1px solid rgba(226,232,240,0.10); border-radius:6px; padding:12px 14px; font-size:13px; color:#f8fafc; line-height:1.5;";
	
	// Convert simple markdown bold/bullets
	var formatted = text
		.replace(/\n\n/g, '<br><br>')
		.replace(/\n/g, '<br>')
		.replace(/\*\*(.*?)\*\*/g, '<strong style="color:#d9b45d;">$1</strong>')
		.replace(/•\s+/g, '&bull; ');

	div.innerHTML = "<div style='font-size:11px; font-weight:600; color:#d9b45d; margin-bottom:4px;'>ZeyTech Sales Advisor</div>" + formatted;
	container.appendChild(div);
	container.scrollTop = container.scrollHeight;
}

function appendTypingIndicator() {
	var container = document.getElementById('aiChatMessages');
	var div = document.createElement('div');
	var id = 'typing_' + Date.now();
	div.id = id;
	div.style.cssText = "align-self:flex-start; background:#182847; border:1px solid rgba(226,232,240,0.10); border-radius:6px; padding:10px 14px; font-size:12px; color:#94a3b8;";
	div.innerHTML = "<em>Analyzing catalog specs &amp; pricing...</em>";
	container.appendChild(div);
	container.scrollTop = container.scrollHeight;
	return id;
}

function removeTypingIndicator(id) {
	var el = document.getElementById(id);
	if (el) el.remove();
}
</script>
