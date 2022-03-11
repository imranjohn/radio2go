<template>
  <div>
    <h1 class="mb-8 font-bold text-3xl">
      <inertia-link class="text-indigo-400 hover:text-indigo-600" :href="route('stations.index')">Stations</inertia-link>
      <span class="text-indigo-400 font-medium">/</span> Create
    </h1>
    <div class="bg-white rounded-md shadow overflow-hidden w-full h-full">
      <form @submit.prevent="store">
        <div class="p-8 -mr-6 -mb-8 flex flex-wrap">
          <text-input v-model="form.name" :error="form.errors.name" class="pr-6 pb-8 w-full lg:w-1/2" label="Name" />
          <text-input v-model="form.stream_url" :error="form.errors.stream_url" class="pr-6 pb-8 w-full lg:w-1/2" label="Stream url" />
          <text-input v-model="form.image_url" :error="form.errors.image_url" class="pr-6 pb-8 w-full lg:w-1/2" label="Image url" />
          <text-input v-model="form.artwork_image" :error="form.errors.artwork_image" class="pr-6 pb-8 w-full lg:w-1/2" label="Artwork url" />
          <text-input v-model="form.description" :error="form.errors.description" class="pr-6 pb-8 w-full lg:w-1/1" label="Description" />
          <textarea-input v-model="form.long_description" :error="form.errors.long_description" class="pr-6 pb-8 w-full lg:w-1/1" label="Long Description" />
          <file-input v-model="form.background" :error="form.errors.background" class="pr-6 pb-8 w-full lg:w-1/2" type="file" accept="image/*" label="Html Background" />
        </div>
        <div class="px-8 py-4 bg-gray-50 border-t border-gray-100 flex justify-end items-center">
          <loading-button :loading="form.processing" class="btn-indigo" type="submit">Create Station</loading-button>
        </div>
      </form>
    </div>
  </div>
</template>

<script>
import Layout from '@/Shared/Layout'
import TextInput from '@/Shared/TextInput'
import TextareaInput from '@/Shared/TextareaInput'
import LoadingButton from '@/Shared/LoadingButton'
import FileInput from '@/Shared/FileInput'

export default {
  metaInfo: { title: 'Create Station' },
  components: {
    FileInput,
    LoadingButton,
    TextareaInput,
    TextInput,
  },
  layout: Layout,
  remember: 'form',
  data() {
    return {
      form: this.$inertia.form({
        name: null,
        stream_url: null,
        image_url: null,
        artwork_image: null,
        description: null,
        long_description: null,
        background: null,
      }),
    }
  },
  methods: {
    store() {
      this.form.post(this.route('stations.store').url())
    },
  },
}
</script>
