<template>
    <embed :id="field.id"/>
</template>

<script>
import Embed from './Embed';
export default {
    components: {Embed},
    props: ['resource', 'resourceName', 'resourceId', 'field'],
}
</script>
