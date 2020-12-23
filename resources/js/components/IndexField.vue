<template>
    <embed :id="field.value"/>
</template>

<script>
import Embed from './Embed';
export default {
    components: {Embed},
    props: ['resourceName', 'field'],
}
</script>
