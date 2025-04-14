<script setup>
import { navigationStore, objectStore } from '../../store/store.js'
</script>

<template>
	<NcAppContent>
		<template #list>
			<PublicationTypeList />
		</template>
		<template #default>
			<NcEmptyContent v-if="showEmptyContent"
				class="detailContainer"
				name="Geen publicatietype"
				description="Nog geen publicatietype geselecteerd">
				<template #icon>
					<FileTreeOutline />
				</template>
				<template #action>
					<NcButton type="primary" @click="navigationStore.setModal('addPublicationType')">
						Publicatietype toevoegen
					</NcButton>
				</template>
			</NcEmptyContent>
			<PublicationTypeDetails v-if="!showEmptyContent" />
		</template>
	</NcAppContent>
</template>

<script>
import { computed } from 'vue'
import { storeToRefs } from 'pinia'
import { NcAppContent, NcEmptyContent, NcButton } from '@nextcloud/vue'
import PublicationTypeList from './PublicationTypeList.vue'
import PublicationTypeDetails from './PublicationTypeDetail.vue'
import FileTreeOutline from 'vue-material-design-icons/FileTreeOutline.vue'

// Make the stores reactive
const { selected } = storeToRefs(navigationStore)
const activePublicationType = computed(() => objectStore.getActiveObject('publicationType'))

const showEmptyContent = computed(() => {
	const hasActivePublicationType = activePublicationType.value
	const isPublicationTypeSelected = selected.value === 'publicationType'
	return !hasActivePublicationType || !isPublicationTypeSelected
})

export default {
	name: 'PublicationTypeIndex',
	components: {
		NcAppContent,
		NcEmptyContent,
		NcButton,
		PublicationTypeList,
		PublicationTypeDetails,
		FileTreeOutline,
	},
}
</script>
