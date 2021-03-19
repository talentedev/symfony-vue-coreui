import Vue from 'vue';
import Vuex from 'vuex';
import SecurityModule from './security';
import User from './module/user';
import Report from './module/report';
import Group from './module/group';
import Country from './module/country';
import Company from './module/company';
import Commodity from './module/commodity';
import Product from './module/product';
import Production from './module/production';
import ImportData from './module/import';
import Region from './module/region';

Vue.use(Vuex);

export default new Vuex.Store({
    modules: {
        security: SecurityModule,
        user : User,
        report: Report,
        group: Group,
        country: Country,
        company: Company,
        commodity: Commodity,
        product: Product,
        production: Production,
        importData: ImportData,
        region: Region
    },
});