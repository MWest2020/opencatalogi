<script setup>
import { navigationStore } from '../../store/store.js'
</script>

<template>
	<NcDialog
		v-if="navigationStore.dialog === 'viewLog'"
		name="Bekijk log regel"
		:can-close="false">
		<table width="100%" class="changesDialog">
			<tr>
				<td><b>Tijdstip</b></td>
				<td>Ruben van der Linde</td>
			</tr>
			<tr>
				<td><b>Gebruiker</b></td>
				<td>Ruben van der Linde</td>
			</tr>
			<tr>
				<td><b>Actie</b></td>
				<td>Created</td>
			</tr>
			<tr>
				<td colspan="2">
					<b>Wijzigingen:</b>
					<pre><code>{{ formattedChanges }}</code></pre>
				</td>
			</tr>
		</table>
		<template #actions>
			<NcButton :disabled="loading" @click="navigationStore.setDialog(false)">
				<template #icon>
					<Cancel :size="20" />
				</template>
				Sluiten
			</NcButton>
		</template>
	</NcDialog>
</template>

<script>
import { NcButton, NcDialog } from '@nextcloud/vue'

import Cancel from 'vue-material-design-icons/Cancel.vue'

export default {
	name: 'ViewLogDialog',
	components: {
		NcDialog,
		NcButton,
		// Icons
		Cancel,
	},
	data() {
		return {
			loading: false,
			changes: {
				title: {
					old: null,
					new: 'KOPIE: Voorlopige energielabels met BAG-kenmerken',
				},
			},
		}
	},
	computed: {
		formattedChanges() {
			return JSON.stringify(this.changes, null, 2)
		},
	},
}
</script>

<style>
.modal__content {
    margin: var(--OC-margin-50);
    text-align: center;
}

.zaakDetailsContainer {
    margin-block-start: var(--OC-margin-20);
    margin-inline-start: var(--OC-margin-20);
    margin-inline-end: var(--OC-margin-20);
}

.success {
    color: green;
}
.changesDialog {
	overflow-x: scroll;
}
</style>
