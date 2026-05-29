Context
Continuation of "Spon++" Laravel project. Implement the following 3 UI improvements only — do not touch any backend logic, routes, or database.

1. Replace All Icons with Lucide Icons (CDN)

Replace every icon currently used across the entire project with Lucide Icons
Load via CDN (no npm install):

html<script src="https://unpkg.com/lucide@latest"></script>
<script>lucide.createIcons();</script>

Use <i data-lucide="icon-name"></i> syntax throughout all blade files
Call lucide.createIcons() at the bottom of the main layout and inside any dynamically rendered content (modals, etc.)

Icon mapping reference (replace existing icons with these):
Action / ElementLucide Icon NameUploaduploadDownloaddownloadEdit / UpdatepencilDelete / Removetrash-2PinpinUnpinpin-offLogoutlog-outClose / XxEye (show password)eyeEye Off (hide password)eye-offSuccess / Checkcircle-checkErrorcircle-xWarningtriangle-alertInfoinfoMembers / UsersusersMaterials / Filefile-textClass / Bookbook-openJoin Classdoor-openCreate ClassplusSettingssettingsKick memberuser-xExit classlog-outDashboardlayout-dashboard

Apply consistently across all pages and components — sidebar, navbar, buttons, table actions, modals, alerts


2. Fix Global Alert / Toast — Dismissible + Swipe to Dismiss
Current bug: The toast alert cannot be closed by clicking the X button, and cannot be swiped away.
Fix — X Button (Click to Dismiss):

Each toast must have a working close button using Lucide x icon
Clicking X immediately removes the toast from DOM:

javascriptfunction dismissToast(el) {
  el.classList.add('toast-hide'); // trigger fade-out animation
  setTimeout(() => el.remove(), 300);
}
html<div class="toast toast-success" id="toast-1">
  <i data-lucide="circle-check"></i>
  <span>Material uploaded successfully.</span>
  <button onclick="dismissToast(this.parentElement)">
    <i data-lucide="x"></i>
  </button>
</div>
Fix — Swipe to Dismiss (Touch devices):

On mobile, user can swipe the toast left or right to dismiss it
Implement using touch events (vanilla JS only):

javascript// On touchstart → record startX
// On touchend → if deltaX > 80px, dismiss toast
toast.addEventListener('touchstart', e => startX = e.touches[0].clientX);
toast.addEventListener('touchend', e => {
  const delta = e.changedTouches[0].clientX - startX;
  if (Math.abs(delta) > 80) dismissToast(toast);
});
Toast CSS requirements:

Toast must have cursor: pointer on X button
Add fade-out animation for .toast-hide:

css.toast-hide {
  opacity: 0;
  transform: translateX(100%);
  transition: all 0.3s ease;
}

Auto-dismiss after 4 seconds still applies (but X or swipe cancels the timer)


3. Confirmation Modal for Delete & Logout (Yes / No)
Replace any confirm() browser dialogs or direct delete/logout links with a custom modal confirmation.
Trigger points — show modal before executing:
ActionModal TitleModal MessageDelete material"Delete Material""Are you sure you want to delete this material? This action cannot be undone."Kick member"Kick Member""Are you sure you want to remove [member name] from this class?"Delete class"Delete Class""Are you sure you want to delete this class? All materials and members will be removed."Exit class (member)"Leave Class""Are you sure you want to leave this class?"Logout"Logout""Are you sure you want to logout?"
Modal Structure:
html<div id="confirm-modal" class="modal-overlay" style="display:none;">
  <div class="modal-box">
    <div class="modal-icon">
      <i data-lucide="triangle-alert"></i>
    </div>
    <h3 id="modal-title">Confirm</h3>
    <p id="modal-message">Are you sure?</p>
    <div class="modal-actions">
      <button id="modal-cancel" onclick="closeConfirmModal()">
        Cancel
      </button>
      <button id="modal-confirm" class="btn-danger">
        Yes, Confirm
      </button>
    </div>
  </div>
</div>
JS Logic — Generic reusable modal:
javascriptfunction showConfirmModal({ title, message, onConfirm }) {
  document.getElementById('modal-title').textContent = title;
  document.getElementById('modal-message').textContent = message;
  document.getElementById('confirm-modal').style.display = 'flex';

  // Attach the confirm action dynamically
  const confirmBtn = document.getElementById('modal-confirm');
  confirmBtn.onclick = function () {
    closeConfirmModal();
    onConfirm(); // execute the actual action
  };
}

function closeConfirmModal() {
  document.getElementById('confirm-modal').style.display = 'none';
}

// Close if clicking outside modal box
document.getElementById('confirm-modal').addEventListener('click', function(e) {
  if (e.target === this) closeConfirmModal();
});
Usage example on delete button:
html<!-- Replace direct form submit with modal trigger -->
<button type="button" onclick="showConfirmModal({
  title: 'Delete Material',
  message: 'Are you sure you want to delete this material? This action cannot be undone.',
  onConfirm: () => document.getElementById('delete-form-{{ $material->id }}').submit()
})">
  <i data-lucide="trash-2"></i> Delete
</button>

<form id="delete-form-{{ $material->id }}"
  action="{{ route('materials.destroy', $material->id) }}"
  method="POST" style="display:none;">
  @csrf @method('DELETE')
</form>
Modal styling requirements:

Overlay: full screen, semi-transparent dark background (rgba(0,0,0,0.5))
Modal box: centered, white card, rounded corners, max-width 400px
Cancel button: neutral/grey style
Confirm button: red/danger style
Add to main layout so it's globally available on all pages


Constraints

Do not change any backend logic, controllers, routes, or DB
Do not use any icon library other than Lucide
Do not use jQuery or any external JS library — vanilla JS only
Lucide createIcons() must be called after every dynamic DOM change (modal open, toast inject)
Confirmation modal must be one single global instance — not duplicated per page
All 3 changes must work consistently across all existing pages