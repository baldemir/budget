<template>

    <div>
        <div class="bg mb-2">
            <input type="hidden" name="tag_type_id" :value="getTagId" />
            <button
                    class="bg__button"
                    :class="{ 'bg__button--active': init_type == 'earning' }"
                    v-on:click.stop.prevent="switchType('earning')">Gelir</button>
            <button
                    class="bg__button"
                    :class="{ 'bg__button--active': init_type == 'spending'}"
                    v-on:click.stop.prevent="switchType('spending')">Gider</button>
        </div>
    </div>

</template>

<script>
    export default {

        props: {
            callback: Function,
            need_callback: Boolean,
        },

        data() {
            return {
                init_type: "earning",
            }
        },

        computed: {
            getTagId() {
                let tag_type_id = -1;

                if (this.init_type == 'spending') {
                    tag_type_id = 1;
                }else if(this.init_type == 'earning'){
                    tag_type_id = 2;
                }else{
                    tag_type_id = -1;
                }

                return tag_type_id;
            }
        },

        methods: {
            switchType(type) {
                if(this.need_callback){
                    this.callback(type);
                }
                this.init_type = type;
            },
        }
    }
</script>