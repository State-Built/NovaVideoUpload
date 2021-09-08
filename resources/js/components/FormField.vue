<template>
    <default-field :field="field" :errors="errors" :show-help-text="showHelpText">
        <template slot="field">
            <div class="flex" v-if="field.value">
                <vimeo-embed :id="field.value"/>
                <button class="btn btn-default btn-icon btn-white ml-4" title="Replace Video" @click.prevent="field.value = null">
                    <icon type="restore" class="text-80" />
                </button>
            </div>
            <dashboard v-if="!field.value" :uppy="uppy" :props="uppyProps"/>
        </template>
    </default-field>
</template>

<script>
import {FormField, HandlesValidationErrors} from 'laravel-nova';
import {Dashboard}                          from '@uppy/vue';
import Uppy                                 from '@uppy/core';
import Tus                                  from '@uppy/tus';
import '@uppy/core/dist/style.css';
import '@uppy/dashboard/dist/style.css';
import VimeoEmbed                           from './VimeoEmbed';

export default {
    mixins: [FormField, HandlesValidationErrors],

    components: {VimeoEmbed, Dashboard},

    props: ['resourceName', 'resourceId', 'field'],

    computed: {
        uppy() {
            return new Uppy({
                allowMultipleUploads: false,
                debug: true,
                restrictions: {
                    allowedFileTypes: ['video/*'],
                    maxNumberOfFiles: 1,
                    minNumberOfFiles: 1,
                },
            }).use(Tus, {
                endpoint: '/nova-tus',
                resume: true,
                retryDelays: [0, 1000, 3000, 5000],
                chunkSize: 1000000,
            });
        },

        uppyProps() {
            return {
                doneButtonHandler: () => {
                    // disable this so value doesn't reset to null.
                },
            }
        },

    },

    methods: {
        /*
         * Set the initial, internal value for the field.
         */
        setInitialValue() {
            this.value = this.field.value || ''
        },

        /**
         * Fill the given FormData object with the field's internal value.
         */
        fill(formData) {
            formData.append(this.field.attribute, this.value || '')
        },
    },

    mounted() {
        this.uppy.on('upload-success', (file, response) => {
            this.value = file.name;
        });
    },

}
</script>
