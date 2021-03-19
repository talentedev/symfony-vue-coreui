<template>
    <div class="card">
        <div class="card-header">
            <span class="h3">Report List</span>
            <b-button v-if="isAdmin" variant="outline-primary" class="float-right" @click="showModal">{{
                $t("views.report.upload") }}
            </b-button>
        </div>
        <div class="card-body">
            <div class="animated fadeIn">
                <div class="row mb-3">
                    <div class="col-sm-12 col-md-6">
                        <div class="row">
                            <div class="col-4 text-center pt-2">
                                <label>{{ $t("views.report.category") }} :</label>
                            </div>
                            <div class="col-8">
                                <multiselect
                                        v-model="selectedCategory"
                                        :options="categories"
                                        :multiple="true" :taggable="true"
                                        @input="changeCategory"
                                        label="text" track-by="value"></multiselect>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-12 col-md-6">
                        <div class="row">
                            <div class="col-4 text-center pt-2">
                                <label>{{ $t("views.report.sub-category") }} :</label>
                            </div>
                            <div class="col-8">
                                <multiselect
                                        v-model="selectedSubCategory"
                                        :options="subCategories"
                                        :multiple="true" :taggable="true"
                                        @input="changeSubCategory"
                                        label="text" track-by="value"></multiselect>
                            </div>
                        </div>
                    </div>
                </div>
                <report-table :table-data="reports" :fields="fields" @clicked="onDeleteReport" :per-page=20 hover small
                              striped bordered fixed></report-table>
                <loading :active.sync="isLoading" :is-full-page="fullPageLoading"></loading>
                <!-- Modal -->
                <b-modal ref="uploadFileModalRef" title="Add Report">
                    <b-form novalidate>
                        <b-form-group
                                label-cols-sm="4"
                                label-cols-lg="3"
                                label="Name"
                                label-for="name"
                        >
                            <b-form-input type="text" id="name" v-model="form.name" :state="Boolean(form.name)"
                                          aria-describedby="nameFeedback"/>
                            <b-form-invalid-feedback id="nameFeedback">
                                This field is required
                            </b-form-invalid-feedback>
                        </b-form-group>
                        <b-form-group
                                label-cols-sm="4"
                                label-cols-lg="3"
                                label="Date"
                                label-for="date"
                        >
                            <b-form-input type="date" id="date" v-model="form.date" :state="Boolean(form.date)"
                                          aria-describedby="dateFeedback"/>
                            <b-form-invalid-feedback id="dateFeedback">
                                This field is required
                            </b-form-invalid-feedback>
                        </b-form-group>
                        <b-form-group
                                label-cols-sm="4"
                                label-cols-lg="3"
                                label="Category"
                                label-for="category"
                        >
                            <b-form-select id="category" v-model="form.category" :options="categories"
                                           :state="Boolean(form.category)" aria-describedby="categoryFeedback"/>
                            <b-form-invalid-feedback id="categoryFeedback">
                                This field is required
                            </b-form-invalid-feedback>
                        </b-form-group>
                        <b-form-group
                                label-cols-sm="4"
                                label-cols-lg="3"
                                label="Sub Category"
                                label-for="subCategory"
                        >
                            <b-form-select id="subCategory" v-model="form.subCategory" :options="subCategories"
                                           :state="Boolean(form.subCategory)" aria-describedby="subCategoryFeedback"/>
                            <b-form-invalid-feedback id="subCategoryFeedback">
                                This field is required
                            </b-form-invalid-feedback>
                        </b-form-group>
                        <b-form-group
                                label-cols-sm="4"
                                label-cols-lg="3"
                                label="File"
                                label-for="file"
                        >
                            <b-form-file
                                    id="file"
                                    v-model="form.file"
                                    :state="Boolean(form.file)"
                                    accept=".jpg, .png, .gif, .pdf, .doc, .xls"
                                    placeholder="Choose a file..."
                                    drop-placeholder="Drop file here..."
                                    aria-describedby="fileFeedback"
                            />
                            <b-form-invalid-feedback id="fileFeedback">
                                This field is required
                            </b-form-invalid-feedback>
                        </b-form-group>
                    </b-form>
                    <div slot="modal-footer">
                        <button type="button" class="btn btn-secondary" v-on:click="hideModal">Close</button>
                        <button type="button" class="btn btn-primary" v-on:click="onSubmit"
                                :disabled="isLoading || invalidForm">
                            Save
                        </button>
                    </div>
                </b-modal>
            </div>
        </div>
    </div>
