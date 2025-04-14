<script setup>
import { navigationStore, objectStore } from '../../store/store.js'
</script>

<template>
	<NcAppContent>
		<template #list>
			<ThemeList />
		</template>
		<template #default>
			<NcEmptyContent v-if="showEmptyContent"
				class="detailContainer"
				name="Geen thema"
				description="Nog geen thema geselecteerd">
				<template #icon>
					<ShapeOutline />
				</template>
				<template #action>
					<NcButton type="primary" @click="navigationStore.setModal('themeAdd')">
						Thema toevoegen
					</NcButton>
				</template>
			</NcEmptyContent>
			<ThemeDetails v-if="!showEmptyContent" />
		</template>
	</NcAppContent>
</template>

<script>
import { computed } from 'vue'
import { storeToRefs } from 'pinia'
import { NcAppContent, NcEmptyContent, NcButton } from '@nextcloud/vue'
import ThemeList from './ThemeList.vue'
import ThemeDetails from './ThemeDetail.vue'
import ShapeOutline from 'vue-material-design-icons/ShapeOutline.vue'

// Make the stores reactive
const { selected } = storeToRefs(navigationStore)
const activeTheme = computed(() => objectStore.getActiveObject('theme'))

const showEmptyContent = computed(() => {
	const hasActiveTheme = activeTheme.value
	const isThemeSelected = selected.value === 'theme'
	return !hasActiveTheme || !isThemeSelected
})

export default {
	name: 'ThemeIndex',
	components: {
		NcAppContent,
		NcEmptyContent,
		NcButton,
		ThemeList,
		ThemeDetails,
		ShapeOutline,
	},
}
</script>
