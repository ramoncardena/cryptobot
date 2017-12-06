 <!-- Notifications Modal -->
 <div class="reveal large" id="notificationsModal" data-reveal>

 	<p class="h2">Notifications</p>

 	<notification-list :notifications="{{ $user->notifications }}"></notification-list>
 	
 	<button class="close-button" data-close aria-label="Close modal" type="button">
 		<span aria-hidden="true">&times;</span>
 	</button>

 </div>