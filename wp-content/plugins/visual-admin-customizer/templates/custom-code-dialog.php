<div id="vac-custom-code-dialog" style="display: none;">
	<div id="vac-cc-actor-filter">

		<ul class="vac-cc-actors">
			<li>
				<a class="vac-cc-actor-item" href="#"
				   v-bind:class="{ 'vac-cc-active': (currentActorFilter === actorFilterPresets.notCurrentUser) }"
				   v-on:click.prevent="currentActorFilter = actorFilterPresets.notCurrentUser">
					Everyone but you
				</a>
			</li>

			<li>
				<a class="vac-cc-actor-item" href="#"
				   v-bind:class="{ 'vac-cc-active': (currentActorFilter === actorFilterPresets.everyone) }"
				   v-on:click.prevent="currentActorFilter = actorFilterPresets.everyone">
					All users
				</a>
			</li>

			<li class="vac-cc-separator"></li>

			<li v-for="roleFilter in singleRoleFilters">
				<a class="vac-cc-actor-item" href="#"
				   v-bind:class="{ 'vac-cc-active': (currentActorFilter === roleFilter) }"
				   v-on:click.prevent="currentActorFilter = roleFilter">
					{{ roleFilter.getSummary() }}
				</a>
			</li>
		</ul>

	</div>
	<div id="vac-cc-tabs">
		CSS
	</div>
	<div id="vac-cc-content">

	</div>
</div>
