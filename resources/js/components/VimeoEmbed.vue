<template>
    <iframe :src="iframeSrc" dusk="vimeo-embed" frameborder="0" allow="autoplay; fullscreen"
            allowfullscreen></iframe>
</template>

<script>
export default {
    name: 'VimeoEmbed',
    props: ['id'],
    computed: {
        iframeSrc() {
            return `https://player.vimeo.com/video/${this.id}`;
        },
    },
}
</script>

<style scoped>

</style>