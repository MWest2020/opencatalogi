/**
 * SearchSideBar.vue
 * Component for displaying the search sidebar
 * @category Components
 * @package opencatalogi
 * @author Ruben Linde
 * @copyright 2024
 * @license AGPL-3.0-or-later
 * @version 1.0.0
 * @link https://github.com/opencatalogi/opencatalogi
 */

<script setup>
import SearchResults from '../../components/SearchResults.vue'
</script>

<template>
	<SearchResults container-class="search-sidebar" />
</template>

<style scoped>
.search-sidebar {
	height: 100%;
}
</style>