</template>

<script>
    import Multiselect from 'vue-multiselect'
    import Loading from 'vue-loading-overlay';
    import ReportTable from '../components/ReportTable.vue'

    export default {
        name: 'Reports',
        components: {Multiselect, Loading, ReportTable},
        data: () => {
            return {
                fullPageLoading: false,
                selectedCategory: null,
                selectedSubCategory: null,
                fields: [
                    {key: 'name', sortable: true},
                    {key: 'date', sortable: true},
                    {key: 'category', sortable: true},
                    {key: 'sub_category', sortable: true},
                    {key: 'file'}
                ],
                form: {
                    name: '',
                    date: new Date().toISOString().substr(0, 10),
                    category: null,
                    subCategory: null,
                    file: null
                },
            }
        },
        computed: {
            isLoading() {
                return this.$store.getters['report/isLoading']
            },
            reports() {
                const reports = this.$store.getters['report/reports']
                let arr = []
                reports.forEach(report => {
                    arr.push({
                        id: report && report.id,
                        name: report && report.name,
                        date: report && report.uploadDate.substr(0, 10),
                        category: report && report.reportCategory.name,
                        sub_category: report && report.reportSubCategory.name,
                        file: report && report.file
                    })
                })
                return arr
            },
            categories() {
                const categories = this.$store.getters['report/categories']
                let arr = []
                categories.forEach(category => arr.push({value: category.id, text: category.name}))
                if (!this.form.category) {
                    this.form.category = categories[0] && categories[0].id
                }
                this.$store.dispatch('report/fetchSubCategories', [this.form.category])
                this.form.subCategory = null
                return arr
            },
            subCategories() {
                const subCategories = this.$store.getters['report/subCategories']
                let arr = []
                subCategories.forEach(subCategory => arr.push({value: subCategory.id, text: subCategory.name}))
                if (!this.form.subCategory) {
                    this.form.subCategory = subCategories[0] && subCategories[0].id
                }

                // Check if selected sub categories exist in updated list.
                if (arr.length > 0) {
                    let newSelectedSubCategories = []
                    this.selectedSubCategory && this.selectedSubCategory.forEach(subCategory => {
                        let exist = arr.some(element => element.value === subCategory.value)
                        if (exist) newSelectedSubCategories.push(subCategory)
                    })
                    this.selectedSubCategory = newSelectedSubCategories
                } else if (this.selectedCategory && this.selectedCategory.length === 0) {
                    this.selectedSubCategory = null
                }

                return arr
            },
            invalidForm() {
                return !this.form.name || !this.form.date || !this.form.category || !this.form.subCategory || !this.form.file
            },
            isAdmin() {
                return this.$store.getters['security/hasRole']('ADMIN');
            }
        },
        created() {
            this.$store.dispatch('report/fetchCategories')
            this.$store.dispatch('report/fetchReports', {
                categoryId: null,
                subCategoryId: null,
                offset: 1
            })
        },
        methods: {
            changeCategory(categories) {
                let categoryIds = []
                categories.forEach(category => categoryIds.push(category.value))
                this.$store.dispatch('report/fetchSubCategories', categoryIds)
                this.getReports()
            },
            changeSubCategory(subCategories) {
                this.getReports()
            },
            getReports() {
                let categoryIds = []
                let subCategoryIds = []
                this.selectedCategory && this.selectedCategory.forEach(category => categoryIds.push(category.value))
                this.selectedSubCategory && this.selectedSubCategory.forEach(subCategory => subCategoryIds.push(subCategory.value))
                this.$store.dispatch('report/fetchReports', {
                    categoryIds: categoryIds,
                    subCategoryIds: subCategoryIds,
                    offset: 1
                })
            },
            showModal() {
                this.$refs.uploadFileModalRef.show()
            },
            hideModal() {
                this.$refs.uploadFileModalRef.hide()
            },
            onSubmit(evt) {
                evt.preventDefault()
                this.$store.dispatch('report/createReport', this.form)
                    .then(res => {
                        this.hideModal()
                    })
            },
            onDeleteReport(reportId) {
                this.$store.dispatch('report/deleteReport', reportId)
                    .then(res => {
                        this.getReports()
                    })
            }
        }
    }
</script>
