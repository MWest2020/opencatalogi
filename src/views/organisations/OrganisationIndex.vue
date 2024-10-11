<script setup>
import { navigationStore, organizationStore, searchStore } from '../../store/store.js'
</script>

<template>
	<NcAppContent>
		<template #list>
			<organizationList :search="searchStore.search" />
		</template>
		<template #default>
			<NcEmptyContent v-if="!organizationStore.organizationItem?.id || navigationStore.selected != 'organizations'"
				class="detailContainer"
				name="Geen organisatie"
				description="Nog geen organisatie geselecteerd">
				<template #icon>
					<OfficeBuildingOutline />
				</template>
				<template #action>
					<NcButton type="primary" @click="navigationStore.setModal('organizationAdd')">
						Organisatie toevoegen
					</NcButton>
				</template>
			</NcEmptyContent>
			<organizationDetails v-if="organizationStore.organizationItem?.id && navigationStore.selected === 'organizations'" :organization-item="organizationStore.organizationItem" />
		</template>
	</NcAppContent>
</template>

<script>
import { NcAppContent, NcButton, NcEmptyContent } from '@nextcloud/vue'
import OfficeBuildingOutline from 'vue-material-design-icons/OfficeBuildingOutline.vue'
import organizationDetails from './organizationDetail.vue'
import organizationList from './organizationList.vue'

export default {
	name: 'organizationIndex',
	components: {
		NcAppContent,
		NcEmptyContent,
		organizationList,
		organizationDetails,
		NcButton,
		// Icons
		OfficeBuildingOutline,
	},
	data() {
		return {

		}
	},
}
</script>
