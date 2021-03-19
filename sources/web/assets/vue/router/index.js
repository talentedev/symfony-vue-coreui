import Vue from 'vue';
import VueRouter from 'vue-router';
import BootstrapVue from 'bootstrap-vue'

import store from '../store';
import DefaultContainer from '../containers/DefaultContainer';
import Home from '../views/Home';
import Login from '../views/Login';
import User from '../views/user/User';
import ManageUser from '../views/user/ManageUser';
import Forget from '../views/Forget';
import Reports from '../views/Reports';
import Productions from '../views/data-submission/Productions';
import Export from '../views/export/Export';
import Groups from '../views/Groups';
import ImportData from '../views/ImportData';
import OnlineDatabase from '../views/online-database/OnlineDatabase';
import CreateUser from '../views/user/CreateUser';
import ActivateUser from '../views/user/ActivateUser';
import ResetPassword from '../views/ResetPassword';

Vue.use(VueRouter);
Vue.use(BootstrapVue);

let router = new VueRouter({
    mode: 'history',
    linkActiveClass: 'open active',
    scrollBehavior: () => ({ y: 0 }),
    routes: [
        {
            path: '/',
            component: DefaultContainer,
            meta: { requiresAuth: true },
            children: [
                {
                    path: '/manage-user',
                    name: 'Users',
                    component: ManageUser,
                },
                {
                    path: '/user',
                    name: 'User',
                    component: User,
                },
                {
                    path: '/reports',
                    name: 'Reports',
                    component: Reports,
                },
                {
                    path: '/data-submission',
                    name: 'Data submission',
                    component: Productions,
                },
                {
                    path: '/export-data',
                    name: 'Export Data',
                    component: Export,
                },
                {
                    path: '/groups',
                    name: 'Create group',
                    component: Groups,
                },
                {
                    path: '/import-data',
                    name: 'Import Data',
                    component: ImportData,
                },
                {
                    path: '/online-database',
                    name: 'Online Database',
                    component: OnlineDatabase,
                },
                {
                    path: '/create-user',
                    name: 'Create new user',
                    component: CreateUser,
                },
            ]
        },
        {
            path: '/home',
            name: 'Home',
            component: Home,
            meta: { requiresAuth: true }
        },
        {
            path: '/login',
            name: 'Login',
            component: Login
        },
        {
            path: '/forget',
            component: Forget
        },
        {
            path: '/reset-password',
            component: ResetPassword
        },
        {
            path: '/password-registration',
            name: 'Password Registration',
            component: ActivateUser,
        }
    ]
});

router.beforeEach((to, from, next) => {
    if (to.matched.some(record => record.meta.requiresAuth)) {
        // this route requires auth, check if logged in
        // if not, redirect to login page.
        if (store.getters['security/isAuthenticated']) {
            next();
        } else {
            next({
                path: '/login',
                query: { redirect: to.fullPath }
            });
        }
    } else {
        next(); // make sure to always call next()!
    }
});

export default router;