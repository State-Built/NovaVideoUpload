<template>
    <div>
        <panel-item :field="field">
            <template slot="value">
                <vimeo-embed :id="field.value"/>
            </template>
        </panel-item>
    </div>
</template>

<script>
import VimeoEmbed from './VimeoEmbed';

export default {
    components: {VimeoEmbed},
    props: ['resource', 'resourceName', 'resourceId', 'field'],
}
</script>
