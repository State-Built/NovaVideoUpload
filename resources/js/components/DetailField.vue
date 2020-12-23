<template>
    <vimeo-embed :id="field.id"/>
</template>

<script>
import VimeoEmbed from './VimeoEmbed';
export default {
    components: {VimeoEmbed},
    props: ['resource', 'resourceName', 'resourceId', 'field'],
}
</script>
