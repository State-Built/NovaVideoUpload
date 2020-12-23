<template>
    <div>
        <iframe :src="iframeSrc" :width="width" :height="height" frameborder="0" allow="autoplay; fullscreen"
                allowfullscreen></iframe>
    </div>
</template>

<script>
export default {
    props: ['resource', 'resourceName', 'resourceId', 'field'],

    computed: {
        iframeSrc() {
            return `https://player.vimeo.com/video/${this.resource}`;
        },

        width() {
            return 640;
        },

        height() {
            return 262;
        },
    },

    mounted() {
        console.log(this.resource, this.field);
    },
}
</script>
