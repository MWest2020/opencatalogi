/**
 * UseFileSelection.js
 * Composable for handling file selection and upload
 * @category Composables
 * @package
 * @author Ruben Linde
 * @copyright 2024
 * @license AGPL-3.0-or-later
 * @version 1.0.0
 * @link https://github.com/opencatalogi/opencatalogi
 */

import { ref } from 'vue'
import { objectStore } from '../store/store.js'

/**
 * Create a file selection composable
 * @param {object} options - Configuration options
 * @param {boolean} [options.allowMultiple] - Whether to allow multiple file selection
 * @param {import('vue').Ref} [options.dropzone] - Reference to the dropzone element
 * @return {object} File selection methods and state
 */
export const useFileSelection = ({ allowMultiple = false, dropzone = null } = {}) => {
	/**
	 * Selected files
	 * @type {import('vue').Ref<Array<File>>}
	 */
	const files = ref([])

	/**
	 * Reset the file selection
	 * @param {string} [fileName] - Optional file name to remove
	 * @return {void}
	 */
	const reset = (fileName = null) => {
		if (fileName) {
			files.value = files.value.filter(file => file.name !== fileName)
		} else {
			files.value = []
		}
	}

	/**
	 * Set tags for all files
	 * @param {Array<string>} tags - Tags to set
	 * @return {void}
	 */
	const setTags = (tags) => {
		files.value.forEach(file => {
			file.tags = tags
		})
	}

	/**
	 * Open file upload dialog
	 * @return {void}
	 */
	const openFileUpload = () => {
		const input = document.createElement('input')
		input.type = 'file'
		input.multiple = allowMultiple
		input.onchange = async (event) => {
			const selectedFiles = Array.from(event.target.files)
			for (const file of selectedFiles) {
				try {
					await objectStore.uploadFile('attachment', file)
					files.value.push(file)
				} catch (error) {
					console.error('Error uploading file:', error)
				}
			}
		}
		input.click()
	}

	// Set up dropzone if provided
	if (dropzone) {
		dropzone.value.addEventListener('dragover', (event) => {
			event.preventDefault()
			event.stopPropagation()
		})

		dropzone.value.addEventListener('drop', async (event) => {
			event.preventDefault()
			event.stopPropagation()

			const droppedFiles = Array.from(event.dataTransfer.files)
			for (const file of droppedFiles) {
				try {
					await objectStore.uploadFile('attachment', file)
					files.value.push(file)
				} catch (error) {
					console.error('Error uploading file:', error)
				}
			}
		})
	}

	return {
		files,
		reset,
		setTags,
		openFileUpload,
	}
}
