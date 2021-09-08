<template>
    <default-field :field="field" :errors="errors" :show-help-text="showHelpText" full-width-content>
        <template slot="field">
            <div class="flex" v-if="field.value">
                <vimeo-embed :id="field.value"/>
                <button class="btn btn-default btn-icon btn-white ml-4" title="Replace Video"
                        @click.prevent="field.value = null">
                    <icon type="restore" class="text-80"/>
                </button>
            </div>
            <div class="flex" v-if="showUppy">
                <dashboard :uppy="uppy" :props="uppyProps"/>
                <div>
                    <button class="btn btn-default btn-primary ml-4" title="Replace Video"
                            @click.prevent="showIdInput = !showIdInput">
                        use vimeo id
                    </button>
                </div>
            </div>
            <div class="flex" v-if="showIdInput">
                <input class="form-control form-input form-input-bordered" v-model="value">
                <button class="btn btn-default btn-primary ml-4" title="Replace Video"
                        @click.prevent="showIdInput = !showIdInput">
                    upload video
                </button>
            </div>
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

        showUppy() {
            return !this.field.value && !this.showIdInput;
        },
    },

    data() {
        return {
            showIdInput: false,
        }
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
