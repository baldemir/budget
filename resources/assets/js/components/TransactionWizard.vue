<template>
    <div>

        <button-radio :callback="updateState" :need_callback="true"></button-radio>

        <div class="input" v-if="type == 'spending'">
            <label>Kategori</label>
            <searchable
                :type=1
                name="tag"
                :items="tags"
                @SelectUpdated="tagUpdated"></searchable>
            <validation-error v-if="errors.tag_id" :message="errors.tag_id"></validation-error>
        </div>
        <div class="input" v-if="type == 'earning'">
            <label>Kategori</label>
            <searchable
                    :type=2
                    name="tag"
                    :items="tags"
                    @SelectUpdated="tagUpdated"></searchable>
            <validation-error v-if="errors.tag_id" :message="errors.tag_id"></validation-error>
        </div>
        <div class="input">
            <label>Tarih</label>
            <date-picker @DateUpdated="onDateUpdate"></date-picker>
            <div class="hint mt-05">YYYY-MM-DD</div>
            <validation-error v-if="errors.date" :message="errors.date"></validation-error>
            <validation-error v-if="errors.day" :message="errors.day"></validation-error>
        </div>
        <div class="input">
            <label>Açıklama</label>
            <input type="text" v-model="description" :placeholder="type == 'earning' ? 'Mart ayı maaşı' : 'ANKARA CEPA AVM Media Markt'" />
            <validation-error v-if="errors.description" :message="errors.description"></validation-error>
        </div>
        <div class="input">
            <label>Ek Açıklama</label>
            <input type="text" v-model="additional_desc" :placeholder="type == 'earning' ? 'Mart ayı maaşı' : 'Ebru için doğum günü hediyesi'" />
            <validation-error v-if="errors.additional_desc" :message="errors.additional_desc"></validation-error>
        </div>
        <div class="input">
            <label>Tutar</label>
            <input type="text" v-model="amount" />
            <validation-error v-if="errors.amount" :message="errors.amount"></validation-error>
        </div>
        <div v-if="type == 'spending'">
            <div class="input row">
                <div class="row__column row__column--compact mr-1">
                    <input type="checkbox" id="test" v-model="isRecurring" />
                </div>
                <div class="row__column">
                    <label for="test">Bu tekrar eden bir harcama&mdash;sonraki aylar için de oluştur.</label>
                </div>
            </div>
            <div v-if="isRecurring">
                <div class="input">
                    <label>Bu harcama ne kadar devam edecek?</label>
                    <div class="row">
                        <div class="row__column row__column--compact mr-1">
                            <input id="noEnd" type="radio" v-model="recurringEnd" value="forever" />
                        </div>
                        <div class="row__column">
                            <label for="noEnd">Hep :(</label>
                        </div>
                    </div>
                    <div class="row">
                        <div class="row__column row__column--compact mr-1">
                            <input id="fixedEnd" type="radio" v-model="recurringEnd" value="fixed" />
                        </div>
                        <div class="row__column">
                            <label for="fixedEnd">Şu tarihe kadar</label>
                            <date-picker name="end" :start-date="recurringEndDate" @DateUpdated="onEndUpdate"></date-picker>
                            <div class="hint mt-05">YYYY-MM-DD</div>
                            <validation-error v-if="errors.end" :message="errors.end"></validation-error>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <button
            class="button"
            @click="createEarning">
            <span v-if="loading">Loading</span>
            <span v-if="!loading">Oluştur</span>
        </button>
        <div
            v-if="success"
            class="mt-2"
            style="color: green;"
        >Başarıyla oluşturuldu</div>
    </div>
</template>

<script>
    export default {
        props: ['tags'],

        data() {
            return {
                type: 'earning',
                errors: [],

                tag: null,
                date: this.getTodaysDate(),
                description: '',
                additional_desc: '',
                amount: '10.00',
                isRecurring: false,
                recurringEnd: 'forever',
                recurringEndDate: this.get100DaysFutureDate(),

                loading: false,
                success: false
            }
        },

        methods: {
            updateState(type){
                this.type=type;
            },

            // Children
            onDateUpdate(date) {
                this.date = date
            },

            onEndUpdate(date) {
                this.recurringEndDate = date
            },

            //
            switchType(type) {
                this.type = type

                this.success = false
            },

            tagUpdated(payload) {
                this.tag = payload.key
            },

            getTodaysDate() {
                return new Date().toISOString().slice(0, 10)
            },

            get100DaysFutureDate() {
                let now = new Date()

                return (now.getFullYear() + 1) + '-' + ('0' + (now.getMonth() + 1)).slice(-2) + '-' + ('0' + now.getDate()).slice(-2)
            },

            createEarning() {
                if (!this.loading) {
                    this.loading = true

                    if (this.type == 'spending' && this.isRecurring) { // It's a recurring
                        let body = {
                            _token: document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                            day: this.date.slice(-2),
                            description: this.description,
                            additional_desc: this.additional_desc,
                            amount: this.amount
                        }

                        if (this.recurringEnd == 'fixed') {
                            body.end = this.recurringEndDate
                        }

                        if (this.tag) {
                            body.tag_id = this.tag
                        }

                        axios.post('/recurrings', body).then(response => {
                            this.handleSuccess()
                        }).catch(error => {
                            this.handleErrors(error.response)
                        })
                    } else { // It's an earning or a spending, not a recurring
                        let body = {
                            _token: document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                            date: this.date,
                            description: this.description,
                            additional_desc: this.additional_desc,
                            amount: this.amount,
                            account_id: this.account_id
                        }

                        if (this.tag) {
                            body.tag_id = this.tag
                        }

                        axios.post('/' + this.type + 's', body).then(response => {
                            this.handleSuccess()
                        }).catch(error => {
                            this.handleErrors(error.response)
                        })
                    }
                }
            },

            handleSuccess() {
                this.loading = false

                this.errors = []

                //
                window.location.href = '/transactions'

                this.date = this.getTodaysDate()
                this.description = ''
                this.additional_desc = '';
                this.amount = ''
                // Leave isRecurring as is
                this.recurringEnd = 'forever'
                this.recurringEndDate = ''

                this.success = true
            },

            handleErrors(response) {
                this.loading = false

                let errors = []

                if (response.data.errors) {
                    for (let key in response.data.errors) {
                        if (response.data.errors.hasOwnProperty(key)) {
                            errors[key] = response.data.errors[key][0]
                        }
                    }
                }

                this.errors = errors

                if (response.status != 422) {
                    alert('Something went wrong')
                }

                this.success = false
            }
        }
    }
</script>
