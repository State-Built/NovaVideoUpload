<template>
    <vimeo-embed :id="field.value"/>
</template>

<script>
import VimeoEmbed from './VimeoEmbed';
export default {
    components: {VimeoEmbed},
    props: ['resource', 'resourceName', 'resourceId', 'field'],
}
</script>
